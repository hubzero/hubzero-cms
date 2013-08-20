<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Events controller for configuration
 */
class EventsControllerConfigure extends Hubzero_Controller
{
	/**
	 * Determines task and attempts to execute it
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->config = new EventsConfigs($this->database);
		$this->config->load();

		parent::execute();
	}

	/**
	 * Show a form for editing configuration values
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->config = $this->config;

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save configuration values
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Get the configuration
		$config = JRequest::getVar('config', array(), 'post');
		foreach ($config as $n => $v)
		{
			$box = array(
				'param' => $n,
				'value' => $v
			);

			$row = new EventsConfig($this->database);
			if (!$row->bind($box)) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
			// Check content
			if (!$row->check()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
			// Store content
			if (!$row->store()) 
			{
				JError::raiseError(500, $row->getError() . ' ' . $row->param . ': ' . $row->value);
				return;
			}
		}

		// Get the custom fields
		$fields = JRequest::getVar('fields', array(), 'post');

		$box = array();
		$box['param'] = 'fields';
		$box['value'] = '';

		if (is_array($fields)) 
		{
			$txta = array();
			foreach ($fields as $val)
			{
				if ($val['title']) 
				{
					$k = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($val['title']));

					$t = str_replace('=', '-', $val['title']);
					$j = (isset($val['type']))     ? $val['type']     : 'text';
					$x = (isset($val['required'])) ? $val['required'] : '0';
					$z = (isset($val['show']))     ? $val['show']     : '0';
					$txta[] = $k . '=' . $t . '=' . $j . '=' . $x . '=' . $z;
				}
			}
			$field = implode("\n", $txta);
		}
		$box['value'] = $field;

		$row = new EventsConfig($this->database);
		if (!$row->bind($box)) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		// Check content
		if (!$row->check()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		// Store content
		if (!$row->store()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_EVENTS_CAL_LANG_CONFIG_SAVED')
		);
	}
}

