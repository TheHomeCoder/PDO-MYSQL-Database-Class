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

    <title>Database Class | Update Example</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Database Class | Update Example</h1>
<p class="lead">
    Update records in the 'city' table with....
    <ul>
        <li>'Name' = 'Tiny'</li>
    </ul>
    where
    <ul>
        <li>Population is less than 1000</li>
    </ul>
</p>
<p class="text-danger">
    Note that as this performs a specified update query on the database, it will only show a number of updated records the first time as there will be nothing to update. To run it again, either drop the database and re-populate it from world.sql or change the value of 'Name' in this file to something other than 'Tiny'.
    
</p>

<?php 
/** Update the records
 *
 *  Using the update() function in the Database class ($db) pass in 
 *  the parameters and assign the resultant array to $query_update
**/
$query_update = $db->update(
    'city',
    array(
        'FIELDS'=>array(
            'Name' => 'Tiny' ,
            ),
        'WHERE'=>array(
            'Population' => array("<", '1000'),
        )
    )
);

?>

<h4>Result</h4>

<?php
// Shown an example of using the count() function from './src/class/DB.php'
echo $query_update->count() .' rows updated<br>';

?>

<h4>Entered Array</h4>

<!-- Echo out the $query_update array to the screen using <pre> tags for formatting -->
<pre>
$query_update = $db->update(
    'city',
    array(
        'FIELDS'=>array(
            'Name' => 'Tiny' ,
            ),
        'WHERE'=>array(
            'Population' => array("<", '1000'),
        )
    )
);
</pre>

<?php // Show the generated SQL and bindings. Function in /src/functions/php
showData ($query_update); ?>

</body>
</html>

