<?php
/**
* $Id: config.php 64 2009-06-01 10:23:25Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
class BrowserConfig {
	function getConfig( &$vars ){
		$vars['file_browser_callback'] = "function(name, url, type, win){tinyMCE.activeEditor.plugins.browser.browse(name, url, type, win);}";
	}
}
?>