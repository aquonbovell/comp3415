<?php
if ($_REQUEST) {
  $pcode = $_REQUEST['pcode'];
  require_once '../../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->sales;
  $result = $collection->aggregate([
    ['$match' => ['items.ProdID' => $pcode]],
    ['$unwind' => '$items'],
    [
      '$group' => [
        '_id' => '$items.ProdID',
        "ProdName" => ['$first' => '$items.ProdName'],
        "amtSold" => ['$sum' => '$items.AmtSold'],
      ]
    ],
    ['$sort' => ['amtSold' => -1]],
    ['$limit' => 20]
  ]);
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