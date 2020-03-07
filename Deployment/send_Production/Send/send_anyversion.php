#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


function sendfile($machine,$file,$version)
{
        echo"Ready to send...\n";
        shell_exec("../versionSend.sh $machine $file");
        echo "Backup old package and wipe files folder=======> /Deploy/Production/Files.............. Done\n";
        echo "move files from /var/www/fly490-pro.com/ ====> /var/www/Backup to save the old tested-package.......... Done\n";
        echo"Send New package to  ~/Deploy/Prodction/Files/ to be tested by Prod.............. Done\n";

        $client = new rabbitMQClient("testRabbitMQ.ini","testServer");
        $request= array();
        $request['type'] = "update_sent";
        $request['lvl']  = 'Prod';
        $request['machine'] = $machine;
        $request['version']  = $version;
        $request['status']  = "good";
        $request['filename'] = $file;
        $response = $client->send_request($request);
        exit();
}

$machine = exec('../getVersion1.sh');
$file = exec('../getVersion2.sh');
$version= substr($file,-5,1);
sendfile($machine,$file,$version);
exit();


?>


