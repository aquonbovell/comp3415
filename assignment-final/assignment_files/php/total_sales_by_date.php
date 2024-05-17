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
  $startD = $_REQUEST['start'];
  $finishD = $_REQUEST['finish'];
  $date = new DateTime($finishD);
  $date->modify('+1 day');
  $finishD = $date->format('Y-m-d');
  $startD = new MongoDB\BSON\UTCDateTime(new DateTimeImmutable($startD));
  $finishD = new MongoDB\BSON\UTCDateTime(new DateTimeImmutable($finishD));
  $page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
  $limit = (int) $_REQUEST['limit'];
  $skip = ($page - 1) * $limit;
  require_once '../../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->sales;
  $result = $collection->aggregate([
    ['$match' => ['salesdate' => ['$gte' => $startD, '$lt' => $finishD]]],
    [
      '$group' => [
        '_id' => [
          'day' => ['$dateToString' => ['format' => '%d', 'date' => '$salesdate']],
          'year' => ['$dateToString' => ['format' => '%Y', 'date' => '$salesdate']],
        ],
        'date' => ['$first' => '$salesdate'],
        'amt' => ['$sum' => '$salestotal'],
        'avgAmt' => ['$avg' => '$salestotal'],
        'counter' => ['$sum' => 1],
      ]
    ],
    ['$sort' => ['date' => -1]],
    ['$skip' => $skip],
    ['$limit' => $limit]
  ]);
  foreach ($result as $entry) {
    $entry['date'] = getDOB($entry['date']);
    $entry['amt'] = number_format($entry['amt'], 2);
    $entry['avgAmt'] = number_format($entry['avgAmt'], 2);
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