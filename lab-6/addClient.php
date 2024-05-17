<?php
if ($_REQUEST) {
	require_once "../vendor/autoload.php";
	$collection = (new MongoDB\Client)->ABC->Clients;
	$_REQUEST['dob'] = new MongoDB\BSON\UTCDateTime(new \DateTimeImmutable($_REQUEST['dob']));
	$insertOneResult = $collection->insertOne($_REQUEST);
	echo json_encode(['status' => true]);
} else {
	echo json_encode(['status' => false]);
}