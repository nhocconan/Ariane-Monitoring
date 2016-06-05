<?php
session_start();

include 'config.php';

if ($mysqli->connect_error) { //Checks if the MySQL Connection works, if not it returns you a error msg and exits.
   echo "Not connected, error: " . $mysqli->connect_error;
   exit;
}

if (version_compare(PHP_VERSION, '5.5.1') < 0) { //PHP Version check
    die("This Script needs at least PHP Version 5.5.0");
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

function escape($text) {
  return htmlspecialchars($text,ENT_QUOTES);
}

function secondsToTime($seconds) {
    $seconds = round($seconds,0);
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes');
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
