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
  require_once "../vendor/autoload.php";
  $collection = (new MongoDB\Client)->ABC->Clients;
  $result = $collection->findOne(['code' => $_REQUEST['code']]);
  if (!$result) {
    echo json_encode(['status' => 'not found']);
    return;
  } else {
    $result['dob'] = getDOB($result['dob']);
    echo json_encode($result);
  }
} else {
  echo json_encode(['status' => false]);
}
