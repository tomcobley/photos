<?php
// file to update db based on requests made from the edit.php page

function giveErrorResponse($code, $message, $redirect=false) {
  // according to JSON:API

  $response = array();
  $response['errors'] = array();
  $response['errors']['status'] = strval($code);
  $response['errors']['title'] = $message;
  if ($redirect) {
    $response['errors']['redirect'] = $redirect;
  }

  echo json_encode($response);

}


// before doing anything else, check user is authed
// if not, redirect to auth page
session_start();
if ( !( isset($_POST['auth_token']) && $_POST['auth_token'] === $_SESSION['auth_token'] ) ) {
  // no auth
  giveErrorResponse("", "Request denied. No authorisation.", "auth.php");
  exit();
}


if (!isset($_POST['request_type'])) {
  die("No request_type set in URL");
}

// wrap all code in try catch block
try {

  require "db/SQLiteClasses.php";
  $request_type = htmlspecialchars($_POST['request_type']);

  $db = (new SQLiteConnection())->connect();


  if ($request_type === 'insert') {

    $data = json_decode( $_POST['data'] , true );

    if (isset($data['elementType']) && $data['elementType'] === 'divider') {

      $timestamp = htmlspecialchars($data['timestamp']);

      $insertDb = new SQLiteInsert($db);

      // insert divider and record id
      $dividerId = $insertDb -> insertDivider();

      // insert content item for divider
      $insertDb -> insertContentItem('divider', $dividerId, $timestamp);

      //throw new \Exception("Error Processing Request", 1);


      http_response_code(201);

    }


  } else if (in_array($request_type, ['show', 'hide', 'update'])) {

    // content_item_id of target
    $target_id = htmlspecialchars($_POST['target_id']);

    $updateDb = new SQLiteUpdate($db);


    if ($request_type === 'show' || $request_type === 'hide') {

      $hidden = ($request_type === 'show' ? 0 : 1);
      $updateDb -> updateRecord('content_items', 'content_item_id', $target_id, array('hidden' => $hidden) );


    } else if ($request_type === 'update') {
      $target_attr = htmlspecialchars($_POST['target_attribute']);
      $target_attr_value = htmlspecialchars($_POST['target_attribute_value']);

      if (in_array($target_attr, ['timestamp'])) {
        $target_table = 'content_items';
        $target_id_name = 'content_item_id';

      } else {
        // we need to query db to find actual target id (foreign id)
        $findRecord = new SQLiteFindRecord($db);

        if (in_array($target_attr, ['city', 'country'])) {
          $item_type = 'divider';
        } else if (in_array($target_attr, ['title'])) {
          $item_type = 'image';
        }

        $target_id_name = $item_type.'_id';
        $target_table = $item_type.'s';

        $result = $findRecord->search('content_items', 'default', 'content_item_id', $target_id);
        $target_id = $result[0][$item_type.'_id'];

      }


      $updateDb -> updateRecord($target_table, $target_id_name, $target_id, array($target_attr => $target_attr_value) );

      http_response_code(200);
    }

  } else {
    giveErrorResponse(400, 'Unknown request_type received.');
  }

} catch ( \PDOException $e) {

  giveErrorResponse(400, 'Database error: '.$e);

} catch ( \Exception $e) {

  error_log($e);
  giveErrorResponse(500, 'Internal server error: '.$e);

}




?>
