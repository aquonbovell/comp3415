<?php
if ($_REQUEST) {
  $min = (float) $_REQUEST['min'];
  $max = (float) $_REQUEST['max'];
  $page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
  $limit = (int) $_REQUEST['limit'];
  $skip = ($page - 1) * $limit; //amount to skip in the database query
  require_once '../../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->products;
  $result = $collection->find(['price' => ['$gte' => $min, '$lte' => $max]], ['sort' => ['price' => 1, 'name' => 1], 'skip' => $skip, 'limit' => $limit]);
  foreach ($result as $entry) {
    $entry['cost'] = number_format($entry['cost'], 2);
    $entry['price'] = number_format($entry['price'], 2);
    $arr_out[] = $entry;
  }
  if (isset($arr_out)) {
    echo json_encode($arr_out);
  } else {
    echo json_encode(['status' => false]);
  }
} else {
  echo json_encode(['status' => false]);
}

// Completed