PDO MySQL Class
===================

A PHP MySQL PDO class which rovide functions for connection to the database, performing select, insert, update and delete querys and returning 
record counts and lastInsertId's, using the binding method to prevent SQL injection attacks and displaying the data as sanitised html.

## Full instructions and a list of working examples can be found in index.php 
To be able to run the examples you will need 

  Server with PHP 5+ (Web host or locally with WAMP / XAMPP
  
  PDO Extension
  
  PDO_MYSQL Driver
  
  MySQL database
  
  
You can run index.php and the 'Instructions' pages on any php enabled web server. 

To use the examples you will need to create a database, using the sample provided and connect to it. Follow the first three steps in the set-up instructions in index.php to do this.

Basic use - using a SELECT query
--------------------------------
Connect to the database by including /src/config.php at the top of the file.

Pass the query parameters into a the select() function via an array 
```php
<?php
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
                            'District' => array("LIKE", 'A%'),
                            'Population' => array("BETWEEN", array('100000','500000'))
                            
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
?>
```
This will create a query of
```php
SELECT `CIT`.`Name` city_name, `CIT`.`Population` pop, `CIT`.`District` FROM `city` CIT  WHERE `CIT`.`District` LIKE ? AND `CIT`.`Population` BETWEEN ? AND ? ORDER BY `CIT`.`District` ASC, `CIT`.`Population` DESC LIMIT 2,10
```
With bindings of 
```php
Array
Array
(
    [0] => A%
    [1] => 100000
    [2] => 500000
)
```
Pass $query_select into the getRows() function using 'all' as a parameter to show we are returning multiple rows and present them in a table
```php
foreach ($query_select->getRows('all') as $city) {
    echo "<tr>";

    foreach ($city as $val) {
        echo "<td>".escape($val)."</td>";
    }

    echo "</tr>";
}
```

Which would return

|city_name | pop  |  District 
|:-----------:|:------------:|:------------:|
|Lhokseumawe  |  109600 | Aceh 
|Rio Branco   |  259537 | Acre 
|Ceyhan  |  102412 | Adana
| etc....


Version History
------------
####2.0
Full rewrite of the class.

Added the option to ive tables and column alias's.

Removed the tableAction() function and implemented this directly into the buildTables() function.

Rebuild of the main CRUD() function to remove duplicated steps and optimise.

Replaced single table name passing into the select array with a new
variable of arrays called $tables.

Switched LIMIT clause from two seperate keys into one array.

Added 'IS NULL' and 'IS NOT NULL' options to the WHERE clause. New whereElementNull() function to accomodate this.

Amended whereClause() in /src/classes/DB.php to accept the new $tables
array instead of the previous $conditions array.

Amended whereClause() in /src/classes/DB.php to iterate the tables and
build the where clause from there.

Amended whereElementStandard (), whereElementIn() and
whereElementBetween() to include backticks around table and column
names.

Added a print_r of the query to the showData() function.

Added a template and navigation menu just to improve the examples and documentation.

Improved commenting.

Added instruction files that can be viewed without connecting to the database.

Better formatting and mor information in the examples.

####1.1
Switched the ORDER BY function into an array

Updated example_select_multiple.php with new method

Updated index.php with new instructions

Updated README.md with new example


A few bits of erroneous commenting fixed

####1.0
Initial Build
