<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectdb";

$connection = new mysqli($servername, $username, $password, $dbname);


if($connection->error == true){
    echo "connection fail";
}else{
    echo "connected";
}


