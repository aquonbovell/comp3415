<?php
require_once "../vendor/autoload.php";
$collection = (new MongoDB\Client("mongodb://localhost:27017"))->mflix_lab->movies;
$result = $collection->findOne([], ['sort' => ['_id' => -1]]);
if ($result) {
	echo json_encode($result);
} else {
	echo json_encode(['status' => false]);
}