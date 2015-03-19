# Pttr Backend API Layer

Pttr's Backend API Layer service for internal and open-source use. The Backend API Layer tries to unite all the current legacy API systems
as well as general data of various shelters and stray animals under one systematic and unified JSON API layer. 

This backend API layer:

* Powers Pttr's web and mobile apps to aggregate stray animals as well as animal shelters that are currently no on the system
* Allows other developers to create custom apps by hooking up to this JSON API layer without any restrictions

## URL endpoints and parameters

Currently, there are only GET routes. Information that needs to be edited will only be editable by certain personnel (eg: only shelters can manage their own animals, only users can edit their own accounts). There might be some expansion for this in the future but for now, all editable data is housed in Pttr's Firebase.

* `/animals`
  * Returns all the animals in the nearby vicinity by default using geolocation in the server side (through the IP address)
  * Optional Parameters
    * city
    * zipcode
    * limit (Default: 50)
    * page
  * Returns
    * Array of animals
    
* `/animal/<animalId>`
  * Required Parameters
    * `animalId`
  * Returns
    * A specific animal
    
* `/shelters/`
  * Optional Parameters
    * city
    * zipcode
    * limit (Default: 50)
    * page
  * Returns
    * Array of shelters
    
* `/shelter/<shelterId>`
  * Required Parameters
    * `shelterId`
  * Returns
    * A specific shelter

For more information, make sure to check out the Wiki!

## Technologies used
* PHP (>= 5.4)
  * Composer for autoloading classes and dependency management
  * badphp/dispatch for routing URL endpoints
  * rmccue/requests for handling CURL requests to other legacy API systems