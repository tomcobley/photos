<?php
// check auth
require "../auth/check_auth.php";
require "../functions.php";
require "../db/SQLiteClasses.php";

$newSrc = 'http://localhost:80/api/img/';

$db = (new SQLiteConnection())->connect();

$updateDb = new SQLiteUpdate($db);

$findRecord = new SQLiteFindRecord($db);

$result = $findRecord->search('images', 'default');

pretty_var_dump($result);
foreach ($result as $row) {
  $updateDb -> updateRecord('images', 'image_id', $row['image_id'],
    array('thumbnail_src' => $newSrc.'thumbnails/'.$row['image_id'].'.jpg',
          'src' => $newSrc.'images/'.$row['image_id'].'.jpg')
    );

}



 ?>
