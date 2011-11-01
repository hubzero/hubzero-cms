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

class modGroups
{
	private $_attributes = array();

	//-----------

	public function __construct($params, $module) 
	{
		$this->params = $params;
		$this->module = $module;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_attributes[$property] = $value;
	}
	
	//-----------
	
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

	//-----------

	public function display()
	{
		$this->database = JFactory::getDBO();

		$type = $this->params->get('type', '1');
		
		switch ($type)
		{
			case '0': $this->type = 'system'; break;
			case '1': $this->type = 'hub'; break;
			case '2': $this->type = 'project'; break;
			case '3': $this->type = 'partner'; break;
		}

		// Privacy
		$this->database->setQuery("SELECT count(*) FROM #__xgroups WHERE published=1 AND privacy=0 AND type='$type'");
		$this->public = $this->database->loadResult();
		
		$this->database->setQuery("SELECT count(*) FROM #__xgroups WHERE published=1 AND privacy=1 AND type='$type'");
		$this->protected = $this->database->loadResult();
		
		$this->database->setQuery("SELECT count(*) FROM #__xgroups WHERE published=1 AND privacy=4 AND type='$type'");
		$this->private = $this->database->loadResult();
		
		// Join policy
		$this->database->setQuery("SELECT count(*) FROM #__xgroups WHERE join_policy=3 AND type='$type'");
		$this->closed = $this->database->loadResult();
		
		$this->database->setQuery("SELECT count(*) FROM #__xgroups WHERE join_policy=2 AND type='$type'");
		$this->invite = $this->database->loadResult();
		
		$this->database->setQuery("SELECT count(*) FROM #__xgroups WHERE join_policy=1 AND type='$type'");
		$this->restricted = $this->database->loadResult();
		
		$this->database->setQuery("SELECT count(*) FROM #__xgroups WHERE join_policy=0 AND type='$type'");
		$this->open = $this->database->loadResult();
		
		// Approved
		$this->database->setQuery("SELECT count(*) FROM #__xgroups WHERE published=1 AND type='$type'");
		$this->approved = $this->database->loadResult();
		
		$this->database->setQuery("SELECT count(*) FROM #__xgroups WHERE published=0 AND type='$type'");
		$this->pending = $this->database->loadResult();
		
		// Last 24 hours
		$lastDay = date('Y-m-d', (mktime() - 24*3600)) . ' 00:00:00';
		
		$this->database->setQuery("SELECT count(*) FROM #__xgroups WHERE created >= '$lastDay' AND type='$type'");
		$this->pastDay = $this->database->loadResult();
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet('/administrator/modules/' . $this->module->module . '/' . $this->module->module . '.css');
		
		// Get the view
		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
