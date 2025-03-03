<?php
$host = "127.0.0.1";     
$username = "eyewitne_deals"; 
$password = "oYp6^tkSa8hc"; 
$database = "eyewitne_deals"; 
$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}