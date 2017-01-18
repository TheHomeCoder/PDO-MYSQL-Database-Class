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

    <title>Database Class | Select Single Row Example</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Database Class | Select Single Row Example</h1>
<p class="lead">
    Show the city with an id of 1074. 
</p>
<p>
    As this returns a single rows, it uses the 'one' parameter in the getRows() function. See an example of a <a href="example_select_multiple.php">Multiple Returned Rows</a>.  
</p>

<?php
/** Get the records from the table
 *
 *  Using the select() function in the Database class ($db) pass in 
 *  the parameters and assign the resultant array to $query_select
**/
$query_select = $db->select(
    'city',
    array(
        'WHERE'=>array(
            'ID' => array("=", 1074)
        ),

    )
);
?>


<h4>Result</h4>

<!-- 
    Start the table to hold the returned records.
    We use the Bootstrap table class (http://getbootstrap.com/css/#tables) purely for aesthetics.
-->
<table class="table">

    <thead>
        <th>ID</th>
        <th>Name</th>
        <th>Country Code</th>
        <th>District</th>
        <th>Population</th>
    </thead>

    <tbody>

        <?php 
        /**
         *  Return the record
         *
         *  Unlike returning multiple rows, when we return one we only get one record
         *  so only need one loop instead of two.
         *
         *  We start off by opening a new table row and then we take 
         *  $query_select that was created above from the query and assign 
         *  it to $city using the getRows() function held in './src/class/DB.php',
         *  using a parameter of 'one' to show that we want only a single returned 
         *  row and not multiple ones.
         *
         *  We then loop through the columns contained in $city 
         *  (as explained in 'exmple_select_multiple.php') before closing the row.
        **/

        echo "<tr>";

        $city = $query_select->getRows('one');
   
        foreach ($city as $val) {
            echo "<td>".escape($val)."</td>";
        }

        echo "</tr>";



        
        ?>

    </tbody>
</table>

<?php
// Shown an example of using the count() function from './src/class/DB.php'
echo '<b>'.$query_select->count () . ' records returned</b>';

?>

<h4>Entered Array</h4>

<!-- Echo out the $query_select array to the screen using <pre> tags for formatting -->
<pre>
$query_select = $db->select(
    'city',
    array(
        'WHERE'=>array(
            'ID' => array("=", 1074)
        ),

    )
);
</pre>

<?php // Show the generated SQL and bindings. Function in /src/functions/php
showData ($query_select); ?>


</body>
</html>

