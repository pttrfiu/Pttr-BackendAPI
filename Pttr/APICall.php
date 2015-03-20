<?php
/**
    APICall Class
    Sets up our API service to instantiate the various legacy API systems
    Pttr can connect to and normalizes their response to the structure
    as defined in the README and wiki developer docs
*/

namespace Pttr;

class APICall {
 
    private $apiKeys;   // API Keys stored in .apikeys.php
    
    public function __construct() {     
        $this->apiKeys = require(__DIR__ . '/../.apikeys.php');
        
        foreach ($this->apiKeys as $apiService => $apiLogin) {
            $apiClassName = '\Pttr\API' . '\\' . $apiService;
            if (class_exists($apiClassName)) {
                $this->apiBunch[] = new $apiClassName($apiLogin);
            }
        }
    }
    
    public function getShelters() {
        return $this->combineResponse("getShelters", func_get_args());
    }
    
    
    public function getShelter($identifier) {
        return $this->combineResponse("getShelter", func_get_args());
    }
    
    public function getAnimals() {
        return $this->combineResponse("getAnimals", func_get_args());
    }
    
    public function getAnimal($identifier) {
        return $this->combineResponse("getAnimal", func_get_args());
    }
    
    /**
        Combines the responses from various APIs into one array
    */
    private function combineResponse($methodName, $methodArguments) {
        $responseSet = array();
        foreach ($this->apiBunch as $apiService) {
            $responseSet[] = call_user_func_array(
                array($apiService, $methodName), $methodArguments
            );
        }
        return $responseSet;
    }
    
}

?>