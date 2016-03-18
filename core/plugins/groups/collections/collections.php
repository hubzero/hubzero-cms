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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * Remove any associated data when group is deleted
	 *
	 * @param   object  $group  Group being deleted
	 * @return  string  Log of items removed
	 */
	public function onGroupDelete($group)
	{
		// Import needed libraries
		include_once(PATH_CORE . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'archive.php');

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
		include_once(PATH_CORE . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'archive.php');

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
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$return = 'html';
		$active = $this->_name;

		// The output array we're returning
		$arr = array(
			'html'     => '',
			'metadata' => ''
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

		include_once(PATH_CORE . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'archive.php');

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
				case 'savecomment':   $arr['html'] = $this->_savecomment();   break;
				case 'newcomment':    $arr['html'] = $this->_newcomment();    break;
				case 'editcomment':   $arr['html'] = $this->_editcomment();   break;
				case 'deletecomment': $arr['html'] = $this->_deletecomment(); break;

				case 'followers': $arr['html'] = $this->_followers(); break;
				case 'following': $arr['html'] = $this->_following(); break;
				case 'follow':    $arr['html'] = $this->_follow('group');    break;
				case 'unfollow':  $arr['html'] = $this->_unfollow('group');  break;

				// Entries
				case 'savepost':   $arr['html'] = $this->_save();   break;
				case 'newpost':    $arr['html'] = $this->_new();    break;
				case 'editpost':   $arr['html'] = $this->_edit();   break;
				case 'deletepost': $arr['html'] = $this->_delete(); break;
				case 'posts':      $arr['html'] = $this->_posts();  break;

				case 'comment':
				case 'post':   $arr['html'] = $this->_post();   break;
				case 'vote':   $arr['html'] = $this->_vote();   break;
				case 'collect': $arr['html'] = $this->_repost(); break;
				case 'remove': $arr['html'] = $this->_remove(); break;
				case 'move':   $arr['html'] = $this->_move();   break;

				case 'followcollection': $arr['html'] = $this->_follow('collection'); break;
				case 'unfollowcollection': $arr['html'] = $this->_unfollow('collection'); break;
				case 'collectcollection': $arr['html'] = $this->_repost();      break;
				case 'newcollection':    $arr['html'] = $this->_newcollection();    break;
				case 'editcollection':   $arr['html'] = $this->_editcollection();   break;
				case 'savecollection':   $arr['html'] = $this->_savecollection();   break;
				case 'deletecollection': $arr['html'] = $this->_deletecollection(); break;
				case 'all':
				case 'collections':      $arr['html'] = $this->_collections();      break;

				case 'settings': $arr['html'] = $this->_settings(); break;
				case 'savesettings': $arr['html'] = $this->_savesettings(); break;

				case 'collection': $arr['html'] = $this->_collection(); break;

				default: $arr['html'] = $this->_collections(); break;
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
			'search'        => Request::getVar('search', ''),
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
				$collection = $this->model->collection(Request::getVar('board', ''));
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
				$collection = $this->model->collection(Request::getVar('board', ''));
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
			'search'      => Request::getVar('search', ''),
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

		// Check authorization
		if (!$this->params->get('access-view-item'))
		{
			return App::abort(403, Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
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
	 * @param   object  $row
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
		if ($no_html)
		{
			$type = strtolower(Request::getWord('type', 'file'));
			if (!in_array($type, array('file', 'image', 'text', 'link')))
			{
				$type = 'file';
			}

			$view = $this->view('edit_' . $type, 'post');
		}
		else
		{
			$view = $this->view('edit', 'post');
		}
		$view->name        = $this->_name;
		$view->option      = $this->option;
		$view->group       = $this->group;
		$view->task        = $this->action;
		$view->params      = $this->params;
		$view->no_html     = $no_html;

		$id = Request::getInt('post', 0);

		$view->collection = $this->model->collection(Request::getVar('board', 0));

		$view->collections = $this->model->collections();
		if (!$view->collections->total())
		{
			$view->collection->setup($this->group->get('cn'), 'group');
			$view->collections = $this->model->collections();
			$view->collection = $this->model->collection(Request::getVar('board', 0));
		}

		$view->entry = (is_object($entry) ? $entry : $view->collection->post($id));
		if (!$view->collection->exists() && $view->entry->exists())
		{
			$view->collection = $this->model->collection($view->entry->get('collection_id'));
		}

		if ($remove = Request::getInt('remove', 0))
		{
			if (!$view->entry->item()->removeAsset($remove))
			{
				$view->setError($view->entry->item()->getError());
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
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

		if ($fields['id'] && !is_numeric($fields['id']))
		{
			App::abort(404, Lang::txt('Post does not exist'));
		}

		// Get model
		$item = new \Components\Collections\Models\Item(intval($fields['id']));

		// Bind content
		if (!$item->bind($fields))
		{
			$this->setError($item->getError());
			return $this->_edit($item);
		}

		// Add some data
		if ($files  = Request::getVar('fls', '', 'files', 'array'))
		{
			$item->set('_files', $files);
		}
		$item->set('_assets', Request::getVar('assets', null, 'post'));
		$item->set('_tags', trim(Request::getVar('tags', '')));
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

		// Create a post entry linking the item to the board
		$p = Request::getVar('post', array(), 'post');

		$post = new \Components\Collections\Models\Post($p['id']);
		if (!$post->exists())
		{
			$post->set('item_id', $item->get('id'));
			$post->set('original', 1);
		}

		if (!isset($p['collection_id']))
		{
			$p['collection_id'] = 0;

			if ($coltitle = Request::getVar('collection_title', '', 'post'))
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

		// Record the activity
		$recipients = array(['group', $this->group->get('gidNumber')]);
		$recipients[] = ['user', $item->get('created_by')];
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		$url = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $this->model->collection($p['collection_id'])->get('alias');

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'collections.item',
				'scope_id'    => $item->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_ITEM_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($url) . '">' . $item->get('title') . '</a>'),
				'details'     => array(
					'title'    => $item->get('title'),
					'post_id'  => $post->get('id'),
					'url'      => $url
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

		/*if (!$this->params->get('access-create-item'))
		{
			$this->setError(Lang::txt('PLG_GROUPS_COLLECTIONS_NOT_AUTHORIZED'));
			return $this->_collections();
		}*/

		$no_html = Request::getInt('no_html', 0);

		// No board ID selected so present repost form
		$repost = Request::getInt('repost', 0);
		if (!$repost)
		{
			// Incoming
			$post_id       = Request::getInt('post', 0);
			$collection_id = Request::getVar('board', 0);

			if (!$post_id && $collection_id)
			{
				$collection = $this->model->collection($collection_id);

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
		if (!$collection_id)
		{
			$collection = new \Components\Collections\Models\Collection();
			$collection->set('title', Request::getVar('collection_title', ''));
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
			$post->description   = Request::getVar('description', '', 'none', 2);
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
		$recipients = array(['group', $this->group->get('gidNumber')]);
		$recipients[] = ['user', $post->get('created_by')];
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		if (!isset($collection))
		{
			$collection = new \Components\Collections\Models\Collection($collection_id);
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'created',
				'scope'       => 'collections.post',
				'scope_id'    => $post->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_POST_CREATED', '<a href="' . Route::url($collection->link()) . '">' . $post->get('title') . '</a>'),
				'details'     => array(
					'title'    => $post->get('title'),
					'post_id'  => $post->get('id'),
					'url'      => $collection->link()
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
	 * Repost an entry
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

		$collection = $this->model->collection($post->get('collection_id'));

		$msg = Lang::txt('Post removed.');
		$type = 'passed';
		if (!$post->remove())
		{
			$msg = $post->getError();
			$type = 'error';
		}

		// Record the activity
		$recipients = array(['group', $this->group->get('gidNumber')]);
		$recipients[] = ['user', $item->get('created_by')];
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		$route = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias'));

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.post',
				'scope_id'    => $post->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_POST_DELETED', '<a href="' . $route . '">' . $post->get('title') . '</a>'),
				'details'     => array(
					'title'    => $post->get('title'),
					'entry_id' => $post->get('id'),
					'url'      => $route
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

		if (!$post->move(Request::getInt('board', 0)))
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
		if (!$post->get('id'))
		{
			return $this->_collections();
		}

		$process = Request::getVar('process', '');
		$confirmdel = Request::getVar('confirmdel', '');

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

		// Mark the entry as deleted
		$item = $post->item();
		$item->set('state', 2);
		if (!$item->store())
		{
			$msg = $item->getError();
			$type = 'error';
		}

		// Record the activity
		$recipients = array(['group', $this->group->get('gidNumber')]);
		$recipients[] = ['user', $item->get('created_by')];
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		$route = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias'));

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.item',
				'scope_id'    => $item->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_ITEM_DELETED', '<a href="' . $route . '">' . $item->get('title') . '</a>'),
				'details'     => array(
					'title'    => $item->get('title'),
					'entry_id' => $item->get('id'),
					'url'      => $route
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
		$data = Request::getVar('comment', array(), 'post');

		// Instantiate a new comment object and pass it the data
		$comment = \Hubzero\Item\Comment::oneOrNew($data['id'])->set($data);

		// Store new content
		if (!$comment->save())
		{
			$this->setError($comment->getError());
			return $this->_post();
		}

		// Log activity
		$recipients = array(['group', $this->group->get('gidNumber')]);
		$recipients[] = ['user', $comment->get('created_by')];
		if ($comment->get('parent'))
		{
			$recipients[] = ['user', $comment->parent()->get('created_by')];
		}
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		$post = \Components\Collections\Models\Post::getInstance($comment->get('item_id'));
		$url = Route::url('index.php?option=com_collections&controller=posts&post=' . $post->get('id') . '&task=comment');

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($data['id'] ? 'updated' : 'created'),
				'scope'       => 'collections.comment',
				'scope_id'    => $comment->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_COMMENT_' . ($data['id'] ? 'UPDATED' : 'CREATED'), $comment->get('id'), '<a href="' . $url . '#c' . $comment->get('id') . '">' . $post->get('title', '#' . $post->get('id')) . '</a>'),
				'details'     => array(
					'title'    => $post->get('title'),
					'entry_id' => $post->get('id'),
					'url'      => $url . '#c' . $comment->get('id')
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

		// Initiate a whiteboard comment object
		$comment = \Hubzero\Item\Comment::oneOrFail($id);
		$comment->set('state', 2);

		// Delete the entry itself
		if (!$comment->save())
		{
			$this->setError($comment->getError());
		}

		// Record the activity
		/*$recipients = array(['group', $this->group->get('gidNumber')]);
		$recipients[] = ['user', $comment->get('created_by')];
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.comment',
				'scope_id'    => $comment->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_COMMENT_DELETED', $comment->get('id'), '<a href="' . Route::url($entry->link()) . '">' . $entry->get('title') . '</a>'),
				'details'     => array(
					'title'    => $entry->get('title'),
					'entry_id' => $entry->get('id'),
					'url'      => $entry->link()
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
		// Incoming
		$id = Request::getInt('post', 0);

		// Get the post model
		$post = \Components\Collections\Models\Post::getInstance($id);

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

		// Get the collection model
		$collection = $this->model->collection($post->get('collection_id'));

		// Record the activity
		$recipients = array(['group', $this->group->get('gidNumber')]);
		$recipients[] = ['user', User::get('id')];

		$url = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias'));

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'voted',
				'scope'       => 'collections.item',
				'scope_id'    => $id,
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_ITEM_VOTED', '<a href="' . $url . '">' . $collection->get('title') . '</a>'),
				'details'     => array(
					'title'    => $collection->get('title'),
					'entry_id' => $collection->get('id'),
					'url'      => $url
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
			$view->entry = $this->model->collection(Request::getVar('board', ''));
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
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields['id'] = intval($fields['id']);

		// Bind new content
		$collection = new \Components\Collections\Models\Collection();

		if (!$collection->bind($fields))
		{
			$this->setError($collection->getError());
			return $this->_editcollection($collection);
		}

		// Store new content
		if (!$collection->store())
		{
			$this->setError($collection->getError());
			return $this->_editcollection($collection);
		}

		// Record the activity
		$recipients = array(['group', $this->group->get('gidNumber')]);
		$recipients[] = ['user', $collection->get('created_by')];
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		$url = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias'));

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'collections.collection',
				'scope_id'    => $collection->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_COLLECTION_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . $url . '">' . $collection->get('title') . '</a>'),
				'details'     => array(
					'title'    => $collection->get('title'),
					'entry_id' => $collection->get('id'),
					'url'      => $url
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
		$id = Request::getVar('board', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			return $this->_collections();
		}

		$process = Request::getVar('process', '');
		$confirmdel = Request::getVar('confirmdel', '');

		// Get the collection model
		$collection = $this->model->collection($id);

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
				->set('action', $this->action)
				->set('settings', $settings)
				->set('collection', $collection)
				->set('no_html', $this->no_html);

			return $view
				->setErrors($this->getErrors())
				->loadTemplate();
		}

		Request::checkToken();

		// Mark the entry as deleted
		$collection->set('state', 2);
		if (!$collection->store())
		{
			$this->setError($collection->getError());
		}

		// Record the activity
		$recipients = array(['group', $this->group->get('gidNumber')]);
		$recipients[] = ['user', $collection->get('created_by')];
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.collection',
				'scope_id'    => $collection->get('id'),
				'description' => Lang::txt('PLG_GROUPS_COLLECTIONS_ACTIVITY_COLLECTION_DELETED', '<a href="' . Route::url($collection->link()) . '">' . $collection->get('title') . '</a>'),
				'details'     => array(
					'title'    => $collection->get('title'),
					'entry_id' => $collection->get('id'),
					'url'      => $collection->link()
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

		$settings = \Hubzero\Plugin\Params::oneByPlugin($this->group->get('gidNumber'), 'groups', $this->_name);

		// Output HTML
		$view = $this->view('default', 'settings')
			->set('name', $this->_name)
			->set('option', $this->option)
			->set('group', $this->group)
			->set('params', $this->params)
			->set('action', $this->action)
			->set('settings', $settings)
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

		$settings = Request::getVar('settings', array(), 'post');

		$row = \Hubzero\Plugin\Params::oneByPlugin($this->group->get('gidNumber'), $this->_type, $this->_name);

		$row->set('object_id', $this->group->get('gidNumber'));
		$row->set('folder', $this->_type);
		$row->set('element', $this->_name);

		// Get parameters
		$prms = Request::getVar('params', array(), 'post');

		$params = new \Hubzero\Config\Registry($prms);

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
		if (!User::isGuest())
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
