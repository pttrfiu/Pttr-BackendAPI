<?php

/**
    APIable Interface
    A contract structure that all API/* classes must abide by
*/

namespace Pttr\API;

interface APIable {
    
    public function getAnimal($identifier);
    
    public function getShelter($identifier);
    
    public function getAnimals();
    
    public function getShelters();
    
}

?>