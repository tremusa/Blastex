# Blastex
Blastex - Php ssl smtp email client (Send e-mail message without smtp server). Works on outlook.com, hotmail.com, gmail.com, yahoo.com, ovh.com (tested)

### Enable php sockets extension in php.ini file !!!

## How to use
```php
<?php
// Enable first php sockets extension in php.ini !!!

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
