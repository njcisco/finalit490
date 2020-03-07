#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function getV($machine)
{
	$db = mysqli_connect("localhost","hassan","password","deploy");
	$statement = "SELECT MAX(version) FROM DevTable WHERE type = '$machine'";
	
	$runQ = mysqli_query($db,$statement);
	$queryBack = mysqli_num_rows($runQ);
	if (!$db){ die("mysql connection failed: ".mysqli_connect_error());}
	else
	{
		if ($queryBack > 0 )
		{
			$Varray = mysqli_fetch_array($runQ);
			$version = $Varray[0];
        		echo"Version Num is : $version".PHP_EOL;
        		return $version;
		}
		else 
		{
			$msg = "There is no version for this machine";
			return $msg;
			exit();
		}
	}
}


function doUpdate($myip,$lvl,$machine,$nextV,$filename)
{
	//This is updating Table after Dev sends New Pkg to Deploy
	
	$db = mysqli_connect("localhost","hassan","password","deploy");
        $statement = "INSERT INTO DevTable (ip,lvl,type,version,filename) VALUES ('$myip','$lvl','$machine','$nextV','$filename')";

        $runQ = mysqli_query($db,$statement);
	return true;
}

function getFile($machine)
{
	$db = mysqli_connect("localhost","hassan","password","deploy");
        
	echo "received Request to get File to send to QA".PHP_EOL;
        $statement = "SELECT MAX(version) FROM DevTable WHERE type = '$machine'";

        $runQ = mysqli_query($db,$statement);
        $queryBack = mysqli_num_rows($runQ);
        if (!$db){ die("mysql connection failed: ".mysqli_connect_error());}
        else
        {
                if ($queryBack > 0 )
                {
                        $Varray = mysqli_fetch_array($runQ);
                        $version = $Varray[0];
                        echo"Version Num is : $version".PHP_EOL;
                }
		$que = "SELECT filename FROM DevTable WHERE type ='$machine' AND version='$version'";
        	$Q = mysqli_query($db,$que);
		if (!$Q)
		{
			echo"Couldn't do query".PHP_EOL;
			exit();
		}	
		else
		{
			foreach($Q as $row)
			{ 
				$file = $row['filename'];
				echo"filename is: $file".PHP_EOL;
				return $file;
			}
		}
	}
}

function updateSent($lvl,$machine,$version,$status,$filename)
{
	//This is keeping track of Which pkg was sent to who

	$db = mysqli_connect("localhost","hassan","password","deploy");

        echo "received Request to track sent pkg".PHP_EOL;
        $statement = "INSERT INTO StatusTable(lvl,type,version,filename,status) VALUES('$lvl','$machine','$version','$filename','$status')";

        $runQ = mysqli_query($db,$statement);
		if (!$runQ)
        	{
                 echo"Couldn't do query".PHP_EOL;
                 exit();
                }
                else
                {
		 $msg = "Tracked: $filename sent to $lvl:$machine";
		 return $msg;
		}
        
}

function changeStatus($lvl,$machine,$version,$file,$status)
{
        //This is going to change status of pkg that was tested

        $db = mysqli_connect("localhost","hassan","password","deploy");

        echo "received Request to change status of pkg".PHP_EOL;

        $statement = "UPDATE StatusTable SET status='$status' WHERE lvl='$lvl' AND type='$machine' AND version = '$version' AND filename = '$file'";

        $runQ = mysqli_query($db,$statement);
                if (!$runQ)
                {
                 echo"Couldn't do query".PHP_EOL;
                 exit();
                }
                else
                {
                 $msg = "Updated status of pkg to $status";
		 echo"$msg".PHP_EOL;
                 return $msg;
		}
}


