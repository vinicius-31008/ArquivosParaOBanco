<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistema";

$con = mysqli_connect($servername, $username, $password, $dbname);

if(!$con){
    die("falha na conexão: ". mysqli_connect_error());
}

?>