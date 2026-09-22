<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Groups Plugin class for assets
 */
class plgGroupsCollections extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Custom params
	 *
	 * @var  object
	 */
	protected $_params = null;

	/**
	 * Loads the plugin language file
	 *
	 * @param   string   $extension  The extension for which a language file should be loaded
	 * @param   string   $basePath   The basepath to use
	 * @return  boolean  True, if the file has successfully loaded.
	 */
	public function loadLanguage($extension = '', $basePath = PATH_APP)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}

		$group = \Hubzero\User\Group::getInstance(Request::getCmd('cn'));
		if ($group && $group->isSuperGroup())
		{
			$basePath = PATH_APP . DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');
		}

		$lang = \App::get('language');
		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_CORE . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true);
	}

	/**
	 * Remove any associated data when group is deleted
	 *
	 * @param   object  $group  Group being deleted
	 * @return  string  Log of items removed
	 */
	public function onGroupDelete($group)
	{
		// Import needed libraries
		include_once \Component::path('com_collections') . DS . 'models' . DS . 'archive.php';

		// Get all the IDs for collections
		$database = App::get('db');
		$database->setQuery("SELECT id FROM `#__collections` WHERE `object_type`='group' AND `object_id`=" . $database->quote($group->get('gidNumber')));
		$entries = $database->loadColumn();

		// Start the log text
		$log = Lang::txt('PLG_GROUPS_COLLECTIONS_LOG') . ': ';

		if (count($entries) > 0)
		{
			$entries = array_map('intval', $entries);

			// Get a list of IDs for posts created by this group
			$database->setQuery("SELECT i.id FROM `#__collections_items` AS i LEFT JOIN `#__collections_posts` AS p ON p.`item_id`=i.`id` WHERE p.`original`=1 AND p.`collection_id` IN (" . implode(',', $entries) . ")");
			$ids = $database->loadColumn();

			if ($ids && count($ids))
			{
				// Mark all posts as "trashed"
				$database->setQuery("UPDATE `#__collections_items` SET `state`=2 WHERE `id` IN (" . implode(',', $ids) . ")");
				$database->query();
			}

			// Mark all collections as "trashed"
			$database->setQuery("UPDATE `#__collections` SET `state`=2 WHERE `id` IN (" . implode(',', $entries) . ")");
			$database->query();

			$log .= implode(" \n", $entries);
		}
		else
		{
			$log .= Lang::txt('PLG_GROUPS_BLOG_NO_RESULTS_FOUND') . "\n";
		}

		// Return the log
		return $log;
	}

	/**
	 * Return a count of items that will be removed when group is deleted
	 *
	 * @param   object  $group  Group to delete
	 * @return  string
	 */
	public function onGroupDeleteCount($group)
	{
		include_once \Component::path('com_collections') . DS . 'models' . DS . 'archive.php';

		$database = App::get('db');
		$database->setQuery("SELECT COUNT(*) FROM `#__collections` WHERE `object_type`=" . $database->quote('group') . " AND `object_id`=" . $database->quote($group->get('gidNumber')));

		return Lang::txt('PLG_GROUPS_COLLECTIONS_LOG') . ': ' . intval($database->loadResult());
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => Lang::txt('PLG_GROUPS_' . strtoupper($this->_name)),
			'default_access'   => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f005'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param   object   $group       Current group
	 * @param   string   $option      Name of the component
	 * @param   string   $authorized  User's authorization level
	 * @param   integer  $limit       Number of records to pull
	 * @param   integer  $limitstart  Start of records to pull
	 * @param   string   $action      Action to perform
	 * @param   array    $access      What can be accessed
	 * @param   array    $areas       Active area(s)
	 * @return  array
	 */
	public function onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas=null)
	{
		$return = 'html';
		$active = $this->_name;

		// The output array we're returning
		$arr = array(
			'html'     => '',
			'metadata' => array()
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$return = 'metadata';
			}
		}

		$this->group    = $group;
		$this->database = App::get('db');

		include_once Component::path('com_collections') . DS . 'models' . DS . 'archive.php';

		$this->model = new \Components\Collections\Models\Archive('group', $this->group->get('gidNumber'));

		//get the plugins params
		//$this->params = \Hubzero\Plugin\Params::getParams($group->gidNumber, 'groups', $this->_name);
		$this->members = $group->get('members');
		$this->authorized = $authorized;

		$this->_authorize('collection');
		$this->_authorize('item');

		//are we returning html
		if ($return == 'html')
		{
			// This needs to be called to ensure scripts are pushed to the document
			$foo = App::get('editor')->display('description', '', '', '', 35, 5, false, 'field_description', null, null, array('class' => 'minimal no-footer'));

			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//get the group members
			$members = $group->get('members');

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if (User::isGuest()
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$url = Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active, false, true);
				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
					Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array(User::get('id'), $members)
			 && $group_plugin_acl == 'members'
			 && $authorized != 'admin')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}
			//user vars
			$this->authorized = $authorized;

			//group vars

			$this->members    = $members;

			// Set some variables so other functions have access
			$this->action     = $action;
			$this->option     = $option;
			$this->name       = substr($option, 4, strlen($option));

			$this->params->set('access-plugin', 0);
			if ($group_plugin_acl == 'registered')
			{
				$this->params->set('access-plugin', 1);
			}
			if ($group_plugin_acl == 'members')
			{
				$this->params->set('access-plugin', 4);
			}

			//push the css to the doc
			$this->css();

			$task = '';
			$controller = 'board';
			$id = 0;

			$path = Request::path();
			if (strstr($path, '/'))
			{
				$path = str_replace(Request::base(true), '', $path);
				$path = str_replace('index.php', '', $path);
				$path = trim($path, '/');
				$parts = explode('/', $path);
				$start = false;
				$bits = array();
				foreach ($parts as $p)
				{
					if ($p == $this->_name)
					{
						$start = true;
						continue;
					}
					if ($start)
					{
						$bits[] = $p;
					}
				}

				if (isset($bits[0]) && $bits[0])
				{
					$bits[0] = strtolower(trim($bits[0]));
					switch ($bits[0])
					{
						case 'post':
							$this->action = 'post';
							if (isset($bits[1]))
							{
								if ($bits[1] == 'new' || $bits[1] == 'save')
								{
									$this->action = $bits[1] . $this->action;
								}
								else
								{
									Request::setVar('post', $bits[1]);
									if (isset($bits[2]))
									{
										if (in_array($bits[2], array('post', 'vote', 'collect', 'remove', 'move', 'comment', 'savecomment', 'deletecomment')))
										{
											$this->action = $bits[2];
										}
										else
										{
											$this->action = $bits[2] . $this->action;
										}
									}
								}
							}
						break;

						case 'all':
						case 'posts':
						case 'followers':
						case 'following':
						case 'follow':
						case 'unfollow':
							$this->action = $bits[0];
						break;

						case 'new':
						case 'save':
							$this->action = $bits[0] . 'collection';
							if (isset($bits[1]))
							{
								Request::setVar('unfollow', $bits[1]);
							}
						break;

						case 'settings':
						case 'savesettings':
							$this->action = $bits[0];
						break;

						default:
							$this->action = 'collection';
							Request::setVar('board', $bits[0]);

							if (isset($bits[1]))
							{
								$this->action = $bits[1] . $this->action;
							}
						break;
					}
				}
			}

			switch ($this->action)
			{
				// Comments
				case 'savecomment':
					$arr['html'] = $this->_savecomment();
					break;
				// 'newcomment' and 'editcomment' used to dispatch to methods that
				// have never existed in this plugin -- reaching either was a fatal.
				// Commenting is savecomment/deletecomment.
				case 'deletecomment':
					$arr['html'] = $this->_deletecomment();
					break;

				case 'followers':
					$arr['html'] = $this->_followers();
					break;
				case 'following':
					$arr['html'] = $this->_following();
					break;
				case 'follow':
					$arr['html'] = $this->_follow('group');
					break;
				case 'unfollow':
					$arr['html'] = $this->_unfollow('group');
					break;

				// Entries
				case 'savepost':
					$arr['html'] = $this->_save();
					break;
				case 'newpost':
					$arr['html'] = $this->_new();
					break;
				case 'editpost':
					$arr['html'] = $this->_edit();
					break;
				case 'deletepost':
					$arr['html'] = $this->_delete();
					break;
				case 'posts':
					$arr['html'] = $this->_posts();
					break;

				case 'comment':
				case 'post':
					$arr['html'] = $this->_post();
					break;
				case 'vote':
					$arr['html'] = $this->_vote();
					break;
				case 'collect':
					$arr['html'] = $this->_repost();
					break;
				case 'remove':
					$arr['html'] = $this->_remove();
					break;
				case 'move':
					$arr['html'] = $this->_move();
					break;

				case 'followcollection':
					$arr['html'] = $this->_follow('collection');
					break;
				case 'unfollowcollection':
					$arr['html'] = $this->_unfollow('collection');
					break;
				case 'collectcollection':
					$arr['html'] = $this->_repost();
					break;
				case 'newcollection':
					$arr['html'] = $this->_newcollection();
					break;
				case 'editcollection':
					$arr['html'] = $this->_editcollection();
					break;
				case 'savecollection':
					$arr['html'] = $this->_savecollection();
					break;
				case 'deletecollection':
					$arr['html'] = $this->_deletecollection();
					break;
				case 'all':
				case 'collections':
					$arr['html'] = $this->_collections();
					break;

				case 'settings':
					$arr['html'] = $this->_settings();
					break;
				case 'savesettings':
					$arr['html'] = $this->_savesettings();
					break;

				case 'collection':
					$arr['html'] = $this->_collection();
					break;

				default:
					$arr['html'] = $this->_collections();
					break;
			}
		}

		// Get a count of all the collections
		$filters = array(
			'count' => true
		);
		if (!$this->params->get('access-manage-collection') && !$authorized)
		{
			$filters['access'] = 0;
		}
		$arr['metadata']['count'] = $this->model->collections($filters);

		return $arr;
	}

	/**
	 * Redirect to the login form
	 *
	 * @return  void
	 */
	private function _login()
	{
		$route = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name, false, true);

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($route)),
			Lang::txt('GROUPS_LOGIN_NOTICE'),
			'warning'
		);
		return;
	}

	/**
	 * Display a list of collections
	 *
	 * @return  string
	 */
	/**
	 * Show the collections this group follows
	 *
	 * The dispatcher has always had a 'following' case calling this, and the
	 * template has always been there, but the method never was -- so reaching
	 * ?scope=following was a fatal. Mirrors _followers() in this file, with the
	 * same access filtering.
	 *
	 * @return  string
	 */
	private function _following()
	{
		// Filters for returning results
		$filters = array(
			'limit' => Request::getInt('limit', Config::get('list_limit')),
			'start' => Request::getInt('limitstart', 0)
		);

		$count = array(
			'count'  => true
		);

		if (!$this->params->get('access-manage-collection'))
		{
			$filters['access'] = (User::isGuest() ? 0 : array(0, 1));
			if (in_array(User::get('id'), $this->group->get('members')))
			{
				$filters['access'] = array(0, 1, 4);
			}
			$count['access'] = $filters['access'];
		}

		$collections = $this->model->collections($count);
		$posts       = $this->model->posts($count);
		$followers   = $this->model->followers($count);

		$total       = $this->model->following($count);
		$rows        = $this->model->following($filters);

		$view = $this->view('following', 'follow')
			->set('name', $this->_name)
			->set('option', $this->option)
			->set('group', $this->group)
			->set('params', $this->params)
			->set('model', $this->model)
			->set('filters', $filters)
			->set('collections', $collections)
			->set('posts', $posts)
			->set('followers', $followers)
			->set('total', $total)
			->set('rows', $rows);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	private function _followers()
	{
		// Filters for returning results
		$filters = array(
			'limit' => Request::getInt('limit', Config::get('list_limit')),
			'start' => Request::getInt('limitstart', 0)
		);

		$count = array(
			'count'  => true
		);

		if (!$this->params->get('access-manage-collection'))
		{
			$filters['access'] = (User::isGuest() ? 0 : array(0, 1));
			if (in_array(User::get('id'), $this->group->get('members')))
			{
				$filters['access'] = array(0, 1, 4);
			}
			$count['access'] = $filters['access'];
		}

		$collections = $this->model->collections($count);
		$posts       = $this->model->posts($count);
		$following   = $this->model->following($count);

		$total       = $this->model->followers($count);
		$rows        = $this->model->followers($filters);

		$view = $this->view('followers', 'follow')
			->set('name', $this->_name)
			->set('option', $this->option)
			->set('group', $this->group)
			->set('params', $this->params)
			->set('model', $this->model)
			->set('filters', $filters)
			->set('collections', $collections)
			->set('posts', $posts)
			->set('following', $following)
			->set('total', $total)
			->set('rows', $rows);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a list of collections
	 *
	 * @return     string
	 */
	private function _collections()
	{
		$view = $this->view('collections', 'collection')
			->set('name', $this->_name)
			->set('option', $this->option)
			->set('group', $this->group)
			->set('params', $this->params)
			->set('model', $this->model);

		// Filters for returning results
		$view->filters = array(
			'limit' => Request::getInt('limit', Config::get('list_limit')),
			'start' => Request::getInt('limitstart', 0)
		);

		// Filters for returning results
		$filters = array(
			'user_id' => User::get('id'),
			'state'   => 1
		);

		$count = array(
			'count'  => true
		);

		if (!$this->params->get('access-manage-collection'))
		{
			$view->filters['access'] = (User::isGuest() ? 0 : array(0, 1));
			if (in_array(User::get('id'), $this->group->get('members')))
			{
				$view->filters['access'] = array(0, 1, 4);
			}
			$filters['access'] = $view->filters['access'];
			$count['access'] = $view->filters['access'];
		}

		$filters['count'] = true;
		$view->total = $this->model->collections($filters);

		$filters['count'] = false;
		$view->rows = $this->model->collections($filters);

		$view->posts = 0;
		if ($view->rows)
		{
			foreach ($view->rows as $row)
			{
				$view->posts += $row->get('posts');
			}
		}

		$view->followers = $this->model->followers($count);

		if ($this->params->get('access-can-follow'))
		{
			$view->following = $this->model->following($count);
		}

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a list of posts in a collection
	 *
	 * @return     string
	 */
	private function _collection()
	{
		$view = $this->view('default', 'collection')
			->set('name', $this->_name)
			->set('option', $this->option)
			->set('group', $this->group)
			->set('params', $this->params)
			->set('model', $this->model);

		// Filters for returning results
		$view->filters = array(
			'limit'         => Request::getInt('limit', Config::get('list_limit')),
			'start'         => Request::getInt('limitstart', 0),
			'user_id'       => User::get('id'),
			'search'        => Request::getString('search', ''),
			'state'         => 1,
			'collection_id' => Request::getVar('board', 0)
		);

		$view->collection = $this->model->collection($view->filters['collection_id']);
		if (!$view->collection->exists())
		{
			App::abort(404, Lang::txt('PLG_GROUPS_COLLECTIONS_ERROR_COLLECTION_DOES_NOT_EXIST'));
			return;
		}

		$view->filters['collection_id'] = $view->collection->get('id');

		$view->filters['sort'] = Request::getWord('sort', $view->collection->get('sort'));
		if (!in_array($view->filters['sort'], array('created', 'ordering')))
		{
			$view->filters['sort'] = 'created';
		}
		$view->filters['sort_Dir'] = ($view->filters['sort'] == 'ordering' ? 'asc' : 'desc');

		$count = array(
			'count' => true,
			'collection_id' => $view->collection->get('id')
		);

		if (!$this->params->get('access-manage-collection'))
		{
			$view->filters['access'] = (User::isGuest() ? 0 : array(0, 1));
			if (in_array(User::get('id'), $this->group->get('members')))
			{
				$view->filters['access'] = array(0, 1, 4);
			}
			$count['access'] = $view->filters['access'];
		}
		if ($this->authorized)
		{
			$count['access'] = -1;
			$view->filters['access'] = -1;
		}

		$view->collections = $this->model->collections($count);
		$view->posts       = $this->model->posts($count);
		$view->followers   = $this->model->followers($count);
		if ($this->params->get('access-can-follow'))
		{
			$view->following   = $this->model->following($count);
		}

		$view->filters['count'] = true;
		$view->count = $view->collection->posts($view->filters);

		$view->filters['count'] = null;
		$view->rows = $view->collection->posts($view->filters);

		$view->scope = $view->collection->get('alias');

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Start following something
	 *
	 * @param   string  $what
	 * @return  string
	 */
	private function _follow($what='collection')
	{
		// Is the board restricted to logged-in users only?
		if (User::isGuest())
		{
			return $this->_login();
		}

		$sfx = '';
		switch ($what)
		{
			case 'group':
				$id = $this->group->get('gidNumber');
			break;

			case 'member':
				$id = $this->member->get('uidNumber');
			break;

			case 'collection':
				$collection = $this->model->collection(Request::getString('board', ''));
				if (!$collection->exists())
				{
					App::abort(404, Lang::txt('PLG_GROUPS_COLLECTIONS_ERROR_COLLECTION_DOES_NOT_EXIST'));
					return;
				}
				$id = $collection->get('id');
				$sfx = '&scope=' . $collection->get('alias') . '/unfollow';
			break;
		}

		if (!$this->model->follow($id, $what, User::get('id'), 'member'))
		{
			$this->setError($this->model->getError());
		}

		if (Request::getInt('no_html', 0))
		{
			$response = new stdClass;
			$response->href = Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=collections' . $sfx);
			$response->success = true;
			if ($this->getError())
			{
				$response->success = false;
				$response->error = $this->getError();
			}
			echo json_encode($response);
			exit;
		}

		return $this->_collection();
	}

	/**
	 * Stop following soemthing
	 *
	 * @param   string  $what
	 * @return  string
	 */
	private function _unfollow($what='collection')
	{
		// Is the board restricted to logged-in users only?
		if (User::isGuest())
		{
			return $this->_login();
		}

		$sfx = '';
		switch ($what)
		{
			case 'group':
				$id = $this->group->get('gidNumber');
			break;

			case 'member':
				$id = $this->member->get('uidNumber');
			break;

			case 'collection':
				$collection = $this->model->collection(Request::getString('board', ''));
				if (!$collection->exists())
				{
					App::abort(404, Lang::txt('PLG_GROUPS_COLLECTIONS_ERROR_COLLECTION_DOES_NOT_EXIST'));
					return;
				}
				$id = $collection->get('id');
				$sfx = '&scope=' . $collection->get('alias') . '/follow';
			break;
		}

		if (!$this->model->unfollow($id, $what, User::get('id'), 'member'))
		{
			$this->setError($this->model->getError());
		}

		if (Request::getInt('no_html', 0))
		{
			$response = new stdClass;
			$response->href = Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=collections' . $sfx);
			$response->success = true;
			if ($this->getError())
			{
				$response->success = false;
				$response->error = $this->getError();
			}
			echo json_encode($response);
			exit;
		}

		return $this->_collection();
	}

	/**
	 * Display a list of posts for all collections
	 *
	 * @return  string
	 */
	private function _posts()
	{
		$view = $this->view('default', 'collection')
			->set('name', $this->_name)
			->set('option', $this->option)
			->set('group', $this->group)
			->set('params', $this->params)
			->set('model', $this->model);

		// Filters for returning results
		$view->filters = array(
			'limit'       => Request::getInt('limit', Config::get('list_limit')),
			'start'       => Request::getInt('limitstart', 0),
			'search'      => Request::getString('search', ''),
			'state'       => 1,
			'object_type' => 'group',
			'object_id'   => $this->group->get('gidNumber'),
			'user_id'     => User::get('id'),
			'sort'        => 'created',
			'sort_Dir'    => 'desc'
		);

		// Filters for returning results
		$count = array(
			'count' => true
		);

		if (!$this->params->get('access-manage-collection'))
		{
			$view->filters['access'] = (User::isGuest() ? 0 : array(0, 1));
			if (in_array(User::get('id'), $this->group->get('members')))
			{
				$view->filters['access'] = array(0, 1, 4);
			}
			$count['access'] = $view->filters['access'];
		}
		if ($this->authorized)
		{
			$count['access'] = -1;
			$view->filters['access'] = -1;
		}

		$view->collections = $this->model->collections($count);
		$view->posts       = $this->model->posts($count);
		$view->followers   = $this->model->followers($count);
		if ($this->params->get('access-can-follow'))
		{
			$view->following   = $this->model->following($count);
		}

		$view->collection = \Components\Collections\Models\Collection::getInstance();

		$view->count = $view->posts;
		$view->rows  = $view->collection->posts($view->filters);
		$view->scope = 'posts';

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a post
	 *
	 * @return  string
	 */
	private function _post()
	{
		$post_id = Request::getInt('post', 0);

		$post = \Components\Collections\Models\Post::getInstance($post_id);

		if (!$post->exists())
		{
			return $this->_collections();
		}

		$collection = $this->model->collection($post->get('collection_id'));

		// A post is reached by bare numeric id and Post::getInstance() returns
		// any row on the hub, so the board it sits on has to be one of this
		// group's. Without this the access test below runs against an empty
		// collection, whose access reads 0, and every group's private post
		// renders through whichever group the caller can see.
		if (!$collection->exists())
		{
			return $this->_collections();
		}

		// Check authorization
		// If the collection is registered and the user is NOT logged in OR
		// If the collection is private and the user is NOT a member of the group...
		if (($collection->get('access') == 1 && User::isGuest())
		 || ($collection->get('access') == 4 && !in_array(User::get('id'), $this->members)))
		{
			App::abort(403, Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
		}

		$no_html = Request::getInt('no_html', 0);

		$view = $this->view('default', 'post')
			->set('name', $this->_name)
			->set('option', $this->option)
			->set('group', $this->group)
			->set('params', $this->params)
			->set('model', $this->model)
			->set('no_html', $no_html)
			->set('collection', $collection)
			->set('post', $post)
			->setErrors($this->getErrors());

		if ($no_html)
		{
			$view->display();
			exit;
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a form for creating an entry
	 *
	 * @return  string
	 */
	private function _new()
	{
		return $this->_edit();
	}

	/**
	 * Display a form for editing an entry
	 *
	 * @param   object  $entry
	 * @return  string
	 */
	private function _edit($entry=null)
	{
		if (User::isGuest())
		{
			return $this->_login();
		}

		if (!$this->params->get('access-create-item') && !$this->params->get('access-edit-item'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name),
				Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'),
				'error'
			);
			return;
		}

		$no_html = Request::getInt('no_html', 0);

		// One template for both paths. The AJAX branch used to dispatch to
		// 'edit_' . $type, and no edit_<type>.php has ever existed in
		// views/post/tmpl/ -- only edit.php. View::loadTemplate() then falls back
		// to the fixed name 'default', which is the post closeup, rendered
		// without $this->post: a fatal on every ?no_html=1 edit. edit.php already
		// threads $this->no_html through its form action, so it is the right
		// template for the AJAX case too.
		$view = $this->view('edit', 'post');
		$view->name        = $this->_name;
		$view->option      = $this->option;
		$view->group       = $this->group;
		$view->task        = $this->action;
		$view->params      = $this->params;
		$view->no_html     = $no_html;

		$id = Request::getInt('post', 0);

		$view->collection = $this->model->collection(Request::getString('board', 0));

		$view->collections = $this->model->collections();
		if (!$view->collections->total())
		{
			$view->collection->setup($this->group->get('cn'), 'group');
			$view->collections = $this->model->collections();
			$view->collection = $this->model->collection(Request::getString('board', 0));
		}

		$view->entry = (is_object($entry) ? $entry : $view->collection->post($id));
		if (!$view->collection->exists() && $view->entry->exists())
		{
			$view->collection = $this->model->collection($view->entry->get('collection_id'));
		}

		if ($remove = Request::getInt('remove', 0))
		{
			// As com_collections' own posts controller: the post is chosen by id
			// and resolves to any row, so the item whose asset is deleted was the
			// caller's to pick. The test is on the ITEM's owner, because the asset
			// hangs off the item and a repost carries the reposter's created_by
			// with the original author's item_id.
			//
			// Owner only, deliberately: 'manager' here means no more than being in
			// the managers list of the group currently being viewed, which anyone
			// gets by creating a group, while the post reached above is not scoped
			// to that group at all. Admitting managers would let any registered
			// user strip the assets off any item on the hub -- from the original
			// and from every repost of it. The edit form no longer offers the
			// delete link to a non-owner, so this refuses nothing the page shows.
			$item = $view->entry->item();

			if ($item->get('created_by') != User::get('id'))
			{
				App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
			}

			if (!$item->removeAsset($remove))
			{
				$view->setError($item->getError());
			}
		}

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		if ($no_html)
		{
			$view->display();
			exit;
		}

		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	private function _save()
	{
		// Check for request forgeries
		Request::checkToken();

		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-edit-item') || !$this->params->get('access-create-item'))
		{
			$this->setError(Lang::txt('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_collections();
		}

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');

		if ($fields['id'] && !is_numeric($fields['id']))
		{
			App::abort(404, Lang::txt('Post does not exist'));
		}

		// Get model
		$item = new \Components\Collections\Models\Item(intval($fields['id']));

		// The post being edited, if any. A member may edit their own post, and
		// for a repost that post carries someone else's item.
		$p    = Request::getArray('post', array(), 'post');
		$post = new \Components\Collections\Models\Post(isset($p['id']) ? intval($p['id']) : 0);

		if ($post->exists()
		 && ($post->get('created_by') != User::get('id')
			|| intval($post->get('item_id')) !== intval($item->get('id'))))
		{
			App::abort(403, Lang::txt('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		// The item itself may only be written by its owner: fields[id] names an
		// existing row and Item resolves any of them, so without this a member
		// of any group could overwrite another member's item -- title,
		// description and url -- by posting its id. For a repost the content
		// stays the original author's and only the post is updated below.
		$writeItem = !$item->exists() || $item->get('created_by') == User::get('id');

		if (!$post->exists() && !$writeItem)
		{
			App::abort(403, Lang::txt('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		$tmp = null;

		if ($writeItem)
		{
			if (substr($item->get('title', ''), 0, 3) == 'tmp')
			{
				$tmp = $item->get('title');
			}

			// Bind content
			if (!$item->bind($fields))
			{
				$this->setError($item->getError());
				return $this->_edit($item);
			}

			// Add some data
			if ($files = Request::getArray('fls', '', 'files'))
			{
				$item->set('_files', $files);
			}
			$item->set('_assets', Request::getArray('assets', array(), 'post'));
			$item->set('_tags', trim(Request::getString('tags', '')));
			$item->set('state', 1);
			if (!$item->exists())
			{
				$item->set('access', 0);
			}

			// Store new content
			if (!$item->store())
			{
				$this->setError($item->getError());
				return $this->_edit($item);
			}
		}

		// It's possible that multiple temporary items could have been created
		// This can happen if someone drops multiple files at once on the file
		// uploader. So, we need to move any attachments back to the main one
		// and remove the extra items.
		//
		// @TODO: Find a better way to do this
		if ($tmp)
		{
			$db = App::get('db');
			$db->setQuery("SELECT id FROM `#__collections_items` WHERE `title`=" . $db->quote($tmp) . " AND `id`!=" . $db->quote($item->get('id')));
			$others = $db->loadColumn();

			if (count($others) > 0)
			{
				$db->setQuery("SELECT * FROM `#__collections_assets` WHERE `item_id` in (" . implode(",", $others) . ")");
				$assets = $db->loadObjectList();
				foreach ($assets as $asset)
				{
					$asset = new \Components\Collections\Models\Asset($asset);
					if (!$asset->move($item->get('id')))
					{
						$this->setError($item->getError());
						return $this->_edit($item);
					}
				}

				$db->setQuery("DELETE FROM `#__collections_items` WHERE `id` in (" . implode(",", $others) . ")");
				$db->query();
			}
		}

		// The board is chosen from a select listing this group's collections, but
		// the id arrives from the request: only post to a board of a group the
		// member belongs to, or one of their own. An empty value creates a new
		// board below.
		if (!empty($p['collection_id']) && !$this->mayPostToCollection($p['collection_id']))
		{
			App::abort(403, Lang::txt('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		if (!$post->exists())
		{
			$post->set('item_id', $item->get('id'));
			$post->set('original', 1);
		}

		if (!isset($p['collection_id']))
		{
			$p['collection_id'] = 0;

			if ($coltitle = Request::getString('collection_title', '', 'post'))
			{
				$collection = new \Components\Collections\Models\Collection();
				$collection->set('title', $coltitle);
				$collection->set('object_id', $this->group->get('gidNumber'));
				$collection->set('object_type', 'group');
				$collection->set('access', $this->params->get('access-plugin'));
				$collection->store();

				$p['collection_id'] = $collection->get('id');
			}
		}

		$post->set('collection_id', $p['collection_id']);
		if (isset($p['description']))
		{
			$post->set('description', $p['description']);
		}
		if (!$post->store())
		{
			$this->setError($post->getError());
		}

		// Check for any errors
		if ($this->getError())
		{
			Request::setVar('post', $p['id']);
			return $this->_edit($post->item()->bind($fields));
		}

		if (!isset($collection))
		{
			$collection = new \Components\Collections\Models\Collection($p['collection_id']);
		}

		$url = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias');

		// Record the activity
		$recipients = array(
			['group', $this->group->get('gidNumber')],
			['collection', $collection->get('id')],
			['user', $item->get('created_by')]
		);
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		$itemObj = new \Components\Collections\Models\Item($post->get('item_id'));
		$itemTitle = $itemObj->get('title', '');

		$collectionLink = '<a href="' . Route::url($collection->link()) . '">' . $collection->get('title') . '</a>';

		$groupTitle = '<a href="' . Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn'), false) . '">'
			. $this->group->get('description') . '</a>';

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($p['id'] ? 'updated' : 'created'),
				'scope'       => 'collections.item',
				'scope_id'    => $item->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_POST_' . ($p['id'] ? 'UPDATED' : 'CREATED'), $itemTitle, $collectionLink, $groupTitle),
				'details'     => array(
					'collection_id' => $collection->get('id'),
					'post_id'       => $post->get('id'),
					'item_id'       => $post->get('item_id'),
					'url'           => $url
				)
			],
			'recipients' => $recipients
		]);

		// Redirect
		App::redirect(
			Route::url($url)
		);
	}

	/**
	 * Whether the current user may post to an already-stored collection.
	 *
	 * The board id arrives from the request. The select that produces it lists
	 * only boards the member can reach, but nothing re-checked that on save,
	 * and Collection resolves any row -- so without this a member could drop a
	 * post onto any board on the hub.
	 *
	 * @param   integer  $id  Collection id
	 * @return  boolean
	 */
	private function mayPostToCollection($id)
	{
		$target = new \Components\Collections\Models\Collection(intval($id));

		return $target->canBePostedToBy();
	}

	/**
	 * Repost an entry
	 *
	 * @return  string
	 */
	private function _repost()
	{
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Deliberately not gated on access-create-item: a repost writes into a
		// board the caller owns, not into this group, and the Collect button is
		// offered to every logged-in viewer of a public group's collection.
		// The board itself is checked below.

		$no_html = Request::getInt('no_html', 0);

		// No board ID selected so present repost form
		$repost = Request::getInt('repost', 0);
		if (!$repost)
		{
			// Incoming
			$post_id       = Request::getInt('post', 0);
			$collection_id = Request::getString('board', 0);

			if (!$post_id && $collection_id)
			{
				// ?board= here is the board's ALIAS, not its id -- it is read with
				// Request::getString() and the Collect control links
				// $row->get('alias'). So resolve it through the archive, which is
				// the only thing that passes the object_id the alias lookup needs:
				// a bare new Collection('<alias>') runs the alias branch of
				// Tables\Collection::load() with the constructor's default
				// object_id of 0 ANDed in, which no real row can match.
				// com_collections' collectTask() can use a bare lookup because its
				// ?board= is numeric; this one cannot.
				//
				// Fall back to an unscoped lookup for a numeric out-of-scope id,
				// then ask the readability predicate. Without that test an
				// out-of-scope board handed back an empty model, and
				// Collection::item() then built an item with a null object_id,
				// ignored check()'s false return and store()d it anyway: one junk
				// #__collections_items row per request, repeatable.
				$collection = $this->model->collection($collection_id);

				if (!$collection->exists())
				{
					$collection = new \Components\Collections\Models\Collection($collection_id);
				}

				if (!$collection->isReadableBy())
				{
					App::abort(403, Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
				}

				$item_id       = $collection->item()->get('id');
				$collection_id = $collection->item()->get('object_id');
			}
			else
			{
				$post = \Components\Collections\Models\Post::getInstance($post_id);

				$item_id = $post->get('item_id');
			}

			$view = $this->view('repost', 'post');

			$view->myboards      = $this->model->mine();
			$view->groupboards   = $this->model->mine('groups');

			$view->name          = $this->_name;
			$view->option        = $this->option;
			$view->group         = $this->group;
			$view->no_html       = $no_html;
			$view->post_id       = $post_id;
			$view->collection_id = $collection_id;
			$view->item_id       = $item_id;

			if ($no_html)
			{
				$view->display();
				exit;
			}

			return $view->loadTemplate();
		}

		// Check for request forgeries
		Request::checkToken();

		$collection_id = Request::getInt('collection_id', 0);

		// Reposting writes a row into the named board, so the board has to be
		// one this member can post to -- collection_id comes straight from the
		// request and Collection resolves any row.
		if ($collection_id && !$this->mayPostToCollection($collection_id))
		{
			App::abort(403, Lang::txt('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		if (!$collection_id)
		{
			$collection = new \Components\Collections\Models\Collection();
			$collection->set('title', Request::getString('collection_title', ''));
			$collection->set('object_id', $this->group->get('gidNumber'));
			$collection->set('object_type', 'group');
			$collection->set('access', $this->params->get('access-plugin'));
			if (!$collection->store())
			{
				$this->setError($collection->getError());
			}
			$collection_id = $collection->get('id');
		}
		$item_id       = Request::getInt('item_id', 0);

		// The item a repost carries is a hidden form field, and nothing
		// downstream re-checks it -- Tables\Post::check() only requires it to be
		// non-zero, and it stamps created_by from the session. Without this a
		// caller can mint a post of their own carrying any item on the hub,
		// which defeats every guard keyed on "the item this post carries": the
		// asset delete, the item delete, and the collection comment scope.
		$__item = new \Components\Collections\Models\Item($item_id);

		if (!$__item->isCollectableBy())
		{
			App::abort(403, Lang::txt('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		// Try loading the current board/bulletin to see
		// if this has already been posted to the board (i.e., no duplicates)
		$post = new \Components\Collections\Tables\Post($this->database);
		$post->loadByBoard($collection_id, $item_id);
		if (!$post->get('id'))
		{
			// No record found -- we're OK to add one
			$post = new \Components\Collections\Tables\Post($this->database);
			$post->item_id       = $item_id;
			$post->collection_id = $collection_id;
			$post->description   = Request::getString('description', '', 'none', 2);
			if (!$post->check())
			{
				$this->setError($post->getError());
			}
			else
			{
				// Store new content
				if (!$post->store())
				{
					$this->setError($post->getError());
				}
			}
		}

		if ($this->getError())
		{
			return $this->getError();
		}

		// Record the activity
		$recipients = array(
			['group', $this->group->get('gidNumber')],
			['collection', $collection_id],
			['user', $post->get('created_by')]
		);
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		if (!isset($collection))
		{
			$collection = new \Components\Collections\Models\Collection($collection_id);
		}
		$itemObj = new \Components\Collections\Models\Item($item_id);
		$itemTitle = $itemObj->get('title', '');

		$collectionLink = '<a href="' . Route::url($collection->link()) . '">' . $collection->get('title') . '</a>';

		$groupTitle = '<a href="' . Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn'), false) . '">'
			. $this->group->get('description') . '</a>';

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'created',
				'scope'       => 'collections.post',
				'scope_id'    => $post->id,
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_POST_CREATED', $itemTitle, $collectionLink, $groupTitle),
				'details'     => array(
					'collection_id' => $post->collection_id,
					'item_id'       => $post->item_id,
					'post_id'       => $post->id
				)
			],
			'recipients' => $recipients
		]);

		// Display updated bulletin stats if called via AJAX
		if ($no_html)
		{
			echo Lang::txt('PLG_GROUPS_COLLECTIONS_POST_REPOSTS', $post->getCount(array('item_id' => $post->get('item_id'), 'original' => 0)));
			exit;
		}

		// Display the main listing
		return $this->_collection();
	}

	/**
	 * Remove an entry
	 *
	 * @return  string
	 */
	private function _remove()
	{
		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-create-item'))
		{
			$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// Incoming
		$post = \Components\Collections\Models\Post::getInstance(Request::getInt('post', 0));

		// The post's collection must belong to this group
		$__coll = new \Components\Collections\Models\Collection($post->get('collection_id'));
		if (!$__coll->get('id') || $__coll->get('object_type') != 'group'
		 || $__coll->get('object_id') != $this->group->get('gidNumber'))
		{
			$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// views/collection/tmpl/default.php offers Remove to the post's creator or
		// to a holder of access-manage-collection, which _authorize() grants to
		// managers only. This method asked for nothing more than
		// access-create-item, which every member holds -- so any member of the
		// group could remove any repost from any of its boards, a control the page
		// never offered them. Match the view: the creator, or someone who may
		// moderate the board. The members plugin's twin already tests this.
		if ($post->get('created_by') != User::get('id')
		 && !$__coll->canBeModeratedBy())
		{
			$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		$collection = $this->model->collection($post->get('collection_id'));

		$msg = Lang::txt('Post removed.');
		$type = 'passed';
		if (!$post->remove())
		{
			$msg = $post->getError();
			$type = 'error';
		}

		$route = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias'));

		// Record the activity
		$recipients = array(
			['group', $this->group->get('gidNumber')],
			['collection', $collection->get('id')],
			['user', $post->get('created_by')]
		);
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.post',
				'scope_id'    => $post->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_POST_DELETED', '<a href="' . $route . '">' . htmlspecialchars((string) ($collection->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'collection_id' => $post->get('collection_id'),
					'item_id'       => $post->get('item_id'),
					'post_id'       => $post->get('id')
				)
			],
			'recipients' => $recipients
		]);

		// Redirect
		if (Request::getInt('no_html', 0))
		{
			echo $route;
			exit;
		}

		App::redirect(
			$route,
			$msg,
			$type
		);
	}

	/**
	 * Move a post to another collection
	 *
	 * @return  void
	 */
	private function _move()
	{
		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-create-item'))
		{
			$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// Incoming
		$post = \Components\Collections\Models\Post::getInstance(Request::getInt('post', 0));

		// The post's collection must belong to this group
		$__coll = new \Components\Collections\Models\Collection($post->get('collection_id'));
		if (!$__coll->get('id') || $__coll->get('object_type') != 'group'
		 || $__coll->get('object_id') != $this->group->get('gidNumber'))
		{
			$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// The destination board arrives from the request like the source did, so
		// it has to be one this member can post to -- otherwise a post can be
		// moved onto any board on the hub.
		$__dest = Request::getInt('board', 0);

		if ($__dest && !$this->mayPostToCollection($__dest))
		{
			App::abort(403, Lang::txt('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		if (!$post->move($__dest))
		{
			$this->setError($post->getError());
		}

		$route = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name);

		if ($no_html = Request::getInt('no_html', 0))
		{
			echo $route;
			exit;
		}

		App::redirect($route);
	}

	/**
	 * Delete an entry
	 *
	 * @return  string
	 */
	private function _delete()
	{
		// Check for request forgeries
		//Request::checkToken();

		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-delete-item'))
		{
			$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// Incoming
		$no_html = Request::getInt('no_html', 0);

		$post = \Components\Collections\Models\Post::getInstance(Request::getInt('post', 0));

		// The post's collection must belong to this group
		$__coll = new \Components\Collections\Models\Collection($post->get('collection_id'));
		if (!$__coll->get('id') || $__coll->get('object_type') != 'group'
		 || $__coll->get('object_id') != $this->group->get('gidNumber'))
		{
			$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		if (!$post->get('id'))
		{
			return $this->_collections();
		}

		// Two different actions share this route. Marking the ITEM deleted removes
		// it from the original board and from every repost of it anywhere on the
		// hub, so only the item's owner may do that -- _repost() will make a post
		// in this group carrying an item owned by someone outside it, so a
		// post-owner test here let a manager delete any item on the hub.
		//
		// A manager moderating their own board takes the POST off instead, which
		// leaves the item and its owner untouched. That case has to be handled
		// here rather than in _remove(): the collection view renders Delete (not
		// Remove) for an original post, and Post::remove() refuses originals
		// outright, so keying this on the item's owner alone left a manager with
		// a button that could only 403.
		$__item     = $post->item();
		$__ownsItem = ($__item->get('created_by') == User::get('id'));

		if (!$__ownsItem && !$__coll->canBeModeratedBy())
		{
			App::abort(403, Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
		}

		$process = Request::getString('process', '');
		$confirmdel = Request::getString('confirmdel', '');

		$collection = $this->model->collection($post->get('collection_id'));

		// Did they confirm delete?
		if (!$process || !$confirmdel)
		{
			if ($process && !$confirmdel)
			{
				$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_ERROR_CONFIRM_DELETION'));
				if ($no_html)
				{
					echo '';
					exit;
				}
			}

			// Output HTML
			$view = $this->view('delete', 'post');
			$view->option   = $this->option;
			$view->group    = $this->group;
			$view->task     = $this->action;
			$view->params   = $this->params;
			$view->post     = $post;
			$view->no_html  = $no_html;
			$view->name     = $this->_name;
			$view->collection = $collection;

			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}

			return $view->loadTemplate();
		}

		Request::checkToken();

		$msg = Lang::txt('PLG_GROUPS_COLLECTIONS_POST_DELETED');
		$type = 'passed';

		if ($__ownsItem)
		{
			// Mark the entry as deleted
			$item = $post->item();
			$item->set('state', 2);
			if (!$item->store())
			{
				$msg = $item->getError();
				$type = 'error';
			}
		}
		else
		{
			// Moderation: drop the post from this group's board and leave the
			// item, which belongs to someone else, exactly as it was.
			$item = $post->item();

			if (!$post->delete())
			{
				$msg  = $post->getError();
				$type = 'error';
			}
		}

		$route = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias'));

		// Record the activity
		$recipients = array(
			['group', $this->group->get('gidNumber')],
			['collection', $post->get('collection_id')],
			['user', $item->get('created_by')]
		);
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.item',
				'scope_id'    => $item->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_ITEM_DELETED', '<a href="' . $route . '">' . htmlspecialchars((string) ($item->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'collection_id' => $post->get('collection_id'),
					'post_id'       => $post->get('id'),
					'item_id'       => $item->get('id'),
					'url'           => $route
				)
			],
			'recipients' => $recipients
		]);

		// Redirect to collection
		if ($no_html)
		{
			echo $route;
			exit;
		}

		App::redirect($route, $msg, $type);
	}

	/**
	 * Save a comment
	 *
	 * @return  string
	 */
	private function _savecomment()
	{
		// Check for request forgeries
		Request::checkToken();

		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Incoming
		$data = Request::getArray('comment', array(), 'post');

		// The post being commented on, which must be one of this group's: the
		// form posts comment[item_id] itself, so without resolving the item from
		// a group-scoped post here a comment could be hung off any item on the
		// hub -- and the activity entry below would still name this group.
		$post   = new \Components\Collections\Models\Post(Request::getInt('post', 0));
		$__coll = new \Components\Collections\Models\Collection($post->get('collection_id'));

		if (!$post->get('id') || !$__coll->get('id')
		 || $__coll->get('object_type') != 'group'
		 || $__coll->get('object_id') != $this->group->get('gidNumber'))
		{
			App::abort(403, Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
		}

		// Instantiate a comment object
		$__cid = isset($data['id']) ? (int) $data['id'] : 0;
		$comment = \Hubzero\Item\Comment::oneOrNew($__cid);

		// Author only, plus a genuine site-wide manager. The group/profile-owner
		// branch that used to be here was keyed on the comment sharing an item
		// with some post in this scope -- and collection comments hang off the
		// ITEM, shared across every repost of it, so any LEGAL repost of a
		// publicly visible item satisfied it. That put every comment on that
		// item inside the reach of anyone who could repost, which is everyone.
		// No view in either plugin renders a comment edit or delete control, so
		// narrowing to the author refuses nothing the page offers, and it
		// matches com_collections' own controller.
		if (!$comment->isNew()
		 && $comment->get('created_by') != User::get('id')
		 && !User::authorise('core.manage', 'com_collections'))
		{
			App::abort(403, Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
		}

		$__isNew = $comment->isNew();
		// The author is never the submitter's to set. The members plugin already
		// unsets this; the two were not brought into line, so on the edit path
		// comment[created_by] was a real column and modify() wrote it -- letting
		// a comment be attributed to a chosen user.
		unset($data['created_by']);

		$__owner = $comment->get('created_by');

		$comment->set($data);

		// Pin what the comment hangs off, on both paths: these are form fields,
		// so an edit could otherwise re-point someone's comment at another object
		// and a new one could be attached anywhere.
		$comment->set('item_type', 'collection');
		$comment->set('item_id', (int) $post->get('item_id'));
		$comment->set('created_by', $__isNew ? User::get('id') : $__owner);

		// Store new content
		if (!$comment->save())
		{
			$this->setError($comment->getError());
			return $this->_post();
		}

		// Log activity ($post is resolved and group-checked above)
		$recipients = array(
			['group', $this->group->get('gidNumber')],
			['collection', $post->get('collection_id')],
			['user', $comment->get('created_by')]
		);
		if ($comment->get('parent'))
		{
			$recipients[] = ['user', $comment->parent()->get('created_by')];
		}
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		$title = $post->item()->get('title');
		$title = ($title ? $title : $post->item()->get('description', '#' . $post->get('id')));
		$title = \Hubzero\Utility\Str::truncate(strip_tags($title), 70);
		$url = Route::url('index.php?option=com_collections&controller=posts&post=' . $post->get('id') . '&task=comment');

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($__cid ? 'updated' : 'created'),
				'scope'       => 'collections.comment',
				'scope_id'    => $comment->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_COMMENT_' . ($__cid ? 'UPDATED' : 'CREATED'), $comment->get('id'), '<a href="' . $url . '#c' . $comment->get('id') . '">' . htmlspecialchars((string) ($title), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'collection_id' => $post->get('collection_id'),
					'post_id'       => $post->get('id'),
					'item_id'       => $comment->get('item_id'),
					'url'           => $url . '#c' . $comment->get('id')
				)
			],
			'recipients' => $recipients
		]);

		return $this->_post();
	}

	/**
	 * Delete a comment
	 *
	 * @return  string
	 */
	private function _deletecomment()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Incoming
		$id = Request::getInt('comment', 0);
		if (!$id)
		{
			return $this->_post();
		}

		// The post the comment is claimed to be on, which must be one of this
		// group's. The route that reaches this method is post/<id>/deletecomment,
		// so the post id is already in the request.
		$post   = new \Components\Collections\Models\Post(Request::getInt('post', 0));
		$__coll = new \Components\Collections\Models\Collection($post->get('collection_id'));

		if (!$post->get('id') || !$__coll->get('id')
		 || $__coll->get('object_type') != 'group'
		 || $__coll->get('object_id') != $this->group->get('gidNumber'))
		{
			App::abort(403, Lang::txt('You are not authorized to perform this action.'));
		}

		// Initiate a whiteboard comment object
		$comment = \Hubzero\Item\Comment::oneOrFail($id);

		// Author only, plus a genuine site-wide manager. The group/profile-owner
		// branch that used to be here was keyed on the comment sharing an item
		// with some post in this scope -- and collection comments hang off the
		// ITEM, shared across every repost of it, so any LEGAL repost of a
		// publicly visible item satisfied it. That put every comment on that
		// item inside the reach of anyone who could repost, which is everyone.
		// No view in either plugin renders a comment edit or delete control, so
		// narrowing to the author refuses nothing the page offers, and it
		// matches com_collections' own controller.
		if ($comment->get('created_by') != User::get('id')
		 && !User::authorise('core.manage', 'com_collections'))
		{
			App::abort(403, Lang::txt('You are not authorized to perform this action.'));
		}

		$comment->set('state', 2);

		// Delete the entry itself
		if (!$comment->save())
		{
			$this->setError($comment->getError());
		}

		// Record the activity
		/*$recipients = array(
			['group', $this->group->get('gidNumber')],
			['collection', $post->get('collection_id')],
			['user', $comment->get('created_by')],
			['user', $post->item()->get('created_by')]
		);
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		$title = $post->item()->get('title');
		$title = ($title ? $title : $post->item()->get('description', '#' . $post->get('id')));
		$title = \Hubzero\Utility\Str::truncate(strip_tags($title), 70);

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.comment',
				'scope_id'    => $comment->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_COMMENT_DELETED', $comment->get('id'), '<a href="' . Route::url($entry->link()) . '">' . htmlspecialchars((string) ($title), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'collection_id' => $post->get('collection_id'),
					'post_id'       => $post->get('id'),
					'item_id'       => $comment->get('item_id')
				)
			],
			'recipients' => $recipients
		]);*/

		// Return the topics list
		return $this->_post();
	}

	/**
	 * Vote for an item
	 *
	 * @return  void
	 */
	private function _vote()
	{
		// Login check. The collection view offers this control only to a logged-in
		// viewer -- a guest is given a login link instead -- and Item::vote() keys
		// its de-duplication row on the user id, which is 0 for a guest: every
		// guest would share one row and toggle it against the others.
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Incoming
		$id = Request::getInt('post', 0);

		// Get the post model
		$post = \Components\Collections\Models\Post::getInstance($id);

		if (!$post->get('id'))
		{
			App::abort(404, Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
		}

		// Apply the same visibility test _post() does -- the control lives in
		// views/collection/tmpl/default.php, whose posts are reached through
		// _post(), so anything _post() will show, voting has to accept. What
		// _vote() had was no check at all, which let a caller vote on a post in
		// a collection they are not allowed to see. The exists() test is half of
		// it: the archive confines a numeric board to this group, and an
		// out-of-scope one comes back empty with an access of 0, which would
		// walk straight through the two comparisons below.
		$collection = $this->model->collection($post->get('collection_id'));

		if (!$collection->exists()
		 || ($collection->get('access') == 1 && User::isGuest())
		 || ($collection->get('access') == 4 && !in_array(User::get('id'), $this->members)))
		{
			App::abort(403, Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
		}

		// Record the vote
		if (!$post->item()->vote())
		{
			$this->setError($post->item()->getError());
		}

		// Display updated item stats if called via AJAX
		$no_html = Request::getInt('no_html', 0);
		if ($no_html)
		{
			echo Lang::txt('PLG_GROUPS_COLLECTIONS_POST_LIKES', $post->item()->get('positive'));
			exit;
		}

		// ($collection is resolved and access-checked above)
		$url = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias'));

		// Record the activity
		$recipients = array(
			['group', $this->group->get('gidNumber')],
			['collection', $collection->get('id')],
			['user', $post->item()->get('created_by')],
			['user', User::get('id')]
		);

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'voted',
				'scope'       => 'collections.item',
				'scope_id'    => $post->item()->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_ITEM_VOTED', '<a href="' . $url . '">' . htmlspecialchars((string) ($collection->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'collection_id' => $collection->get('id'),
					'post_id'       => $post->get('id'),
					'item_id'       => $post->item()->get('id')
				)
			],
			'recipients' => $recipients
		]);

		// Display the main listing
		App::redirect(
			$url
		);
	}

	/**
	 * Display a form for creating a collection
	 *
	 * @return  string
	 */
	private function _newcollection()
	{
		return $this->_editcollection();
	}

	/**
	 * Display a form for editing a collection
	 *
	 * @param   object  $row
	 * @return  string
	 */
	private function _editcollection($row=null)
	{
		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-create-collection') && !$this->params->get('access-edit-collection'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name),
				Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'),
				'error'
			);
			return;
		}

		$view = $this->view('edit', 'collection');

		$view->name       = $this->_name;
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->task       = $this->action;
		$view->params     = $this->params;
		$view->no_html = Request::getInt('no_html', 0);

		if (is_object($row))
		{
			$view->entry = $row;
		}
		else
		{
			$view->entry = $this->model->collection(Request::getString('board', ''));
		}
		if (!$view->entry->exists())
		{
			$view->entry->set('access', $this->params->get('access-plugin'));
		}

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		if ($view->no_html)
		{
			$view->display();
			exit;
		}

		return $view->loadTemplate();
	}

	/**
	 * Save a collection
	 *
	 * @return  string
	 */
	private function _savecollection()
	{
		// Check for request forgeries
		Request::checkToken();

		if (User::isGuest())
		{
			return $this->_login();
		}

		if (!$this->params->get('access-edit-collection') || !$this->params->get('access-create-collection'))
		{
			$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');
		$fields['id'] = isset($fields['id']) ? intval($fields['id']) : 0;

		// An EXISTING board has to be this one's already. bind() takes the id
		// from the request and Model::store() turns that into an UPDATE, while
		// object_id and object_type are bound from the form too -- so without
		// this a caller could name any board on the hub and rewrite its owner to
		// themselves, which satisfies canBePostedToBy(), canBeModeratedBy() and
		// isReadableBy() on it in a single request.
		if ($fields['id'])
		{
			$__stored = new \Components\Collections\Models\Collection($fields['id']);

			if (!$__stored->exists()
			 || $__stored->get('object_type') != 'group'
			 || (int) $__stored->get('object_id') !== (int) $this->group->get('gidNumber'))
			{
				App::abort(403, Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			}
		}

		// ...and where it belongs is never the submitter's to set
		$fields['object_type'] = 'group';
		$fields['object_id']   = (int) $this->group->get('gidNumber');

		// Bind new content
		$collection = new \Components\Collections\Models\Collection();

		if (!$collection->bind($fields))
		{
			$this->setError($collection->getError());
			return $this->_editcollection($collection);
		}

		if ($collection->get('access') != 0 && $collection->get('access') != 4)
		{
			$collection->set('access', 0);
		}

		// Store new content
		if (!$collection->store())
		{
			$this->setError($collection->getError());
			return $this->_editcollection($collection);
		}

		$collection->item()->tag(trim(Request::getString('tags', '', 'post')));

		$url = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias'));

		// Record the activity
		$recipients = array(
			['group', $this->group->get('gidNumber')],
			['collection', $collection->get('id')],
			['user', $collection->get('created_by')]
		);
		$recipients[] = ['user', $collection->get('created_by')];
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'collections.collection',
				'scope_id'    => $collection->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_COLLECTION_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . $url . '">' . htmlspecialchars((string) ($collection->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'title' => $collection->get('title'),
					'id'    => $collection->get('id'),
					'url'   => $url
				)
			],
			'recipients' => $recipients
		]);

		// Redirect to collection
		App::redirect(
			$url
		);
	}

	/**
	 * Delete a collection
	 *
	 * @return  string
	 */
	private function _deletecollection()
	{
		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-delete-collection'))
		{
			$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// Incoming
		$no_html = Request::getInt('no_html', 0);
		$id = Request::getString('board', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			return $this->_collections();
		}

		$process = Request::getString('process', '');
		$confirmdel = Request::getString('confirmdel', '');

		// Get the collection model
		$collection = $this->model->collection($id);

		// Tables\Collection::load() short-circuits to parent::load() for a
		// NUMERIC id and drops the object_id/object_type scope, so ?board=<id>
		// resolves any board on the hub and this would soft-delete it.
		if (!$collection->exists()
		 || $collection->get('object_type') != 'group'
		 || (int) $collection->get('object_id') !== (int) $this->group->get('gidNumber'))
		{
			App::abort(403, Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
		}

		// Did they confirm delete?
		if (!$process || !$confirmdel)
		{
			if ($process && !$confirmdel)
			{
				$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			$view = $this->view('delete', 'collection')
				->set('name', $this->_name)
				->set('option', $this->option)
				->set('group', $this->group)
				->set('params', $this->params)
				->set('collection', $collection)
				->set('no_html', $no_html);

			return $view->loadTemplate();
		}

		Request::checkToken();

		// Mark the entry as deleted
		$collection->set('state', 2);
		if (!$collection->store())
		{
			$this->setError($collection->getError());
		}

		// Record the activity
		$recipients = array(
			['group', $this->group->get('gidNumber')],
			['collection', $collection->get('id')],
			['user', $collection->get('created_by')]
		);
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.collection',
				'scope_id'    => $collection->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_COLLECTION_DELETED', '<a href="' . Route::url($collection->link()) . '">' . htmlspecialchars((string) ($collection->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'title' => $collection->get('title'),
					'id'    => $collection->get('id'),
					'url'   => $collection->link()
				)
			],
			'recipients' => $recipients
		]);

		// Redirect to main view
		$route = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name);

		if ($no_html)
		{
			echo $route;
			exit;
		}

		App::redirect($route);
	}

	/**
	 * Display settings
	 *
	 * @return  string
	 */
	private function _settings()
	{
		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		if ($this->authorized != 'manager' && $this->authorized != 'admin')
		{
			App::abort(403, Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
		}

		// Output HTML
		$view = $this->view('default', 'settings')
			->set('name', $this->_name)
			->set('option', $this->option)
			->set('group', $this->group)
			->set('params', $this->params)
			->set('action', $this->action)
			->set('authorized', $this->authorized);

		return $view
			->setErrors($this->getErrors())
			->loadTemplate();
	}

	/**
	 * Save blog settings
	 *
	 * @return  void
	 */
	private function _savesettings()
	{
		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		if ($this->authorized != 'manager' && $this->authorized != 'admin')
		{
			$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// Check for request forgeries
		Request::checkToken();

		$row = \Hubzero\Plugin\Params::oneByPluginOrNew($this->group->get('gidNumber'), $this->_type, $this->_name);

		// Get parameters
		$params = new \Hubzero\Config\Registry(Request::getArray('params', array(), 'post'));
		$row->set('params', $params->toString());

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->_settings();
		}

		// Record the activity
		$recipients = array(['group', $this->group->get('gidNumber')]);
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'updated',
				'scope'       => 'collections.settings',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_SETTINGS_UPDATED')
			],
			'recipients' => $recipients
		]);

		App::redirect(
			Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=' . $this->_name),
			Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_SAVED'),
			'passed'
		);
	}

	/**
	 * Get the group's custom params
	 *
	 * @param   integer  $group_id
	 * @return  object
	 */
	protected function _params($group_id)
	{
		if (!$this->_params)
		{
			$this->_params = \Hubzero\Plugin\Params::getCustomParams($group_id, 'groups', $this->_name);
		}
		return $this->_params;
	}

	/**
	 * Set permissions
	 *
	 * @param   string   $assetType  Type of asset to set permissions for (component, section, category, thread, post)
	 * @param   integer  $assetId    Specific object to check permissions for
	 * @return  void
	 */
	protected function _authorize($assetType='plugin', $assetId=null)
	{
		// Everyone can view by default
		$this->params->set('access-view', true);
		$this->params->set('access-can-follow', false);
		if (!User::isGuest() && $this->group->published == 1)
		{
			$customParams = $this->_params($this->group->get('gidNumber'));
			$this->params->merge($customParams);

			// Set asset to viewable
			$isMember = in_array(User::get('id'), $this->members);

			$this->params->set('access-view-' . $assetType, false);
			if ($isMember)
			{
				$this->params->set('access-view-' . $assetType, true);
			}

			// Can NOT create, delete, or edit by default
			$this->params->set('access-create-' . $assetType, false);
			$this->params->set('access-delete-' . $assetType, false);
			$this->params->set('access-edit-' . $assetType, false);
			switch ($assetType)
			{
				case 'collection':
					// Only managers and admins can work with boards
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-manage-' . $assetType, true);
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
					if (!$this->params->get('create_collection', 1) && $isMember)
					{
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
				case 'item':
					// All members can post bulletins
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-manage-' . $assetType, true);
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
					if (!$this->params->get('create_post', 0) && $isMember)
					{
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
				case 'plugin':
				default:
					// Only managers and admins
					if ($this->authorized == 'admin' || $this->authorized == 'manager')
					{
						$this->params->set('access-manage-' . $assetType, true);
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
			}
		}
	}
}
