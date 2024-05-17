<?php
require_once '../../../vendor/autoload.php';
$collection = (new MongoDB\Client)->POS->products;
$result = $collection->findOne([], ['sort' => ['_id' => 1]]);
$product = new stdClass();
$product->_id = (string) $result['_id'];
$product->Code = $result['code'];
$product->Name = $result['name'];
$product->Cost = $result['cost'];
$product->Price = $result['price'];
$product->Onhand = $result['onhand'];
$product->vendors = $result['vendors'];

echo json_encode($product);

// Completed