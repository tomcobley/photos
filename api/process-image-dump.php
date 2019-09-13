<?php
// check auth
require "auth/check_auth.php";

// remove execution time limit to allow long processing times
set_time_limit(0);

require_once "db/SQLiteClasses.php";
require_once "functions.php";

// import fn-generate-thumbnails.php
require "lib/fn-generate-thumbnails.php";
// set thumbnail dimensions in px
$thumbnailWidth = 300;
$thumbnailHeight = 300;

$sourceDirPath = "google-photos-dump/";
$destDirPath = "../public/";

$sourceDirContents = scandir($sourceDirPath);

if (count($sourceDirContents) === 2) {
  echo "<br>No files found in source directory ( apart from '.' and '..' ).";
}


$images = array();

foreach ($sourceDirContents as $filename) {
  // ignore album metadata file
  if ($filename === '.' || $filename === '..' || $filename === 'metadata.json') {continue;}
  if (strpos($filename, '.json') !== false) {
    // json file so skip
    continue;
  }

  // reset var
  $useEditedImage = false;

  $imageCode = str_replace('.jpg', '', $filename);

  if (strpos($imageCode, '-edited') !== false) {
    $useEditedImage = true;
    $imageCode = str_replace('-edited', '', $imageCode);
  }


  if (array_key_exists($imageCode, $images) && $useEditedImage) {
    // image has already been added to array, but we need to replace it with the edited image
    $images[$imageCode]['filename'] = $filename;

  } else if (array_key_exists($imageCode, $images) && !$useEditedImage) {
    // the edited version of the image has already been added to array, so skip this one
    continue;

  } else {
    // the image does not already exist in the array, so add it
    $images[$imageCode] = array('filename' => $filename);
    // retrieve the metadata from the relevant file and save to array \
    $metaData = json_decode(file_get_contents($sourceDirPath.$imageCode.'.jpg.json') , true);
    $images[$imageCode]['geoData'] = $metaData['geoData'];
    $images[$imageCode]['timestamp'] = $metaData['photoTakenTime']['timestamp'];

  }
}


// connect to db
$db = (new SQLiteConnection())->connect();
$findRecord = new SQLiteFindRecord($db);
$dbInsert = new SQLiteInsert($db);
$dbUpdate = new SQLiteUpdate($db);

$imageCount = count($images);
$currentImageCounter = 1;


foreach ($images as $imageCode => $imageInfo) {

  // check if image with this image code already exists in the database

  $record = $findRecord->search('images', 'default', 'image_code', $imageCode);
  //pretty_var_dump($record);

  if ($record) {
    // therefore image already exists in db, so find unique id of image
    $imageId = intval($record[0]['image_id']);

  } else {
    // image does not exist in database, so add it

    // add empty record to db and store returned ID value
    $imageId = intval($dbInsert->insertImage($imageCode, "", "", "", ""));

    // generate src and thumbnail src using id
    $dataToUpdate = array(
      'coords' => number_format($imageInfo['geoData']['latitude'], 4) . ', ' . number_format($imageInfo['geoData']['longitude'], 4),
      'altitude' => number_format($imageInfo['geoData']['altitude'], 1),
      'image_timestamp' => $imageInfo['timestamp'],
      'src' => 'http://localhost:80/public/images/'.$imageId.'.jpg',
      'thumbnail_src' => 'http://localhost:80/public/thumbnails/'.$imageId.'.jpg',
    );

    // now add these srcs to the record in the db
    $dbUpdate->updateRecord('images', 'image_id', $imageId, $dataToUpdate);
  }


  // check if image with this id already exists in content_items table of this database
  $record = $findRecord->search('content_items', 'default', 'image_id', $imageId);
  //pretty_var_dump($record);

  if (!$record) {
    // image does not exist in content_items table, so add it

    // add record to db
    $dbInsert->insertContentItem('image', $imageId, $imageInfo['timestamp']);

  }


  // copy image to public folder
  copy($sourceDirPath.$imageInfo['filename'], $destDirPath.'images/'.$imageId.'.jpg');

  // make thumbnail of image by cropping and compressing

  // generate thumbnail for each image
  generateThumbnail($sourceDirPath.$imageInfo['filename'],
                    $destDirPath.'thumbnails/'.$imageId.'.jpg',
                    $thumbnailWidth,
                    $thumbnailHeight
                   );

  echo "<br>Processed image $currentImageCounter of $imageCount. ";
  $currentImageCounter++;
}


// now empty source directory
foreach ($sourceDirContents as $filename) {
  // ignore non-files
  if ($filename === '.' || $filename === '..') continue;

  unlink($sourceDirPath.$filename);
}

?>
