<?php
if ($_REQUEST) {
  require_once '../../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->products;
  $result = $collection->findOne(['code' => $_REQUEST['code']]);
  if ($result) {
    $product = new stdClass();
    $product->_id = (string) $result['_id'];
    $product->Code = $result['code'];
    $product->Name = $result['name'];
    $product->Cost = $result['cost'];
    $product->Price = $result['price'];
    $product->Onhand = $result['onhand'];
    $product->vendors = $result['vendors'];
    echo json_encode($product);
  } else {
    $null_product = new stdClass();
    $null_product->Name = '';
    echo json_encode($null_product);
  }
}

// Completed