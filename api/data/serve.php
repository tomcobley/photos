<?php
require "../functions.php";

function give404() {
  http_response_code(404);
  die();
}

if (!(isset($_GET['noContentType']) && $_GET['noContentType'])) {
  header('Content-Type: application:/vnd.api+json');
}

// Note: currently, hiding records only hides them when all record are requested.
//    IF a record is requested by id, it will still be returned, even if hidden=1
// This is true for all item types
if (isset($_GET['includeHidden']) && $_GET['includeHidden']) {
  $includeHidden = true;
} else {
  $includeHidden = false;
}

require "../db/SQLiteClasses.php";
$db = (new SQLiteConnection())->connect('../db/phpsqlite.db');
$findRecord = new SQLiteFindRecord($db);


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
  $orderQuery = 'ORDER BY timestamp ASC';

} else if ($recordType === 'image' || $recordType === 'divider') {
  $recordTableName = $recordTypePlural = $recordType .'s';
  $recordIdColumnName = $recordType . '_id';
  if ($getAllRecords && $recordType === 'image') {
    $orderQuery = "ORDER BY image_timestamp ASC";
  } else {
    $orderQuery = "default";
  }

} else {
  // invalid type
  give404();
}


// Retrieve data from db

if ($getAllRecords) {

  // get all records from db
  if ($recordType === 'content-item' && !$includeHidden) {
    // don't include hidden items
    $dataArray = $findRecord -> search($recordTableName, $orderQuery, 'hidden', 0);
  } else if (!$includeHidden && ($recordType == 'image' || $recordType == 'divider')) {
    // record type is not content-item, but hidden items must not be included
    $dataArray = $findRecord -> search($recordTableName, $orderQuery);

    // loop through each of the returned items and remove from array if hidden
    // NOTE: this is absolutely not the best way to do this,
    //    but a quick solution was desired
    foreach ($dataArray as $i => $data) {

      if ($findRecord ->
        search('content_items', '', $recordType.'_id', $data[$recordType.'_id'])[0]['hidden'] === '1'
      ) {
        // item should be hidden, since hidden value in content_items table
        //    is 'truthy' for this record
        unset($dataArray[$i]);
      }
    }

  }
  else {
    // include hidden items
    $dataArray = $findRecord -> search($recordTableName, $orderQuery);
  }

} else {
  // get requested record from db
  $dataArray = $findRecord -> search($recordTableName, $orderQuery, $recordIdColumnName, $recordId);
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
    'timestamp' => 'timestamp',
    'divider-id' => 'divider_id',
    'image-id' => 'image_id',
    'hidden' => 'hidden'
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
$imageOrderVal = 0;

//pretty_var_dump($dataArray);


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

  if ($getAllRecords && $recordType === 'image') {
    // add ephemeral image-order information (auto-gen)
    $recordOutputData['attributes']['order-ephemeral'] = $imageOrderVal;
    $imageOrderVal++;

    $outputData['data'][] = $recordOutputData;
  } else if ($getAllRecords) {
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
