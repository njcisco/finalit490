#!/usr/bin/php
<?php
 	require_once('path.inc');
        require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');
	
	getFile(); //get pkg file name from File directory
	function getFile()
	{
		//What file did you test
		$file = shell_exec("./getFile.sh");

		echo $file;
		updateTable($file);
	}

	function updateTable($file)
	{
		$ip = shell_exec("ifconfig enp0s3 | grep 'inet ' | awk '{print $2}' | cut -d/ -f1");
        echo "Your IP is: $ip";
        $machineType  = "";
        
                if (trim($ip) == "192.168.1.44"){

			$machineType = "FE";
			$lvl = "QA";
                }               
                else{
                
			$machineType = "BE";
			$lvl = "QA";
                }

                echo "Changing status to Good for ".$machineType." $file\n";

		$status = "good";
		$filename = trim($file);
		$version = substr($filename,-5,1);

		$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
                $request= array();
		$request['type'] = "changeStatus";
		$request['lvl'] = $lvl;
		$request['machine'] = $machineType;
		$request['version'] = $version ;
		$request['file'] = $filename;
		$request['status'] = trim($status);
                $response = $client->send_request($request);

	}

?>
