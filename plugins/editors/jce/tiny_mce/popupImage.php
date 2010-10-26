<?php
/**
* $Id: popupImage.php 26 2009-05-25 10:21:53Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
//Redirect to index2.php?option=com_jce

define( '_VALID_MOS', 1 );

$base = str_replace( 'mambots/editors/jce/jscripts/tiny_mce', '', str_replace( DIRECTORY_SEPARATOR, '/', dirname(__FILE__) ) );

include ( $base . "/configuration.php" );
include ( $base . "/includes/joomla.php" );

function getInput( $item, $def=null ){
	return htmlspecialchars( mosGetParam( $_REQUEST, $item, $def ) );
}

$mode = ( getInput( 'mode', 'basic' ) == 'basic' ) ? '0' : '1'; 
$title = getInput( 'title' );
$alt = getInput( 'alt' );

$img = getInput( 'img' );
$src = getInput( 'src' );

$img = ( $src ) ? $src : $img;
mosRedirect( "$mosConfig_live_site/index2.php?option=com_jce&task=popup&img=$img&mode=$mode&title=$title&alt=$alt" );

?>
