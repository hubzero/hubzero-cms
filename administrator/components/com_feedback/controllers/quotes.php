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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Feedback controller class for quotes
 */
class FeedbackControllerQuotes extends Hubzero_Controller
{

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->type = JRequest::getVar('type', '', 'post');

		if (!$this->type)
		{
			$this->type = JRequest::getVar('type', 'regular', 'get');
		}
		$this->type = ($this->type == 'regular') ? $this->type : 'selected';

		parent::execute();
	}

	/**
	 * Display a list of quotes
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		if (JRequest::getMethod() == 'POST')
		{
			// Check for request forgeries
			JRequest::checkToken() or jexit('Invalid Token');
		}

		$this->view->type = $this->type;

		// Get site configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['search'] = urldecode($app->getUserStateFromRequest(
			$this->_option . '.search',
			'search',
			''
		));

		// Get sorting variables
		$this->view->filters['sortby']     = trim($app->getUserStateFromRequest(
			$this->_option . '.sort', 
			'filter_order', 
			'date'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.sortdir', 
			'filter_order_Dir', 
			'DESC'
		));
		
		// Get paging variables
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);

		if ($this->type == 'regular')
		{
			$className = 'FeedbackQuotes';
		}
		else
		{
			$className = 'SelectedQuotes';
		}
		
		$obj = new $className($this->database);

		// Get a record count
		$this->view->total = $obj->getCount($this->view->filters);

		// Get records
		$this->view->rows = $obj->getResults($this->view->filters);

		// Initiate paging class
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

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
	 * Create a new entry
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit an entry
	 * 
	 * @return     void
	 */
	public function editTask()
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		if (JRequest::getMethod() == 'POST')
		{
			// Check for request forgeries
			JRequest::checkToken() or jexit('Invalid Token');
		}

		$this->view->type = $this->type;

		// Incoming ID
		$id = JRequest::getInt('id', 0);

		// Initiate database class and load info
		if ($this->type == 'regular')
		{
			$this->view->row = new FeedbackQuotes($this->database);
		}
		else
		{
			$this->view->row = new SelectedQuotes($this->database);
		}
		$this->view->row->load($id);

		$username = trim(JRequest::getVar('username', ''));
		if ($username)
		{
			ximport('Hubzero_User_Profile');

			$profile = new Hubzero_User_Profile();
			$profile->load($username);

			$this->view->row->fullname = $profile->get('name');
			$this->view->row->org      = $profile->get('organization');
			$this->view->row->userid   = $profile->get('uidNumber');
		}

		if (!$id)
		{
			$this->view->row->date = date('Y-m-d H:i:s');
		}

		if ($this->type == 'regular')
		{
			$this->view->row->notable_quotes = 0;
			$this->view->row->flash_rotation = 0;
		}

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
	 * Save an entry
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$replacequote   = JRequest::getInt('replacequote', 0);
		$notable_quotes = JRequest::getInt('notable_quotes', 0);
		$flash_rotation = JRequest::getInt('flash_rotation', 0);

		if ($replacequote)
		{
			// Replace original quote

			// Initiate class and bind posted items to database fields
			$row = new FeedbackQuotes($this->database);
			if (!$row->bind($_POST))
			{
				JError::raiseError(500, $row->getError());
				return;
			}

			// Code cleaner for xhtml transitional compliance
			$row->quote = str_replace('<br>', '<br />', $row->quote);

			$row->picture = basename($bits);

			// Check new content
			if (!$row->check())
			{
				JError::raiseError(500, $row->getError());
				return;
			}

			// Store new content
			if (!$row->store())
			{
				JError::raiseError(500, $row->getError());
				return;
			}

			$msg = JText::sprintf('FEEDBACK_QUOTE_SAVED',  $row->fullname);
		}

		if ($this->type == 'selected' || $notable_quotes || $flash_rotation)
		{
			// Initiate class and bind posted items to database fields
			$rowselected = new SelectedQuotes($this->database);
			if (!$rowselected->bind($_POST))
			{
				JError::raiseError(500, $rowselected->getError());
				return;
			}

			$rowselected->notable_quotes = $notable_quotes;
			$rowselected->flash_rotation = $flash_rotation;

			// Use new id if already exists under selected quotes
			if ($this->type == 'regular')
			{
				$rowselected->id = 0;
			}

			// Code cleaner for xhtml transitional compliance
			$rowselected->quote = str_replace('<br>', '<br />', $rowselected->quote);

			$rowselected->picture = basename($rowselected->picture);

			// Trim the text to create a short quote
			$rowselected->short_quote = ($rowselected->short_quote) ? $rowselected->short_quote : substr($rowselected->quote, 0, 270);
			if (strlen($rowselected->short_quote) >= 271)
			{
				$rowselected->short_quote .= '...';
			}

			// Trim the text to create a mini quote
			$rowselected->miniquote = ($rowselected->miniquote) ? $rowselected->miniquote : substr($rowselected->short_quote, 0, 150);
			if (strlen($rowselected->miniquote) >= 147)
			{
				$rowselected->miniquote .= '...';
			}

			// Store new content
			if (!$rowselected->store())
			{
				JError::raiseError(500, $rowselected->getError());
				return;
			}

			$msg = '';
		}

		if ($flash_rotation)
		{
			$msg .= JText::_('FEEDBACK_QUOTE_SELECTED_FOR_ROTATION');
		}
		if ($notable_quotes)
		{
			$msg .= JText::_('FEEDBACK_QUOTE_SELECTED_FOR_QUOTES');
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->controller . 'type=' . $this->type,
			$msg
		);
	}

	/**
	 * Delete one or more entries
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Check for an ID
		if (!$id)
		{
			JError::raiseError(500, JText::_('FEEDBACK_SELECT_QUOTE_TO_DELETE'));
			return;
		}

		// Load the quote
		if ($this->type == 'regular')
		{
			$row = new FeedbackQuotes($this->database);
		}
		else
		{
			$row = new SelectedQuotes($this->database);
		}
		$row->load($id);

		// Delete associated files
		$row->deletePicture($this->config);

		// Delete the quote
		$row->delete();

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->controller . 'type=' . $type,
			JText::_('FEEDBACK_REMOVED')
		);
	}

	/**
	 * Cancel a task and redirect to main listing
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->controller . 'type=' . $this->type
		);
	}
}

