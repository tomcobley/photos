<?php

// import fn-generate-thumbnails.php
require "lib/fn-generate-thumbnails.php";
// set thumbnail dimensions in px
$thumbnailWidth = 300;
$thumbnailHeight = 300;

$sourceDirPath = "google-photos-dump/";
$destDirPath = "../public/";

$sourceDirContents = scandir($sourceDirPath);


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


// read in existing data from images json file
$imagesJSON = json_decode(file_get_contents('json/images.json') , true);
$id = 0;

foreach ($images as $imageCode => $imageInfo) {
  // make copy of image
  copy($sourceDirPath.$imageInfo['filename'], $destDirPath.'images/'.$imageInfo['filename']);
  // make thumbnail of image by cropping and compressing

  // create array to be converted to JSON and used by frontend (id is string)
  $imagesJSON[] = array('type' => 'images',
                        'id' => $id."",
                        'attributes' => array('title' => 'Sample Title',
                                              'coords' => 'Co-ords',
                                              'src' => 'http://localhost:80/public/images/'.$imageInfo['filename'],
                                              'thumbnail-src' => 'http://localhost:80/public/thumbnails/'.$imageInfo['filename']
                                             )
                        );

  // generate thumbnail for each image
  generateThumbnail($sourceDirPath.$imageInfo['filename'],
                    $destDirPath.'thumbnails/'.$imageInfo['filename'],
                    $thumbnailWidth,
                    $thumbnailHeight
                  );


  // increment id counter
  $id++;

}


$imagesJSONFilePath = "json/images.json";
$imagesJSONFile = fopen($imagesJSONFilePath, "w");
if (!$imagesJSONFile) {
  // error handling if file cannot be written to
  apiError("Failed to write to images JSON file with path " . $imagesJSONFilePath);
}
$imagesJSONConverted = json_encode($imagesJSON);
if (!$imagesJSONConverted) {
  // error handling if json did not encode correctly
  apiError("Failed to encode data to JSON " . $imagesJSONFilePath);
}
fwrite($imagesJSONFile, $imagesJSONConverted);
fclose($imagesJSONFile);

?>
