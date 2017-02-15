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
    <?php include 'src/template/head.php'; ?>
    <title>Database Class | Select Multiple Tables Example</title>
</head>

<body>

<div id="jumbo" class="jumbotron">
    <div class="container">
        <img src="homecoderstrip200.png">
        <h1>
          PDO Database Class
        </h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="container">
                <?php include 'src/template/nav.php'; ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    
    <div class="row">
        <div class="col-md-2">
            <?php include 'src/template/left.php'; ?>
        </div>
        <div class="col-md1 col-md-10"">
            <div class="page-header">
                <h1>Database Class | Select Multiple Tables Example</h1>
            </div>



<p class="lead">
    xx
</p>
<p>
   xx
</p>

<?php
/** Get the records from the table
 *
 *  Using the select() function in the Database class ($db) pass in 
 *  the parameters and assign the resultant array to $query_select
**/
$query_select = $db->select(
    $tables = array(
        'city'=> array(
            'alias' => 'CIT',
            'fields' => array (
                'Name' => array (
                    'alias' => 'city_name',
                ),
                'Population' => array (
                    'alias' => 'pop',
                ),
                'District' => array (

                ),
            ),
            'where' => array (
                //'District' => array("LIKE", 'A%'),
                //'Population' => array("BETWEEN", array('100000','500000'))
                
            )
        ), 
        'country'=> array(
            'alias' => 'COUN',
            'fields' => array (
                'Code' => array (

                ),
                'Region' => array (

                ),
                'Name' => array (
                    'alias' => 'country_name',
                ),
                'IndepYear' => array (
                    'alias' => 'year_of_ind',
                ),
            ),
            'join' => array (
                'join_type' => 'LEFT',
                'foreign_table' => 'CIT', // Mention alias or table
                'foreign_key' => 'CountryCode',
                'local_key' => 'Code',
            ),
            'where' => array (
                //'Code' => array("IN", array('USA','ARG','IND','JPN')),
                'IndepYear' => array("NULL")
                
            )
        ), 

    ),
    $conditions = array(
        'ORDER'=>array(
            array('CIT','District','ASC'),
            array('CIT','Population','DESC')
        ),
        'LIMIT'=>array(10, 2)
    )
);

showData ($query_select);
?>
<h4>Result</h4>

<!-- 
    Start the table to hold the returned records.
    We use the Bootstrap table class (http://getbootstrap.com/css/#tables) purely for aesthetics.
-->
<table class="table">

    <thead>
    <?php
     
foreach ($query_select->getRows('one') as $key=> $value) {
   
            echo "<th>".escape($key)."</th>";
         

      
        }

    ?>

    </thead>

    <tbody>

        <?php 
        /**
         *  Start returning the records
         *
         *  Using a standard foreach loop (http://php.net/manual/en/control-structures.foreach.php)
         *  we iterate through the records returned.
         * 
         *  We take $query_select that was created above from the query and pass it into
         *  the getRows() function held in './src/class/DB.php', using a parameter of 'all'
         *  to show that we want every returned row and not just one.
         *
         *  Each time we loop through a row, we assign it to a new variable called '$city'
         *  and then use that to call each column.
         *
         *  We start off by opening a new table row and then we loop through each record
         *  in $city and assign it to a new variable called $val before echoing it out in 
         *  a <td> using the eacape() function in './src/functions/escape_html.php' to
         *  sanitise the ouput.
         *
         *  Finally we close the table row.
         *  
         *  Just to explain the nested loop a little more as it can be confusing, when the 
         *  '$query_select' was created it contained an array of each record returned and the 
         *  values of each column.
         *
         *  Looking at the first three rows returned we get
         *
         *  [0] => (
         *      [ID] => 1074
         *      [Name] => Mysore
         *      [CountryCode] => IND
         *      [District] => Karnataka
         *      [Population] => 480692
         *  )
         *
         *  [1] => (
         *      [ID] => 1075
         *      [Name] => Aligarh
         *      [CountryCode] => IND
         *      [District] => Uttar Pradesh
         *      [Population] => 480520
         *  )
         *
         *  [2] => (
         *      [ID] => 1076
         *      [Name] => Guntur
         *      [CountryCode] => IND
         *      [District] => Andhra Pradesh
         *      [Population] => 471051
         *  )
         *  
         *  As we assign each of these sets of column data to a variable called $city as we loop
         *  through, so with the array keys ([0], [1], [2]) shown above, we theoretically replace
         *  the key with $city, so we then end up with....
         *
         *  $city => (
         *      [ID] => 1074
         *      [Name] => Mysore
         *      etc...
         *  )
         *
         *  $city => (
         *      [ID] => 1075
         *      [Name] => Aligarh
         *      etc...
         *  )
         *
         *  $city => (
         *      [ID] => 1076
         *      [Name] => Guntur
         *      etc...
         *  )
         *
         *  There are five columns in each $city (ID, Name, Country Code, District, Population) so 
         *  the second foreach we simply loop through each element in the array and assign the value
         *  to $val and the echo it out.
         * 
         *  So given that we had a $city array for the first row of 
         *  $city => (
         *      [ID] => 1074
         *      [Name] => Mysore
         *      [CountryCode] => IND
         *      [District] => Karnataka
         *      [Population] => 480692
         *  )
         * 
         *  On each loop, we create $val from the value so would end up with 
         *  
         *  $val = 1074
         *  $val = Mysore
         *  $val = IND
         *  $val = Karnataka
         *  $val = 480692
         *
         * and these are what is shown in each <td></td>
        **/

        foreach ($query_select->getRows('all') as $city) {
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
echo '<b>'.$query_select->count () . ' records returned</b>';

?>

<h4>Entered Array</h4>

<!-- Echo out the $query_select array to the screen using <pre> tags for formatting -->
<pre>
$query_select = $db->select(
    'city',
    array(
        'WHERE'=>array(
            'Population' => array("BETWEEN", array('100000','500000')), 
            'CountryCode' => array("IN", array('IND','TUR'))
        ),
        'ORDER'=>array(
            'CountryCode ASC', 
            'Population DESC'
        ),
        'LIMIT'=> 10
    )
);
</pre>

<?php // Show the generated SQL and bindings. Function in /src/functions/php
showData ($query_select); ?>


        </div>
      </div>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
      </script>
      <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js">
      </script>
      
    </div>
    <?php include 'src/template/footer.php'; ?>

</body>
</html>

