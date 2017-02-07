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
  
  
Follow the first three steps in the set-up instructions in index.php to create and connect to the sample database used in the examples.

Basic use - using a SELECT query
--------------------------------
Connect to the database by including /src/config.php at the top of the file.

Pass the query parameters into a the select() function via an array 
```php
<?php
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
?>
```
This will create a query of
```php
SELECT * FROM `city` WHERE `Population` BETWEEN ? AND ? AND `CountryCode` IN (?, ?) ORDER BY CountryCode ASC, Population DESC DESC LIMIT 10
```
With bindings of 
```php
Array
(
    [0] => 100000
    [1] => 500000
    [2] => IND
    [3] => TUR
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

|ID | Name  |  Country Code  |  District |   Population
|:-----------:|:------------:|:------------:|:------------:|:------------:|
|1074  |  Mysore | IND| Karnataka  | 480692
|1075  |  Aligarh |IND |Uttar Pradesh |  480520
|3366  |  Diyarbakir | TUR |Diyarbakir | 479884
| etc....


Version History
------------
####1.1
Switched the ORDER BY function into an array

Updated example_select_multiple.php with new method

Updated index.php with new instructions

Updated README.md with new example


A few bits of erroneous commenting fixed

####1.0
Initial Build
