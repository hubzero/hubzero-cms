<?php
/**
* This is the dynamic loader for the library. It checks whether you have
* the mbstring extension available and includes relevant files
* on that basis, falling back to the native (as in written in PHP) version
* if mbstring is unavailabe.
*
* It's probably easiest to use this, if you don't want to understand
* the dependencies involved, in conjunction with PHP versions etc. At
* the same time, you might get better performance by managing loading
* yourself. The smartest way to do this, bearing in mind performance,
* is probably to "load on demand" - i.e. just before you use these
* functions in your code, load the version you need.
*
* It makes sure the the following functions are available;
* utf8_strlen, utf8_strpos, utf8_strrpos, utf8_substr,
* utf8_strtolower, utf8_strtoupper
* Other functions in the ./native directory depend on these
* six functions being available
* @package utf8
*/

/**
* Put the current directory in this constant
*/
if ( !defined('UTF8') ) {
    define('UTF8',dirname(__FILE__));
}

/**
* Also need to check we have the correct internal mbstring
* encoding
*/
if ( extension_loaded('mbstring')) {
    mb_internal_encoding('UTF-8');
}

/**
* Check whether PCRE has been compiled with UTF-8 support
*/
$UTF8_ar = array();
if ( preg_match('/^.{1}$/u',"ñ",$UTF8_ar) != 1 ) {
    trigger_error('PCRE is not compiled with UTF-8 support',E_USER_ERROR);
}
unset($UTF8_ar);


/**
* Load the smartest implementations of utf8_strpos, utf8_strrpos
* and utf8_substr
*/
if ( !defined('UTF8_CORE') ) {
    if ( function_exists('mb_substr') ) {
        require_once UTF8 . '/mbstring/core.php';
    } else {
        require_once UTF8 . '/utils/unicode.php';
        require_once UTF8 . '/native/core.php';
    }
}

/**
* Load the native implementation of utf8_substr_replace
*/
require_once UTF8 . '/substr_replace.php';

/**
* You should now be able to use all the other utf_* string functions
*/
