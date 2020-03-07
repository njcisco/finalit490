#!/bin/bash

MACH=$1
FILE=$2
        #info for VersionControl  machine
                #192.168.1.200 is the Deploy machine
                IP_D="192.168.1.200"
                USER_M="hassan"
                PASS_M="password"
# copy the previous pkg in Files into Backup folder        
# Delete old pkg files from ~/deploy/Production/Files
# Delete files from /var/www/Backup/
# mv files from /var/www/fly490-pro to /var/www/Backup/
cp -r ~/Deploy/Production/Files/* ~/Deploy/Production/Backup
rm  -r ~/Deploy/Production/Files/*
cp -r /var/www/fly490-pro.com/* /var/www/Backup
sudo rm -r  /var/www/fly490-pro.com/*
sshpass -p ${PASS_M} scp "${USER_M}"@"${IP_D}":~/Deploy/Deployment/Packages/${FILE}  ~/Deploy/Production/Files
sudo unzip -o ~/Deploy/Production/Files/FE_version_*.zip -d /var/www/fly490-pro.com/

# Restart apache for Prod
#sudo systemctl restart apache2.service
exit 0
