<?php
// assumes current php working directory is api/

session_start();

if ( !(isset($_SESSION['auth']) && $_SESSION['auth'] === true ) ) {
  // user is not authenticated
  header("Location: ./auth.php");
  exit();
}
 ?>
