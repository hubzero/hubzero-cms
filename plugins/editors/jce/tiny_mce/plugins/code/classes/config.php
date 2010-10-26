<?php
/**
* @version		$Id: config.php 48 2009-05-27 10:46:36Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
class CodeConfig 
{
	function getConfig(&$vars)
	{
		// Get JContentEditor instance
		$jce 	=& JContentEditor::getInstance();
		$params = $jce->getPluginParams('code');
		
		if(!in_array('code', $vars['plugins'])) {
			$vars['plugins'][] = 'code';
		}
		
		$vars['code_php'] 			= $jce->getParam($params, 'code_allow_php', 0);
		$vars['code_javascript'] 	= $jce->getParam($params, 'code_allow_javascript', 0);
		$vars['code_css'] 			= $jce->getParam($params, 'code_allow_css', 0);
		
		$vars['code_cdata'] 		= $jce->getParam($params, 'code_cdata', 0);
		
		// Invalid Elements
		if ($vars['code_javascript'] == 1) {
			$jce->removeKeys($vars['invalid_elements'], 'script');
		}
		if ($vars['code_css'] == 1) {
			$jce->removeKeys($vars['invalid_elements'], 'style');
		}
	}
}
?>