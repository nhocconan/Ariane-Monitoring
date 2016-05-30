<?php
include 'pages/functions.php';

$error = false;

if ($_POST["KEY"] == NULL) {
  die("Key not set");
}

$key = $_POST['KEY'];
$ip = $_POST['IP'];
$uptime = $_POST['UPTIME'];
$kernel = $_POST['KERNEL'];
$cpu = $_POST['CPU'];
$cpu_cores = $_POST['CPU_CORES'];
$cpu_mhz = $_POST['CPU_MHZ'];
$cpu_usage = $_POST['CPU_USAGE'];
$cpu_steal = $_POST['CPU_STEAL'];
$io_wait = $_POST['IO_WAIT'];

$cpu_steal = str_replace(' ', '', $cpu_steal);
$io_wait = str_replace(' ', '', $io_wait);
$io_wait = str_replace('wa', '', $io_wait);

$memory_total = $_POST['RAM_TOTAL'];
$memory_free = $_POST['RAM_FREE'];
$memory_cached = $_POST['RAM_CACHED'];
$memory_buffer = $_POST['RAM_BUFFER'];
$memory_active = $_POST['RAM_ACTIVE'];
$memory_inactive = $_POST['RAM_INACTIVE'];

$hdd_usage = $_POST['HDD_USAGE'];
$hdd_total = $_POST['HDD_TOTAL'];

$tx = $_POST['TX'];
$rx = $_POST['RX'];

if(!preg_match("/^[a-zA-Z0-9]+$/",$key)){ die("Key contains invalid Letters!");}
if ($ip != "") {
  if(!filter_var($ip, FILTER_VALIDATE_IP)) { die("Invalid IP!"); }
}

if(!is_numeric($uptime)){ die("Uptime contains invalid Letters!");}
if(!preg_match("/^[A-Za-z0-9.-]+$/",$key)){ die("Kernel contains invalid Letters!");}
if(!preg_match("/^[A-Za-z0-9 ()@.]+$/",$cpu)){ die("CPU Name contains invalid Letters!");}

if(!is_numeric($cpu_cores)){ die("CPU contains invalid Letters!");}
if(!is_numeric($cpu_mhz)){ die("CPU contains invalid Letters!");}
if(!is_numeric($cpu_usage)){ die("CPU contains invalid Letters!");}
if(!is_numeric($cpu_steal)){ die("CPU contains invalid Letters!");}
if(!is_numeric($io_wait)){ die("IO contains invalid Letters!");}

if(!is_numeric($memory_total)){ die("Memory contains invalid Letters!");}
if(!is_numeric($memory_free)){ die("Memory contains invalid Letters!");}
if(!is_numeric($memory_cached)){ die("Memory contains invalid Letters!");}
if(!is_numeric($memory_buffer)){ die("Memory contains invalid Letters!");}
if(!is_numeric($memory_active)){ die("Memory contains invalid Letters!");}
if(!is_numeric($memory_inactive)){ die("Memory contains invalid Letters!");}

if(!is_numeric($hdd_usage)){ die("HDD contains invalid Letters!");}
if(!is_numeric($hdd_total)){ die("HDD contains invalid Letters!");}

if(!is_numeric($tx)){ die("NET contains invalid Letters!");}
if(!is_numeric($rx)){ die("NET contains invalid Letters!");}


$success = true;

$stmt = $mysqli->prepare("SELECT id,server_name FROM servers WHERE server_key = ? LIMIT 1");
$stmt->bind_param('s', $key);
if (!$stmt->execute()) { $success = false; }
$stmt->bind_result($server_id,$server_name);
$stmt->fetch();
$stmt->close();

if ($success == false) {
  die ("MySQL Error");
}
if (empty($server_id)) {
  die("Invalid Key!");
}

//Update IP
$stmt = $mysqli->prepare("UPDATE servers SET server_ip = ?,server_uptime = ?,server_kernel = ?,server_cpu = ?,server_cpu_cores = ?,server_cpu_mhz = ?  WHERE id = ?");
$stmt->bind_param('ssssidi',$ip,$uptime,$kernel,$cpu,$cpu_cores,$cpu_mhz,$server_id);
if (!$stmt->execute()) { $success = false; }
$stmt->close();

