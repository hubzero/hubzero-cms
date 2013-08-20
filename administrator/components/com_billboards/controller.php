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

ximport('Hubzero_Controller');

/**
 * Primary controller for the Billboards component
 */
class BillboardsController extends Hubzero_Controller
{
	/**
	 * Controller execute method, used for selecting the correct function based on task.  
	 * Defaults to the billboards browse view.
	 * 
	 * @return void
	 */
	public function execute()
	{
		$this->_task = JRequest::getVar('task', '');

		switch ($this->_task)
		{
			// Billboards
			case 'browse':            $this->browse();             break;
			case 'add':               $this->edit();               break;
			case 'edit':              $this->edit();               break;
			case 'save':              $this->save();               break;
			case 'saveorder':         $this->saveorder();          break;
			case 'delete':            $this->delete();             break;
			case 'publish':           $this->publish(1);           break;
			case 'unpublish':         $this->publish(0);           break;
			case 'cancel':            $this->cancel();             break;

			// Billboard Collections (billboard groupings)
			case 'collections':       $this->collections();        break;
			case 'editcollection':    $this->editcollection();     break;
			case 'newcollection':     $this->editcollection();     break;
			case 'savecollection':    $this->savecollection();     break;
			case 'deletecollection':  $this->deletecollection();   break;
			case 'cancelcollection':  $this->cancelcollection();   break;

			default: $this->browse(); break;
		}
	}

	//----------------------------------------------------------
	// Billboards Views
	//----------------------------------------------------------

