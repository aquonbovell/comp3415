<?php
if ($_REQUEST) {
  $sdate = $_REQUEST['date'];
  $date = new DateTime($sdate);
  $date->modify('+1 day');
  $fdate = $date->format('Y-m-d');
  $sdate = new MongoDB\BSON\UTCDateTime(new DateTimeImmutable($sdate));
  $fdate = new MongoDB\BSON\UTCDateTime(new DateTimeImmutable($fdate));
  require_once '../../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->sales;
  $result = $collection->aggregate([
    ['$match' => ['salesdate' => ['$gte' => $sdate, '$lt' => $fdate]]],
    ['$unwind' => '$items'],
    ['$sort' => ['saleno' => -1]],
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