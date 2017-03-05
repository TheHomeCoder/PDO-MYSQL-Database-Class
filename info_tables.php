<?php

/**
* Include the config file
**/
require_once 'src/config.php';

/**
* Display php errors. Uncomment to use
**/
// ini_set('display_errors', 'ON');

?>


<!doctype html>

<html lang="en">
<head>
    <?php include 'src/template/head.php'; ?>
    <title>Database Class | Table Selection</title>
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
                <h1>Database Class | Table Selection</h1>
            </div>
            <p class="lead"> Each table is passed in as a key in the '$tables' array. Any other conditions (where clause, alias, fields to select and joins) are passed in as an array under the $key.</p>

            <pre>
    $tables = array(
        'city'=> array(
            'alias' => 'CIT',
            'fields' => array (
                ...
            ),
            'where' => array (
                ...
            )
        ), 
        'country'=> array(
            'fields' => array (
                ...
            ),
            'join' => array (
                'join_type' => 'LEFT',
                'foreign_table' => 'CIT', //Alias of 'city'
                'foreign_key' => 'CountryCode',
                'local_key' => 'Code',
            ),
            'where' => array (
                ...
            )
        ), 

    )</pre>
            <p>
                <strong>The 'fields' and 'where' elements are explained on separate pages so we will just be dealing with the tables, their aliases and joins here.</strong>
            </p>

            <p>
                You require one key containing an array for each table and the key must match the name of the table.
            </p>

            <p>
                <h3>Table alias</h3>
                If you want to set an alias for the table, add it at the start of the array for that table using <code>'alias' => 'xxx'</code> where xxx is the name of the array that you want to use.
            </p>

            <p>
                <div class="alert alert-info">
                    <h4>NOTE</h4>
                    <p>
                        If you set an alias to the table, that is what you will need to use when joining another table to it, or outputting it to the screen.
                    </p>
                </div><!-- /.alert alert-info -->
            </p>

            <p>
                <h3>Joining tables</h3>
                If you have more than one table and want to join that to another one use <code>'join' => array (...)</code> with the following parameters..<br>
                
                <code>'join_type' => 'xxx'</code>Where xxx is the type of join. Only LEFT and RIGHT have been tested so I am not sure how other joins will work.   <br>
                <code>'foreign_table' => 'xxx'</code>Where xxx is the name of the table that you are joining to. Note that if you have set an alias for the table you are joining to, you will need to use that instead of the table name.   <br>
                <code>'foreign_key' => 'xxx'</code>Where xxx is the column name of the table that you want to join to.   <br>
                <code>'local_key' => 'xxx'</code>Where xxx is the column nae of this table that you want to join to the other table.   <br>
            </p>

      
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
      
    </div>
    <?php include 'src/template/footer.php'; ?>

</body>
</html>

