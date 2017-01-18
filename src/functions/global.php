<?php

/*------------------------------------------------------------------------------
** File:        /src/functions/global.php
** Description: Global functions file 
** Author:      Steve Ball
**------------------------------------------------------------------------------ */


// =============================================================
//  Autoloads class files on request

include 'autoload_class.php';

// =============================================================
//  Provides function to sanitize html
include 'escape_html.php';



// =============================================================
//  Function to show a formatted output  of the generated SQL 
//  and bindings from a query that has been passed in. 
//  Useful for debugging purposes
function showData ($qry) {;
    echo '<h4>Generated SQL</h4>';
    echo '<pre>';
    
    echo $qry->sql();
    echo '</pre>';
    if($qry->bindValue ()) {
        echo '<h4>Bindings</h4>';
        echo '<pre>';
        print_r($qry->bindValue ());
        echo '</pre>';
    }
  
}

