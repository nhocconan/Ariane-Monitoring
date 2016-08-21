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
$cpu_usage = $_POST['CPU_USAGE']; //User Usage
$cpu_usage_sys = $_POST['CPU_USAGE_SYS']; //System Usage
$cpu_steal = $_POST['CPU_STEAL'];
$io_wait = $_POST['IO_WAIT'];

//Filter values
$cpu_steal = explode("\n", $cpu_steal); $cpu_steal = $cpu_steal[1];
$cpu_steal = str_replace(' st', '', $cpu_steal);
$cpu_steal = str_replace('%st', '', $cpu_steal);
$io_wait = explode("\n", $io_wait); $io_wait = $io_wait[1];
$io_wait = str_replace(' wa', '', $io_wait);
$io_wait = str_replace('%wa', '', $io_wait);
$cpu_usage = explode("\n", $cpu_usage); $cpu_usage = $cpu_usage[1];
$cpu_usage = str_replace(' us', '', $cpu_usage);
$cpu_usage = str_replace('%us', '', $cpu_usage);
$cpu_usage_sys = explode("\n", $cpu_usage_sys); $cpu_usage_sys = $cpu_usage_sys[1];
$cpu_usage_sys = str_replace(' sy', '', $cpu_usage_sys);
$cpu_usage_sys = str_replace('%sy', '', $cpu_usage_sys);

$memory_total = $_POST['RAM_TOTAL'];
$memory_free = $_POST['RAM_FREE'];
$memory_cached = $_POST['RAM_CACHED'];
$memory_buffer = $_POST['RAM_BUFFER'];
$memory_active = $_POST['RAM_ACTIVE'];
$memory_inactive = $_POST['RAM_INACTIVE'];

//Filter Values thanks Ubuntu 12.04
$memory_cached = preg_replace( "/\r|\n/", "", $memory_cached );

$hdd_usage = $_POST['HDD_USAGE'];
$hdd_total = $_POST['HDD_TOTAL'];

$tx = $_POST['TX'];
$rx = $_POST['RX'];

if(!preg_match("/^[a-zA-Z0-9]+$/",$key)){ die("Key contains invalid Letters!");}
if ($ip != "") {
  if(!filter_var($ip, FILTER_VALIDATE_IP)) { die("Invalid IP!"); }
}

$success = true;

$stmt = $database->prepare("SELECT id,server_name,user_id FROM servers WHERE server_key = ? LIMIT 1");
$stmt->bind_param('s', $key);
if (!$stmt->execute()) { $success = false; }
$stmt->bind_result($server_id,$server_name,$user_id);
$stmt->fetch();
$stmt->close();

if ($success == false) {
  die ("MySQL Error");
}
if (empty($server_id)) {
  die("Invalid Key!");
}

if(!is_numeric($uptime)){ addLog($user_id,1,"Uptime contains invalid Letters!"); die("Uptime contains invalid Letters!");}
if(!preg_match("/^[A-Za-z0-9.-]+$/",$key)){ addLog($user_id,1,"Kernel contains invalid Letters!"); die("Kernel contains invalid Letters!");}
if(!preg_match("/^[A-Za-z0-9 ()@.-]+$/",$cpu)){ addLog($user_id,1,"CPU Name contains invalid Letters!"); die("CPU Name contains invalid Letters!");}

if(!is_numeric($cpu_cores)){ addLog($user_id,1,"CPU Cores contains invalid Letters!"); die("CPU contains invalid Letters!");}
if(!is_numeric($cpu_mhz)){ addLog($user_id,1,"CPU Mhz contains invalid Letters!"); die("CPU contains invalid Letters!");}
if(!is_numeric($cpu_usage)){ addLog($user_id,1,"CPU Usage contains invalid Letters!"); die("CPU contains invalid Letters!");}
if(!is_numeric($cpu_steal)){ addLog($user_id,1,"CPU Steal contains invalid Letters!"); die("CPU contains invalid Letters!");}
if(!is_numeric($io_wait)){ addLog($user_id,1,"I/O Wait contains invalid Letters!"); die("IO contains invalid Letters!");}

if(!is_numeric($memory_total)){ addLog($user_id,1,"Memory Total contains invalid Letters!"); die("Memory contains invalid Letters!");}
if(!is_numeric($memory_free)){ addLog($user_id,1,"Memory Free contains invalid Letters!"); die("Memory contains invalid Letters!");}
if(!is_numeric($memory_cached)){ addLog($user_id,1,"Memory Cached contains invalid Letters!"); die("Memory contains invalid Letters!");}
if(!is_numeric($memory_buffer)){ addLog($user_id,1,"Memory Buffer contains invalid Letters!"); die("Memory contains invalid Letters!");}
if(!is_numeric($memory_active)){ addLog($user_id,1,"Memory Active contains invalid Letters!"); die("Memory contains invalid Letters!");}
if(!is_numeric($memory_inactive)){ addLog($user_id,1,"Memory Inactive contains invalid Letters!"); die("Memory contains invalid Letters!");}

if(!is_numeric($hdd_usage)){ addLog($user_id,1,"HDD Usage contains invalid Letters!"); die("HDD contains invalid Letters!");}
if(!is_numeric($hdd_total)){ addLog($user_id,1,"HDD Total contains invalid Letters!"); die("HDD contains invalid Letters!");}

if(!is_numeric($tx)){ addLog($user_id,1,"Network TX contains invalid Letters!"); die("NET contains invalid Letters!");}
if(!is_numeric($rx)){ addLog($user_id,1,"Network RX contains invalid Letters!"); die("NET contains invalid Letters!");}

