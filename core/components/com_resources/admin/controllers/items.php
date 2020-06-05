<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin\Controllers;

require_once Component::path('com_resources') . '/helpers/badges.php';

use Components\Members\Models\Member;
use Components\Resources\Models\Entry;
use Components\Resources\Models\Type;
use Components\Resources\Models\Association;
use Components\Resources\Models\Rating;
use Components\Resources\Models\Author;
use Components\Resources\Models\License;
use Components\Resources\Helpers\Tags;
use Components\Resources\Helpers\Utilities;
use Components\Resources\Helpers\Badges;
use Components\Resources\Helpers\Html;
use Components\Resources\Helpers\Helper;
use Hubzero\Component\AdminController;
use Hubzero\Utility\Str;
use Request;
use Config;
use Route;
use Event;
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
	 * @return  void
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
		$filters = array(
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
			),
			'license' => Request::getState(
				$this->_option . '.resources.license',
				'license',
				'all'
			)
		);

		$query = Entry::all()
			->whereEquals('standalone', 1);

		if ($filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$query->whereEquals('id', (int)$filters['search']);
			}
			else
			{
				$filters['search'] = strtolower((string)$filters['search']);

				$query->whereLike('title', $filters['search'], 1)
						->orWhereLike('fulltxt', $filters['search'], 1)
						->resetDepth();
			}
		}

		if ($filters['type'])
		{
			$query->whereEquals('type', (int)$filters['type']);
		}

		if ($filters['status'] != 'all')
		{
			$query->whereEquals('published', (int)$filters['status']);
		}

		if ($filters['license'] != 'all')
		{
			$query->whereEquals('license', $filters['license']);
		}

		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Get major types
		$types = Type::getMajorTypes();

		$licenses = License::all()
			->order('text', 'asc')
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->set('types', $types)
			->set('licenses', $licenses)
			->display();
	}

	/**
	 * List child resources of a parent resource
	 *
	 * @return  void
	 */
	public function childrenTask()
	{
		// Resource's parent ID
		$pid = Request::getState(
			$this->_option . '.children.pid',
			'pid',
			0,
			'int'
		);

		// Incoming
		$filters = array(
			'parent_id' => $pid,
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
		$parent = Entry::oneOrFail((int)$filters['parent_id']);

		$query = $parent->children();

		if ($filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$query->whereEquals('id', (int)$filters['search']);
			}
			else
			{
				$filters['search'] = strtolower((string)$filters['search']);

				$query->whereLike('title', $filters['search'], 1)
						->orWhereLike('fulltxt', $filters['search'], 1)
						->resetDepth();
			}
		}

		if ($filters['status'] != 'all')
		{
			$query->whereEquals('published', (int)$filters['status']);
		}

		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('pid', $pid)
			->set('parent', $parent)
			->set('filters', $filters)
			->set('rows', $rows)
			->display();
	}

	/**
	 * List "child" resources without any parent associations
	 *
	 * @return  void
	 */
	public function orphansTask()
	{
		// Incoming
		$filters = array(
			'parent_id' => -1,
			'search' => urldecode(Request::getState(
				$this->_option . '.orphans.search',
				'search',
				''
			)),
			'sort' => Request::getState(
				$this->_option . '.orphans.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.orphans.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'status' => Request::getState(
				$this->_option . '.orphans.status',
				'status',
				'all'
			)
		);

		$associations = Association::all()
			->select('child_id')
			->group('child_id');

		// Get parent info
		$query = Entry::all()
			->whereEquals('standalone', 0)
			->whereRaw('id NOT IN (' . $associations->toString() . ')');

		if ($filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$query->whereEquals('id', (int)$filters['search']);
			}
			else
			{
				$filters['search'] = strtolower((string)$filters['search']);

				$query->whereLike('title', $filters['search'], 1)
						->orWhereLike('fulltxt', $filters['search'], 1)
						->resetDepth();
			}
		}

		if ($filters['status'] != 'all')
		{
			$query->whereEquals('published', (int)$filters['status']);
		}

		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
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
		require_once dirname(dirname(__DIR__)) . '/models/rating.php';

		// Incoming
		$id = Request::getInt('id', 0);
		$rows = array();

		// Do we have an ID to work with?
		if (!$id)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_MISSING_ID'));
		}
		else
		{
			$rows = Rating::all()
				->whereEquals('resource_id', $id)
				->rows();
		}

		// Output the HTML
		$this->view
			->set('id', $id)
			->set('rows', $rows)
			->setErrors($this->getErrors())
			->display();
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
		$id   = Request::getArray('id', array(0));
		$step = Request::getInt('step', 1);

		if (!empty($id) && !$pid)
		{
			$pid = $id[0];
			$id = 0;
		}

		// Make sure we have a prent ID
		if (!$pid)
		{
			Notify::warning(Lang::txt('COM_RESOURCES_ERROR_MISSING_PARENT_ID'));
			return $this->cancelTask();
		}

		// Get the available types
		$types = Type::all()
			->whereEquals('category', 30)
			->rows();

		switch ($step)
		{
			case 1:
				// Load the parent resource
				$parent = Entry::oneOrFail($pid);

				// Output the HTML
				$this->view
					->set('pid', $pid)
					->set('parent', $parent)
					->set('types', $types)
					->display();
			break;

			case 2:
				// Get the creation method
				$method = Request::getString('method', '');

				if ($method == 'create')
				{
					// We're starting from scratch
					$this->editTask(1);
				}
				elseif ($method == 'existing')
				{
					// We're just linking up an existing resource
					// Get the child ID we're linking
					$cid = Request::getInt('childid', 0);

					if ($cid)
					{
						$child = Entry::oneOrFail($cid);

						if ($child && $child->title != '')
						{
							// Link 'em up!
							$this->_attachChild($cid, $pid);
						}
						else
						{
							// No child ID! Throw an error and present the form from the previous step
							$this->setError(Lang::txt('COM_RESOURCES_ERROR_RESOURCE_NOT_FOUND'));

							// Load the parent resource
							$parent = Entry::oneOrFail($pid);

							$this->view
								->set('pid', $pid)
								->set('types', $types)
								->set('parent', $parent)
								->setErrors($this->getErrors())
								->display();
						}
					}
					else
					{
						// No child ID! Throw an error and present the form from the previous step
						$this->setError(Lang::txt('COM_RESOURCES_ERROR_MISSING_ID'));

						// Load the parent resource
						$parent = Entry::oneOrFail($pid);

						// Output the HTML
						$this->view
							->set('pid', $pid)
							->set('types', $types)
							->set('parent', $parent)
							->setErrors($this->getErrors())
							->display();
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
			Notify::warning(Lang::txt('COM_RESOURCES_ERROR_MISSING_PARENT_ID'));
			return $this->cancelTask();
		}

		if (!$id)
		{
			Notify::warning(Lang::txt('COM_RESOURCES_ERROR_MISSING_CHILD_ID'));

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid, false)
			);
		}

		// Instantiate a Resources Assoc object
		$assoc = Association::blank()
			->set(array(
				'parent_id' => $pid,
				'child_id'  => $id,
				'grouping'  => 0
			));

		if (!$assoc->save())
		{
			Notify::error($this->getError());
		}
		else
		{
			// Redirect
			Notify::success(Lang::txt('COM_RESOURCES_ITEM_SAVED'));
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid, false)
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
		$ids = Request::getArray('id', array(0));
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
			Notify::warning(Lang::txt('COM_RESOURCES_ERROR_MISSING_CHILD_ID'));

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid, true)
			);
		}

		// Multiple IDs - loop through and delete them
		$removed = 0;
		foreach ($ids as $id)
		{
			$assoc = Association::oneByRelationship($pid, $id);

			if (!$assoc->destroy())
			{
				Notify::error($assoc->getError());
				continue;
			}

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_RESOURCES_ITEMS_REMOVED', $removed));
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=children&pid=' . $pid, true)
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

		// Push some needed styles to the tmeplate
		$this->css('resources.css');

		// Incoming resource ID
		$id = Request::getArray('id', array(0));
		if (is_array($id))
		{
			$id = (!empty($id) ? $id[0] : 0);
		}

		// Incoming parent ID - this determines if the resource is standalone or not
		$pid = Request::getInt('pid', 0);

		// Grab some filters for returning to place after editing
		$return = array();
		$return['type']   = Request::getString('type', '');
		$return['sort']   = Request::getString('sort', '');
		$return['status'] = Request::getString('status', '');

		// Instantiate our resource object
		$row = Entry::oneOrNew($id);

		// Fail if checked out not by 'me'
		if ($row->isCheckedOut())
		{
			Notify::warning(Lang::txt('COM_RESOURCES_WARNING_CHECKED_OUT'));

			$task = '';
			if ($pid)
			{
				$task = '&task=children&pid=' . $pid;
			}
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $task, false)
			);
			return;
		}

		// Is this a new resource?
		if ($row->isNew())
		{
			$row->set('created', Date::toSql());
			$row->set('created_by', User::get('id'));
			$row->set('publish_up', Date::toSql());

			// Was a prent ID found?
			// If yes, then set this up as a child resource.
			if ($pid)
			{
				$row->set('published', Entry::STATE_PUBLISHED);
				$row->set('standalone', 0);
			}
			else
			{
				$row->set('published', Entry::STATE_PENDING); // default to "new" status
				$row->set('standalone', 1);
			}
			$row->set('access', 0);
		}
		else
		{
			// Editing existing
			$row->checkout(User::get('id'));
		}

		if (!$row->get('publish_down') || $row->get('publish_down') == '0000-00-00 00:00:00')
		{
			$row->set('publish_down', Lang::txt('COM_RESOURCES_NEVER'));
		}

		// Get params definitions
		$params = new \Hubzero\Html\Parameter($row->get('params'), dirname(dirname(__DIR__)) . DS . 'config' . DS . 'entry.xml');

		// Build selects of various types
		if ($row->standalone != 1)
		{
			$lists['type']         = Html::selectType(Type::all()->whereEquals('category', 30)->order('type', 'asc')->rows(), 'fields[type]', $row->get('type'), 'type', '', '', '', '');
			$lists['logical_type'] = Html::selectType(Type::all()->whereEquals('category', 28)->order('type', 'asc')->rows(), 'fields[logical_type]', $row->get('logical_type'), 'logical_type', '[ none ]', '', '', '');
			$lists['sub_type']     = Html::selectType(Type::all()->whereEquals('category', 30)->order('type', 'asc')->rows(), 'fields[logical_type]', $row->get('logical_type'), 'sub_type', '[ none ]', '', '', '');
		}
		else
		{
			$lists['type']         = Html::selectType(Type::all()->whereEquals('category', 27)->order('type', 'asc')->rows(), 'fields[type]', $row->get('type'), 'type', '', '', '', '');
			$lists['logical_type'] = Html::selectType(Type::all()->whereEquals('category', 21)->order('type', 'asc')->rows(), 'fields[logical_type]', $row->get('logical_type'), 'logical_type', '[ none ]', '', '', '');
		}

		// Build the <select> of admin users
		$lists['created_by'] = $this->userSelect('created_by', 0, 1);

		// Build the <select> for the group access
		$lists['access'] = Html::selectAccess($this->config->get('accesses'), $row->access);

		// Is this a standalone resource?
		if ($row->standalone == 1)
		{
			$lists['tags'] = '';

			// Get groups
			$filters = array(
				'authorized' => 'admin',
				'fields'     => array('cn', 'description', 'published', 'gidNumber', 'type'),
				'type'       => array(1, 3),
				'sortby'     => 'description'
			);
			$groups = \Hubzero\User\Group::find($filters);

			// Build <select> of groups
			$lists['groups'] = Html::selectGroup($groups, $row->group_owner->get('cn'));

			// Get all contributors linked to this resource
			$authnames = array();

			if ($row->id)
			{
				/*$sql = "SELECT n.id, a.authorid, a.name, n.givenName, n.middleName, n.surname, a.role, a.organization
						FROM `#__author_assoc` AS a
						LEFT JOIN `#__users` AS n ON n.id=a.authorid
						WHERE a.subtable='resources'
						AND a.subid=" . $row->id . "
						ORDER BY a.ordering";
				$this->database->setQuery($sql);
				$authnames = $this->database->loadObjectList();*/
				$authnames = $row->authors()
					->ordered()
					->rows();

				// Get the tags on this item
				$tagger = new Tags($row->id);
				$lists['tags'] = $tagger->render('string');

				// Get the badges on this item
				$badges = new Badges([
					'scope' => 'resources',
					'scopeId' => $row->id
				]);
				$lists['badges'] = $badges->render('string');
			}

			// Build <select> of contributors
			$authorslist = new \Hubzero\Component\View(array(
				'name'   => $this->_controller,
				'layout' => 'authors'
			));
			$authorslist->authnames = $authnames;
			$authorslist->attribs   = $row->attribs;
			$authorslist->option    = $this->_option;
			$authorslist->roles     = $row->type->roles()->rows();

			$lists['authors'] = $authorslist->loadTemplate();
		}

		$licenses = License::all()
			->order('text', 'asc')
			->rows();

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('lists', $lists)
			->set('pid', $pid)
			->set('isnew', $isnew)
			->set('rconfig', $this->config)
			->set('params', $params)
			->set('return', $return)
			->set('licenses', $licenses)
			->setLayout('edit')
			->setErrors($this->getErrors())
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

		$fields = Request::getArray('fields', array(), 'post');

		// Initiate extended database class
		$row = Entry::oneOrNew(Request::getInt('id', 0, 'post'));

		$old = clone $row;

		$row->set($fields);

		$isNew = 0;
		if ($row->get('id') <= 1)
		{
			$isNew = 1;
		}

		$old = Entry::blank();

		if ($isNew)
		{
			// New entry
			$row->set('access', 0);
		}
		else
		{
			$created_by_id = Request::getInt('created_by_id', 0);

			// Updating entry
			if ($created_by_id)
			{
				$row->set('created_by', $row->created_by ? $row->created_by : $created_by_id);
			}

			// update access
			$row->set('access', Request::getInt('access', 0));
		}

		// publish up
		$row->set('publish_up', Date::of($row->get('publish_up'), Config::get('offset'))->toSql());

		// publish down
		if (!$row->get('publish_down')
		 || $row->get('publish_down') == '0000-00-00 00:00:00'
		 || $row->get('publish_down') == Lang::txt('COM_RESOURCES_NEVER'))
		{
			$row->set('publish_down', '0000-00-00 00:00:00');
		}
		else
		{
			$row->set('publish_down', Date::of($row->get('publish_down'), Config::get('offset'))->toSql());
		}

		// Get parameters
		$params = Request::getArray('params', array(), 'post');
		if (is_array($params))
		{
			$txt = new \Hubzero\Config\Registry('');
			foreach ($params as $k => $v)
			{
				$txt->set($k, $v);
			}
			$row->set('params', $txt->toString());
		}

		// Get attributes
		$attribs = Request::getArray('attrib', array(), 'post');
		if (is_array($attribs))
		{
			$txta = new \Hubzero\Config\Registry('');
			foreach ($attribs as $k => $v)
			{
				if ($k == 'timeof')
				{
					if (strtotime(trim($v)) === false)
					{
						$v = null;
					}

					$v = trim($v)
						? Date::of($v, Config::get('offset'))->toSql()
						: null;
				}
				$txta->set($k, $v);
			}
			$row->set('attribs', $txta->toString());
		}

		// Get custom areas, add wrappers, and compile into fulltxt
		$nbtag = Request::getArray('nbtag', array(), 'post');
		if (!empty($nbtag))
		{
			$type = $row->type;

			include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'elements.php';
			$elements = new \Components\Resources\Models\Elements(array(), $type->customFields);
			$schema = $elements->getSchema();

			$fields = array();
			foreach ($schema->fields as $field)
			{
				$fields[$field->name] = $field;
			}

			//$nbtag = $_POST['nbtag'];
			$found = array();
			$fulltxt = $row->get('fulltxt');
			foreach ($nbtag as $tagname => $tagcontent)
			{
				$f = '';

				$fulltxt .= "\n" . '<nb:' . $tagname . '>';
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
						$fulltxt .= '<' . $key . '>' . trim($val) . '</' . $key . '>';
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
						$fulltxt .= $f;
					}
				}
				$fulltxt .= '</nb:' . $tagname . '>' . "\n";

				$row->set('fulltxt', $fulltxt);

				if (!$tagcontent && isset($fields[$tagname]) && $fields[$tagname]->required)
				{
					$this->setError(Lang::txt('COM_RESOURCES_REQUIRED_FIELD_CHECK', $fields[$tagname]->label));
				}

				$found[] = $tagname;
			}

			foreach ($fields as $field)
			{
				if (!in_array($field->name, $found) && $field->required)
				{
					$found[] = $field->name;
					$this->setError(Lang::txt('COM_RESOURCES_REQUIRED_FIELD_CHECK', $field->label));
				}
			}
		}

		// Store content
		if (!$row->save())
		{
			App::abort(500, $row->getError());
		}

		// Checkin resource
		$row->checkin();

		// Rename the temporary upload directory if it exist
		$tmpid = Request::getInt('tmpid', 0, 'post');

		if ($tmpid != Str::pad($row->get('id')))
		{
			// Build the full paths
			$path    = Html::dateToPath($row->created);
			$dir_id  = Str::pad($row->get('id'));

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

			$row->set('path', str_replace($tmpid, Str::pad($row->get('id')), $row->get('path')));
			$row->save();
		}

		// Incoming tags
		$tags = Request::getString('tags', '', 'post');

		// Save the tags
		$rt = new Tags($row->get('id'));
		$rt->setTags($tags, User::get('id'), 0);

		// Incoming badges
		$badgeString = Request::getString('badges', '', 'post');

		// Save the badges
		$badges = new Badges([
			'scope' => 'resources',
			'scopeId' => $row->get('id')
		]);
		$badges->updateBadges($badgeString, User::get('id'), 0);

		// Incoming authors
		if (!$row->isTool())
		{
			$authorsOldstr = Request::getString('old_authors', '', 'post');
			$authorsNewstr = Request::getString('new_authors', '', 'post');
			if (!$authorsNewstr)
			{
				$authorsNewstr = $authorsOldstr;
			}

			$authorsNew = explode(',', $authorsNewstr);
			$authorsOld = explode(',', $authorsOldstr);

			// We have either a new ordering or new authors or both
			if ($authorsNewstr)
			{
				for ($i=0, $n=count($authorsNew); $i < $n; $i++)
				{
					if (is_numeric($authorsNew[$i]))
					{
						$authorid = $authorsNew[$i];
					}
					else
					{
						$authorid = Author::all()
							->whereEquals('name', $authorsNew[$i])
							->where('authorid', '<', 0)
							->limit(1)
							->row()
							->get('authorid');

						if (!$authorid || $authorid > 0)
						{
							$authorid = Author::all()
								->order('authorid', 'asc')
								->limit(1)
								->row()
								->get('authorid');

							if ($authorid > 0)
							{
								$authorid = 0;
							}
							$authorid--;
						}
					}

					$rc = Author::oneByRelationship($row->get('id'), $authorid);
					$rc->set('subtable', 'resources');
					$rc->set('subid', $row->get('id'));
					$rc->set('authorid', $authorid);
					$rc->set('ordering', $i);
					$rc->set('role', trim(Request::getString($authorsNew[$i] . '_role', '')));
					$rc->set('name', trim(Request::getString($authorsNew[$i] . '_name', '')));
					$rc->set('organization', trim(Request::getString($authorsNew[$i] . '_organization', '')));
					$rc->save();

					$authorsNew[$i] = $rc->get('authorid');
				}
			}

			// Run through previous author list and check to see if any IDs had been dropped
			if ($authorsOldstr)
			{
				for ($i=0, $n=count($authorsOld); $i < $n; $i++)
				{
					if (!in_array($authorsOld[$i], $authorsNew))
					{
						$rc = Author::oneByRelationship($row->get('id'), $authorsOld[$i]);
						$rc->destroy();
					}
				}
			}
		}

		// If this is a child, add parent/child association
		$pid = Request::getInt('pid', 0, 'post');

		if ($isNew && $pid)
		{
			$this->_attachChild($row->get('id'), $pid);
		}

		// Is this a standalone resource and we need to email approved submissions?
		if ($row->standalone == 1 && $this->config->get('email_when_approved'))
		{
			// If the state went from pending to published
			if ($row->published == 1 && $old->published == Entry::STATE_DRAFT)
			{
				$this->_emailContributors($row);

				// Log activity
				$recipients = array(
					['resource', $row->get('id')],
					['user', $row->get('created_by')]
				);

				foreach ($row->authors()->where('authorid', '>', 0)->rows() as $author)
				{
					$recipients[] = ['user', $author->get('authorid')];
				}

				Event::trigger('system.logActivity', [
					'activity' => [
						'action'      => 'published',
						'scope'       => 'resource',
						'scope_id'    => $row->title,
						'description' => Lang::txt(
							'COM_RESOURCES_ACTIVITY_ENTRY_PUBLISHED',
							'<a href="' . Route::url($row->link()) . '">' . $row->title . '</a>'
						),
						'details'     => array(
							'title' => $row->title,
							'url'   => Route::url($row->link())
						)
					],
					'recipients' => $recipients
				]);
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
	 * @param   object  $row  Resource
	 * @return  bool
	 */
	private function _emailContributors($row)
	{
		$contributors = array();

		foreach ($row->authors()->where('authorid', '>', 0)->rows() as $author)
		{
			$contributors[] = $author->get('authorid');
		}

		if (count($contributors) > 0)
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
			$message .= $base . '/resources/' . $row->id;

			// Send message
			if (!Event::trigger('xmessage.onSendMessage', array('resources_submission_approved', $subject, $message, $from, $contributors, $this->_option)))
			{
				$this->setError(Lang::txt('COM_RESOURCES_ERROR_FAILED_TO_MESSAGE_USERS'));
				return false;
			}
		}

		return true;
	}

	/**
	 * Removes a resource
	 * Redirects to main listing
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('id', array(0));

		// Ensure we have some IDs to work with
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_RESOURCES_NO_ITEM_SELECTED'));
			return $this->cancelTask();
		}

		$removed = 0;

		foreach ($ids as $id)
		{
			// Load resource info
			$row = Entry::oneOrFail($id);

			// Delete the resource
			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$removed++;
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
	 * @return  void
	 */
	public function accessTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$id  = Request::getInt('id', 0);
		$pid = Request::getInt('pid', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			Notify::warning(Lang::txt('COM_RESOURCES_ERROR_MISSING_ID'));
			return $this->cancelTask();
		}

		// Choose access level
		switch ($this->getTask())
		{
			case 'accesspublic':
				$access = 0;
				break;
			case 'accessregistered':
				$access = 1;
				break;
			case 'accessspecial':
				$access = 2;
				break;
			case 'accessprotected':
				$access = 3;
				break;
			case 'accessprivate':
				$access = 4;
				break;
			default:
				$access = 0;
				break;
		}

		// Load resource info
		$row = Entry::oneOrFail($id);
		$row->set('access', $access);

		// Check and store changes
		if (!$row->save())
		{
			Notify::error($row->getError());
		}

		// Redirect
		App::redirect(
			$this->buildRedirectURL($pid)
		);
	}

	/**
	 * Sets the state of a resource
	 * Redirects to main listing
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$pid = Request::getInt('pid', 0);
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for a resource
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_RESOURCES_ERROR_SELECT_TO', $this->_task));
			return $this->cancelTask();
		}

		switch ($this->getTask())
		{
			case 'archive':
				$msg = 'COM_RESOURCES_ITEMS_ARCHIVED';
				$publish = Entry::STATE_ARCHIVED;
			break;
			case 'unpublish':
				$msg = 'COM_RESOURCES_ITEMS_UNPUBLISHED';
				$publish = Entry::STATE_UNPUBLISHED;
			break;
			case 'publish':
			default:
				$msg = 'COM_RESOURCES_ITEMS_PUBLISHED';
				$publish = Entry::STATE_PUBLISHED;
			break;
		}

		$success = 0;

		// Loop through all the IDs
		foreach ($ids as $id)
		{
			// Load the resource
			$resource = Entry::oneOrFail($id);

			// Only allow changes if the resource isn't checked out or
			// is checked out by the user requesting changes
			if (!$resource->get('checked_out') || $resource->get('checked_out') == User::get('id'))
			{
				$old = $resource->get('published');

				$resource->set('published', $publish);

				// If we're publishing, set the UP date
				if ($publish)
				{
					$resource->set('publish_up', Date::toSql());
				}

				// Is this a standalone resource and we need to email approved submissions?
				if ($resource->get('standalone') == 1 && $this->config->get('email_when_approved'))
				{
					// If the state went from pending to published
					if ($resource->get('published') == Entry::STATE_PUBLISHED
					 && $old == Entry::STATE_PENDING)
					{
						$this->_emailContributors($resource);

						// Log activity
						$recipients = array(
							['resource', $resource->id],
							['user', $resource->created_by]
						);

						foreach ($resource->authors()->where('authorid', '>', 0)->rows() as $author)
						{
							$recipients[] = ['user', $author->get('authorid')];
						}

						Event::trigger('system.logActivity', [
							'activity' => [
								'action'      => 'published',
								'scope'       => 'resource',
								'scope_id'    => $resource->title,
								'description' => Lang::txt(
									'COM_RESOURCES_ACTIVITY_ENTRY_PUBLISHED',
									'<a href="' . Route::url($resource->link()) . '">' . $resource->title . '</a>'
								),
								'details'     => array(
									'title' => $resource->title,
									'url'   => Route::url($resource->link())
								)
							],
							'recipients' => $recipients
						]);
					}
				}

				// Store and checkin the resource
				$resource->save();
				$resource->checkin();

				$success++;
			}
		}

		if ($success)
		{
			Notify::success(Lang::txt($msg, $success));
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
		if ($id)
		{
			$row = Entry::oneOrNew($id);
			$row->checkin();
		}

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

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$id = Request::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the hits, save, checkin
			$row = Entry::oneOrFail($id);
			$row->set('hits', 0);

			if (!$row->save())
			{
				Notify::error($row->getError());
			}
			else
			{
				Notify::success(Lang::txt('COM_RESOURCES_HITS_RESET'));
			}
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

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$id = Request::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the ratings, save
			$row = Entry::oneOrFail($id);
			$row->set('rating', 0.0);
			$row->set('times_rated', 0);

			if (!$row->save())
			{
				Notify::error($row->getError());
			}
			else
			{
				Notify::success(Lang::txt('COM_RESOURCES_RATING_RESET'));
			}
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

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$id = Request::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the ratings, save, checkin
			$row = Entry::oneOrFail($id);
			$row->set('ranking', 0);

			if (!$row->save())
			{
				Notify::error($row->getError());
			}
			else
			{
				Notify::success(Lang::txt('COM_RESOURCES_RANKING_RESET'));
			}
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
		$ids = Request::getArray('id', array(0));

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the resource and check it in
			$row = Entry::oneOrFail($id);

			if (!$row->checkin())
			{
				Notify::error($row->getError());
			}
		}

		// Redirect
		$this->cancelTask();
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
		$id  = Request::getArray('id', array());
		$id  = $id[0];
		$pid = Request::getInt('pid', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			Notify::warning(Lang::txt('COM_RESOURCES_ERROR_MISSING_ID'));
			return $this->cancelTask();
		}

		// Ensure we have a parent ID to work with
		if (!$pid)
		{
			Notify::warning(Lang::txt('COM_RESOURCES_ERROR_MISSING_PARENT_ID'));
			return $this->cancelTask();
		}

		// Get the element moving
		$resource = Association::oneByRelationship($pid, $id);

		// Move the item
		$delta = ($this->getTask() == 'orderup' ? -1 : 1);

		if (!$resource->move($delta))
		{
			Notify::error($resource->getError());
		}

		// Redirect
		App::redirect(
			Route::url($this->buildRedirectURL($pid), false)
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
	 * @param   string   $name        Name of the select element
	 * @param   string   $active      Selected value
	 * @param   integer  $nouser      Display an empty start option
	 * @param   string   $javascript  Any JS to attach to the select element
	 * @param   string   $order       Field to order the users by
	 * @return  string
	 */
	private function userSelect($name, $active, $nouser=0, $javascript=null, $order='a.name')
	{
		$database = \App::get('db');

		$group_id = 'g.id';
		$aro_id = 'aro.id';

		$query = "SELECT a.id AS value, a.name AS text, g.title AS groupname
			FROM `#__users` AS a
			INNER JOIN `#__user_usergroup_map` AS gm ON gm.user_id = a.id
			INNER JOIN `#__usergroups` AS g ON g.id = gm.group_id
			WHERE a.block = '0' AND g.title='Super Users'
			ORDER BY ". $order;

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
	 * @return  string
	 */
	public function authorTask()
	{
		$id   = Request::getString('u', '');
		$role = Request::getString('role', '');
		$rid  = Request::getInt('rid', 0);

		// Get the member's info
		if (is_numeric($id))
		{
			$profile = Member::oneOrNew(intval($id));
		}
		else
		{
			$profile = Member::oneByUsername((string)$id);
		}

		if (!is_object($profile) || !$profile->get('id'))
		{
			$profile = User::all()
				->whereEquals('name', $id)
				->row();
		}

		if (is_object($profile) && $profile->get('id'))
		{
			$name = $profile->name;
			$org  = $profile->get('organization');
			$id   = $profile->get('id');
		}
		else
		{
			$name = null;

			if (is_numeric($id))
			{
				$author = Author::all()
					->whereEquals('authorid', $id)
					->limit(1)
					->row();

				if (is_object($author) && $author->name)
				{
					$name = $author->name;
					$org  = $author->organization;
				}
			}

			if (!$this->view->name)
			{
				$org  = '';
				$name = str_replace('_', ' ', $id);
				$id   = Author::blank()->getUserId($name);
			}
		}

		$row = Entry::oneOrFail($rid);

		$roles = Type::oneOrFail($row->get('type'))->roles;

		$this->view
			->set('name', $name)
			->set('org', $org)
			->set('id', $id)
			->set('roles', $roles)
			->set('role', $role)
			->display();
	}

	/**
	 * Check resource paths
	 *
	 * @return  void
	 */
	public function checkTask()
	{
		include_once dirname(dirname(__DIR__)) . '/helpers/tests/links.php';

		$auditor = new \Hubzero\Content\Auditor('resource');
		$auditor->registerTest(new \Components\Resources\Helpers\Tests\Links);

		$test   = Request::getString('test');
		$status = Request::getString('status', 'failed');

		if (!in_array($status, array('skipped', 'passed', 'failed')))
		{
			$status = 'failed';
		}

		$audits = $auditor->getTests();

		if (count($audits) == 1)
		{
			foreach ($audits as $key => $tester)
			{
				$test = $key;
			}
			$status = $status ? $status : 'failed';
		}

		$this->view
			->set('test', $test)
			->set('status', $status)
			->set('tests', $auditor->getReport())
			->setLayout('check')
			->display();
	}
}
