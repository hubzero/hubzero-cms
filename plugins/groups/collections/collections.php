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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Groups Plugin class for assets
 */
class plgGroupsCollections extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Custom params
	 *
	 * @var    object
	 */
	protected $_params = null;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => JText::_('PLG_GROUPS_' . strtoupper($this->_name)),
			'default_access'   => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f005'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param      object  $group      Current group
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
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
		$this->juser    = JFactory::getUser();
		$this->database = JFactory::getDBO();

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'archive.php');

		$this->model = new CollectionsModelArchive('group', $this->group->get('gidNumber'));

		//get the plugins params
		//$p = new \Hubzero\Plugin\Params($this->database);
		//$this->params = $p->getParams($group->gidNumber, 'groups', $this->_name);
		$this->members = $group->get('members');
		$this->authorized = $authorized;

		$this->_authorize('collection');
		$this->_authorize('item');

		//are we returning html
		if ($return == 'html')
		{
			// This needs to be called to ensure scripts are pushed to the document
			$foo = \JFactory::getEditor()->display('description', '', '', '', 35, 5, false, 'field_description', null, null, array('class' => 'minimal no-footer'));

			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//Create user object
			//$juser = JFactory::getUser();

			//get the group members
			$members = $group->get('members');

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody')
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if ($this->juser->get('guest')
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$url = JRoute::_('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active, false, true);
				$this->redirect(
					JRoute::_('index.php?option=com_users&view=login?return=' . base64_encode($url)),
					JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array($this->juser->get('id'), $members)
			 && $group_plugin_acl == 'members'
			 && $authorized != 'admin')
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
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
			\Hubzero\Document\Assets::addPluginStylesheet('groups', $this->_name);

			$task = '';
			$controller = 'board';
			$id = 0;

			$juri = JURI::getInstance();
			$path = $juri->getPath();
			if (strstr($path, '/'))
			{
				$path = str_replace($juri->base(true), '', $path);
				$path = str_replace('index.php', '', $path);
				$path = DS . trim($path, DS);
				$path = str_replace('/groups/' . $this->group->get('cn') . '/' . $this->_name, '', $path);
				$path = ltrim($path, DS);
				$bits = explode('/', $path);

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
									JRequest::setVar('post', $bits[1]);
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
								JRequest::setVar('unfollow', $bits[1]);
							}
						break;

						case 'settings':
						case 'savesettings':
							$this->action = $bits[0];
						break;

						default:
							$this->action = 'collection';
							JRequest::setVar('board', $bits[0]);

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
	 * @return     void
	 */
	private function _login()
	{
		$route = JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name, false, true);

		$this->redirect(
			JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($route)),
			JText::_('GROUPS_LOGIN_NOTICE'),
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
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'follow',
				'layout'  => 'followers'
			)
		);
		$view->name        = $this->_name;
		$view->juser       = $this->juser;
		$view->option      = $this->option;
		$view->group       = $this->group;
		$view->params      = $this->params;
		$view->model       = $this->model;

		$this->jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters = array(
			'limit' => JRequest::getInt('limit', $this->jconfig->getValue('config.list_limit')),
			'start' => JRequest::getInt('limitstart', 0)
		);

		$count = array(
			'count'  => true
		);

		if (!$this->params->get('access-manage-collection'))
		{
			$view->filters['access'] = ($this->juser->get('guest') ? 0 : array(0, 1));
			$count['access'] = $view->filters['access'];
		}

		$view->collections = $this->model->collections($count);

		$view->posts       = $this->model->posts($count);

		$view->following   = $this->model->following($count);

		$view->total = $this->model->followers($count);

		$view->rows  = $this->model->followers($view->filters);

		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->total,
			$view->filters['start'],
			$view->filters['limit']
		);

		$view->pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
		$view->pageNav->setAdditionalUrlParam('active', $this->_name);
		$view->pageNav->setAdditionalUrlParam('scope', 'followers');

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
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
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'collection',
				'layout'  => 'collections'
			)
		);
		$view->name        = $this->_name;
		$view->juser       = $this->juser;
		$view->option      = $this->option;
		$view->group       = $this->group;
		$view->params      = $this->params;
		$view->model       = $this->model;

		$this->jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters = array(
			'limit' => JRequest::getInt('limit', $this->jconfig->getValue('config.list_limit')),
			'start' => JRequest::getInt('limitstart', 0)
		);

		// Filters for returning results
		$filters = array(
			'user_id' => $this->juser->get('id'),
			'state'   => 1
		);

		$count = array(
			'count'  => true
		);

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

		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->total,
			$view->filters['start'],
			$view->filters['limit']
		);

		$view->pageNav->setAdditionalUrlParam('cn', $view->group->get('cn'));
		$view->pageNav->setAdditionalUrlParam('active', $this->_name);
		$view->pageNav->setAdditionalUrlParam('scope', 'all');

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
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
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'collection',
				'layout'  => 'default'
			)
		);
		$view->name    = $this->_name;
		$view->juser   = $this->juser;
		$view->option  = $this->option;
		$view->group   = $this->group;
		$view->params  = $this->params;
		$view->model   = $this->model;

		$this->jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters = array(
			'limit'         => JRequest::getInt('limit', $this->jconfig->getValue('config.list_limit')),
			'start'         => JRequest::getInt('limitstart', 0),
			'user_id'       => JFactory::getUser()->get('id'),
			'search'        => JRequest::getVar('search', ''),
			'state'         => 1,
			'collection_id' => JRequest::getVar('board', 0)
		);

		$view->collection = $this->model->collection($view->filters['collection_id']);
		if (!$view->collection->exists())
		{
			return $this->_collections();
		}

		$view->filters['collection_id'] = $view->collection->get('id');

		$count = array(
			'count' => true,
			'collection_id' => $view->collection->get('id')
		);

		if (!$this->params->get('access-manage-collection'))
		{
			$view->filters['access'] = ($this->juser->get('guest') ? 0 : array(0, 1));
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

		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->count,
			$view->filters['start'],
			$view->filters['limit']
		);

		$view->pageNav->setAdditionalUrlParam('cn', $view->group->get('cn'));
		$view->pageNav->setAdditionalUrlParam('active', $this->_name);
		$view->pageNav->setAdditionalUrlParam('scope', $view->collection->get('alias'));

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
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
		if ($this->juser->get('guest'))
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
				$collection = $this->model->collection(JRequest::getVar('board', ''));
				if (!$collection->exists())
				{
					JError::raiseError(400, JText::_('PLG_GROUPS_COLLECTIONS_ERROR_COLLECTION_DOES_NOT_EXIST'));
					return;
				}
				$id = $collection->get('id');
				$sfx = '&scope=' . $collection->get('alias') . '/unfollow';
			break;
		}

		if (!$this->model->follow($id, $what, $this->juser->get('id'), 'member'))
		{
			$this->setError($this->model->getError());
		}

		if (JRequest::getInt('no_html', 0))
		{
			$response = new stdClass;
			$response->href = JRoute::_('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=collections' . $sfx);
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
			return $this->_collection();
		}
	}

	/**
	 * Display a list of items in a collection
	 *
	 * @return     string
	 */
	private function _unfollow($what='collection')
	{
		// Is the board restricted to logged-in users only?
		if ($this->juser->get('guest'))
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
				$collection = $this->model->collection(JRequest::getVar('board', ''));
				if (!$collection->exists())
				{
					JError::raiseError(400, JText::_('PLG_GROUPS_COLLECTIONS_ERROR_COLLECTION_DOES_NOT_EXIST'));
					return;
				}
				$id = $collection->get('id');
				$sfx = '&scope=' . $collection->get('alias') . '/follow';
			break;
		}

		if (!$this->model->unfollow($id, $what, $this->juser->get('id'), 'member'))
		{
			$this->setError($this->model->getError());
		}

		if (JRequest::getInt('no_html', 0))
		{
			$response = new stdClass;
			$response->href = JRoute::_('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=collections' . $sfx);
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
			return $this->_collection();
		}
	}

	/**
	 * Display a list of posts for all collections
	 *
	 * @return     string
	 */
	private function _posts()
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'collection',
				'layout'  => 'default'
			)
		);
		$view->name    = $this->_name;
		$view->group   = $this->group;
		$view->option  = $this->option;
		$view->params  = $this->params;
		$view->model   = $this->model;

		$this->jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters = array(
			'limit'       => JRequest::getInt('limit', $this->jconfig->getValue('config.list_limit')),
			'start'       => JRequest::getInt('limitstart', 0),
			'search'      => JRequest::getVar('search', ''),
			'state'       => 1,
			'object_type' => 'group',
			'object_id'   => $this->group->get('gidNumber'),
			'user_id'     => JFactory::getUser()->get('id')
		);

		// Filters for returning results
		$count = array(
			'count' => true
		);

		if (!$this->params->get('access-manage-collection'))
		{
			$view->filters['access'] = ($this->juser->get('guest') ? 0 : array(0, 1));
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

		$view->collection = CollectionsModelCollection::getInstance();

		//$view->filters['user_id'] = JFactory::getUser()->get('id');

		$view->count = $view->posts;
		$view->rows = $view->collection->posts($view->filters);

		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->count,
			$view->filters['start'],
			$view->filters['limit']
		);

		$view->pageNav->setAdditionalUrlParam('cn', $view->group->get('cn'));
		$view->pageNav->setAdditionalUrlParam('active', $this->_name);
		$view->pageNav->setAdditionalUrlParam('scope', 'posts');

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		return $view->loadTemplate();
	}

	/**
	 * Display a post
	 *
	 * @return     string
	 */
	private function _post()
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'post'
			)
		);
		$view->option      = $this->option;
		$view->group       = $this->group;
		$view->params      = $this->params;
		$view->juser       = $this->juser;
		$view->name        = $this->_name;
		$view->model      = $this->model;

		$post_id = JRequest::getInt('post', 0);

		$view->post = CollectionsModelPost::getInstance($post_id);

		if (!$view->post->exists())
		{
			return $this->_collections();
		}

		$view->collection = $this->model->collection($view->post->get('collection_id'));

		// Check authorization
		if (!$this->params->get('access-view-item'))
		{
			JError::raiseError(403, JText::_('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return;
		}

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		$view->no_html = JRequest::getInt('no_html', 0);

		if ($view->no_html)
		{
			$view->display();
			exit;
		}
		else
		{
			return $view->loadTemplate();
		}
	}

	/**
	 * Display a form for creating an entry
	 *
	 * @return     string
	 */
	private function _new()
	{
		return $this->_edit();
	}

	/**
	 * Display a form for editing an entry
	 *
	 * @return     string
	 */
	private function _edit($entry=null)
	{
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		if (!$this->params->get('access-create-item') && !$this->params->get('access-edit-item'))
		{
			$this->redirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name),
				JText::_('PLG_GROUPS_COLLECTIONS_NOT_AUTH'),
				'error'
			);
			return;
		}

		$no_html = JRequest::getInt('no_html', 0);
		if ($no_html)
		{
			$type = strtolower(JRequest::getWord('type', 'file'));
			if (!in_array($type, array('file', 'image', 'text', 'link')))
			{
				$type = 'file';
			}

			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'post',
					'layout'  => 'edit_' . $type
				)
			);
		}
		else
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'post',
					'layout'  => 'edit'
				)
			);
		}
		$view->name        = $this->_name;
		$view->option      = $this->option;
		$view->group       = $this->group;
		$view->task        = $this->action;
		$view->params      = $this->params;
		$view->no_html     = $no_html;

		$id = JRequest::getInt('post', 0);

		$view->collection = $this->model->collection(JRequest::getVar('board', 0));

		$view->collections = $this->model->collections();
		if (!$view->collections->total())
		{
			$view->collection->setup($this->group->get('cn'), 'group');
			$view->collections = $this->model->collections();
			$view->collection = $this->model->collection(JRequest::getVar('board', 0));
		}

		$view->entry = $view->collection->post($id);
		if (!$view->collection->exists() && $view->entry->exists())
		{
			$view->collection = $this->model->collection($view->entry->get('collection_id'));
		}

		if ($remove = JRequest::getInt('remove', 0))
		{
			if (!$view->entry->item()->removeAsset($remove))
			{
				$view->setError($view->entry->item()->getError());
			}
		}

		if ($no_html)
		{
			$view->display();
			exit;
		}
		else
		{
			return $view->loadTemplate();
		}
	}

	/**
	 * Save an entry
	 *
	 * @return     void
	 */
	private function _save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Login check
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-edit-item') || !$this->params->get('access-create-item'))
		{
			$this->setError(JText::_('PLG_GROUPS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_collections();
		}

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post', 'none', 2);

		// Get model
		$row = new CollectionsModelItem($fields['id']);

		// Bind content
		if (!$row->bind($fields))
		{
			$this->setError($row->getError());
			return $this->_edit($row);
		}

		// Add some data
		if ($files  = JRequest::getVar('fls', '', 'files', 'array'))
		{
			$row->set('_files', $files);
		}
		$row->set('_assets', JRequest::getVar('assets', null, 'post'));
		$row->set('_tags', trim(JRequest::getVar('tags', '')));
		$row->set('state', 1);
		if (!$row->exists())
		{
			$row->set('access', 0);
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->_edit($row);
		}

		// Create a post entry linking the item to the board
		$p = JRequest::getVar('post', array(), 'post');

		$post = new CollectionsModelPost($p['id']);
		if (!$post->exists())
		{
			$post->set('item_id', $row->get('id'));
			$post->set('original', 1);
		}

		$coltitle = JRequest::getVar('collection_title', '', 'post');
		if (!$p['collection_id'] && $coltitle)
		{
			$collection = new CollectionsModelCollection();
			$collection->set('title', $coltitle);
			$collection->set('object_id', $this->group->get('gidNumber'));
			$collection->set('object_type', 'group');
			$collection->set('access', $this->params->get('access-plugin'));
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
			return $this->_edit($row);
		}

		$this->redirect(
			JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $this->model->collection($p['collection_id'])->get('alias'))
		);
	}

	/**
	 * Repost an entry
	 *
	 * @return     string
	 */
	private function _repost()
	{
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		if (!$this->params->get('access-create-item'))
		{
			$this->setError(JText::_('PLG_GROUPS_COLLECTIONS_NOT_AUTHORIZED'));
			return $this->_collections();
		}

		$no_html = JRequest::getInt('no_html', 0);

		// No board ID selected so present repost form
		$repost = JRequest::getInt('repost', 0);
		if (!$repost)
		{
			// Incoming
			$post_id       = JRequest::getInt('post', 0);
			$collection_id = JRequest::getVar('board', 0);

			if (!$post_id && $collection_id)
			{
				$collection = $this->model->collection($collection_id);

				$item_id       = $collection->item()->get('id');
				$collection_id = $collection->item()->get('object_id');
			}
			else
			{
				$post = CollectionsModelPost::getInstance($post_id);

				$item_id = $post->get('item_id');
			}

			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'post',
					'layout'  => 'repost'
				)
			);

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
			else
			{
				return $view->loadTemplate();
			}
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$collection_id = JRequest::getInt('collection_id', 0);
		if (!$collection_id)
		{
			$collection = new CollectionsModelCollection();
			$collection->set('title', JRequest::getVar('collection_title', ''));
			$collection->set('object_id', $this->group->get('gidNumber'));
			$collection->set('object_type', 'group');
			$collection->set('access', $this->params->get('access-plugin'));
			if (!$collection->store())
			{
				$this->setError($collection->getError());
			}
			$collection_id = $collection->get('id');
		}
		$item_id       = JRequest::getInt('item_id', 0);

		// Try loading the current board/bulletin to see
		// if this has already been posted to the board (i.e., no duplicates)
		$post = new CollectionsTablePost($this->database);
		$post->loadByBoard($collection_id, $item_id);
		if (!$post->get('id'))
		{
			// No record found -- we're OK to add one
			$post->item_id       = $item_id;
			$post->collection_id = $collection_id;
			$post->description   = JRequest::getVar('description', '', 'none', 2);
			if ($post->check())
			{
				$this->setError($post->getError());
			}
			// Store new content
			if (!$post->store())
			{
				$this->setError($post->getError());
			}
		}
		if ($this->getError())
		{
			return $this->getError();
		}

		// Display updated bulletin stats if called via AJAX
		if ($no_html)
		{
			echo JText::sprintf('PLG_GROUPS_COLLECTIONS_POST_REPOSTS', $post->getCount(array('item_id' => $post->get('item_id'), 'original' => 0)));
			exit;
		}

		// Display the main listing
		return $this->_collection();
	}

	/**
	 * Repost an entry
	 *
	 * @return     string
	 */
	private function _remove()
	{
		// Login check
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-create-item'))
		{
			$this->setError(JText::_('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// Incoming
		$post = CollectionsModelPost::getInstance(JRequest::getInt('post', 0));

		$collection = $this->model->collection($post->get('collection_id'));

		$msg = JText::_('Post removed.');
		$type = 'passed';
		if (!$post->remove())
		{
			$msg = $post->getError();
			$type = 'error';
		}

		$route = JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias'));

		if (($no_html = JRequest::getInt('no_html', 0)))
		{
			echo $route;
			exit;
		}

		$this->redirect(
			$route,
			$msg,
			$type
		);
	}

	/**
	 * Move a post to another collection
	 *
	 * @return     void
	 */
	private function _move()
	{
		// Login check
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-create-item'))
		{
			$this->setError(JText::_('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// Incoming
		$post = CollectionsModelPost::getInstance(JRequest::getInt('post', 0));

		if (!$post->move(JRequest::getInt('board', 0)))
		{
			$this->setError($post->getError());
		}

		$route = JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name);

		if (($no_html = JRequest::getInt('no_html', 0)))
		{
			echo $route;
			exit;
		}

		$this->redirect($route);
	}

	/**
	 * Delete an entry
	 *
	 * @return     string
	 */
	private function _delete()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit('Invalid Token');

		// Login check
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-delete-item'))
		{
			$this->setError(JText::_('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// Incoming
		$no_html = JRequest::getInt('no_html', 0);

		$post = CollectionsModelPost::getInstance(JRequest::getInt('post', 0));
		if (!$post->get('id'))
		{
			return $this->_collections();
		}

		$process = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');

		$collection = $this->model->collection($post->get('collection_id'));

		// Did they confirm delete?
		if (!$process || !$confirmdel || !JRequest::checkToken())
		{
			if ($process && !$confirmdel)
			{
				$this->setError(JText::_('PLG_GROUPS_COLLECTIONS_ERROR_CONFIRM_DELETION'));
				if ($no_html)
				{
					echo '';
					exit;
				}
			}

			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'post',
					'layout'  => 'delete'
				)
			);
			$view->option   = $this->option;
			$view->group    = $this->group;
			$view->task     = $this->action;
			$view->params   = $this->params;
			$view->post     = $post;
			$view->no_html  = $no_html;
			$view->name     = $this->_name;
			$view->collection = $collection;

			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}
			return $view->loadTemplate();
		}

		$msg = JText::_('PLG_GROUPS_COLLECTIONS_POST_DELETED');
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
		$route = JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias'));

		if ($no_html)
		{
			echo $route;
			exit;
		}

		$this->redirect($route, $msg, $type);
	}

	/**
	 * Save a comment
	 *
	 * @return     string
	 */
	private function _savecomment()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Ensure the user is logged in
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		// Incoming
		$comment = JRequest::getVar('comment', array(), 'post');

		// Instantiate a new comment object and pass it the data
		$row = new \Hubzero\Item\Comment($this->database);
		if (!$row->bind($comment))
		{
			$this->setError($row->getError());
			return $this->_post();
		}

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			return $this->_post();
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->_post();
		}

		return $this->_post();
	}

	/**
	 * Delete a comment
	 *
	 * @return     string
	 */
	private function _deletecomment()
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		// Incoming
		$id = JRequest::getInt('comment', 0);
		if (!$id)
		{
			return $this->_post();
		}

		// Initiate a whiteboard comment object
		$comment = new \Hubzero\Item\Comment($this->database);
		$comment->load($id);
		$comment->state = 2;

		// Delete the entry itself
		if (!$comment->store())
		{
			$this->setError($comment->getError());
		}

		// Return the topics list
		return $this->_post();
	}

	/**
	 * Vote for an item
	 *
	 * @return     void
	 */
	private function _vote()
	{
		// Incoming
		$id = JRequest::getInt('post', 0);

		// Get the post model
		$post = CollectionsModelPost::getInstance($id);

		// Record the vote
		if (!$post->item()->vote())
		{
			$this->setError($post->item()->getError());
		}

		// Display updated item stats if called via AJAX
		$no_html = JRequest::getInt('no_html', 0);
		if ($no_html)
		{
			echo JText::sprintf('PLG_GROUPS_COLLECTIONS_POST_LIKES', $post->item()->get('positive'));
			exit;
		}

		// Get the collection model
		$collection = $this->model->collection($post->get('collection_id'));

		// Display the main listing
		$this->redirect(
			JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $collection->get('alias'))
		);
	}

	/**
	 * Display a form for creating a collection
	 *
	 * @return     string
	 */
	private function _newcollection()
	{
		return $this->_editcollection();
	}

	/**
	 * Display a form for editing a collection
	 *
	 * @return     string
	 */
	private function _editcollection($row=null)
	{
		// Login check
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-create-collection') && !$this->params->get('access-edit-collection'))
		{
			$this->redirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name),
				JText::_('PLG_GROUPS_COLLECTIONS_NOT_AUTH'),
				'error'
			);
			return;
		}

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'collection',
				'layout'  => 'edit'
			)
		);

		$view->name       = $this->_name;
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->task       = $this->action;
		$view->params     = $this->params;
		$view->no_html = JRequest::getInt('no_html', 0);

		if (is_object($row))
		{
			$view->entry = $row;
		}
		else
		{
			$view->entry = $this->model->collection(JRequest::getVar('board', ''));
		}
		if (!$view->entry->exists())
		{
			$view->entry->set('access', $this->params->get('access-plugin'));
		}

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
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
	 * @return     string
	 */
	private function _savecollection()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		if (!$this->params->get('access-edit-collection') || !$this->params->get('access-create-collection'))
		{
			$this->setError(JText::_('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post', 'none', 2);

		// Bind new content
		$row = new CollectionsModelCollection();
		if (!$row->bind($fields))
		{
			$this->setError($row->getError());
			return $this->_editcollection($row);
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->_editcollection($row);
		}

		// Redirect to collection
		$this->redirect(
			JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&scope=' . $row->get('alias'))
		);
	}

	/**
	 * Delete a collection
	 *
	 * @return     string
	 */
	private function _deletecollection()
	{
		// Login check
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-delete-collection'))
		{
			$this->setError(JText::_('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_collections();
		}

		// Incoming
		$no_html = JRequest::getInt('no_html', 0);
		$id = JRequest::getVar('board', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			return $this->_collections();
		}

		$process = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');

		// Get the collection model
		$collection = $this->model->collection($id);

		// Did they confirm delete?
		if (!$process || !$confirmdel)
		{
			if ($process && !$confirmdel)
			{
				$this->setError(JText::_('PLG_GROUPS_COLLECTIONS_ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'collection',
					'layout'  => 'delete'
				)
			);
			$view->option     = $this->option;
			$view->group      = $this->group;
			$view->task       = $this->action;
			$view->params     = $this->params;
			$view->collection = $collection;
			$view->no_html    = $no_html;
			$view->name       = $this->_name;

			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}
			return $view->loadTemplate();
		}

		// Mark the entry as deleted
		$collection->set('state', 2);
		if (!$collection->store())
		{
			$this->setError($collection->getError());
		}

		// Redirect to main view
		$route = JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name);

		if ($no_html)
		{
			echo $route;
			exit;
		}

		$this->redirect($route);
	}

	/**
	 * Display settings
	 *
	 * @return     string
	 */
	private function _settings()
	{
		// Login check
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		if ($this->authorized != 'manager' && $this->authorized != 'admin')
		{
			$this->setError(JText::_('PLG_GROUPS_COLLECTIONS_NOT_AUTH'));
			return $this->_browse();
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'settings'
			)
		);
		$view->option      = $this->option;
		$view->group       = $this->group;
		$view->action      = $this->action;
		$view->params      = $this->params;

		$view->settings    = new \Hubzero\Plugin\Params($this->database);
		$view->settings->loadPlugin($this->group->get('gidNumber'), 'groups', $this->_name);

		$view->authorized  = $this->authorized;
		$view->message     = (isset($this->message)) ? $this->message : '';

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		return $view->loadTemplate();
	}

	/**
	 * Save blog settings
	 *
	 * @return     void
	 */
	private function _savesettings()
	{
		// Login check
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		if ($this->authorized != 'manager' && $this->authorized != 'admin')
		{
			$this->setError(JText::_('PLG_COLLECTIONS_BLOG_NOT_AUTH'));
			return $this->_collections();
		}

		$settings = JRequest::getVar('settings', array(), 'post');

		$row = new \Hubzero\Plugin\Params($this->database);
		if (!$row->bind($settings))
		{
			$this->setError($row->getError());
			return $this->_settings();
		}

		// Get parameters
		$prms = JRequest::getVar('params', array(), 'post');

		$params = new JRegistry();
		$params->loadArray($prms);
		$row->params = $params->toString();

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			return $this->_settings();
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->_settings();
		}

		$this->redirect(
			JRoute::_('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=' . $this->_name),
			JText::_('PLG_GROUPS_COLLECTIONS_SETTINGS_SAVED'),
			'passed'
		);
	}

	/**
	 * Get the group's custom params
	 *
	 * @param      integer  $group_id
	 * @return     object
	 */
	protected function _params($group_id)
	{
		if (!$this->_params)
		{
			$database = JFactory::getDBO();
			$p = new \Hubzero\Plugin\Params($database);
			$this->_params = $p->getCustomParams($group_id, 'groups', $this->_name);
		}
		return $this->_params;
	}

	/**
	 * Set permissions
	 *
	 * @param      string  $assetType Type of asset to set permissions for (component, section, category, thread, post)
	 * @param      integer $assetId   Specific object to check permissions for
	 * @return     void
	 */
	protected function _authorize($assetType='plugin', $assetId=null)
	{
		// Everyone can view by default
		$this->params->set('access-view', true);
		$this->params->set('access-can-follow', false);
		if (!$this->juser->get('guest'))
		{
			$customParams = $this->_params($this->group->get('gidNumber'));
			$this->params->merge($customParams);

			// Set asset to viewable
			$this->params->set('access-view-' . $assetType, false);
			if (in_array($this->juser->get('id'), $this->members))
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
					if (!$this->params->get('create_collection', 1))
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
					if (!$this->params->get('create_post', 0))
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
