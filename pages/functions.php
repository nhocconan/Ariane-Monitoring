<?php
session_start();
date_default_timezone_set('Europe/Amsterdam');

define("_email_sender","noreply@yoursite.net");
define("_email_target","alert@yoursite.net");

$mysqli = new mysqli("localhost", "", "password", "");

if ($mysqli->connect_error) { //Checks if the MySQL Connection works, if not it returns you a error msg and exits.
   echo "Not connected, error: " . $mysqli->connect_error;
   exit;
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 24; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function checkAcess($server_id,$user_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT user_id FROM servers WHERE id = ? LIMIT 1");
  $stmt->bind_param('i', $server_id);
  $stmt->execute();
  $stmt->bind_result($db_user_id);
  $stmt->fetch();
  $stmt->close();

  if ($db_user_id === $user_id) {
    return true;
  } else {
    return false;
  }
}



 ?>
