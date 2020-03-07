<?php
//session_start();

	require('rmq/testRabbitMQClient.php');     //will have register client function
	$logs = array();     //will hold values for logging
	
	$username       = $_POST['username'];
	$email          = $_POST['email'];
	$password       = $_POST['password'];
	$password1	= $_POST['password1'];
	
	if ( $password1 != $password)
        {
	 

            echo "Sorry passwords didn't match";
                exit();
        }
	else
        {
            $response = register($username,$password,$email);
                if ($response != false)
                {
                        // Message to insert into Log file
			$msg = "Successful Registration";
			
			array_push($logs,$msg,$username,$password);
			//Redirect user to login page.

                 	header('location:index.html?register=success');
			
			require("do_log.php");

                }

                else
                {
                        // Message to insert into Log file
                        $msg = "Unsuccessful Registration";

                        array_push($logs,$msg,$username,$password);
                        //Redirect user to login page.

                        header('location:index.html?register=nosuccess');
                
                        require("do_log.php");

                 
                }
        }
	
?>
