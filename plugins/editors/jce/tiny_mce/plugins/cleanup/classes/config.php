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
class CleanupConfig 
{
	function getConfig(&$vars)
	{
		$jce =& JContentEditor::getInstance();
		
		if(!in_array('cleanup', $vars['plugins'])) {
			$vars['plugins'][] = 'cleanup';
		}
		
		$vars['cleanup_pluginmode'] = $jce->getEditorParam('cleanup_pluginmode', 0, 0);
		
		$vars['verify_html'] 		= $jce->getEditorParam('editor_verify_html', 0, 1);
        $vars['event_elements'] 	= $jce->getEditorParam('editor_event_elements', 'a,img', 'a,img');
		
		// Tables & Lists
        $vars['table_inline_editing'] 				= 1;
        $vars['fix_list_elements'] 					= 1;
        $vars['fix_table_elements'] 				= 1;
	}
}
?>