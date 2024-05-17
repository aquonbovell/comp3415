<?php
if ($_REQUEST) {
  require_once "../vendor/autoload.php";
  $collection = (new MongoDB\Client)->ABC->Clients;
  $deletedOneResult = $collection->findOneAndDelete(['code' => $_REQUEST['code']]);
  if ($deletedOneResult) {
    echo json_encode(['status' => 'ok']);
  } else {
    echo json_encode(['status' => false]);
  }
} else {
  echo json_encode(['status' => false]);
}

