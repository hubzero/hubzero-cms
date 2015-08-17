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
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Collection.php');

/**
 * Controller class for storefront collections
 */
class StorefrontControllerCollections extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of all collections
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Get filters
		$this->view->filters = array(
			'access' => -1
		);
		$this->view->filters['sort'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'title'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		// Get paging variables
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		//print_r($this->view->filters);

		$obj = new StorefrontModelArchive();

		// Get record count
		$this->view->total = $obj->collections('count', $this->view->filters);

		// Get records
		$this->view->rows  = $obj->collections('list', $this->view->filters);
		//print_r($this->view->rows); die;

		// Initiate paging
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
		//print_r($this->view); die;
		$this->view->display();
	}

	/**
	 * Create a new category
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a category
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$obj = new StorefrontModelArchive();

		if (is_object($row))
		{
			$this->view->row = $row;
			$this->view->task = 'edit';
		}
		else
		{
			// Incoming
			$id = JRequest::getVar('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load collection
			$this->view->row = new StorefrontModelCollection($id);
		}

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
	 * Save a category and come back to the edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save a collection
	 *
	 * @param   boolean  $redirect  Redirect the page after saving
	 * @return  void
	 */
	public function saveTask($redirect = true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		// Get the collection
		$collection = new StorefrontModelCollection($fields['cId']);

		try
		{
			if (isset($fields['cName']))
			{
				$collection->setName($fields['cName']);
			}
			if (isset($fields['state']))
			{
				$collection->setActiveStatus($fields['state']);
			}
			if (isset($fields['alias']))
			{
				$collection->setAlias($fields['alias']);
			}
			$collection->setType('category');

			// Make sure there are no integrity issues
			$this->checkIntegrity($collection);
			$collection->save();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			$this->editTask($collection);
			return;
		}

		if ($redirect)
		{
			// Redirect
			$this->setRedirect(
				'index.php?option='.$this->_option . '&controller=' . $this->_controller,
				JText::_('COM_STOREFRONT_COLLECTION_SAVED')
			);
			return;
		}

		$this->editTask($collection);
	}

	/**
	 * Remove an entry
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Incoming
		$step = JRequest::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

		// What step are we on?
		switch ($step)
		{
			case 1:
				JRequest::setVar('hidemainmenu', 1);

				// Incoming
				$id = JRequest::getVar('id', array(0));
				if (!is_array($id) && !empty($id))
				{
					$id = array($id);
				}

				$this->view->cId = $id;

				// Set any errors
				if ($this->getError())
				{
					$this->view->setError($this->getError());
				}

				// Output the HTML
				$this->view->display();
			break;

			case 2:
				// Check for request forgeries
				JRequest::checkToken() or jexit('Invalid Token');

				// Incoming
				$cIds = JRequest::getVar('cId', array(0));

				// Make sure we have IDs to work with
				if (empty($cIds))
				{
					$this->setRedirect(
						'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
						JText::_('COM_STOREFRONT_NO_ID'),
						'error'
					);
					return;
				}

				$delete = JRequest::getVar('delete', 0);

				$msg = "Delete canceled";
				$type = 'error';
				if ($delete)
				{
					// Do the delete
					foreach ($cIds as $cId)
					{
						// Delete option group
						try
						{
							$collection = new StorefrontModelCollection($cId);
							$collection->delete();
						}
						catch (Exception $e)
						{
							$this->setRedirect(
								'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly',
								$e->getMessage(),
								$type
							);
							return;
						}
					}

					$msg = "Collection(s) deleted";
					$type = 'message';
				}

				// Set the redirect
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly',
					$msg,
					$type
				);
				break;
		}
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Set the state of an entry
	 *
	 * @param      integer $state State to set
	 * @return     void
	 */
	public function stateTask($state = 0)
	{
		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				($state == 1 ? JText::_('COM_STOREFRONT_SELECT_PUBLISH') : JText::_('COM_STOREFRONT_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		foreach ($ids as $cId)
		{
			// Save category
			try {
				$collection = new StorefrontModelCollection($cId);
				$collection->setActiveStatus($state);

				// Make sure there are no integrity issues
				$this->checkIntegrity($collection);
				$collection->save();
			}
			catch (Exception $e)
			{
				//JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				//return;
				$error = true;
			}
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = JText::sprintf('COM_STOREFRONT_ARCHIVED', count($ids));
			break;
			case '1':
				$message = JText::sprintf('COM_STOREFRONT_PUBLISHED', count($ids));
			break;
			case '0':
				$message = JText::sprintf('COM_STOREFRONT_UNPUBLISHED', count($ids));
			break;
		}

		$type = 'message';

		if (isset($error) && $error)
		{
			switch ($state)
			{
				case '1':
					$action = 'published';
					break;
				case '0':
					$action = 'unpublished';
					break;
			}

			$message = 'Collection could not be ' . $action;
			if (sizeof($ids) > 1)
			{
				$message = 'Some collection(s) could not be ' . $action;
			}
			$type = 'error';
		}

		// Redirect
		$this->setRedirect(
			'index.php?option='.$this->_option . '&controller=' . $this->_controller,
			$message,
			$type
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	private function checkIntegrity($collection)
	{
		// If the collection is not being published, no need to check for integrity
		if (!$collection->getActiveStatus())
		{
			return true;
		}

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'helpers' . DS . 'Integrity.php');
		$integrityCheck = Integrity::collectionIntegrityCheck($collection);

		if ($integrityCheck->status != 'ok')
		{
			$errorMessage = "Integrity check error:";
			foreach ($integrityCheck->errors as $error)
			{
				$errorMessage .= '<br>' . $error;
			}

			throw new Exception($errorMessage);
		}
		return true;
	}
}

