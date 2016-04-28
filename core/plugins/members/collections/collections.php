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
			'icon' => 'f005'
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
			'metadata' => ''
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

		include_once(PATH_CORE . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'archive.php');
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
			$this->action = Request::getVar('action', $default);

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
				case 'savecomment':   $arr['html'] = $this->_savecomment();   break;
				case 'newcomment':    $arr['html'] = $this->_newcomment();    break;
				case 'editcomment':   $arr['html'] = $this->_editcomment();   break;
				case 'deletecomment': $arr['html'] = $this->_deletecomment(); break;

				case 'followers': $arr['html'] = $this->_followers(); break;
				case 'following': $arr['html'] = $this->_following(); break;
				case 'follow':    $arr['html'] = $this->_follow('member');    break;
				case 'unfollow':  $arr['html'] = $this->_unfollow('member');  break;

				// Entries
				case 'savepost':   $arr['html'] = $this->_save();   break;
				case 'newpost':    $arr['html'] = $this->_new();    break;
				case 'editpost':   $arr['html'] = $this->_edit();   break;
				case 'deletepost': $arr['html'] = $this->_delete(); break;
				case 'posts':      $arr['html'] = $this->_posts();  break;

				case 'comment':
				case 'post':    $arr['html'] = $this->_post();   break;
				case 'vote':    $arr['html'] = $this->_vote();   break;
				case 'collect': $arr['html'] = $this->_repost(); break;
				case 'remove':  $arr['html'] = $this->_remove(); break;
				case 'move':    $arr['html'] = $this->_move();   break;

				case 'followcollection': $arr['html'] = $this->_follow('collection'); break;
				case 'unfollowcollection': $arr['html'] = $this->_unfollow('collection'); break;
				case 'collectcollection':  $arr['html'] = $this->_repost();           break;
				case 'newcollection':      $arr['html'] = $this->_newcollection();    break;
				case 'editcollection':     $arr['html'] = $this->_editcollection();   break;
				case 'savecollection':     $arr['html'] = $this->_savecollection();   break;
				case 'deletecollection':   $arr['html'] = $this->_deletecollection(); break;

				case 'all':
				case 'collections':      $arr['html'] = $this->_collections();      break;

				case 'collection': $arr['html'] = $this->_collection(); break;

				case 'feed': $arr['html'] = $this->_feed(); break;
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
			'search'  => Request::getVar('search', ''),
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
			'search'        => Request::getVar('search', ''),
			'state'         => 1,
			'collection_id' => Request::getVar('board', ''),
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
				$collection = $this->model->collection(Request::getVar('board', ''));
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
				$collection = $this->model->collection(Request::getVar('board', ''));
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
		$view->filters['search']      = Request::getVar('search', '');
		$view->filters['state']       = 1;
		$view->filters['collection_id'] = Request::getVar('board', '');

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
			'search'      => Request::getVar('search', ''),
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

		$collection = $this->model->collection($post->get('collection_id'));

		if ($collection->get('access') == 4 // private collection
		 && User::get('id') != $this->member->get('id')) // is user the collection owner?
		{
			$this->params->set('access-view-item', false);
		}

		// Check authorization
		if (!$this->params->get('access-view-item'))
		{
			App::abort(403, Lang::txt('PLG_MEMBERS' . strtoupper($this->_name) . 'NOT_AUTH'));
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
			$view =  $this->view('edit', 'post');
		}
		$view->name       = $this->_name;
		$view->option     = $this->option;
		$view->member     = $this->member;
		$view->task       = $this->action;
		$view->params     = $this->params;
		$view->no_html     = $no_html;

		$id = Request::getInt('post', 0);

		$view->collection = $this->model->collection(Request::getVar('board', 0));

		$view->collections = $this->model->collections();
		if (!$view->collections->total())
		{
			$view->collection->setup($this->member->get('id'), 'member');
			$view->collections = $this->model->collections();
			$view->collection  = $this->model->collection(Request::getVar('board', 0));
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

		$coltitle = Request::getVar('collection_title', '', 'post');
		if (!$p['collection_id'] && $coltitle)
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
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_POST_' . ($p['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($url) . '">' . $collection->get('title') . '</a>'),
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
		if (!$collection_id)
		{
			$collection = new \Components\Collections\Models\Collection();
			$collection->set('title', Request::getVar('collection_title', ''));
			$collection->set('object_id', User::get('id'));
			$collection->set('object_type', 'member');
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
		if (!isset($collection))
		{
			$collection = new \Components\Collections\Models\Collection($collection_id);
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'created',
				'scope'       => 'collections.post',
				'scope_id'    => $post->id,
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_POST_CREATED', '<a href="' . Route::url($collection->link()) . '">' . $collection->get('title') . '</a>'),
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
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_POST_DELETED', '<a href="' . $route . '">' . $collection->get('title') . '</a>'),
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

		if (!$post->move(Request::getInt('board', 0)))
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

		$process = Request::getVar('process', '');
		$confirmdel = Request::getVar('confirmdel', '');

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

		// Mark the entry as deleted
		$item = $post->item();
		$item->set('state', 2);
		if (!$item->store())
		{
			$msg = $item->getError();
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
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_ITEM_DELETED', '<a href="' . $route . '">' . $item->get('title') . '</a>'),
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
		$comment = Request::getVar('comment', array(), 'post');

		// Instantiate a new comment object and pass it the data
		$row = \Hubzero\Item\Comment::blank()->set($comment);

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->_post();
		}

		// Log activity
		$post = new \Components\Collections\Models\Post(Request::getInt('post', 0));

		$recipients = array(
			['collection', $post->get('collection_id')],
			['user', $comment->get('created_by')]
		);
		if ($comment->get('parent'))
		{
			$recipients[] = ['user', $comment->parent()->get('created_by')];
		}

		$title = $post->item()->get('title');
		$title = ($title ? $title : $post->item()->get('description', '#' . $post->get('id')));
		$title = \Hubzero\Utility\String::truncate(strip_tags($title), 70);
		$url = Route::url('index.php?option=com_collections&controller=posts&post=' . $post->get('id') . '&task=comment');

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($data['id'] ? 'updated' : 'created'),
				'scope'       => 'collections.comment',
				'scope_id'    => $comment->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_COMMENT_' . ($data['id'] ? 'UPDATED' : 'CREATED'), $comment->get('id'), '<a href="' . $url . '#c' . $comment->get('id') . '">' . $title . '</a>'),
				'details'     => array(
					'collection_id' => $post->get('collection_id'),
					'post_id'       => $post->get('id'),
					'item_id'       => $row->get('item_id'),
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

		// Initiate a whiteboard comment object
		$comment = \Hubzero\Item\Comment::oneOrFail($id);
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
			echo Lang::txt('%s likes', $post->item()->get('positive'));
			exit;
		}

		// Get the collection model
		$collection = $this->model->collection($post->get('collection_id'));

		$url = Route::url($this->member->link() . '&active=' . $this->_name . '&task=' . $collection->get('alias'));

		// Record the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'voted',
				'scope'       => 'collections.item',
				'scope_id'    => $post->item()->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_ITEM_VOTED', '<a href="' . $url . '">' . $collection->get('title') . '</a>'),
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
				Route::url('index.php?option=com_users&view=login?return=' . base64_encode($collection)),
				Lang::txt('MEMBERS_LOGIN_NOTICE'),
				'warning'
			);
			return;
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
			$view->entry = $this->model->collection(Request::getVar('board', ''));
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
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields['id'] = intval($fields['id']);

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

		$url = Route::url($this->member->link() . '&active=' . $this->_name . '&task=all');

		// Record the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'collections.collection',
				'scope_id'    => $collection->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_COLLECTION_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . $url . '">' . $collection->get('title') . '</a>'),
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
				$this->setError(Lang::txt('PLG_GROUPS' . strtoupper($this->_name) . 'ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			$view->task       = $this->action;

			$view = $this->view('delete', 'collection')
				->set('name', $this->_name)
				->set('option', $this->option)
				->set('member', $this->member)
				->set('params', $this->params)
				->set('collection', $collection)
				->set('no_html', $this->no_html)
				->setErrors($this->getErrors());

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
				'description' => Lang::txt('PLG_MEMBERS_COLLECTIONS_ACTIVITY_COLLECTION_DELETED', '<a href="' . Route::url($collection->link()) . '">' . $collection->get('title') . '</a>'),
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
				include_once(PATH_CORE . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'archive.php');

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
				include_once(PATH_CORE . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'archive.php');

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
