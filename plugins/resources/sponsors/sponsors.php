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

jimport('joomla.plugin.plugin');
ximport('Hubzero_Plugin');

/**
 * Display sponsors on a resource page
 */
class plgResourcesSponsors extends Hubzero_Plugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onResourcesSubAreas($resource)
	{
		$areas = array(
			'sponsors' => JText::_('PLG_RESOURCES_SPONSORS')
		);
		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 * 
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      integer $miniview  View style
	 * @return     array
	 */
	public function onResourcesSub($resource, $option, $miniview=0)
	{
		$arr = array(
			'area' => 'sponsors',
			'html' => '',
			'metadata' => ''
		);

		// Get recommendations
		$this->database = JFactory::getDBO();

		// Instantiate a view
		ximport('Hubzero_Plugin_View');
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'resources',
				'element' => 'sponsors',
				'name'    => 'display',
				'layout'  => 'mini'
			)
		);

		if ($miniview) 
		{
			$this->view->setLayout('mini');
		}

		// Pass the view some info
		$this->view->option   = $option;
		$this->view->resource = $resource;
		$this->view->params   = $this->params;
		$this->view->data     = '';

		require_once(JPATH_ROOT . DS . 'plugins' . DS . 'resources' . DS . 'sponsors' . DS . 'tables' . DS . 'sponsor.php');

		$this->sponsors = array();

		$model = new ResourcesSponsor($this->database);
		$records = $model->getRecords(array('state' => 1));
		if (!$records)
		{
			return $arr;
		}

		foreach ($records As $record)
		{
			$this->sponsors[$record->alias] = $record;
		}

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');
		$rt = new ResourcesTags($this->database);
		$tags = $rt->getTags($resource->id, 0, 0, 1);

		if ($tags)
		{
			foreach ($tags as $tag)
			{
				if (isset($this->sponsors[$tag->tag]))
				{
					$this->view->data = $this->sponsors[$tag->tag]->description;
					break;
				}
			}
		}
		
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Return the output
		$arr['html'] = $this->view->loadTemplate();

		return $arr;
	}

	/**
	 * Return plugin name if this plugin has an admin interface
	 *
	 * @return	string
	 */
	public function onCanManage()
	{
		return $this->_name;
	}

	/**
	 * Determine task and execute it
	 *
	 * @param     string $option     Component name
	 * @param     string $controller Controller name
	 * @param     string $task       Task to perform
	 * @return    void
	 */
	public function onManage($option, $controller='plugins', $task='default')
	{
		$task = ($task) ?  $task : 'default';

		ximport('Hubzero_Plugin_View');
		require_once(JPATH_ROOT . DS . 'plugins' . DS . 'resources' . DS . 'sponsors' . DS . 'tables' . DS . 'sponsor.php');

		$this->_option     = $option;
		$this->_controller = $controller;
		$this->_task       = $task;
		$this->database    = JFactory::getDBO();

		$method = strtolower($task) . 'Task';

		return $this->$method();
	}

	/**
	 * Display a list of sponsors
	 *
	 * @return	void
	 */
	public function defaultTask()
	{
		// Instantiate a view
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'resources',
				'element' => 'sponsors',
				'name'    => 'admin',
				'layout'  => 'default'
			)
		);
		$this->view->option = $this->_option;
		$this->view->controller = $this->_controller;
		$this->view->task = $this->_task;
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.plugins.sponsors.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.plugins.sponsors.limitstart', 
			'limitstart', 
			0, 
			'int'
		);
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.plugins.sponsors.sort', 
			'filter_order', 
			'title'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.plugins.sponsors.sortdir', 
			'filter_order_Dir', 
			'ASC'
		));
		
		$model = new ResourcesSponsor($this->database);
		
		// Get a record count
		$this->view->total = $model->getCount($this->view->filters);

		// Get records
		$this->view->rows = $model->getRecords($this->view->filters);

		// initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);
		
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		return $this->view->loadTemplate();
	}
	
	/**
	 * Add a new type
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		return $this->editTask();
	}

	/**
	 * Edit a type
	 * 
	 * @return     void
	 */
	public function editTask($row=null)
	{
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'resources',
				'element' => 'sponsors',
				'name'    => 'admin',
				'layout'  => 'edit'
			)
		);
		$this->view->option = $this->_option;
		$this->view->controller = $this->_controller;
		$this->view->task = $this->_task;
		
		if ($row)
		{
			$this->view->row = $row;
		}
		else 
		{
			// Incoming (expecting an array)
			$id = JRequest::getInt('id', 0);

			// Load the object
			$this->view->row = new ResourcesSponsor($this->database);
			$this->view->row->load($id);
		}
		
		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		return $this->view->loadTemplate();
	}

	/**
	 * Save a type
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Initiate extended database class
		$fields = JRequest::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		$row = new ResourcesSponsor($this->database);
		if (!$row->bind($fields)) 
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'tag.php');

		$t = new TagsTableTag($this->database);
		$t->loadTag($row->alias);
		if (!$t->id) 
		{
			// Add new tag! 
			$t->tag = $row->alias;
			$t->raw_tag = addslashes($row->title);
			if (!$t->store()) 
			{
				$this->setError($t->getError());
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors',
			JText::_('Sponsor successfully saved')
		);
	}

	/**
	 * Remove one or more types
	 * 
	 * @return     void Redirects back to main listing
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (expecting an array)
		$ids = JRequest::getVar('id', array());

		// Ensure we have an ID to work with
		if (empty($ids)) 
		{
			// Redirect with error message
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors',
				JText::_('No sponsor selected'),
				'error'
			);
			return;
		}

		$rt = new ResourcesSponsor($this->database);

		foreach ($ids as $id)
		{
			// Delete the type
			$rt->delete($id);
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors',
			JText::_('Type successfully saved')
		);
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
	 * Sets the state of one or more entries
	 * 
	 * @param      integer The state to set entries to
	 * @return     void
	 */
	public function stateTask($state=0) 
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1) 
		{
			$action = ($state == 1) ? JText::_('unpublish') : JText::_('publish');

			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors',
				JText::_('Select an entry to ' . $action),
				'error'
			);
			return;
		}

		foreach ($ids as $id) 
		{
			// Update record(s)
			$row = new ResourcesSponsor($this->database);
			$row->load(intval($id));
			$row->state = $state;
			if (!$row->store()) 
			{
				$this->setError($row->getError());
				return $this->defaultTask();
			}
		}

		// set message
		if ($state == 1) 
		{
			$message = JText::_(count($ids) . ' Item(s) successfully published');
		} 
		else
		{
			$message = JText::_(count($ids) . ' Item(s) successfully unpublished');
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors',
			$message
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors'
		);
	}

	/**
	 * Redirect
	 *
	 * @return	void
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}
}

