<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Blog\Controllers;

use Hubzero\Component\AdminController;
use Components\Blog\Models\Entry;
use Components\Blog\Models\Comment;
use Components\Blog\Tables;

/**
 * Blog controller class for comments
 */
class Comments extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of blog entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get configuration
		$jconfig = \JFactory::getConfig();
		$app = \JFactory::getApplication();

		$this->view->filters = array(
			'entry_id'     => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.entry_id',
				'entry_id',
				0,
				'int'
			),
			'search'  => urldecode($app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			// Get sorting variables
			'sort'         => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created'
			),
			'sort_Dir'     => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			// Get paging variables
			'limit'        => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				$jconfig->getValue('config.list_limit'),
				'int'
			),
			'start'        => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		$this->view->entry = new Entry($this->view->filters['entry_id']);

		// Instantiate our table object
		$obj = new Tables\Comment($this->database);

		// Get records
		$rows = $obj->getEntries($this->view->filters);

		$levellimit = ($this->view->filters['limit'] == 0) ? 500 : $this->view->filters['limit'];

		$list = array();
		$children = array();
		if ($rows)
		{
			// First pass - collect children
			foreach ($rows as $v)
			{
				$k = new Comment($v);

				$pt = $k->get('parent');
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $k);
				$children[$pt] = $list;
			}

			// Second pass - get an indent list of the items
			$list = $this->treeRecurse(0, '', array(), $children, max(0, $levellimit-1));
		}

		// Get record count
		$this->view->total = count($list);

		$this->view->rows = array_slice($list, $this->view->filters['start'], $this->view->filters['limit']);

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
				$id = $v->get('id');

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

				if ($v->get('parent') == 0)
				{
					$txt = '';
				}
				else
				{
					$txt = $pre;
				}
				$pt = $v->get('parent');

				$list[$id] = $v;
				$list[$id]->set('treename', "$indent$txt");
				$list[$id]->set('children', count(@$children[$id]));
				$list = $this->treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type);
			}
		}
		return $list;
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		\JRequest::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = \JRequest::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$row = new Comment($id);
		}

		$this->view->row = $row;

		if (!$this->view->row->exists())
		{
			$this->view->row->set('entry_id', \JRequest::getInt('entry_id', 0));
			$this->view->row->set('created_by', $this->juser->get('id'));
			$this->view->row->set('created', \JFactory::getDate()->toSql());
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
	 * Save changes to an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		\JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = \JRequest::getVar('fields', array(), 'post', 'none', 2);

		// Initiate extended database class
		$row = new Comment($fields['id']);
		if (!$row->bind($fields))
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		// Set the redirect
		$this->setRedirect(
			\JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&entry_id=' . $fields['entry_id'], false),
			\JText::_('COM_BLOG_COMMENT_SAVED')
		);
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		\JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = \JRequest::getVar('id', array());

		if (count($ids) > 0)
		{
			$this->setMessage(\JText::_('COM_BLOG_COMMENT_DELETED'));

			// Loop through all the IDs
			foreach ($ids as $id)
			{
				$entry = new Comment(intval($id));
				// Delete the entry
				if (!$entry->delete())
				{
					$this->setMessage($entry->getError(), 'error');
				}
			}
		}

		// Set the redirect
		$this->setRedirect(
			\JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&entry_id=' . \JRequest::getInt('entry_id', 0), false)
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		// Set the redirect
		$this->setRedirect(
			\JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&entry_id=' . \JRequest::getInt('entry_id', 0), false)
		);
	}
}

