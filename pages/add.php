<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

  $name = $_POST['servername'];

  if(!preg_match("/^[a-zA-Z0-9 .]+$/",$name)){ $msg = "This Username contains invalid letters (a-z,A-Z,0-9 are allowed).<br>";  $error = true;}

    if ($error == false) {

      $key = randomPassword();

      $stmt = $database->prepare("INSERT INTO servers(user_id,server_name,server_key) VALUES (?, ?, ?)");
      $stmt->bind_param('iss', $USER_ID,$name,$key);
      if (!$stmt->execute()) {
        $error = true;
      }
      $stmt->close();

    }
?>

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
