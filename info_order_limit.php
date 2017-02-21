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
    <title>Database Class | ORDER BY and LIMIT</title>
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
                <h1>Database Class | ORDER BY and LIMIT</h1>
            </div>

            <p class="lead"> The ORDER BY and LIMIT conditions are passed via an array called '$conditions'. </p>

            <pre>
    $conditions = array(
        'ORDER'=>array(
            array('city','District','ASC'),
            array('country','Code','DESC')
        ),
        'LIMIT'=>array(10, 2)
    )</pre>


            <h3>ORDER BY</h3>
            <p>
                This is set by the array key 'ORDER' and contains a separate array for each part of the ORDER BY clause and each array contains three parts : table name, field name and direction. The ORDER BY priority is set by the order that the arrays are passed.
            </p>

            <p>
                In the example above, the SQL generated would be <code>ORDER BY `city`.`District` ASC, `country`.`Code` DESC</code>.
            </p>

            <p>
                <div class="alert alert-info">
                    <h4>NOTE</h4>
                    <p>
                        If you have set aliases for the tables, you will need to use the in place of the table name in the ORDER array. EG if you have set an alias of <b>CIT</b> for the city table, you would have to use <code>array('CIT','District','ASC')</code> in the ORDER array.
                    </p>
                </div><!-- /.alert alert-info -->

            </p>

            <h3>LIMIT</h3>
            
            <p>
                This is set by the array key 'LIMIT' and contains a single array containing either 1 or 2 parts : the number to limit by and the offset. If there is only one part, it will set that as the LIMIT number with no offset.
            </p>

            <p>
                In the example above, the SQL generated would be <code>LIMIT 2,10</code>.
            </p>




        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
      
    </div>
    <?php include 'src/template/footer.php'; ?>

</body>
</html>

