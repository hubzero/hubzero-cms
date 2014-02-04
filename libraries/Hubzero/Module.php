<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Base class for modules
 */
class Hubzero_Module extends \Hubzero\Module\Module
{
	/**
	 * Adds a linked stylesheet from a module to the page
	 *
	 * @param	string  $module     Module name
	 * @param	string  $stylesheet Stylesheet name (optional, uses module name if left blank)
	 * @param	string  $type       Mime encoding type
	 * @param	string  $media      Media type that this stylesheet applies to
	 * @param	string  $attribs    Attributes to add to the link
	 * @return  void
	 */
	public function addStyleSheet($stylesheet='', $type = 'text/css', $media = null, $attribs = array())
	{
		$this->css($stylesheet);
	}

	/**
	 * Adds a stylesheet declaration to the page
	 *
	 * @param   string  $content Style declarations
	 * @param   string  $type    Type of stylesheet (defaults to 'text/css')
	 * @return  void
	 */
	public function addStyleDeclaration($content, $type = 'text/css')
	{
		$this->css($content);
	}

	/**
	 * Adds a linked script to the page
	 *
	 * @param   string  $module  	URL to the linked script
	 * @param	string  $script  	Script name (optional, uses module name if left blank)
	 * @param   string  $type		Type of script. Defaults to 'text/javascript'
	 * @param   bool    $defer		Adds the defer attribute.
	 * @param   bool    $async		Adds the async attribute.
	 * @return  void
	 */
	public function addScript($script = '', $type = 'text/javascript', $defer = false, $async = false)
	{
		$this->js($script);
	}

	/**
	 * Adds a script to the page
	 *
	 * @param   string  $content Script
	 * @param   string  $type    Scripting mime (defaults to 'text/javascript')
	 * @return  void
	 */
	public function addScriptDeclaration($content, $type = 'text/javascript')
	{
		$this->js($content);
	}
}

