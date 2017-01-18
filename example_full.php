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

    <title>Database Class | Full Query Example</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Database Class | Full Query Example</h1>
<p class="lead">
    If the query is complex (ie. requiring table joins or 'OR') you can pass it in in it's entirety, with no bindings.
</p>


<?php 
/** Get the records from the table
 *
 *  Using the fullQuery() function in the Database class ($db) pass in 
 *  the the full sql statement and assign the resultant array to $query_full
**/
$query_full = $db->fullQuery("
    SELECT `city`.`Name`, `city`.`District`, `city`.`Population`, `city`.`CountryCode` 
    FROM `city` 
    LEFT JOIN `country` ON `country`.`Code` = `city`.`CountryCode` 
    WHERE `country`.`Region` = 'Western Europe' 
    ORDER BY `city`.`Population` DESC 
    LIMIT  1,5
");

?>

<h4>Result</h4>

<!-- 
    Start the table to hold the returned records.
    We use the Bootstrap table class (http://getbootstrap.com/css/#tables) purely for aesthetics.
-->
<table class="table">

    <thead>
        <th>Name</th>
        <th>District</th>
        <th>Population</th>
        <th>Country Code</th>
    </thead>

    <tbody>

        <?php 
        /**
         *  Start returning the records
         *
         *  A full explanation of how this part works can be found in
         *  example_select_multiple.php
        **/

        foreach ($query_full->getRows('all') as $city) {
            echo "<tr>";

            foreach ($city as $val) {
                echo "<td>".escape($val)."</td>";
            }

            echo "</tr>";
        }

        
        ?>

    </tbody>
</table>


<?php
// Shown an example of using the count() function from './src/class/DB.php'
echo '<b>There are ' . $query_full->count () . ' cities the full_query() query</b>';

?>

<h4>Entered Array</h4>

<!-- Echo out the $query_full array to the screen using <pre> tags for formatting -->
<pre>
$query_full = $db->fullQuery("
    SELECT `city`.`Name`, `city`.`District`, `city`.`Population`, `city`.`CountryCode` 
    FROM `city` 
    LEFT JOIN `country` ON `country`.`Code` = `city`.`CountryCode` 
    WHERE `country`.`Region` = 'Western Europe' 
    ORDER BY `city`.`Population` DESC 
    LIMIT  1,5
");
</pre>

<?php // Show the generated SQL and bindings. Function in /src/functions/php
showData ($query_full); ?>

</body>
</html>

