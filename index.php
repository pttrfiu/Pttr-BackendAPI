<?php

require('vendor/autoload.php');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

map('GET', '/animals', function() {
    $apiCall = new \Pttr\APICall();
    $apiCall->getAnimals();
});

map('GET', '/animal/<id>', function($params) {
    $apiCall = new \Pttr\APICall();
    $apiCall->getAnimal($params['id']);
});

map('GET', '/shelters', function() {
    $apiCall = new \Pttr\APICall();
    $apiCall->getShelters();
});

map('GET', '/shelter/<id>', function($params) {
    $apiCall = new \Pttr\APICall();
    $apiCall->getShelter($params['id']);
});

config('url', "http://localhost/pttrv2-backend/");

dispatch();

?>