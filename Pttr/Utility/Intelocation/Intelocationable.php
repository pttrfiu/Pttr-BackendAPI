<?php

/**
    Intelocationable Interface
    A contract structure that all Intelocation/* classes must abide by
*/

namespace Pttr\Utility\Intelocation;

interface Intelocationable {
    
    public function getCity();
    
    public function getState();
    
    public function getCountry();
    
}

?>