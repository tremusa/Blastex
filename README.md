# Blastex
Blastex - Php ssl smtp email client (Send e-mail message without smtp server). Works on outlook.com, hotmail.com, gmail.com, yahoo.com, ovh.com (tested)

### Enable php sockets extension in php.ini file !!!

## How to use (Blastex smtp client SSL/TLS)
```php
// With authentication (don't need use addPassword and addUser after this)
// $m = new BlastexSmtp('email@breakermind.com','Password');

// Authentication with addPassword and addUser
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
$m->addFrom("email@breakermind.com", "Ania BezPic");

// Add to
$m->addTo("email@gmail.com", "Maxiu");
$m->addTo("email@hotmail.com", "Second Email");

// Add Cc
$m->addCc("email@yahoo.com", "Yahoo email");// 
// Add Bcc
$m->addBcc("email@qflash.pl");

// Text message (need add both Text and html)
$m->addText("Hello message");
// Html message
$m->addHtml('<h1>Nowa wiadomość</h1> <br> Świat sie kręci cały czas <img src="cid:zenek123">');
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
```

## How to use (Blastex without smtp server SSL/TLS)
```php
<?php
// Enable first php sockets extension in php.ini !!!

// Add class
require('blastex-send-without-smtp-server.php');

echo "Sending emails... <br>";

// Create object
$m = new Blastex();

// Show logs
$m->Debug(1);

// hello hostname
$m->addHeloHost("qflash.pl");

// Add from
$m->addFrom("boo@qflash.pl", "Ania Bania");

// Add to
$m->addTo("xxx@breakermind.com", "Albercik Kutafonek");
$m->addTo("xxx@gmail.com", "Adela Mela");

// Add Cc
$m->addCc("zzz@gmail.com", "Ben");
$m->addCc("ccc@yahoo.com");
$m->addCc("aaa@outlook.com");

// Add Bcc
$m->addBcc("boos@domain.com", "BOSS");    

$m->addText("Hello message");
$m->addHtml('<h1>Hello message html</h1> <br> My photo <img src="cid:zenek123">');
$m->addSubject("Blastex Php smtp client without smtp server  !!!");

// Add files inline
$m->addFile('photo.jpg',"zenek123");

// Add file
$m->addFile('sun.png');

// Send email to hosts from recipient dns mx records
if($m->Send() == 1){
  echo "Email has been send";
}
// Smtp client last error (you can use after every method)
echo $m->lastError;

?>
```
