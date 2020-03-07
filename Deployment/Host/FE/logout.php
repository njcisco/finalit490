<?php
  	/////////////////////////////////// LOGOUT.PHP /////////////////////////////////////
	echo "Logging out...<br>";
 	//session_start using a non-default path and detect path in session cookie
	//session_set_cookie_params(0,"~sp2235/download/Asst2-d/Asst2/","web.njit.edu");
	session_start();
  	$sessionId = session_id();
  	echo "<br>The session ID is $sessionId<br>";
	$_SESSION = array( );    //make $_SESSION empty
	session_destroy( );      //kills server data on session
 	//sends header to browser to delete session cookie on browser
	//setcookie("PHPSESSID", "", time()-3600, "~sp2235/download/Asst2-d/Asst2/", "", 0,0);
  	echo "<br><br>Session has ended...
        <br>You are being redirected... <br>" ;
  	header ( "refresh: 3; url = ../index.html" );
?>
