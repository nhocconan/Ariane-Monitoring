<?php
include 'pages/functions.php';

if (php_sapi_name() == "cli") { //Make sure you can only run it, with "php cron.php" localy

//Cleanup Data which is older then 30 Days
$query = "SELECT id,server_timestamp FROM servers_data ORDER by id";

if ($result = $database->query($query)) {

    /* fetch object array */
    while ($row = $result->fetch_row()) {
      $time = time();
      $delete = strtotime('+30 day', $row[1]);
      if ($time > $delete) {
        $stmt = $database->prepare("DELETE FROM servers_data WHERE id = ?");
        $stmt->bind_param('i', $row[0]);
        $stmt->execute();
        $stmt->close();
      }
    }
    /* free result set */
    $result->close();
}

//Cleanup Log entries which are older then 7 Days
$query = "SELECT id,timestamp FROM logs ORDER by id";

if ($result = $database->query($query)) {

    /* fetch object array */
    while ($row = $result->fetch_row()) {
      $time = time();
      $delete = strtotime('+7 day', $row[1]);
      if ($time > $delete) {
        $stmt = $database->prepare("DELETE FROM logs WHERE id = ?");
        $stmt->bind_param('i', $row[0]);
        $stmt->execute();
        $stmt->close();
      }
    }
    /* free result set */
    $result->close();
}

//Downtime Check
$query = "SELECT id,last_update,offline_alert,offline_alert_send,server_name  FROM servers ORDER by id";

if ($result = $database->query($query)) {

    /* fetch object array */
    while ($row = $result->fetch_row()) {
      $last_run = strtotime('+2 minutes', $row['1']);
      if ($row['2'] == 1 AND $row['3'] == 0 AND $last_run < time() AND $row['1'] != NULL) {

        $msg = "Alert: The Server ". escape($row['4']) . " has gone offline.";
        $headers = "From: "._email_sender."\r\n";
        mail(_email_target,"Ariane - Downtime Alert - " . escape($row['4']),$msg,$headers);

        $send = 1;

        $stmt = $database->prepare("UPDATE servers SET offline_alert_send = ?  WHERE id = ?");
        $stmt->bind_param('ii',$send,$row['0']);
        $stmt->execute();
        $stmt->close();

      } elseif ($row['2'] == 1 AND $row['3'] == 1 AND $last_run > time() AND $row['1'] != NULL) {

        $msg = "Alert: The Server ". escape($row['4']) . " is back Online.";
        $headers = "From: "._email_sender."\r\n";
        mail(_email_target,"Ariane - Uptime Alert - " . escape($row['4']),$msg,$headers);

        $send = 0;

        $stmt = $database->prepare("UPDATE servers SET offline_alert_send = ?  WHERE id = ?");
        $stmt->bind_param('ii',$send,$row['0']);
        $stmt->execute();
        $stmt->close();

      }
    }
    /* free result set */
    $result->close();
}



echo "Ok";

}

?>
