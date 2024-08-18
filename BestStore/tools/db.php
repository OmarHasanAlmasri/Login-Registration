<?php
function getDatabaseConnection(){
$serverName="localhost";
$userName="root";
$password="";
$dataBase="beststoredb";

//cretae connection
$connection=new mysqli($serverName,$userName,$password,$dataBase);
if($connection->connect_error){
    die("Error field to connection Mysql " . $connection->connect_error);
}
return $connection;
}

?>