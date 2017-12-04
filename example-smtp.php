<?php
// Add blastex smtp class
require('blastex-smtp.php');

// With authentication (don't need use addPassword and addUser after this)
// $m = new BlastexSmtp('email@breakermind.com','Password');

// Create object
$m = new BlastexSmtp();

// Show logs
$m->Debug(0);
// Disable Self signed server certificate
$m->disableSelfSigned(1);

// Smtp server hostname
$m->addHostname("ns0.ovh.net");
// Add smtp port 25, 587
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
$m->addHtml('<h1>Nowa wiadomość</h1> <br> Witaj !!<img src="cid:zenek123">');
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

?>
