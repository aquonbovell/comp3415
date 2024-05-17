<?php
$_id = $_REQUEST['_id'];
require_once "../vendor/autoload.php";
$collection = (new MongoDB\Client("mongodb://localhost:27017"))->mflix_lab->movies;
try {
	$result = $collection->findOne(['_id' => ['$lt' => new MongoDB\BSON\ObjectID($_id)]], ['sort' => ['_id' => -1]]);
	if ($result) {
		echo json_encode($result);
	} else {
		echo json_encode(['status' => 'no previous']);
	}
} catch (\Exception $e) {
	echo json_encode(['status' => false]);
}