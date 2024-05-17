<?php
if ($_REQUEST) {
  require_once "../../../vendor/autoload.php";
  $collection = (new MongoDB\Client)->POS->products;
  $_REQUEST['price'] = floatval($_REQUEST['price']);
  $_REQUEST['cost'] = floatval($_REQUEST['cost']);
  $_REQUEST['onhand'] = intval($_REQUEST['onhand']);
  $_REQUEST['vendors'] = json_decode($_REQUEST['vendors']);
  $insertOneResult = $collection->updateOne(
    ['_id' => new MongoDB\BSON\ObjectId($_REQUEST['_id'])],
    ['$set' => [
      'name' => $_REQUEST['name'],
      'price' => $_REQUEST['price'],
      'cost' => $_REQUEST['cost'],
      'onhand' => $_REQUEST['onhand'],
      'vendors' => $_REQUEST['vendors'],
    ]],
  );
}

// Completed