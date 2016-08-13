<meta http-equiv="refresh" content="60">
<div class="container base-box">
 <table class="table table-hover">
 <thead>
   <tr>
     <th>Server</th>
     <th>CPU Load</th>
     <th>Memory</th>
     <th>HDD</th>
     <th>IP</th>
     <th>Status</th>
   </tr>
 </thead>
 <tbody>

 <?php

    $query = "SELECT id,server_name,server_ip,server_uptime FROM servers WHERE user_id = ? ORDER by id";
    $stmt = $database->prepare($query);
    $stmt->bind_param('i', $USER_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {

      $stmt = $database->prepare("SELECT cpu_load, memory_free, memory_buffer, memory_cached, memory_total, hdd_total, hdd_usage   FROM servers_data WHERE server_id = ? ORDER by server_timestamp DESC LIMIT 1");
      $stmt->bind_param('i', $row['id']);
      $stmt->execute();
      $stmt->bind_result($cpu_load,$memory_free,$memory_buffer,$memory_cached,$memory_total,$hdd_total,$hdd_usage);
      $stmt->fetch();
      $stmt->close();

      echo "<tr class='clickable-row' data-href='index.php?page=dashboard?server=".escape($row['id'])."'>";
        echo "<td>".escape($row['server_name'])."</td>";
        echo "<td>".escape($cpu_load)."%</td>";
        echo "<td>".escape(round(($memory_free + $memory_buffer + $memory_cached) / 1024,0)."/".round($memory_total / 1024,0))."MB</td>";
        echo "<td>".escape(round(($hdd_usage) / 1024 / 1024 / 1024,0)."/".round($hdd_total / 1024 / 1024 / 1024,0))."GB</td>";
        if (empty($row['server_ip'])) {
          echo "<td>n/a</td>";
        } else {
          echo "<td>".escape($row['server_ip'])."</td>";
        }
        if (empty($row['server_uptime'])) {
          echo "<td>Needs Data Bro</td>";
        } else {
          echo "<td>Okay</td>";
        }
      echo "<tr>";
    }

?>
 </tbody>
</table>
<center><p>Version: 0.1.94</p></center>
</div>
