<?php
/*------------------------------------------------------------------------------
** File:        /src/classes/Config.php
** Description: Reads the values set in $GLOBALS[] 
** Author:      Steve Ball
** 
** @param   array   The $GLOBALS[] value to retrieve     
** @param   array   The $GLOBALS[] type                 Optional 
** 
** Examples of use in './src/config.php'
**------------------------------------------------------------------------------ */

// Include the './src/config.php' file to access the $GLOBALS[]
require_once './src/config.php';

class Config {

    // Create a public function called 'get' to access this globally
    public static function get($path, $type=null) {

        // The class requires a path so only proceed if it exists
        if($path) {

            // Set $config to the $GLOBALS[] type. If null, default to $GLOBALS['config']
            $config = ($type) ? $GLOBALS[$type] : $GLOBALS['config'];

            // Explode the path get all elements
            $path = explode('/', $path);

            // Loop through each part of the path
            foreach($path as $bit) {

                // Check if the current path exists in the $GLOBALS[] type
                if(isset($config[$bit])) {

                    // If the path does exist, append the path to the $GLOBALS[] type
                    $config = $config[$bit];

                }
            }

            return $config;
        }

        return false;
    }
}