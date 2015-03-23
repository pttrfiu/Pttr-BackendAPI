<?php

require('vendor/autoload.php');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

map('GET', '/animals', function() {
    $apiCall = new \Pttr\APICall();
    echo processOutput($apiCall->getAnimals());
});

map('GET', '/animal/<id>', function($params) {
    $apiCall = new \Pttr\APICall();
    echo processOutput($apiCall->getAnimal($params['id']));
});

map('GET', '/shelters', function() {
    $apiCall = new \Pttr\APICall();
    echo processOutput($apiCall->getShelters());
});

map('GET', '/shelter/<id>', function($params) {
    $apiCall = new \Pttr\APICall();
    echo processOutput($apiCall->getShelter($params['id']));
});

map(404, function() {
    echo processOutput('', 'error', '404 - That API endpoint does not exist');
});

function processOutput($output = '', $status = 'ok', $message = '') {
    if ($status == 'ok' && is_array($output)) {
        $output = json_encode(array(
            'status' => 'ok',
            'message' => $message,
            'results' => $output,
            'count' => count($output)
        ));
    } else {
        $output = json_encode(array(
            'status' => $status,
            'message' => $message,
            'results' => $output,
            'count' => '0'
        ));
    }
    
    if (isset($_GET['callback'])) {
        $output = $_GET['callback'] . '(' . $output . ')';
    }
    
    return $output;
}

config('url', "http://localhost/pttrv2-backend/");

dispatch();

?>