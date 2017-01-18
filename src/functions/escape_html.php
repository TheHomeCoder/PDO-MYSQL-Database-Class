<?php

/*------------------------------------------------------------------------------
** File:        /src/functions/escape_html.php
** Description: Sanitizes html before passing to the browser 
** Author:      Steve Ball
**
** Ref : http://php.net/manual/en/function.htmlentities.php
**------------------------------------------------------------------------------ */


function escape($string) {

    return htmlentities($string, ENT_QUOTES, 'ISO-8859-15');
}