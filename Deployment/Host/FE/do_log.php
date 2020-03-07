#!/usr/bin/php
<?php

// Variable for Log file.
$myfile = fopen("log.txt","a+") or die ("Log file not found");

// Insert text into Log file
$today = date("l jS \of F Y h:i:s A");

// $log[0] = message to be inserted ( Successful or Failed ) sent from login/register.php
// $log[1] = username to be inserted ( Successful or Failed ) sent from login/register.php
// $log[2] = passwordto be inserted ( Successful or Failed ) sent from login/register.php

fwrite ($myfile, "* ".$today." ".$logs[0]." For User: ".$logs[1]." with Password: ".$logs[2]."\n");

// Close Log file
fclose($myfile);

?>
