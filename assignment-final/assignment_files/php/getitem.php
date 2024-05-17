<?php
if ($_REQUEST) {
  require_once '../../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->products;
  $result = $collection->findOne(['code' => $_REQUEST['code']]);
  if ($result) {
    $product = new stdClass();
    $product->Name = $result['name'];
    $product->Unit = $result['price'];
    echo json_encode($product);
  } else {
    $product = new stdClass();
    $product->Name = '';
    echo json_encode($product);
  }
} else {
  $product = new stdClass();
  $product->Name = '';
  echo json_encode($product);
}

// Completed