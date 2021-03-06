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
    <title>Database Class | Insert Example</title>
</head>

<body>

<div id="jumbo" class="jumbotron">
    <div class="container">
        <img src="homecoderstrip200.png">
        <h1>
          PDO Database Class
        </h1>
    </div>

</div>

<div class="container">
    
    <div class="row">
        <div class="col-md-3">
            <?php include 'src/template/left.php'; ?>
        </div>
        <div class="col-md-9">
            <div class="page-header">
                <h1>Database Class | Insert Example</h1>
            </div>

            <p class="lead">
                Run an insert on a single table
            </p>
            <p>
                Insert into the 'city' table a record with....
                <ul>
                    <li>'Name' = 'TestName'</li>
                    <li>'Population' = '9999'</li>
                    <li>'District' = 'Disty'</li>
                    <li>'CountryCode' = 'ZWE'</li>
                </ul>
            </p>

            <?php 
            /** Insert the record
             *
             *  Using the insert() function in the Database class ($db) pass in 
             *  the parameters and assign the resultant array to $query_insert
            **/
            $query_insert = $db->insert(
                $tables = array(
                    'city'=> array(
                        'fields'=>array(
                            'Name' => 'TestName' ,
                            'Population' => "9999", 
                            'District' => "Disty", 
                            'CountryCode' => 'ZWE'
                        )
                    )
                )
            );

            ?>
            
            <h4>Entered Array</h4>

            <!-- Echo out the $query_select array to the screen using <pre> tags for formatting -->
            <pre>
            $query_insert = $db->insert(
                $tables = array(
                    'city'=> array(
                        'fields'=>array(
                            'Name' => 'Test bName' ,
                            'Population' => "99899", 
                            'District' => "Disbty", 
                            'CountryCode' => 'ZWE'
                        )
                    )
                )
            );
            </pre>

            <?php // Show the generated SQL and bindings. Function in /src/functions/php
            showData ($query_insert); ?>

            <h4>Returning records</h4>
            <p>
                We return the number of records entered using <code>$query_insert->count ()</code> and the id of the last inserted record using <code>$db->insertId ()</code>.
            </p>


            <h4>Result</h4>

            <?php
            // Shown an example of using the count() function from './src/class/DB.php'
            echo $query_insert->count() .' rows inserted<br>';

            // Shown an example of using the insertId() function from './src/class/DB.php'
            echo 'New record ID : ' . $db->insertId ();
            ?>

        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
      
</div>
<?php include 'src/template/footer.php'; ?>

</body>
</html>

