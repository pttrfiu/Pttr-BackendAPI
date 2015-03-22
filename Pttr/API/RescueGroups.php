<?php

namespace Pttr\API;

class RescueGroups implements APIable {
    
    private $url = "https://api.rescuegroups.org/http/json";
    private $apiKey;
    
    public function __construct($login) {
        $this->apiKey = $login['key'];
    }
    
    public function getShelters() {
        
    }
    
    public function getShelter($identifier) {
        
    }
    
    public function getAnimals() {
        
    }
    
    public function getAnimal($identifier) {
        
    }
    
    private function constructParameters($config) {
        
    }
    
    private function fireRequest() {
        
    }
    
}

?>