<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top navbar-opacity">
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
      <ul class="nav navbar-nav">
        <li class="active"><a href="index.php?page=dashboard">Servers</a></li>
        <li><a href="index.php?page=dashboard?add">Add Server</a></li>
        <li><a href="index.php?page=dashboard?account">Account</a></li>
        <li><a href="index.php?page=logout">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

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
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $USER_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {

      $stmt = $mysqli->prepare("SELECT cpu_load, memory_free, memory_buffer, memory_cached, memory_total, hdd_total, hdd_usage   FROM servers_data WHERE server_id = ? ORDER by server_timestamp DESC LIMIT 1");
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
</div>
