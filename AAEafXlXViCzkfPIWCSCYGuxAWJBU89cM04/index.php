<?php
require '_cred.php';
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
$update = json_decode($request, true);
if (isset($update['message'])) {
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
                $htmlcode = urlencode("<b>To check if a site is up send me a URL or <b>to set a url send me a url to get regular updates in the following format:</b>\n\n/seturl https://example.com.</b>");
                    compose($requrl,$user,$htmlcode); 
            }
            else if($val==='/geturls'){
                $htmlcode = urlencode("⏱ <b>Getting your URLS...</b>\n\n Please wait...");
                compose($requrl,$user,$htmlcode); 
                $sql = "SELECT url_got FROM managers WHERE user_id='$user'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // output data of each row
                        $listing = 1 ;
                        $htmlcode = "You have set the following URL(s):\n\n";
                        while($row = $result->fetch_assoc()) {
                            $htmlcode .= $listing.". ".$row['url_got']."\n";
                            $listing++;
                        }
                        $htmlcode = urlencode($htmlcode);
                    } else {
                        $htmlcode = urlencode("❌ <b>You have not set any URLs to receive updates for.</b>");
                    }
                    updateMessage($requrl,$user,$htmlcode,$message_id);
            }
            else if($val==='/stopurl'){
                $htmlcode = urlencode("⏱ <b>Getting your URLS...</b>\n\n Please wait...");
                compose($requrl,$user,$htmlcode); 
                $sql = "SELECT url_got,id FROM managers WHERE user_id='$user'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // output data of each row
                        $htmlcode = "You have set the following URL(s):\n\n";
                        $db_urls = array();
                        while($row = $result->fetch_assoc()) {
                            $inline_array = array(array('text'=>$row['url_got'],'callback_data'=>$row['id']));
                            array_push($db_urls,$inline_array);
                             }
                        $htmlcode = urlencode("<b>Click on any of the urls you have set to remove it</b>");
                        $inlineKeyboardMarkup = array(
                          'inline_keyboard' => $db_urls
                        );
                $reply_markup = json_encode($inlineKeyboardMarkup);
                    } else {
                        $htmlcode = urlencode("❌ <b>You have not set any URLs to receive updates for.</b>");
                    }
                    updateMessage($requrl,$user,$htmlcode,$message_id,$reply_markup);
            }
            else if(substr($val.trim(), 0, 7)==='/seturl'){
                if(substr($val.trim(), 7)===""){
                    $htmlcode = urlencode("<b>To set a url to get regular updates, send me a url in the following format:</b>\n\n/seturl https://example.com");
                        compose($requrl,$user,$htmlcode); 
                }
                else{
                    $test_url =substr($val.trim(), 7);
                    $htmlcode = urlencode("<b>Setting the following URL</b>\n".substr($val, 7));
                        $payload = file_get_contents($requrl . "sendMessage?chat_id=" . $user . "&text=" . $htmlcode . "&parse_mode=HTML");
                        // prepare and bind
                        $sql = "SELECT * FROM managers WHERE user_id='$user' AND url_got='$test_url'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // output data of each row
                        $htmlcode = urlencode("⚠ The URL <b>".substr($val, 7)."</b> already exists. No change has been done");
                        updateMessage($requrl,$user,$htmlcode,$message_id);
                    } else {
                        $stmt = $conn->prepare("INSERT INTO managers (url_got, user_id) VALUES (?, ?)");
                        $stmt->bind_param("ss", $url, $user_id);

                        // set parameters and execute
                        $user_id=$user;
                        $url = substr($val, 7);
                        if($stmt->execute()){
                            $htmlcode = urlencode("⭐💫The URL <b>".substr($val, 7)."</b> has been successfully set.\nYou will receive updates on Downtime");
                            updateMessage($requrl,$user,$htmlcode,$message_id);
                        }
                        else{
                            $htmlcode = urlencode("❌The URL <b>".substr($val, 7)."</b> couldn't be set!");
                        $payload = file_get_contents($requrl . "sendMessage?chat_id=" . $user . "&text=" . $htmlcode . "&parse_mode=HTML");
                        }
                        $stmt->close();
                    }
                }
            }
            else{
                $url = $val;
                urlExists($requrl,$user,$val,$message_id);
            }
        }
    
    }
}
}
else if (isset($update['callback_query'] )) {
        $userId = $update["callback_query"]["from"]["id"];
        $idofrecord = $update["callback_query"]["data"];
        $message_id = $update["callback_query"]["message"]["message_id"];
        $sql ="DELETE FROM managers WHERE id='$idofrecord' AND user_id='$userId'";
        if ($conn->query($sql) === TRUE) {
            $htmlcode= urlencode("The URL has been removed and stopped successfully!");
            updateMessage($requrl,$userId,$htmlcode,($message_id-1));
        } else {
            $htmlcode= urlencode("The URL could not be removed. Please try again later!");
            updateMessage($requrl,$userId,$htmlcode,($message_id-1));
        }
}


function urlExists($requrl,$user,$urlpassed=NULL,$message_id){
    if($urlpassed == NULL) return false; 
    $htmlcode = urlencode("⏱ Please wait...\n Getting status of ".$urlpassed); 
    compose($requrl,$user,$htmlcode); 
    $ch = curl_init($urlpassed);  
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
    $title = "✅ The server is running well and is up.";
    }
    else if($httpcode>=300 && $httpcode<400){  
        $title = "⚠ The server is up. Some additional action needs to be taken."; 
        } 
    else if($httpcode>=400 && $httpcode<500){  
            $title = "⚠ The server is up however there is a client error."; 
            }
    else { 
        $title = "❌ The server has encountered an error"; 
    }  
    $htmlcode = urlencode( $title."\n\n<i>".$urlpassed."</i>\nThe server Responded with the following status code:\n\nStatus Code | Meaning \n".$httpcode." | ".$message);
    updateMessage($requrl,$user,$htmlcode,$message_id);
    }
    else{
        $htmlcode = urlencode("<b>❌ The server is unreachable and could not be reached</b>\n<i>".$url."</i>\n\nThe server is down and may not exist or is experiencing some error.");
        updateMessage($requrl,$user,$htmlcode,$message_id);
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
function compose($requrl,$user,$htmlcode){
    $payload = file_get_contents($requrl . "sendMessage?chat_id=" . $user . "&text=" . $htmlcode . "&parse_mode=HTML&disable_web_page_preview=true");
} 
function updateMessage($requrl,$user,$htmlcode,$message_id,$replymarkup=null){
    if($replymarkup!=null){
        $payload = file_get_contents($requrl . "editMessageText?chat_id=" . $user . "&message_id=".($message_id+1)."&text=" . $htmlcode . "&parse_mode=HTML&disable_web_page_preview=true&reply_markup=".$replymarkup);
    }
    else{
    $payload = file_get_contents($requrl . "editMessageText?chat_id=" . $user . "&message_id=".($message_id+1)."&text=" . $htmlcode . "&parse_mode=HTML&disable_web_page_preview=true");
    }
}     