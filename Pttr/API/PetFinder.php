<?php

/**
    PetFinder Class
    Petfinder is a concrete implementation of the APIable interface,
    using the Petfinder service
*/

namespace Pttr\API;

use ReflectionClass;

class PetFinder implements APIable {
    
    private $url;
    
    private $apiKey;
    private $apiSecret;
    
    // Finalized Request endpoint for the PetFinder service
    private $requestUrl;
    
    private $className;
    
    public function __construct($login) {
        $this->url = "http://api.petfinder.com/";
        $this->apiKey = $login['key'];
        $this->apiSecret = $login['secret'];
        
        $this->className = (new ReflectionClass($this))->getShortName();
    }
    
    public function getShelters() {
        $this->requestUrl = $this->url . "shelter.find";
        
        $additionalParameters = array();
        if (isset($_GET['name'])) {
            $additionalParameters['name'] = $_GET['name'];
        }
        $response = $this->fireRequest($additionalParameters);
        $output = array();
        
        if (array_key_exists('petfinder', $response)
            && array_key_exists('shelters', $response['petfinder']) 
            && array_key_exists('shelter', $response['petfinder']['shelters'])
        ) {
            $shelterObjects = $response['petfinder']['shelters']['shelter'];
            if (isset($_GET['limit']) && $_GET['limit'] == 1) {
                $output[] =  $this->constructShelterObject($shelterObjects);
            } else {
                foreach ($shelterObjects as $shelterObject) {
                    $output[] =  $this->constructShelterObject($shelterObject);
                }       
            } 
        }
        
        return $output;
    }
    
    public function getShelter($identifier) {
        $this->requestUrl = $this->url . "shelter.get";
        
        $response = $this->fireRequest(array('id' => $identifier));
        
        if (array_key_exists('petfinder', $response)
           && array_key_exists('shelter', $response['petfinder'])
        ) {
            $shelterObject = $response['petfinder']['shelter'];
            return $this->constructShelterObject($shelterObject);
        }
    }
    
    public function getAnimals() {
        $this->requestUrl = $this->url . "pet.find";
        
        $additionalParameters = array();
        if (isset($_GET['sex'])) {
            $additionalParameters['sex'] = $_GET['sex'];   
        }
        if (isset($_GET['species'])) {
            $additionalParameters['animal'] = $_GET['species'];
        }
        $response = $this->fireRequest($additionalParameters);
        $output = array();
        
        if (array_key_exists('petfinder', $response)
            && array_key_exists('pets', $response['petfinder'])
            && array_key_exists('pet', $response['petfinder']['pets'])
        ) {
            $animalObjects = $response['petfinder']['pets']['pet'];
            if (isset($_GET['limit']) && $_GET['limit'] == 1) {
                $output[] = $this->constructAnimalObject($animalObjects);
            } else {
                foreach ($animalObjects as $animalObject) {
                    $output[] = $this->constructAnimalObject($animalObject);
                }
            }
        }
        
        return $output;
    }
    
    public function getAnimal($identifier) {
        $this->requestUrl = $this->url . "pet.get";
        
        $response = $this->fireRequest(array('id' => $identifier));
        
        if (array_key_exists('petfinder', $response)
            && array_key_exists('pet', $response['petfinder'])
           )
        {
            $animalObject = $response['petfinder']['pet'];
            return $this->constructAnimalObject($animalObject);
        }
    }
    
    /**
        Given an atomic Shelter object from PetFinder remote API, construct a normalized version
        as we have documented in our developer docs
    */
    private function constructShelterObject($shelterObject) {
        return array(
            'name' => $this->getPropertyValue($shelterObject['name']),
            'address' => $this->getPropertyValue($shelterObject['address1']),
            'city' => $this->getPropertyValue($shelterObject['city']),
            'state' => $this->getPropertyValue($shelterObject['state']),
            'zipcode' => $this->getPropertyValue($shelterObject['zip']),
            'country' => $this->getPropertyValue($shelterObject['country']),
            'id' => $this->getPropertyValue($shelterObject['id'], $this->className),
            'email' => $this->getPropertyValue($shelterObject['email']),
            'orgUrl' => ''
        );
    }
    
    /**
        Given an atomic Animal object from Petfinder remote API, construct a normalized version
        as we have documented in our developer docs
    */
    private function constructAnimalObject($animalObject) {
        $thumbnail = '';
        $largePicture = '';
        $animalSex = '';
        $animalStatus = '';
        if (array_key_exists('sex', $animalObject)) {
            $animalSex = $this->getPropertyValue($animalObject['sex']);
        }
        if (array_key_exists('status', $animalObject)) {
            $animalStatus = $this->getPropertyValue($animalObject['status']);   
        }
        
        if ($animalSex == "M") {
            $animalSex = "male";
        } else if ($animalSex == "F") {
            $animalSex = "female";
        } else {
            $animalSex = "";
        }
        
        if ($animalStatus == "A") {
            $animalStatus = "alive";   
        } else {
            $animalStatus = "dead";
        }
        
        if (array_key_exists('media', $animalObject) 
            && array_key_exists('photos', $animalObject['media'])
            && array_key_exists('photo', $animalObject['media']['photos'])
        ) {
            foreach ($animalObject['media']['photos']['photo'] 
                     as $pictureObject) {
                if ($pictureObject['@size'] == "x") {
                    $largePicture = $pictureObject['$t'];
                } else if ($pictureObject['@size'] == "fpm") {
                    $thumbnail = $pictureObject['$t'];
                }
                if (!empty($largePicture) && !empty($thumbnail)) {
                    break;
                }
            }
        }
        
        return array(
            'name' => $this->getPropertyValue($animalObject['name']),
            'id' => $this->getPropertyValue($animalObject['id'], $this->className),
            'belongsToShelter' => $this->getPropertyValue($animalObject['shelterId']),
            'description' => $this->getPropertyValue($animalObject['description']),
            'species' => strtolower($this->getPropertyValue($animalObject['animal'])),
            'breed' => $this->getPropertyValue($animalObject['breeds']['breed']),
            'sex' => $animalSex,
            'status' => $animalStatus,
            'age' => strtolower($this->getPropertyValue($animalObject['age'])),
            'pictures' => array(
                'thumbnail' => $thumbnail,
                'largePicture' => $largePicture
            )
        );
    }
    
    /**
        PetFinder's API is ugly. Each property of an Animal or Shelter Object
        they return, has a value that can only be accessed by accessing the
        $t property of that property e.g. accessing a name of an Animal Object
        has to be $animalObject['name']['$t'] instead of just
        $animalObject['name'].
    */
    private function getPropertyValue($property, $append = NULL) {
        $cleanedProperty = '';
        if (array_key_exists('$t', $property)) {
            if (isset($append)) {
                $cleanedProperty .= $append . "-";   
            }
            $cleanedProperty .= $property['$t'];
        }
        return $cleanedProperty;
    }
    
    /**
        Assembles the API-wide GET parameters (regardless of what function) provided by the user of our API
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