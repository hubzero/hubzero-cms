<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Hubzero_Module_Helper Class
 *    Helper Class to render and display modules as needed.  
 **/

class Hubzero_Module_Helper
{
	public static function displayModules($position, $style=-2)
	{
		echo Hubzero_Module_Helper::renderModules($position, $style);
	}

	public static function displayModule($name, $style=-1)
	{
		echo Hubzero_Module_Helper::renderModule($name,$style);
	}

	public static function renderModule($name, $style=-1)
	{
		$module = JModuleHelper::getModule($name);
		$params	= array('style'=>$style);
		$contents = JModuleHelper::renderModule($module, $params);

		return($contents);
	}

	public static function renderModules( $position, $style=-2 )
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
		foreach (JModuleHelper::getModules($position) as $mod)
		{
			if ($mod->showtitle != 0) {
				$contents .= "<h3>" . $mod->title . "</h3>";
			}
			$contents .= $renderer->render($mod,$params);
		}

		return $contents;
	}
}

