<?php
/**
* $Id: config.php 72 2009-06-03 18:13:26Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
class AdvcodeConfig 
{
	function getConfig(&$vars) {
		$document 	=& JFactory::getDocument();
		$jce 		=& JContentEditor::getInstance();
		
		$toggle 	= $jce->getSharedParam('advcode', 'toggle', '1');
		$state 		= $jce->getSharedParam('advcode', 'editor_state', '1');
		$text		= htmlspecialchars($jce->getSharedParam('advcode', 'toggle_text', '[show/hide]'));

		$document->addScript(JURI::root(true) . '/plugins/editors/jce/tiny_mce/plugins/advcode/js/toggle.js?version='. $jce->getVersion());
		//$document->addStyleSheet(JURI::root(true) . '/plugins/editors/jce/tiny_mce/plugins/advcode/css/content.css?version='. $jce->getVersion());
		$vars['onpageload'] = "function(){AdvCode.init('". $text ."', ".$state .", ". $toggle .");}";
	}
}
?>