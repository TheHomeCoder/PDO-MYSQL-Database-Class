<?php
/*------------------------------------------------------------------------------
** File:        /src/classes/DB.php
** Description: Database class file
** Author:      Steve Ball
** 
** Includes 
** Public   dbConnect()  Open a connection to the database
** Public   fullQuery()  Runs a full sql query for complicated queries (including joins)
** Private  crud()       Covers all CRUD functions. Called from separate functions below.
** Public   select()     Runs a SELECT query on a specified table with criteria using crud()
** Public   delete()     Runs a DELETE query on a specified table with criteria using crud()
** Public   insert()     Runs a INSERT query on a specified table with criteria using crud()
** Public   update()     Runs a UPDATE query on a specified table with criteria using crud()
** Public   getRows ()   Returns rows from the query
** Public   insertId ()  Returns the last inserted id following an insert query
** Public   count ()     Returns a row count on a specified query
** Plus helper functions used within all of the above
** 
**------------------------------------------------------------------------------ */

// Include the './src/config.php' file to access $GLOBALS['db'] for the db connection
require_once './src/config.php';

class DB {
    private static $_instance = null;
    private $_pdo,
            $_sql,
            $_query,
            $_bindArray,
            $_error = false,
            $_results,
            $_count = 0;

    private function __construct() {
        try {
            $this->_pdo = new PDO('mysql:host=' . Config::get('db/host', 'db') . ';dbname=' . Config::get('db/dbname', 'db'),  Config::get('db/username', 'db'), Config::get('db/password', 'db') );

        } catch(PDOException $e) {
            echo "<h3>Connection to the database failed!</h3><p>Please check that you have set you correct database settings in <b>/src/config_files/db.php</b></p>"; //user friendly message
            die($e->getMessage());
        }
    }


    /** PUBLIC FUNCTIONS
     *  
     *  dbConnect()     Opens a connection to the database
     *-----------------------------------------------------
     *
     *  select()        Runs a 'SELECT' query
     *  delete()        Runs a 'DELETE' query
     *  insert()        Runs a 'INSERT' query
     *  update()        Runs a 'UPDATE' query
     *
     *  $table          An array of tables, fields and where criteria
     *  $conditions     An array of conditions such as ORDER BY and LIMIT
     *
     *  These pass the data into the private function crud()
     *
     *-----------------------------------------------------
     *  fullQuery()     Runs a full SQL query
     *  
     *  $sql            A full SQL query
     *
     *-----------------------------------------------------
     *  
    **/

