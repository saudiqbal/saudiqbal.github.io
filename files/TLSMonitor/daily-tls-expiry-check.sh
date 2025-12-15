#!/bin/bash
date=$(date '+%Y-%m-%d %H:%M:%S')
DAYS="5"
DB_FILE_NAME="/var/www/html/TLSMonitor/TLS.db"
RESULT=$(sqlite3 "${DB_FILE_NAME}" "SELECT name, value FROM domains;")
IFS=$'\n'
read -d '' -ra lines <<< "$RESULT"
for line in ${lines[@]}; do
domainname=$(echo "$line" | cut -d'|' -f1)
statusvalue=$(echo "$line" | cut -d'|' -f2)
logger -t TLSMonitor -p warning "TLS certificate checking if $domainname expires in less than $DAYS days"
expirationdate=$(date -d "$(: | openssl s_client -connect "$domainname":443 -servername "$domainname" 2>/dev/null | openssl x509 -text | grep 'Not After' |awk '{print $4,$5,$7}')" '+%s');
indays=$(($(date +%s) + (86400*DAYS)));
if [ "$indays" -gt "$expirationdate" ]; then
logger -t TLSMonitor -p warning "TLS CHECK WARNING - Certificate for $domainname expires in less than $DAYS days, on $(date -d @"$expirationdate" '+%Y-%m-%d')"
# SQLite
sqlite3 "${DB_FILE_NAME}" "UPDATE domains SET value='ER', timestamp='$(date -d @"$expirationdate" '+%Y-%m-%d')' WHERE name='$domainname';"
# Send notification only once
if [ "${statusvalue}" = "OK" ]; then
# API Call
curl -sSG "http://example.com/api.php?token=password" --data-urlencode "title=Certificate expires on $(date -d @"$expirationdate" '+%Y-%m-%d')" --data-urlencode "message=[$date] Certificate for $domainname expires in less than $DAYS days, on $(date -d @"$expirationdate" '+%Y-%m-%d')"
fi
else
logger -t TLSMonitor -p warning "TLS CHECK OK - Certificate for $domainname expires on $(date -d @"$expirationdate" '+%Y-%m-%d')"
# SQLite
sqlite3 "${DB_FILE_NAME}" "UPDATE domains SET value='OK', timestamp='$(date -d @"$expirationdate" '+%Y-%m-%d')' WHERE name='$domainname';"
fi;
done
#unset IFS

exit 0
