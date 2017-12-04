<?php
/*
* Blastex - Php Email Client SSL/TLS
* @autor
* Marcin Łukaszewski hello@breakermind.com
*
* Smtp create multipart mime message article
* https://pl.wikipedia.org/wiki/Multipurpose_Internet_Mail_Extensions
*/

//error_reporting(E_ALL);
//error_reporting(E_ERROR | E_PARSE | E_STRICT | E_WARNING);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

class BlastexSmtp
{   
    public $DebugShow = 0;    
    public $Timeout = 60;
    public $mime;
    public $filesList;
    // To: Cc: and Bcc:
    public $toList;
    public $ccList;    
    public $bccList;
    // From
    public $From;
    public $ReplyTo;
    public $Text = "";
    public $Html = "";
    public $Subject = "";

    // EHLO hostname\r\n
    public $heloHostname = 'local.host';
    
    // charset: utf-8, utf-16, iso-8859-2, iso-8859-1
    public $mEncoding = 'utf-8';

    // smtp hostname
    public $smtpPassword = '';
    public $smtpUser = '';
    public $smtpHost = '';
    public $smtpPort = 25;
    
    // Don't validate server certificate
    public $AllowSelfSigned = 1;
    // Last error
    public $lastError = '';

    function __construct($User = '', $Pass = '', $showErrors = 1, $Encoding = 'utf-8'){        
        $this->mEncoding = $Encoding;        
        $this->smtpUser = $User;
        $this->smtpPassword = $Pass;

        if($showErrors == 1){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            error_reporting(E_ERROR | E_PARSE | E_STRICT);
        }        
    }

    function Debug($enable = 1){
        $this->DebugShow = $enable;
    }

    function disableSelfSigned($enable = 0){
        $this->AllowSelfSigned = $enable;
    }

    function addHostname($host = "localhost"){
        $this->smtpHost = $host;
    }

    function addPort($port = 25){
        $this->smtpPort = $port;
    }   

    function addUser($email = ''){
        $this->smtpUser = $email;
    } 

    function addPassword($pass = ''){
        $this->smtpPassword = $pass;
    } 

    function addText($textMsg){
        $this->Text = $textMsg;
    }

    function addHtml($htmlMsg){
        $this->Html = $htmlMsg;
    }

    function addSubject($subjectMsg){
        $this->Subject = $subjectMsg;
    }

