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
 * Events controller for categories
 */
class EventsControllerCategories extends Hubzero_Controller
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
	 * Display a list of entries
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->section = $this->_option;

		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$limit = $app->getUserStateFromRequest(
			$this->_option . '.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		$this->view->section_name = '';
		if (intval($this->view->section) > 0) 
		{
			$table = 'content';

			$this->database->setQuery("SELECT name FROM #__sections WHERE id='" . $this->view->section . "'");
			$this->view->section_name = $this->database->loadResult();
			if ($this->database->getErrorNum()) 
			{
				JError::raiseError(500, $this->database->getErrorMsg());
				return;
			}
			$this->view->section_name .= ' Section';
		} 
		else if (strpos($this->view->section, 'com_') === 0) 
		{
			$table = substr($this->view->section, 4);

			if (version_compare(JVERSION, '1.6', 'lt'))
			{
				$this->database->setQuery("SELECT name FROM #__components WHERE link='option=" . $this->view->section . "'");
			}
			else
			{
				$this->database->setQuery("SELECT name FROM #__extensions WHERE type='component' AND element='" . $this->view->section . "'");
			}
			$this->view->section_name = $this->database->loadResult();
			if ($this->database->getErrorNum()) 
			{
				JError::raiseError(500, $this->database->getErrorMsg());
				return;
			}
		} 
		else 
		{
			$table = $this->view->section;
		}

		// Get the total number of records
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$this->database->setQuery("SELECT count(*) FROM #__categories WHERE section='" . $this->view->section . "'");
		}
		else
		{
			$this->database->setQuery("SELECT count(*) FROM #__categories WHERE extension='" . $this->view->section . "'");
		}
		$this->view->total = $this->database->loadResult();
		if ($this->database->getErrorNum()) 
		{
			JError::raiseError(500, $this->database->stderr());
			return;
		}

		// dmcd may 22/04  added #__events_categories table to fetch category color property
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$this->database->setQuery("SELECT  c.*, g.name AS groupname, u.name AS editor, cc.color AS color, "
				. "COUNT(DISTINCT s2.checked_out) AS checked_out, COUNT(DISTINCT s1.id) AS num"
				. "\nFROM #__categories AS c"
				. "\nLEFT JOIN #__users AS u ON u.id = c.checked_out"
				. "\nLEFT JOIN #__groups AS g ON g.id = c.access"
				. "\nLEFT JOIN #__$table AS s1 ON s1.catid = c.id"
				. "\nLEFT JOIN #__$table AS s2 ON s2.catid = c.id AND s2.checked_out > 0"
				. "\nLEFT JOIN #__${table}_categories AS cc ON cc.id = c.id"
				. "\nWHERE section='" . $this->view->section . "'"
				. "\nGROUP BY c.id"
				. "\nORDER BY c.ordering, c.name"
				. "\nLIMIT $limitstart,$limit"
			);
		}
		else
		{
			$this->database->setQuery("SELECT  c.*, 'Public' AS groupname, u.name AS editor, cc.color AS color, "
				. "COUNT(DISTINCT s2.checked_out) AS checked_out, COUNT(DISTINCT s1.id) AS num"
				. "\nFROM #__categories AS c"
				. "\nLEFT JOIN #__users AS u ON u.id = c.checked_out"
				. "\nLEFT JOIN #__$table AS s1 ON s1.catid = c.id"
				. "\nLEFT JOIN #__$table AS s2 ON s2.catid = c.id AND s2.checked_out > 0"
				. "\nLEFT JOIN #__${table}_categories AS cc ON cc.id = c.id"
				. "\nWHERE extension='" . $this->view->section . "'"
				. "\nGROUP BY c.id"
				. "\nORDER BY c.title"
				. "\nLIMIT $limitstart,$limit"
			);
		}

		// Execute query
		$this->view->rows = $this->database->loadObjectList();
		if ($this->database->getErrorNum()) 
		{
			JError::raiseError(500, $this->database->stderr());
			return;
		}

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$limitstart, 
			$limit
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
	 * Show a form for adding an entry
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
	 * @return     void
	 */
	public function editTask()
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Load the category
		$this->view->row = new EventsCategory($this->database);
		$this->view->row->load($id);

		// Fail if checked out not by 'me'
		if ($this->view->row->checked_out && $this->view->row->checked_out <> $this->juser->get('id')) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('EVENTS_CAL_LANG_CATEGORY_CHECKEDOUT')
			);
			return;
		}

		if ($this->view->row->id) 
		{
			// Existing record
			$this->view->row->checkout($this->juser->get('id'));
		} 
		else 
		{
			// New record
			$this->view->row->section = $this->_option;
			$this->view->row->color = '';
		}

		// Make order list
		$order = array();

		$max = intval($this->view->row->getCategoryCount()) + 1;
		for ($i=1; $i < $max; $i++)
		{
			$order[] = JHTML::_('select.option', $i, $i, 'value', 'text');
		}

		$ipos[] = JHTML::_('select.option', 'left', JText::_('left'), 'value', 'text');
		$ipos[] = JHTML::_('select.option', 'right', JText::_('right'), 'value', 'text');

		$this->view->iposlist = JHTML::_(
			'select.genericlist', 
			$ipos, 
			'image_position', 
			'class="inputbox" size="1"',
			'value', 
			'text', 
			($this->view->row->image_position ? $this->view->row->image_position : 'left'), 
			false, 
			false
		);

		$imgFiles = $this->readDirectory(JPATH_ROOT . DS . 'images' . DS . 'stories');
		$images = array(JHTML::_('select.option', '', JText::_('Select Image'), 'value', 'text'));
		foreach ($imgFiles as $file)
		{
			if (preg_match("/bmp|gif|jpg|jpe|jpeg|png/", $file)) 
			{
				$images[] = JHTML::_('select.option', $file, $file, 'value', 'text');
			}
		}

		$this->view->imagelist = JHTML::_(
			'select.genericlist', 
			$images, 
			'image', 
			'class="inputbox" size="1"' . " onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='../images/stories/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='../images/M_images/blank.png'}\"",
			'value', 
			'text', 
			$this->view->row->image, 
			false, 
			false
		);

		$this->view->orderlist = JHTML::_(
			'select.genericlist', 
			$order, 
			'ordering', 
			'class="inputbox" size="1"',
			'value', 
			'text', 
			$this->view->row->ordering, 
			false, 
			false
		);

		// Get list of groups
		$this->database->setQuery("SELECT id AS value, name AS text FROM #__groups ORDER BY id");
		$this->view->groups = $this->database->loadObjectList();

		$this->view->glist = JHTML::_('select.genericlist', $this->view->groups, 'access', 'class="inputbox" size="1"','value', 'text', intval($this->view->row->access), false, false);

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
	 * Read the contents of a directory
	 * 
	 * @param      string  $path     Path to read
	 * @param      string  $filter   Filters to apply
	 * @param      boolean $recurse  Recursive?
	 * @param      boolean $fullpath Full path?
	 * @return     array 
	 */
	private function readDirectory($path, $filter='.', $recurse=false, $fullpath=false)
	{
		$arr = array(null);

		// Get the files and folders
		jimport('joomla.filesystem.folder');
		$files   = JFolder::files($path, $filter, $recurse, $fullpath);
		$folders = JFolder::folders($path, $filter, $recurse, $fullpath);
		// Merge files and folders into one array
		$arr = array_merge($files, $folders);
		// Sort them all
		asort($arr);
		return $arr;
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

		$row = new EventsCategory($this->database);
		if (!$row->bind($_POST)) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		if (!$row->check()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		if (!$row->store()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		$row->checkin();
		//$row->updateOrder("section='$row->section'");

		if ($oldtitle = JRequest::getVar('oldtitle', null, 'post')) 
		{
			if ($oldtitle != $row->title) 
			{
				$this->database->setQuery("UPDATE #__menu SET name='$row->title' WHERE name='$oldtitle' AND type='content_category'");
				$this->database->query();
			}
		}

		// Update Section Count
		if ($row->section != 'com_weblinks') 
		{
			$this->database->setQuery("UPDATE #__sections SET count=count+1 WHERE id = '$row->section'");
		}

		if (!$this->database->query()) 
		{
			JError::raiseError(500, $this->database->getErrorMsg());
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Publish one or more entries
	 * 
	 * @return     void
	 */
	public function publishTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids)) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		// Instantiate a category object
		$event = new EventsCategory($this->database);

		// Loop through the IDs and publish the category
		foreach ($ids as $id)
		{
			$event->publish($id);
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('EVENTS_CAL_LANG_CATEGORY_PUBLISHED')
		);
	}

	/**
	 * Unpublish one or more entries
	 * 
	 * @return     void
	 */
	public function unpublishTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids)) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		// Instantiate a category object
		$event = new EventsCategory($this->database);

		// Loop through the IDs and unpublish the category
		foreach ($ids as $id)
		{
			$event->unpublish($id);
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('EVENTS_CAL_LANG_CATEGORY_UNPUBLISHED')
		);
	}

	/**
	 * Move an item up one in the ordering
	 * 
	 * @return     void
	 */
	public function orderupTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array(0));
		$id = intval($id[0]);

		// Load the category, reorder, save
		$row = new EventsCategory($this->database);
		$row->load($id);
		$row->move(-1, "section='$row->section'");

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Move an item down one in the ordering
	 * 
	 * @return     void
	 */
	public function orderdownTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array(0));
		$id = intval($id[0]);

		// Load the category, reorder, save
		$row = new EventsCategory($this->database);
		$row->load($id);
		$row->move(1, "section='$row->section'");

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Remove one or more categories
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids)) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		$cids = array();
		if (count($ids) > 0) 
		{
			// Loop through each category ID
			foreach ($ids as $id)
			{
				// Load the category
				$cat = new EventsCategory($this->database);
				$cat->load($id);
				// Check its count of items in it
				if ($cat->count > 0) 
				{
					// Category is NOT empty
					$cids[] = $cat->name;
				} 
				else 
				{
					// Empty category, go ahead and delete
					$cat->delete($id);
				}
			}
		}

		if (count($cids)) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::sprintf('EVENTS_CAL_LANG_CATEGORY_NOTEMPTY', implode("\', \'", $cids)),
				'warning'
			);
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('EVENTS_CAL_LANG_CATEGORY_REMOVED')
		);
	}

	/**
	 * Cancel a task by redirecting to main page
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		// Checkin the category
		$row = new EventsCategory($this->database);
		$row->bind($_POST);
		$row->checkin();

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}

