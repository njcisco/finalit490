#!/bin/bash

MACH=$1
FILE=$2
        #info for VersionControl  machine
                #192.168.1.40 is the Deploy machine
                IP_D="192.168.1.200"
                USER_M="hassan"
                PASS_M="password"
        
# Delete old pkg files from ~/deploy/Production/Files
# Delete files from /var/www/Backup/
# mv files from /var/www/fly490 to /var/www/Backup/
cp -r ~/QA/Files/* ~/QA/Backup
rm  -r ~/QA/Files/*
cp -r /var/www/fly490-qa.com/* /var/www/Backup
sudo rm -r  /var/www/fly490-qa.com/*
sshpass -p ${PASS_M} scp "${USER_M}"@"${IP_D}":~/Deploy/Deployment/Packages/${FILE}  ~/QA/Files
sudo unzip -o ~/QA/Files/FE_version_*.zip -d /var/www/fly490-qa.com/

# Restart apache for Prod
#sudo systemctl restart apache2.service
exit 0
