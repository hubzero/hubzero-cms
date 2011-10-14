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

class modPollTitle 
{
	private $_attributes = array();

	//-----------

	public function __construct($params, $module) 
	{
		$this->params = $params;
		$this->module = $module;
	}

	//-----------


	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_attributes[$property] = $value;
	}

	//-----------


	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->_attributes[$property])) 
		{
			return $this->_attributes[$property];
		}
	}
	
	//-----------

	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	{
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_xpoll' . DS . 'tables' . DS . 'poll.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_xpoll' . DS . 'tables' . DS . 'data.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_xpoll' . DS . 'tables' . DS . 'date.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_xpoll' . DS . 'tables' . DS . 'menu.php');

		$this->database = JFactory::getDBO();


		// Load the latest poll
		$this->poll = new XPollPoll($this->database);
		$this->poll->getLatestPoll();
		
		require(JModuleHelper::getLayoutPath($this->module->module));
	}
	
	//-----------
	
	public function display()
	{
		$juser =& JFactory::getUser();
		
		if (!$juser->get('guest') && intval($this->params->get('cache', 0))) 
		{
			$cache =& JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . date('Y-m-d H:i:s', time()) . ' -->';
			return;
		}
		
		$this->run();
	}
}
