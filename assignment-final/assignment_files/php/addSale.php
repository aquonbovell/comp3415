<?php

if ($_REQUEST) {
	require_once "../../../vendor/autoload.php";

	$collection = (new MongoDB\Client)->POS->sales;
	$unique = $collection->findOneAndUpdate(
		['field' => 'unique'],
		['$inc' => ['unique_number' => 1]],
		[
			'upsert' => true,
			'returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
		]
	);
	$_REQUEST['salesno'] = $unique->unique_number;
	$dateTime = new DateTime();
	$utcDateTime = new MongoDB\BSON\UTCDateTime($dateTime);
	$_REQUEST['salesdate'] = $utcDateTime;
	$_REQUEST['items'] = json_decode($_REQUEST['items']);
	$_REQUEST['salestotal'] = (float) $_REQUEST['salestotal'];
	$_REQUEST['tenderedamount'] = (float) $_REQUEST['tenderedamount'];
	$result = $collection->insertOne($_REQUEST);

	$collection = (new MongoDB\Client)->POS->products;
	foreach ($_REQUEST['items'] as $item) {
		$collection->updateOne(
			['code' => $item->ProdID],
			['$inc' => ['onhand' => -1 * $item->AmtSold]],
		);
	}
}

// Completed