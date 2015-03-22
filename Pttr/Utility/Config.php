<?php
/**
    Config class
    Reads our configuration file and allows for our application
    to then read and access such configuration settings.
*/

namespace Pttr\Utility;

class Config {
    
    private $config;
    
    public function __construct() {
        $this->config = require(__DIR__ . '/../../.config.php');
    }
    
    public function getSettings() {
        if (is_array($this->config)) {
            return $this->config;  
        }
        return array();  
    }
    
}

?>