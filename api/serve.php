<?php

function give404() {
  http_response_code(404);
  die();
}

if (!(isset($_GET['noContentType']) && $_GET['noContentType'])) {
  header('Content-Type: application:/vnd.api+json');
}

require "db/SQLiteClasses.php";
// check if image with this image code already exists in the database
$db = (new SQLiteConnection())->connect();
$findRecord = new SQLiteFindRecord($db);

require "functions.php";


if (isset($_GET['allRecords']) && $_GET['allRecords']) {
  $getAllRecords = true;

} else {
  $getAllRecords = false;
  $recordId = $_GET['id'];
}

$recordType = $_GET['type'];
if ($recordType === 'content-item') {
  $recordTableName = 'content_items';
  $recordIdColumnName = 'content_item_id';
  $recordTypePlural = 'content-items';

} else if ($recordType === 'image' || $recordType === 'divider') {
  $recordTableName = $recordTypePlural = $recordType .'s';
  $recordIdColumnName = $recordType . '_id';

} else {
  // invalid type
  give404();
}


// Retrieve data from db

if ($getAllRecords) {
  // get all records from db
  $dataArray = $findRecord -> search($recordTableName);

} else {
  // get requested record from db
  $dataArray = $findRecord -> search($recordTableName, $recordIdColumnName, $recordId);
}

// pretty_var_dump($dataArray);

if (!$dataArray) {
  // no data was returned so give 'Not Found' response
  give404();
}

// array of attributeTitle => dbColumnName for each type of data
$attributesToReturn = array(

  'content-item' => array(
    'content-type' => 'content_type',
    'position' => 'position',
    'divider-id' => 'divider_id',
    'image-id' => 'image_id'
  ),
  'image' => array(
    'title' => 'title',
    'coords' => 'coords',
    'src' => 'src',
    'thumbnail-src' => 'thumbnail_src',
    'altitude' => 'altitude',
    'timestamp' => 'image_timestamp'
  ),
  'divider' => array(
    'city' => 'city',
    'country' => 'country'
  )

);

// form output data response (to be converted to valid JSON)

if ($getAllRecords) {
  $outputData = array('data' => [] );
} else {
  $outputData = array('data' => array() );
}

$attributes = array();

foreach ($dataArray as $data) {

  // generate array with attributes
  foreach ($attributesToReturn[$recordType] as $attributeName => $indexName) {
    $attributes[$attributeName] = $data[$indexName];
  }

  $recordOutputData = array(
    'type' => $recordTypePlural,
    'id' => $data[$recordIdColumnName],
    'attributes' => $attributes
  );

  if ($getAllRecords) {
    $outputData['data'][] = $recordOutputData;
  } else {
    $outputData['data'] = $recordOutputData;
  }

}

// pretty_var_dump($outputData);

// convert php object to json and output
$outputJSON = json_encode($outputData);
if (!$outputJSON) {
  error_log('FATAL ERROR: PHP object could not be encoded to JSON');
  give404();
}
echo $outputJSON;


?>