    public static function dbConnect() {
        
        if(!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    public function select ($table, $conditions = array()) {

        return $this->crud('SELECT', $table, $conditions);        
    }

    public function delete ($table, $conditions = array()) {
        
        return $this->crud('DELETE', $table, $conditions);
    }

    public function insert ($table, $conditions = array()) {
        
        return $this->crud('INSERT', $table, $conditions);
    }

    public function update ($table, $conditions = array()) {
        
        return $this->crud('UPDATE', $table, $conditions);
    }

    public function fullQuery ($sql) {
        //$this->_bindArray = array();
        $this->error = false;

        // Create an accesible variable of the query for debugging
        $this->_sql = $sql;

        if ($this->_query = $this->_pdo->prepare($sql)) {

            if($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
        }

        return $this;
    }


    /** crud()
     *  This is the main function that all of the others are passed into for processing
     *
     *  $method         The type of query (SELECT, INSERT, UPDATE, DELETE)
     *  $tables         An array of tables, fields and where criteria passed in from the public function
     *  $conditions     An array of any other parameters for the query (WHERE, ORDER BY, LIMIT and START)
    **/

    private function crud ($action, $tables, $conditions = array()) {

        // The final SQL statement is built using four variables so null them first
        $this->show_fields = null;
        $this->show_tables = null;
        $this->show_table_action = null;
        $this->show_where = null;


        // All of the bindings for the query get placed in an array so create it
        $this->_bindArray = array();

       
        // Collect the fields, tables and joins, and where clauses
        $this->buildQuery ($tables, $action);


        // Start building the query by adding the method (SELECT, DELETE, INSERT, UPDATE) to the start of the SQL statement
        $this->_sql = $action ;

        // Each different method has a slightly different make up so we continue the build seperately
        if ($action == 'SELECT') {
            
            // Next up on a SELECT is the fields or * if none have been set
            $this->_sql .= ($this->fields_exists) ? $this->show_fields : ' *';

            // Build the table part of the SQL statement
            $this->_sql .= $this->show_tables;

            // Add the WHERE clause
            $this->_sql .= $this->show_where;

            // Add the ORDER clause
            $this->orderClause ($conditions);

            // Add the LIMIT BY cause
            $this->limitClause ($conditions);
        } 


        if ($action == 'INSERT') {

       
            // Next up on an INSERT is the table
            $this->_sql .= $this->show_tables;

            // Then we set the fields
            $this->_sql .= $this->show_fields;
        }


        if ($action == 'DELETE') {
       
            // Next up on a DELETE is the table
            $this->_sql .= $this->show_tables;

            // Then we add the WHERE clause
            $this->_sql .= $this->show_where;
        } 

      
        if ($action == 'UPDATE') {
       
            // Next up on an UPDATE is the table
            $this->_sql .= $this->show_tables;

            // Then we set the fields
            $this->_sql .= $this->show_fields;

            // Then we add the WHERE clause
            $this->_sql .= $this->show_where;
        }


        // Now that the SQL statement is built, we run the query
        $this->runQuery ();


        // If the query has been an INSERT, set the $this->_lastInsertId
        if ($action == 'INSERT') {

            $this->_lastInsertId = $this->_pdo->lastInsertId();

        }


        // Retutn everything built up in $this
        return $this;

    } //function crud ()



    /**  Build the parts of the SQL statement
     *
     *  Creates the fields to select, tables and their joins, the where clause
     *  and the action (FROM, SET etc)
     *
    **/
    private function buildQuery ($tables, $action) {
      
        // All of the selected fields for the query get placed in an array so create it
        $this->fields_output = array();

        // We use $this->fields_exists elsewhere to make decisions so default it to false
        $this->fields_exists = false;

        
        // Loop through all the tables where the $key will be the table name and the $value will be the 
        // 'fields', 'where' and 'joins' arrays for the table.
        foreach ($tables as $key => $value) {

           
            // Set the table to $this->table as we will be using this in several nested functions
            $this->table = (isset($value['alias'])) ? $value['alias'] : $key;


            // There different build orders depending on the method so we continue the build seperately
            if ($action == 'INSERT' || $action == 'UPDATE') {
                
                // Create the tables and joooins part of the SQL statement and add it to $this->show_fields
                $this->show_tables = $this->buildTables ($tables, $action);

                // Create the required fields part of the SQL statement and add it to $this->show_fields
                $this->show_fields = $this->buildFields (isset($value['fields']) ? $value['fields'] : '', $action) ;


            } else {
                // Create the required fields part of the SQL statement and add it to $this->show_fields
                $this->show_fields = $this->buildFields (isset($value['fields']) ? $value['fields'] : '', $action) ;

                // Create the tables and joooins part of the SQL statement and add it to $this->show_fields
                $this->show_tables = $this->buildTables ($tables, $action);
            }
            

            // Call the buildWhere () function to build up the $this->show_where variable
            if(isset($value['where'])) {$this->buildWhere ($value['where']);} 

        } // End foreach

    } // buildQuery ()




    /**  Build the fields
     *
     *  Returns the specified fields to add to the SQL statement and passes it back
     *  to $this->show_fields in buildQuery().
     *
     *  For each of the tables, check to see if any fields have been selected and if so,
     *  create a `table`.`column` pairing for each of them in the $this->fields_output array.
     *  If not, add `table`* to the $this->fields_output array.
     *
     *  At the end, if the $this->fields_output array contains any data, return it,
     *  otherwise just return ' * '.
     *
     *  $array          An array of fields passed in from buildQuery()
     *
    **/
    private function buildFields ($array, $action) {

        // As we do not always specifiy fields, only run if the $array exists
        if($array){

            // As the $array exists, set $this->fields_exists to true for reference elsewhere
            $this->fields_exists = true;

            // We only use fields on SELECT, INSERT and UPDATE and each works differently so build them seperately
            if ($action == 'SELECT') {

                // Loop through the array where $key is the column name and $value are the 
                // arrays for that column such as 'alias' and 'count'
                foreach ($array as $key => $value) {

                  
                    // Check to see if an alias fir the field has been set
                    // If so, set the alias to $this->alias
                    $this->alias = (isset($value['alias'])) ? ' ' . $value['alias'] : '';


                    // Create the `table`.`column` pair and appned $this->alias ot it then add it to the $this->fields_output array
                    $this->fields_output[] .= ' `'.$this->table.'`'.'.'.'`'.$key.'`' . $this->alias;
                       
                } // End foreach
            
                // Return  $this->fields_output[] as a comma seperated string
                return implode($this->fields_output, ',');
            } 


            if ($action == 'INSERT') { 

                // We use two arrays, $fields and $values so set them first
                $fields = array();
                $values = array();

                // Loop through the array
                foreach ($array as $key => $value) {

                    // Add the key to $fields
                    $fields[] .= '`'.$key.'`';

                    // Add a ? to $values
                    $values[] .= '?';

                    // Add the value to $this->_bindArray[]
                    $this->_bindArray[] .= $value;

                }

                // Create the two parts of the INSERT with $out_1 being the fields and $out_2 being the values ('?')
                $out_1 = '('.implode(', ', $fields).')';
                $out_2 = '('.implode(', ', $values).')';

                
                // Return the two parts seperated by ' VALUES '
                return $out_1 . ' VALUES ' . $out_2;
            }


            if ($action == 'UPDATE') { 

       
                // With UPDATE we only need a single string so go straight in to the foreach
                foreach ($array as $key => $value) {

                    // Add the ley followed by '=?' to build up the INSERT string
                    $this->fields_output[] .= ' `'.$key.'`' . ' = ?';

                    // Add the value to $this->_bindArray[]
                    $this->_bindArray[] .= $value;

                }

                // Return  $this->fields_output[] as a comma seperated string
                return implode(',',  $this->fields_output);

            }


        } else { // No Array
            
            // The array contains no data so add `table`* to the $this->fields_output array
            $this->fields_output[] .= '`'.$this->table.'`'.'.*';
                
        } // End if $array


    } // buildFields ()




    /**  Build the tables
     *
     *  Returns the correct action (FROM, SET etc), followed by the tables and their joins to the SQL 
     *  statement and passes it back to $this->show_tables in buildQuery(). 
     *
     *  For each of the tables, wrap the table name in backticks and , wrap it in the join criteria if set. 
     *
     *  $tables         An array of tables, fields and where criteria passed in from buildQuery()
     *  $action         The type of query (SELECT, INSERT, UPDATE, DELETE) passed in from buildQuery()
     *
    **/
    private function buildTables ($tables, $action) {

    

        // Each different method has a slightly different table make up so we continue the build seperately
        if ($action == 'INSERT' || $action == 'DELETE') {

            // Before the table, as this is an INSERT or DELETE we need to add 'INTO' or 'FROM' into the SQL Statement
            $out = ($action == 'INSERT') ? ' INTO' : ' FROM';

            // We only have one table with an INSERT and that is a key in the array so we use array_keys 
            // and set it to the variable $table.... 
            $table = array_keys($tables);
  
            // ....then we simply use position [0] (the only key) as the table name
            $out .= ' `' . $table[0].'`';

        
        } // INSERT || DELETE


        if ($action == 'UPDATE') {

            // We only have one table with an INSERT and that is a key in the array so we use array_keys 
            // and set it to the variable $table.... 
            $table = array_keys($tables);
  
            // ....then we simply use position [0] (the only key) as the table name
            $out = ' `' . $table[0].'`';

            // Add ' SET' t the end of the string
            $out .= ' SET';

        
        } // UPDATE


        if ($action == 'SELECT') {

            $out = ' FROM';
            // Loop through all the tables in the array - $ket is the table name
            foreach ($tables as $key => $value) {

                // We use two variables the wrap the table name in if it is a joined table so clear them of any old data
                $joinType = '';
                $joinOn = '';

              
                /**  Create the table joins
                 *
                 *  If a join has been specified, create it along with the criteria
                 *
                 *  $joinType
                 *  Prepends the join type (LEFT, RIGHT etc) to the word 'JOIN'
                 *
                 *  $joinOn
                 *  Adds the parameters for the join
                 *
                 *  Starts with 'ON ' and then adds the first table.column using 
                 *  $key.'.'.$value['join']['local_key']
                 *
                 *  It the adds ' = ' before adding the second table.column using
                 *  $value['join']['foreign_table'].'.'.$value['join']['foreign_key']
                 *
               **/

                // Prepend the join type (LEFT, RIGHT etc) to the word 'JOIN'

                $this->alias = (isset($value['alias'])) ? $value['alias'] . ' ' : '';
                $this->join_name = ($this->alias) ? $this->alias: $key;

                if(isset($value['join'])) {
                    $joinType = $value['join']['join_type'] . ' JOIN';
                    $joinOn = 'ON `' . $this->join_name.'`.`'.$value['join']['local_key'] . '` = `' . $value['join']['foreign_table'].'`.`'.$value['join']['foreign_key'] .'`';
                } // if join 

                // Wrap the table name ($key) with the join parts. The join parts will be empty strings if no join was set
                $out .= $joinType.' `' . $key . '` ' .$this->alias.$joinOn;

            } // foreach

        } // SELECT



        // Return the tables part of the SQL statement
        return $out;

    } // buildTables ()




    /**  Build the where clause
     *
     *  Returns the where clause to add to the SQL statement and passes it back
     *  to $this->show_where in buildQuery().
     *
     *  For each of the tables, check to see if any fields have been selected and if so,
     *  create a `table`.`column` pairing for each of them in the $this->fields_output array.
     *  If not, add `table`* to the $this->fields_output array.
     *
     *  At the end, if the $this->fields_output array contains any data, return it,
     *  otherwise just return ' * '.
     *
     *  $array          An array of fields passed in from buildQuery()
     *
    **/
    private function buildWhere ($array) {

        // Only do anything if the array contains anything
        if($array){ 

            // Loop through the array where $key is the column name and $value are the 
            // array containing the operator [0] and values [1]
            foreach($array as $key => $value){ 

                // We set each element of the where clause to $this->where before setting it to
                // $this->show_where. If it already exists we must have already have something
                // in the where clause so set $preSql to ' AND ' otherwise set it to ' WHERE '
                $preSql = (isset($this->where)) ? ' AND ' : ' WHERE ';

                // As we need different ways of building the where clause elements, depending on
                // the operator ($value[0]), we run a switch statement to call the function to
                // build the correct type of element, passing in the field name ($key) and the
                // operator and criteria ($value array)
                switch($value[0]) {
                    case '>':
                    case '<':
                    case '=':
                    case '<=':
                    case '>=':
                    case '!=':
                    case '<>':
                    case 'LIKE':
                        $this->where = $this->whereElementStandard ($key, $value);
                        break;

                    case 'IN':
                        $this->where = $this->whereElementIn ($key, $value); 
                        break;

                    case 'BETWEEN':
                        $this->where = $this->whereElementBetween ($key, $value); 
                        break;
                    case 'NULL':
                    case 'NOT NULL':
                        $this->where = $this->whereElementNull ($key, $value); 
                        break;

                    default:
                        $this->where = $this->whereElementStandard ($key, $value);
                        break;
                } // End switch

           
                // Add the created where clause elements to $this->show_where
                $this->show_where .= $preSql.$this->where;
            } // End foreach
        } // End if $array
    } // buildWhere ()




    /** QUERY BUILDING FUNCTIONS


        orderClause()
        limitClause()

        Simply handles the WHERE, ORDER BY and LIMIT parts of the query
    */
  
    /** Create the order clause for the SQL statement
     *
     *  Builds the order clause for the SQL statement from an array of arrays.
     *
     *  $conditions        An array of conditions passed in from buildQuery()
     *  
     *  We are using the 'ORDER' key from the array the get 'ORDER BY' parameters
     *
    **/
    private function orderClause ($conditions) {

        // We only do anything if the 'ORDER' key exists in the array
        if(array_key_exists("ORDER",$conditions)){
            // The 'ORDER' key exists in the array so continue the SQL statement with an 'ORDER BY'
            $this->_sql .= ' ORDER BY ';

            // We will use $i to see if we are at the first condition so set it to 0
            $i = 0;

            foreach($conditions['ORDER'] as $value){

                // If we have more than one condition, prefix each one (after the //first) with ', '
                $this->_sql .= ($i > 0) ? ', ' : '';

                // Add the ORDER BY condition
                // [0] = table
                // [1] = column
                // [2] = direction
                //
                // Work on this once table aliases are avaiable
                $this->_sql .= '`'.$value[0].'`.`'.$value[1].'` '.$value[2];

                // Increment the counter
                $i++;
            } // End foreach
        } // End array_key_exists
    } // orderClause ()


    /** Create the limit clause for the SQL statement
     *
     *  Builds the limit clause for the SQL statement from an array.
     *
     *  $conditions        An array of conditions passed in from buildQuery()
     *  
     *  We are using the 'LIMIT' key from the array the get 'ORDER BY' parameters
     *
    **/
    private function limitClause ($conditions) {

        // We only do anything if the 'LIMIT' key exists in the array
        if (array_key_exists("LIMIT",$conditions)) {

            // The 'LIMIT' key exists in the array so continue the SQL statement with an 'LIMIT'
            $this->_sql .= ' LIMIT ';

            // If a start position has been specified add it to the SQL statement followed by a comma
            $this->_sql .= (isset($conditions['LIMIT'][1])) ? $conditions['LIMIT'][1].',' : '';

            // Add the limit number to the SQL statement
            $this->_sql .= $conditions['LIMIT'][0];

         }
    } // limitClause ()



    /**  WHERE ELEMENT FUNCTIONS

        whereElementStandard()
        whereElementIn()
        whereElementBetween()

        Controls the elements of the WHERE clause, setting the fields and binding the values

        whereElementStandard()
        Used by =, <, >, <=, =>, !=, <>, LIKE
        Example : `id` = ? 

        whereElementIn()
        Used by IN
        Example : `id` IN (?, ?) 

        whereElementIn()
        Used by BETWEEN
        Example : `id` BETWEEN ? AND ?

        whereElementNull()
        Used by IS NULL, IS NOT NULL
        Example : `id` BETWEEN ? AND ?
    */

    private function whereElementStandard ($key, $value) {
        
        // Add the second part of the $value array (the value) to $this->_bindArray[]
        $this->_bindArray[] .= $value[1];

         // Append the first part of the $value array (the operator) to the table and return it
        return '`'.$this->table.'`'.'.'.'`'.$key .'`'. ' ' . $value[0]. ' ?';

    } // whereElementStandard ()

    private function whereElementIn ($key, $value) {

        // Set a counter to 1 so we know when to use a comma to seperate
        $x = 1;

        // Create a blank $fields variable
        $fields = '';

        // Set the second part of the $value array (the value) to $valueArray for future use
        $valueArray = $value[1];

        // Loop through $valueArray
        foreach($valueArray as $field => $newbindValue) {

            // If we are beyond the first first field, add a comma before each subsequent one and add it $fields
            $fields .= ($x > 1) ? ', ' : '';

            // Add a comma for each value in the IN statement
            $fields .= '?';

            // Add the value to $this->_bindArray[]
            $this->_bindArray[] .= $newbindValue;

            // Increment the counter
            $x++;
        } // foreach

        // Append the first part of the $value array (the operator) to the table, place the $fields between the brackets and return it
        return '`'.$this->table.'`'.'.'.'`'.$key .'`' . ' ' . $value[0]. ' ('.$fields.')';

    } // whereElementIn ()

    private function whereElementBetween ($key, $value) {

        // Set the second part of the $value array (the value) to $valueArray for future use
        $valueArray = $value[1];

        // Loop through $valueArray
        foreach($valueArray as $field => $newbindValue) {

            // Add the value to $this->_bindArray[]
            $this->_bindArray[] .= $newbindValue; 

        }

        // Append the first part of the $value array (the operator) to the table, followit with ' ? AND ?' and return it
        return '`'.$this->table.'`'.'.'.'`'.$key .'`' . ' ' . $value[0].  ' ? AND ?';

    } // whereElementBetween ()

    private function whereElementNull ($key, $value) {
        
        // It does not appear possible to bind IS NULL and IS NOT NULL so add them as they are
        return '`'.$this->table.'`'.'.'.'`'.$key .'`'. ' IS ' .$value[0];
    } // whereElementStandard ()



    /** Execution

        runQuery()
        execute()
        Executes $this->_query and returns the count and results

        runQuery() prepares the sql and binds the values
        execute() runs the query and returns the count and results
    */
    private function runQuery () {

        // Prepare the constructed SQL statement
        if ($this->_query = $this->_pdo->prepare($this->_sql)) {

            // Set a counter to one to use when binding
            $x = 1;
                   
            // Loop through the binding array
            foreach($this->_bindArray as $param) {
                   
                // Run the bindValue function to match the correct value to the correct placing
                $this->_query->bindValue($x, $param);

                // Increment the counter
                $x++;
                
            }
            
            // Run the execute function
            $this->execute();
        }
    }
        
    private function execute () {

        // Check if the query has executed succesfully
        if($this->_query->execute()) {

            // It has been succesful so add the output to $this->results and set it as an object
            $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);

            // Set the affected row count to $this->_count
            $this->_count = $this->_query->rowCount();

        } else {

            // The query failed so set $this->_error to true. MORE WORK NEEDED HERE
            $this->_error = true;

        }
    }

    /** Return results

        getRows()

        By default it will fetch all results.
        To return only a single row set a $length when calling it. This can
        be a name of your choice such as 'one' or 'single'
       
    */
    public function getRows ($length='all') {

        // We return multiple results unless $length equals anything other than 'all'
        if($length!=='all') {

            // $length does not equal 'all' so return just the first record [0]
            return   $this->_results[0];

        } else {

            // $length does equal 'all' so return all records
            return   $this->_results;
        }


    } // getRows()



    // Functions for quick public access

    
    // ==================================================================
    //  Get the id of the last inserted row
    public function insertId () {

        return $this->_lastInsertId;
    } // insertId ()

    // ==================================================================
    //  Return a count of the query

    public function count () {

        return $this->_count;
    } // count ()

    // ==================================================================
    //  Return any errors
    public function error () {

        return $this->_error;
    } // error ()


    // Only used for debugging
    public function sql () {

        return $this->_sql;
    } // sql ()

    public function bindValue () {
       
        return $this->_bindArray;
    } // bindValue ()


}