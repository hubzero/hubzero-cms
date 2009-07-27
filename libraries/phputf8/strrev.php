<?php
/**
* @version $Id: strrev.php 7692 2007-06-08 20:41:29Z tcp $
* @package utf8
* @subpackage strings
*/

//---------------------------------------------------------------
/**
* UTF-8 aware alternative to strrev
* Reverse a string
* @param string UTF-8 encoded
* @return string characters in string reverses
* @see http://www.php.net/strrev
* @package utf8
* @subpackage strings
*/
function utf8_strrev($str){
    preg_match_all('/./us', $str, $ar);
    return join('',array_reverse($ar[0]));
}

