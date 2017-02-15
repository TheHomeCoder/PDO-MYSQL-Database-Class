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
        $this->_bindArray = array();
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
        $this->_sql = $action . ' ';



        // Add all of the parts of the SQL statement to $this->_sql 
        $this->_sql .= $this->show_fields;
        $this->_sql .= $this->show_tables;
        $this->_sql .= $this->show_where;



        if ($action == 'SELECT') {
            $this->orderClause ($conditions);
            $this->limitClause ($conditions);
            $this->runQuery ();
        } 


        // Continue the query based on what method we are using
        if ($action == 'INSERT') {
            if(array_key_exists("FIELDS",$conditions)){
                 
                $keys = array_keys($conditions['FIELDS']);
                $values = '';

                $x = 1;
                foreach($conditions['FIELDS'] as $field) {

                    $values .= ($x > 1) ? ', ' : '';
                    $values .= '?';
                    $x++;
                }
                
                $this->_sql .= "(`" . implode('`,`', $keys) . "`) VALUES ({$values})";

                $this->error = false;

                $this->_bindArray = $conditions['FIELDS'];

                $this->runQuery ();

                $this->_lastInsertId = $this->_pdo->lastInsertId();
            }
        } 
        
        if ($action == 'DELETE') {
            $this->whereClause ($conditions);
            $this->runQuery ();
        } 

        if ($action == 'UPDATE') {
            if(array_key_exists("FIELDS",$conditions)){
                 
                $keys = array_keys($conditions['FIELDS']);
                $values = '';

                $x = 1;
                foreach($conditions['FIELDS'] as $key => $value) {
                    $values .= ($x > 1) ? ', ' : '';
                    $values .= $key . '=?';
                    $x++;
                    $this->_bindArray[] .= $value;
                }

                $this->_sql .= 'SET ' . $values;

                $this->whereClause ($conditions);
                $this->runQuery ();
            }
        } 


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
        
        
        // Loop through all the tables where the $key will be the table name and the $value will be the 
        // 'fields', 'where' and 'joins' arrays for the table.
        foreach ($tables as $key => $value) {

      
            // Set the table to $this->table as we will be using this in several nested functions
            $this->table = ($value['alias']) ? $value['alias'] : $key;

            // Create the required fields part of the SQL statement and add it to $this->show_fields
            $this->show_fields = $this->buildFields ($value['fields']);

            // Create the tables and joooins part of the SQL statement and add it to $this->show_fields
            $this->show_tables = $this->buildTables ($tables, $action);

            // Call the buildWhere () function to build up the $this->show_where variable
            $this->buildWhere ($value['where']);

        } // End foreach

    } // buildQuery ()


    /**  Set the action part of the SQL statement
     *
     *  Sets the correct action (FROM, SET etc) for the table in the query. 
     *  
     *  Creates the $this->show_table_action variable to be used just before $this->show_tables
     *
     *  $action         The type of query (SELECT, INSERT, UPDATE, DELETE)
     *
    **/
    private function tableAction ($action) {

        // Simply switch the $action variable for the correct type of action
        switch ($action) {
            case 'SELECT':
                $data = ' FROM';
                break;
            
            case 'INSERT':
                $data = ' INTO';
                break;
            
            case 'UPDATE':
                $data = '';
                break;
            
            case 'DELETE':
                $data = ' FROM';
                break;
            
            default:
                $data = ' FROM';
                break;
        } // End switch statement

        // Create the variable from the data return
        return $data;

   } // tableAction ()


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
    private function buildFields ($array) {

        // Only do anything if the array contains anything
        if($array){
            
            // Loop through the array where $key is the column name and $value are the 
            // arrays for that column such as 'alias' and 'count'
            foreach ($array as $key => $value) {

               // print_r($value);

                $this->alias = (isset($value['alias'])) ? ' ' . $value['alias'] : '';

        
               // $this->table = ($value['alias']) ? $value['alias'] : $key;

                // Create the `table`.`column` pair and add it to the $this->fields_output array
                $this->fields_output[] .= '`'.$this->table.'`'.'.'.'`'.$key.'`' . $this->alias;
                   
            } // End foreach

        } else {
            
            // The array contains no data so add `table`* to the $this->fields_output array
            $this->fields_output[] .= '`'.$this->table.'`'.'.*';
                
        } // End if $array

        // If there is anything in the $this->fields_output array, implode it, otherwise just return ' * ' 
        return ($this->fields_output) ? implode($this->fields_output, ', ') : ' * ';


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

        // Set the table action (FROM, SET etc)
        $out =  $this->tableAction ($action);

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

            $this->alias = ($value['alias']) ? $value['alias'] . ' ' : '';
            $this->join_name = ($value['alias']) ? $value['alias']: $key;

            if(isset($value['join'])) {
                $joinType = $value['join']['join_type'] . ' JOIN';
                $joinOn = 'ON `' . $this->join_name.'`.`'.$value['join']['local_key'] . '` = `' . $value['join']['foreign_table'].'`.`'.$value['join']['foreign_key'] .'`';
            } // if join
           
            // Wrap the table name ($key) with the join parts. The join parts will be empty strings if no join was set
            $out .= $joinType.' `' . $key . '` ' .$this->alias.$joinOn;

        } // foreach

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
        Used by =, <, >, <=, =>, LIKE
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
        
        $this->_bindArray[] .= $value[1];

        return '`'.$this->table.'`'.'.'.'`'.$key .'`'. ' ' . $value[0]. ' ?';
    } // whereElementStandard ()

    private function whereElementIn ($key, $value) {
        $x = 1;
        $fields = '';
        $valueArray = $value[1];
        foreach($valueArray as $field => $newbindValue) {
            $fields .= ($x > 1) ? ', ' : '';
            $fields .= '?';
            $this->_bindArray[] .= $newbindValue;
            $x++;
        }

     return '`'.$this->table.'`'.'.'.'`'.$key .'`' . ' ' . $value[0]. ' ('.$fields.')';
    } // whereElementIn ()

    private function whereElementBetween ($key, $value) {
        $x = 1;
        $fields = '';
        $valueArray = $value[1];

        foreach($valueArray as $field => $newbindValue) {
            $this->_bindArray[] .= $newbindValue; 
        }

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
        if ($this->_query = $this->_pdo->prepare($this->_sql)) {
            $x = 1;
                   
            foreach($this->_bindArray as $param) {
                   
                $this->_query->bindValue($x, $param);

                $x++;
                
            }
            
            $this->execute();
        }
    }
        
    private function execute () {
        if($this->_query->execute()) {
            $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
            $this->_count = $this->_query->rowCount();
        } else {
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

        if($length!=='all') {
            $results =  $this->_results[0];
        } else {
            $results =  $this->_results;
        }

        return $results;
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