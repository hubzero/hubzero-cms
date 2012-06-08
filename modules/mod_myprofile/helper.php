<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying a user's profile information
 */
class modMyProfile extends JObject
{
	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Constructor
	 * 
	 * @param      object $params JParameter
	 * @param      object $module Database row
	 * @return     void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) 
		{
			return $this->attributes[$property];
		}
	}

	/**
	 * Display module content
	 * 
	 * @return     void
	 */
	public function display()
	{
		$config =& JComponentHelper::getParams( 'com_members' );

		$this->id = JFactory::getUser()->get('id');

		$profile = Hubzero_User_Profile::getInstance($this->id);

		if (!$profile->get('name')) 
		{
			$name  = $profile->get('givenName') . ' ';
			$name .= ($profile->get('middleName')) ? $profile->get('middleName') . ' ' : '';
			$name .= $profile->get('surname');
			$profile->set('name', $name);
		}

		// Get the member's picture (if it exist)
		if ($profile->get('picture')) 
		{
			ximport('Hubzero_View_Helper_Html');

			$dir = DS . trim($config->get('webpath', '/site/members'), DS) . Hubzero_View_Helper_Html::niceidformat($this->id);
			if (!file_exists(JPATH_ROOT . $dir . DS . $profile->get('picture'))) 
			{
				$profile->set('picture', '');
			} 
			else 
			{
				$profile->set('picture', $dir . DS . stripslashes($profile->get('picture')));
			}
		} 

		if (!$profile->get('picture')) 
		{
			$default = DS . trim($config->get('defaultpic', '/components/com_members/images/profile.gif'));
			if (!file_exists(JPATH_ROOT . $default)) 
			{
				$profile->set('picture', '');
			} 
			else 
			{
				$profile->set('picture', $default);
			}
		}

		$this->profile = $profile;

		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet($this->module->module);

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}

