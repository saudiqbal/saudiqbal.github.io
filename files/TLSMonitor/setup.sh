#! /bin/bash

mkdir -p /var/www/html/TLSMonitor
DB_FILE_NAME="/var/www/html/TLSMonitor/TLS.db"

clear

function nameadd {
sqlite3 "${DB_FILE_NAME}" "INSERT INTO domains (name, value, timestamp) VALUES ('$1', '0' , '0');"
} 

function namedelete {
sqlite3 "${DB_FILE_NAME}" "DELETE FROM domains WHERE name = '$1'"
}

OPTIONS=(
	"1" "Exit setup"
	"2" "Setup new database"
	"3" "Add (sub)domain"
	"4" "Delete (sub)domain"
	"5" "List all (sub)domains"
)

while [ 1 ]
do
CHOICE=$(whiptail --title "TLS Certificate Monitoring Server Setup" --menu "Choose an option:" 16 100 8 "${OPTIONS[@]}" 3>&2 2>&1 1>&3)
if [ $? -gt 0 ]; then
	break
fi

result=""
case $CHOICE in
	"1")   
		exit
	;;
	"2")   
		if [ -f "$DB_FILE_NAME" ]; then
		result="Database file already exists"
		else
		sqlite3 "${DB_FILE_NAME}" "CREATE TABLE domains (rowid INTEGER PRIMARY KEY, name TEXT UNIQUE, value TEXT, timestamp TEXT);"
		result="New database has been setup."
		fi
		whiptail --title "Information" --msgbox "$result" 10 70
	;;
	"3")   
		NAME=$(whiptail --inputbox "Please enter (sub)domain to monitor" 10 100 3>&1 1>&2 2>&3)
		if [ -z "$NAME" ]; then
		result="Cancelled"
		else
		validate="^([a-zA-Z0-9_](([a-zA-Z0-9-]){0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$"
		if [[ "$NAME" =~ $validate ]]; then
		nameadd $NAME
		result="Hostname $NAME has been added to the database"
		else
		result="Invalid name: $NAME"
		fi
		fi
		whiptail --title "Information" --msgbox "$result" 20 70
	;;
	"4")   
		NAME=$(whiptail --inputbox "Please enter (sub)domain to remove" 10 100 3>&1 1>&2 2>&3)
		if [ -z "$NAME" ]; then
		result="Cancelled"
		else
		namedelete $NAME
		result="Hostname $NAME has been removed from the database"
		fi
		whiptail --title "Information" --msgbox "$result" 20 70
	;;
	"5")
		AllDomains=$(sqlite3 "${DB_FILE_NAME}" "SELECT name FROM domains;")
		result="(Scroll down to see more)\n\n"
		result+="$AllDomains"
		result+="\n\nEnd of list"
		whiptail --scrolltext --title "Information" --msgbox "$result" 20 70
	;;
esac
done
exit
