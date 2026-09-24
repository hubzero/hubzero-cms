<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for collections
 */
class plgMembersCollections extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @return  array   Plugin name
	 */
	public function onMembersAreas($user, $member)
	{
		$areas = array(
			'collections' => Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name)),
			'icon'        => 'f005',
			'icon-class' => 'icon-archive',
			'menu'        => $this->params->get('display_tab', 1)
		);
		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @param   string  $option  Component name
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$arr = array(
			'html'     => '',
			'metadata' => array()
		);
		$returnhtml = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		$this->member = $member;

		$this->_authorize('collection');

		include_once \Component::path('com_collections') . DS . 'models' . DS . 'archive.php';
		$this->model = new \Components\Collections\Models\Archive('member', $this->member->get('id'));

		//are we returning html
		if ($returnhtml)
		{
			// This needs to be called to ensure scripts are pushed to the document
			$foo = App::get('editor')->display('description', '', '', '', 35, 5, false, 'field_description', null, null, array('class' => 'minimal no-footer'));

			// Set some variables so other functions have access
			$this->option   = $option;
			$this->database = App::get('db');

			$this->_authorize('item');

			$default = $this->params->get('defaultView', 'feed');
			if (User::get('id') != $member->get('id'))
			{
				$default = 'collections';
			}
			$this->action = Request::getCmd('action', $default);

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
					$arr['html'] = $this->_follow('member');
					break;
				case 'unfollow':
					$arr['html'] = $this->_unfollow('member');
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

				case 'collection':
					$arr['html'] = $this->_collection();
					break;
				case 'feed':
					$arr['html'] = $this->_feed();
					break;
				default:
					if ($this->params->get('defaultView', 'feed') == 'collections')
					{
						$arr['html'] = $this->_collections();
					}
					else
					{
						$arr['html'] = $this->_feed();
					}
				break;
			}
		}

		// Get a count of all the collections
		$filters = array(
			'count' => true
		);

		if (!$this->model->collections($filters))
		{
			$collection = $this->model->collection(0);
			$collection->setup($this->member->get('id'), 'member');
		}

		if (!$this->params->get('access-manage-collection'))
		{
			$filters['access'] = 0;
		}
		$arr['metadata']['count'] = $this->model->collections($filters);

		return $arr;
	}

	/**
	 * Redirect to the login form
	 *
	 * @return     void
	 */
	private function _login()
	{
		$route = Route::url('index.php?option=' . $this->option . '&id=' . $this->member->get('id') . '&active=' . $this->_name);

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($route)),
			Lang::txt('MEMBERS_LOGIN_NOTICE'),
			'warning'
		);
		return;
	}

	/**
	 * Display a list of collections
	 *
	 * @return     string
	 */
	private function _followers()
	{
		$view = $this->view('followers', 'follow');
		$view->name        = $this->_name;
		$view->option      = $this->option;
		$view->member      = $this->member;
		$view->params      = $this->params;
		$view->model       = $this->model;

		// Filters for returning results
		$view->filters = array();
		$view->filters['limit'] = Request::getInt('limit', Config::get('list_limit'));
		$view->filters['start'] = Request::getInt('limitstart', 0);

		$count = array(
			'count'  => true
		);

		if (!$this->params->get('access-manage-collection'))
		{
			$count['access'] = (User::isGuest() ? 0 : array(0, 1));
			$view->filters['access'] = $count['access'];
		}

		$view->collections = $this->model->collections($count);

		$view->posts       = $this->model->posts($count);

		$view->following   = $this->model->following($count);

		$view->total = $this->model->followers($count);

		$view->rows = $this->model->followers($view->filters);

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
	private function _following()
	{
		$view = $this->view('following', 'follow');
		$view->name        = $this->_name;
		$view->option      = $this->option;
		$view->member      = $this->member;
		$view->params      = $this->params;
		$view->model       = $this->model;

		// Filters for returning results
		$view->filters = array();
		$view->filters['limit'] = Request::getInt('limit', Config::get('list_limit'));
		$view->filters['start'] = Request::getInt('limitstart', 0);

		$filters = array();
		$filters['user_id'] = User::get('id');
		$filters['state']   = 1;

		$count = array(
			'count'  => true
		);

		$filters = array();
		if (!$this->params->get('access-manage-collection'))
		{
			$filters['access'] = (User::isGuest() ? 0 : array(0, 1));
			$count['access'] = $filters['access'];
		}

		//$filters['count'] = true;
		$view->collections = $this->model->collections($count);

		$view->posts       = $this->model->posts($count);

		$view->followers   = $this->model->followers($count);

		$view->total = $this->model->following($count);
		$view->rows  = $this->model->following($view->filters);

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
		$view = $this->view('collections', 'collection');
		$view->name        = $this->_name;
		$view->option      = $this->option;
		$view->member      = $this->member;
		$view->params      = $this->params;
		$view->model       = $this->model;

		// Filters for returning results
		$view->filters = array(
			'limit'   => Request::getInt('limit', Config::get('list_limit')),
			'start'   => Request::getInt('limitstart', 0),
			'search'  => Request::getString('search', ''),
			'state'   => 1,
			'user_id' => User::get('id')
		);

		$count = array(
			'count'  => true,
			'state'  => 1
		);

		if (!$this->params->get('access-manage-collection'))
		{
			$view->filters['access'] = (User::isGuest() ? 0 : array(0, 1));
			$count['access']   = $view->filters['access'];
		}

		$view->filters['count'] = true;
		$view->total = $this->model->collections($view->filters);

		$view->filters['count'] = false;
		$view->rows  = $this->model->collections($view->filters);

		$view->posts     = $this->model->posts($count);
		$view->followers = $this->model->followers($count);
		$view->following = $this->model->following($count);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a list of items in a collection
	 *
	 * @return     string
	 */
	private function _collection()
	{
		$view = $this->view('default', 'collection');
		$view->name       = $this->_name;
		$view->member     = $this->member;
		$view->option     = $this->option;
		$view->params     = $this->params;
		$view->model      = $this->model;

		// Filters for returning results
		$view->filters = array(
			'limit'         => Request::getInt('limit', Config::get('list_limit')),
			'start'         => Request::getInt('limitstart', 0),
			'user_id'       => $this->member->get('id'),
			'search'        => Request::getString('search', ''),
			'state'         => 1,
			'collection_id' => Request::getString('board', ''),
			'access'        => -1
		);

		$view->collection = $this->model->collection($view->filters['collection_id']);
		if (!$view->collection->exists())
		{
			App::abort(404, Lang::txt('Collection not found.'));
			return;
		}

		// Is the board restricted to logged-in users only?
		if ($view->collection->get('access') != 0 && User::isGuest())
		{
			return $this->_login();
		}

		// Is it a private board?
		if ($view->collection->get('access') == 4 && User::get('id') != $this->member->get('id'))
		{
			App::abort(403, Lang::txt('Your are not authorized to access this content.'));
			return;
		}

		$view->filters['sort'] = Request::getWord('sort', $view->collection->get('sort'));
		if (!in_array($view->filters['sort'], array('created', 'ordering')))
		{
			$view->filters['sort'] = 'created';
		}
		$view->filters['sort_Dir'] = ($view->filters['sort'] == 'ordering' ? 'asc' : 'desc');

		$count = array(
			'count'  => true,
			'state'  => 1,
			'access' => -1
		);
		if (!$this->params->get('access-manage-collection'))
		{
			$view->filters['access'] = (User::isGuest() ? 0 : array(0, 1));
			$count['access'] = $view->filters['access'];
		}

		$view->collections = $this->model->collections($count);
		$view->posts       = $this->model->posts($count);
		$view->following   = $this->model->following($count);
		$view->followers   = $this->model->followers($count);

		$view->filters['collection_id'] = $view->collection->get('id');

		$view->filters['count'] = true;
		$view->total = $view->collection->posts($view->filters);

		$view->filters['count'] = null;
		$view->rows = $view->collection->posts($view->filters);

		$view->task = $view->collection->get('alias');

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a list of items in a collection
	 *
	 * @param      string  $what
	 * @return     string
	 */
	private function _follow($what='collection')
	{
		// Is the board restricted to logged-in users only?
		if (User::isGuest())
		{
			return $this->_login();
		}

		if (User::get('id') == $this->member->get('id'))
		{
			App::abort(500, Lang::txt('Your cannot follow your own content.'));
			return;
		}

		$sfx = '';
		switch ($what)
		{
			case 'group':
				$id = $this->group->get('gidNumber');
			break;

			case 'member':
				$id = $this->member->get('id');
				$sfx = '&task=unfollow';
			break;

			case 'collection':
				$collection = $this->model->collection(Request::getString('board', ''));
				if (!$collection->exists())
				{
					App::abort(404, Lang::txt('Collection does not exist'));
					return;
				}
				$id = $collection->get('id');
				$sfx = '&task=' . $collection->get('alias') . '/unfollow';
			break;
		}

		if (!$this->model->follow($id, $what, User::get('id'), 'member'))
		{
			$this->setError($this->model->getError());
		}

		if (Request::getInt('no_html', 0))
		{
			$response = new stdClass;
			$response->href = Route::url($this->member->link() . '&active=collections' . $sfx);
			$response->success = true;
			if ($this->getError())
			{
				$response->success = false;
				$response->error = $this->getError();
			}
			echo json_encode($response);
			exit;
		}
		else
		{
			return $this->_feed();
		}
	}

	/**
	 * Display a list of items in a collection
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

		// Is it a private board?
		if (User::get('id') == $this->member->get('id'))
		{
			App::abort(500, Lang::txt('Your cannot unfollow your own content.'));
			return;
		}

		$sfx = '';
		switch ($what)
		{
			case 'group':
				$id = $this->group->get('gidNumber');
			break;

			case 'member':
				$id = $this->member->get('id');
				$sfx = '&task=follow';
			break;

			case 'collection':
				$collection = $this->model->collection(Request::getString('board', ''));
				if (!$collection->exists())
				{
					App::abort(404, Lang::txt('Collection does not exist'));
					return;
				}
				$id = $collection->get('id');
				$sfx = '&task=' . $collection->get('alias') . '/follow';
			break;
		}

		if (!$this->model->unfollow($id, $what, User::get('id'), 'member'))
		{
			$this->setError($this->model->getError());
		}

		if (Request::getInt('no_html', 0))
		{
			$response = new stdClass;
			$response->href = Route::url($this->member->link() . '&active=collections' . $sfx);
			$response->success = true;
			if ($this->getError())
			{
				$response->success = false;
				$response->error = $this->getError();
			}
			echo json_encode($response);
			exit;
		}
		else
		{
			return $this->_feed();
		}
	}

	/**
	 * Display a list of items in a collection
	 *
	 * @return  string
	 */
	private function _feed()
	{
		$view = $this->view('feed', 'collection');
		$view->name       = $this->_name;
		$view->member     = $this->member;
		$view->option     = $this->option;
		$view->params     = $this->params;

		$view->filters = array();
		$view->filters['limit']       = Request::getInt('limit', Config::get('list_limit'));
		$view->filters['start']       = Request::getInt('limitstart', 0);
		$view->filters['user_id']     = $this->member->get('id');
		$view->filters['search']      = Request::getString('search', '');
		$view->filters['state']       = 1;
		$view->filters['collection_id'] = Request::getString('board', '');

		// Filters for returning results
		$count = array(
			'count' => true
		);
		if (!$this->params->get('access-manage-collection'))
		{
			$count['access'] = 0;
		}

		$view->collections = $this->model->collections($count);

		$view->posts       = $this->model->posts($count);

		$view->followers   = $this->model->followers($count);

		$view->following   = $this->model->following($count);

		$view->filters['collection_id'] = $this->model->following(array(), 'collections');
		$view->collection = \Components\Collections\Models\Collection::getInstance();
		if (count($view->filters['collection_id']) <= 0)
		{
			$view->filters['collection_id'][] = -1;
		}

		$view->filters['count'] = true;
		$view->total = $view->collection->posts($view->filters);

		$view->filters['count'] = null;
		$view->rows = $view->collection->posts($view->filters);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a list of items in a collection
	 *
	 * @return  string
	 */
	private function _posts()
	{
		$view = $this->view('default', 'collection')
			->set('name', $this->_name)
			->set('option', $this->option)
			->set('member', $this->member)
			->set('params', $this->params)
			->set('model', $this->model);

		// Filters for returning results
		$view->filters = array(
			'limit'       => Request::getInt('limit', Config::get('list_limit')),
			'start'       => Request::getInt('limitstart', 0),
			'created_by'  => $this->member->get('id'),
			'search'      => Request::getString('search', ''),
			'state'       => 1,
			'object_id'   => $this->member->get('id'),
			'object_type' => 'member',
			'access'      => -1,
			'sort'        => 'created',
			'sort_Dir'    => 'desc'
		);

		// Filters for returning results
		//$filters = array();
		$count = array(
			'count' => true
		);

		if (!$this->params->get('access-manage-collection'))
		{
			$view->filters['access'] = (User::isGuest() ? 0 : array(0, 1));
			$count['access'] = $view->filters['access'];
		}

		$view->collections = $this->model->collections($count);
		$view->followers   = $this->model->followers($count);
		$view->following   = $this->model->following($count);
		$view->posts       = $this->model->posts($count);
		$view->total = $view->posts;

		$view->collection = \Components\Collections\Models\Collection::getInstance();

		$view->filters['user_id'] = $this->member->get('id');

		$view->rows = $view->collection->posts($view->filters);

		$view->task = 'posts';

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

		// A post is reached by bare numeric id and Post::getInstance() returns any
		// row on the hub, so the board it sits on has to be readable. It must NOT
		// be required to be this member's: _feed() lists posts from the boards
		// this member FOLLOWS, which are by construction somebody else's (_follow()
		// refuses your own), and feed.php renders a closeup link for each one. The
		// archive is scoped to this member, so resolve through it first and fall
		// back to an unscoped lookup, then ask the readability predicate -- which
		// is what com_collections' own controller does.
		$collection = $this->model->collection($post->get('collection_id'));

		if (!$collection->exists())
		{
			$collection = new \Components\Collections\Models\Collection($post->get('collection_id'));
		}

		// An unresolved board reads access 0, i.e. "public", which is how a
		// private post used to render through whichever profile the caller
		// happened to be on.
		if (!$collection->isReadableBy())
		{
			$this->params->set('access-view-item', false);
		}

		// Check authorization
		if (!$this->params->get('access-view-item'))
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTH'));
			return;
		}

		$no_html = Request::getInt('no_html', 0);

		$view = $this->view('default', 'post')
			->set('name', $this->_name)
			->set('option', $this->option)
			->set('member', $this->member)
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
		$restrictUsers = \Component::params('com_answers')->get('restrict_users');
		if ($restrictUsers == 'active')
		{
			$restrictDays = \Component::params('com_answers')->get('restrict_days');
			$now = new \DateTime();
			$registered = new \DateTime(User::get('registerDate'));
			if ($now->diff($registered)->days < $restrictDays)
			{
				App::redirect(
					Route::url('index.php?option=com_members'),
					Lang::txt('PLG_MEMBERS_COLLECTIONS_ERROR_NEW_ACCOUNT'),
					'warning'
				);
				return;
			}
		}

		if (!$this->params->get('access-edit-item') && !$this->params->get('access-create-item'))
		{
			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name),
				Lang::txt('You are not authorized to perform this action.'),
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
		// threads $this->no_html through its form action and a hidden input, so
		// it is the right template for the AJAX case too.
		$view = $this->view('edit', 'post');
		$view->name       = $this->_name;
		$view->option     = $this->option;
		$view->member     = $this->member;
		$view->task       = $this->action;
		$view->params     = $this->params;
		$view->no_html     = $no_html;

		$id = Request::getInt('post', 0);

		$view->collection = $this->model->collection(Request::getString('board', 0));

		$view->collections = $this->model->collections();
		if (!$view->collections->total())
		{
			$view->collection->setup($this->member->get('id'), 'member');
			$view->collections = $this->model->collections();
			$view->collection  = $this->model->collection(Request::getString('board', 0));
		}

		$view->entry = (is_object($entry) ? $entry : $view->collection->post($id));
		if (!$view->collection->exists() && $view->entry->exists())
		{
			$view->collection = $this->model->collection($view->entry->get('collection_id'));
		}

		// post= resolves any row on the hub, and the form prints the item's
		// title, description, assets and tags. Only what _save() will accept is
		// shown: the caller's own post (a repost included) or their own item.
		if ($view->entry->exists()
		 && $view->entry->get('created_by') != User::get('id')
		 && $view->entry->item()->get('created_by') != User::get('id'))
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		if ($remove = Request::getInt('remove', 0))
		{
			// As com_collections' own posts controller: the post is chosen by id
			// and resolves to any row, so the item whose asset is deleted was the
			// caller's to pick. The test is on the ITEM's owner, because the asset
			// hangs off the item and a repost carries the reposter's created_by
			// with the original author's item_id.
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

		if (User::isGuest())
		{
			return $this->_login();
		}

		if (!$this->params->get('access-create-item') && !$this->params->get('access-edit-item'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
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
		$p = Request::getArray('post', array(), 'post');
		$post = new \Components\Collections\Models\Post(isset($p['id']) ? intval($p['id']) : 0);
		if ($post->exists()
		 && ($post->get('created_by') != User::get('id') || intval($post->get('item_id')) !== intval($item->get('id'))))
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		// The item itself may only be written by its owner: for a repost the
		// content stays the original author's and only the post is updated below
		$writeItem = !$item->exists() || $item->get('created_by') == User::get('id');
		if (!$post->exists() && !$writeItem)
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
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

		// Update for PHP 8.2: id cannot by an emptry string. It has to be null.
		if ($item->get("id") === "") {
			$item->set("id", null);
		}

		// Add some data
		if ($files  = Request::getArray('fls', '', 'files'))
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
		} // end of the item write (skipped for a repost)

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

		// Create a post entry linking the item to the board
		$p = Request::getArray('post', array(), 'post');

		// The board is chosen from a select listing this member's own collections,
		// but the id arrives from the request: only post to a board the member owns
		// (or a group board they belong to). An empty value creates a new board below.
		if (!empty($p['collection_id']) && !$this->mayPostToCollection($p['collection_id']))
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		$post = new \Components\Collections\Models\Post($p['id']);
		if (!$post->exists())
		{
			$post->set('item_id', $item->get('id'));
			$post->set('original', 1);
		}

		$coltitle = Request::getString('collection_title', '', 'post');
		if (empty($p['collection_id']) && $coltitle)
		{
			$collection = new \Components\Collections\Models\Collection();
			$collection->set('title', $coltitle);
			$collection->set('object_id', $this->member->get('id'));
			$collection->set('object_type', 'member');
			$collection->store();

			$p['collection_id'] = $collection->get('id');
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
			//return $this->_edit($row);
			Request::setVar('post', $p['id']);
			return $this->_edit($post->item()->bind($fields));
		}

		if (!isset($collection))
		{
			$collection = new \Components\Collections\Models\Collection($p['collection_id']);
		}

		$url = $this->member->link() . '&active=' . $this->_name . '&task=' . $collection->get('alias');

		// Record the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($p['id'] ? 'updated' : 'created'),
				'scope'       => 'collections.item',
				'scope_id'    => $item->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_POST_' . ($p['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($url) . '">' . htmlspecialchars((string) ($collection->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'collection_id' => $collection->get('id'),
					'post_id'       => $post->get('id'),
					'item_id'       => $post->get('item_id'),
					'url'           => $url
				)
			],
			'recipients' => [
				['collection', $collection->get('id')],
				['user', $item->get('created_by')]
			]
		]);

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
					App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
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
			$view->member        = $this->member;
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
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		if (!$collection_id)
		{
			$collection = new \Components\Collections\Models\Collection();
			$collection->set('title', Request::getString('collection_title', ''));
			$collection->set('object_id', User::get('id'));
			$collection->set('object_type', 'member');
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
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
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
		if (!isset($collection))
		{
			$collection = new \Components\Collections\Models\Collection($collection_id);
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'created',
				'scope'       => 'collections.post',
				'scope_id'    => $post->id,
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_POST_CREATED', '<a href="' . Route::url($collection->link()) . '">' . htmlspecialchars((string) ($collection->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'collection_id' => $post->collection_id,
					'item_id'       => $post->item_id,
					'post_id'       => $post->id
				)
			],
			'recipients' => [
				['collection', $collection_id],
				['user', $post->get('created_by')]
			]
		]);

		// Display updated item stats if called via AJAX
		if ($no_html)
		{
			echo Lang::txt('%s reposts', $post->getCount(array('item_id' => $post->get('item_id'), 'original' => 0)));
			exit;
		}

		// Display the main listing
		App::redirect(Route::url($this->member->link() . '&active=' . $this->_name));
	}

	/**
	 * Remove a post
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
			$this->setError(Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_collections();
		}

		// Incoming
		$post = \Components\Collections\Models\Post::getInstance(Request::getInt('post', 0));

		// The post's own creator, or whoever owns the board it sits on. Keying
		// this on the creator alone locked the board owner out of their own
		// board: the view offers Remove on every post there -- _authorize()
		// grants every access-* on your own profile -- and com_collections'
		// collectTask() let anyone drop a post onto it, so the owner was left
		// with content they could see, were offered a control for, and could
		// not remove.
		if ($post->get('id')
		 && $post->get('created_by') != User::get('id')
		 && !$this->model->collection($post->get('collection_id'))->canBeModeratedBy())
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		$collection = $this->model->collection($post->get('collection_id'));

		$msg  = Lang::txt('Post removed.');
		$type = 'passed';
		if (!$post->remove())
		{
			$msg  = $post->getError();
			$type = 'error';
		}

		$route = Route::url($this->member->link() . '&active=' . $this->_name . '&task=' . $collection->get('alias'));

		// Record the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.post',
				'scope_id'    => $post->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_POST_DELETED', '<a href="' . $route . '">' . htmlspecialchars((string) ($collection->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'collection_id' => $post->get('collection_id'),
					'item_id'       => $post->get('item_id'),
					'post_id'       => $post->get('id')
				)
			],
			'recipients' => [
				['collection', $collection->get('id')],
				['user', $post->get('created_by')]
			]
		]);

		if (Request::getInt('no_html', 0))
		{
			echo $route;
			exit;
		}

		App::redirect($route, $msg, $type);
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

		// Authorization check
		if (!$this->params->get('access-edit-item'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_collections();
		}

		// Incoming
		$post = \Components\Collections\Models\Post::getInstance(Request::getInt('post', 0));

		// The post's own creator, or whoever owns the board it sits on. Keying
		// this on the creator alone locked the board owner out of their own
		// board: the view offers Remove on every post there -- _authorize()
		// grants every access-* on your own profile -- and com_collections'
		// collectTask() let anyone drop a post onto it, so the owner was left
		// with content they could see, were offered a control for, and could
		// not remove.
		if ($post->get('id')
		 && $post->get('created_by') != User::get('id')
		 && !$this->model->collection($post->get('collection_id'))->canBeModeratedBy())
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		// The destination board arrives from the request like the source did, so
		// it has to be one this member can post to -- otherwise a post can be
		// moved onto any board on the hub.
		$__dest = Request::getInt('board', 0);

		if ($__dest && !$this->mayPostToCollection($__dest))
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		if (!$post->move($__dest))
		{
			$this->setError($post->getError());
		}

		$route = Route::url($this->member->link() . '&active=' . $this->_name);

		if (Request::getInt('no_html', 0))
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
		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-delete-item'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_collections();
		}

		// Incoming
		$no_html = Request::getInt('no_html', 0);

		$post = \Components\Collections\Models\Post::getInstance(Request::getInt('post', 0));

		if (!$post->get('id'))
		{
			return $this->_collections();
		}

		// Two different actions share this route. Marking the ITEM deleted removes
		// it from the board it was posted to and from every repost of it, so only
		// the item's owner may do that: owning the post proves nothing, because
		// _repost() will make one carrying an item you do not own.
		//
		// The board's own owner moderating their board takes the POST off
		// instead, which leaves the item alone. That case belongs here because
		// the view renders Delete (not Remove) for an original post and
		// Post::remove() refuses originals.
		$__item     = $post->item();
		$__ownsItem = ($__item->get('created_by') == User::get('id'));
		$__board    = $this->model->collection($post->get('collection_id'));

		if (!$__ownsItem && !$__board->canBeModeratedBy())
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		$process = Request::getString('process', '');
		$confirmdel = Request::getString('confirmdel', '');

		$collection = $this->model->collection($post->get('collection_id'));

		// Did they confirm delete?
		if (!$process || !$confirmdel)
		{
			if ($process && !$confirmdel)
			{
				$this->setError(Lang::txt('PLG_GROUPS_' . strtoupper($this->_name) . '_ERROR_CONFIRM_DELETION'));
				if ($no_html)
				{
					echo '';
					exit;
				}
			}

			// Output HTML
			$view = $this->view('delete', 'post');
			$view->option   = $this->option;
			$view->member   = $this->member;
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

		$msg = Lang::txt('Post deleted.');
		$type = 'passed';

		$item = $post->item();

		if ($__ownsItem)
		{
			// Mark the entry as deleted
			$item->set('state', 2);
			if (!$item->store())
			{
				$msg = $item->getError();
				$type = 'error';
			}
		}
		else if (!$post->delete())
		{
			// Moderation: drop the post, leave someone else's item alone
			$msg  = $post->getError();
			$type = 'error';
		}

		// Redirect to collection
		$route = Route::url($this->member->link() . '&active=' . $this->_name . '&task=' . $collection->get('alias'));

		// Record the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.item',
				'scope_id'    => $item->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_ITEM_DELETED', '<a href="' . $route . '">' . htmlspecialchars((string) ($item->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'collection_id' => $post->get('collection_id'),
					'post_id'       => $post->get('id'),
					'item_id'       => $item->get('id'),
					'url'           => $route
				)
			],
			'recipients' => [
				['collection', $post->get('collection_id')],
				['user', $item->get('created_by')]
			]
		]);

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
		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Incoming
		// Check for request forgeries
		Request::checkToken();

		$comment = Request::getArray('comment', array(), 'post');

		// Never allow the author to be set from the request
		unset($comment['created_by']);

		// The post being commented on. comment[item_id] and comment[item_type]
		// are form fields, so what the comment hangs off is taken from a post
		// resolved here instead of from the request body.
		$post = new \Components\Collections\Models\Post(Request::getInt('post', 0));

		if (!$post->get('id') || !$post->get('item_id'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_post();
		}

		if (!empty($comment['id']))
		{
			$existing = \Hubzero\Item\Comment::oneOrFail((int) $comment['id']);

			// Author only, plus a genuine site-wide manager. The profile-owner
			// branch that used to be here was keyed on the comment sharing an
			// item with this post -- and collection comments hang off the ITEM,
			// shared across every repost of it, so any LEGAL repost of a visible
			// item satisfied it. No view renders a comment edit or delete
			// control, so narrowing to the author refuses nothing the page
			// offers, and it matches com_collections' own controller.
			if (User::get('id') != $existing->get('created_by')
			 && !User::authorise('core.manage', 'com_collections'))
			{
				$this->setError(Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
				return $this->_post();
			}
		}

		// Instantiate a new comment object and pass it the data. Only the
		// form's own fields are bound: every other comment[] key is a column
		// (state, access, positive, negative, ...) the poster must not set.
		$row = \Hubzero\Item\Comment::blank()->set(array_intersect_key($comment, array_flip(array('id', 'content', 'parent', 'anonymous'))));

		// Pin what the comment hangs off, on both paths
		$row->set('item_type', 'collection');
		$row->set('item_id', (int) $post->get('item_id'));
		if (empty($comment['id']))
		{
			$row->set('state', \Hubzero\Item\Comment::STATE_PUBLISHED);
		}

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->_post();
		}

		// Log activity ($post is resolved above)

		$recipients = array(
			['collection', $post->get('collection_id')],
			['user', $row->get('created_by')]
		);
		if ($row->get('parent'))
		{
			$recipients[] = ['user', $row->parent()->get('created_by')];
		}

		$title = $post->item()->get('title');
		$title = ($title ? $title : $post->item()->get('description', '#' . $post->get('id')));
		$title = \Hubzero\Utility\Str::truncate(strip_tags($title), 70);
		$url = Route::url('index.php?option=com_collections&controller=posts&post=' . $post->get('id') . '&task=comment');

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => (!empty($comment['id']) ? 'updated' : 'created'),
				'scope'       => 'collections.comment',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_COMMENT_' . (!empty($comment['id']) ? 'UPDATED' : 'CREATED'), $row->get('id'), '<a href="' . $url . '#c' . $row->get('id') . '">' . htmlspecialchars((string) ($title), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'collection_id' => $post->get('collection_id'),
					'post_id'       => $post->get('id'),
					'item_id'       => $row->get('item_id'),
					'url'           => $url . '#c' . $row->get('id')
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

		// The post the comment is claimed to be on. The route that reaches this
		// method is post/<id>/deletecomment, so the post id is in the request.
		$post = new \Components\Collections\Models\Post(Request::getInt('post', 0));

		// Initiate a whiteboard comment object
		$comment = \Hubzero\Item\Comment::oneOrFail($id);

		// Author only, plus a genuine site-wide manager. The profile-owner
		// branch that used to be here was keyed on the comment sharing an
		// item with this post -- and collection comments hang off the ITEM,
		// shared across every repost of it, so any LEGAL repost of a visible
		// item satisfied it. No view renders a comment edit or delete
		// control, so narrowing to the author refuses nothing the page
		// offers, and it matches com_collections' own controller.
		if (User::get('id') != $comment->get('created_by')
		 && !User::authorise('core.manage', 'com_collections'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_post();
		}

		$comment->set('state', $comment::STATE_DELETED);

		// Delete the entry itself
		if (!$comment->save())
		{
			$this->setError($comment->getError());
		}

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
			App::abort(404, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		// Apply the same visibility test _post() does, and for the same reason it
		// cannot be "is this board the member's": the Like control is rendered by
		// views/collection/tmpl/feed.php as well as collection/tmpl/default.php,
		// and every post in the feed sits on a board this member merely FOLLOWS.
		// Requiring the archive's own scope here would 403 every Like on the
		// default view. Resolve through the archive, fall back to an unscoped
		// lookup, then ask the readability predicate.
		//
		// What _vote() had was no check at all, which let a caller vote on a post
		// in a collection they are not allowed to see.
		$collection = $this->model->collection($post->get('collection_id'));

		if (!$collection->exists())
		{
			$collection = new \Components\Collections\Models\Collection($post->get('collection_id'));
		}

		if (!$collection->isReadableBy())
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTH'));
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
			echo Lang::txt('%s likes', $post->item()->get('positive'));
			exit;
		}

		// ($collection is resolved and access-checked above)

		$url = Route::url($this->member->link() . '&active=' . $this->_name . '&task=' . $collection->get('alias'));

		// Record the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'voted',
				'scope'       => 'collections.item',
				'scope_id'    => $post->item()->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_ITEM_VOTED', '<a href="' . $url . '">' . htmlspecialchars((string) ($collection->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'collection_id' => $collection->get('id'),
					'post_id'       => $post->get('id'),
					'item_id'       => $post->item()->get('id')
				)
			],
			'recipients' => [
				['collection', $collection->get('id')],
				['user', $post->item()->get('created_by')],
				['user', User::get('id')]
			]
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
		$collection = Route::url($this->member->link() . '&active=' . $this->_name);

		// Login check
		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_login?return=' . base64_encode($collection)),
				Lang::txt('MEMBERS_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}
		$restrictUsers = \Component::params('com_answers')->get('restrict_users');
		if ($restrictUsers == 'active')
		{
			$restrictDays = \Component::params('com_answers')->get('restrict_days');
			$now = new \DateTime();
			$registered = new \DateTime(User::get('registerDate'));
			if ($now->diff($registered)->days < $restrictDays)
			{
				App::redirect(
					Route::url('index.php?option=com_members'),
					Lang::txt('PLG_MEMBERS_COLLECTIONS_ERROR_NEW_ACCOUNT'),
					'warning'
				);
				return;
			}
		}

		// Access check
		if (!$this->params->get('access-create-collection') && !$this->params->get('access-edit-collection'))
		{
			App::redirect(
				$collection,
				Lang::txt('You are not authorized to edit this collection.'),
				'error'
			);
			return;
		}

		$view = $this->view('edit', 'collection');
		$view->name    = $this->_name;
		$view->option  = $this->option;
		$view->member  = $this->member;
		$view->task    = $this->action;
		$view->params  = $this->params;
		$view->no_html = Request::getInt('no_html', 0);

		if (is_object($row))
		{
			$view->entry = $row;
		}
		else
		{
			$view->entry = $this->model->collection(Request::getString('board', ''));
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

		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-edit-collection') || !$this->params->get('access-create-collection'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
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
			 || $__stored->get('object_type') != 'member'
			 || (int) $__stored->get('object_id') !== (int) $this->member->get('id'))
			{
				App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			}
		}

		// ...and where it belongs is never the submitter's to set
		$fields['object_type'] = 'member';
		$fields['object_id']   = (int) $this->member->get('id');

		// Bind new content
		$collection = new \Components\Collections\Models\Collection();

		if (!$collection->bind($fields))
		{
			$this->setError($collection->getError());
			return $this->_editcollection($collection);
		}

		if ($collection->get('access') != 0 && $collection->get('access') != 1 && $collection->get('access') != 4)
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

		$url = Route::url($this->member->link() . '&active=' . $this->_name . '&task=all');

		// Record the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'collections.collection',
				'scope_id'    => $collection->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_COLLECTION_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . $url . '">' . htmlspecialchars((string) ($collection->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'title' => $collection->get('title'),
					'id'    => $collection->get('id'),
					'url'   => $url
				)
			],
			'recipients' => [
				['collection', $collection->get('id')],
				['user', $collection->get('created_by')]
			]
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
			$this->setError(Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
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
		 || $collection->get('object_type') != 'member'
		 || (int) $collection->get('object_id') !== (int) $this->member->get('id'))
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
		}

		// Did they confirm delete?
		if (!$process || !$confirmdel)
		{
			if ($process && !$confirmdel)
			{
				$this->setError(Lang::txt('PLG_GROUPS' . strtoupper($this->_name) . 'ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			$view = $this->view('delete', 'collection')
				->set('name', $this->_name)
				->set('option', $this->option)
				->set('member', $this->member)
				->set('params', $this->params)
				->set('collection', $collection)
				->set('no_html', $no_html)
				->setErrors($this->getErrors());

			$view->task       = $this->action;

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
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.collection',
				'scope_id'    => $collection->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_COLLECTION_DELETED', '<a href="' . Route::url($collection->link()) . '">' . htmlspecialchars((string) ($collection->get('title')), ENT_QUOTES, 'UTF-8') . '</a>'),
				'details'     => array(
					'title' => $collection->get('title'),
					'id'    => $collection->get('id'),
					'url'   => $collection->link()
				)
			],
			'recipients' => [
				['collection', $collection->get('id')],
				['user', $collection->get('created_by')]
			]
		]);

		// Redirect to main view
		$route = Route::url($this->member->link() . '&active=' . $this->_name . '&task=all');

		if ($no_html)
		{
			echo $route;
			exit;
		}

		App::redirect($route);
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
		$this->params->set('access-view-' . $assetType, true);
		if (!User::isGuest())
		{
			// Can NOT create, delete, or edit by default
			$this->params->set('access-manage-' . $assetType, false);
			$this->params->set('access-create-' . $assetType, false);
			$this->params->set('access-delete-' . $assetType, false);
			$this->params->set('access-edit-' . $assetType, false);

			if (User::get('id') == $this->member->get('id'))
			{
				$this->params->set('access-manage-' . $assetType, true);
				$this->params->set('access-create-' . $assetType, true);
				$this->params->set('access-delete-' . $assetType, true);
				$this->params->set('access-edit-' . $assetType, true);
				$this->params->set('access-view-' . $assetType, true);
			}
		}
	}

	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * This method marks blog posts as "trashed" if an account has
	 * been disabled and/or marked as spam.
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was succesfully stored in the database.
	 * @param   string   $msg      Message.
	 * @return  void
	 */
	public function onMemberAfterSave($user, $isnew, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		// New user = shouldn't be anything to do here
		if ($isnew)
		{
			return true;
		}

		// If the user was blocked and account not approved
		// OR email address starts with SPAM_
		if (($user['block'] && !$user['approved'])
		 || substr($user['email'], 0, strlen('SPAM_')) == 'SPAM_')
		{
			try
			{
				// Mark all content as trashed
				include_once \Component::path('com_collections') . DS . 'models' . DS . 'archive.php';

				$db = App::get('db');

				$model = new \Components\Collections\Tables\Collection($db);

				$entries = $model->find('list', array(
					'created_by' => $user['id']
				));

				foreach ($entries as $entry)
				{
					$entry = new \Components\Collections\Models\Collection($entry);
					$entry->set('state', 2);

					if (!$entry->store(false))
					{
						throw new Exception($entry->getError());
					}
				}
			}
			catch (Exception $e)
			{
				return false;
			}
		}
	}

	/**
	 * Remove all user blog entries for the given user ID
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     Holds the user data
	 * @param   boolean  $success  True if user was succesfully stored in the database
	 * @param   string   $msg      Message
	 * @return  boolean
	 */
	public function onMemberAfterDelete($user, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		$userId = \Hubzero\Utility\Arr::getValue($user, 'id', 0, 'int');

		if ($userId)
		{
			try
			{
				include_once \Component::path('com_collections') . DS . 'models' . DS . 'archive.php';

				$db = App::get('db');

				$model = new \Components\Collections\Tables\Collection($db);

				$entries = $model->find('list', array(
					'created_by' => $userId
				));

				foreach ($entries as $entry)
				{
					$entry = new \Components\Collections\Models\Collection($entry);

					if (!$entry->delete())
					{
						throw new Exception($entry->getError());
					}
				}
			}
			catch (Exception $e)
			{
				return false;
			}
		}

		return true;
	}
}
