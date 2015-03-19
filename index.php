<?php

require('vendor/autoload.php');

header("Access-Control-Allow-Origin: *");

map('GET', '/animals', function() {
    
});

map('GET', '/animal', function() {
    
});

map('GET', '/shelters', function() {
    
});

map('GET', '/shelter', function() {
    
});

config('url', "http://localhost/pttrv2-backend/");

dispatch();

?>