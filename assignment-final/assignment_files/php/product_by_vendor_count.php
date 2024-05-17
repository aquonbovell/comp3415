<?php
if ($_REQUEST) {
  $vendorname = $_REQUEST['vendor'];
  $position = $_REQUEST['position'];
  require_once '../../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->products;
  if ($position == -1) {
    $result = $collection->countDocuments(['vendors' => $vendorname]);
  } else {
  $result = $collection->countDocuments(['vendors.' . $position => $vendorname]);
  }
  echo json_encode(['count' => $result]);
} else {
  echo json_encode(['count' => 0]);
}

// Completed