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
    <title>Database Class | Field Selection</title>
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
        <div class="col-md-3">
            <?php include 'src/template/left.php'; ?>
        </div>
        <div class="col-md-9">
            <div class="page-header">
                <h1>Database Class | Field Selection</h1>
            </div>
            <p class="lead"> Fields are passed as separate arrays under the table element of the $tables array. This can be ommitted if you are selecting all fields.</p>

            <pre>
    $tables = array(
        'city'=> array(
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

        ), 
        
    )</pre>
            <p>
                <strong>The 'fields' array contains a separate array for each field. At the moment the array can only contain and optional alias.</strong>
            </p>

            <p>
               If you do not set any fields for a particular table, it will default to selecting all using the table name or alias <code>SELECT `city`.*</code> or <code>SELECT `CIT`.*</code> with each field comma separated <code>SELECT `CIT`.`Name` city_name, `CIT`.`Population` pop, `country`.`Code`</code>. If no fields are selected on any table, the end result will simply be <code>SELECT *</code>.
            </p>


            <p>
                <h3>Examples</h3>
                <h4>Fields from both tables, some with alias'.</h4>
                   <pre>
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
                ..
            )
        ), 
        'country'=> array(
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
               ...
            ),
            'where' => array (
                ...
            )
        ), 

    )</pre>

                Returns <code>SELECT `CIT`.`Name` city_name ,`CIT`.`Population` pop ,`CIT`.`District` ,`country`.`Code` ,`country`.`Region` ,`country`.`Name` country_name ,`country`.`IndepYear` year_of_ind  FROM</code>


                <h4>Fields from one table, some with alias' and none from the other.</h4>
                <pre>
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
                ..
            )
        ), 
        'country'=> array(
            'join' => array (
               ...
            ),
            'where' => array (
                ...
            )
        ), 

    )</pre>

                Returns <code>SELECT `CIT`.`Name` city_name ,`CIT`.`Population` pop ,`CIT`.`District` ,`country`.* FROM</code>

                <h4>No fields selected from either table.</h4>
                <pre>
    $tables = array(
        'city'=> array(
            'alias' => 'CIT',
            'where' => array (
                ..
            )
        ), 
        'country'=> array(
            'join' => array (
               ...
            ),
            'where' => array (
                ...
            )
        ), 

    )</pre>

                Returns <code>SELECT * FROM</code>
            </p>



      
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
      
    </div>
    <?php include 'src/template/footer.php'; ?>

</body>
</html>