if ($success == false) {
  die ("MySQL Error");
}

$server_time = time();

$stmt = $mysqli->prepare("SELECT server_tx,server_rx FROM servers_data WHERE server_id = ? ORDER by id DESC LIMIT 1");
$stmt->bind_param('i', $server_id);
if (!$stmt->execute()) { $success = false; }
$stmt->bind_result($db_server_tx,$db_server_rx);
$stmt->fetch();
$stmt->close();

if ($success == false) {
  die ("MySQL Error");
}

$tx_diff = $tx - $db_server_tx;
$rx_diff = $rx - $db_server_rx;

$stmt = $mysqli->prepare("INSERT INTO servers_data(server_id,memory_total,memory_free,memory_cached,memory_buffer,server_tx,server_rx,cpu_load,server_timestamp,server_tx_diff,server_rx_diff,memory_active,memory_inactive,hdd_usage,hdd_total,cpu_steal,io_wait) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ?, ?)");
$stmt->bind_param('iiiiiiididdiiiidd', $server_id,$memory_total,$memory_free,$memory_cached,$memory_buffer,$tx,$rx,$cpu_usage,$server_time,$tx_diff,$rx_diff,$memory_active,$memory_inactive,$hdd_usage,$hdd_total,$cpu_steal,$io_wait);
if (!$stmt->execute()) { $success = false; }
$stmt->close();

if ($success == false) {
  die ("MySQL Error");
}

//Limit Check
$stmt = $mysqli->prepare("SELECT cpu_alert,cpu_alert_send,cpu_steal_alert,cpu_steal_alert_send,io_wait_alert,io_wait_alert_send FROM servers WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $server_id);
if (!$stmt->execute()) { $success = false; }
$stmt->bind_result($db_cpu,$db_cpu_send,$db_cpu_steal,$db_cpu_steal_send,$db_io_wait,$db_io_wait_send);
$stmt->fetch();
$stmt->close();

if ($success == false) {
  die ("MySQL Error");
}

//CPU alert
if ($cpu_usage >= $db_cpu AND $db_cpu != NULL AND $db_cpu_send <= time()) {

    $msg = "Alert: The CPU Load of the Server ". $server_name . " has reached " . $cpu_usage . "%";
    $headers = "From: "._email_sender."\r\n";
    mail(_email_target,"Ariane - CPU Load Alert - " . $server_name,$msg,$headers);

    $lock = strtotime('+30 minutes', time());

    $stmt = $mysqli->prepare("UPDATE servers SET cpu_alert_send = ?  WHERE id = ?");
    $stmt->bind_param('ii',$lock,$server_id);
    $stmt->execute();
    $stmt->close();

  }

  //CPU Steal Alert
  if ($cpu_steal >= $db_cpu_steal AND $db_cpu_steal != NULL AND $db_cpu_steal_send <= time()) {

      $msg = "Alert: The CPU Steal of the Server ". $server_name . " has reached " . $cpu_steal . "%";
      $headers = "From: "._email_sender."\r\n";
      mail(_email_target,"Ariane - CPU Steal Alert - " . $server_name,$msg,$headers);

      $lock = strtotime('+30 minutes', time());

      $stmt = $mysqli->prepare("UPDATE servers SET cpu_steal_alert_send = ?  WHERE id = ?");
      $stmt->bind_param('ii',$lock,$server_id);
      $stmt->execute();
      $stmt->close();

    }

    //I/O Alert
    if ($io_wait >= $db_io_wait AND $db_io_wait != NULL AND $db_io_wait_send <= time()) {
        $msg = "Alert: The I/O Load of the Server ". $server_name . " has reached " . $io_wait . "%";
        $headers = "From: "._email_sender."\r\n";
        mail(_email_target,"Ariane - I/O Wait Alert - " . $server_name,$msg,$headers);

        $lock = strtotime('+30 minutes', time());

        $stmt = $mysqli->prepare("UPDATE servers SET io_wait_alert_send = ?  WHERE id = ?");
        $stmt->bind_param('ii',$lock,$server_id);
        $stmt->execute();
        $stmt->close();

      }


echo "Okay";
 ?>
