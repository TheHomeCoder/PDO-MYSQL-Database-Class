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
    <title>Database Class | Full Query Example</title>
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
                <h1>Database Class | Full Query Examplee</h1>
            </div>

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

            <h4>Entered Array</h4>

            <!-- Echo out the $query_select array to the screen using <pre> tags for formatting -->
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

            <h4>Returning records</h4>

            <p>
                First off, we return the number of returned records using <code>$query_select->count ()</code>.
            </p>
            <p>
               We create the the standard &lt;table&gt;, &lt;thead&gt; and &lt;tbody&gt; manually.  
            </p>
            <p>
                To create the Table headers, we loop through the returned results using <code>$query_select->getRows('one') as $key => $value</code> as <code>getRows('one')</code> returns just the one row.
                <pre>
                    foreach ($query_select->getRows('one') as $key => $value) {
                        echo '&lt;th&gt;'.escape($key)."'&lt;/th&gt;'";
                    }
                </pre>
            </p>
            <p>
                To create the table rows we use <code>$query_select->getRows('all') as $city</code> to set each returned record to a variable called '$city'. As we loop through each of these, we create a new &lt;tr&gt; and &lt;tr&gt; and inside that, run another foreach to loop through the columns. 
                <pre>
                    foreach ($query_select->getRows('all') as $city) {
                        echo '&lt;tr&gt;';

                        foreach ($city as $val) {
                            echo '&lt;td&gt;'.escape($val).'&lt;/td&gt;';
                        }

                        echo '&lt;/tr&gt;';
                    }
                </pre>
                
            </p>


            <h4>Result</h4>

            <?php
            // Shown an example of using the count() function from './src/class/DB.php'
            echo '<b>'.$query_full->count () . ' records returned</b>'; 
            ?>
            <!-- 
                Start the table to hold the returned records.
                We use the Bootstrap table class (http://getbootstrap.com/css/#tables) purely for aesthetics.
            -->
            <table class="table">

                <thead>
                    <?php foreach ($query_full->getRows('one') as $key => $value) {
                       
                            echo "<th>".escape($key)."</th>";
                             
                    } ?>
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

        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
      
</div>
<?php include 'src/template/footer.php'; ?>

</body>
</html>
