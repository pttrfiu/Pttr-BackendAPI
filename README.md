# Pttr Backend API Layer

Pttr's Backend API Layer service for internal and open-source use. The Backend API Layer tries to unite all the current legacy API systems
as well as general data of various shelters and stray animals under one systematic and unified JSON API layer. 

This backend API layer:

* Powers Pttr's web and mobile apps to aggregate stray animals as well as animal shelters that are NOT currently on the system.
* Allows other developers to create custom apps by hooking up to this JSON API layer without any restrictions.

## Developer Documentation

Currently, there are only GET routes. Information that needs to be edited will only be editable by certain personnel (eg: only shelters can manage their own animals, only users can edit their own accounts). There might be some expansion for this in the future but for now, all editable data is housed in Pttr's Firebase.

For more information, make sure to check out our generously documented [developer docs](https://github.com/pttrfiu/Pttr-BackendAPI/wiki)!

## Technologies used
* PHP (>= 5.4)
  * Composer for autoloading classes and dependency management
  * badphp/dispatch for routing URL endpoints