
<?php

require "functions.php";


$recordType = $_GET['type'];

if (isset($_GET['allRecords']) && $_GET['allRecords']) {
  $getAllRecords = true;
  //echo "All records";
} else {
  $getAllRecords = false;
  $recordId = $_GET['id'];
  //echo "Record with id " . $_GET['id'];
}

// Retrieve json data and convert to php object
$fileContents = file_get_contents($recordType."s.json");


if ($getAllRecords) {
  // simply output all data from the file (already in JSON form)
  $outputData = $fileContents;

} else {
  // find the requested record

  $records = json_decode($fileContents, true);
  $recordExists = false;

  foreach ($records as $index => $record) {
    if ($record["id"] === $recordId) {
      $recordExists = true;
      $recordIndex = $index;
      break;
    }
  }

  // TODO: fix
  if (!$recordExists) {
    die("Could not find record");
  }

  // Note: even though only one record is being returned, JSON API specifies that
  //    it must be wrapped in an array [] anyway

  $recordEncoded = json_encode($records[$recordIndex]) or die('Error occured encoding json');
  $outputData = "[" . $recordEncoded . "]";

}

// format output in accordance with JSON:API
$outputFormatted = '{"data":'.$outputData.'}';

echo $outputFormatted;

//pretty_var_dump(json_decode($outputFormatted));

?>
