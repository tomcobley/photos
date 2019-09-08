<?php

require "db/SQLiteClasses.php";

$pdo = (new SQLiteConnection())->connect();
$sqlite = new SQLiteInsert($pdo);

// insert a new project
$projectId = $sqlite->insertProject('PHP SQLite Demo');
// insert some tasks for the project
$sqlite->insertTask('Prepare the other sample database schema', '2016-06-01', '2016-06-01', 1, $projectId);
$sqlite->insertTask('Prepare the sample database schema', '2016-06-01', '2016-06-01', 1, $projectId);
$sqlite->insertTask('Create new tables ', '2016-05-01', null, 0, $projectId);
$sqlite->insertTask('Insert some sample data', '2016-05-01', '2016-06-02', 1, $projectId);

// insert a second project
$projectId = $sqlite->insertProject('Mastering SQLite');
// insert the tasks for the second project
$sqlite->insertTask('Go to sqlitetutorial.net', '2016-06-01', null, 0, $projectId);
$sqlite->insertTask('Read all the tutorials.', '2016-06-01', null, 0, $projectId);
$sqlite->insertTask('Use Try It page to practice the SQLite commands.', '2016-06-01', null, 0, $projectId);
$sqlite->insertTask('Develop a simple SQLite-based application', '2016-06-15', null, 0, $projectId);
