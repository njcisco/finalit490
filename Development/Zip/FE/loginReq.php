#!/usr/bin/php
<?php
        session_start();
        #contains login client function
        require ('rmq/testRabbitMQClient.php');
	
	//will hold values for logging
        $logs = array();
	
	//check if user entered correctly, by clicking submit button
        if(isset($_POST['submit']))
        {
                //get user input and send info to Client.php
                $username = $_POST['username'];
                $password = $_POST['password'];
                $response = login($username, $password );
	
		//login successful or not?
                //log result
                if ( $response != false ) //username and pass match
                {
                        //$_SESSION['logged'] = true;
                        //$_SESSION['username'] = $username;
                        
			// Message to insert into Log file
			$msg = "Successful Login";
			
			// Push Variables into Array -> $logs()
			array_push( $logs, $msg,$username,$password) ;
			

			// Redirect User INTO our website
                        header  ( 'location:./home.html');
			
			// Triger the Logging script
                        require("do_log.php");
                }
                //username and pass no match
                else
 		{
		 // Message to insert into Log file
		$msg = "Unsuccessful Login";

		//Push Variables into Array		
                array_push( $logs,$msg, $username, $password);
		
                header  ( 'location:index.html?login=error' );
                require("do_log.php");
                        exit();
                }
        }
        //user did not click submit in form
        else
        {
    		header  ( 'location:index.html?login=nosubmit' );
                exit();
        }
?>

