<?php
// check auth
require "auth/check_auth.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Edit</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <script src="vendor/jquery/jquery.min.js" charset="utf-8"></script>

  <link href="style/edit.css" rel="stylesheet">
  <script src="lib/edit.js" charset="utf-8"></script>


</head>

<body>
  <input id="auth_token" name="auth_token" type="hidden" value="<?= $_SESSION['auth_token'] ?>">

  <div id="edit-panels-wrapper">
  </div>

</body>
