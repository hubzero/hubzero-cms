<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Tables\Resource;
use Components\Resources\Tables\Type;
use Components\Resources\Tables\Review;
use Components\Resources\Tables\Assoc;
use Components\Resources\Tables\Contributor;
use Components\Resources\Helpers\Tags;
use Components\Resources\Helpers\Utilities;
use Components\Resources\Helpers\Html;
use Components\Resources\Helpers\Helper;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Route;
use Lang;
use App;

/**
 * Manage resource entries
 */
class Items extends AdminController
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

		//$this->registerTask('publish', 'state');
		//$this->registerTask('unpublish', 'state');

		$this->registerTask('add', 'edit');

		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');

		parent::execute();
	}

	/**
	 * Lists standalone resources
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'limit' => Request::getState(
				$this->_option . '.resources.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.resources.limitstart',
				'limitstart',
				0,
				'int'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.resources.search',
				'search',
				''
			)),
			'sort' => Request::getState(
				$this->_option . '.resources.sort',
				'filter_order',
				'created'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.resources.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			'status' => Request::getState(
				$this->_option . '.resources.status',
				'status',
				'all'
			),
			'type' => Request::getState(
				$this->_option . '.resources.type',
				'type',
				''
			)
		);

		$model = new Resource($this->database);

		// Get record count
		$this->view->total = $model->getItemCount($this->view->filters);

		// Get resources
		$this->view->rows = $model->getItems($this->view->filters);

		// Get <select> of types
		$rt = new Type($this->database);
		$this->view->types = $rt->getMajorTypes();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * List child resources of a parent resource
	 *
	 * @return  void
	 */
	public function childrenTask()
	{
		// Resource's parent ID
		//$this->view->pid = Request::getInt('pid', 0);
		$this->view->pid = Request::getState(
			$this->_option . '.children.pid',
			'pid',
			0,
			'int'
		);

		// Incoming
		$this->view->filters = array(
			'parent_id' => $this->view->pid,
			'limit' => Request::getState(
				$this->_option . '.children.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.children.limitstart',
				'limitstart',
				0,
				'int'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.children.search',
				'search',
				''
			)),
			'sort' => Request::getState(
				$this->_option . '.children.sort',
				'filter_order',
				'ordering'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.children.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'status' => Request::getState(
				$this->_option . '.children.status',
				'status',
				'all'
			)
		);

		// Get parent info
		$this->view->parent = new Resource($this->database);
		$this->view->parent->load($this->view->filters['parent_id']);

		// Record count
		$this->view->total = $this->view->parent->getItemChildrenCount($this->view->filters);

		// Get only children of this parent
		$this->view->rows = $this->view->parent->getItemChildren($this->view->filters);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * List "child" resources without any parent associations
	 *
	 * @return  void
	 */
	public function orphansTask()
	{
		$this->view->pid = '-1';

		// Incoming
		$this->view->filters = array(
			'parent_id' => $this->view->pid,
			'limit' => Request::getState(
				$this->_option . '.orphans.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.orphans.limitstart',
				'limitstart',
				0,
				'int'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.orphans.search',
				'search',
				''
			)),
			'sort' => Request::getState(
				$this->_option . '.orphans.sort',
				'filter_order',
				'created'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.orphans.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			'status' => Request::getState(
				$this->_option . '.orphans.status',
				'status',
				'all'
			)
		);

		$model = new Resource($this->database);

		// Record count
		$this->view->total = $model->getItemChildrenCount($this->view->filters);

		// Get only children of this parent
		$this->view->rows = $model->getItemChildren($this->view->filters);

		// Get sections for learning modules
		// TODO: Phase out all learning modules code
		$rt = new Type($this->database);
		$this->view->sections = $rt->getTypes(29);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('children')
			->display();
	}

	/**
	 * Show the ratings for a resource
	 *
	 * @return  void
	 */
	public function ratingsTask()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Do we have an ID to work with?
		if (!$id)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_MISSING_ID'));
		}
		else
		{
			$rr = new Review($this->database);
			$this->view->rows = $rr->getRatings($id);
			$this->view->id = $id;
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Show a form for adding a child to a resource
	 *
	 * @return  void
	 */
	public function addchildTask()
	{
		// Incoming
		$pid  = Request::getInt('pid', 0);
		$id   = Request::getVar('id', array(0));
		$step = Request::getVar('step', 1);

		if (!empty($id) && !$pid)
		{
			$pid = $id[0];
			$id = 0;
		}

		// Make sure we have a prent ID
		if (!$pid)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_RESOURCES_ERROR_MISSING_PARENT_ID'),
				'error'
			);
			return;
		}

		switch ($step)
		{
			case 1:
				$this->view->pid = $pid;

				// Get the available types
				$rt = new Type($this->database);
				$this->view->types = $rt->getTypes(30);

				// Load the parent resource
				$this->view->parent = new Resource($this->database);
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
				$method = Request::getVar('method', '');

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
					$cid = Request::getInt('childid', 0);
					if ($cid)
					{
						$child = new Resource($this->database);
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
							$this->setError(Lang::txt('COM_RESOURCES_ERROR_RESOURCE_NOT_FOUND'));

							// Get the available types
							$rt = new Type($this->database);
							$this->view->types = $rt->getTypes(30);

							// Load the parent resource
							$this->view->parent = new Resource($this->database);
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
						$this->setError(Lang::txt('COM_RESOURCES_ERROR_MISSING_ID'));

						// Get the available types
						$rt = new Type($this->database);
						$this->view->types = $rt->getTypes(30);

						// Load the parent resource
						$this->view->parent = new Resource($this->database);
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
	 * @param   integer  $id   ID of the child
	 * @param   integer  $pid  ID of the parent
	 * @return  void
	 */
	public function _attachChild($id, $pid)
	{
		// Make sure we have both parent and child IDs
		if (!$pid)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_RESOURCES_ERROR_MISSING_PARENT_ID'),
				'error'
			);
			return;
		}

		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid, false),
				Lang::txt('COM_RESOURCES_ERROR_MISSING_CHILD_ID'),
				'error'
			);
			return;
		}

		// Instantiate a Resources Assoc object
		$assoc = new Assoc($this->database);

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
			$this->setMessage($this->getError(), 'error');
		}
		else
		{
			// Redirect
			$this->setMessage(Lang::txt('COM_RESOURCES_ITEM_SAVED'));
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid, false),
			$this->getError(),
			'error'
		);
	}

	/**
	 * Removes a parent/child association
	 * Redirects to parent's children listing
	 *
	 * @return  void
	 */
	public function removechildTask()
	{
		// Incoming
		$ids = Request::getVar('id', array(0));
		$pid = Request::getInt('pid', 0);

		// Make sure we have a parent ID
		if (!$pid)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, true),
				Lang::txt('COM_RESOURCES_ERROR_MISSING_PARENT_ID'),
				'error'
			);
			return;
		}

		// Make sure we have children IDs
		if (!$ids || count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid, true),
				Lang::txt('COM_RESOURCES_ERROR_MISSING_CHILD_ID'),
				'error'
			);
			return;
		}

		$assoc = new Assoc($this->database);

		// Multiple IDs - loop through and delete them
		foreach ($ids as $id)
		{
			$assoc->delete($pid, $id);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid, true),
			Lang::txt('COM_RESOURCES_ITEMS_REMOVED', count($ids))
		);
	}

	/**
	 * Edit form for a new resource
	 *
	 * @return  void
	 */
	public function addTask()
	{
		return $this->editTask(1);
	}

	/**
	 * Edit form for a resource
	 *
	 * @param   integer  $isnew  Flag for editing (0) or creating new (1)
	 * @return  void
	 */
	public function editTask($isnew=0)
	{
		Request::setVar('hidemainmenu', 1);

		$this->view->isnew = $isnew;

		// Get the resource component config
		$this->view->rconfig = $this->config;

		// Push some needed styles to the tmeplate
		$this->css('resources.css');

		// Incoming resource ID
		$id = Request::getVar('id', array(0));
		if (is_array($id))
		{
			$id = (!empty($id) ? $id[0] : 0);
		}

		// Incoming parent ID - this determines if the resource is standalone or not
		$this->view->pid = Request::getInt('pid', 0);

		// Grab some filters for returning to place after editing
		$this->view->return = array();
		$this->view->return['type']   = Request::getVar('type', '');
		$this->view->return['sort']   = Request::getVar('sort', '');
		$this->view->return['status'] = Request::getVar('status', '');

		// Instantiate our resource object
		$this->view->row = new Resource($this->database);
		$this->view->row->load($id);

		// Fail if checked out not by 'me'
		if ($this->view->row->checked_out
		 && $this->view->row->checked_out <> User::get('id'))
		{
			$task = '';
			if ($this->view->pid)
			{
				$task = '&task=children&pid=' . $this->view->pid;
			}
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $task, false),
				Lang::txt('COM_RESOURCES_WARNING_CHECKED_OUT'),
				'notice'
			);
			return;
		}

		// Is this a new resource?
		if (!$id)
		{
			$this->view->row->created      = Date::toSql();
			$this->view->row->created_by   = User::get('id');
			$this->view->row->modified     = $this->database->getNullDate();
			$this->view->row->modified_by  = 0;
			$this->view->row->publish_up   = Date::toSql();
			$this->view->row->publish_down = Lang::txt('COM_RESOURCES_NEVER');
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
			$this->view->row->access = 0;
		}

		// Editing existing
		$this->view->row->checkout(User::get('id'));

		if (trim($this->view->row->publish_down) == '0000-00-00 00:00:00')
		{
			$this->view->row->publish_down = Lang::txt('COM_RESOURCES_NEVER');
		}

		// Get name of resource creator
		$creator = User::getInstance($this->view->row->created_by);

		$this->view->row->created_by_name = $creator->get('name');
		$this->view->row->created_by_name = ($this->view->row->created_by_name) ? $this->view->row->created_by_name : Lang::txt('Unknown');

		// Get name of last person to modify resource
		if ($this->view->row->modified_by)
		{
			$modifier = User::getInstance($this->view->row->modified_by);

			$this->view->row->modified_by_name = $modifier->get('name');
			$this->view->row->modified_by_name = ($this->view->row->modified_by_name) ? $this->view->row->modified_by_name : Lang::txt('Unknown');
		}
		else
		{
			$this->view->row->modified_by_name = '';
		}

		// Get params definitions
		$this->view->params  = new \Hubzero\Html\Parameter($this->view->row->params, dirname(dirname(__DIR__)) . DS . 'resources.xml');
		$this->view->attribs = new \Hubzero\Config\Registry($this->view->row->attribs);

		// Build selects of various types
		$rt = new Type($this->database);
		if ($this->view->row->standalone != 1)
		{
			$this->view->lists['type']         = Html::selectType($rt->getTypes(30), 'type', $this->view->row->type, '', '', '', '');
			$this->view->lists['logical_type'] = Html::selectType($rt->getTypes(28), 'logical_type', $this->view->row->logical_type, '[ none ]', '', '', '');
			$this->view->lists['sub_type']     = Html::selectType($rt->getTypes(30), 'logical_type', $this->view->row->logical_type, '[ none ]', '', '', '');
		}
		else
		{
			$this->view->lists['type']         = Html::selectType($rt->getTypes(27), 'type', $this->view->row->type, '', '', '', '');
			$this->view->lists['logical_type'] = Html::selectType($rt->getTypes(21), 'logical_type', $this->view->row->logical_type, '[ none ]', '', '', '');
		}

		// Build the <select> of admin users
		$this->view->lists['created_by'] = $this->userSelect('created_by', 0, 1);

		// Build the <select> for the group access
		$this->view->lists['access'] = Html::selectAccess($this->view->rconfig->get('accesses'), $this->view->row->access);

		// Is this a standalone resource?
		if ($this->view->row->standalone == 1)
		{
			$this->view->lists['tags'] = '';

			// Get groups
			$filters = array(
				'authorized' => 'admin',
				'fields'     => array('cn', 'description', 'published', 'gidNumber', 'type'),
				'type'       => array(1, 3),
				'sortby'     => 'description'
			);
			$groups = \Hubzero\User\Group::find($filters);

			// Build <select> of groups
			$this->view->lists['groups'] = Html::selectGroup($groups, $this->view->row->group_owner);

			include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'profile.php');
			include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'association.php');

			// Get all contributors
			$mp = new \Components\Members\Tables\Profile($this->database);
			$members = null; //$mp->getRecords(array('sortby'=>'surname DESC','limit'=>'all','search'=>'','show'=>''), true);

			// Get all contributors linked to this resource
			$authnames = array();
			if ($this->view->row->id)
			{
				$ma = new \Components\Members\Tables\Association($this->database);
				$sql = "SELECT n.uidNumber AS id, a.authorid, a.name, n.givenName, n.middleName, n.surname, a.role, a.organization
						FROM " . $ma->getTableName() . " AS a
						LEFT JOIN " . $mp->getTableName() . " AS n ON n.uidNumber=a.authorid
						WHERE a.subtable='resources'
						AND a.subid=" . $this->view->row->id . "
						ORDER BY a.ordering";
				$this->database->setQuery($sql);
				$authnames = $this->database->loadObjectList();

				// Get the tags on this item
				$tagger = new Tags($this->view->row->id);
				$this->view->lists['tags'] = $tagger->render('string');
			}

			// Build <select> of contributors
			$authorslist = new \Hubzero\Component\View(array(
				'name'   => $this->_controller,
				'layout' => 'authors'
			));
			$authorslist->authnames = $authnames;
			$authorslist->attribs   = $this->view->attribs;
			$authorslist->option    = $this->_option;
			$authorslist->roles     = $rt->getRolesForType($this->view->row->type);

			$this->view->lists['authors'] = $authorslist->loadTemplate();
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
	 * Saves a resource
	 * Redirects to main listing
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Initiate extended database class
		$row = new Resource($this->database);
		if (!$row->bind($_POST))
		{
			throw new Exception($row->getError(), 400);
		}

		$isNew = 0;
		if ($row->id < 1)
		{
			$isNew = 1;
		}

		if ($isNew)
		{
			// New entry
			$row->created    = $row->created    ? $row->created    : Date::toSql();
			$row->created_by = $row->created_by ? $row->created_by : User::get('id');
			$row->access     = 0;
		}
		else
		{
			$old = new Resource($this->database);
			$old->load($row->id);

			$created_by_id = Request::getInt('created_by_id', 0);

			// Updating entry
			$row->modified    = Date::toSql();
			$row->modified_by = User::get('id');

			if ($created_by_id)
			{
				$row->created_by = $row->created_by ? $row->created_by : $created_by_id;
			}
			else
			{
				$row->created_by = $row->created_by ? $row->created_by : User::get('id');
			}
		}

		// publish up
		$row->publish_up = Date::of($row->publish_up, Config::get('offset'))->toSql();

		// publish down
		if (!$row->publish_down || trim($row->publish_down) == '0000-00-00 00:00:00' || trim($row->publish_down) == 'Never')
		{
			$row->publish_down = '0000-00-00 00:00:00';
		}
		else
		{
			$row->publish_down = Date::of($row->publish_down, Config::get('offset'))->toSql();
		}

		// Get parameters
		$params = Request::getVar('params', array(), 'post');
		if (is_array($params))
		{
			$txt = new \Hubzero\Config\Registry('');
			foreach ($params as $k => $v)
			{
				$txt->set($k, $v);
			}
			$row->params = $txt->toString();
		}

		// Get attributes
		$attribs = Request::getVar('attrib', array(), 'post');
		if (is_array($attribs))
		{
			$txta = new \Hubzero\Config\Registry('');
			foreach ($attribs as $k => $v)
			{
				if ($k == 'timeof')
				{
					if (strtotime(trim($v)) === false)
					{
						$v = NULL;
					}

					$v = trim($v)
						? Date::of($v, Config::get('offset'))->toSql()
						: NULL;
				}
				$txta->set($k, $v);
			}
			$row->attribs = $txta->toString();
		}

		// Get custom areas, add wrappers, and compile into fulltxt
		if (isset($_POST['nbtag']))
		{
			$type = new Type($this->database);
			$type->load($row->type);

			include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
			$elements = new \Components\Resources\Models\Elements(array(), $type->customFields);
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
					throw new Exception(Lang::txt('RESOURCES_REQUIRED_FIELD_CHECK', $fields[$tagname]->label), 500);
				}

				$found[] = $tagname;
			}

			foreach ($fields as $field)
			{
				if (!in_array($field->name, $found) && $field->required)
				{
					$found[] = $field->name;
					$this->setError(Lang::txt('COM_CONTRIBUTE_REQUIRED_FIELD_CHECK', $field->label));
				}
			}
		}

		// Code cleaner for xhtml transitional compliance
		if ($row->type != 7)
		{
			$row->introtext = str_replace('<br>', '<br />', $row->introtext);
			$row->fulltxt   = str_replace('<br>', '<br />', $row->fulltxt);
		}

		// Check content
		if (!$row->check())
		{
			throw new Exception($row->getError(), 500);
		}

		// Store content
		if (!$row->store())
		{
			throw new Exception($row->getError(), 500);
		}

		// Checkin resource
		$row->checkin();

		// Rename the temporary upload directory if it exist
		$tmpid = Request::getInt('tmpid', 0, 'post');
		if ($tmpid != Html::niceidformat($row->id))
		{
			// Build the full paths
			$path    = Html::dateToPath($row->created);
			$dir_id  = Html::niceidformat($row->id);

			$tmppath = Utilities::buildUploadPath($path . DS . $tmpid);
			$newpath = Utilities::buildUploadPath($path . DS . $dir_id);

			// Attempt to rename the temp directory
			if (\Filesystem::exists($tmppath))
			{
				$result = \Filesystem::move($tmppath, $newpath);
				if ($result !== true)
				{
					$this->setError($result);
				}
			}

			$row->path = str_replace($tmpid, Html::niceidformat($row->id), $row->path);
			$row->store();
		}

		// Incoming tags
		$tags = Request::getVar('tags', '', 'post');

		// Save the tags
		$rt = new Tags($row->id);
		$rt->setTags($tags, User::get('id'), 1, 1);

		// Incoming authors
		if ($row->type != 7)
		{
			$authorsOldstr = Request::getVar('old_authors', '', 'post');
			$authorsNewstr = Request::getVar('new_authors', '', 'post');
			if (!$authorsNewstr)
			{
				$authorsNewstr = $authorsOldstr;
			}

			include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'contributor.php');

			$authorsNew = explode(',', $authorsNewstr);
			$authorsOld = explode(',', $authorsOldstr);

			// We have either a new ordering or new authors or both
			if ($authorsNewstr)
			{
				for ($i=0, $n=count($authorsNew); $i < $n; $i++)
				{
					$rc = new Contributor($this->database);
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
					$rc->role         = trim(Request::getVar($authorsNew[$i] . '_role', ''));
					$rc->name         = trim(Request::getVar($authorsNew[$i] . '_name', ''));
					$rc->organization = trim(Request::getVar($authorsNew[$i] . '_organization', ''));

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
				$rc = new Contributor($this->database);

				for ($i=0, $n=count($authorsOld); $i < $n; $i++)
				{
					if (!in_array($authorsOld[$i], $authorsNew))
					{
						$rc->deleteAssociation($authorsOld[$i], $row->id, 'resources');
					}
				}
			}
		}

		// If this is a child, add parent/child association
		$pid = Request::getInt('pid', 0, 'post');
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
		App::redirect(
			$this->buildRedirectURL($pid),
			Lang::txt('COM_RESOURCES_ITEM_SAVED')
		);
	}

	/**
	 * Sends a message to all contributors on a resource
	 *
	 * @param      object $row      Resource
	 * @param      object $database JDatabase
	 * @return     void
	 */
	private function _emailContributors($row, $database)
	{
		include_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'helper.php');

		$helper = new Helper($row->id, $database);
		$helper->getContributorIDs();

		$contributors = $helper->contributorIDs;

		if ($contributors && count($contributors) > 0)
		{
			// E-mail "from" info
			$from = array();
			$from['email'] = Config::get('mailfrom');
			$from['name']  = Config::get('sitename') . ' ' . Lang::txt('COM_RESOURCES_SUBMISSIONS');

			// Message subject
			$subject = Lang::txt('COM_RESOURCES_EMAIL_SUBJECT');

			$base = Request::base();
			$base = trim($base, '/');
			if (substr($base, -13) == 'administrator')
			{
				$base = substr($base, 0, strlen($base)-13);
			}
			$base = trim($base, '/');

			// Build message
			$message  = Lang::txt('COM_RESOURCES_EMAIL_MESSAGE', Config::get('sitename')) . "\r\n";
			$message .= $base . DS . 'resources' . DS . $row->id;

			// Send message
			if (!Event::trigger('xmessage.onSendMessage', array('resources_submission_approved', $subject, $message, $from, $contributors, $this->_option)))
			{
				$this->setError(Lang::txt('COM_RESOURCES_ERROR_FAILED_TO_MESSAGE_USERS'));
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
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array(0));

		// Ensure we have some IDs to work with
		if (count($ids) < 1)
		{
			$this->setMessage(Lang::txt('COM_RESOURCES_NO_ITEM_SELECTED'));
			return $this->cancelTask();
		}

		foreach ($ids as $id)
		{
			// Load resource info
			$row = new Resource($this->database);
			$row->load($id);

			// Get path and delete directories
			if ($row->path != '')
			{
				$listdir = $row->path;
			}
			else
			{
				// No stored path, derive from created date
				$listdir = Html::build_path($row->created, $id, '');
			}

			// Build the path
			$path = Utilities::buildUploadPath($listdir, '');

			$base  = PATH_APP . '/' . trim($this->config->get('webpath', '/site/resources'), '/');
			$baseY = $base . '/'. Date::of($row->created)->format("Y");
			$baseM = $baseY . '/' . Date::of($row->created)->format("m");

			// Check if the folder even exists
			if (!is_dir($path) or !$path)
			{
				$this->setError(Lang::txt('COM_RESOURCES_ERROR_DIRECTORY_NOT_FOUND'));
			}
			else
			{
				if ($path == $base
				 || $path == $baseY
				 || $path == $baseM)
				{
					$this->setError(Lang::txt('COM_RESOURCES_ERROR_DIRECTORY_NOT_FOUND'));
				}
				else
				{
					// Attempt to delete the folder
					if (!\Filesystem::deleteDirectory($path))
					{
						$this->setError(Lang::txt('COM_RESOURCES_ERROR_UNABLE_TO_DELETE_DIRECTORY'));
					}
				}
			}

			// Delete associations to the resource
			$row->deleteExistence();

			// Delete the resource
			$row->delete();
		}

		$pid = Request::getInt('pid', 0);

		// Redirect
		App::redirect(
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
		Request::checkToken(['get', 'post']);

		// Incoming
		$id  = Request::getInt('id', 0);
		$pid = Request::getInt('pid', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setMessage(Lang::txt('COM_RESOURCES_ERROR_MISSING_ID'));
			return $this->cancelTask();
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
		$row = new Resource($this->database);
		$row->load($id);
		$row->access = $access;

		// Check and store changes
		if (!$row->check())
		{
			$this->setMessage($row->getError());
			return $this->cancelTask();
		}
		if (!$row->store())
		{
			$this->setMessage($row->getError());
			return $this->cancelTask();
		}

		// Redirect
		App::redirect(
			$this->buildRedirectURL($pid)
		);
	}

	/**
	 * Sets the state of a resource to published
	 * Redirects to main listing
	 *
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Sets the state of a resource to unpublished
	 * Redirects to main listing
	 *
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Sets the state of a resource to archived
	 * Redirects to main listing
	 *
	 * @return     void
	 */
	public function archiveTask()
	{
		$this->stateTask(-1);
	}

	/**
	 * Sets the state of a resource
	 * Redirects to main listing
	 *
	 * @return     void
	 */
	public function stateTask($publish=1)
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$pid = Request::getInt('pid', 0);
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for a resource
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_RESOURCES_ERROR_SELECT_TO', $this->_task),
				'error'
			);
			return;
		}

		$i = 0;

		// Loop through all the IDs
		foreach ($ids as $id)
		{
			// Load the resource
			$resource = new Resource($this->database);
			$resource->load($id);

			// Only allow changes if the resource isn't checked out or
			// is checked out by the user requesting changes
			if (!$resource->checked_out || $resource->checked_out == Config::get('id'))
			{
				$old = $resource->published;

				$resource->published = $publish;

				// If we're publishing, set the UP date
				if ($publish)
				{
					$resource->publish_up = Date::toSql();
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

				$i++;
			}
		}

		if ($i)
		{
			if ($publish == -1)
			{
				$this->setMessage(Lang::txt('COM_RESOURCES_ITEMS_ARCHIVED', $i));
			}
			elseif ($publish == 1)
			{
				$this->setMessage(Lang::txt('COM_RESOURCES_ITEMS_PUBLISHED', $i));
			}
			elseif ($publish == 0)
			{
				$this->setMessage(Lang::txt('COM_RESOURCES_ITEMS_UNPUBLISHED', $i));
			}
		}

		// Redirect
		App::redirect(
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
		Request::checkToken();

		// Incoming
		$id  = Request::getInt('id', 0);
		$pid = Request::getInt('pid', 0);

		// Checkin the resource
		$row = new Resource($this->database);
		$row->bind($_POST);
		$row->checkin();

		// Redirect
		App::redirect(
			$this->buildRedirectURL($pid)
		);
	}

	/**
	 * Resets the hit count of a resource
	 * Redirects to edit task for the resource
	 *
	 * @return  void
	 */
	public function resethitsTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the hits, save, checkin
			$row = new Resource($this->database);
			$row->load($id);
			$row->hits = '0';
			$row->store();
			$row->checkin();

			$this->setMessage(Lang::txt('COM_RESOURCES_HITS_RESET'));
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $id, false)
		);
	}

	/**
	 * Resets the rating of a resource
	 * Redirects to edit task for the resource
	 *
	 * @return  void
	 */
	public function resetratingTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the ratings, save, checkin
			$row = new Resource($this->database);
			$row->load($id);
			$row->rating = '0.0';
			$row->times_rated = '0';
			$row->store();
			$row->checkin();

			$this->setMessage(Lang::txt('COM_RESOURCES_RATING_RESET'));
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $id, false)
		);
	}

	/**
	 * Resets the ranking of a resource
	 * Redirects to edit task for the resource
	 *
	 * @return  void
	 */
	public function resetrankingTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the ratings, save, checkin
			$row = new Resource($this->database);
			$row->load($id);
			$row->ranking = '0';
			$row->store();
			$row->checkin();

			$this->setMessage(Lang::txt('COM_RESOURCES_RANKING_RESET'));
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $id, false)
		);
	}

	/**
	 * Checks-in one or more resources
	 * Redirects to the main listing
	 *
	 * @return  void
	 */
	public function checkinTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array(0));

		// Make sure we have at least one ID
		if (count($ids))
		{
			// Loop through the IDs
			foreach ($ids as $id)
			{
				// Load the resource and check it in
				$row = new Resource($this->database);
				$row->load($id);
				$row->checkin();
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Reorders a resource child
	 * Redirects to parent resource's children lsiting
	 *
	 * @return  void
	 */
	public function reorderTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getVar('id', array());
		$id = $id[0];
		$pid = Request::getInt('pid', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setMessage(Lang::txt('COM_RESOURCES_ERROR_MISSING_ID'));
			return $this->cancelTask();
		}

		// Ensure we have a parent ID to work with
		if (!$pid)
		{
			$this->setMessage(Lang::txt('COM_RESOURCES_ERROR_MISSING_PARENT_ID'));
			return $this->cancelTask();
		}

		// Get the element moving down - item 1
		$resource1 = new Assoc($this->database);
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
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid, false)
		);
	}

	/**
	 * Builds the appropriate URL for redirction
	 *
	 * @param   integer  $pid  Parent resource ID (optional)
	 * @return  string
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

		return Route::url($url, false);
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
		$database = \App::get('db');

		$group_id = 'g.id';
		$aro_id = 'aro.id';

		$query = "SELECT a.id AS value, a.name AS text, g.title AS groupname"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__user_usergroup_map AS gm ON gm.user_id = a.id"	// map aro to group
			. "\n INNER JOIN #__usergroups AS g ON g.id = gm.group_id"
			. "\n WHERE a.block = '0' AND g.title='Super Users'"
			. "\n ORDER BY ". $order;

		$database->setQuery($query);
		$result = $database->loadObjectList();

		if (!$result)
		{
			$result = array();
		}

		if ($nouser)
		{
			$users[] = \Html::select('option', '0', Lang::txt('COM_RESOURCES_DO_NOT_CHANGE'), 'value', 'text');
			$users = array_merge($users, $result);
		}
		else
		{
			$users = $result;
		}

		return \Html::select('genericlist', $users, $name, ' ' . $javascript, 'value', 'text', $active, false, false);
	}

	/**
	 * Gets the full name of a user from their ID #
	 *
	 * @return     string
	 */
	public function authorTask()
	{
		$this->view->id   = Request::getVar('u', '');
		$this->view->role = Request::getVar('role', '');
		$rid = Request::getInt('rid', 0);

		// Get the member's info
		$profile = new \Hubzero\User\Profile();
		$profile->load($this->view->id);

		if (!is_object($profile) || !$profile->get('uidNumber'))
		{
			$this->database->setQuery("SELECT id FROM `#__users` WHERE `name`=" . $this->database->Quote($this->view->id));
			if ($id = $this->database->loadResult())
			{
				$profile->load($id);
			}
		}

		if (is_object($profile) && $profile->get('uidNumber'))
		{
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
			$this->view->id  = $profile->get('uidNumber');
		}
		else
		{
			$this->view->name = null;

			include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'contributor.php');

			$rcc = new Contributor($this->database);

			if (is_numeric($this->view->id))
			{
				$this->database->setQuery("SELECT name, organization FROM `#__author_assoc` WHERE authorid=" . $this->database->Quote($this->view->id) . " LIMIT 1");
				$author = $this->database->loadObject();

				if (is_object($author) && $author->name)
				{
					$this->view->name = $author->name;
					$this->view->org  = $author->organization;
				}
			}

			if (!$this->view->name)
			{
				$this->view->org  = '';
				$this->view->name = str_replace('_', ' ', $this->view->id);
				$this->view->id   = $rcc->getUserId($this->view->name);
			}
		}

		$row = new Resource($this->database);
		$row->load($rid);

		$rt = new Type($this->database);

		$this->view->roles = $rt->getRolesForType($row->type);

		$this->view->display();
	}

	/**
	 * Check resource paths
	 */
	public function checkTask()
	{
		// hold missing
		$this->view->missing = array();
		$this->view->warning = array();
		$this->view->good    = array();

		// get all resources
		$db  = \App::get('db');
		$sql = "SELECT id, title, type, path FROM `#__resources` ORDER BY id";
		$db->setQuery($sql);
		$results = $db->loadObjectList();

		// get upload path
		$params = Component::params('com_resources');
		$base = $params->get('uploadpath', '/site/resources');
		$base = PATH_APP . DS . trim($base, DS) . DS;

		// loop through each resource
		foreach ($results as $result)
		{
			// make sure we have a path
			if (isset($result->path) && $result->path != '')
			{
				// trim our result
				$path = ltrim($result->path, DS);
				$path = trim($path);

				if (preg_match('/^(https?:|mailto:|ftp:|gopher:|news:)/i', $path)
				 || substr($path, 0, strlen('://')) == '://')
				{
					$this->view->good[] = '<span style="color: blue">#' . $result->id . ': ' . Lang::txt('COM_RESOURCES_NO_PROBLEMS_FOUND', $path) . '</span>';
					continue;
				}

				// checks
				try
				{
					if (is_dir($path))
					{
						$this->view->warning[] = '<span style="color: yellow;">#' . $result->id . ': ' . Lang::txt('COM_RESOURCES_PATH_IS_DIRECTORY') . ' ' . $path . '</span>';
					}
					elseif (\JURI::isInternal($path) && !file_exists($base . $path) && $result->type != 12)
					{
						$this->view->missing[] = '<span style="color: red">#' . $result->id . ': ' . Lang::txt('COM_RESOURCES_MISSING_RESOURCE_AT', $base . $path) . '</span>';
					}
					else
					{
						$this->view->good[] = '<span style="color: green">#' . $result->id . ': ' . Lang::txt('COM_RESOURCES_NO_PROBLEMS_FOUND', $path) . '</span>';
					}
				}
				catch (\Exception $e)
				{
					$this->view->warning[] = '<span style="color: yellow;">#' . $result->id . ': ' . $path . '</span>';
				}
			}
		}

		$this->view->display();
	}
}

