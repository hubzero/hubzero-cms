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
class PreviewConfig 
{
	function getConfig(&$vars)
	{
		$jce =& JContentEditor::getInstance();
		$vars['plugin_preview_width'] 	= $jce->getEditorParam('editor_preview_width', '750', '550');
        $vars['plugin_preview_height'] 	= $jce->getEditorParam('editor_preview_height', '550', '600');
	}
}
?>