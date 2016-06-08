<?php
//MySQL Connection, localhost, user, password, database
$mysqli = new mysqli("localhost", "root", "", "ariane");

//Timezone
date_default_timezone_set('Europe/Amsterdam');

//Email alerts
define("_email_sender","noreply@yoursite.net");
define("_email_target","alert@yoursite.net");

 ?>
