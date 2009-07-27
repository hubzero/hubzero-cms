<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * XModuleHelper Class
 *    Helper Class to render and display modules as needed.  
 **/

Class XModuleHelper
{
	function displayModules($position, $style=-2) 
	{
		echo XModuleHelper::renderModules($position, $style);
	}

	function displayModule($name, $style=-1)
	{
		echo XModuleHelper::renderModule($name,$style);
	}

	function renderModule($name, $style=-1) 
	{
		$module = JModuleHelper::getModule($name);
		$params	= array('style'=>$style);
		$contents = JModuleHelper::renderModule($module, $params);

		return($contents);
	}

	function renderModules( $position, $style=-2 )
	{
		if (!defined('_JEXEC')) {
			ob_start();
			mosLoadModules($position,$style);
			$result = ob_get_contents();
			ob_end_clean();
			return $result;
		}

		$document = &JFactory::getDocument();
		$renderer = $document->loadRenderer('module');
		$params	  = array('style'=>$style);
	
		$contents = '';
		foreach (JModuleHelper::getModules($position) as $mod)  {
			if ($mod->showtitle != 0) {
				$contents .= "<h3>" . $mod->title . "</h3>";
			}
			$contents .= $renderer->render($mod,$params);
		}
	
		return $contents;
	}
}

?>
