<?php
/**
    APICall Class
    Sets up our API service to instantiate the various legacy API systems
    Pttr can connect to and normalizes their response to the structure
    as defined in the README and wiki developer docs
*/

namespace Pttr;

use Pttr\Utility\Config;
use ReflectionClass;

class APICall {
 
    private $dataApiKeys;   // Data API Keys stored in .config.php
    private $geoApiKeys;    // Geo API keys stored in .config.php
    
    private $apiBunch;      // Array of Apiable instances
    private $geoBunch;      // Intelocationable instance
    
    private $config;
    
    public function __construct() {
        $this->config = new Config();
        $settings = $this->config->getSettings();
        
        if (array_key_exists('dataApis', $settings)) {
            $this->dataApiKeys = $settings['dataApis'];
        }
        
        if (array_key_exists('geoApis', $settings)) {
            $this->geoApiKeys = $settings['geoApis'];
        }
        
        foreach ($this->dataApiKeys as $apiService => $apiLogin) {
            $apiClassName = '\Pttr\API' . '\\' . $apiService;
            if (class_exists($apiClassName)) {
                $apiInstance = new $apiClassName($apiLogin);
                if ($apiInstance instanceof \Pttr\API\APIable) {
                    $this->apiBunch[] = $apiInstance;
                }
            }
        }
        
        foreach ($this->geoApiKeys as $apiService => $apiLogin) {
            $apiClassName = '\Pttr\Utility\Intelocation' . '\\' . $apiService;
            if (class_exists($apiClassName)) {
                $apiInstance = new $apiClassName($apiLogin);
                if ($apiInstance instanceof \Pttr\Utility\Intelocation\Intelocationable) {
                    $this->geoBunch = $apiInstance;
                }
            }
        }
    }
    
    public function getShelters() {
        return $this->combineResponse("getShelters");
    }
    
    
    public function getShelter($identifier) {
        list($id, $remoteApiToCall) = $this->parseId($identifier);
        return $this->singularResponse($remoteApiToCall, "getShelter", array($id));
    }
    
    public function getAnimals() {
        return $this->combineResponse("getAnimals");
    }
    
    public function getAnimal($identifier) {
        list($id, $remoteApiToCall) = $this->parseId($identifier);
        return $this->singularResponse($remoteApiToCall, "getAnimal", array($id));
    }
    
    /**
        Parses the ID, which is composed of the name of the API the object came from
        and the actual ID of that object.
    */
    private function parseId($identifier) {
        $fromApi = strstr($identifier, '-', TRUE);
        $id = substr(strstr($identifier, '-'), 1);
        return array($id, $fromApi);
    }
    
    /**
        Combines the responses from various APIs into one array
    */
    private function combineResponse($methodName, $methodArguments = array()) {
        $responseSet = array();
        if (!isset($_GET['city']) 
            && $this->geoBunch instanceof \Pttr\Utility\Intelocation\Intelocationable
            && ($this->geoBunch->getCity() != "-" || $this->geoBunch->getState() != "-")
        ) {
            $_GET['city'] = $this->geoBunch->getCity() . "," . $this->geoBunch->getState();
        }
        // For each API service that we have, call the requested method and pass arguments through
        foreach ($this->apiBunch as $apiService) {
            $apiServiceResponse = call_user_func_array(
                array($apiService, $methodName), $methodArguments
            );
            if (is_array($apiServiceResponse)) {
                $responseSet += $apiServiceResponse;
            }
        }
        return $responseSet;
    }
    
    /**
        Calls the class and class method in question
    */
    private function singularResponse($className, $methodName, $methodArguments = array()) {
        $response = array();
        foreach ($this->apiBunch as $apiService) {
            if ((new ReflectionClass($apiService))->getShortName() === $className) {
                $apiServiceResponse = call_user_func_array(
                    array($apiService, $methodName), $methodArguments  
                );
                if (is_array($apiServiceResponse)) {
                    return $apiServiceResponse;
                }
            }
        }
        return $response;
    }
    
    
}

?>