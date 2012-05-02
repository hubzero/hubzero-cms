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
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'hosttype.php');

/**
 * Short description for 'ToolsController'
 * 
 * Long description (if any) ...
 */
class ToolsControllerHosttypes extends Hubzero_Controller
{
	/**
	 * Short description for 'type_display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();
		
		// Get filters
		$this->view->filters = array();
		$this->view->filters['usertype'] = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.usertype', 
			'usertype', 
			''
		));
		$this->view->filters['hosttype'] = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.hosttype', 
			'hosttype', 
			''
		));
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		// Form the query and show the table.
		$mwdb->setQuery("SELECT * FROM hosttype ORDER BY VALUE");
		$this->view->rows = $mwdb->loadObjectList();
		if ($this->view->rows)
		{
			foreach ($this->view->rows as $key => $row)
			{
				$this->view->rows[$key]->refs = $this->_refs($row->value);
			}
		}
		
		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		
		// Display results
		$this->view->display();
	}

	/**
	 * Edit a record
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a record
	 * 
	 * @return     void
	 */
	public function editTask($row = null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming
		$item = JRequest::getVar('item', '', 'get');

		$mwdb =& MwUtils::getMWDBO();

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else 
		{
			$this->view->row = new ToolHosttype($mwdb);
			$this->view->row->load($item);
		}

		if ($this->view->row->value > 0) 
		{
			$this->view->bit = log($this->view->row->value)/log(2);
		} 
		else 
		{
			$this->view->bit = '';
		}

		$this->view->refs = $this->_refs($this->view->row->value);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Display results
		$this->view->display();
	}
	
	/**
	 * Short description for 'hosttype_refs'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _refs($value)
	{
		$refs = 0;
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		$mwdb->setQuery("SELECT count(*) AS count FROM host WHERE provisions & " . $mwdb->Quote($value) . " != 0");
		$elts = $mwdb->loadObjectList();
		if ($elts)
		{
			$elt  = $elts[0];
			$refs = $elt->count;
		}

		return $refs;
	}
	
	/**
	 * Save changes to a record
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		//$id = JRequest::getVar('id', '', 'post');
		//$description = JRequest::getVar('description', '', 'post');
		$fields = JRequest::getVar('fields', '', 'post');
		
		$row = new ToolHosttype($mwdb);
		if (!$row->bind($fields)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$insert = false;
		if (!$fields['value']) 
		{
			$insert = true;

			$mwdb->setQuery("SELECT * FROM hosttype ORDER BY VALUE");
			$rows = $mwdb->loadObjectList();

			$value = 1;
			for ($i=0; $i<count($rows); $i++)
			{
				if ($value == $rows[$i]->value) 
				{
					$value = $value * 2;
				}
			}
			for ($i=0; $i<count($rows); $i++)
			{
				if ($row->name == $rows[$i]->name) 
				{
					$insert = false;
				}
			}

			$row->value = $value;
		}

		// Check content
		if (!$row->check()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store($insert)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			Jtext::_('Host type successfully saved.'),
			'message'
		);
	}

	/**
	 * Delete a hostname record
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		$mwdb =& MwUtils::getMWDBO();

		if (count($ids) > 0) 
		{
			$row = new ToolHosttype($mwdb);
			
			// Loop through each ID
			foreach ($ids as $id) 
			{
				if (!$row->delete($id)) 
				{
					JError::raiseError(500, $row->getError());
					return;
				}
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Host type successfully deleted.'),
			'message'
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
