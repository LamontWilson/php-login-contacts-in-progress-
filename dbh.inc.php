<?php

$servername = "localhost";
$dBUsername = "root";
$dBPassword = "";
$dBName = "loginsystrm";

//connection to the db
$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);

//if connection fails...
if(!$conn){
    die("Connection failed: ".mysqli_connect_error());
}