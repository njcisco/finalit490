#!/usr/bin/php
<?php
 	require_once('path.inc');
        require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');

	//function to check version of package in DevTable(version control)
	function chkV($machine)
	{
		echo "Getting last Version # for ".$machine.PHP_EOL;
		$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

		$request= array();
		$request['type'] = "chkV";
		$request['machine'] = $machine;
		$response = $client->send_request($request);

		#save version number in a veriable
		echo "Last Version number is $response \n";

		$newVerNum = $response+1;
	
		 return $newVerNum;
	}
	//function to update DevTable
	function doUpdate($nextV,$machine)
	{

		echo "Ready to update StatusTable...";
		$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
		#script to get ip address
		$myip = shell_exec("ifconfig enp0s3 | grep 'inet ' | awk '{print $2}' | cut -d/ -f1");
                $lvl = "Dev";
                $fname = $machine."_version_".$nextV.".zip";
                //echo "file name is: $fname  \n";

                $request            =   array();
                $request['type']    =   "updateTable";
                $request['ip']      =   trim($myip);
                $request['lvl']     =   $lvl;
                $request['machine'] =   $machine;
                $request['version'] =   $nextV;
                $request['filename']=   $fname;

		$response = $client->send_request($request);
		exit ;	
	}
	//function to make a new version and send it to version control using bash script
	function makeNewVersion($machine)
	{
		//$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
		
		$nextV = chkV($machine);
		echo"Our new Version Number is $nextV \n";
 		
		//sending the package to Version control after zipping it using bash script
		shell_exec("../package.sh $machine $nextV");

		//Updating DevTable (version control)
	 	$Update = doUpdate($nextV,$machine);
	}

	$ip = shell_exec("ifconfig enp0s3 | grep 'inet ' | awk '{print $2}' | cut -d/ -f1");
	echo "Your IP is: $ip";
	$machineType  = "";
	
		if (trim($ip) == "192.168.1.40"){

			$machineType = "FE";
		}		
		else{
		
			$machineType = "BE";
		}

		echo "Creating new Version for ".$machineType."\n";
		makeNewVersion($machineType);
?>
