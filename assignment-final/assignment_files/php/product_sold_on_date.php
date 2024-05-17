<?php
if ($_REQUEST) {
  $soldDate = $_REQUEST['soldDate'];
  $date = new DateTime($soldDate);
  $date->modify('+1 day');
  $soldDateNext = $date->format('Y-m-d');
  $soldDate = new MongoDB\BSON\UTCDateTime(new DateTimeImmutable($soldDate));
  $soldDateNext = new MongoDB\BSON\UTCDateTime(new DateTimeImmutable($soldDateNext));

  require_once '../../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->sales;
  $result = $collection->aggregate([
    ['$match' => ['salesdate' => ['$gte' => $soldDate, '$lt' => $soldDateNext]]],
    ['$unwind' => '$items'],
    [
      '$group' => [
        '_id' => '$items.ProdID',
        "ProdID" => ['$first' => '$items.ProdID'],
        "ProdName" => ['$first' => '$items.ProdName'],
        "Unit" => ['$first' => '$items.UnitPrice'],
        "AmtSold" => ['$sum' => '$items.AmtSold'],
        "subtotal" => ['$sum' => ['$multiply' => ['$items.UnitPrice', '$items.AmtSold']]],
      ]
    ],
    ['$sort' => ['_id' => 1]]
  ]);
  foreach ($result as $entry) {
    $entry['SubTotal'] = number_format($entry['subtotal'], 2);
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