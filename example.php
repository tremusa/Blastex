<?php
// Add class
require('blastex-ssl-smtp-client.php');

echo "Sending emails... <br>";

// Create object
$m = new Blastex();

// Show logs
$m->Debug(1);

// hello hostname
$m->addHeloHost("qflash.pl");

// Add from
$m->addFrom("info@qflash.pl", "Ania Bania");

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

$m->addHtml("<h1>Hello message html</h1>");

$m->addSubject("Hello from smtp email client !!!");

// Add files inline
// $m->addFile('photo.jpg',"zenek123");

// Add file
// $m->addFile('sun.png');

// Send email from dns mx hosts
$m->Send();
echo $m->lastError;

// Create mime message: $msgText, $msgHtml, $subject, $fromName, $fromEmail, $replyTo
// $m->createMime("Witaj księżniczko Alabambo",'<h1>Witaj księżniczko Alabambo <img src="cid:zenek123"> </h1>',"Wesołych świąt życzę!","Heniek Wielki", "heniek@domain.com");

// get mime
// $m->getMime();

// Show mime
// echo nl2br(htmlentities($m->getMime()));

// Show mime without Bcc
// echo nl2br($m->cutBcc($m->getMime()));

?>
