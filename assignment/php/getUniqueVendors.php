<?php
require_once '../../vendor/autoload.php';
$collection = (new MongoDB\Client)->POS->products;
$result = $collection->distinct('vendors');
echo json_encode($result);

// Completed