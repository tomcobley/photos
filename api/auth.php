<?php

/*
Auth info:
  * Auth types: edit and view

Edit info:
  * One of the permitted access keys must be provided to obtain access to edit page
  * At the start of each session, a random token (32 chars) will be generated and saved in the
    users session ('auth_token'). Every interaction made with the api will require this token

View info:
  * View access key stored in url as query parameter
  * At the start of each session, a random token (16 chars) will be generated and saved in
    the users session ('view_token'). Every interaction made with the api will require this token
  */

session_start();

$default_redirect = 'edit.php';
$permitted_auth_keys = ['master83012?!', 'jk3h42jh242kjh5HK535SKJHFK23HsfSLpl22zm56sx'];

$permitted_view_keys = ['passw0rd'];

if (isset($_POST['access_key'])) {
  // access_key in post so check it
  $access_key = htmlspecialchars($_POST['access_key']);

  if (isset($_POST['redirect_uri'])) {
    $redirect_uri = htmlspecialchars($_POST['redirect_uri']);
  } else {
    $redirect_uri = $default_redirect;
  }

  if (!$redirect_uri) {
    $redirect_uri = $default_redirect;
  }

  if (in_array($access_key, $permitted_auth_keys)) {
    // auth successful
    $_SESSION['auth_edit'] = true;
    require "auth/gen_token.php";
    // also grant view access
    $_SESSION['auth_view'] = true;
    require "auth/gen_view_token.php";

    header("Location: ".$redirect_uri);

  } else {
    $_SESSION['auth'] = false;
    // we are already on auth.php, so give an error message and user can try again
    $error_message = "Access Key not recognised";
  }

} else if (isset($_GET['key']) {
  // view key is set in url
  $view_key = $_GET['key'] // TODO continue


} else {

  // user must have just arrived on this page, so prepare for them to enter access key
  //    or redirect them if they are already authenticated

  if (isset($_GET['redirect']) && $_GET['redirect'] !== 'auth.php') {
    $redirect_uri = $_GET['redirect'];
  } else {
    $redirect_uri = $default_redirect;
  }


  if (isset($_SESSION['auth']) && $_SESSION['auth'] === true ) {
    // user is already authenticated so they don't need to reauthenticate

    // check they have an auth_token set in session
    if (!$_SESSION['auth_token']) {
      require "auth/gen_token.php";
    }

    header("Location: ".$redirect_uri);
    exit();
  }

}


?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Authentication</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <script src="vendor/jquery/jquery.min.js" charset="utf-8"></script>

  <link href="style/auth.css" rel="stylesheet">

</head>

<body>
  <form method="post">
    <?php //form posts to self  ?>
    <div class="form-group">
      <label for="exampleInputPassword1">Enter Access Key</label>
      <input type="password" name="access_key" class="form-control">
    </div>
    <input name="redirect_uri" type="hidden" class="form-control" value="<?= $redirect_uri ?>">
    <button type="submit" class="btn btn-primary">Submit</button>

    <?php if (isset($error_message)) echo '<p class="error">'.$error_message.'</p>'; ?>
  </form>
</body>
