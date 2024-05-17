<?php
require_once __DIR__ . "/vendor/autoload.php";
$client = new MongoDB\Client("mongodb://localhost:27017");

$collection = $client->mflix->movies;
$result = $collection->find(['year' => ['$gte' => 2011]]);

foreach ($result as $entry) {
    echo $entry['title'] . "<br>";
}