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

jimport('joomla.plugin.plugin');

/**
 * Groups Plugin class for assets
 */
class plgMembersCollections extends JPlugin
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
	 * Event call to determine if this plugin should return data
	 * 
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @return     array   Plugin name
	 */
	public function onMembersAreas($user, $member)
	{
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		//if ($user->get('id') == $member->get('uidNumber'))
		//{
			$areas['collections'] = JText::_('PLG_MEMBERS_' . strtoupper($this->_name));
		//}

		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 * 
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @param      string  $option Component name
	 * @param      string  $areas  Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);
		$returnhtml = true;
		//$returnmeta = true;

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
		$this->juser  = $user;

		$this->_authorize('collection');

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'collections.php');
		$this->model = new CollectionsModel('member', $this->member->get('uidNumber'));

		//are we returning html
		if ($returnhtml) 
		{
			// Set some variables so other functions have access
			$this->option   = $option;
			$this->database = JFactory::getDBO();

			$this->_authorize('item');

			$default = $this->params->get('defaultView', 'feed');
			if ($this->juser->get('id') != $member->get('uidNumber'))
			{
				$default = 'collections';
			}
			$this->action = JRequest::getVar('action', $default);

			//push the css to the doc
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', $this->_name);

			$this->dateFormat = '%d %b, %Y';
			$this->timeFormat = '%I:%M %p';
			$this->tz = 0;
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$this->dateFormat = 'd M, Y';
				$this->timeFormat = 'h:i a';
				$this->tz = true;
			}

			$juri =& JURI::getInstance();
			$path = $juri->getPath();
			if (strstr($path, '/')) 
			{
				$path = str_replace('/members/' . $this->member->get('uidNumber') . '/' . $this->_name, '', $path);
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
			$collection->setup($this->member->get('uidNumber'), 'member');
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
		$route = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name);

		$app =& JFactory::getApplication();
		$app->enqueueMessage(JText::_('MEMBERS_LOGIN_NOTICE'), 'warning');
		$app->redirect(JRoute::_('index.php?option=com_login&return=' . base64_encode($route)));
		return;
	}

	/**
	 * Display a list of collections
	 * 
	 * @return     string
	 */
	private function _followers()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'follow',
				'layout'  => 'followers'
			)
		);
		$view->name        = $this->_name;
		$view->juser       = $this->juser;
		$view->option      = $this->option;
		$view->member      = $this->member;
		$view->params      = $this->params;
		$view->model       = $this->model;

		Hubzero_Document::addPluginScript('members', $this->_name);

		$this->jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters = array();
		$view->filters['limit']       = JRequest::getInt('limit', $this->jconfig->getValue('config.list_limit'));
		$view->filters['start']       = JRequest::getInt('limitstart', 0);

		//$filters = array();
		//$filters['user_id'] = $this->juser->get('id');
		//$filters['state']   = 1;

		//$filters = array();
		$count = array(
			'count'  => true
		);

		if (!$this->params->get('access-manage-collection')) 
		{
			$count['access'] = 0;
			$view->filters['access'] = 0;
		}

		/*$filters['count'] = true;
		$view->collections = $this->model->collections($filters);

		$filters['count'] = false;
		$view->rows = $this->model->collections($filters);

		$view->posts = 0;
		if ($view->rows) 
		{
			foreach ($view->rows as $row)
			{
				$view->posts += $row->get('posts');
			}
		}*/

		$view->collections = $this->model->collections($count);

		$view->posts       = $this->model->posts($count);

		$view->following   = $this->model->following($count);

		$view->total = $this->model->followers($count);

		$view->rows = $this->model->followers($view->filters);

		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->total, 
			$view->filters['start'], 
			$view->filters['limit']
		);

		$view->pageNav->setAdditionalUrlParam('id', $this->member->get('uidNumber'));
		$view->pageNav->setAdditionalUrlParam('active', $this->_name);
		$view->pageNav->setAdditionalUrlParam('task', 'followers');

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
	private function _following()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'follow',
				'layout'  => 'following'
			)
		);
		$view->name        = $this->_name;
		$view->juser       = $this->juser;
		$view->option      = $this->option;
		$view->member      = $this->member;
		$view->params      = $this->params;
		$view->model       = $this->model;

		Hubzero_Document::addPluginScript('members', $this->_name);

		$this->jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters = array();
		$view->filters['limit']       = JRequest::getInt('limit', $this->jconfig->getValue('config.list_limit'));
		$view->filters['start']       = JRequest::getInt('limitstart', 0);

		$filters = array();
		$filters['user_id'] = $this->juser->get('id');
		$filters['state']   = 1;

		$count = array(
			'count'  => true
		);

		$filters = array();
		if (!$this->params->get('access-manage-collection')) 
		{
			$filters['access'] = 0;
			$count['access'] = 0;
		}

		//$filters['count'] = true;
		$view->collections = $this->model->collections($count);

		$view->posts       = $this->model->posts($count);

		$view->followers   = $this->model->followers($count);

		$view->total = $this->model->followers($count);

		$view->rows  = $this->model->following($view->filters);

		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->total, 
			$view->filters['start'], 
			$view->filters['limit']
		);

		$view->pageNav->setAdditionalUrlParam('id', $this->member->get('uidNumber'));
		$view->pageNav->setAdditionalUrlParam('active', $this->_name);
		$view->pageNav->setAdditionalUrlParam('task', 'followers');

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
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'collection',
				'layout'  => 'collections'
			)
		);
		$view->name        = $this->_name;
		$view->juser       = $this->juser;
		$view->option      = $this->option;
		$view->member      = $this->member;
		$view->params      = $this->params;
		$view->model       = $this->model;

		$this->jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters = array();
		$view->filters['limit']       = JRequest::getInt('limit', $this->jconfig->getValue('config.list_limit'));
		$view->filters['start']       = JRequest::getInt('limitstart', 0);

		//Hubzero_Document::addPluginScript('members', $this->_name, 'jquery.masonry');
		Hubzero_Document::addComponentScript('com_collections', 'assets/js/jquery.masonry');
		Hubzero_Document::addComponentScript('com_collections', 'assets/js/jquery.infinitescroll');
		Hubzero_Document::addPluginScript('members', $this->_name);

		// Filters for returning results
		$filters = array();
		$filters['user_id'] = $this->juser->get('id');
		$filters['state']   = 1;

		//$filters = array();
		$count = array(
			'count'  => true
		);

		if (!$this->params->get('access-manage-collection')) 
		{
			$filters['access'] = 0;
			$count['access'] = 0;
		}

		$filters['count'] = true;
		$view->total = $this->model->collections($filters);

		$filters['count'] = false;
		$view->rows  = $this->model->collections($filters);

		$view->posts = 0;
		if ($view->rows) 
		{
			foreach ($view->rows as $row)
			{
				$view->posts += $row->get('posts');
			}
		}

		$view->followers = $this->model->followers($count);

		$view->following = $this->model->following($count);

		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->total, 
			$view->filters['start'], 
			$view->filters['limit']
		);

		$view->pageNav->setAdditionalUrlParam('id', $view->member->get('uidNumber'));
		$view->pageNav->setAdditionalUrlParam('active', $this->_name);
		$view->pageNav->setAdditionalUrlParam('task', 'all');

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
	private function _collection()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'collection'
			)
		);
		$view->name       = $this->_name;
		$view->member     = $this->member;
		$view->option     = $this->option;
		$view->params     = $this->params;
		$view->dateFormat = $this->dateFormat;
		$view->timeFormat = $this->timeFormat;
		$view->tz         = $this->tz;
		$view->model      = $this->model;

		//Hubzero_Document::addPluginScript('members', $this->_name, 'jquery.masonry');
		Hubzero_Document::addComponentScript('com_collections', 'assets/js/jquery.masonry');
		Hubzero_Document::addComponentScript('com_collections', 'assets/js/jquery.infinitescroll');
		Hubzero_Document::addPluginScript('members', $this->_name);

		$this->jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters = array();
		$view->filters['limit']       = JRequest::getInt('limit', $this->jconfig->getValue('config.list_limit'));
		$view->filters['start']       = JRequest::getInt('limitstart', 0);
		$view->filters['user_id']     = $this->member->get('uidNumber');
		$view->filters['search']      = JRequest::getVar('search', '');
		$view->filters['state']       = 1;
		$view->filters['collection_id'] = JRequest::getVar('board', '');

		$view->collection = $this->model->collection($view->filters['collection_id']);
		if (!$view->collection->exists())
		{
			return $this->_collections();
			//$view->collection->setup($this->model->get('object_id'), $this->model->get('object_type'));
		}

		// Is the board restricted to logged-in users only?
		if ($view->collection->get('access') != 0 && $this->juser->get('guest'))
		{
			return $this->_login();
		}

		// Is it a private board?
		if ($view->collection->get('access') == 4 && $this->juser->get('id') != $this->member->get('uidNumber'))
		{
			JError::raiseError(403, JText::_('Your are not authorized to access this content.'));
			return;
		}

		$view->filters['collection_id'] = $view->collection->get('id');

		$view->filters['count'] = true;
		$view->posts = $view->collection->posts($view->filters);

		$view->filters['count'] = null;
		$view->rows = $view->collection->posts($view->filters);

		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->posts, 
			$view->filters['start'], 
			$view->filters['limit']
		);

		$view->pageNav->setAdditionalUrlParam('id', $view->member->get('uidNumber'));
		$view->pageNav->setAdditionalUrlParam('active', $this->_name);
		$view->pageNav->setAdditionalUrlParam('task', $view->collection->get('alias'));

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

		// Is it a private board?
		/*if ($this->juser->get('id') != $this->member->get('uidNumber'))
		{
			JError::raiseError(403, JText::_('Your are not authorized to access this content.'));
			return;
		}*/

		if ($this->juser->get('id') == $this->member->get('uidNumber'))
		{
			JError::raiseError(500, JText::_('Your cannot follow your own content.'));
			return;
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
					JError::raiseError(400, JText::_('Collection does not exist'));
					return;
				}
				$id = $collection->get('id');
				$sfx = '&task=' . $collection->get('alias') . '/unfollow';
			break;
		}

		if (!$this->model->follow($id, $what, $this->juser->get('id'), 'member'))
		{
			$this->setError($this->model->getError());
		}

		if (JRequest::getInt('no_html', 0))
		{
			$response = new stdClass;
			$response->href = JRoute::_('index.php?option=com_members&id=' . $this->member->get('uidNumber') . '&active=collections' . $sfx);
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
	 * @return     string
	 */
	private function _unfollow($what='collection')
	{
		// Is the board restricted to logged-in users only?
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		// Is it a private board?
		if ($this->juser->get('id') == $this->member->get('uidNumber'))
		{
			JError::raiseError(500, JText::_('Your cannot unfollow your own content.'));
			return;
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
					JError::raiseError(400, JText::_('Collection does not exist'));
					return;
				}
				$id = $collection->get('id');
				$sfx = '&task=' . $collection->get('alias') . '/follow';
			break;
		}

		if (!$this->model->unfollow($id, $what, $this->juser->get('id'), 'member'))
		{
			$this->setError($this->model->getError());
		}

		if (JRequest::getInt('no_html', 0))
		{
			$response = new stdClass;
			$response->href = JRoute::_('index.php?option=com_members&id=' . $this->member->get('uidNumber') . '&active=collections' . $sfx);
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
	 * @return     string
	 */
	private function _feed()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'collection',
				'layout'  => 'feed'
			)
		);
		$view->name       = $this->_name;
		$view->member     = $this->member;
		$view->option     = $this->option;
		$view->params     = $this->params;
		$view->dateFormat = $this->dateFormat;
		$view->timeFormat = $this->timeFormat;
		$view->tz         = $this->tz;

		//Hubzero_Document::addPluginScript('members', $this->_name, 'jquery.masonry');
		Hubzero_Document::addComponentScript('com_collections', 'assets/js/jquery.masonry');
		Hubzero_Document::addComponentScript('com_collections', 'assets/js/jquery.infinitescroll');
		Hubzero_Document::addPluginScript('members', $this->_name);

		// Filters for returning results
		$this->jconfig = JFactory::getConfig();

		$view->filters = array();
		$view->filters['limit']       = JRequest::getInt('limit', $this->jconfig->getValue('config.list_limit'));
		$view->filters['start']       = JRequest::getInt('limitstart', 0);
		$view->filters['user_id']     = $this->member->get('uidNumber');
		//$view->filters['created_by']     = $this->member->get('uidNumber');
		$view->filters['search']      = JRequest::getVar('search', '');
		$view->filters['state']       = 1;
		$view->filters['collection_id'] = JRequest::getVar('board', '');

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
		$view->collection = CollectionsModelCollection::getInstance();
		if (count($view->filters['collection_id']) <= 0)
		{
			$view->filters['collection_id'][] = -1;
		}

		$view->filters['count'] = true;
		$view->total = $view->collection->posts($view->filters);

		$view->filters['count'] = null;
		$view->rows = $view->collection->posts($view->filters);

		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->total, 
			$view->filters['start'], 
			$view->filters['limit']
		);

		$view->pageNav->setAdditionalUrlParam('id', $view->member->get('uidNumber'));
		$view->pageNav->setAdditionalUrlParam('active', $this->_name);
		//$view->pageNav->setAdditionalUrlParam('task', 'feed');

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
	private function _posts()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'collection',
				'layout'  => 'posts'
			)
		);
		$view->name       = $this->_name;
		$view->member     = $this->member;
		$view->option     = $this->option;
		$view->params     = $this->params;
		$view->dateFormat = $this->dateFormat;
		$view->timeFormat = $this->timeFormat;
		$view->tz         = $this->tz;
		$view->model      = $this->model;

		//Hubzero_Document::addPluginScript('members', $this->_name, 'jquery.masonry');
		Hubzero_Document::addComponentScript('com_collections', 'assets/js/jquery.masonry');
		Hubzero_Document::addComponentScript('com_collections', 'assets/js/jquery.infinitescroll');
		Hubzero_Document::addPluginScript('members', $this->_name);

		$this->jconfig = JFactory::getConfig();

		// Filters for returning results
		$view->filters = array();
		$view->filters['limit']       = JRequest::getInt('limit', $this->jconfig->getValue('config.list_limit'));
		$view->filters['start']       = JRequest::getInt('limitstart', 0);
		//$view->filters['user_id']     = $this->member->get('uidNumber');
		$view->filters['created_by']  = $this->member->get('uidNumber');
		$view->filters['search']      = JRequest::getVar('search', '');
		$view->filters['state']       = 1;
		$view->filters['object_id']   = $this->member->get('uidNumber');
		$view->filters['object_type'] = 'member';
		//$view->filters['collection_id'] = JRequest::getVar('board', '');

		// Filters for returning results
		//$filters = array();
		$count = array(
			'count' => true
		);

		if (!$this->params->get('access-manage-collection')) 
		{
			$view->filters['access'] = 0;
			$count['access'] = $view->filters['access'];
		}

		$view->collections = $this->model->collections($count);

		$view->followers   = $this->model->followers($count);

		$view->following   = $this->model->following($count);

		/*$count['object_id'] = '';
		$count['object_type'] = '';
		$count['created_by']  = $this->member->get('uidNumber');*/
		$view->posts       = $this->model->posts($count);

		$view->collection = CollectionsModelCollection::getInstance();

		$view->filters['user_id']     = $this->member->get('uidNumber');

		$view->rows = $view->collection->posts($view->filters);

		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->posts, 
			$view->filters['start'], 
			$view->filters['limit']
		);

		$view->pageNav->setAdditionalUrlParam('id', $view->member->get('uidNumber'));
		$view->pageNav->setAdditionalUrlParam('active', $this->_name);
		$view->pageNav->setAdditionalUrlParam('task', 'posts');

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
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'post'
			)
		);
		$view->option     = $this->option;
		$view->member     = $this->member;
		$view->params     = $this->params;
		$view->juser      = $this->juser;
		$view->name       = $this->_name;
		$view->dateFormat = $this->dateFormat;
		$view->timeFormat = $this->timeFormat;
		$view->tz         = $this->tz;
		$view->model      = $this->model;

		$post_id = JRequest::getInt('post', 0);

		$view->post = CollectionsModelPost::getInstance($post_id);

		if (!$view->post->exists()) 
		{
			return $this->_collections();
		}

		$view->collection = $this->model->collection($view->post->get('collection_id'));
		if ($view->collection->get('access') == 4 // private collection
		 && $this->juser->get('id') != $this->member->get('uidNumber')) // is user the collection owner?
		{
			$this->params->set('access-view-item', false);
		}

		// Check authorization
		if (!$this->params->get('access-view-item')) 
		{
			JError::raiseError(403, JText::_('PLG_MEMBERS' . strtoupper($this->_name) . 'NOT_AUTH'));
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
	private function _edit()
	{
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		if (!$this->params->get('access-edit-item') && !$this->params->get('access-create-item')) 
		{
			$app =& JFactory::getApplication();
			$app->enqueueMessage(JText::_('You are not authorized to perform this action.'), 'error');
			$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name));
			return;
		}

		ximport('Hubzero_Plugin_View');
		$no_html = JRequest::getInt('no_html', 0);
		if ($no_html)
		{
			$type = strtolower(JRequest::getWord('type', 'file'));
			if (!in_array($type, array('file', 'image', 'text', 'link')))
			{
				$type = 'file';
			}

			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => $this->_name,
					'name'    => 'post',
					'layout'  => 'edit_' . $type
				)
			);
		}
		else
		{
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => $this->_name,
					'name'    => 'post',
					'layout'  => 'edit'
				)
			);
		}
		$view->name       = $this->_name;
		$view->option     = $this->option;
		$view->member     = $this->member;
		$view->task       = $this->action;
		$view->params     = $this->params;
		$view->dateFormat = $this->dateFormat;
		$view->timeFormat = $this->timeFormat;
		$view->tz         = $this->tz;
		$view->no_html     = $no_html;

		$id = JRequest::getInt('post', 0);

		$view->collection = $this->model->collection(JRequest::getVar('board', 0));

		$view->collections = $this->model->collections();
		if (!$view->collections->total())
		{
			$view->collection->setup($this->member->get('uidNumber'), 'member');
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
			Hubzero_Document::addPluginScript('members', $this->_name);

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
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		if (!$this->params->get('access-create-item') && !$this->params->get('access-edit-item')) 
		{
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_collections();
		}

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');
		$files  = JRequest::getVar('fls', '', 'files', 'array');
		/*$descriptions = JRequest::getVar('description', array(), 'post');*/

		// Get model
		$row = new CollectionsModelItem();

		// Bind content
		if (!$row->bind($fields)) 
		{
			$this->setError($row->getError());
			return $this->_edit($row);
		}

		// Add some data
		if ($files)
		{
			$row->set('_files', $files);
		}
		$row->set('_assets', JRequest::getVar('assets', array(), 'post'));
		$row->set('_tags', trim(JRequest::getVar('tags', '')));
		$row->set('state', 1);
		$row->set('access', 0);

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
			$collection->set('object_id', $this->member->get('uidNumber'));
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
			return $this->_edit($row);
		}

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name . '&task=' . $this->model->collection($p['collection_id'])->get('alias')));
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

		/*if (!$this->params->get('access-create-item')) 
		{
			$this->setError(JText::_('PLG_GROUPS' . strtoupper($this->_name) . 'NOT_AUTHORIZED'));
			return $this->_collections();
		}*/

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

			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => $this->_name,
					'name'    => 'post',
					'layout'  => 'repost'
				)
			);

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
			else 
			{
				return $view->loadTemplate();
			}
		}

		$collection_id = JRequest::getInt('collection_id', 0);
		if (!$collection_id)
		{
			$collection = new CollectionsModelCollection();
			$collection->set('title', JRequest::getVar('collection_title', ''));
			$collection->set('object_id', $this->juser->get('id'));
			$collection->set('object_type', 'member');
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
			$post->description   = JRequest::getVar('description', '');
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

		// Display updated item stats if called via AJAX
		if ($no_html)
		{
			echo JText::sprintf('%s reposts', $post->getCount(array('item_id' => $post->get('item_id'), 'original' => 0)));
			exit;
		}

		// Display the main listing
		return $this->_browse();
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
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_collections();
		}

		// Incoming
		$post = CollectionsModelPost::getInstance(JRequest::getInt('post', 0));

		$collection = $this->model->collection($post->get('collection_id'));

		if (!$post->remove())
		{
			$this->setError($post->getError());
		}

		$route = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name . '&task=' . $collection->get('alias'));

		if (($no_html = JRequest::getInt('no_html', 0)))
		{
			echo $route;
			exit;
		}

		$app =& JFactory::getApplication();
		$app->redirect($route);
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

		// Authorization check
		if (!$this->params->get('access-edit-item')) 
		{
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_collections();
		}

		// Incoming
		$post = CollectionsModelPost::getInstance(JRequest::getInt('post', 0));

		if (!$post->move(JRequest::getInt('board', 0)))
		{
			$this->setError($post->getError());
		}

		$route = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name);

		if (($no_html = JRequest::getInt('no_html', 0)))
		{
			echo $route;
			exit;
		}

		$app =& JFactory::getApplication();
		$app->redirect($route);
	}

	/**
	 * Delete an entry
	 * 
	 * @return     string
	 */
	private function _delete()
	{
		// Login check
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-delete-item')) 
		{
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
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
		if (!$process || !$confirmdel) 
		{
			if ($process && !$confirmdel) 
			{
				$this->setError(JText::_('PLG_GROUPS_' . strtoupper($this->_name) . '_ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => $this->_name,
					'name'    => 'post',
					'layout'  => 'delete'
				)
			);
			$view->option   = $this->option;
			$view->member   = $this->member;
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

		// Mark the entry as deleted
		$item = $post->item();
		$item->set('state', 2);
		if (!$item->store()) 
		{
			$this->setError($item->getError());
		}

		// Redirect to collection
		$route = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name . '&task=' . $collection->get('alias'));

		if ($no_html)
		{
			echo $route;
			exit;
		}

		$app =& JFactory::getApplication();
		$app->redirect($route);
	}

	/**
	 * Save a comment
	 * 
	 * @return     string
	 */
	private function _savecomment()
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		// Incoming
		$comment = JRequest::getVar('comment', array(), 'post');

		// Instantiate a new comment object and pass it the data
		ximport('Hubzero_Item_Comment');
		$row = new Hubzero_Item_Comment($this->database);
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
		ximport('Hubzero_Item_Comment');
		$comment = new Hubzero_Item_Comment($this->database);
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
			echo JText::sprintf('%s likes', $post->item()->get('positive'));
			exit;
		}

		// Get the collection model
		$collection = $this->model->collection($post->get('collection_id'));

		// Display the main listing
		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name . '&task=' . $collection->get('alias')));
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
		$app =& JFactory::getApplication();

		$collection = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name);

		// Login check
		if ($this->juser->get('guest')) 
		{
			$app->enqueueMessage(JText::_('MEMBERS_LOGIN_NOTICE'), 'warning');
			$app->redirect('/login?return=' . base64_encode($collection));
			return;
		}

		// Access check
		if (!$this->params->get('access-create-collection') && !$this->params->get('access-edit-collection')) 
		{
			$app->enqueueMessage(JText::_('You are not authorized to edit this collection.'), 'error');
			$app->redirect($collection);
			return;
		}

		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'collection',
				'layout'  => 'edit'
			)
		);
		$view->name    = $this->_name;
		$view->option  = $this->option;
		$view->member  = $this->member;
		$view->task    = $this->action;
		$view->params  = $this->params;
		$view->no_html = JRequest::getInt('no_html', 0);

		if (is_object($row))
		{
			$view->entry = $row;
		}
		else
		{
			$view->entry = $this->model->collection(JRequest::getVar('board', ''));
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
		// Login check
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		// Access check
		if (!$this->params->get('access-edit-collection') || !$this->params->get('access-create-collection')) 
		{
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_collections();
		}

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		// Bind new content
		$row = new CollectionsModelCollection();
		if (!$row->bind($fields)) 
		{
			$this->setError($row->getError());
			return $this->_editcollection($row);
		}
		if ($row->get('access') != 0 && $row->get('access') != 4)
		{
			$row->set('access', 0);
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->_editcollection($row);
		}

		// Redirect to collection
		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name . '&task=' . $row->get('alias')));
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
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
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
				$this->setError(JText::_('PLG_GROUPS' . strtoupper($this->_name) . 'ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => $this->_name,
					'name'    => 'collection',
					'layout'  => 'delete'
				)
			);
			$view->option     = $this->option;
			$view->member     = $this->member;
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
		$route = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name);

		if ($no_html)
		{
			echo $route;
			exit;
		}

		$app =& JFactory::getApplication();
		$app->redirect($route);
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
		$this->params->set('access-view-' . $assetType, true);
		if (!$this->juser->get('guest')) 
		{
			// Can NOT create, delete, or edit by default
			$this->params->set('access-manage-' . $assetType, false);
			$this->params->set('access-create-' . $assetType, false);
			$this->params->set('access-delete-' . $assetType, false);
			$this->params->set('access-edit-' . $assetType, false);

			if ($this->juser->get('id') == $this->member->get('uidNumber'))
			{
				$this->params->set('access-manage-' . $assetType, true);
				$this->params->set('access-create-' . $assetType, true);
				$this->params->set('access-delete-' . $assetType, true);
				$this->params->set('access-edit-' . $assetType, true);
				$this->params->set('access-view-' . $assetType, true);
			}
		}
	}
}
