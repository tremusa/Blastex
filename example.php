<?php
// Add class
require('blastex-ssl-smtp-client.php');

echo "Sending emails... <br>";

// Create object
$m = new Blastex();

// Show logs
$m->Debug(1);

// hello hostname (your vps ip hostname)
$m->addHeloHost("domain.xx");

// Add from
$m->addFrom("jim@domain.xx", "Ania Bania");

// Add to
$m->addTo("albert@hotmail.com", "Albert");
$m->addTo("adzia@gmail.com", "Adela Mela");

// Add Cc
$m->addCc("ben@gmail.com", "Ben Pen");
$m->addCc("max@yahoo.com");
$m->addCc("asy@outlook.com");

// Add Bcc
$m->addBcc("boos@domain.com", "BOSS");    

$m->addText("Hello message");

$m->addHtml('<h1>Hello message html</h1> My photo <img src="cid:photo.zenek123">');

$m->addSubject("Hello from smtp email client !!!");

// Add files inline
$m->addFile('photo.jpg',"photo.zenek123");

// Add file
$m->addFile('sun.png');

// Send email from dns mx hosts
$m->Send();

// Show last error (you can add after every methods)
echo $m->lastError;

// Create mime message: $msgText, $msgHtml, $subject, $fromName, $fromEmail, $replyTo
// $m->createMime("Witaj Maxiu",'<h1>Witaj Maxiu <img src="cid:zenek123"> </h1>',"Wesołych świąt życzę!","Heniek Wielki", "jim@domain.xx");

// get mime
// $m->getMime();

// Show mime
// echo nl2br(htmlentities($m->getMime()));

// Show mime without Bcc
// echo nl2br($m->cutBcc($m->getMime()));

?>
