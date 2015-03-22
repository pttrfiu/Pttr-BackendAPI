<?php

/**
    IpInfoDb Class
    IpInfoDb is a concrete implementation of the Intelocationable
    interface, using the IpInfoDb service
*/

namespace Pttr\Utility\Intelocation;

class IpInfoDb implements Intelocationable {
 
    private $ip;
    private $ipInformation;
    
    private $intelocation;
    
    private $apiUrl;
    
    public function __construct($apiLogin, $ipAddress = "") {
        $this->ip = $_SERVER['REMOTE_ADDR'];
        
		if ( function_exists( 'apache_request_headers' ) ) {
			$headers = apache_request_headers();
		} else {
			$headers = $_SERVER;
		}

		if (array_key_exists( 'X-Forwarded-For', $headers ) 
            && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) 
        ) {
			$this->ip = $headers['X-Forwarded-For'];
		} elseif (array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) 
                  && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
		) {
			$this->ip = $headers['HTTP_X_FORWARDED_FOR'];
		} else {
			$this->ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
		}
        
        if (!empty($ipAddress)) {
            $this->ip = $ipAddress;
        }
        
        $this->intelocation = array();
        
        if (array_key_exists('key', $apiLogin)) {
            $this->apiUrl = 'http://api.ipinfodb.com/v3/ip-city/?key=' 
                . $apiLogin['key']
                . '&ip=' . $this->ip . '&format=json';
            $this->intelocation = json_decode(file_get_contents($this->apiUrl), true);
        }
    }
    
    public function getCity() {
        if (array_key_exists('cityName', $this->intelocation)) {
            return $this->intelocation['cityName'];
        }
        return '';
    }
    
    public function getState() {
        if (array_key_exists('regionName', $this->intelocation)) {
            return $this->intelocation['regionName'];   
        }
        return '';
    }
    
    public function getCountry() {
        if (array_key_exists('countryName', $this->intelocation)) {
            return $this->intelocation['countryName'];
        }
        return '';
    }
    
}

?>