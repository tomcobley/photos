<?php
// session must already be started
// assumes current php working directory is api/

if ( !(isset($_SESSION['auth']) && $_SESSION['auth'] === true ) ) {
  // user is not authenticated
  header("Location: ./auth.php");
  exit();
}
 ?>
