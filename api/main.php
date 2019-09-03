<?php


function pretty_var_dump($data) {
  highlight_string("<?php\n\$data =\n" . var_export($data, true) . ";\n?>");
}

$sourceDirPath = "google-photos-dump/";
$destDirPath = "public/";

$sourceDirContents = scandir($sourceDirPath);

// var_dump($sourceDirContents);

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

  echo "<br>$filename";

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

pretty_var_dump($images);

foreach ($images as $imageCode => $imageInfo) {
  // make copy of image
  copy($sourceDirPath.$imageInfo['filename'], $destDirPath.'images/'.$imageInfo['filename']);
  // make thumbnail of image by cropping and compressing 
}

 ?>