    function addFrom($email, $name = ""){       
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->From['email'] = $email;
            $this->From['name'] = $name;            
            return 1;
        }
        $this->lastError = "Invalid from email";
        return 0;
    }

    function addReplyTo($email, $name = ""){       
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->ReplyTo['email'] = $email;
            $this->ReplyTo['name'] = $name;            
            return 1;
        }
        $this->lastError = "Invalid from email";
        return 0;
    }

    function addFile($filePath, $ContentID = ""){
        $i = count($this->filesList)+1;
        if(file_exists($filePath)){
            $this->filesList[$i]['path'] = $filePath;
            $this->filesList[$i]['cid'] = $ContentID;
            return 1;
        }
        $this->lastError = "Invalid file path";
        return 0;
    }

    function addCc($email, $name = ""){
        $i = count($this->ccList)+1;
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->ccList[$i]['name'] = $name;
            $this->ccList[$i]['email'] = $email;
            return 1;
        }
        $this->lastError = "Invalid Cc email";
        return 0;
    }

    function addBcc($email, $name = ""){
        $i = count($this->bccList)+1;
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->bccList[$i]['name'] = $name;
            $this->bccList[$i]['email'] = $email;
            return 1;
        }
        $this->lastError = "Invalid Bcc email";
        return 0;
    }

    function addTo($email, $name = ""){
        $i = count($this->toList)+1;
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->toList[$i]['name'] = $name;
            $this->toList[$i]['email'] = $email;
            return 1;
        }
        $this->lastError = "Invalid To email";
        return 0;
    }

    function createMime($msgText, $msgHtml, $subject, $fromName, $fromEmail){                
        if (empty($replyTo)) { $replyTo = $fromEmail; }
        // simple message
        // $header .= "Content-type: text/html; charset=".$this->mEncoding." \r\n";
        // random strings
        $tm = time();
        $boundary1 = md5($tm);
        $boundary2 = md5($tm-10);

        // create To emails
        $to = "";
        foreach ($this->toList as $em) {
            $to .= ltrim($em['name']." <".$em['email'].'>,');            
        }
        // Cc:
        $cc = "";
        foreach ($this->ccList as $em) {
            $cc .= ltrim($em['name']." <".$em['email'].'>,');            
        }

        // Bcc:
        $bcc = "";
        foreach ($this->bccList as $em) {
            $bcc .= ltrim($em['name']." <".$em['email'].'>,');            
        }

        // multipart message
        $header = "Date: ".date("r (T)")." \r\n";        
        $header .= "From: ".$fromName." <".$fromEmail."> \r\n";   
        // To
        if(!empty($to)){ $header .= "To: ".$to."\r\n"; }
        if(!empty($cc)){ $header .= "Cc: ".$cc."\r\n"; }
        if(!empty($bcc)){ $header .= "Bcc: ".$bcc."\r\n"; }
        // Data 
        $header .= "Subject: =?".$this->mEncoding."?B?".base64_encode($subject)."?=\r\n";
        // Add reply to
        if(!empty($this->ReplyTo['email']) && !empty($this->ReplyTo['name'])){            
            $header .= "Reply-To: ".$this->ReplyTo['name']." <".$this->ReplyTo['email'].">\r\n";
        }else if(!empty($this->ReplyTo['email'])){
            $header .= "Reply-To: <".$this->ReplyTo['email'].">\r\n";
        }
        // $header .= "Return-Path: <".$fromEmail.">\r\n";         
        $header .= "MIME-Version: 1.0 \r\n";
        $header .= "Content-Transfer-Encoding: 8bit \r\n";        
        $header .= "Content-Type: multipart/mixed; boundary=\"$boundary1\"\r\n\r\n";   
        $header .= "--$boundary1\r\n";
        $header .= "Content-Type: multipart/alternative; boundary=\"$boundary2\"\r\n\r\n";
        $header .= "--$boundary2\r\n";
        $header .= "Content-Type: text/plain; charset=\"".$this->mEncoding."\"\r\n";
        $header .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $header .= quoted_printable_encode($msgText)."\r\n\r\n";
        $header .= "--$boundary2\r\n";
        $header .= "Content-Type: text/html; charset=\"".$this->mEncoding."\"\r\n";
        $header .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $header .= quoted_printable_encode($msgHtml)."\r\n\r\n";
        $header .= "--$boundary2--\r\n\r\n";
        // add atachments
        if(count($this->filesList) > 0){
            foreach ($this->filesList as $f) {
                // file name and id if inline image
                $path = $f['path'];
                $cid = $f['cid'];
                if(file_exists($f['path'])){
                    // create mime file
                    $file = basename($path);
                    $filecontent = base64_encode(file_get_contents($path));
                    $extension = pathinfo(basename($path), PATHINFO_EXTENSION);
                    $mimetype = mime_content_type($path);
                    // cout << "MIME " << mimetype << endl << extension << endl;
                    // cout << "FILE CONTENT " << fc << endl;
                    $header .= "--$boundary1\r\n";
                    $header .= "Content-Type: ".$mimetype."; name=\"".$file."\"\r\n";
                    $header .= "Content-Transfer-Encoding: base64\r\n";                
                    if(!empty($cid)){
                        // if inline image
                        $header .= "Content-Disposition: attachment; filename=\"".$file."\"\r\n";
                        $header .= "Content-ID: <".$cid.">\r\n\r\n";
                    }else{
                        $header .= "Content-Disposition: attachment; filename=\"".$file."\"\r\n\r\n";
                    }
                    $header .= $filecontent."\r\n\r\n";
                }
            }
        }
        $header .= "--$boundary1--\r\n\r\n";
        $header .= "\r\n.\r\n";
        // add mime
        $this->mime = $header;
        error_reporting('E_ALL');
    }

    function getMime(){
        return $this->mime;
    }

    function getMX($hostname = "localhost", $show = 0){
        if(dns_get_mx($hostname, $mxhosts, $weights)) {
            $i = 0;
            $mxList = NULL;
            foreach($mxhosts as $key => $host) {
                if($show == 1) echo "Hostname: $host (Weight: {$weights[$key]}) <br>";
                $ip = gethostbyname($host);
                if($show == 1) echo "IP " . $ip . "\n<br>";
                if($show == 1) echo "IP " . gethostbyaddr($ip) . "\n<br>";
                $mxList[$i]['mxhost'] = $host;
                $mxList[$i]['ip'] = $ip;
                $mxList[$i]['weight'] = $weights[$key];
                $i++;
            }
            return $mxList;
        } else {
            $this->lastError = "Could not find any MX records for $hostname";
            return $mxList;
        }
    }

    function Send(){
        if(count($this->From) == 0){
            $this->lastError = "Add From email (sender) !!!";
            return 0;   
        }
        if(empty($this->Text) || empty($this->Html) || empty($this->Subject)){
            $this->lastError = "Add Text, Html and Subject to message !!!";
            return 0;
        }
        if(count($this->toList) == 0){
            $this->lastError = "Add To email (recipient) !!!";
            return 0;   
        }

        // Create mime message                
        $this->createMime($this->Text, $this->Html, $this->Subject, $this->From['name'], $this->From['email']);

        // Send to all recipients
        $ok = $this->SendSingle();            
        if($ok == 0){
            return 0;
        }        
        
        $this->lastError = "[EMAILS_HAS_BEEN_SENT]";
        return 1;
    }
    

    function SendSingle(){        

        // Get mx hosts for hostname
        
        
        if(empty($this->smtpHost)){
            $this->lastError = "Add smtp server hostname !!!";
            return 0;
        }
        
        if(!empty($this->smtpHost)){            

            // Create stream
            $ctx = stream_context_create();

            // Validate ssl certs
            if($this->AllowSelfSigned == 1){
                stream_context_set_option($ctx, 'ssl', 'verify_peer', false);
                stream_context_set_option($ctx, 'ssl', 'verify_peer_name', false);
            }

            // Send email
            try{
                $logi = "";
                // echo $socket = stream_socket_client('ssl://smtp.gmail.com:587', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
                $socket = stream_socket_client('tcp://'.$this->smtpHost.':'.$this->smtpPort, $err, $errstr, $this->Timeout, STREAM_CLIENT_CONNECT, $ctx);
                if (!$socket) {
                    $this->lastError = "Failed to connect $err $errstr";
                    return 0;
                }else{
                    
                    // Http
                    // fwrite($socket, "GET / HTTP/1.0\r\nHost: www.example.com\r\nAccept: */*\r\n\r\n");
                    // read 220 from smtp server                        
                    $serr = fread($socket,8192) . "<br>";
                    $logi .= $serr;
                    // if error                        
                    if(strpos($serr, '220 ') === FALSE){
                    	// $this->lastError = $serr;
                    	// return 0;
                    }

                    // Send ehlo
                    fwrite($socket, "EHLO ".$this->heloHostname."\r\n") . "<br>";
                    $logi .= fread($socket,8192) . "<br>";
                    if(strpos($logi, '250 ') === FALSE){
                        $logi .= fread($socket,8192) . "<br>";
                        if (strpos($logi, '503 ') !== FALSE || strpos($logi, '501 ') !== FALSE) {
                            $this->lastError = "[ERROR_HELO_HOSTNAME]";
                            return 0;
                        }
                    }                        

                    if(strpos($logi, "STARTTLS") >= 0){
                        // Start tls connection
                        fwrite($socket, "STARTTLS\r\n") . "<br>";
                        $serr = fread($socket,8192) . "<br>";
                        $logi .= $serr;
                        // if error
                        if(strpos($serr, '220 ') === FALSE){
                        	$this->lastError = $serr;
                        	return 0;
                        }
                    }else{
                        $lastError = "Error start SSL connection. Command STARTTLS not supported.";
                        return 0;
                    }

                    // starttls
                    $sslerror = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT) . "<br>";
                    if($sslerror == 0){
                    	$this->lastError = "[SSL_HANDSHAKE_ERROR_NOT_VALID_SERVER_CERTIFICATE]";
                    	return 0;
                    }

                    // Send ehlo
                    $f1 = "EHLO ".$this->heloHostname."\r\n";
                    $logi .= $f1. "<br>";
                    fwrite($socket, $f1) . "<br>";
                    $serr = fread($socket,8192) . "<br>";
                    $logi .= $serr;
                    // if error
                    if(strpos($serr, '250 ') === FALSE){
                    	$this->lastError = $serr;
                    	return 0;
                    }

                    if(!empty($this->smtpUser) && !empty($this->smtpPassword)){

                        fwrite($socket, "AUTH LOGIN\r\n") . "<br>";
                        $serr = fread($socket,8192) . "<br>";
                        $logi .= $serr;
                        // if error
                        if(strpos($serr, '334 ') === FALSE){
                        	$this->lastError = $serr;
                        	return 0;
                        }

                        fwrite($socket, base64_encode($this->smtpUser)."\r\n") . "<br>";
                        $serr = fread($socket,8192) . "<br>";
                        $logi .= $serr;
                        // if error
                        if(strpos($serr, '334 ') === FALSE){
                        	$this->lastError = $serr;
                        	return 0;
                        }

                        fwrite($socket, base64_encode($this->smtpPassword)."\r\n") . "<br>";
                        $serr = fread($socket,8192) . "<br>";
                        $logi .= $serr;
                        // if error
                        if(strpos($serr, '235 ') === FALSE){
                        	$this->lastError = $serr;
                        	return 0;
                        }
                    }

                    if(!empty($this->From['email'])){                                
                        $f1 = "mail from: <".$this->From['email'].">\r\n";
                        $logi .= $f1. "<br>";
                        fwrite($socket, $f1) . "<br>";
                        $serr = fread($socket,8192) . "<br>";
                        $logi .= $serr;
                        // if error
                        if(strpos($serr, '250 ') === FALSE){
                        	$this->lastError = $serr;
                        	return 0;
                        }
                    }else{
                        $this->lastError = "[ERROR_FROM_EMAIL]";
                        return 0;
                    }

                    foreach ($this->toList as $e) {       
                        if(!empty($e['email'])){
                            $f1 = "rcpt to: <".$e['email'].">\r\n";
                            $logi .= $f1. "<br>";
                            fwrite($socket, $f1) . "<br>";
                            $serr = fread($socket,8192) . "<br>";
                            $logi .= $serr;
                            // if error
                            if(strpos($serr, '250 ') === FALSE){
                            	$this->lastError = $serr;
                            	return 0;
                            }	                            
                        }else{
                            $this->lastError = "[ERROR_TO_EMAIL]";
                            return 0;
                        }        
                	}

                    $f1 = "DATA\r\n";                
                    $logi .= $f1 . "<br>";
                    fwrite($socket, $f1) . "<br>";                        
                    $serr = fread($socket,8192) . "<br>";
                    $logi .= $serr;
                    // if error
                    if(strpos($serr, '354 ') === FALSE){
                    	$this->lastError = $serr;
                    	return 0;
                    }

                    // Get mime message
                    $mimeMsg = $this->getMime();

                    // Send email to server
                    fwrite($socket, $mimeMsg) . "<br>";                        
                    $serr = fread($socket,8192) . "<br>";
                    $logi .= $serr;
                    // if error
                    if(strpos($serr, '250 ') === FALSE){
                    	$this->lastError = $serr;
                    	return 0;
                    }

                    // Exit
                    fwrite($socket, "QUIT\r\n") . "<br>";
                    $logi .= fread($socket,8192) . "<br>";
                    $logi .= "<br><br>";

                    if($this->DebugShow == 1){
                        echo $logi;
                    }
                    /* Turn off encryption for the rest */
                    // stream_socket_enable_crypto($fp, false);
                    fclose($socket);                                            
                }
                $emailSend = 1;
            }catch(Exception $e){
                $logi .= $e->getMessage();
                $this->lastError = "[SEND_ERROR_EXCEPTION]";
                return 0;
            }                  
        }
        return 1;    
    }

    function cutBcc($mime){
        $out = preg_replace('/Bcc:(.*)[, ]\r\n/', '', $mime);
        // print_r($out);
        if (empty($out)) {
            return 0;
        }        
        return $out;
    }

}// end class

