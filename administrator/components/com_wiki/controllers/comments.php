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

/**
 * Wiki controller class for entries
 */
class WikiControllerComments extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of blog entries
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$jconfig = JFactory::getConfig();
		$app = JFactory::getApplication();

		$this->view->filters = array();
		$this->view->filters['pageid']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.pageid',
			'pageid',
			0,
			'int'
		));
		$this->view->filters['search']  = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		)));
		// Get sorting variables
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'created'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		// Get paging variables
		$this->view->filters['limit']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$jconfig->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		$this->view->entry = new WikiModelPage($this->view->filters['pageid']);

		// Instantiate our HelloEntry object
		$obj = new WikiTableComment($this->database);

		// Get records
		$rows = $obj->find('list', $this->view->filters);

		$levellimit = ($this->view->filters['limit'] == 0) ? 500 : $this->view->filters['limit'];

		$list = array();
		$children = array();
		if ($rows)
		{
			// First pass - collect children
			foreach ($rows as $v)
			{
				//$v->name = '';
				$pt = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}

			// Second pass - get an indent list of the items
			$list = $this->treeRecurse(0, '', array(), $children, max(0, $levellimit-1));
		}

		// Get record count
		$this->view->total = count($list);

		$this->view->rows = array_slice($list, $this->view->filters['start'], $this->view->filters['limit']);

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
		$this->view->display();
	}

	/**
	 * Recursive function to build tree
	 *
	 * @param      integer $id       Parent ID
	 * @param      string  $indent   Indent text
	 * @param      array   $list     List of records
	 * @param      array   $children Container for parent/children mapping
	 * @param      integer $maxlevel Maximum levels to descend
	 * @param      integer $level    Indention level
	 * @param      integer $type     Indention type
	 * @return     void
	 */
	public function treeRecurse($id, $indent, $list, $children, $maxlevel=9999, $level=0, $type=1)
	{
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->id;

				if ($type)
				{
					$pre    = '<span class="treenode">&#8970;</span>&nbsp;';
					$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				}
				else
				{
					$pre    = '- ';
					$spacer = '&nbsp;&nbsp;';
				}

				if (!is_a($v, 'stdClass'))
				{
					$data = $v->toArray();
				}
				else
				{
					foreach (get_object_vars($v) as $key => $val)
					{
						if (substr($key, 0, 1) != '_')
						{
							$data[$key] = $val;
						}
					}
				}

				$k = new stdClass;
				foreach ($data as $key => $val)
				{
					$k->$key = $val;
				}

				if ($v->parent == 0)
				{
					$txt = '';
				}
				else
				{
					$txt = $pre;
				}
				$pt = $v->parent;

				$list[$id] = $k;
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = count(@$children[$id]);
				$list = $this->treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type);
			}
		}
		return $list;
	}

	/**
	 * Create a new category
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param      object $row WikiTableComment
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming
			$id = JRequest::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$this->view->row = new WikiModelComment($id);
		}

		if (!$this->view->row->exists())
		{
			$this->view->row->set('pageid', JRequest::getInt('pageid', 0));
			$this->view->row->set('created_by', $this->juser->get('id'));
			$this->view->row->set('created', JFactory::getDate()->toSql());  // use gmdate() ?
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
	 * Save an entry and fall through to edit form
	 *
	 * @return     void
	 */
	public function applyTask($redirect=1)
	{
		$this->saveTask(0);
	}

	/**
	 * Save an entry
	 *
	 * @param      integer $redirect Redirect (1) or fall through to edit form (0) ?
	 * @return     void
	 */
	public function saveTask($redirect=1)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new WikiModelComment($fields['id']);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($redirect)
		{
			// Set the redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $fields['pageid'],
				JText::_('COM_WIKI_COMMENT_SAVED')
			);
		}

		$this->editTask($row);
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
		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) > 0)
		{
			// Loop through all the IDs
			foreach ($ids as $id)
			{
				$entry = new WikiModelComment(intval($id));
				// Delete the entry
				if (!$entry->delete())
				{
					$this->addComponentMessage($entry->getError(), 'error');
				}
			}
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . JRequest::getInt('pageid', 0),
			JText::sprintf('COM_WIKI_COMMENTS_DELETED', count($ids))
		);
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(2);
	}

	/**
	 * Set the status on one or more entries
	 *
	 * @param      integer $state Status to set
	 * @return     void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids    = JRequest::getVar('id', array());
		$ids    = (!is_array($ids) ? array($ids) : $ids);
		$pageid = JRequest::getInt('pageid', 0);

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($state == 1) ? JText::_('COM_WIKI_UNPUBLISH') : JText::_('COM_WIKI_PUBLISH');

			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid,
				JText::sprintf('COM_WIKI_ERROR_SELECT_TO', $action),
				'error'
			);
			return;
		}

		// Loop through all the IDs
		foreach ($ids as $id)
		{
			$entry = new WikiModelComment(intval($id));
			$entry->set('status', $state);
			if (!$entry->store())
			{
				$this->addComponentMessage($entry->getError(), 'error');
			}
		}

		// Set message
		if ($state == 1)
		{
			$message = JText::sprintf('COM_WIKI_ITEMS_PUBLISHED', count($ids));
		}
		else
		{
			$message = JText::sprintf('COM_WIKI_ITEMS_UNPUBLISHED', count($ids));
		}

		// Set redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid,
			$message
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return     void
	 */
	public function cancel()
	{
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . JRequest::getInt('pageid', 0)
		);
	}
}

