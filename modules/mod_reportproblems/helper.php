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
 * Module class for displaying a report problems form
 */
class modReportProblems
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
	 * Check if a property is set
	 * 
	 * @param      string $property Property to check
	 * @return     boolean True if set
	 */
	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
	}

	/**
	 * Display module content
	 * 
	 * @return     void
	 */
	public function display()
	{
		$this->juser = JFactory::getUser();

		$this->verified = 0;
		if (!$this->juser->get('guest')) 
		{
			ximport('Hubzero_User_Profile');
			$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));
			if ($profile->get('emailConfirmed') == 1) 
			{
				$this->verified = 1;
			}
		}

		$this->referrer = JRequest::getVar('REQUEST_URI','','server');
		$this->referrer = str_replace('&amp;', '&', $this->referrer);
		$this->referrer = str_replace('&', '&amp;', $this->referrer);

		ximport('Hubzero_Browser');
		$browser = new Hubzero_Browser();

		$this->os = $browser->getOs();
		$this->os_version = $browser->getOsVersion();
		$this->browser = $browser->getBrowser();
		$this->browser_ver = $browser->getBrowserVersion();

		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStylesheet($this->module->module);
		Hubzero_Document::addModuleScript($this->module->module);

		$this->supportParams = JComponentHelper::getParams('com_support');

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
