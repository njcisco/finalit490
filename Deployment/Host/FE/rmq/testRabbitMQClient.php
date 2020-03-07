#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


function login ($username, $password)
{
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$request = array();
$request['type'] = "login";
$request['username'] = $username;
$request['password'] = $password;

$response = $client->send_request($request);
//$response = $client->publish($request);

return $response;
}

function register ($username,$password,$email)
{
        $client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$request = array();
$request['type'] = "register";
$request['username'] = $username;
$request['password'] = $password;
$request['email'] = $email;

$response = $client->send_request($request);
//$response = $client->publish($request);

return $response;
}
?>
