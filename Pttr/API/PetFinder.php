<?php

namespace Pttr\API;

class PetFinder implements APIable {
    
    private $url = "http://api.petfinder.com/";
    
    private $apiKey;
    private $apiSecret;
    
    private $requestUrl;
    
    public function __construct($login) {
        $this->apiKey = $login['key'];
        $this->apiSecret = $login['secret'];
    }
    
    public function getShelters() {
        $this->requestUrl = $this->url . "shelter.find";
        $response = $this->fireRequest();
        $shelters = array();
        if (array_key_exists('petfinder', $response)
            && array_key_exists('shelters', $response['petfinder']) 
            && array_key_exists('shelter', $response['petfinder']['shelters'])
        ) {
            foreach ($response['petfinder']['shelters']['shelter'] 
                     as $shelterObject) {
                $shelters[] =  array(
                    'name' => array_key_exists('$t', $shelterObject['name'])
                        ? $shelterObject['name']['$t'] : '',
                    'address' => array_key_exists('$t', $shelterObject['address1'])
                        ? $shelterObject['address1']['$t'] : '',
                    'city' => array_key_exists('$t', $shelterObject['city'])
                        ? $shelterObject['city']['$t'] : '',
                    'state' => array_key_exists('$t', $shelterObject['state'])
                        ? $shelterObject['state']['$t'] : '',
                    'zipcode' => array_key_exists('$t', $shelterObject['zip'])
                        ? $shelterObject['zip']['$t'] : '',
                    'country' => array_key_exists('$t', $shelterObject['country'])
                        ? $shelterObject['country']['$t'] : '',
                    'id' => array_key_exists('$t', $shelterObject['id'])
                        ? 'PetFinder-' . $shelterObject['id']['$t'] : '',
                    'email' => array_key_exists('$t', $shelterObject['email'])
                        ? $shelterObject['email']['$t'] : '',
                    'orgUrl' => ''
                );
            }
        }
        return $shelters;
    }
    
    public function getShelter($identifier) {
        $this->requestUrl = $this->url . "shelter.get";
    }
    
    public function getAnimals() {
        $this->requestUrl = $this->url . "pet.find";
    }
    
    public function getAnimal($identifier) {
        $this->requestUrl = $this->url . "pet.get";
    }
    
    /**
        Assembles the GET parameters provided by the user of our API
        Internal helper helper function
        @param array $additionalParams
        @return array
    */
    private function constructParameters(array $additionalParams = array()) {
        $parameters = array();
        if (isset($this->apiKey)) {
            $parameters['key'] = (string) $this->apiKey;   
        }
        if (isset($_GET['city'])) {
            $parameters['location'] = (string) $_GET['city'];
        }
        if (isset($_GET['zipcode']) && $_GET['zipcode'] > 0) {
            $parameters['location'] = (int) $_GET['zipcode'];
        }
        if (isset($_GET['limit']) && $_GET['limit'] > 0) {
            $parameters['count'] = (int) $_GET['limit'];
        }
        if (isset($_GET['page']) && $_GET['page'] > 0) {
            $parameters['offset'] = (int) ($_GET['page'] - 1) * 25;   
        }
        $parameters['format'] = "json";
        
        if (count($additionalParams) > 0) {
            $parameters = $parameters + $additionalParams;
        }
        
        return $parameters;
    }
    
    /**
        Fires the API request to the Petfinder service and gets the response.
        Internal class helper function
        @param array $additionalParams Additional GET parameters to give to the API call we are making
        @return array
    */
    private function fireRequest(array $additionalParams = array()) {
        if (isset($this->requestUrl)) {
            $this->requestUrl .= "?" . http_build_query($this->constructParameters($additionalParams));
            $response = json_decode(file_get_contents($this->requestUrl), true);
            unset($this->requestUrl);

            return $response;
        }
    }
    
}

?>