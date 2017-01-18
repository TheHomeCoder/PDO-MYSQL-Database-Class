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


    /** dbConnect()

    *   Create a connection to the database
    */
    public static function dbConnect() {
        if(!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    /** fullQuery()

        Process a full query for complex ones that contain JOINed tables or OR's in the WHERE clause.
        Look at creating a better function at a later date.

        $sql the sql statenebt to run the query on
    */
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
        This is the main function that all of the others are passed into for processing

        $method is the type of query (SELECT, INSERT, UPDATE, DELETE)
        $table is the table to run the query on
        $conditions is an array of any other parameters for the query (WHERE, ORDER BY, LIMIT and START)
    */
    private function crud ($method, $table, $conditions = array()) {
        $this->_bindArray = array();
        // Start building the query
        $this->_sql = $method . ' ';

        // Continue building the query depending on the query type
        $this->_sql .= $this->tableAction ($method);

        // Now add the table itself to the query
        $this->_sql .= '`'.$table.'`';

        // Continue the query based on what method we are using
        if ($method == 'INSERT') {
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
        
        if ($method == 'DELETE') {
            $this->whereClause ($conditions);
            $this->runQuery ();
        } 

        if ($method == 'SELECT') {
            $this->whereClause ($conditions);
            $this->orderClause ($conditions);
            $this->limitClause ($conditions);
            $this->runQuery ();
        } 

        if ($method == 'UPDATE') {
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
    }


    /** QUERY BUILDING FUNCTIONS

        whereClause()
        orderClause()
        limitClause()

        Simply handles the WHERE, ORDER BY and LIMIT parts of the query
    */
    private function whereClause ($conditions) {
        if(array_key_exists("WHERE",$conditions)){
            // Continue the query with a 'WHERE'
            $this->_sql .= ' WHERE ';
            $i = 0;

            // Loop through the array
            foreach($conditions['WHERE'] as $key => $value){
                // If we have more than one condition, prefix each one (after the first) with 'AND'
                $preSql = ($i > 0) ? ' AND ' : '';
                // Depending on the operator, we need to structure the WHERE clause slightly differently
                switch($value[0]) {
                    case '>':
                    case '<':
                    case '=':
                    case 'LIKE':
                        $this->whereElementStandard ($preSql, $key, $value);
                        break;

                    case 'IN':
                        $this->whereElementIn ($preSql, $key, $value); 
                        break;

                    case 'BETWEEN':
                        $this->whereElementBetween ($preSql, $key, $value); 
                        break;

                    default:
                        $this->whereElementStandard ($preSql, $key, $value);
                        break;
                } // End switch

                // Increment the counter
                $i++;
                    
            } // End foreach
        } // End array_key_exists
    } // whereClause ()

    private function orderClause ($conditions) {
        if(array_key_exists("ORDER",$conditions)){
            // Continue the query with an 'ORDER BY'
            $this->_sql .= ' ORDER BY '.$conditions['ORDER'];
        }
    } // orderClause ()

    private function limitClause ($conditions) {
        if (array_key_exists("LIMIT",$conditions)) {
            // Continue the query with a 'LIMIT'
            $this->_sql .= ' LIMIT ';
            $this->_sql .= (array_key_exists("START",$conditions)) ? $conditions['START'].',' : '';
            $this->_sql .= $conditions['LIMIT'];

        }
    } // limitClause ()

    /** tableAction()
        Sets the correct action for the table in the query

        $method is the type of query (SELECT, INSERT, UPDATE, DELETE)
    */
    private function tableAction ($method) {
        // Note : 'SELECT' currrently selects all (*) but will look at selecting columns at a later date
            switch ($method) {
                case 'SELECT':
                    $data = '* FROM ';
                    break;
                
                case 'INSERT':
                    $data = 'INTO ';
                    break;
                
                case 'UPDATE':
                    $data = '';
                    break;
                
                case 'DELETE':
                    $data = 'FROM ';
                    break;
                
                default:
                    $data = '* FROM ';
                    break;
            }

            return $data;
    }


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
    */
    private function whereElementStandard ($preSql, $key, $value) {
        $this->_sql .= $preSql . '`' . $key . '` ' . $value[0]. ' ?';
        $this->_bindArray[] .= $value[1];
    }

    private function whereElementIn ($preSql, $key, $value) {
        $x = 1;
        $fields = '';
        $valueArray = $value[1];
        foreach($valueArray as $field => $newbindValue) {
            $fields .= ($x > 1) ? ', ' : '';
            $fields .= '?';
            $this->_bindArray[] .= $newbindValue;
            $x++;
        }

        $this->_sql .= $preSql . '`' . $key . '` ' . $value[0]. ' ('.$fields.')';
    }

    private function whereElementBetween ($preSql, $key, $value) {
        $x = 1;
        $fields = '';
        $valueArray = $value[1];
        foreach($valueArray as $field => $newbindValue) {
            $this->_bindArray[] .= $newbindValue;
        }

        $this->_sql .= $preSql . '`' . $key . '` ' . $value[0]. ' ? AND ?';

    }



    /** CORE FUNCTIONS

        select()
        delete()
        insert()
        update()

        Runs a query with the desired method

        $table is the table to run the query on
        $conditions is an array of any other parameters for the query (WHERE, ORDER BY, LIMIT and START)
    */
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
    }



    // Functions for quick public access

    
    // ==================================================================
    //  Get the id of the last inserted row
    public function insertId () {

        return $this->_lastInsertId;
    }

    // ==================================================================
    //  Return a count of the query

    public function count () {

        return $this->_count;
    }

    // ==================================================================
    //  Return any errors
    public function error () {

        return $this->_error;
    }



    // Only used for debugging
    public function sql () {

        return $this->_sql;
    }

    public function bindValue () {

        return $this->_bindArray;
    }



}