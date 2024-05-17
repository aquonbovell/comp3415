<?php
require_once "../vendor/autoload.php";
$myItem = $_REQUEST['item'];
$field = $_REQUEST['field'];
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
$collection = (new MongoDB\Client)->ABC->Clients;
$item_regex = new MongoDB\BSON\Regex($myItem, 'i');
$result = $collection->find([$field => $item_regex], ['limit' => 10]);
foreach ($result as $row) {
	$row['dob'] = getDOB($row['dob']);
	$arr_out[] = $row;
}
if (isset($arr_out)) {
	echo json_encode($arr_out);
} else {
	echo json_encode(['status' => false]);
}