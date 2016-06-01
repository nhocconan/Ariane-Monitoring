<?php
//MySQL Connection
$mysqli = new mysqli("localhost", "user", "password", "db");

//Timezone
date_default_timezone_set('Europe/Amsterdam');

//Email alerts
define("_email_sender","noreply@yoursite.net");
define("_email_target","alert@yoursite.net");

 ?>
