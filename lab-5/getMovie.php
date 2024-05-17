<?php
$_id = $_REQUEST['_id'];
require_once "../vendor/autoload.php";
$collection = (new MongoDB\Client("mongodb://localhost:27017"))->mflix_lab->movies;
try {
	$result = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID($_id)]);
	if ($result) {
		echo json_encode($result);
	} else {
		echo json_encode(['status' => 'not found']);
	}
} catch (Exception $e) {
	echo json_encode(['status' => false]);
}