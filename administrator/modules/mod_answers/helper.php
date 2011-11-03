<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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

class modAnswers
{
	private $_attributes = array();

	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	public function __set($property, $value)
	{
		$this->_attributes[$property] = $value;
	}

	public function __get($property)
	{
		if (isset($this->_attributes[$property]))
		{
			return $this->_attributes[$property];
		}
	}

	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
	}

	public function display()
	{
		$this->database = JFactory::getDBO();

		// Open
		$this->database->setQuery("SELECT count(*) FROM #__answers_questions WHERE state=1");
		$this->closed = $this->database->loadResult();

		// Closed
		$this->database->setQuery("SELECT count(*) FROM #__answers_questions WHERE state=0");
		$this->open = $this->database->loadResult();

		// Last 24 hours
		$lastDay = date('Y-m-d', (mktime() - 24*3600)) . ' 00:00:00';

		$this->database->setQuery("SELECT count(*) FROM #__answers_questions WHERE created >= '$lastDay'");
		$this->pastDay = $this->database->loadResult();

		if ($this->params->get('showMine', 0))
		{
			// My Open
			$juser =& JFactory::getUser();
			$this->username = $juser->get('username');

			$this->database->setQuery("SELECT count(*) FROM #__answers_questions WHERE state=1 AND created_by=" . $juser->get('id'));
			$this->myclosed = $this->database->loadResult();

			// My Closed
			$this->database->setQuery("SELECT count(*) FROM #__answers_questions WHERE state=0 AND created_by=" . $juser->get('id'));
			$this->myopen = $this->database->loadResult();
		}

		$document =& JFactory::getDocument();
		$document->addStyleSheet('/administrator/modules/' . $this->module->module . '/' . $this->module->module . '.css');

		// Get the view
		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
