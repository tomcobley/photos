<?php
if (isset($_GET['redirect'])) {
  $redirect_uri = $_GET['redirect'];
} else {
  $redirect_uri = "";
}

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

  <link href="style/auth.css" rel="stylesheet">
  <link href="style/edit.css" rel="stylesheet">
  <script src="edit.js" charset="utf-8"></script>


</head>

<body>
  <form method="post" action="auth/process_auth.php">
    <div class="form-group">
      <label for="exampleInputPassword1">Enter Access Key</label>
      <input type="password" name="access_key" class="form-control">
    </div>
    <input name="redirect_uri" type="hidden" class="form-control" value="<?= $redirect_uri ?>">
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</body>
