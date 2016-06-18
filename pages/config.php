<?php
//MySQL Connection, localhost, user, password, database
$mysqli = new mysqli("localhost", "root", "", "ariane");

//Timezone
date_default_timezone_set('Europe/Amsterdam');

//URL, Rootfolder of Ariane
define("_URL","http://yourpage.com/ariane/");

//Email alerts
define("_email_sender","noreply@yoursite.net");
define("_email_target","alert@yoursite.net");

 ?>
