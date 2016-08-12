<?php
exit() //Remove this Line if you want to create an account, PUT IT BACK AFTER ADDING IT OR DELETE THE FILE
include '../pages/functions.php';

//Well change that stuff here, its just an example
$name = "Test"; $password = "1234567899";

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $database->prepare("INSERT INTO users(username,password) VALUES (?, ?)");
$stmt->bind_param('ss', $name,$hash);
$stmt->execute();
$stmt->close();


echo "okay";
 ?>
