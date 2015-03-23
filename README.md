# Pttr Backend API Layer

Pttr's Backend API Layer service for internal and open-source use. The Backend API Layer tries to unite all the current legacy API systems
as well as general data of various shelters and stray animals under one systematic and unified JSON API layer. 

This backend API layer:

* Powers Pttr's web and mobile apps to aggregate stray animals as well as animal shelters that are NOT currently on the system.
* Allows other developers to create custom apps by hooking up to this JSON API layer without any restrictions.

## Developer Documentation

Currently, there are only GET routes. Information that needs to be edited will only be editable by certain personnel (eg: only shelters can manage their own animals, only users can edit their own accounts). There might be some expansion for this in the future but for now, all editable data is housed in Pttr's Firebase.

For more information, make sure to check out our generously documented [developer docs](https://github.com/pttrfiu/Pttr-BackendAPI/wiki)!

## Spinning up your own service

If you want to implement Pttr's Backend API layer in your own server, make sure to have a `.config.php` file in the root of your project that returns an array. This array should have at minimum, a `baseUrl` index specifying the base url where you plan to run the API layer. 

It should also have a `dataApis` and `geoApis` property with corresponding login information. A sample `.config.php` file has been included to show a sample of the structure.

## Extending the API

Pttr's API layer is highly advanced. It can accept multiple animal shelter and stray animal APIs and normalize them into one unified JSON API, primarily by using PHP's Reflection APIs. We currently support PetFinder and RescueGroup's APIs, but you can easily add and extend another API service simply by:

* Adding your API's class name under the `dataApis` property of the array returned by `.config.php`.
* Specifying the API login details as an array that is the value of the API class name under the `dataAPIs` property. These login details are auto-injected as the first parameter in the constructor function of your custom API class, whenever an instance of that class is called, allowing you to have access to your stored login credentials

## Technologies used
* PHP (>= 5.4)
  * Composer for autoloading classes and dependency management
  * badphp/dispatch for routing URL endpoints