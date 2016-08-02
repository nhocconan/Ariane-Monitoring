<?php
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



<?php }
