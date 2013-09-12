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
 * Blog controller class for entries
 */
class BlogControllerComments extends Hubzero_Controller
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
		$app =& JFactory::getApplication();

		$this->view->filters = array();
		$this->view->filters['entry_id']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.entry_id', 
			'entry_id', 
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

		$this->view->entry = new BlogTableEntry($this->database);
		$this->view->entry->load($this->view->filters['entry_id']);

		// Instantiate our HelloEntry object
		$obj = new BlogTableComment($this->database);

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
	 * @param      object $row BlogTableComment
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
			$ids = JRequest::getVar('id', array(0));
			if (is_array($ids) && !empty($ids)) 
			{
				$id = $ids[0];
			}

			// Load the article
			$this->view->row = new BlogTableComment($this->database);
			$this->view->row->load($id);
		}

		if (!$this->view->row->id)
		{
			$this->view->row->entry_id   = JRequest::getInt('entry_id', 0);
			$this->view->row->created_by = $this->juser->get('id');
			$this->view->row->created    = date('Y-m-d H:i:s', time());  // use gmdate() ?
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
		$fields = JRequest::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new BlogTableComment($this->database);
		if (!$row->bind($fields)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Check content
		if (!$row->check()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&entry_id=' . $fields['entry_id'],
			JText::_('Comment saved!')
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
		$ids      = JRequest::getVar('id', array());
		$entry_id = JRequest::getInt('entry_id', 0);

		if (count($ids) > 0) 
		{
			// Create a category object
			$entry = new BlogTableComment($this->database);

			// Loop through all the IDs
			foreach ($ids as $id)
			{
				// Delete the entry
				if (!$entry->delete(intval($id)))
				{
					$this->addComponentMessage($entry->getError(), 'error');
				}
			}
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&entry_id=' . $entry_id,
			JText::_('Comments deleted!')
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 * 
	 * @return     void
	 */
	public function cancel()
	{
		$entry_id = JRequest::getInt('entry_id', 0);

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&entry_id=' . $entry_id
		);
	}
}

