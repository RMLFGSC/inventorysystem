<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "inventorygmc";

$conn = mysqli_connect("$host", "$username", "$password", "$database");

if(!$conn)
{
    echo " DATABASE CONNECTION FAILED";
    die();
}

?>