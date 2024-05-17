<?php
if ($_REQUEST) {
  require_once "../../../vendor/autoload.php";
  $_id = $_REQUEST['_id'];
  $collection = (new MongoDB\Client)->POS->products;
  try {
    $result = $collection->findOneAndDelete(['_id' => new MongoDB\BSON\ObjectID($_id)]);
    if ($result) {
      echo json_encode($result);
    } else {
      echo json_encode(['status' => 'not found']);
    }
  } catch (Exception $e) {
    echo json_encode(['status' => false]);
  }
}

// Completed