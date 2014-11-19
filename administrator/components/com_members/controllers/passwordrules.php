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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Manage members password rules
 */
class MembersControllerPasswordRules extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of password rules
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Incoming
		$this->view->filters = array(
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				$config->getValue('config.list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			'sort' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'ordering'
			),
			'sort_Dir' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort_Dir',
				'filter_order_Dir',
				'ASC'
			)
		);
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);


		// Get password rules object
		$prObj = new MembersPasswordRules($this->database);

		// Get count
		$this->view->total = $prObj->getCount($this->view->filters);

		// If count is zero, i.e. no records, let's add some default password rules
		if ($this->view->total == 0)
		{
			// Add default rules if we don't have any already
			$prObj->defaultContent();

			// Refresh count now that we've added the defaults
			$this->view->total = $prObj->getCount($this->view->filters);
		}

		// Get the records list
		$this->view->rows = $prObj->getRecords($this->view->filters);

		// Initiate pagination
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new password rule
	 *
	 * @return     void
	 */
	public function addTask()
	{
		// Output the HTML
		$this->editTask();
	}

	/**
	 * Edit a password rule
	 *
	 * @param      integer $id ID of member to edit
	 * @return     void
	 */
	public function editTask($id=0)
	{
		JRequest::setVar('hidemainmenu', 1);

		if (!$id)
		{
			// Incoming
			$id = JRequest::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}
		}

		// Initiate database class and load info
		$this->view->row = new MembersPasswordRules($this->database);
		$this->view->row->load($id);

		$this->view->rules_list = $this->rulesList($this->view->row->rule);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Apply changes to a password rule
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		// Save without redirect
		$this->saveTask(0);
	}

	/**
	 * Save password rule
	 *
	 * @param   integer  $redirect  Whether or not to redirect after save
	 * @return  void
	 */
	public function saveTask($redirect=1)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming password rule edits
		$fields = JRequest::getVar('fields', array(), 'post');

		// Load the profile
		$row = new MembersPasswordRules($this->database);

		// Try to save
		if (!$row->save($fields))
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Redirect
		if ($redirect)
		{
			// Redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_MEMBERS_PASSWORD_RULES_SAVE_SUCCESS'),
				'message'
			);
		}
		else
		{
			$this->view->task = 'edit';
			$this->editTask($fields['id']);
		}
	}

	/**
	 * Order up
	 *
	 * @return  void
	 */
	public function orderupTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$this->orderTask(1);

		// Output message and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_MEMBERS_PASSWORD_RULES_ORDERING_SAVED')
		);
	}

	/**
	 * Order down
	 *
	 * @return  void
	 */
	public function orderdownTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$this->orderTask(0);

		// Output message and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_MEMBERS_PASSWORD_RULES_ORDERING_SAVED')
		);
	}

	/**
	 * Reorder rules
	 *
	 * @param   integer  $up  Whether order up or down
	 * @return  void
	 */
	public function orderTask($up)
	{
		$cid = JRequest::getVar('id', array(0), 'post', 'array');
		JArrayHelper::toInteger($cid, array(0));

		$id  = $cid[0];
		$inc = ($up) ? -1 : 1;

		$row = new MembersPasswordRules($this->database);
		$row->load($id);
		$row->move($inc);
	}

	/**
	 * Save order
	 *
	 * @return  void
	 */
	public function saveorderTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the id's
		$cid = JRequest::getVar('id', array(0), 'post', 'array');
		JArrayHelper::toInteger($cid, array(0));

		// Get total and order values
		$total = count($cid);
		$order = JRequest::getVar('order', array(0), 'post', 'array');
		JArrayHelper::toInteger($order, array(0));

		// Get password rules object
		$row = new MembersPasswordRules($this->database);

		// Update ordering values
		for ($i=0; $i < $total; $i++)
		{
			$row->load((int) $cid[$i]);
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store())
				{
					JError::raiseError(500, $db->getErrorMsg());
				}
			}
		}

		// Output message and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_MEMBERS_PASSWORD_RULES_ORDERING_SAVED')
		);
	}

	/**
	 * Toggle a password rule between enabled and disabled
	 *
	 * @return  void
	 */
	public function toggle_enabledTask()
	{
		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the billboard
			$row = new MembersPasswordRules($this->database);
			$row->load($id);

			$enabled = ($row->enabled) ? 0 : 1;

			$row->enabled = $enabled;
			if (!$row->store())
			{
				JError::raiseError(500, $row->getError());
				return;
			}
		}

		// Output message and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_MEMBERS_PASSWORD_RULES_TOGGLE_ENABLED')
		);
	}

	/**
	 * Restore default password rules (found in password_rules table class)
	 *
	 * @return  void
	 */
	public function restore_default_contentTask()
	{
		// Get the object
		$obj = new MembersPasswordRules($this->database);

		// Do the restore (set flag = 1 to force restore)
		$obj->defaultContent(1);

		// Output message and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_MEMBERS_PASSWORD_RULES_RESTORED')
		);
	}

	/**
	 * Removes [a] password rule(s)
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				$row = new MembersPasswordRules($this->database);

				// Remove the record
				$row->delete($id);
			}
		}
		else // no rows were selected
		{
			// Output message and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_MEMBERS_PASSWORD_RULES_DELETE_NO_ROW_SELECTED'),
				'warning'
			);
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_MEMBERS_PASSWORD_RULES_DELETE_SUCCESS')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Build rules select list
	 *
	 * @return  void
	 */
	public function rulesList($current_rule='')
	{
		$rules[] = JHTML::_('select.option', 'minClassCharacters',  'minClassCharacters',  'value', 'text');
		$rules[] = JHTML::_('select.option', 'minPasswordLength',   'minPasswordLength',   'value', 'text');
		$rules[] = JHTML::_('select.option', 'maxPasswordLength',   'maxPasswordLength',   'value', 'text');
		$rules[] = JHTML::_('select.option', 'minUniqueCharacters', 'minUniqueCharacters', 'value', 'text');
		$rules[] = JHTML::_('select.option', 'notBlacklisted',      'notBlacklisted',      'value', 'text');
		$rules[] = JHTML::_('select.option', 'notNameBased',        'notNameBased',        'value', 'text');
		$rules[] = JHTML::_('select.option', 'notUsernameBased',    'notUsernameBased',    'value', 'text');
		$rules[] = JHTML::_('select.option', 'notReused',           'notReused',           'value', 'text');
		$rules[] = JHTML::_('select.option', 'notStale',            'notStale',            'value', 'text');

		$rselected = $current_rule;

		return JHTML::_('select.genericlist', $rules, 'fields[rule]', '', 'value', 'text', $rselected, 'field-rule', false, false);
	}
}