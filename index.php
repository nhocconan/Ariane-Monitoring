<?php
include 'pages/functions.php';

if (isset($_GET["page"])) {
  $page = $_GET["page"];
}

if(!isset($page)) {
  $page="login";
}

$USER_ID = $_SESSION['user_id'];
if (!is_numeric($USER_ID)) {
  $USER_ID = "";
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Ariane</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/c3.min.css" rel="stylesheet">
    <link href="css/bg_night.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap-toggle.min.css" rel="stylesheet">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap-toggle.min.js"></script>

    <!-- Load d3.js and c3.js -->
    <script src="js/d3.min.js" charset="utf-8"></script>
    <script src="js/c3.min.js"></script>

  </head>
  <body>
<?php

$error = false;

if ($page=="login") {
    include 'pages/login.php';
 } elseif (startsWith($page,"dashboard?server=") AND $_SESSION['login'] === 1 AND $USER_ID != "") {
    include 'pages/dashboard.php';
  } elseif ($page == "logout") {
    session_unset();
    session_destroy();
    header('Location: index.php');
 } elseif ($page == "dashboard?account" AND $_SESSION['login'] === 1 AND $USER_ID != "") {
    include 'pages/account.php';
 } elseif ($page == "dashboard" AND $_SESSION['login'] === 1 AND $USER_ID != "") { ?>

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

<?php } elseif ($page == "dashboard?add" AND $_SESSION['login'] === 1) {

  if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

    $name = $_POST['servername'];

    if(!preg_match("/^[a-zA-Z0-9 .]+$/",$name)){ $msg = "This Username contains invalid letters (a-z,A-Z,0-9 are allowed).<br>";  $error = true;}

      if ($error == false) {

        $key = randomPassword();

        $stmt = $mysqli->prepare("INSERT INTO servers(user_id,server_name,server_key) VALUES (?, ?, ?)");
        $stmt->bind_param('iss', $USER_ID,$name,$key);
        if (!$stmt->execute()) {
          $error = true;
        }
        $stmt->close();

      }
?>

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
          <li><a href="index.php?page=dashboard">Servers</a></li>
          <li class="active"><a href="index.php?page=dashboard?add">Add Server</a></li>
          <li><a href="index.php?page=dashboard?account">Account</a></li>
          <li><a href="index.php?page=logout">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="col-md-4 col-md-offset-4 base-box">
  <form action="index.php?page=dashboard?add" method="post" style="margin-top:10px;margin-bottom:10px;">
    <h2><center>Install Agent</center></h2>
      <?php

      if (empty($key)) {
        echo '<pre>Invalid Name</pre>';
      } else {
        echo '<p><center>Please execute this Command on your Box to Add the Server.</center></p>';
        echo '<pre>wget '._URL.'scripts/install.sh && bash install.sh '. $key.'</pre>';
      }

       ?>
</form>
</div>

<?php } else { ?>

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
            <li><a href="index.php?page=dashboard">Servers</a></li>
            <li class="active"><a href="index.php?page=dashboard?add">Add Server</a></li>
            <li><a href="index.php?page=dashboard?account">Account</a></li>
            <li><a href="index.php?page=logout">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="col-md-4 col-md-offset-4 base-box">
    <form action="index.php?page=dashboard?add" method="post" style="margin-top:10px;margin-bottom:10px;">
      <h2><center>New Server</center></h2>
      <div class="form-group">
        <label for="servername">Server Name</label>
        <input class="form-control" placeholder="Servername" name="servername"  value="">
      </div>
  <button type="submit" name="confirm" class="btn btn-primary">Submit</button>
</form>
</div>



<?php }} else { header('Location: index.php?page=login'); } ?>

    <script>
    jQuery(document).ready(function($) {
      $(".clickable-row").click(function() {
          window.document.location = $(this).data("href");
      });
    });
    </script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
