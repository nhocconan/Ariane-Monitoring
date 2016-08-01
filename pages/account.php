<?php

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

<div class="col-md-4 col-md-offset-4 base-box">
  <nav class="navbar navbar-default navbar-fixed-top">
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
          <li><a href="index.php?page=dashboard?add">Add Server</a></li>
          <li class="active"><a href="index.php?page=dashboard?account">Account</a></li>
          <li><a href="index.php?page=dashboard?logout">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
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
