<?php
/**
* @version		$Id: joomlalinks.php 46 2009-05-26 16:59:42Z happynoodleboy $
* @package      JCE Advlink
* @copyright    Copyright (C) 2008 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
// no direct access
defined( '_JCE_EXT' ) or die( 'Restricted access' );
// Core function	
function zoo()
{
	// Joomla! file and folder processing functions
	jimport('joomla.filesystem.folder');
	jimport('joomla.filesystem.file');
		
	// Base path for corelinks files
	$path = dirname(__FILE__) .DS. 'zoo';	
		
	// Get all files
	$files = JFolder::files($path, '\.(php)$');	
	
	$items = array();
	
	// For AdvLink link plugins
	if (isset($files)) {
		foreach ($files as $file) {
			$items[] = array(
				'name'		=> JFile::stripExt($file),
				'path' 		=> $path,
				'file' 		=> $file
			);
		}
	}
	return $items;
}	
?>