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
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member)))) 
			{
				$returnhtml = false;
			}
		}

		$arr = array(
			'html' => '',
			'metadata' => ''
		);

			//user vars
			$this->juser      = $user;

			// Set some variables so other functions have access
			$this->action     = JRequest::getVar('action', 'boards');
			$this->option     = $option;
			$this->name       = substr($option, 4, strlen($option));
			$this->database   = JFactory::getDBO();
			$this->member     = $member;

			$this->_authorize('collection');
			$this->_authorize('item');

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'collections.php');

		$this->model = new CollectionsModel('member', $this->member->get('uidNumber'));

		//are we returning html
		if ($returnhtml) 
		{
			//push the css to the doc
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', $this->_name);

			$this->dateFormat  = '%d %b, %Y';
			$this->timeFormat  = '%I:%M %p';
			$this->tz = 0;
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$this->dateFormat  = 'd M, Y';
				$this->timeFormat  = 'h:i a';
				$this->tz = true;
			}

			//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'bulletin.php');
			//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'stick.php');
			//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'asset.php');
			//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'vote.php');
			//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'helpers' . DS . 'tags.php');

			//$task = '';
			//$controller = 'board';
			//$id = 0;
			//$this->action = 'boards';

			$juri =& JURI::getInstance();
			$path = $juri->getPath();
			if (strstr($path, '/')) 
			{
				
				$path = str_replace('/members/' . $this->member->get('uidNumber') . '/' . $this->_name, '', $path);
				$path = ltrim($path, DS);
				$bits = explode('/', $path);

				if (isset($bits[0]) && $bits[0])
				{
					if ($bits[0] == 'post')
					{
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
									if (in_array($bits[2], array('post', 'vote', 'repost', 'remove', 'move', 'comment')))
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
					}
					else if ($bits[0] == 'new')
					{
						$this->action = 'newboard';
					}
					else
					{
						$this->action = 'board';
						JRequest::setVar('board', $bits[0]);

						if (isset($bits[1]))
						{
							$this->action = $bits[1] . $this->action;
						}
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

				// Entries
				case 'savepost':   $arr['html'] = $this->_save();   break;
				case 'newpost':    $arr['html'] = $this->_new();    break;
				case 'editpost':   $arr['html'] = $this->_edit();   break;
				case 'deletepost': $arr['html'] = $this->_delete(); break;

				case 'comment':
				case 'post':   $arr['html'] = $this->_post();   break;
				case 'vote':   $arr['html'] = $this->_vote();   break;
				case 'repost': $arr['html'] = $this->_repost(); break;
				case 'remove': $arr['html'] = $this->_remove(); break;
				case 'move':   $arr['html'] = $this->_move();   break;

				case 'repostboard': $arr['html'] = $this->_repost();      break;
				case 'newboard':    $arr['html'] = $this->_newboard();    break;
				case 'editboard':   $arr['html'] = $this->_editboard();   break;
				case 'saveboard':   $arr['html'] = $this->_saveboard();   break;
				case 'deleteboard': $arr['html'] = $this->_deleteboard(); break;
				case 'boards':      $arr['html'] = $this->_boards();      break;

				case 'board': $arr['html'] = $this->_board(); break;

				default: $arr['html'] = $this->_boards(); break;
			}
		}

		// Get a count of all the collections
		$filters = array(
			'count' => true
		);
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
		$board = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name);
		$app =& JFactory::getApplication();
		$app->enqueueMessage(JText::_('MEMBERS_LOGIN_NOTICE'), 'warning');
		$app->redirect(JRoute::_('index.php?option=com_login&return=' . base64_encode($board)));
		return;
	}

	/**
	 * Display a list of latest whiteboard entries
	 * 
	 * @return     string
	 */
	private function _boards()
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

		Hubzero_Document::addPluginScript('members', $this->_name, 'jquery.masonry');
		Hubzero_Document::addPluginScript('members', $this->_name);

		// Filters for returning results
		$view->filters = array();
		//$view->filters['limit']       = JRequest::getInt('limit', 25);
		//$view->filters['start']       = JRequest::getInt('limitstart', 0);
		$view->filters['user_id']     = $this->juser->get('id');
		//$view->filters['object_type'] = 'member';
		//$view->filters['object_id']   = $this->member->get('uidNumber');
		//$view->filters['search']      = JRequest::getVar('search', '');
		$view->filters['state']       = 1;

		$filters = array();
		if (!$this->params->get('access-manage-collection')) 
		{
			$filters['access'] = 0;
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

		$view->likes = 0; //$vote->getLikes($view->filters);

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
	 * Display a list of latest whiteboard entries
	 * 
	 * @return     string
	 */
	private function _board()
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

		Hubzero_Document::addPluginScript('members', $this->_name, 'jquery.masonry');
		Hubzero_Document::addPluginScript('members', $this->_name);

		// Filters for returning results
		$view->filters = array();
		$view->filters['limit']       = JRequest::getInt('limit', 25);
		$view->filters['start']       = JRequest::getInt('limitstart', 0);
		$view->filters['user_id']     = $this->member->get('uidNumber');
		//$view->filters['object_type'] = 'member';
		//$view->filters['object_id']   = $this->member->get('uidNumber');
		$view->filters['search']      = JRequest::getVar('search', '');
		$view->filters['state']       = 1;
		$view->filters['collection_id'] = JRequest::getVar('board', 0);

		$view->collection = $this->model->collection($view->filters['collection_id']);
		if (!$view->collection->exists())
		{
			$view->collection->setup($this->model->get('object_id'), $this->model->get('object_type'));
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

		$view->rows = $view->collection->posts($view->filters);

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
	 * Display a whiteboard entry
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
				'name'    => 'entry'
			)
		);
		$view->option = $this->option;
		$view->member = $this->member;
		$view->params = $this->params;
		$view->juser  = $this->juser;
		$view->name   = $this->_name;

		$view->dateFormat  = $this->dateFormat;
		$view->timeFormat  = $this->timeFormat;
		$view->tz          = $this->tz;

		$post_id = JRequest::getInt('post', 0);

		$view->post = new CollectionsTablePost($this->database);
		$view->post->load($post_id);

		$view->row = new CollectionsTableItem($this->database);
		$view->row->load($view->post->item_id);
		$view->row->reposts = $view->row->getReposts();
		$view->row->voted   = $view->row->getVote();

		if (!$view->row->id) 
		{
			return $this->_board();
		}

		// Check authorization
		if (!$this->params->get('access-view-item')) 
		{
			JError::raiseError(403, JText::_('PLG_MEMBERS_BULLETINBOARD_NOT_AUTH'));
			return;
		}

		ximport('Hubzero_Item_Comment');
		$bc = new Hubzero_Item_Comment($this->database);
		$view->comments = $bc->getComments($view->row->id);

		//count($this->comments, COUNT_RECURSIVE)
		$view->comment_total = 0;
		if ($view->comments) 
		{
			foreach ($view->comments as $com)
			{
				$view->comment_total++;
				if ($com->replies) 
				{
					foreach ($com->replies as $rep)
					{
						$view->comment_total++;
						if ($rep->replies) 
						{
							$view->comment_total = $view->comment_total + count($rep->replies);
						}
					}
				}
			}
		}
		$view->board = new BulletinboardBoard($this->database);
		$view->board->load($view->post->board_id);

		$bt = new BulletinboardTags($this->database);
		$view->tags = $bt->get_tag_cloud(0, 0, $view->row->id);

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
					'name'    => 'edit',
					'layout'  => '_' . $type
				)
			);
		}
		else
		{
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => $this->_name,
					'name'    => 'edit'
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

		$id = JRequest::getInt('post', 0);

		$view->collection = $this->model->collection(JRequest::getVar('board', 0));

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
			$view->collections = $this->model->collections();
			if (!$view->collections->total())
			{
				$view->collection->setup($this->member->get('uidNumber'), 'member');
				$view->collections = $this->model->collections();
			}
	
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
			return $this->_browse();
		}

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');
		$files  = JRequest::getVar('fls', '', 'files', 'array');
		$descriptions = JRequest::getVar('description', array(), 'post');

		// Get model
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'item.php');
		$row = new CollectionsModelItem();

		// Bind content
		if (!$row->bind($fields)) 
		{
			$this->setError($row->getError());
			return $this->_edit($row);
		}

		// Add some data
		$row->set('_files', $files);
		$row->set('_descriptions', $descriptions);
		$row->set('_tags', trim(JRequest::getVar('tags', '')));

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->_edit($row);
		}

		// Create a post entry linking the item to the board
		$post = new CollectionsModelPost();
		$post->set('item_id', $row->get('id'));
		$post->set('collection_id', $fields['collection_id']);
		$post->set('original', 1);
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
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name . '&task=' . $fields['collection_id']));
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
			$this->setError(JText::_('PLG_GROUPS_BULLETINBOARD_NOT_AUTHORIZED'));
			return $this->_boards();
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

			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => $this->_name,
					'name'    => 'edit',
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

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'post.php');

		$collection_id = JRequest::getInt('collection_id', 0);
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
			return $this->_boards();
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
	 * Move a bulletin to another board
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
			return $this->_boards();
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
			return $this->_boards();
		}

		// Incoming
		$no_html = JRequest::getInt('no_html', 0);
		//$id = JRequest::getInt('post', 0);
		
		$post = CollectionsModelPost::getInstance(JRequest::getInt('post', 0));
		if (!$post->get('id')) 
		{
			return $this->_boards();
		}

		$process = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');

		// Initiate a whiteboard entry object
		//$bulletin = new CollectionsTableItem($this->database);
		//$bulletin->load($post->id);

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
					'name'    => 'edit',
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

		// Set the created time
		if (!$row->id) 
		{
			$row->created = date('Y-m-d H:i:s', time());  // use gmdate() ?
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

		// Delete the entry itself
		if (!$comment->delete($id)) 
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
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'post.php');

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
	 * Display a form for creating an entry
	 * 
	 * @return     string
	 */
	private function _newboard()
	{
		return $this->_editboard();
	}

	/**
	 * Display a form for editing an entry
	 * 
	 * @return     string
	 */
	private function _editboard($row=null)
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
	 * Display a form for editing an entry
	 * 
	 * @return     string
	 */
	private function _saveboard()
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
			return $this->_board();
		}

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		// Bind new content
		$row = new CollectionsModelCollection();
		if (!$row->bind($fields)) 
		{
			$this->setError($row->getError());
			return $this->_editboard($row);
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->_editboard($row);
		}

		// Redirect to collection
		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name . '&task=' . $row->get('alias')));
	}

	/**
	 * Delete an entry
	 * 
	 * @return     string
	 */
	private function _deleteboard()
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
			return $this->_boards();
		}

		// Incoming
		$no_html = JRequest::getInt('no_html', 0);
		$id = JRequest::getVar('board', 0);

		// Ensure we have an ID to work with
		if (!$id) 
		{
			return $this->_boards();
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
				$this->setError(JText::_('PLG_GROUPS_BULLETINBOARD_ERROR_CONFIRM_DELETION'));
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
