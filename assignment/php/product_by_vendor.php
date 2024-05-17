<?php
if ($_REQUEST) {
  $vendorname = $_REQUEST['vendor'];
  $position = $_REQUEST['position'];
  $page = (int) $_REQUEST['page'];
  $limit = (int) $_REQUEST['limit'];
  $skip = ($page - 1) * $limit;
  require_once '../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->products;
  if ($position == -1) {
    $result = $collection->find(['vendors' => $vendorname], ['limit' => $limit, 'skip' => $skip]);
  } else {
    $result = $collection->find(['vendors.' . $position => $vendorname], ['limit' => $limit, 'skip' => $skip]);
  }

  foreach ($result as $entry) {
    $arr_out[] = $entry;
  }
  if (isset ($arr_out)) {
    echo json_encode($arr_out);
  } else {
    echo json_encode(['status' => false]);
  }
} else {
  echo json_encode(['status' => false]);
}

// Completed