/* 

// With authentication (don't need use addPassword and addUser after this)
// $m = new BlastexSmtp('user6@hotmail.com','Password');
$m = new BlastexSmtp();

// Show logs
$m->Debug(0);
// Disable Self signed server certificate
$m->disableSelfSigned(1);

// Smtp server hostname
$m->addHostname("ns0.ovh.net");
// Add smtp port
$m->addPort(25);
// Smtp server email
$m->addUser('email@breakermind.com');
// Smtp server password
$m->addPassword("Password");

// Add from
$m->addFrom("email@breakermind.com", "Ania Bez pic");
// Add to
$m->addTo("email@gmail.com", "Maxiu");
// Add Cc
$m->addCc("email@yahoo.com", "Yahoo email");// 
// Add Bcc
$m->addBcc("email@qflash.pl", "Henio");

// Text message
$m->addText("Hello message");
// Html message
$m->addHtml('<h1>Nowa wiadomość</h1> <br> CO znowu zjebali<img src="cid:zenek123">');
// Message subject
$m->addSubject("Blastex - Php smtp email client!");

// Add files inline
$m->addFile('photo.jpg',"zenek123");
// Add file
$m->addFile('sun.png');

// Send email from smtp server
if($m->Send() == 1){
	echo "Email has been sent!";	
}else{
	// Show last error
	echo $m->lastError;	
}


// Create mime message: $msgText, $msgHtml, $subject, $fromName, $fromEmail, $replyTo
// $m->createMime("Witaj księżniczko !",'<h1>Witaj Maniu <img src="cid:zenek123"> </h1>',"Wesołych świąt życzę!","Heniek Wielki", "heniek@domain.xx");

// get mime
// $m->getMime();

// Show mime
// echo nl2br(htmlentities($m->getMime()));

// Show mime without Bcc
// echo nl2br($m->cutBcc($m->getMime()));

*/

?>

