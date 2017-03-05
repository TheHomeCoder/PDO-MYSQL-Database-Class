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
   <title>Database Class</title>
</head>

<body>

<div id="jumbo" class="jumbotron">
    <div class="container">
        <img src="homecoderstrip200.png">
        <h1>
          PDO Database Class
        </h1>
        <p>
         An Object Orientated database class for connection, read, write, create and delete
        </p>
    </div>

</div>
<div class="container">
    
    <div class="row">
        <div class="col-md-3">
            <?php include 'src/template/left.php'; ?>
        </div>
        <div class="col-md-9">
            <div class="page-header">
                <h1>PDO Database CRUD Class</h1>
            </div>


            <h2 id="examples">Examples</h2>
            <a href="example_select_multiple.php">Select Multiple</a> | 
            <a href="example_select_single.php">Select Single</a> | 
            <a href="example_insert.php">Insert</a> | 
            <a href="example_update.php">Update</a> | 
            <a href="example_delete.php">Delete</a> | 
            <a href="example_full.php">Full Query</a> <br>
            <small>To use the examples you need to have a database containing the sample database. Please follow the first three steps in 'Set-up' below to create this and connect to it.</small>

            <h2 id="usage">Usage</h2>

            <h4>Requirements</h4>

            <ul>
                <li>
                    <p>Server with PHP 5+ (Web host or locally with <a href="http://www.wampserver.com/" target="_blank">WAMP</a> / <a href="https://www.apachefriends.org/" target="_blank">XAMPP</a></p>
                </li>
                <li>
                    <p>PDO Extension</p>
                </li>
                <li>
                    <p>PDO_MYSQL Driver</p>
                </li>
                <li>
                    <p>MySQL database</p>
                </li>
            </ul>

            <h4>Set-up</h4>
                <p>The easiest way to test the functionality is to use the example database stored in world.sql (from the MYSQL <a href="https://dev.mysql.com/doc/index-other.html" target="_blank"> Official Site</a>). The instrucions here assume you have done that. If you are using your own database tables, change any details accordingly.</p>

                <p>Create a new database and import world.sql into it.</p>

                <p>Open /src/config_files/db.php and change the settings to match those of your database.</p>

                <p>Create a new file and include /src/config.php at the top.</p>

                <p>Open up a connection the the Database class and assign it to a variable<br>$db = DB::dbConnect();<br>NOTE: You do not need to include the database class file (/src/classes/DB.php) as this is controled by /src/functions/autoload_class.php</p>

                <p>You can now use $db to access all of the functions within the class.</p>

            <h4>Available public functions</h4>  

            <ul>
                <li>dbConnect() - Opens up a connection to the database</li>
                <li>select() - runs a SELECT query</li>
                <li>delete() - runs a DELETE query</li>
                <li>insert() - runs a INSERT query</li>
                <li>update() - runs a UPDATE query</li>
                <li>fullQuery() - runs general query via a full sql statement</li>
                <li>insertId() - returns the id of the last row inserted into the database by the last query</li>
                <li>count() - returns the number of rows affected by the last query</li>
            </ul>

            <p>Each of the functions that run a query on the database (except for fullQuery()) take two parameters, each of which are arrays
            <ul>
                <li>$tables - lists the tables, their joins, fields, alias' and where conditions relevant to each table. This is done in three seperate arrays : 'fields', 'join' and 'where'</li>
                <li>$conditions - contains the order and limit criteria</li>
            </ul>
            <p>
                More information on these can be found in the following files..<br>
            </p>
            <a href="info_tables.php">Tables</a> | 
            <a href="info_fields.php">Fields</a> | 
            <a href="info_where_clause.php">WHERE Clause</a> | 
            <a href="info_order_limit.php">ORDER BY and LIMIT</a> 

            <h4>Example Use</h4>
            <small>See the examples for more details</small>
            <pre>            
             $query_select = $db-&gt;select(
                $tables = array(
                    'city'=&gt; array(
                        'alias' =&gt; 'CIT',
                        'fields' =&gt; array (
                            'Name' =&gt; array (
                                'alias' =&gt; 'city_name',
                            ),
                            'Population' =&gt; array (
                                'alias' =&gt; 'pop',
                            ),
                            'District' =&gt; array (

                            ),
                        ),
                        'where' =&gt; array (
                            'District' =&gt; array("LIKE", 'A%'),
                            'Population' =&gt; array("BETWEEN", array('100000','500000'))
                            
                        )
                    ), 
                ),
                $conditions = array(
                    'ORDER'=&gt;array(
                        array('CIT','District','ASC'),
                        array('CIT','Population','DESC')
                    ),
                    'LIMIT'=&gt;array(10, 2)
                )
            );
            </pre>

        </div>


    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
      
</div>
<?php include 'src/template/footer.php'; ?>

</body>
</html>

