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
    <link href="c3js/c3.min.css" rel="stylesheet">
    <link href="css/bg_night.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery.min.js"></script>

    <!-- Load d3.js and c3.js -->
    <script src="c3js/d3.min.js" charset="utf-8"></script>
    <script src="c3js/c3.min.js"></script>

  </head>
  <body>
<?php

$error = false;

if ($page=="login") {

  if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    if(!preg_match("/^[a-zA-Z0-9]+$/",$username)){ $msg = "This Username contains invalid letters (a-z,A-Z,0-9 are allowed).<br>";  $error = true;}
    if (strlen($password) < 10 ) {$msg = "The Password to short."; $error = true;}
    if (strlen($password) > 160 ) {$msg = "The Password is to long."; $error = true;}
    if (strlen($username) < 3 ) {$msg = "The Username is to short."; $error = true;}
    if (strlen($username) > 50 ) {$msg = "The Username is to long."; $error = true;}
    if ($password == "") { $msg = "You need to enter a Password"; $error = true;}
    if ($username == "") { $msg = "You need to enter a Username"; $error = true;}

      if ($error == false) {

        $success = false;

        $stmt = $mysqli->prepare("SELECT password,id FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        if ($stmt->execute()) { $success = true; }
        $stmt->bind_result($password_db,$id);
        $stmt->fetch();
        $stmt->close();

        if ($success) {
          if (password_verify($password, $password_db)) {
          // Success!
          $_SESSION['login'] = 1;
          $_SESSION['user_id'] = $id;
          header('Location: index.php?page=dashboard');
          } else {
            $error = true;
            $msg = "Incorrect Login.";
          }
        } else {
          $error = true;
          $msg = "MySQL Error.";
        }
      }
}
?>

    <div class="container">
        <div class="row vertical-offset-100">
        	<div class="col-md-4 col-md-offset-4">
        		<div class="panel panel-default box-opacity">
    			  	<div class="panel-heading">
    			    	<h3 class="panel-title">Please sign in</h3>
    			 	  </div>
    			  	<div class="panel-body">
              <?php if ($error == true) {
                echo'<div class="alert alert-danger">
                    <h4><center>Error!</center></h4>
                    <p><center>'.$msg.'</center></p>
                    </div>';  } ?>
    			    		<form action="index.php" method="post">
                        <fieldset>
    			    	  	<div class="form-group">
    			    		    <input class="form-control" placeholder="Username" name="username" type="text">
    			    		</div>
    			    		<div class="form-group">
    			    			<input class="form-control" placeholder="Password" name="password" type="password" value="">
    			    		</div>
    			    		<div class="checkbox">
    			    	    	<!---<label>
    			    	    		<input name="remember" type="checkbox" value="Remember Me"> Remember Me
    			    	    	</label>-->
    			    	    </div>
    			    		<input class="btn btn-lg btn-success btn-block" type="submit" name="confirm" value="Login">
    			    	</fieldset>
    			      	</form>
    			    </div>
    			</div>
    		</div>
    	</div>
    </div>

 <?php } elseif (startsWith($page,"dashboard?server=") AND $_SESSION['login'] === 1 AND $USER_ID != "") {

   include 'pages/dashboard.php';

  } elseif ($page == "logout") {

    session_unset();
    session_destroy();
    header('Location: index.php');


 } elseif ($page == "dashboard?account" AND $_SESSION['login'] === 1 AND $USER_ID != "") {

   $msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

   if (isset($_POST['confirm'])) {

    $old_pw = $_POST['old_pw'];
    $pw = $_POST['new_pw'];
    $pw2 = $_POST['new_pw2'];

    $success = true;

    if (strlen($pw) < 10 ) {$msg = "Passwords to short."; $success = false;}
    if (strlen($pw) > 160 ) {$msg = "Passwords are to long."; $success = false;}
    if ($pw != $pw2) {$msg = "Passwords not equal."; $success = false;}

    $stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param('i', $USER_ID);
    $stmt->execute();
    $stmt->bind_result($password_db);
    $stmt->fetch();
    $stmt->close();

    if ($password_db == NULL) {
      session_unset();
      session_destroy();
      header('Location: index.php');
    }

    if ($success == true) {
      if (password_verify($old_pw, $password_db)) {

        $hash = password_hash($pw, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("UPDATE users SET password = ?  WHERE id = ?");
        $stmt->bind_param('si',$hash,$_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();

        $msg = "Password changed.";
        $success = true;
      } else {
        $msg = "Old Password is incorrect.";
        $success = false;
      }
    }
  }
}


?>

   <div class="col-md-4 col-md-offset-4" style="background-color:white;opacity:0.85;margin-top:150px;border-radius:8px;">
     <ul class="nav nav-tabs">
       <li><a href="index.php?page=dashboard">Servers</a></li>
       <li><a href="index.php?page=dashboard?add">Add Server</a></li>
       <li class="active"><a href="index.php?page=dashboard?account">Account</a></li>
       <li><a href="index.php?page=logout">Logout</a></li>
     </ul>
     <form class="form-horizontal"  action="index.php?page=dashboard?account" method="post" >
       <h3>Account Password</h3>
       <?php
         if ($msg != "" and $success == false) {
           echo'<div class="alert alert-danger col-md-12" style="text-align: center;">
                 <h2>Error!</h3>
                 <p>'.$msg.'</p>
                 </div>';
         } elseif ($msg != "" and $success == true) {
           echo'<div class="alert alert-success col-md-12" style="text-align: center;">
                 <h2>Okay!</h3>
                 <p>'.$msg.'</p>
                 </div>';
         }
        ?>
      <div class="form-group">
          <label for="inputEmail" class="control-label col-xs-2">Old:</label>
          <div class="col-xs-6">
              <input type="password" class="form-control" placeholder="Old Password" name="old_pw">
          </div>
      </div>
      <div class="form-group">
          <label for="inputPassword" class="control-label col-xs-2">New:</label>
          <div class="col-xs-6">
              <input type="password" class="form-control" placeholder="New Password" name="new_pw">
          </div>
      </div>
      <div class="form-group">
          <label for="inputPassword" class="control-label col-xs-2">Repeat:</label>
          <div class="col-xs-6">
              <input type="password" class="form-control" placeholder="Repeat Password" name="new_pw2">
          </div>
      </div>
      <div class="form-group">
          <div class="col-xs-offset-2 col-xs-10">
              <button type="submit" name="confirm" class="btn btn-primary">Save</button>
          </div>
      </div>
  </form>

     </div>


   <?php

 } elseif ($page == "dashboard" AND $_SESSION['login'] === 1 AND $USER_ID != "") { ?>

  <meta http-equiv="refresh" content="60">
  <div class="col-md-4 col-md-offset-4 base-box">
    <form action="index.php?page=dashboard" method="post">
      <ul class="nav nav-tabs">
        <li class="active"><a href="index.php?page=dashboard">Servers</a></li>
        <li><a href="index.php?page=dashboard?add">Add Server</a></li>
        <li><a href="index.php?page=dashboard?account">Account</a></li>
        <li><a href="index.php?page=logout">Logout</a></li>
      </ul>
    </form>
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

         echo "<tr class='clickable-row' data-href='index.php?page=dashboard?server=".htmlspecialchars($row['id'],ENT_QUOTES)."'>";
           echo "<td>".htmlspecialchars($row['server_name'],ENT_QUOTES)."</td>";
           echo "<td>".htmlspecialchars($cpu_load,ENT_QUOTES)."%</td>";
           echo "<td>".htmlspecialchars(round(($memory_free + $memory_buffer + $memory_cached) / 1024,0)."/".round($memory_total / 1024,0),ENT_QUOTES)."MB</td>";
           echo "<td>".htmlspecialchars(round(($hdd_usage) / 1024 / 1024 / 1024,0)."/".round($hdd_total / 1024 / 1024 / 1024,0),ENT_QUOTES)."GB</td>";
           if (empty($row['server_ip'])) {
             echo "<td>n/a</td>";
           } else {
             echo "<td>".htmlspecialchars($row['server_ip'],ENT_QUOTES)."</td>";
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

    if(!preg_match("/^[a-zA-Z0-9 ]+$/",$name)){ $msg = "This Username contains invalid letters (a-z,A-Z,0-9 are allowed).<br>";  $error = true;}

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

<div class="col-md-4 col-md-offset-4" style="background-color:white;opacity:0.8;margin-top:150px;border-radius:8px;">
  <ul class="nav nav-tabs">
    <li><a href="index.php?page=dashboard">Servers</a></li>
    <li class="active"><a href="index.php?page=dashboard?add">Add Server</a></li>
    <li><a href="index.php?page=dashboard?account">Account</a></li>
    <li><a href="index.php?page=logout">Logout</a></li>
  </ul>
  <form action="index.php?page=dashboard?add" method="post" style="margin-top:10px;margin-bottom:10px;">
    <h2><center>Install Agent</center></h2>
      <p><center>Please execute this Command on your Box to Add the Server.</center></p>
      <pre>wget https://mon.x8e.net/install.sh && bash install.sh <?= $key ?></pre>
</form>
</div>

<?php } else { ?>

  <div class="col-md-4 col-md-offset-4" style="background-color:white;opacity:0.8;margin-top:150px;border-radius:8px;">
    <ul class="nav nav-tabs">
      <li><a href="index.php?page=dashboard">Servers</a></li>
      <li class="active"><a href="index.php?page=dashboard?add">Add Server</a></li>
      <li><a href="index.php?page=dashboard?account">Account</a></li>
      <li><a href="index.php?page=logout">Logout</a></li>
    </ul>
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
    <script src="js/bootstrap.js"></script>
  </body>
</html>
