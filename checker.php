<?php

if(isset($_GET["url"])){
    $url = $_GET["url"];
    if(urlExists($url)){
        echo '<i class="fa fa-check align-items-center align-content-center fa-4x text-success text-center"></i><p>The site is <b>UP</b> 👍😊</p>';
    }
    else{
        echo '<i class="fa fa-close align-items-center align-content-center fa-4x text-danger"></i><p>The site is <b>DOWN</b> 😭</p>';
    }
}

function urlExists($url=NULL)  
{  
    if($url == NULL) return false;  
    $ch = curl_init($url);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    // $data = curl_exec($ch);  
    // $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
    // curl_close($ch);
    //get answer
    $response = curl_exec($ch);

   curl_close($ch);
   if ($response) return true;
   return false;
}  