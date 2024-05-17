<?php
if ($_REQUEST) {
  require_once '../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->products;
  $regex = new MongoDB\BSON\Regex('^' . $_REQUEST['code'], 'i');
  $result = $collection->find(['name' => $regex], ['limit' => 8, 'sort' => ['name' => 1]], ['projection' => ['name' => 1, 'price' => 1, 'code' => 1]], );
  foreach ($result as $row) {
    $arr_out[] = $row;
  }
  if (isset($arr_out)) {
    echo json_encode($arr_out);
  } else {
    echo json_encode([]);
  }
} else {
  echo json_encode([]);
}

// Completed