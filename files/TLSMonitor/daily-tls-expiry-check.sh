#!/bin/bash
logger -t TLSMonitor -p warning "[TLS CHECKER] Script start ***"
date=$(date '+%Y-%m-%d %H:%M:%S')
DAYS="14"
DB_FILE_NAME="/var/www/html/TLSMonitor/TLS.db"
RESULT=$(sqlite3 "${DB_FILE_NAME}" "SELECT name, value, timestamp FROM domains;")
IFS=$'\n'
read -d '' -ra lines <<< "$RESULT"
for line in ${lines[@]}; do
domainname=$(echo "$line" | cut -d'|' -f1)
statusvalue=$(echo "$line" | cut -d'|' -f2)
timestampvalue=$(echo "$line" | cut -d'|' -f3)
logger -t TLSMonitor -p warning "[TLS CHECKER] Checking if $domainname expires in less than $DAYS days"
expirationdate=$(date -d "$(: | openssl s_client -connect "$domainname":443 -servername "$domainname" 2>/dev/null | openssl x509 -noout -enddate | cut -d= -f2)" +%s);
indays=$(($(date +%s) + (86400*DAYS)));
if [ "$indays" -gt "$expirationdate" ]; then
logger -t TLSMonitor -p warning "[TLS CHECKER] Certificate for $domainname expires in less than $DAYS days, on $(date -d @"$expirationdate" '+%Y-%m-%d %H:%M:%S')"
# SQLite
sqlite3 "${DB_FILE_NAME}" "UPDATE domains SET value='ER', timestamp='$expirationdate' WHERE name='$domainname';"
# Send notification only once
if [ "${statusvalue}" = "OK" ]; then
# API Call or Email
#mail -s "Certificate expires on $(date -d @"$expirationdate" '+%Y-%m-%d %H:%M:%S')" -r "from@example.com" "to@example.com" <<< "[$date] Certificate for $domainname expires in less than $DAYS days, on $(date -d @"$expirationdate" '+%Y-%m-%d %H:%M:%S')"
#curl -sSG "http://example.com/api.php?token=password" --data-urlencode "title=Certificate expires on $(date -d @"$expirationdate" '+%Y-%m-%d %H:%M:%S')" --data-urlencode "message=[$date] Certificate for $domainname expires in less than $DAYS days, on $(date -d @"$expirationdate" '+%Y-%m-%d %H:%M:%S')"
fi
else
logger -t TLSMonitor -p warning "[TLS CHECKER] OK - Certificate for $domainname expires on $(date -d @"$expirationdate" '+%Y-%m-%d %H:%M:%S')"
# SQLite
if [ "$timestampvalue" -ne "$expirationdate" ]; then
sqlite3 "${DB_FILE_NAME}" "UPDATE domains SET value='OK', timestamp='$expirationdate' WHERE name='$domainname';"
logger -t TLSMonitor -p warning "[TLS CHECKER] TimeStamp change detected in $domainname, SQLite updated"
else
logger -t TLSMonitor -p warning "[TLS CHECKER] No TimeStamp change detected in $domainname, SQLite update skipped"
fi
fi
datetoday=$(date '+%Y-%m-%d')
expiringtoday=$(date -d @$expirationdate +'%Y-%m-%d')
if [ "$datetoday" == "$expiringtoday" ]; then
# API Call or Email
#mail -s "Certificate expires(ed) today" -r "from@example.com" "to@example.com" <<< "[$date] Certificate for $domainname expires(ed) today"
#curl -sSG "http://example.com/api.php?token=password" --data-urlencode "title=Certificate expires(ed) today" --data-urlencode "message=[$date] Certificate for $domainname expires(ed) today"
fi
done
#unset IFS
logger -t TLSMonitor -p warning "[TLS CHECKER] Script end ***"
exit 0
