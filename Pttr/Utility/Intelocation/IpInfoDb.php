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
            
            $curl = curl_init();
            curl_setopt ($curl, CURLOPT_URL, $this->apiUrl);
            curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, 30);
            $contents = curl_exec($curl);
            curl_close($curl);
            $output = json_decode($contents, true);
            
            if (is_array($output)) {
                $this->intelocation = $output;
            }
        }
    }
    
    public function getCity() {
        if (array_key_exists('cityName', $this->intelocation)) {
            return $this->intelocation['cityName'];
        }
        return '';
    }
    
    public function getStateAbbreviation($stateLongName) {
        if ($stateLongName == "-" || empty($stateLongName)) {
            return "";   
        }
        $states = array(
            'Alabama'=>'AL',
            'Alaska'=>'AK',
            'Arizona'=>'AZ',
            'Arkansas'=>'AR',
            'California'=>'CA',
            'Colorado'=>'CO',
            'Connecticut'=>'CT',
            'Delaware'=>'DE',
            'Florida'=>'FL',
            'Georgia'=>'GA',
            'Hawaii'=>'HI',
            'Idaho'=>'ID',
            'Illinois'=>'IL',
            'Indiana'=>'IN',
            'Iowa'=>'IA',
            'Kansas'=>'KS',
            'Kentucky'=>'KY',
            'Louisiana'=>'LA',
            'Maine'=>'ME',
            'Maryland'=>'MD',
            'Massachusetts'=>'MA',
            'Michigan'=>'MI',
            'Minnesota'=>'MN',
            'Mississippi'=>'MS',
            'Missouri'=>'MO',
            'Montana'=>'MT',
            'Nebraska'=>'NE',
            'Nevada'=>'NV',
            'New Hampshire'=>'NH',
            'New Jersey'=>'NJ',
            'New Mexico'=>'NM',
            'New York'=>'NY',
            'North Carolina'=>'NC',
            'North Dakota'=>'ND',
            'Ohio'=>'OH',
            'Oklahoma'=>'OK',
            'Oregon'=>'OR',
            'Pennsylvania'=>'PA',
            'Rhode Island'=>'RI',
            'South Carolina'=>'SC',
            'South Dakota'=>'SD',
            'Tennessee'=>'TN',
            'Texas'=>'TX',
            'Utah'=>'UT',
            'Vermont'=>'VT',
            'Virginia'=>'VA',
            'Washington'=>'WA',
            'West Virginia'=>'WV',
            'Wisconsin'=>'WI',
            'Wyoming'=>'WY'
        );
        return $states[$stateLongName];
    }
    
    public function getState() {
        if (array_key_exists('regionName', $this->intelocation)) {
            return $this->getStateAbbreviation($this->intelocation['regionName']);   
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