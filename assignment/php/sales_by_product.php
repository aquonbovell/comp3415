<?php
function getDOB($date)
{
  try {
    $utcdatetime = new MongoDB\BSON\UTCDateTime((string) $date);
    $datetime = $utcdatetime->toDateTime();
    $mydate = $datetime->format('Y-m-d');
    return $mydate;
  } catch (\Exception $e) {
    return $date;
  }
}

if ($_REQUEST) {
  $code = $_REQUEST['pcode'];
  $page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
  $limit = (int) $_REQUEST['limit'];
  $skip = ($page - 1) * $limit;
  require_once '../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->sales;
  $result = $collection->aggregate([
    ['$unwind' => '$items'],
    ['$match' => ['items.ProdID' => $code]],
    [
      '$group' => [
        '_id' => [
          'day' => ['$dateToString' => ['format' => '%d', 'date' => '$salesdate']],
          'year' => ['$dateToString' => ['format' => '%Y', 'date' => '$salesdate']],
        ],
        'date' => ['$first' => '$salesdate'],
        'subtotal' => ['$sum' => '$items.UnitPrice'],
        'count' => ['$sum' => 1],
      ]
    ],
    ['$sort' => ['date' => -1]],
    ['$skip' => $skip],
    ['$limit' => $limit]
  ]);

  foreach ($result as $entry) {
    $entry['date'] = getDOB($entry['date']);
    $entry['subTotal'] = number_format($entry['subtotal'], 2);
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