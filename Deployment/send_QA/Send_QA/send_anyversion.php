#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


function sendfile($machine,$file,$version)
{
        echo"Ready to send...\n";
        shell_exec("../versionSend.sh $machine $file");
        echo "Backup old package and wipe files folder=======> /QA/File.............. Done\n";
        echo "move files from /var/www/fly490/ ====> /var/www/Backup to save the old tested-package.......... Done\n";
        echo"Send New package to  ~/QA/Files/ to be tested by QA.............. Done\n";

        $client = new rabbitMQClient("testRabbitMQ.ini","testServer");
        $request= array();
        $request['type'] = "update_sent";
        $request['lvl']  = 'QA';
        $request['machine'] = $machine;
        $request['filename'] = $file;
        $request['version']  = $version;
        $request['status']  = "Pending";
	$response = $client->send_request($request);
	echo "hello";
}

$machine = exec('../getVersion1.sh');
$file = exec('../getVersion2.sh');
$version= substr($file,-5,1);
sendfile($machine,$file,$version);
exit();


?>


