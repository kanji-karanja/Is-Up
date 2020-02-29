<?php
function handleError($errno, $errstr) {
    echo "The application has encountered an error :(
            Don't worry, its just a configuration error.
            We are working on it ASAP.";
    die();
}
//set_error_handler("handleError");

if($_SERVER['HTTP_HOST']==='localhost'){
    $conn = new mysqli('localhost','root','','isup');
}
else if($_SERVER['HTTP_HOST']==='is-up.cryosoft.co.ke'){
    $conn = new mysqli('den1.mysql3.gear.host', 'portalmsu', 'Dp1z?1-yE2zP', 'isup');
}
else{
    $conn = new mysqli('localhost','root','','isup');
}

$conn->set_charset('utf8mb4');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}