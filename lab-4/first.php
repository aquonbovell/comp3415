<?php
require_once __DIR__ . "/vendor/autoload.php";

$client = new MongoDB\Client("mongodb://localhost:27017");

$collection = $client->mflix->movies;

$year = (int)$_POST['year'];

$filter = [];
if (isset($year) && $year != 0) {
	$filter = ['year' => ['$eq' => $year]];
}

$result = $collection->find($filter,['sort' => ['year' => -1],'limit' => 100]);

foreach ($result as $entry) {
	$search_arr_out[] = $entry;
}
if(isset($search_arr_out)){
	echo json_encode($search_arr_out);
}
else{
	echo json_encode(array('status'=> False));
}
