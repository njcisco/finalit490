#!/bin/bash

MACH=$1
FILE=$2
	#info for QA machine
	if  [ $MACH == 'FE' ]
        then
                # is a Prod FrontEnd machine
        	IP_D="192.168.1.48"
        	USER_M="mena"
       		PASS_M="password"
	else
		# is a Prod BackEnd machine
                IP_S="192.168.1.50"
                USER_M="mena"
                PASS_M="password"
	fi

#Backup old package and empty Files folder====> from /Production/Files/
/usr/bin/sshpass -p "${PASS_M}" ssh -t "${USER_M}"@"${IP_D}" 'cp -r  ~/Deploy/Production/Files/*  ~/Deploy/Production/Backup'
/usr/bin/sshpass -p "${PASS_M}" ssh -t "${USER_M}"@"${IP_D}" 'rm -r  ~/Deploy/Production/Files/*'


#move files from /var/www/fly490/ ====> /var/www/Backup to save the old tested-package in apache server
/usr/bin/sshpass -p "${PASS_M}" ssh -t "${USER_M}"@"${IP_D}" 'cp -r   /var/www/fly490-pro.com/*  /var/www/Backup'
/usr/bin/sshpass -p "${PASS_M}" ssh -t "${USER_M}"@"${IP_D}" 'rm -r   /var/www/fly490-pro.com/* '

# Send New package to  ~/Production/Files/ using ssh nd scp
cd ~/Deploy/Deployment/Packages/
/usr/bin/sshpass -p "${PASS_M}" scp ${FILE} "${USER_M}"@"${IP_D}":~/Deploy/Production/Files/

#Unzip and Host new pkg files to /var/www/fly490/
/usr/bin/sshpass -p "${PASS_M}" ssh  "${USER_M}"@"${IP_D}" 'unzip ~/Deploy/Production/Files/'${FILE}' -d /var/www/fly490-pro.com'
