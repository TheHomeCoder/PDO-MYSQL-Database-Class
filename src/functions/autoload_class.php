<?php

/*------------------------------------------------------------------------------
** File:        /src/functions/autoload.php
** Description: Autoloads class files on request
** Author:      Steve Ball
** 
** @param   array   List of directories to look for class files
** 
** Uses the built in php spl_autoload_register 
** http://php.net/manual/en/function.spl-autoload-register.php
** 
** All class files and the class they contain must be the same.
** EG. The 'Config' class must be contained in Config.php
** 
** Passes in an array of folders to search for the required class and then includes it
** in the page using require_once. 
** If a class file with the same name is found in more than one directory, require_once
** ensures that only the first one will be used.
** 
** Example Use
** autoload_class (
**     $dirs = array(
**         './src/classes/', # Reads the src/classes directory in the root
**         '../classes/',    # Reads the classes directory one level up from the file
**     )
** );
** 
** NOTE : Need to find a better way of passing $dirs rather than setting it globally
** 
**------------------------------------------------------------------------------ */

function autoload_class ($dirs=array()) {

    // Call the built in php spl_autoload_register 
    spl_autoload_register(function($class_name) {

        // Call the passed-in array of directories as a $global value
        global $dirs;
        
        // Loop through the array of directories
        foreach( $dirs as $dir ) {

            // Check if the class file exists in the current directory
            if (file_exists($dir.$class_name.'.php')) {
                
                // If the file exists, call it using require_once
                require_once($dir.$class_name.'.php');
                return;
            }
        }
    });
}