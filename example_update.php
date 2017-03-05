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
    <title>Database Class | Select Update Example</title>
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
                    $tables = array(
                        'city'=> array(
                            'fields'=>array(
                                'Name' => 'Tixny' ,
                                'District' => 'GTHBR'
                                ),
                            'where'=>array(
                                'Population' => array("<", '1000'),
                            )
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

            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
        </script>
        <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js">
        </script>
      
    </div>
</div>
<?php include 'src/template/footer.php'; ?>

</body>
</html>
