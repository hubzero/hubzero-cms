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

class modSupportTickets
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
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'ticket.php');

		$juser =& JFactory::getUser();

		$this->database = JFactory::getDBO();

		$jconfig =& JFactory::getConfig();
		$this->offset = $jconfig->getValue('config.offset');

		$type = JRequest::getVar('type', 'submitted');
		$this->type  = ($type == 'automatic') ? 1 : 0;

		$this->group = JRequest::getVar('group', '');

		$this->year  = JRequest::getInt('year', strftime("%Y", time()+($this->offset*60*60)));

		$st = new SupportTicket($this->database);

		$opened = array();
		$my = array();

		// Currently open tickets
		$opened['open'] = $st->getCountOfOpenTickets($this->type, false, $this->group);

		// Currently unassigned tickets
		$opened['unassigned'] = $st->getCountOfOpenTickets($this->type, true, $this->group);

		$filters = array();
		$filters['search'] = '';
		$filters['status'] = 'new';
		$filters['type'] = 0;
		$filters['owner'] = '';
		$filters['reportedby'] = '';
		$filters['severity'] = '';
		$filters['sort'] = 'created';
		$filters['sortdir'] = 'DESC';

		$opened['new'] = $st->getTicketsCount($filters, true);

		$this->opened = $opened;

		if ($this->params->get('showMine', 1))
		{
			$filters['status'] = 'open';
			$filters['reportedby'] = $juser->get('username');
			$my['open'] = $st->getTicketsCount($filters, true);

			$filters['reportedby'] = '';
			$filters['status'] = 'open';
			$filters['owner'] = $juser->get('username');
			$my['assigned'] = $st->getTicketsCount($filters, true);

			$this->my = $my;
		}

		// Get avgerage lifetime
		$this->lifetime = $st->getAverageLifeOfTicket($this->type, $this->year, $this->group);

		//ximport('Hubzero_Document');
		//Hubzero_Document::addModuleStyleSheet($this->module->module);

		$document =& JFactory::getDocument();
		$document->addStyleSheet('/administrator/modules/' . $this->module->module . '/' . $this->module->module . '.css');

		// Get the view
		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
