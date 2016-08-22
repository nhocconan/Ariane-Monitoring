<?php
//Database
define("_db_host", "localhost");
define("_db_login", "root");
define("_db_password", "");
define("_db", "ariane");

//Timezone
date_default_timezone_set('Europe/Amsterdam');

//URL, Rootfolder of Ariane
define("_URL","http://yourpage.com/ariane/");

//Domain
define("_Domain","yourpage.com");

//Cronjob Cleanup settings
define("_cron_data_cleanup",30); //30 Days
define("_cron_log_cleanup",7); //7 Days

//Email alerts
define("_email_sender","noreply@yoursite.net");
define("_email_target","alert@yoursite.net");

 ?>
