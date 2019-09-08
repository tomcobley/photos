<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Edit</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <link href="style/edit.css" rel="stylesheet">

</head>



<?php

function searchById($array, $id) {
  // id must be top-level attribute with key "id"

  foreach ($array as $item) {
    if ($item['id'] === $id) {
      return $item;
    }
  }
  return null;
}


function addEditPanel($contentItem, $imagesArray, $dividersArray) {

  $html = '<div data-content-item-id="'.$contentItem['id'].'" class="item-edit-panel">';

  $itemType = $contentItem['attributes']['content-type'];
  $actualContentId = $contentItem['attributes'][$itemType.'-id'];

  if ($itemType === 'image') {

    // get thumbnail src
    $html .= '<h6>Image ' .$actualContentId. '</h6>';
    $itemInfo = searchById($imagesArray, $actualContentId);
    $html .= '<img class="edit-mode-thumbnail" src="' .$itemInfo['attributes']['thumbnail-src']. '">';
    $html .= '<input name="image-title" value="' .$itemInfo['attributes']['title']. '" class="text edit-mode-text-input" >';


  } else if ($itemType === 'divider') {

    // get divider text
    $itemInfo = searchById($dividersArray, $actualContentId);
    $html .= '<h6>Divider ' .$actualContentId. '</h6>';
    $html .= '<input name="divider-city" value="' .$itemInfo['attributes']['city']. '" class="input edit-mode-text-input" >';
    $html .= '<input name="divider-country" value="' .$itemInfo['attributes']['country']. '" class="text edit-mode-text-input" >';

  }

  $html .= '</div>';

  echo $html;

}


require "functions.php";


$contentItemsArray = json_decode(file_get_contents('json/content-items.json') , true);
$imagesArray = json_decode(file_get_contents('json/images.json') , true);
$dividersArray = json_decode(file_get_contents('json/dividers.json') , true);

echo '<div class="edit-panels-wrapper">';
foreach ($contentItemsArray as $contentItem) {
  addEditPanel($contentItem, $imagesArray, $dividersArray);
}
echo '</div>';


 ?>
