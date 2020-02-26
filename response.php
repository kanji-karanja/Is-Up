<?php

$message="";
$httpcode="";
$title = "";
$url="";
if(isset($_GET["url"])){
    $url = $_GET["url"];
    urlExists($url);
}

function urlExists($urlpassed=NULL){  
    if($urlpassed == NULL) return false;  
    $ch = curl_init($urlpassed);  
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
    // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt_array($ch, array(
        CURLOPT_URL => $urlpassed,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER=> false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
      ));
      
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    $message =getStatus($httpcode);
   curl_close($ch);
   if ($response){
   if($httpcode>=200 && $httpcode<300){  
    $color="success";
    $title = "<i class='fa fa-check-circle'></i>&nbsp;The server is running well and is up."; compose($color,$title,$urlpassed,$httpcode,$message);
    }else if($httpcode>=300 && $httpcode<400){  
        $color="warning";
        $title = "<i class='fa fa-warning'></i>&nbsp;The server is up. Some additional action needs to be taken."; compose($color,$title,$urlpassed,$httpcode,$message); 
        } else if($httpcode>=400 && $httpcode<500){  
            $color="warning";
            $title = "<i class='fa fa-warning'></i>&nbsp; The server is up however there is a client error."; compose($color,$title,$urlpassed,$httpcode,$message);
            }
    else { 
        $color="danger";
        $title = "<i class='fa fa-times-circle'></i>&nbsp;The server has encountered an error"; compose($color,$title,$urlpassed,$httpcode,$message);
    }  
    }
    else{
        error($urlpassed);
    }
}  
function getStatus($httpcode){
  
    switch($httpcode){
        case 200: $message="OK";break;
        case 201: $message="Created";break;
        case 202: $message="Accepted";break;
        case 204: $message="No Content";break;
        case 301: $message="Moved Permanently";break;
        case 302: $message="Found";break;
        case 303: $message="See Other";break;
        case 304: $message="Not Modified";break;
        case 307: $message="Temporary Redirect";break;
        case 400: $message="Bad Request";break;
        case 401: $message="Unauthorised";break;
        case 403: $message="Forbidden";break;
        case 404: $message="Not Found";break;
        case 405: $message="Method Not Allowed";break;
        case 406: $message="Not Acceptable";break;
        case 412: $message="Precondition Failed";break;
        case 415: $message="Unsupported Media Type";break;
        case 500: $message="Internal Server Error";break;
        case 501: $message="Not Implemented";break;
        default:$message="Not Found";break;
    }
    return $message;
}
function compose($color,$title,$url,$httpcode,$message){
    echo '<div class="card">
    <div class="card-body">
        <h4 class="text-'.$color.' card-title">'.$title.'</h4>
        <h6 class="text-muted card-subtitle mb-2">'.$url.'</h6>
        <p class="text-dark card-text">The server Responded with the following status code:</p>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Status Code</th>
                        <th>Meaning</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>'.$httpcode.'</td>
                        <td>'.$message.'</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    </div>';
}    
function error($url){
    echo '<div class="card">
    <div class="card-body">
        <h4 class="text-danger card-title"><i class="fa fa-times-circle"></i>&nbsp;The server is unreachable and could not be reached.</h4>
        <h6 class="text-muted card-subtitle mb-2">'.$url.'</h6>
        <p class="text-dark card-text">The server is down and may not exist or is experiencing some error.</p>
        
    </div>
    </div>';
}    