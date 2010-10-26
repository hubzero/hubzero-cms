<?php
/**
* $Id: config.php 26 2009-05-25 10:21:53Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
class SpellcheckerConfig {
	function getConfig( &$vars ){
		$jce 	=& JContentEditor::getInstance();
		$params = $jce->getPluginParams( 'spellchecker' );
		
		$vars['spellchecker_languages'] = '+' . $jce->getParam( $params, 'spellchecker_languages', 'English=en', '' );
		$vars['spellchecker_engine'] 	= $jce->getParam( $params, 'spellchecker_engine', 'googlespell', 'googlespell' );
	}
}
?>