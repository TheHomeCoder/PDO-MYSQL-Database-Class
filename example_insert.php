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

    <title>Database Class | Select Example</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Database Class | Insert Example</h1>
<p class="lead">
    Insert into the 'city' table a record with....
    <ul>
        <li>'Name' = 'TestName'</li>
        <li>'Population' = '9999'</li>
        <li>'District' = 'Disty'</li>
        <li>'CountryCode' = 'PSE'</li>
    </ul>
</p>

<?php 
/** Insert the record
 *
 *  Using the insert() function in the Database class ($db) pass in 
 *  the parameters and assign the resultant array to $query_insert
**/
$query_insert = $db->insert('city',
    array('FIELDS'=>array(
        'Name' => 'Test Name' ,
        'Population' => "9999", 
        'District' => "Disty", 
        'CountryCode' => 'PSE'
        )
    )
);

?>

<h4>Result</h4>

<?php
// Shown an example of using the count() function from './src/class/DB.php'
echo $query_insert->count() .' rows inserted<br>';

// Shown an example of using the insertId() function from './src/class/DB.php'
echo 'New record ID : ' . $db->insertId ();
?>

<h4>Entered Array</h4>

<!-- Echo out the $query_insert array to the screen using <pre> tags for formatting -->
<pre>
$query_insert = $db->insert(
    'city,
    array(
        'FIELDS'=>array(
            'Name' => 'TestName' ,
            'Population' => "9999", 
            'District' => "Disty", 
            'CountryCode' => 'PSE'
        )
    )
);
</pre>

<?php // Show the generated SQL and bindings. Function in /src/functions/php
showData ($query_insert); ?>

</body>
</html>

