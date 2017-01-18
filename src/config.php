<?php

/*------------------------------------------------------------------------------
** File:        ./src/config.php
** Description: Global Configuration file for the site 
** Author:      Steve Ball
**------------------------------------------------------------------------------ */

/*
* Start the Php session
*
* As this file is the start of all front end pages, start the session here at the start
*
*/
session_start();

/*
* Set Default Timezone
*
* Allows you to override the time set on the server.
* Used if your site is hosted in a different timezone.
*
* @param string     A PHP supported Timezone as defined at http://php.net/manual/en/timezones.php
*
*/
date_default_timezone_set('Europe/London'); 


/*
* Define Paths
*
* Set Variables for common paths.
*
* These are only used for files where /src/classes/Config.php is not accessible,
* otherwise a $GLOBALS['config'] variable can be used
*
* @param string     Defined variable name
* @param string     Specified path
*
*/
define('CSS', dirname(__FILE__).'/css/');
define('JS', dirname(__FILE__).'/js/');


/*
* $GLOBALS[] 
*
* Sets Global Variables that can be used anywhwere.
*
* $GLOBALS['config'] is stored within this file.
* All other $GLOBALS[] are stored within seperate files within
* ./src/config_files, grouped by type
*
* Use
* echo Config::get('site_name', 'site_settings')
* would return the value of $GLOBALS['site_settings']['site_name']
*
* echo Config::get('settings/owner', 'site_settings')
* would return the value of $GLOBALS['site_settings']['settings']['owner']
*
* NOTE : The second parameter is optional. 
* If it is omitted then $GLOBALS['config'] will be assumed so 
* echo Config::get('css') will return the value of $GLOBALS['config']['css'] 
*/

// ==================================================================
//  Set values to $GLOBALS['config']

$GLOBALS['config'] = array(
  'css_path' => './css/',
  'js_path' => './js/',
  'site_name' => 'Database Class',
  'settings' => array (
        'owner' => 'Steve Ball',
        'company' => 'Gitprojects',
    )
);


// ==================================================================
//  Include all of the other $GLOBALS[] files from /src/config_files/

foreach (glob('./src/config_files/*.php') as $filename) {
   require_once ($filename);
}

/*
* Include global functions
*
*/
require_once './src/functions/global.php';


/*
* autoload_class ()
*
* Called from ./src/functions/global.php > ./src/functions/autoload_class.php
^ Automatically loads classes from the specified folders when called
*
* @param array     Paths to all folders containing class files
*
*/
autoload_class (
    $dirs = array(
        './src/classes/',  
    )
);