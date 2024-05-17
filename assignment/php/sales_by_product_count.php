<?php
if ($_REQUEST) {
  $pcode = $_REQUEST['pcode'];
  require_once "../../vendor/autoload.php";
  $collection = (new MongoDB\Client)->POS->sales;
  $result = $collection->aggregate([
    ['$unwind' => '$items'],
    ['$match' => ['items.ProdID' => $pcode]],
    [
      '$group' => [
        '_id' => [
          'day' => ['$dateToString' => ['format' => '%d', 'date' => '$salesdate']],
          'year' => ['$dateToString' => ['format' => '%Y', 'date' => '$salesdate']],
        ]
      ]
    ],
    ['$count' => 'total']
  ]);
  foreach ($result as $entry) {
    echo json_encode($entry);
  }
} else {
  echo json_encode(['status' => false]);
}

// Completed