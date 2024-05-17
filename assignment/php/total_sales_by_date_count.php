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
  require_once '../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->sales;
  $result = $collection->aggregate([
    ['$match' => ['salesdate' => ['$gte' => $startD, '$lt' => $finishD]]],
    [
      '$group' => [
        '_id' => [
          'day' => ['$dateToString' => ['format' => '%d', 'date' => '$salesdate']],
          'year' => ['$dateToString' => ['format' => '%Y', 'date' => '$salesdate']],
        ],
      ]
    ],
    ['$count' => 'total'],
  ]);
  foreach ($result as $entry) {
    echo json_encode($entry);
  }
} else {
  echo json_encode(['status' => false]);
}

// Completed