<?php
include 'pages/functions.php';

$query = "SELECT id,server_timestamp FROM servers_data ORDER by id";

if ($result = $mysqli->query($query)) {

    /* fetch object array */
    while ($row = $result->fetch_row()) {
      $time = time();
      $delete = strtotime('+2 day', $row[1]);
      if ($time > $delete) {
        $stmt = $mysqli->prepare("DELETE FROM servers_data WHERE id = ?");
        $stmt->bind_param('i', $row[0]);
        $stmt->execute();
        $stmt->close();
      }
    }
    /* free result set */
    $result->close();
}


echo "ok";

?>
