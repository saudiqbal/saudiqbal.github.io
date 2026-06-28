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
		readarray -t my_array < <(sqlite3 "${DB_FILE_NAME}" "SELECT name FROM domains;")
		# 2. Iterate through the generated array safely
		for item in "${my_array[@]}"; do
		#echo "$item"
		items+=("$item" )
		done
		# 2. Build the structured array required by whiptail (Tag, Description, Status)
		whiptail_args=()
		for i in "${!items[@]}"; do
			tag="${items[$i]}"
			description="${items[$i]}"
			# Set the very first item as 'ON' by default, others 'OFF'
			#if [ "$i" -eq 0 ]; then
			#status="ON"
			#else
			status="OFF"
			#fi
			whiptail_args+=("$tag" "$description" "$status")
		done
		# 3. Calculate list height dynamically
		list_height=${#items[@]}
		unset items
		# 4. Run whiptail and capture the chosen tag
		# We swap stdout and stderr (3>&1 1>&2 2>&3) because whiptail prints the choice to stderr
		choice=$(whiptail --title "Delete a hostname" --notags --radiolist "Use [Space] to select an option, then press [Enter]:" 20 70 "$list_height" "${whiptail_args[@]}" 3>&1 1>&2 2>&3)
		# 5. Handle the user's action
		exit_status=$?
		if [ $exit_status -eq 0 ]; then
			if [[ -n "$choice" ]]; then
			namedelete $choice
			result="Hostname $choice has been removed from the database"
			whiptail --title "Information" --msgbox "$result" 10 70
			else
			whiptail --title "Information" --msgbox "No selection was made" 10 70
			fi
		else
			whiptail --title "Information" --msgbox "Cencelled" 10 70
		fi
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
