<?php
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

  if (!isSecure()) {
    $msg = "The Login works only with SSL enabled."; $error = true;
  }

  if ($_SERVER['HTTP_HOST'] != _Domain) {
    $msg = "Domain seems to be invalid, check config."; $error = true;
  }

    if ($error == false) {

      $success = false;

      $stmt = $database->prepare("SELECT password,id FROM users WHERE username = ? LIMIT 1");
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
        echo '<meta http-equiv="Refresh" content="2; url=index.php?page=dashboard">';
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
            <?php if ($error == true) { echo'<div class="alert alert-danger"><h4><center>Error!</center></h4><p><center>'.$msg.'</center></p></div>';  }
            elseif ($error == false AND $success == true) { echo'<div class="alert alert-success"><h4><center>Success!</center></h4><p><center>Redirection in 2 seconds otherwise <a style="color:white;" href="index.php?page=dashboard">click here</a></center></p></div>'; } ?>
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
