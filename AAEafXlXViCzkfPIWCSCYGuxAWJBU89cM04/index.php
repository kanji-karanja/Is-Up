<?php
$message="";
$httpcode="";
$title = "";
$url="";
$request = file_get_contents('php://input');
$requrl ="https://api.telegram.org/bot1063299581:AAEafXlXViCzkfPIWCSCYGuxAWJBU89cM04/";
$jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($request, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);
$user=0;
foreach ($jsonIterator as $key => $val) {
    if (is_array($val)) {
        echo "$key:\n";
    } else {
        echo "$key => $val\n";
        if ($key === 'id') {
            $user = $val;
        }
        if ($key === 'username') {
            $username = $val;
        }
        if ($key === 'message_id') {
            $message_id = $val;
        }
        if ($key === 'text') {
            if ($val === '/start') {
                $htmlcode = urlencode('<b>To check if a site is up send me a URL.</b>');
                $payload = file_get_contents($requrl . "sendMessage?chat_id=" . $user . "&text=" . $htmlcode . "&parse_mode=HTML");
            }
            else{
                $url = $val;
                urlExists($requrl,$user,$val);
            }
        }
    }
}


function urlExists($requrl,$user,$urlpassed=NULL){  
    if($urlpassed == NULL) return false;  
    $ch = curl_init($urlpassed);  
    curl_setopt_array($ch, array(
        CURLOPT_URL => $urlpassed,
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
    $title = "✅ The server is running well and is up."; compose($requrl,$user,$title,$urlpassed,$httpcode,$message);
    }else if($httpcode>=300 && $httpcode<400){  
        $title = "⚠ The server is up. Some additional action needs to be taken."; compose($requrl,$user,$title,$urlpassed,$httpcode,$message); 
        } else if($httpcode>=400 && $httpcode<500){  

            $title = "⚠ The server is up however there is a client error."; compose($requrl,$user,$title,$urlpassed,$httpcode,$message);
            }
    else { 
        $title = "❌ The server has encountered an error"; compose($requrl,$user,$title,$urlpassed,$httpcode,$message);
    }  
    }
    else{
        error($requrl,$user,$urlpassed);
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
function compose($requrl,$user,$title,$url,$httpcode,$message){
    $htmlcode = urlencode( $title."\n\n<i>".$url."</i>\nThe server Responded with the following status code:\n\nStatus Code | Meaning \n".$httpcode." | ".$message);
    $payload = file_get_contents($requrl . "sendMessage?chat_id=" . $user . "&text=" . $htmlcode . "&parse_mode=HTML");
}    
function error($requrl,$user,$url){
    $htmlcode = urlencode("<b>❌ The server is unreachable and could not be reached</b>\n\n<i>".$url."</i>\n\nThe server is down and may not exist or is experiencing some error.");
       $payload = file_get_contents($requrl . "sendMessage?chat_id=" . $user . "&text=" . $htmlcode . "&parse_mode=HTML");
}    