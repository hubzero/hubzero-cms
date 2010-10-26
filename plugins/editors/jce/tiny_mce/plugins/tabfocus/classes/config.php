<?php
/**
* @version		$Id: config.php 73 2009-06-04 09:38:23Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
class TabfocusConfig 
{
	function getConfig(&$vars) {
		$vars['tabfocus_elements'] = ":prev,:next";		
	}
}
?>