$stmt = $database->prepare("SELECT server_uptime FROM servers WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $server_id);
if (!$stmt->execute()) { $success = false; }
$stmt->bind_result($db_uptime_before);
$stmt->fetch();
$stmt->close();

$last_update = time();

//Update IP/Uptime/CPU/Cores....
$stmt = $database->prepare("UPDATE servers SET server_ip = ?,server_uptime = ?,server_kernel = ?,server_cpu = ?,server_cpu_cores = ?,server_cpu_mhz = ?, last_update = ?  WHERE id = ?");
$stmt->bind_param('ssssidii',$ip,$uptime,$kernel,$cpu,$cpu_cores,$cpu_mhz,$last_update,$server_id);
if (!$stmt->execute()) { $success = false; }
$stmt->close();

if ($success == false) {
  addLog($user_id,1,"MySQL Error after Updating IP/Uptime...");
  die ("MySQL Error");
}

$server_time = time();

$stmt = $database->prepare("SELECT server_tx,server_rx FROM servers_data WHERE server_id = ? ORDER by id DESC LIMIT 1");
$stmt->bind_param('i', $server_id);
if (!$stmt->execute()) { $success = false; }
$stmt->bind_result($db_server_tx,$db_server_rx);
$stmt->fetch();
$stmt->close();

if ($success == false) {
  addLog($user_id,1,"MySQL Error after selecting TX/RX...");
  die ("MySQL Error");
}

$tx_diff = $tx - $db_server_tx;
$rx_diff = $rx - $db_server_rx;

$stmt = $database->prepare("INSERT INTO servers_data(server_id,memory_total,memory_free,memory_cached,memory_buffer,server_tx,server_rx,cpu_load,server_timestamp,server_tx_diff,server_rx_diff,memory_active,memory_inactive,hdd_usage,hdd_total,cpu_steal,io_wait) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ?, ?)");
$stmt->bind_param('iiiiiiididdiiiidd', $server_id,$memory_total,$memory_free,$memory_cached,$memory_buffer,$tx,$rx,$cpu_usage,$server_time,$tx_diff,$rx_diff,$memory_active,$memory_inactive,$hdd_usage,$hdd_total,$cpu_steal,$io_wait);
if (!$stmt->execute()) { $success = false; }
$stmt->close();

if ($success == false) {
  addLog($user_id,1,"MySQL Error after Insert new Data...");
  die ("MySQL Error");
}

//Limit Check
$stmt = $database->prepare("SELECT cpu_alert,cpu_alert_send,cpu_steal_alert,cpu_steal_alert_send,io_wait_alert,io_wait_alert_send,reboot_alert FROM servers WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $server_id);
if (!$stmt->execute()) { $success = false; }
$stmt->bind_result($db_cpu,$db_cpu_send,$db_cpu_steal,$db_cpu_steal_send,$db_io_wait,$db_io_wait_send,$db_reboot_alert);
$stmt->fetch();
$stmt->close();

if ($success == false) {
  die ("MySQL Error");
}

//CPU alert
if ($cpu_usage >= $db_cpu AND $db_cpu != NULL AND $db_cpu_send <= time()) {

    $msg = "Alert: The CPU Load of the Server ". escape($server_name) . " has reached " . $cpu_usage . "%";
    $headers = "From: "._email_sender."\r\n";
    mail(_email_target,"Ariane - CPU Load Alert - " . escape($server_name),$msg,$headers);

    $lock = strtotime('+30 minutes', time());

    $stmt = $database->prepare("UPDATE servers SET cpu_alert_send = ?  WHERE id = ?");
    $stmt->bind_param('ii',$lock,$server_id);
    $stmt->execute();
    $stmt->close();

  }

  //CPU Steal Alert
  if ($cpu_steal >= $db_cpu_steal AND $db_cpu_steal != NULL AND $db_cpu_steal_send <= time()) {

      $msg = "Alert: The CPU Steal of the Server ". escape($server_name) . " has reached " . $cpu_steal . "%";
      $headers = "From: "._email_sender."\r\n";
      mail(_email_target,"Ariane - CPU Steal Alert - " . escape($server_name),$msg,$headers);

      $lock = strtotime('+30 minutes', time());

      $stmt = $database->prepare("UPDATE servers SET cpu_steal_alert_send = ?  WHERE id = ?");
      $stmt->bind_param('ii',$lock,$server_id);
      $stmt->execute();
      $stmt->close();

    }

    //I/O Alert
    if ($io_wait >= $db_io_wait AND $db_io_wait != NULL AND $db_io_wait_send <= time()) {
        $msg = "Alert: The I/O Load of the Server ". escape($server_name) . " has reached " . $io_wait . "%";
        $headers = "From: "._email_sender."\r\n";
        mail(_email_target,"Ariane - I/O Wait Alert - " . escape($server_name),$msg,$headers);

        $lock = strtotime('+30 minutes', time());

        $stmt = $database->prepare("UPDATE servers SET io_wait_alert_send = ?  WHERE id = ?");
        $stmt->bind_param('ii',$lock,$server_id);
        $stmt->execute();
        $stmt->close();

      }

    //Reboot Alert
    if ($db_uptime_before > $uptime AND $db_reboot_alert == 1) {
        $msg = "Alert: The Server ". escape($server_name) . " has been rebooted.";
        $headers = "From: "._email_sender."\r\n";
        mail(_email_target,"Ariane - Reboot Alert - " . escape($server_name),$msg,$headers);
      }


echo "Okay";
 ?>
