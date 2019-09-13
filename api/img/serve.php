<?php

function give404() {
  http_response_code(404);
  die();
}
function give400() {
  http_response_code(400);
  die();
}

echo file_get_contents('img/file.txt');

$pathToImages = 'private/';

// all image requests are interpreted by htaccess and sent to this file so the
//    images can be taken from outside of the web root (for security) and
//    displayed
$type = (isset($_GET['type']) ? $_GET['type'] : '');

$id = (isset($_GET['id']) ? $_GET['id'] : '');

if (!$type || !$id) {
  // bad request
  give400();
}

$filePath = $pathToImages . $type.'s/' . $id . '.jpg';

if (!file_exists($filePath)) {
  give404();
}


header('Content-Type: image/jpeg');
header('Content-Length: ' . filesize($filePath));
echo file_get_contents($filePath);

?>
