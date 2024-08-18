<?php
//Initialize session
session_start();

//unset all of the sesion variable
$_SESSION=array();

//Destroy the session
session_destroy();

//redirected to the home page
header("location:/BestStore/index.php");

?>