function getProd($machine)
{
	 $db = mysqli_connect("localhost","hassan","password","deploy");

        echo "received Request to get File to send to Production".PHP_EOL;
        $statement = "SELECT MAX(version) FROM StatusTable WHERE lvl = 'QA' AND type ='$machine' AND status='good'";

        $runQ = mysqli_query($db,$statement);
        $queryBack = mysqli_num_rows($runQ);
        if (!$db){ die("mysql connection failed: ".mysqli_connect_error());}
        else
        {
                if ($queryBack > 0 )
                {
                        $Varray = mysqli_fetch_array($runQ);
                        $version = $Varray[0];
                        echo"Version Num is : $version".PHP_EOL;
                }
                $que = "SELECT filename FROM StatusTable WHERE lvl='QA' AND type='$machine' AND version='$version' AND status='good'";
                $Q = mysqli_query($db,$que);
                if (!$Q)
                {
                        echo"Couldn't do query".PHP_EOL;
                        exit();
                }
                else
                {
                        foreach($Q as $row)
                        {
                                $file = $row['filename'];
                                echo"filename is: $file".PHP_EOL;
                                return $file;
                        }
                }
        }
}

function getOld($lvl,$machine)
{
	 $db = mysqli_connect("localhost","hassan","password","deploy");

        echo "received Request to get last working pkg for Production".PHP_EOL;
        $statement = "SELECT MAX(version) FROM StatusTable WHERE lvl = '$lvl' AND type ='$machine' AND status='good'";

        $runQ = mysqli_query($db,$statement);
        $queryBack = mysqli_num_rows($runQ);
        if (!$db){ die("mysql connection failed: ".mysqli_connect_error());}
        else
        {
                if ($queryBack > 0 )
                {
                        $Varray = mysqli_fetch_array($runQ);
                        $version = $Varray[0];
                        echo"Version Num is : $version".PHP_EOL;
                }
		if (empty($version))
		{  
			//no prev working version for lvl : machine
			$msg = "false";
			return $msg;
			exit(); 	
		}
                $que = "SELECT filename FROM StatusTable WHERE lvl='$lvl' AND type='$machine' AND version='$version' AND status='good'";
                $Q = mysqli_query($db,$que);
                if (!$Q)
                {
                        echo"Couldn't do query".PHP_EOL;
                        exit();
                }
                else
                {
                        foreach($Q as $row)
                        {
                                $file = $row['filename'];
                
				if(!$row)
				{ 
				  $msg = "false";
				  return $msg;
				  exit();
				}
				 echo"filename is: $file".PHP_EOL;
                                return $file;
                        }
                }
        }
}

function requestProcessor($request)
{
  	echo "received request".PHP_EOL;
	var_dump($request);

  	if(!isset($request['type']))
  	{
    	 return "ERROR: unsupported message type";
 	}
  	switch ($request['type'])
  	{
	case "chkV":
		return getV($request['machine']);
	case "updateTable":
                return doUpdate($request['ip'],$request['lvl'],$request['machine'],$request['version'],$request['filename']);
	case "getFile":
		return getFile($request['machine']);
	case "update_sent":
                return updateSent($request['lvl'],$request['machine'],$request['version'],$request['status'],$request['filename']);
	case "changeStatus":
                return changeStatus($request['lvl'],$request['machine'],$request['version'],$request['file'],$request['status']);
	case "getProd":
                return getProd($request['machine']);
	case "getOld":
                return getOld($request['lvl'],$request['machine']);
	case "test":
                return doTest($request['bzid']);
	case "validate_session":
		return doValidate($request['sessionId']);
	}
	return array("returnCode" =>'0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

$server->process_requests('requestProcessor');


function prodProcessor($request)
{
	echo "received request for Production".PHP_EOL;
        var_dump($request);

        if(!isset($request['type']))
        {
         return "ERROR: unsupported message type";
        }
        switch ($request['type'])
        {
        case "getOld":
                return getOld($request['lvl'],$request['machine']);
	}
}
$pro = new rabbitMQServer("testRabbitMQ.ini","Prod");
$pro->process_requests('prodProcessor');
exit();

?>
