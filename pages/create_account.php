<?php
exit();
include 'functions.php';

$name = "Test"; $password = "123456789";

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("INSERT INTO users(username,password) VALUES (?, ?)");
$stmt->bind_param('ss', $name,$hash);
$stmt->execute();
$stmt->close();


echo "okay";
 ?>
