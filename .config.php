<?php

/**

Sample .config.php structure:

    return array(
        'dataApis' => array(
            'PetFinder' => array(
                'key' => 'yourOwnKey',
                'secret' => 'yourOwnSecret'
            ),
            'RescueGroups' => array(
                'key' => 'yourOwnKey'
            ),
            'YourOwnClassUnderPttrAPINamespace' => array(
                'property' => 'someValue'
            )
        ),
        'geoApis' => array(
            'IpInfoDb' => array(
                'key' => 'yourOwnkey'  
            )
        ),
        'baseUrl' => "someBaseUrl"
    );

If you plan to add a new stray animal/animal shelter API to the 
Pttr API layer, create a new class representing that remote API
under the \Pttr\API namespace.

For example, `YourOwnClassUnderPttrAPINamespace` would have the
corresponding class - \Pttr\API\YourOwnClassUnderPttrAPINamespace.

This class will be automatically injected with the login and other
details you specified as the class' value in this file.

For example: the array('property' => 'someValue') will be injected
as the first parameter in the __construct() method of the
\Pttr\API\YourOwnClassUnderPttrAPINamespace class.

*/

?>