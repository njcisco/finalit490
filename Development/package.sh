#!/bin/bash
#set -x

MACH=$1
VER=$2
	#version control VM ip address,version control VM username, and Version Control VM password
        IP_D="192.168.1.200"
        USER_M="hassan"
        PASS_M="password"

	# Use MACH variable which is the machine type (FE/BE), to determine which folders to zip & send.

	if  [ $MACH == 'FE' ]
	
	then
		#192.168.1.40 is a DEV FrontEnd machine
		#zip files to get send
		cp -r ~/Deploy/Development/Zip/FE/*    /var/www/fly490-dev.com/
       		 cd ~/Deploy/Development/Zip/
	      		zip -r FE_version_${VER}.zip FE/*
		#send to Version Control VM
        		/usr/bin/sshpass -p "${PASS_M}" scp FE_version_${VER}.zip "${USER_M}"@"${IP_D}":~/Deploy/Deployment/Packages
		# unzip in deploy vm and move to new location
        		/usr/bin/sshpass -p "${PASS_M}" ssh "${USER_M}"@"${IP_D}" 'unzip -o ~/Deploy/Deployment/Packages/FE_version_'${VER}'.zip -d ~/Deploy/Deployment/Host'

		exit 0
	
	else
        	#192.168.1.42 is a DEV BackEnd machine
        	#zip files to get send
        	cd ~Deploy/Development/Zip/
                	zip -r BE_version_${VER}.zip BE/*
        	#send to Version Control VM
                	sshpass -p "${PASS_M}" scp BE_version_${VER}.zip "${USER_M}"@"${IP_D}":~/Deploy/Deploment/Packages
			#unzip in Version Control vm and move to new location(Host)
                	sshpass -p "${PASS_M}" ssh "${USER_M}"@"${IP_D}" 'unzip ~/Deploy/Deployment/Packages/BE_version_'${VER}'.zip -d ~/Deploy/Deployment/Host'

        fi

