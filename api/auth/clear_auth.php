<?php
//essentially log out
session_start();
$_SESSION['auth'] = false;
$_SESSION['token'] = "";

header("Location: ../auth.php");

 ?>
