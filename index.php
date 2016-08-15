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
    <link rel="shortcut icon" href="">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Ariane</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap_flatly.min.css" rel="stylesheet">
    <link href="css/c3.min.css" rel="stylesheet">
    <link href="css/bg_night.css" rel="stylesheet">
    <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap-toggle.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery.min.js"></script>
    <script src="js/moment.js"></script>
    <script src="js/transition.js"></script>
    <script src="js/collapse.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-datetimepicker.js"></script>
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
    getNavbar($page);
    include 'pages/account.php';
 } elseif ($page == "dashboard" AND $_SESSION['login'] === 1 AND $USER_ID != "") {
    getNavbar($page);
    include 'pages/overview.php';
  } elseif (startsWith($page,"dashboard?remove=") AND $_SESSION['login'] === 1 AND $USER_ID != "") {
     getNavbar($page);
     include 'pages/overview.php';
  } elseif ($page == "overview" AND $_SESSION['login'] === 1 AND $USER_ID != "") {
     getNavbar($page);
     include 'pages/overview_graph.php';
  } elseif ($page == "dashboard?logs" AND $_SESSION['login'] === 1 AND $USER_ID != "") {
     getNavbar($page);
     include 'pages/logs.php';
 } elseif ($page == "dashboard?add" AND $_SESSION['login'] === 1) {
    getNavbar($page);
   include 'pages/add.php';
 } else { header('Location: index.php?page=login'); } ?>

    <script>
    jQuery(document).ready(function($) {
      $(".clickable-row").click(function() {
          window.document.location = $(this).data("href");
      });
    });
    </script>
  </body>
</html>
