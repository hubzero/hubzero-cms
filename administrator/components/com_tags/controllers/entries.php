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
 * Tags controller class for managing entries
 */
class TagsControllerEntries extends Hubzero_Controller
{
	/**
	 * List all tags
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart', 
			'limitstart', 
			0, 
			'int'
		);
		$this->view->filters['search'] = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search', 
			''
		)));
		$this->view->filters['by']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.by', 
			'filterby', 
			'all'
		));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'filter_order', 
			'raw_tag'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir', 
			'filter_order_Dir', 
			'ASC'
		));
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$t = new TagsModelCloud();

		// Record count
		$this->view->total = $t->tags('count', $this->view->filters);

		$this->view->filters['limit'] = ($this->view->filters['limit'] == 0) ? 'all' : $this->view->filters['limit'];

		// Get records
		$this->view->rows = $t->tags('list', $this->view->filters);

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
	 * Add a new entry
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
	 * @param      object $tag Tag being edited
	 * @return     void
	 */
	public function editTask($tag=NULL)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Load a tag object if one doesn't already exist
		if (is_object($tag))
		{
			$this->view->tag = $tag;
		}
		else 
		{
			// Incoming
			$id = JRequest::getInt('id', 0, 'request');

			$this->view->tag = new TagsModelTag($id);
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

	/**
	 * Save an entry
	 * 
	 * @param      integer $redirect Redirect after saving? (defaults to 1 = yes)
	 * @return     void
	 */
	public function saveTask($redirect=1)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$fields = JRequest::getVar('fields', array(), 'post');

		$row = new TagsModelTag($fields['id']);
		if (!$row->bind($fields)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($fields['admin'])
		{
			$row->set('admin', 1);
		}
		else
		{
			$row->set('admin', 0);
		}

		// Store new content
		if (!$row->store(true)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Redirect to main listing
		if ($redirect)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('TAG_SAVED')
			);
		}
	}

	/**
	 * Remove one or more entries
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids)) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('No items selected'),
				'error'
			);
			return;
		}

		// Get Tags plugins
		JPluginHelper::importPlugin('tags');
		$dispatcher =& JDispatcher::getInstance();

		foreach ($ids as $id)
		{
			// Remove references to the tag
			$dispatcher->trigger('onTagDelete', array($id));

			// Remove the tag
			$tag = new TagsModelTag($id);
			$tag->delete();
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('TAG_REMOVED')
		);
	}

	/**
	 * Merge two tags into one
	 * 
	 * @return     void
	 */
	public function mergeTask()
	{
		// Incoming
		$ids  = JRequest::getVar('id', array());
		$step = JRequest::getInt('step', 1);
		$step = ($step) ? $step : 1;

		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

		// Make sure we have some IDs to work with
		if ($step == 1 
		&& (!$ids || count($ids) < 1)) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		$idstr = implode(',', $ids);

		switch ($step)
		{
			case 1:
				// Instantiate a new view
				$this->view->step = 2;
				$this->view->idstr = $idstr;
				$this->view->tags = array();

				// Loop through the IDs of the tags we want to merge
				foreach ($ids as $id)
				{
					// Add the tag object to an array
					$this->view->tags[] = new TagsModelTag($id);
				}

				// Get all tags
				$cloud = new TagsModelCloud();
				$this->view->rows = $cloud->tags('list');

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

				// Get the string of tag IDs we plan to merge
				$ind = JRequest::getVar('ids', '', 'post');
				if ($ind) 
				{
					$ids = explode(',', $ind);
				} 
				else 
				{
					$ids = array();
				}

				// Incoming
				$tag_exist = JRequest::getInt('existingtag', 0, 'post');
				$tag_new   = JRequest::getVar('newtag', '', 'post');

				// Are we merging tags into a totally new tag?
				if ($tag_new) 
				{
					// Yes, we are
					$newtag = new TagsModelTag($tag_new);
					if (!$newtag->store(true))
					{
						$this->setError($newtag->getError());
					}
					$mtag = $newtag->get('id');
				} 
				else 
				{
					// No, we're merging into an existing tag
					$mtag = $tag_exist;
				}

				foreach ($ids as $id)
				{
					if ($mtag == $id) 
					{
						continue;
					}

					$oldtag = new TagsModelTag($id);
					if (!$oldtag->mergeWith($mtag))
					{
						$this->setError($oldtag->getError());
					}
				}

				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('TAGS_MERGED')
				);
			break;
		}
	}

	/**
	 * Copy all tag associations from one tag to another
	 * 
	 * @return     void
	 */
	public function pierceTask()
	{
		// Incoming
		$ids  = JRequest::getVar('id', array());
		$step = JRequest::getInt('step', 1);
		$step = ($step) ? $step : 1;

		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

		// Make sure we have some IDs to work with
		if ($step == 1 
		 && (!$ids || count($ids) < 1)) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		$idstr = implode(',', $ids);

		switch ($step)
		{
			case 1:
				$this->view->step = 2;
				$this->view->idstr = $idstr;
				$this->view->tags = array();

				// Loop through the IDs of the tags we want to merge
				foreach ($ids as $id)
				{
					// Load the tag's info
					$this->view->tags[] = new TagsModelTag($id);
				}

				// Get all tags
				$cloud = new TagsModelCloud();
				$this->view->rows = $cloud->tags('list');

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

				// Get the string of tag IDs we plan to merge
				$ind = JRequest::getVar('ids', '', 'post');
				if ($ind) 
				{
					$ids = explode(',', $ind);
				} 
				else 
				{
					$ids = array();
				}

				// Incoming
				$tag_exist = JRequest::getInt('existingtag', 0, 'post');
				$tag_new   = JRequest::getVar('newtag', '', 'post');

				// Are we merging tags into a totally new tag?
				if ($tag_new) 
				{
					// Yes, we are
					$newtag = new TagsModelTag($tag_new);
					if (!$newtag->store(true))
					{
						$this->setError($newtag->getError());
					}
					$mtag = $newtag->get('id');
				} 
				else 
				{
					// No, we're merging into an existing tag
					$mtag = $tag_exist;
				}

				foreach ($ids as $id)
				{
					if ($mtag == $id) 
					{
						continue;
					}

					$oldtag = new TagsModelTag($id);
					if (!$oldtag->copyTo($mtag))
					{
						$this->setError($oldtag->getError());
					}
				}

				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('TAGS_COPIED')
				);
			break;
		}
	}
}

