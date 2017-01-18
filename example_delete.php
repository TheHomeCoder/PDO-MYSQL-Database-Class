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

    <title>Database Class | Delete Example</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Database Class | Delete Example</h1>
<p class="lead">
    Delete records in the 'city' table where
    <ul>
        <li>Name starts with 'a'</li>
        <li>Population is more than 1000</li>
    </ul>
</p>
<p class="text-danger">
    Note that as this performs a specified deleted query on the database, it will only show a number of deleted records the first time as there will be nothing to update. To run it again, either drop the database and re-populate it from world.sql or change the value of 'Name' in this file to something other than 'a' (do not forget to leave the % if you are still using a 'LIKE' parameter.
    
</p>

<?php 
/** Delete the records from the table
 *
 *  Using the delete() function in the Database class ($db) pass in 
 *  the parameters and assign the resultant array to $query_delete
**/
$query_delete = $db->delete(
    'city',
    array(
        'WHERE'=>array(
            'Name' => array("LIKE", '%a'), 
            'Population' => array(">", '1000'),
        )
    )
);

?>

<h4>Result</h4>

<?php
// Shown an example of using the count() function from './src/class/DB.php'
echo $query_delete->count() .' rows deleted<br>';

?>
<h4>Entered Array</h4>

<!-- Echo out the $query_delete array to the screen using <pre> tags for formatting -->
<pre>
$query_delete = $db->delete(
    'city',
    array(
        'WHERE'=>array(
            'Name' => array("LIKE", '%a'), 
            'Population' => array(">", '1000'),
        )
    )
);
</pre>

<?php // Show the generated SQL and bindings. Function in /src/functions/php
showData ($query_delete); ?>

</body>
</html>

