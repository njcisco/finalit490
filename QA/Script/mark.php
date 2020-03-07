#!/usr/bin/php
<?php
 	require_once('path.inc');
        require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');
		

	
function download_pkg($lvl,$machineType,$file)
{

                echo"The old pkg that was working for $lvl $machineType was: $file".PHP_EOL;
                //ask Deploy for this old pkg
		exec("./getOld_pkg.sh $machineType $file");


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
                echo "Changing status for ".$machineType." ".$file."\n";

                //$status = exec('./getStatus.sh');   WE ARE NOT ASKING USER TO INPUT WHAT STATUS CHANGE IS, this script only changes to bad
                //changing status to bad
                $status = "bad";
                $filename = trim($file);
                //get version # based on zip filename
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
                echo"$response".PHP_EOL;
                sleep(2);
                // get the old working pkg filename from Table 
		echo"getting the Old Pkg...".PHP_EOL;

        	//getting the last pkg marked good for Prod-FE/BE
       		$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
                $request= array();
                $request['type'] = "getOld";
                $request['lvl'] = $lvl;
                $request['machine'] = $machineType;
                $old = $client->send_request($request);

                //if there was no old working pkg..
                if ($old == "false")
                {echo"response is $old...".PHP_EOL;}

                if ($old == "false")
                {
                        $msg = "There was no previous working $lvl : $machineType version found";
                        echo"$msg".PHP_EOL;
                        exit();
                }
                else
                {
			$file = trim($old);

                        //get the old working pkg from Version Control VM
			download_pkg($lvl,$machineType,$file);
			echo"last working version $old sent to Production==========> Done".PHP_EOL;
			$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
		$newOld=substr($old,-5,1);
	        $request= array();
       	 	$request['type'] = "update_sent";
        	$request['lvl']  = 'QA';
        	$request['machine'] = $machineType;
        	$request['version']  = $newOld;
        	$request['status']  = "good";
        	$request['filename'] = $file;

		$response = $client->send_request($request);
		echo"statusTable updated to last version as $newOld ==========>Done".PHP_EOL;

			
                }

        }

function getFile()//get the zip filename from Packages directory in this vm
{		echo"QA Rollback started".PHP_EOL;
		echo "===================================================================".PHP_EOL;
                //What file were you hosting
                $file = shell_exec("./getFile.sh");

                echo "current pkg is: $file".PHP_EOL;

                //change the status to Bad for this pkg in Deploy StatusTable
		updateTable($file);

		echo"=====================================================================".PHP_EOL;
		echo"QA Rollback Done".PHP_EOL;
		echo"             ".PHP_EOL;
        }

getFile();

?>