	/**
	 * Browse the list of billboards
	 * 
	 * @return void
	 */
	protected function browse()
	{
		// Instantiate a new view
		$view = new JView(array('name'=>'billboards'));
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option . '.billboards.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option . '.billboards.limitstart', 'limitstart', 0, 'int');

		// Get a billboard object
		$billboards = new BillboardsBillboard($this->database);

		// Get a record count
		$view->total = $billboards->getCount($view->filters);

		// Grab all the records
		$view->rows = $billboards->getRecords($view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination($view->total, $view->filters['start'], $view->filters['limit']);

		// Set any errors
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Edit a billboard
	 * 
	 * @return void
	 */
	protected function edit()
	{
		// Hide the menu, force users to save or cancel
		JRequest::setVar('hidemainmenu', 1);

		// Instantiate a new view
		$view = new JView(array('name'=>'billboard'));
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Incoming - expecting an array
		$cid = JRequest::getVar('cid', array(0));
		if (!is_array($cid)) 
		{
			$cid = array(0);
		}
		$uid = $cid[0];

		// Load the billboard and the collection (we need the collection to grab the collection name)
		$view->row = new BillboardsBillboard($this->database);
		$view->row->load($uid);
		$view->collection = new BillboardsCollection($this->database);
		$view->collection->load($view->row->collection_id);

		// Fail if not checked out by 'me'
		if ($view->row->checked_out && $view->row->checked_out != $this->juser->get('id')) 
		{
			$this->_redirect = 'index.php?option='. $this->_option;
			$this->_message = JText::_('BILLBOARDS_ERROR_CHECKED_OUT');
			return;
		}

		// Build the html select list for ordering
		$query = $view->row->buildOrderingQuery($view->row->collection_id);

		// Are we editing an existing entry?
		if ($uid) 
		{
			// Yes, we should check it out first
			$view->row->checkout($this->juser->get('id'));

			// Build the ordering info
			$view->row->ordering = $this->ordering($view->row, $uid, $query);
		} 
		else 
		{
			// Set some defaults
			$view->row->ordering = $this->ordering($view->row, '', $query);
		}

		// Grab the file location for the background images
		$params =& JComponentHelper::getParams('com_billboards');
		$view->image_location = $params->get('image_location', '/site/media/images/billboards/');

		if (!is_dir(JPATH_ROOT . DS . ltrim($view->image_location, DS)))
		{
			jimport('joomla.file.folder');
			JFolder::create(JPATH_ROOT . DS . ltrim($view->image_location, DS));
		}

		// Get the relative image location for building the links to the media manager
		$mparams =& JComponentHelper::getParams('com_media');
		$view->media_path = $mparams->get('image_path', 'site/media/images');

		// Make sure the image path is in the format that we need (i.e. remove any leading or trailing "/")
		if (substr($view->media_path, 0, 1) != DS)
		{
			$view->media_path = DS.$view->media_path;
		}
		if (substr($view->media_path, -1, 1) != DS)
		{
			$view->media_path = $view->media_path.DS;
		}
		$view->media_path = rtrim(str_replace($view->media_path, "", $view->image_location), DS);

		// Build the collection select list
		$view->clist = BillboardsHTML::buildCollectionsList($view->row->collection_id);

		// Build the select list for possible learn-more locations
		$view->learnmorelocation = BillboardsHTML::buildLearnMoreList($view->row->learn_more_location);

		// Set any errors
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Save a billboard
	 * 
	 * @return void
	 */
	protected function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming, make sure to allow HTML to pass through
		$billboard = JRequest::getVar('billboard', array(), 'post', 'array', JREQUEST_ALLOWHTML);
		$billboard = array_map('trim', $billboard);
		$row = new BillboardsBillboard($this->database);

		// If this is a new item, let's order it last
		if ($billboard['id'] == 0) 
		{
			$new_id = $row->getNextOrdering($billboard['collection_id']);
			$billboard['ordering'] = $new_id;
		}

		// Save the billboard
		if (!$row->bind($billboard)) 
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

		// Check in the billboard now that we've saved it
		$row->checkin();

		// Redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&task=billboards';
		$this->_message = JText::_('BILLBOARDS_BILLBOARD_SUCCESSFULLY_SAVED');
	}

	/**
	 * Save the new order
	 * 
	 * @return void
	 */
	function saveorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Initialize variables
		$cid   = JRequest::getVar('cid', array(), 'post', 'array');
		$order = JRequest::getVar('order', array(), 'post', 'array');
		$total = count($cid);
		$row   = new BillboardsBillboard($this->database);

		// Make sure we have something to work with
		if (empty($cid))
		{
			JError::raiseWarning(500, JText::_('BILLBOARDS_ORDER_PLEASE_SELECT_ITEMS'));
			return;
		}

		// Update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$row->load($cid[$i]);
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store())
				{
					JError::raiseError(500, $row->getError());
					return;
				}
			}
		}

		// Clear the component's cache
		$cache =& JFactory::getCache('com_billboards');
		$cache->clean();

		// Redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&task=billboards';
		$this->_message = JText::_('BILLBOARDS_ORDER_SUCCESSFULLY_UPDATED');
	}

	/**
	 * Delete a billboard
	 * 
	 * @return void
	 */
	protected function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (expecting an array)
		$ids = JRequest::getVar('cid', array(0));
		if (!is_array($ids))
		{
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0) 
		{
			$billboard = new BillboardsBillboard($this->database);

			// Loop through the array of ID's and delete
			foreach ($ids as $id)
			{
				if (!$billboard->delete($id)) 
				{
					$this->_redirect = 'index.php?option=' . $this->_option;
					$this->_message = JText::_('BILLBOARDS_ERROR_CANT_DELETE');
					return;			
				}
			}
		}

		// Redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&task=billboards';
		$this->_message = JText::sprintf('BILLBOARDS_BILLBOARD_SUCCESSFULLY_DELETED',count($ids));
	}

	/**
	 * Toggle a billboard between published and unpublished.  We're looking for an array of ID's to publish/unpublish
	 * 
	 * @param  $publish: 1 to publish and 0 for unpublish
	 * @return void
	 */
	protected function publish($publish=1)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('cid', array(0));
		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the billboard
			$row = new BillboardsBillboard($this->database);
			$row->load($id);

			// Only alter items not checked out or checked out by 'me'
			if ($row->checked_out == 0 || $row->checked_out == $this->juser->get('id')) 
			{
				$row->published = $publish;
				if (!$row->store($publish)) 
				{
					JError::raiseError(500, $row->getError());
					return;
				}
				// Check it back in
				$row->checkin($id);
			} 
			else 
			{
				$this->_redirect = 'index.php?option=' . $this->_option;
				$this->_message = JText::_('BILLBOARDS_ERROR_CHECKED_OUT');
				return;
			}
		}

		// Redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&task=billboards';
	}

	/**
	 * Cancels out of the billboard edit view, makes sure to check the billboard back in for other people to edit
	 * 
	 * @return void
	 */
	protected function cancel()
	{
		// Incoming - we need an id so that we can check it back in
		$billboard = JRequest::getVar('billboard', array(), 'post');

		// Check the billboard back in
		$row = new BillboardsBillboard($this->database);
		$row->bind($billboard);
		$row->checkin();

		// Redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&task=billboards';
	}

	//----------------------------------------------------------
	// Collections Views
	//----------------------------------------------------------

	/**
	 * Browse billboards collections (collections are used to display multiple billboards via mod_billboards)
	 * 
	 * @return void
	 */
	protected function collections()
	{
		// Instantiate a new view
		$view = new JView(array('name'=>'collections'));
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Get paging variables
		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option . '.collections.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option . '.collections.limitstart', 'limitstart', 0, 'int');

		// Get an object
		$collections = new BillboardsCollection($this->database);

		// Get a record count
		$view->total = $collections->getCount($view->filters);

		// Grab the results
		$view->rows = $collections->getRecords($view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination($view->total, $view->filters['start'], $view->filters['limit']);

		// Set any errors
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Edit a billboards collection
	 * 
	 * @return void
	 */
	protected function editcollection()
	{
		// Hide the menu, force users to save or cancel
		JRequest::setVar('hidemainmenu', 1);

		// Instantiate a new view
		$view = new JView(array('name'=>'collection'));
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Incoming (expecting an array)
		$id = JRequest::getVar('id', array(0));
		if (!is_array($id)) 
		{
			$id = array(0);
		}
		$cid = $id[0];

		// Initiate a class and load the info
		$view->row = new BillboardsCollection($this->database);
		$view->row->load($cid);

		// Set any errors
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Save a billboard collection
	 * 
	 * @return void
	 */
	protected function savecollection()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Trim all posted items (we don't need to allow HTML here)
		$collection = JRequest::getVar('collection', array(), 'post');
		$collection = array_map('trim',$collection);

		// Initiate class and bind posted items to database fields
		$row = new BillboardsCollection($this->database);
		if (!$row->bind($collection)) 
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

		// Output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&task=collections';
		$this->_message = JText::_('BILLBOARDS_COLLECTION_SUCCESSFULLY_SAVED');
	}

	/**
	 * Delete a billboard collection
	 * 
	 * @return void
	 */
	protected function deletecollection()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array(0));
		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

		// Loop through the selected collections to delete
		// @TODO: maybe we should warn people if trying to delete a collection with associated billboards?
		foreach ($ids as $id)
		{
			// Delete collection
			$collection = new BillboardsCollection($this->database);
			$collection->delete($id);
		}

		// Output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&task=collections';
		$this->_message = JText::sprintf('BILLBOARDS_COLLECTION_SUCCESSFULLY_DELETED',count($ids));
	}

	/**
	 * Cancel out of editing a billboard collection (i.e. just redirect back to the collections view)
	 * 
	 * @return void
	 */
	protected function cancelcollection()
	{
		// Just redirect, no checkin necessary
		$this->_redirect = 'index.php?option=' . $this->_option . '&task=collections';
	}

	//----------------------------------------------------------
	// Miscellaneous Functions
	//----------------------------------------------------------

	/**
	 * Build the select list for ordering of a specified Table
	 * 
	 * @return $ordering
	 */
	protected function ordering(&$row, $id, $query, $neworder = 0)
	{
		$db =& JFactory::getDBO();

		if ($id) 
		{
			$order = JHTML::_('list.genericordering', $query);
			$ordering = JHTML::_('select.genericlist', $order, 'billboard[ordering]', 'class="inputbox" size="1"', 'value', 'text', intval($row->ordering));
		} 
		else 
		{
			if ($neworder) 
			{
				$text = JText::_('descNewItemsFirst');
			} 
			else 
			{
				$text = JText::_('descNewItemsLast');
			}
			$ordering = '<input type="hidden" name="billboard[ordering]" value="' . $row->ordering . '" />' . $text;
		}

		return $ordering;
	}
}