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
class PasteConfig 
{
	function getConfig(&$vars)
	{
		$jce 	=& JContentEditor::getInstance();
		$params = $jce->getPluginParams('paste');
		
		$vars['paste_dialog_width']				= $jce->getParam($params, 'paste_dialog_width', 450, 450);
		$vars['paste_dialog_height']			= $jce->getParam($params, 'paste_dialog_height', 400, 400);
		$vars['paste_keep_linebreaks']			= $jce->getParam($params, 'paste_keep_linebreaks', 1, 1);
		$vars['paste_auto_cleanup_on_paste']	= $jce->getParam($params, 'paste_auto_cleanup_on_paste', 1, 1);
		$vars['paste_use_dialog']				= $jce->getParam($params, 'paste_use_dialog', 0, 0);
		$vars['paste_remove_styles']			= $jce->getParam($params, 'paste_remove_styles', 0, 0);
		$vars['paste_strip_class_attributes']	= $jce->getParam($params, 'paste_strip_class_attributes', 'all', 'all');
		$vars['paste_strip_class_attributes']	= $jce->getParam($params, 'paste_retain_style_properties', '', '');
		$vars['paste_remove_spans']				= $jce->getParam($params, 'paste_remove_spans', 0, 0);
		$vars['paste_remove_styles_if_webkit']	= $jce->getParam($params, 'paste_remove_styles_if_webkit', 0, 0);
		$vars['paste_text']						= $jce->getParam($params, 'paste_text', 1, 1);
		$vars['paste_html']						= $jce->getParam($params, 'paste_html', 1, 1);
	}
}
?>