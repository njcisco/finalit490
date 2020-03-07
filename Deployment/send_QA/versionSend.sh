#!/bin/bash
MACH=$1
FILE=$2
        #info for QA machine
        if  [ $MACH == 'FE' ]
        then
                #192.168.1.40 =========> QA FrontEnd machine
                IP_D="192.168.1.44" #QA ip-address
                USER_M="gabrielmacias"     #QA user-name       
                PASS_M="password"   #QA password        
        else
                #192.168.1.43 =========> QA BackEnd machine
                IP_D="192.168.1.46" #QA ip-address
                USER_M="gabrielmacias"      #QA user-name       
                PASS_M="password"   #QA password        
        fi


#Backup old package and empty Files folder====> from /QA/Files/
/usr/bin/sshpass -p "${PASS_M}" ssh -t "${USER_M}"@"${IP_D}" 'cp -r  ~/QA/Files/*  ~/QA/Backup; rm -r  ~/QA/Files/*'
# /usr/bin/sshpass -p "${PASS_M}" ssh -t "${USER_M}"@"${IP_D}" 'rm -r  ~/QA/Files/*'


#move files from /var/www/fly490/ ====> /var/www/Backup to save the old tested-package in apache server
/usr/bin/sshpass -p "${PASS_M}" ssh -t "${USER_M}"@"${IP_D}" 'cp -r   /var/www/fly490-qa.com/*  /var/www/Backup; rm -r   /var/www/fly490-qa.com/*'
# /usr/bin/sshpass -p "${PASS_M}" ssh -t "${USER_M}"@"${IP_D}" 'rm -r   /var/www/fly490-qa.com/* '

# Send New package to  ~/QA/Files/ using ssh nd scp
cd ~/Deploy/Deployment/Packages/
/usr/bin/sshpass -p "${PASS_M}" scp ${FILE} "${USER_M}"@"${IP_D}":~/QA/Files/


#Unzip and Host new pkg files to /var/www/fly490/
/usr/bin/sshpass -p "${PASS_M}" ssh "${USER_M}"@"${IP_D}" 'unzip ~/QA/Files/'${FILE}' -d /var/www/fly490-qa.com'

