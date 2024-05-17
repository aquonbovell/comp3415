<?php
if ($_REQUEST) {
  $limit = (int) $_REQUEST['limit'];
  require_once '../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->sales;
  $result = $collection->aggregate([
    ['$unwind' => '$items'],
    [
      '$group' => [
        '_id' => '$items.ProdID',
        "pname" => ['$first' => '$items.ProdName'],
        "pcode" => ['$first' => '$items.ProdID'],
        "unit" => ['$sum' => '$items.UnitPrice'],
        "totalAmtSold" => ['$sum' => '$items.AmtSold'],
      ]
    ],
    ['$sort' => ['totalAmtSold' => -1]],
    ['$limit' => $limit]
  ]);
  foreach ($result as $entry) {
    $entry['unit'] = number_format($entry['unit'], 2);
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