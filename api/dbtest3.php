<?php

require "db/SQLiteClasses.php";
require "functions.php";

// $db = (new SQLiteConnection())->connect();
//
// $stmt = $db->query("SELECT name
//                            FROM sqlite_master
//                            WHERE type = 'table'
//                            ORDER BY name");
// $tables = [];
// while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
//     $tables[] = $row['name'];
// }
// pretty_var_dump($tables);
//
//
// $stmt = $db->query("SELECT * FROM tasks");
//
// pretty_var_dump($stmt->fetch(\PDO::FETCH_ASSOC));

$db = (new SQLiteConnection())->connect();
$dbInsert = new SQLiteInsert($db);
$dbUpdate = new SQLiteUpdate($db);


$dbInsert->insertImage('IMG_20190511_075143', 'imagetitle', 'coords', 'source', 'th');
$dbUpdate->updateRecord('images', 'image_code', 'IMG_20190511_075143', array('image_code' => 'bob2', 'coords' => 3));


$checker = new SQLiteFindRecord($db);



var_dump ($checker->search('images', 'image_code', 'IMG_20190511_075143'));
// if (!($stmt->fetch(\PDO::FETCH_ASSOC))) {
//   $uniqueIdFound = true;
// }
