<?php
if ($_REQUEST) {
  $min = (float) $_REQUEST['min'];
  $max = (float) $_REQUEST['max'];
  require_once '../../vendor/autoload.php';
  $collection = (new MongoDB\Client)->POS->products;
  $result = $collection->countDocuments(['price' => ['$gte' => $min, '$lte' => $max]]);
  echo json_encode(['count' => $result]);
} else {
  echo json_encode(['count' => 0]);
}

// Completed