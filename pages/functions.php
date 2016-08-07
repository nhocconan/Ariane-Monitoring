<?php

include 'config.php';

session_set_cookie_params(0,'/','.'._Domain,true,true);
session_start();

$database = new mysqli(_db_host, _db_login, _db_password, _db);

if ($database->connect_error) { //Checks if the MySQL Connection works, if not it returns you a error msg and exits.
   echo "Not connected, error: " . $database->connect_error;
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

function generateBacon($server_id,$timeframe,$start_in = 0,$end_in = 0) {
  global $database;

  $server_data = array();
  $start = time() - 3600;
  $end = time();
  $steps = 1;
  $count = 0;

  if ($timeframe == 0) {
      $start = $start_in;
      $end = $end_in;
  } elseif ($timeframe == 1) {
      $start = time() - 3600;
  } elseif ($timeframe == 2) {
      $start = time() - 7200;
  } elseif ($timeframe == 4) {
      $start = time() - 14400;
  } elseif ($timeframe == 12) {
        $start = time() - 43200;
        $steps = 5;
  } elseif ($timeframe == 24) {
        $start = time() - 86400;
        $steps = 5;
  }

  $query = "SELECT memory_total,memory_free,memory_cached,memory_buffer,cpu_load,server_rx_diff,server_tx_diff,memory_active,memory_inactive,hdd_usage,hdd_total,cpu_steal,io_wait,server_timestamp FROM servers_data WHERE server_id = ? AND server_timestamp >= ? AND server_timestamp <= ? ORDER by id";
  $stmt = $database->prepare($query);
  $stmt->bind_param('iii', $server_id,$start,$end);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {

    if ($count == $steps) {

      $server_data['memory_total'][] = escape(round($row['memory_total'] / 1024,1));
      $server_data['memory_free'][] = escape(round($row['memory_free'] / 1024,1));
      $server_data['memory_cached'][] = escape(round($row['memory_cached'] / 1024,1));
      $server_data['memory_buffer'][] = escape(round($row['memory_buffer'] / 1024,1));
      $server_data['memory_active'][] = escape(round($row['memory_active'] / 1024,1));
      $server_data['memory_inactive'][] = escape(round($row['memory_inactive'] / 1024,1));
      $used = $row['memory_total']-($row['memory_free']);
      $server_data['memory_used'][] = escape(round($used/ 1024,1));

      $server_data['cpu_load'][] = escape(round($row['cpu_load'],2));
      $server_data['cpu_steal'][] = escape(round($row['cpu_steal'],2));

      $server_data['server_rx_diff'][] = escape(round($row['server_rx_diff'] / 1048576 / 60,2));
      $server_data['server_tx_diff'][] = escape(round($row['server_tx_diff'] / 1048576 / 60,2));

      $server_data['hdd_usage'][] = escape(round($row['hdd_usage'] / 1024 / 1024 / 1024,1));
      $server_data['hdd_total'][] = escape(round($row['hdd_total'] / 1024 / 1024 / 1024,1));

      $server_data['io_wait'][] = escape(round($row['io_wait'],2));

      $server_data['server_timestamp'][] = date("'H:i'",escape($row['server_timestamp']));

      $count = 0;

    }

    $count++;
  }
  return $server_data;
}

function addLog($user,$type,$msg) {
  global $database;

  $current_time = time();

  $stmt = $database->prepare("INSERT INTO logs(user_id,type,msg,timestamp) VALUES (?, ?, ?, ?)");
  $stmt->bind_param('iisi', $user,$type,$msg,$current_time);
  $stmt->execute();
  $stmt->close();

}

function getNavbar($page) {

$navbar = '<nav class="navbar navbar-default navbar-fixed-top navbar-opacity">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php?page=dashboard">Ariane</a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">';
                if ($page == 'dashboard') {
                      $navbar .= '<li class="active"><a href="index.php?page=dashboard">Servers</a></li>';
                } else {
                      $navbar .= '<li><a href="index.php?page=dashboard">Servers</a></li>';
                }
                if ($page == 'overview') {
                      $navbar .= '<li class="active"><a href="index.php?page=overview">Overview</a></li>';
                } else {
                      $navbar .= '<li><a href="index.php?page=overview">Overview</a></li>';
                }
                if ($page == 'dashboard?add') {
                     $navbar .= '<li class="active"><a href="index.php?page=dashboard?add">Add Server</a></li>';
               } else {
                     $navbar .= '<li><a href="index.php?page=dashboard?add">Add Server</a></li>';
               }
               if ($page == 'dashboard?account') {
                    $navbar .= '<li class="active"><a href="index.php?page=dashboard?account">Account</a></li>';
               } else {
                    $navbar .= '<li><a href="index.php?page=dashboard?account">Account</a></li>';
               }
               if ($page == 'dashboard?logs') {
                    $navbar .= '<li class="active"><a href="index.php?page=dashboard?logs">Logs</a></li>';
               } else {
                    $navbar .= '<li><a href="index.php?page=dashboard?logs">Logs</a></li>';
               }

        $navbar .='<li><a href="index.php?page=logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </nav>';

     echo $navbar;

}

function isSecure() {
  return
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || $_SERVER['SERVER_PORT'] == 443;
}

function checkAccess($server_id,$user_id) {
  global $database;

  $stmt = $database->prepare("SELECT user_id FROM servers WHERE id = ? LIMIT 1");
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
