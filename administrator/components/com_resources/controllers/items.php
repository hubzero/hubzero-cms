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
 * Manage resource entries
 */
class ResourcesControllerItems extends Hubzero_Controller
{
	/**
	 * Executes a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->registerTask('accesspublic', 'access');
		$this->registerTask('accessregistered', 'access');
		$this->registerTask('accessspecial', 'access');
		$this->registerTask('accessprotected', 'access');
		$this->registerTask('accessprivate', 'access');

		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		$this->registerTask('add', 'editTask');

		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');

		parent::execute();
	}

	/**
	 * Lists standalone resources
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'resources.css');

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.resources.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.resources.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']   = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.resources.search',
			'search',
			''
		)));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.resources.sort',
			'filter_order',
			'created'
			));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.resources.sortdir',
			'filter_order_Dir',
			'DESC'
		));
		$this->view->filters['status']   = trim($app->getUserStateFromRequest(
			$this->_option . '.resources.status',
			'status',
			'all'
		));
		$this->view->filters['type']     = trim($app->getUserStateFromRequest(
			$this->_option . '.resources.type',
			'type',
			''
		));

		$model = new ResourcesResource($this->database);

		// Get record count
		$this->view->total = $model->getItemCount($this->view->filters);

		// Get resources
		$this->view->rows = $model->getItems($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Get <select> of types
		$rt = new ResourcesType($this->database);
		$this->view->types = $rt->getMajorTypes();

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
	 * List child resources of a parent resource
	 * 
	 * @return     void
	 */
	public function childrenTask()
	{
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'resources.css');

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Resource's parent ID
		//$this->view->pid = JRequest::getInt('pid', 0);
		$this->view->pid = $app->getUserStateFromRequest(
			$this->_option . '.children.pid',
			'pid',
			0,
			'int'
		);

