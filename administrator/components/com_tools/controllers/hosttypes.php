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
	public function editTask()
	{
		JRequest::setVar('hidemainmenu', 1);
		
		// Incoming
		$item = JRequest::getVar('item', '', 'get');
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		$mwdb->setQuery("SELECT * FROM hosttype WHERE name='$item'");
		$this->view->row = $mwdb->loadObject();
		
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
		
		// Incoming
		$name = JRequest::getVar('name', '', 'post');
		if (!$name) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				Jtext::_('You must specify a valid name.'),
				'error'
			);
			return;
		} 
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		$id = JRequest::getVar('id', '', 'post');
		$description = JRequest::getVar('description', '', 'post');
		
		if ($id) 
		{
			$mwdb->setQuery("UPDATE hosttype SET name=" . $mwdb->Quote($name) . ", description=" . $mwdb->Quote($description) . " WHERE name=" . $mwdb->Quote($id).";");
			if (!$mwdb->query())
			{
				$this->setError($mwdb->getError());
			}
		} 
		else 
		{
			$mwdb->setQuery("SELECT * FROM hosttype ORDER BY VALUE");
			$rows = $mwdb->loadObjectList();

			$value = 1;
			for ($i=0; $i<count($rows); $i++)
			{
				$row = $rows[$i];
				if ($value == $row->value) 
				{
					$value = $value * 2;
				}
			}
			for ($i=0; $i<count($rows); $i++)
			{
				$row = $rows[$i];
				if ($row->name == $name) 
				{
					$this->setError(JText::_('"' . $name . '" already exists in the table.'));
					$name = '';
				}
			}
			if ($name) 
			{
				$mwdb->setQuery("INSERT INTO hosttype (name,value,description) VALUES(" . $mwdb->Quote($name) . "," . $mwdb->Quote($value) . "," . $mwdb->Quote($description) . ");");
				if (!$mwdb->query())
				{
					$this->setError($mwdb->getError());
				}
			}
		}
		
		if ($this->getError())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$this->getError(),
				'error'
			);
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
	public function deleteTask()
	{
		$name = JRequest::getVar('name', '', 'get');
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		$mwdb->setQuery("DELETE FROM hosttype WHERE name=" . $mwdb->Quote($item));
		if (!$mwdb->query()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$mwdb->getErrorMsg(),
				'error'
			);
			return;
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
