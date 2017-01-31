<?php

/**
* Include the config file
**/
require_once 'src/config.php';

/**
* Display php errors. Uncomment to use
**/
// ini_set('display_errors', 'ON');

/**
* Connect to the database and assign it to a variable called $db
* When communicating with the database, we can now use $db->
**/
$db = DB::dbConnect();


?>


<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Database Class</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">


</head>

<body>

<h1>PDO Database CRUD Class</h1>
<p class="lead">An Object Orientated database class for connection, read, write, create and delete</p>

<h2 id="examples">Examples</h2>
<a href="example_select_multiple.php">Select Multiple</a> | 
<a href="example_select_single.php">Select Single</a> | 
<a href="example_insert.php">Insert</a> | 
<a href="example_update.php">Update</a> | 
<a href="example_delete.php">Delete</a> | 
<a href="example_full.php">Full Query</a> <br>
<small>To use the examples you need to have a database containing the sample database. Please follow the first three steps in 'Set-up' below to create this and connect to it.</small>

<h2 id="usage">Usage</h2>

<h4>Requirements</h4>

<ul>
    <li>
        <p>Server with PHP 5+ (Web host or locally with <a href="http://www.wampserver.com/" target="_blank">WAMP</a> / <a href="https://www.apachefriends.org/" target="_blank">XAMPP</a></p>
    </li>
    <li>
        <p>PDO Extension</p>
    </li>
    <li>
        <p>PDO_MYSQL Driver</p>
    </li>
    <li>
        <p>MySQL database</p>
    </li>
</ul>

<h4>Set-up</h4>
    <p>The easiest way to test the functionality is to use the example database stored in world.sql (from the MYSQL <a href="https://dev.mysql.com/doc/index-other.html" target="_blank"> Official Site</a>). The instrucions here assume you have done that. If you are using your own database tables, change any details accordingly.</p>

    <p>Create a new database and import world.sql into it.</p>

    <p>Open /src/config_files/db.php and change the settings to match those of your database.</p>

    <p>Create a new file and include /src/config.php at the top.</p>

    <p>Open up a connection the the Database class and assign it to a variable<br>$db = DB::dbConnect();<br>NOTE: You do not need to include the database class file (/src/classes/DB.php) as this is controled by /src/functions/autoload_class.php</p>

    <p>You can now use $db to access all of the functions within the class.</p>

<h4>Available public functions</h4>  

<ul>
    <li>dbConnect() - Opens up a connection to the database</li>
    <li>select() - runs a SELECT query</li>
    <li>delete() - runs a DELETE query</li>
    <li>insert() - runs a INSERT query</li>
    <li>update() - runs a UPDATE query</li>
    <li>fullQuery() - runs general query via a full sql statement</li>
    <li>insertId() - returns the id of the last row inserted into the database by the last query</li>
    <li>count() - returns the number of rows affected by the last query</li>
</ul>

<p>Each of the functions that run a query on the database (except for fullQuery()) take two parameters
<ul>
    <li>Name of the database table</li>
    <li>An array of conditions</li>
</ul>

The available conditions are

    <ul>
        <li>
            'FIELDS' - array - Only used in insert() and update()<br>
            A pairing of a column name and the value to insert/update<br>
            EG. 
            <pre>
            'FIELDS'=>array(
                'Name' => 'TestName' ,
                'Population' => "9999", 
                'District' => "Disty", 
                'CountryCode' => 'CTY'
            )

            Equivalent to a MySQL query of

            `Name` = 'TestName' ,
            `Population` = "9999", 
            `District` = "Disty", 
            `CountryCode` = 'CTY'
            </pre>
        </li>
        <li>
            'WHERE' - array - Used in all query functions. Can take an unlimited number of parameters.
            <ul>
                <li>'>', '<', '=', 'LIKE' take a key/array pairing. The key is the column name to search on and the array takes two parameters - operator and search value.<br>
                <pre>
                'WHERE'=>array(
                   
                    'Population' => array(">", '10000'),
                    'ID' => array("<", '100'),
                    'CountryCode' => array("=", 'GBR'), 
                    'Name' => array("LIKE", '%a'), 
                    

                )

                Equivalent to a MySQL query of

                WHERE `Population` >  10000 
                AND `ID` < 100 
                AND `CountryCode` = 'GBR'
                AND `Name` LIKE '%a'
                </pre>
                </li>
                <li>IN takes an array of values.The key is the column name to search on and the array takes two parameters - operator and an array of search values.<br>
                <pre>
                'WHERE'=>array(
                    'CountryCode' => array("IN", array('IND','TUR')
                )

                Equivalent to a MySQL query of

                WHERE `CountryCode` IN ('IND','TUR') 
                </pre></li>
                <li>BETWEEN takes an array of two values -  The key is the column name to search on and the array takes two parameters - operator and an array of the lower and upper limit.
                <pre>
                'WHERE'=>array(
                    'Population' => array("BETWEEN", array('100000','500000')),
                )  
                
                Equivalent to a MySQL query of

                WHERE `Population` BETWEEN  100000 AND 500000
                </pre>
                 </li>
            </ul>
        </li>
        <li>
            'ORDER' - array - Only used in select()<br>
            The order to sort the results on <br>
            EG. 
            <pre>
            'ORDER'=>array(
                'CountryCode ASC', 
                'Population DESC'
            ),

            Equivalent to a MySQL query of

            ORDER BY CountryCode ASC, Population DESC
            </pre>
        </li>
       
        <li>
            'LIMIT' - key/value pairing - The maximum number of records to return<br>
            EG. 
            <pre>
            'LIMIT'=> 10

            Equivalent to a MySQL query of

            LIMIT 10
            </pre>
        </li>

         <li>
            'START' - key/value pairing - Sets an offset for the starting record, used in conjunction with LIMIT<br>

            EG. 
            <pre>
            'START'=> 2
            'LIMIT'=> 10

            Equivalent to a MySQL query of

            LIMIT 2,10
            </pre>
        </li>
    </ul>
</p>

</body>
</html>