		// Incoming
		$this->view->filters = array();
		$this->view->filters['parent_id'] = $this->view->pid;
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.children.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.children.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']   = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.children.search',
			'search',
			''
		)));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.children.sort',
			'filter_order',
			'ordering'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.children.sortdir',
			'filter_order_Dir',
			'ASC'
		));
		$this->view->filters['status']   = trim($app->getUserStateFromRequest(
			$this->_option . '.children.status',
			'status',
			'all'
		));

		// Get parent info
		$this->view->parent = new ResourcesResource($this->database);
		$this->view->parent->load($this->view->filters['parent_id']);

		// Record count
		$this->view->total = $this->view->parent->getItemChildrenCount($this->view->filters);

		// Get only children of this parent
		$this->view->rows = $this->view->parent->getItemChildren($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Get sections for learning modules
		// TODO: Phase out all learning modules code
		$this->view->sections = array();
		if ($this->view->parent->type == 4)
		{
			$rt = new ResourcesType($this->database);
			$this->view->sections = $rt->getTypes(29);
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
	 * List "child" resources without any parent associations
	 * 
	 * @return     void
	 */
	public function orphansTask()
	{
		$this->view->setLayout('children');

		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'resources.css');

		$this->view->pid = '-1';

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['parent_id'] = $this->view->pid;
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.orphans.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.orphans.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']   = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.orphans.search',
			'search',
			''
		)));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.orphans.sort',
			'filter_order',
			'created'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.orphans.sortdir',
			'filter_order_Dir',
			'DESC'
		));
		$this->view->filters['status']   = trim($app->getUserStateFromRequest(
			$this->_option . '.orphans.status',
			'status',
			'all'
		));

		$model = new ResourcesResource($this->database);

		// Record count
		$this->view->total = $model->getItemChildrenCount($this->view->filters);

		// Get only children of this parent
		$this->view->rows = $model->getItemChildren($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Get sections for learning modules
		// TODO: Phase out all learning modules code
		$rt = new ResourcesType($this->database);
		$this->view->sections = $rt->getTypes(29);

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
	 * Show the ratings for a resource
	 * 
	 * @return     void
	 */
	public function ratingsTask()
	{
		// Incoming
		$id = JRequest::getInt('id', 0);

		// Do we have an ID to work with?
		if (!$id)
		{
			$this->setError(JText::_('Missing resource ID'));
		}
		else
		{
			$rr = new ResourcesReview($this->database);
			$this->view->rows = $rr->getRatings($id);
			$this->view->id = $id;
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
	 * Show a form for adding a child to a resource
	 * 
	 * @return     void
	 */
	public function addchildTask()
	{
		// Incoming
		$pid  = JRequest::getInt('pid', 0);
		$id   = JRequest::getVar('id', array(0));
		$step = JRequest::getVar('step', 1);

		if (!empty($id) && !$pid)
		{
			$pid = $id[0];
			$id = 0;
		}

		// Make sure we have a prent ID
		if (!$pid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Missing parent resource ID'),
				'error'
			);
			return;
		}

		switch ($step)
		{
			case 1:
				$this->view->pid = $pid;

				// Get the available types
				$rt = new ResourcesType($this->database);
				$this->view->types = $rt->getTypes(30);

				// Load the parent resource
				$this->view->parent = new ResourcesResource($this->database);
				$this->view->parent->load($this->view->pid);

				// Set any errors
				if ($this->getError())
				{
					$this->view->setError($this->getError());
				}

				// Output the HTML
				$this->view->display();
			break;

			case 2:
				// Get the creation method
				$method = JRequest::getVar('method', '');

				if ($method == 'create')
				{
					// We're starting from scratch
					$this->view->setLayout('edit');
					$this->editTask(1);
				}
				elseif ($method == 'existing')
				{
					// We're just linking up an existing resource
					// Get the child ID we're linking
					$cid = JRequest::getInt('childid', 0);
					if ($cid)
					{
						$child = new ResourcesResource($this->database);
						$child->load($cid);

						if ($child && $child->title != '')
						{
							// Link 'em up!
							$this->_attachChild($cid, $pid);
						}
						else
						{
							$this->view->pid = $pid;

							// No child ID! Throw an error and present the form from the previous step
							$this->setError(JText::_('Resource with provided ID # not found.'));

							// Get the available types
							$rt = new ResourcesType($this->database);
							$this->view->types = $rt->getTypes(30);

							// Load the parent resource
							$this->view->parent = new ResourcesResource($this->database);
							$this->view->parent->load($pid);

							// Set any errors
							if ($this->getError())
							{
								$this->view->setError($this->getError());
							}

							// Output the HTML
							$this->view->display();
						}
					}
					else
					{
						$this->view->pid = $pid;

						// No child ID! Throw an error and present the form from the previous step
						$this->setError(JText::_('Please provide an ID #'));

						// Get the available types
						$rt = new ResourcesType($this->database);
						$this->view->types = $rt->getTypes(30);

						// Load the parent resource
						$this->view->parent = new ResourcesResource($this->database);
						$this->view->parent->load($pid);

						// Set any errors
						if ($this->getError())
						{
							$this->view->setError($this->getError());
						}

						// Output the HTML
						$this->view->display();
					}
				}
			break;
		}
	}

	/**
	 * Attaches a resource as a child to another resource
	 * Redirects to parent's children listing
	 * 
	 * @param      integer $id  ID of the child
	 * @param      integer $pid ID of the parent
	 * @return     void
	 */
	public function _attachChild($id, $pid)
	{
		// Make sure we have both parent and child IDs
		if (!$pid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Missing parent ID'), 
				'error'
			);
			return;
		}

		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid,
				JText::_('Missing child ID'), 
				'error'
			);
			return;
		}

		// Instantiate a ResourcesAssoc object
		$assoc = new ResourcesAssoc($this->database);

		// Get the last child in the ordering
		$order = $assoc->getLastOrder($pid);
		$order = ($order) ? $order : 0;

		// Increase the ordering - new items are always last
		$order = $order + 1;

		// Create new parent/child association
		$assoc->parent_id = $pid;
		$assoc->child_id  = $id;
		$assoc->ordering  = $order;
		$assoc->grouping  = 0;
		if (!$assoc->check())
		{
			$this->setError($assoc->getError());
		}
		else 
		{
			if (!$assoc->store(true))
			{
				$this->setError($assoc->getError());
			}
		}

		if ($this->getError())
		{
			// Redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid,
				$this->getError(), 
				'error'
			);
		}
		else 
		{
			// Redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid,
				JText::_('Child successfully added')
			);
		}
	}

	/**
	 * Removes a parent/child association
	 * Redirects to parent's children listing
	 * 
	 * @return     void
	 */
	public function removechildTask()
	{
		// Incoming
		$ids = JRequest::getVar('id', array(0));
		$pid = JRequest::getInt('pid', 0);

		// Make sure we have a parent ID
		if (!$pid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Missing parent ID'), 
				'error'
			);
			return;
		}

		// Make sure we have children IDs
		if (!$ids || count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid,
				JText::_('Missing child ID'), 
				'error'
			);
			return;
		}

		$assoc = new ResourcesAssoc($this->database);

		// Multiple IDs - loop through and delete them
		foreach ($ids as $id)
		{
			$assoc->delete($pid, $id);
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid,
			JText::sprintf('%s children successfully removed', count($ids))
		);
	}

	/**
	 * Edit form for a new resource
	 * 
	 * @return     void 
	 */
	public function addTask()
	{
		return $this->editTask(1);
	}

	/**
	 * Edit form for a resource
	 * 
	 * @param      integer $isnew Flag for editing (0) or creating new (1)
	 * @return     void 
	 */
	public function editTask($isnew=0)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		$this->view->isnew = $isnew;

		// Get the resource component config
		$this->view->rconfig = $this->config;

		// Push some needed styles to the tmeplate
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/' . $this->_option . '/assets/css/resources.css');

		// Incoming resource ID
		$id = JRequest::getVar('id', array(0));
		if (is_array($id)) {
			$id = $id[0];
		}

		// Incoming parent ID - this determines if the resource is standalone or not
		$this->view->pid = JRequest::getInt('pid', 0);

		// Grab some filters for returning to place after editing
		$this->view->return = array();
		$this->view->return['type']   = JRequest::getVar('type', '');
		$this->view->return['sort']   = JRequest::getVar('sort', '');
		$this->view->return['status'] = JRequest::getVar('status', '');

		// Instantiate our resource object
		$this->view->row = new ResourcesResource($this->database);
		$this->view->row->load($id);

		// Fail if checked out not by 'me'
		if ($this->view->row->checked_out
		 && $this->view->row->checked_out <> $this->juser->get('id'))
		{
			$task = '';
			if ($this->view->pid)
			{
				$task = '&task=children&pid=' . $this->view->pid;
			}
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . $task,
				JText::_('This resource is currently being edited by another administrator'),
				'notice'
			);
			return;
		}

		// Is this a new resource?
		if (!$id)
		{
			$this->view->row->created      = date('Y-m-d H:i:s', time());
			$this->view->row->created_by   = $this->juser->get('id');
			$this->view->row->modified     = '0000-00-00 00:00:00';
			$this->view->row->modified_by  = 0;
			$this->view->row->publish_up   = date('Y-m-d H:i:s', time());
			$this->view->row->publish_down = 'Never';
			if ($this->view->pid)
			{
				$this->view->row->published  = 1;
				$this->view->row->standalone = 0;
			}
			else
			{
				$this->view->row->published  = 3; // default to "new" status
				$this->view->row->standalone = 1;
			}
		}

		// Editing existing
		$this->view->row->checkout($this->juser->get('id'));

		if (trim($this->view->row->publish_down) == '0000-00-00 00:00:00')
		{
			$this->view->row->publish_down = JText::_('Never');
		}

		// Get name of resource creator
		$creator = JUser::getInstance($this->view->row->created_by);

		$this->view->row->created_by_name = $creator->get('name');
		$this->view->row->created_by_name = ($this->view->row->created_by_name) ? $this->view->row->created_by_name : JText::_('Unknown');

		// Get name of last person to modify resource
		if ($this->view->row->modified_by)
		{
			$modifier = JUser::getInstance($this->view->row->modified_by);

			$this->view->row->modified_by_name = $modifier->get('name');
			$this->view->row->modified_by_name = ($this->view->row->modified_by_name) ? $this->view->row->modified_by_name : JText::_('Unknown');
		}
		else
		{
			$this->view->row->modified_by_name = '';
		}

		$paramsClass = 'JParameter';
		//if (version_compare(JVERSION, '1.6', 'ge'))
		//{
		//	$paramsClass = 'JRegistry';
		//}

		// Get params definitions
		$this->view->params  = new $paramsClass($this->view->row->params, JPATH_COMPONENT . DS . 'resources.xml');
		$this->view->attribs = new $paramsClass($this->view->row->attribs);

		// Build selects of various types
		$rt = new ResourcesType($this->database);
		if ($this->view->row->standalone != 1)
		{
			$this->view->lists['type']         = ResourcesHtml::selectType($rt->getTypes(30), 'type', $this->view->row->type, '', '', '', '');
			$this->view->lists['logical_type'] = ResourcesHtml::selectType($rt->getTypes(28), 'logical_type', $this->view->row->logical_type, '[ none ]', '', '', '');
			$this->view->lists['sub_type']     = ResourcesHtml::selectType($rt->getTypes(30), 'logical_type', $this->view->row->logical_type, '[ none ]', '', '', '');
		}
		else
		{
			$this->view->lists['type']         = ResourcesHtml::selectType($rt->getTypes(27), 'type', $this->view->row->type, '', '', '', '');
			$this->view->lists['logical_type'] = ResourcesHtml::selectType($rt->getTypes(21), 'logical_type', $this->view->row->logical_type, '[ none ]', '', '', '');
		}

		// Build the <select> of admin users
		$this->view->lists['created_by'] = $this->userSelect('created_by', 0, 1);

		// Build the <select> for the group access
		$this->view->lists['access'] = ResourcesHtml::selectAccess($this->view->rconfig->get('accesses'), $this->view->row->access);

		// Is this a standalone resource?
		if ($this->view->row->standalone == 1)
		{
			// Get groups
			ximport('Hubzero_Group');
			$filters = array();
			$filters['authorized'] = 'admin';
			$filters['fields'] = array('cn','description','published','gidNumber','type');
			$filters['type'] = array(1,3);
			$filters['sortby'] = 'description';
			$groups = Hubzero_Group::find($filters);

			// Build <select> of groups
			$this->view->lists['groups'] = ResourcesHtml::selectGroup($groups, $this->view->row->group_owner);

			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'profile.php');
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'association.php');

			// Get all contributors
			$mp = new MembersProfile($this->database);
			$members = null; //$mp->getRecords(array('sortby'=>'surname DESC','limit'=>'all','search'=>'','show'=>''), true);

			// Get all contributors linked to this resource
			$ma = new MembersAssociation($this->database);
			$sql = "SELECT n.uidNumber AS id, a.authorid, a.name, n.givenName, n.middleName, n.surname, a.role, a.organization  
					FROM " . $ma->getTableName() . " AS a  
					LEFT JOIN " . $mp->getTableName() . " AS n ON n.uidNumber=a.authorid 
					WHERE a.subtable='resources'
					AND a.subid=" . $this->view->row->id . " 
					ORDER BY a.ordering";
			$this->database->setQuery($sql);
			$authnames = $this->database->loadObjectList();

			// Build <select> of contributors
			$authorslist = new JView(array(
				'name'   => $this->_controller, 
				'layout' => 'authors'
			));
			$authorslist->authnames = $authnames;
			$authorslist->attribs   = $this->view->attribs;
			$authorslist->option    = $this->_option;
			$authorslist->roles     = $rt->getRolesForType($this->view->row->type);

			$this->view->lists['authors'] = $authorslist->loadTemplate(); //ResourcesHtml::selectAuthors($members, $authnames, $this->view->attribs, $this->_option);

			// Get the tags on this item
			$rt = new ResourcesTags($this->database);
			$this->view->lists['tags'] = $rt->get_tag_string($this->view->row->id, 0, 0, NULL, 0, 1);
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
	 * Saves a resource
	 * Redirects to main listing
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Initiate extended database class
		$row = new ResourcesResource($this->database);
		if (!$row->bind($_POST))
		{
			echo ResourcesHtml::alert($row->getError());
			exit();
		}

		$isNew = 0;
		if ($row->id < 1)
		{
			$isNew = 1;
		}

		if ($isNew)
		{
			// New entry
			$row->created    = $row->created ? $row->created : date("Y-m-d H:i:s");
			$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('id');
		} else {
			$old = new ResourcesResource($this->database);
			$old->load($row->id);

			$created_by_id = JRequest::getInt('created_by_id', 0);

			// Updating entry
			$row->modified    = date("Y-m-d H:i:s");
			$row->modified_by = $this->juser->get('id');
			//$row->created     = $row->created ? $row->created : date("Y-m-d H:i:s");
			if ($created_by_id)
			{
				$row->created_by = $row->created_by ? $row->created_by : $created_by_id;
			}
			else
			{
				$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('id');
			}
		}
		if (trim($row->publish_down) == 'Never')
		{
			$row->publish_down = '0000-00-00 00:00:00';
		}

		// Get parameters
		$params = JRequest::getVar('params', '', 'post');
		if (is_array($params))
		{
			$txt = array();
			foreach ($params as $k => $v)
			{
				$txt[] = "$k=$v";
			}
			$row->params = implode("\n", $txt);
		}

		// Get attributes
		$attribs = JRequest::getVar('attrib', '', 'post');
		if (is_array($attribs))
		{
			$txta = array();
			foreach ($attribs as $k => $v)
			{
				$txta[] = "$k=$v";
			}
			$row->attribs = implode("\n", $txta);
		}

		// Get custom areas, add wrappers, and compile into fulltxt
		if (isset($_POST['nbtag']))
		{
			$type = new ResourcesType($this->database);
			$type->load($row->type);

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
			$elements = new ResourcesElements(array(), $type->customFields);
			$schema = $elements->getSchema();

			$fields = array();
			foreach ($schema->fields as $field)
			{
				$fields[$field->name] = $field;
			}

			$nbtag = $_POST['nbtag'];
			$found = array();

			foreach ($nbtag as $tagname => $tagcontent)
			{
				$f = '';

				$row->fulltxt .= "\n" . '<nb:' . $tagname . '>';
				if (is_array($tagcontent))
				{
					$c = count($tagcontent);
					$num = 0;
					foreach ($tagcontent as $key => $val)
					{
						if (trim($val))
						{
							$num++;
						}
						$row->fulltxt .= '<' . $key . '>' . trim($val) . '</' . $key . '>';
					}
					if ($c == $num)
					{
						$f = 'found';
					}
				}
				else 
				{
					$f = trim($tagcontent);
					if ($f)
					{
						$row->fulltxt .= trim($tagcontent);
					}
				}
				$row->fulltxt .= '</nb:' . $tagname . '>' . "\n";

				if (!$tagcontent && isset($fields[$tagname]) && $fields[$tagname]->required) 
				{
					echo ResourcesHtml::alert(JText::sprintf('RESOURCES_REQUIRED_FIELD_CHECK', $fields[$tagname]->label));
					exit();
				}

				$found[] = $tagname;
			}

			foreach ($fields as $field)
			{
				if (!in_array($field->name, $found) && $field->required)
				{
					$found[] = $field->name;
					$this->setError(JText::sprintf('COM_CONTRIBUTE_REQUIRED_FIELD_CHECK', $field->label));
				}
			}
		}

		// Code cleaner for xhtml transitional compliance
		$row->introtext = str_replace('<br>', '<br />', $row->introtext);
		$row->fulltxt  = str_replace('<br>', '<br />', $row->fulltxt);

		// Check content
		if (!$row->check())
		{
			echo ResourcesHtml::alert($row->getError());
			exit();
		}

		// Store content
		if (!$row->store())
		{
			echo ResourcesHtml::alert($row->getError());
			exit();
		}

		// Checkin resource
		$row->checkin();

		// Rename the temporary upload directory if it exist
		$tmpid = JRequest::getInt('tmpid', 0, 'post');
		if ($tmpid != ResourcesHtml::niceidformat($row->id))
		{
			jimport('joomla.filesystem.folder');

			// Build the full paths
			$path    = ResourcesHtml::dateToPath($row->created);
			$dir_id  = ResourcesHtml::niceidformat($row->id);

			$tmppath = ResourcesUtilities::buildUploadPath($path . DS . $tmpid);
			$newpath = ResourcesUtilities::buildUploadPath($path . DS . $dir_id);

			// Attempt to rename the temp directory
			$result = JFolder::move($tmppath, $newpath);
			if ($result !== true)
			{
				$this->setError($result);
			}

			$row->path = str_replace($tmpid, ResourcesHtml::niceidformat($row->id), $row->path);
			$row->store();
		}

		// Incoming tags
		$tags = JRequest::getVar('tags', '', 'post');

		// Save the tags
		$rt = new ResourcesTags($this->database);
		$rt->tag_object($this->juser->get('id'), $row->id, $tags, 1, 1);

		// Incoming authors
		$authorsOldstr = JRequest::getVar('old_authors', '', 'post');
		$authorsNewstr = JRequest::getVar('new_authors', '', 'post');
		if (!$authorsNewstr)
		{
			$authorsNewstr = $authorsOldstr;
		}
		//if ($authorsNewstr != $authorsOldstr) 
		//{
			include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'contributor.php');

			$authorsNew = explode(',', $authorsNewstr);
			$authorsOld = explode(',', $authorsOldstr);

			// We have either a new ordering or new authors or both
			if ($authorsNewstr)
			{
				for ($i=0, $n=count($authorsNew); $i < $n; $i++)
				{
					$rc = new ResourcesContributor($this->database);
					$rc->subtable     = 'resources';
					$rc->subid        = $row->id;
					if (is_numeric($authorsNew[$i]))
					{
						$rc->authorid     = $authorsNew[$i];
					}
					else 
					{
						$rc->authorid = $rc->getUserId($authorsNew[$i]);
					}
					$rc->ordering     = $i;
					$rc->role         = trim(JRequest::getVar($authorsNew[$i] . '_role', ''));
					$rc->name         = trim(JRequest::getVar($authorsNew[$i] . '_name', ''));
					$rc->organization = trim(JRequest::getVar($authorsNew[$i] . '_organization', ''));

					$authorsNew[$i] = $rc->authorid;

					if (in_array($authorsNew[$i], $authorsOld))
					{
						//echo 'update: ' . $rc->authorid . ', ' . $rc->role . ', ' . $rc->name . ', ' . $rc->organization . '<br />';
						// Updating record
						$rc->updateAssociation();
					}
					else
					{
						//echo 'create: ' . $rc->authorid . ', ' . $rc->role . ', ' . $rc->name . ', ' . $rc->organization . '<br />';
						// New record
						$rc->createAssociation();
					}
				}
			}
			// Run through previous author list and check to see if any IDs had been dropped
			if ($authorsOldstr)
			{
				$rc = new ResourcesContributor($this->database);

				for ($i=0, $n=count($authorsOld); $i < $n; $i++)
				{
					if (!in_array($authorsOld[$i], $authorsNew))
					{
						$rc->deleteAssociation($authorsOld[$i], $row->id, 'resources');
					}
				}
			}
		//}

		// If this is a child, add parent/child association
		$pid = JRequest::getInt('pid', 0, 'post');
		if ($isNew && $pid)
		{
			$this->_attachChild($row->id, $pid);
		}

		// Is this a standalone resource and we need to email approved submissions?
		if ($row->standalone == 1 && $this->config->get('email_when_approved'))
		{
			// If the state went from pending to published
			if ($row->published == 1 && $old->published == 3)
			{
				$this->_emailContributors($row, $this->database);
			}
		}

		// Redirect
		$this->setRedirect(
			$this->buildRedirectURL($pid),
			JText::_('Item successfully saved')
		);
	}

	/**
	 * Sends a message to all contributors on a resource
	 * 
	 * @param      object $row      ResourcesResource
	 * @param      object $database JDatabase
	 * @return     void
	 */
	private function _emailContributors($row, $database)
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . $this->_option . DS . 'helpers' . DS . 'helper.php');
		$helper = new ResourcesHelper($row->id, $database);
		$helper->getContributorIDs();

		$contributors = $helper->contributorIDs;

		if ($contributors && count($contributors) > 0)
		{
			// Email all the contributors
			$jconfig =& JFactory::getConfig();

			// E-mail "from" info
			$from = array();
			$from['email'] = $jconfig->getValue('config.mailfrom');
			$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_('SUBMISSIONS');

			// Message subject
			$subject = JText::_('EMAIL_SUBJECT');

			$juri =& JURI::getInstance();

			$base = $juri->base();
			$base = trim($base, '/');
			if (substr($base, -13) == 'administrator')
			{
				$base = substr($base, 0, strlen($base)-13);
			}
			$base = trim($base, '/');

			// Build message
			$message  = JText::sprintf('EMAIL_MESSAGE', $jconfig->getValue('config.sitename')) . "\r\n";
			$message .= $base . DS . 'resources' . DS . $row->id;

			// Send message
			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array('resources_submission_approved', $subject, $message, $from, $contributors, $this->_option)))
			{
				$this->setError(JText::_('Failed to message users.'));
			}
		}
	}

	/**
	 * Removes a resource
	 * Redirects to main listing
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array(0));

		// Ensure we have some IDs to work with
		if (count($ids) < 1)
		{
			echo ResourcesHtml::alert(JText::_('Select a resource to delete'));
			exit;
		}

		jimport('joomla.filesystem.folder');

		foreach ($ids as $id)
		{
			// Load resource info
			$row = new ResourcesResource($this->database);
			$row->load($id);

			// Get path and delete directories
			if ($row->path != '')
			{
				$listdir = $row->path;
			}
			else
			{
				// No stored path, derive from created date		
				$listdir = ResourcesHtml::build_path($row->created, $id, '');
			}

			// Build the path
			$path = ResourcesUtilities::buildUploadPath($listdir, '');

			// Check if the folder even exists
			if (!is_dir($path) or !$path)
			{
				$this->setError(JText::_('DIRECTORY_NOT_FOUND'));
			}
			else
			{
				// Attempt to delete the folder
				if (!JFolder::delete($path))
				{
					$this->setError(JText::_('UNABLE_TO_DELETE_DIRECTORY'));
				}
			}

			// Delete associations to the resource
			$row->deleteExistence();

			// Delete the resource
			$row->delete();
		}

		$pid = JRequest::getInt('pid', 0);

		// Redirect
		$this->setRedirect(
			$this->buildRedirectURL($pid)
		);
	}

	/**
	 * Changes a child resource's "grouping"
	 * 
	 * TODO: Phase out - used for learning modules only
	 * 
	 * @return     void
	 */
	public function regroupTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		$pid = JRequest::getInt('pid', 0);

		if (is_array($ids))
		{
			$id = $ids[0];
		}
		else
		{
			$id = 0;
		}

		// Ensure we have an ID to work with
		if (!$id)
		{
			echo ResourcesHtml::alert(JText::_('No resource ID found.'));
			exit;
		}

		// Ensure we have an ID to work with
		if (!$pid)
		{
			echo ResourcesHtml::alert(JText::_('No parent resource ID found.'));
			exit;
		}

		// Load the Association, set its new grouping, save
		$assoc = new ResourcesAssoc($this->database);
		$assoc->loadAssoc($pid, $id);
		$assoc->grouping = JRequest::getInt('grouping' . $id, 0, 'post');
		$assoc->store();

		// Redirect
		$this->setRedirect(
			$this->buildRedirectURL($pid)
		);
	}

	/**
	 * Sets the access level of a resource
	 * Redirects to main listing
	 * 
	 * @return     void
	 */
	public function accessTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id  = JRequest::getInt('id', 0);
		$pid = JRequest::getInt('pid', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			echo ResourcesHtml::alert(JText::_('No Resource ID found.'));
			exit;
		}

		// Choose access level
		switch ($this->_task)
		{
			case 'accesspublic':     $access = 0; break;
			case 'accessregistered': $access = 1; break;
			case 'accessspecial':    $access = 2; break;
			case 'accessprotected':  $access = 3; break;
			case 'accessprivate':    $access = 4; break;
			default: $access = 0; break;
		}

		// Load resource info
		$row = new ResourcesResource($this->database);
		$row->load($id);
		$row->access = $access;

		// Check and store changes
		if (!$row->check())
		{
			echo ResourcesHtml::alert($row->getError());
			exit;
		}
		if (!$row->store())
		{
			echo ResourcesHtml::alert($row->getError());
			exit;
		}

		// Redirect
		$this->setRedirect(
			$this->buildRedirectURL($pid)
		);
	}

	/**
	 * Sets the state of a resource
	 * Redirects to main listing
	 * 
	 * @return     void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		$publish = ($this->_task == 'publish') ? 1 : 0;

		// Incoming
		$pid = JRequest::getInt('pid', 0);
		$ids = JRequest::getVar('id', array());

		// Check for a resource
		if (count($ids) < 1)
		{
			echo ResourcesHtml::alert(JText::sprintf('Select a resource to %s',$this->_task));
			exit();
		}

		// Loop through all the IDs
		foreach ($ids as $id)
		{
			// Load the resource
			$resource = new ResourcesResource($this->database);
			$resource->load($id);

			// Only allow changes if the resource isn't checked out or
			// is checked out by the user requesting changes
			if (!$resource->checked_out || $resource->checked_out == $this->juser->get('id'))
			{
				$old = $resource->published;

				$resource->published = $publish;

				// If we're publishing, set the UP date
				if ($publish)
				{
					$resource->publish_up = date("Y-m-d H:i:s");
				}

				// Is this a standalone resource and we need to email approved submissions?
				if ($resource->standalone == 1 && $this->config->get('email_when_approved'))
				{
					// If the state went from pending to published
					if ($resource->published == 1 && $old == 3)
					{
						$this->_emailContributors($resource, $this->database);
					}
				}

				// Store and checkin the resource
				$resource->store();
				$resource->checkin();
			}
		}

		if ($publish == '-1')
		{
			$this->_message = JText::sprintf('%s Item(s) successfully Archived', count($ids));
		}
		elseif ($publish == '1')
		{
			$this->_message = JText::sprintf('%s Item(s) successfully Published', count($ids));
		}
		elseif ($publish == '0')
		{
			$this->_message = JText::sprintf('%s Item(s) successfully Unpublished', count($ids));
		}

		// Redirect
		$this->setRedirect(
			$this->buildRedirectURL($pid)
		);
	}

	/**
	 * Checks in a checked-out resource and redirects
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id  = JRequest::getInt('id', 0);
		$pid = JRequest::getInt('pid', 0);

		// Checkin the resource
		$row = new ResourcesResource($this->database);
		$row->bind($_POST);
		$row->checkin();

		// Redirect
		$this->setRedirect(
			$this->buildRedirectURL($pid)
		);
	}

	/**
	 * Resets the hit count of a resource
	 * Redirects to edit task for the resource
	 * 
	 * @return     void
	 */
	public function resethitsTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the hits, save, checkin
			$row = new ResourcesResource($this->database);
			$row->load($id);
			$row->hits = '0';
			$row->store();
			$row->checkin();

			$this->_message = JText::_('Successfully reset Hit count');
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id[]=' . $id,
			$this->_message
		);
	}

	/**
	 * Resets the rating of a resource
	 * Redirects to edit task for the resource
	 * 
	 * @return     void
	 */
	public function resetratingTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the ratings, save, checkin
			$row = new ResourcesResource($this->database);
			$row->load($id);
			$row->rating = '0.0';
			$row->times_rated = '0';
			$row->store();
			$row->checkin();

			$this->_message = JText::_('Successfully reset Rating');
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id[]=' . $id,
			$this->_message
		);
	}

	/**
	 * Resets the ranking of a resource
	 * Redirects to edit task for the resource
	 * 
	 * @return     void
	 */
	public function resetrankingTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the ratings, save, checkin
			$row = new ResourcesResource($this->database);
			$row->load($id);
			$row->ranking = '0';
			$row->store();
			$row->checkin();

			$this->_message = JText::_('Successfully reset Ranking');
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id[]=' . $id,
			$this->_message
		);
	}

	/**
	 * Checks-in one or more resources
	 * Redirects to the main listing
	 * 
	 * @return     void
	 */
	public function checkinTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array(0));

		// Make sure we have at least one ID 
		if (count($ids) < 1)
		{
			echo ResourcesHtml::alert(JText::_('Select a resource to check in'));
			exit;
		}

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the resource and check it in
			$row = new ResourcesResource($this->database);
			$row->load($id);
			$row->checkin();
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Reorders a resource child
	 * Redirects to parent resource's children lsiting
	 * 
	 * @return     void
	 */
	public function reorderTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array());
		$id = $id[0];
		$pid = JRequest::getInt('pid', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			echo ResourcesHtml::alert(JText::_('No resource ID found.'));
			exit;
		}

		// Ensure we have a parent ID to work with
		if (!$pid)
		{
			echo ResourcesHtml::alert(JText::_('No parent resource ID found.'));
			exit;
		}

		// Get the element moving down - item 1
		$resource1 = new ResourcesAssoc($this->database);
		$resource1->loadAssoc($pid, $id);

		// Get the element directly after it in ordering - item 2
		$resource2 = clone($resource1);
		$resource2->getNeighbor($this->_task);

		switch ($this->_task)
		{
			case 'orderup':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $resource2->ordering;
				$orderdn = $resource1->ordering;

				$resource1->ordering = $orderup;
				$resource2->ordering = $orderdn;
			break;

			case 'orderdown':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $resource1->ordering;
				$orderdn = $resource2->ordering;

				$resource1->ordering = $orderdn;
				$resource2->ordering = $orderup;
			break;
		}

		// Save changes
		$resource1->store();
		$resource2->store();

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid
		);
	}

	/**
	 * Builds the appropriate URL for redirction
	 * 
	 * @param      integer $pid Parent resource ID (optional)
	 * @return     string
	 */
	private function buildRedirectURL($pid=0)
	{
		$url  = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
		if ($pid)
		{
			if ($pid > 0)
			{
				$url .= '&task=children';
			}
			else
			{
				$url .= '&task=orphans';
			}
			$url .= '&pid=' . $pid;
		}

		return $url;
	}

	/**
	 * Builds a select list of users
	 * 
	 * @param      string  $name       Name of the select element
	 * @param      string  $active     Selected value
	 * @param      integer $nouser     Display an empty start option
	 * @param      string  $javascript Any JS to attach to the select element
	 * @param      string  $order      Field to order the users by
	 * @return     string
	 */
	private function userSelect($name, $active, $nouser=0, $javascript=NULL, $order='a.name')
	{
		$database =& JFactory::getDBO();

		$group_id = 'g.id';
		$aro_id = 'aro.id';

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$query = "SELECT a.id AS value, a.name AS text, g.title AS groupname"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__user_usergroup_map AS gm ON gm.user_id = a.id"	// map aro to group
			. "\n INNER JOIN #__usergroups AS g ON " . $group_id . " = gm.group_id"
			. "\n WHERE a.block = '0' AND " . $group_id . "=8"
			. "\n ORDER BY ". $order;
		}
		else
		{
			$query = "SELECT a.id AS value, a.name AS text, g.name AS groupname"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
			. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = " . $aro_id . ""	// map aro to group
			. "\n INNER JOIN #__core_acl_aro_groups AS g ON " . $group_id . " = gm.group_id"
			. "\n WHERE a.block = '0' AND " . $group_id . "=25"
			. "\n ORDER BY ". $order;
		}

		$database->setQuery($query);
		$result = $database->loadObjectList();

		if ($nouser)
		{
			$users[] = JHTML::_('select.option', '0', 'Do not change', 'value', 'text');
			$users = ($result && is_array($result)) ? array_merge($users, $result) : $users;
		}
		else
		{
			$users = $result;
		}

		return JHTML::_('select.genericlist', $users, $name, ' ' . $javascript, 'value', 'text', $active, false, false);
	}

	/**
	 * Gets the full name of a user from their ID #
	 * 
	 * @return     string
	 */
	public function authorTask()
	{
		$this->view->id   = JRequest::getVar('u', '');
		$this->view->role = JRequest::getVar('role', '');
		$rid = JRequest::getInt('rid', 0);

		if (is_numeric($this->view->id))
		{
			// Get the member's info
			ximport('Hubzero_User_Profile');
			$profile = new Hubzero_User_Profile();
			$profile->load($this->view->id);

			if (!$profile->get('name'))
			{
				$this->view->name  = $profile->get('givenName') . ' ';
				$this->view->name .= ($profile->get('middleName')) ? $profile->get('middleName') . ' ' : '';
				$this->view->name .= $profile->get('surname');
			}
			else
			{
				$this->view->name  = $profile->get('name');
			}
			$this->view->org = $profile->get('organization');
		}
		else 
		{
			include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'contributor.php');

			$rcc = new ResourcesContributor($this->database);

			$this->view->org  = '';
			$this->view->name = str_Replace('_', ' ', $this->view->id);
			$this->view->id   = $rcc->getUserId($this->view->name);
		}

		$row = new ResourcesResource($this->database);
		$row->load($rid);

		$rt = new ResourcesType($this->database);

		$this->view->roles = $rt->getRolesForType($row->type);

		$this->view->display();
	}
}

