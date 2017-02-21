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
    <title>Database Class | WHERE clause</title>
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
                <h1>Database Class | WHERE clause</h1>
            </div>
            <p class="lead"> The WHERE clause conditions are passed via the 'where' key in the '$tables' array. </p>

            <pre>
    $tables = array(
        'city'=> array(
            ...
            'where' => array (
                'District' => array("LIKE", 'A%'),
                'Population' => array("BETWEEN", array('100000','500000'))
                
            )
        ), 
    )</pre>

            <p>
                This is set by the array key 'where' and contains a single array. This array contains a key (field name) and array for each field, containing the operator and the criteria where required.
            </p>

            <p>
                Each of the fields are added to the prepared statement and their values are bound using bindValue. The only exception to this are the NULL and NOT NULL parameters, which are currently placed into the statement as they are.
            </p>

            <p>
                <div class="alert alert-info">
                    <h4>NOTE</h4>
                    <p>
                        Each of the fields in the 'WHERE' clause are currently defaulted to be used as 'AND'. An option to have 'OR' will come in a future version.
                    </p>
                </div><!-- /.alert alert-info -->
            </p>

            <p>
                <h4>Avaiable options</h4>

                <code>'CountryCode' => array("=", 'USA')</code><br>
                <i>CountryCode equals 'USA'</i><br><br>

                <code>'Population' => array(">", '100000')</code><br>
                <i>Population is greater than 100000</i><br><br>

                <code>'Population' => array("<", '500000')</code><br>
                <i>Population is less than 500000</i><br><br>

                <code>'Population' => array(">=", '100000')</code><br>
                <i>Population is equal to or greater than 100000</i><br><br>

                <code>'Population' => array("<=", '500000')</code><br>
                <i>Population is less than or equal to 500000</i><br><br>

                <code>'CountryCode' => array("!=", 'USA')</code><br>
                <i>CountryCode does not equal 'USA'</i><br><br>

                <code>'Population' => array("<>", '100000')</code><br>
                <i>CountryCode is greater than or less than 100000</i><br><br>

                <code>'District' => array("LIKE", 'A%')</code><br>
                <i>District begins with 'A'</i><br><br>

                <code>'District' => array("LIKE", '%LAND')</code><br>
                <i>District ends with 'LAND'</i><br><br>

                <code>'Population' => array("BETWEEN", array('100000','500000'))</code><br>
                <i>Population is between 100000 and 500000</i><br><br>

                <code>'Code' => array("IN", array('USA','ARG','IND','JPN'))</code><br>
                <i>Code equals 'USA' or 'ARG' or 'IND' or 'JPN</i><br><br>

                <code>'IndepYear' => array("NULL")</code><br>
                <i>IndepYear is empty</i><br><br>

                <code>'IndepYear' => array("NOT NULL")</code><br>
                <i>IndepYear is not empty</i><br><br>

            </p>

        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
      
    </div>
    <?php include 'src/template/footer.php'; ?>

</body>
</html>

