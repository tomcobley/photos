<?php
session_start();
$access_key = htmlspecialchars($_POST['access_key']);
$redirect_uri = htmlspecialchars($_POST['redirect_uri']);
if (!$redirect_uri) {
  $redirect_uri = '../edit.php';
}

if ($access_key === 'master83012?!') {
  // auth successful
  $_SESSION['auth'] = true;
  require "gen_token.php";

  header("Location: ".$redirect_uri);

} else {
  $_SESSION['auth'] = false;
  header("Location: auth.php?redirect=".$redirect_uri);
  exit();
}

?>
