<?php
exit();
include 'functions.php';

//Well change that stuff here, its just an example
$name = "Test"; $password = "1234567899";

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("INSERT INTO users(username,password) VALUES (?, ?)");
$stmt->bind_param('ss', $name,$hash);
$stmt->execute();
$stmt->close();


echo "okay";
 ?>
