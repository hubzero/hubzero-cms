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
							if ($bits[1] == 'new')
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
				/*$this->action = (isset($bits[0])) ? $bits[0] : $this->action;
				if (isset($bits[1]))
				{
					if (is_numeric($bits[1]))
					{
						$id = intval($bits[1]);
						$task = (isset($bits[2])) ? $bits[2] : $task;
					}
					else
					{
						$task = (isset($bits[1])) ? $bits[1] : $task;
					}
				}*/
			}

			/*if ($this->action == 'collection')
			{
				if ($id)
				{
					$this->action = 'board';
					JRequest::setVar('board', $id);
				}
				else if ($task)
				{
					$this->action = 'board';
				}
				$this->action = ($task) ? $task . $this->action : $this->action;
			}

			if ($this->action == 'posts')
			{
				$this->action = 'post';
				if (in_array($task, array('post', 'vote', 'repost', 'unpost', 'move', 'comment')))
				{
					$this->action = '';
				}
				if ($id)
				{
					JRequest::setVar('post', $id);
				}
				$this->action = ($task) ? $task . $this->action : $this->action;
			}*/

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
		//$view->filters['board_id']    = JRequest::getInt('board', 0);

		/*$view->board = new BulletinboardBoard($this->database);

		$filters = array(
			'object_type' => $view->filters['object_type'],
			'object_id'   => $view->filters['object_id'],
			'state'       => 1
		);*/
		$filters = array();
		if (!$this->params->get('access-manage-collection')) 
		{
			$filters['access'] = 0;
		}
		$view->rows = $this->model->collections($filters); //$view->board->getRecords($filters);

		//$view->filters['collection_id'] = array();

		$view->posts = 0;
		if ($view->rows) 
		{
			foreach ($view->rows as $row)
			{
				$view->posts += $row->get('posts');
				//$view->filters['collection_id'][] = $row->get('id');
			}
		}

		//$bulletin = new CollectionsTableItem($this->database);
		//$view->bulletins = 0;//$bulletin->getCount($view->filters);

		//$vote = new CollectionsTableVote($this->database);
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

		/*$view->board = new BulletinboardBoard($this->database);
		if (!$view->filters['board_id'])
		{
			$view->board->loadDefault($view->filters['object_id'], $view->filters['object_type']);
		}
		else
		{
			$view->board->load($view->filters['board_id']);
		}*/
		$view->collection = $this->model->collection($view->filters['collection_id']);
		if (!$view->collection->exists())
		{
			$view->collection->setup($view->filters['object_id'], $view->filters['object_type']);
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

		/*$view->boards = $view->board->getCount(array(
			'object_type' => $view->filters['object_type'],
			'object_id'   => $view->filters['object_id'],
			'state'       => 1
		));*/

		//$view->filters['collection_id'] = $view->collection->get('id');

		//$bulletin = new CollectionsTableItem($this->database);
		$view->rows = $view->collection->posts($view->filters); //$bulletin->getRecords($view->filters);

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
		//$board = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name);
		if ($this->juser->get('guest')) 
		{
			/*$app->enqueueMessage(JText::_('MEMBERS_LOGIN_NOTICE'), 'warning');
			$app->redirect('/login?return=' . base64_encode($board));
			return;*/
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
		$view->dateFormat  = $this->dateFormat;
		$view->timeFormat  = $this->timeFormat;
		$view->tz          = $this->tz;

		$id = JRequest::getInt('post', 0);

		$view->entry = CollectionsModelPost::getInstance($id);

		//$view->board_id = JRequest::getVar('board', 0);

		$view->collection = $this->model->collection(JRequest::getVar('board', 0)); //new BulletinboardBoard($this->database);
		if (!$view->collection->exists())
		{
			
		}

		$view->entry = $view->collection->post($id);
		/*$view->board->loadDefault($this->member->get('uidNumber'), 'member');
		if (!$view->board->id)
		{
			$view->board->setup($this->member->get('uidNumber'), 'member');
		}*/
		if ($remove = JRequest::getInt('remove', 0))
		{
			$attachment = new CollectionsTableAsset($this->database);
			$attachment->remove($remove);
		}

		if ($no_html)
		{
			$view->display();
			exit;
		}
		else
		{
			/*$view->boards = $view->board->getRecords(array(
				'object_type' => 'member',
				'object_id'   => $this->member->get('uidNumber'),
				'state'       => 1
			));*/
			$view->collections = $this->model->collections();
			if (!$view->collections)
			{
				$view->collection->setup($this->member->get('uidNumber'), 'member');
				//$view->collections = array($view->board);
				$view->collections = $this->model->collections();
			}
	
			Hubzero_Document::addPluginScript('members', $this->_name);

			//$bt = new CollectionsTags($this->database);
			$view->tags = ''; //$bt->get_tag_string($view->entry->id);

			return $view->loadTemplate();
		}
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
	 * Save an entry
	 * 
	 * @return     void
	 */
	private function _save()
	{
		if ($this->juser->get('guest')) 
		{
			//$this->setError(JText::_('MEMBERS_LOGIN_NOTICE'));
			return $this->_login();
		}

		if (!$this->params->get('access-create-item') && !$this->params->get('access-edit-item')) 
		{
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		$fields = JRequest::getVar('fields', array(), 'post');

		$row = new CollectionsTableItem($this->database);
		if (!$row->bind($fields)) 
		{
			$this->setError($row->getError());
			return $this->_edit();
		}

		$files = JRequest::getVar('fls', '', 'files', 'array');
		if ($row->type == 'image' || $row->type == 'file')
		{
			if (!$files || count($files['name']) <= 0)
			{
				$this->setError(JText::_('Please provide a file'));
				return $this->_edit();
			}
		}

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			return $this->_edit();
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->_edit();
		}

		if ($row->type == 'image' || $row->type == 'file')
		{
			$descriptions = JRequest::getVar('description', array(), 'post');
			if (!$this->_upload($files, $row->id, $row->type, $descriptions))
			{
				return $this->_edit();
			}

			$assets = JRequest::getVar('asset', array(), 'post');
			if ($assets && count($assets) > 0)
			{
				foreach ($assets as $asset)
				{
					$attachment = new CollectionsTableAsset($this->database);
					$attachment->load(intval($asset['id']));
					$attachment->description = (isset($asset['description'])) ? trim($asset['description']) : '';

					if (!$attachment->check()) 
					{
						$this->setError($attachment->getError());
						continue;
					}
					if (!$attachment->store()) 
					{
						$this->setError($attachment->getError());
					}
				}
			}
		}

		$stick = new CollectionsTablePost($this->database);
		$stick->bulletin_id = $row->id;
		$stick->board_id    = $fields['board_id'];
		$stick->original    = 1;
		if ($stick->check()) 
		{
			// Store new content
			if (!$stick->store()) 
			{
				$this->setError($stick->getError());
			}
		}

		// Process tags
		$tags = trim(JRequest::getVar('tags', ''));
		$bt = new CollectionsTags($this->database);
		$bt->tag_object($this->juser->get('id'), $row->id, $tags, 1, 1);

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name . '&task=boards/' . $fields['board_id']));
	}

	/**
	 * Repost an entry
	 * 
	 * @return     string
	 */
	private function _repost()
	{
		// Incoming
		$post_id       = JRequest::getInt('post', 0);
		$collection_id = JRequest::getVar('board', 0);
		$no_html       = JRequest::getInt('no_html', 0);

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'item.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'post.php');
		//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'asset.php');
		//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'vote.php');
		//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'helpers' . DS . 'tags.php');

		if (!$post_id && $collection_id)
		{
			$row = new CollectionsTableCollection($this->database);
			$row->load($collection_id, $this->member->get('uidNumber'), 'member');

			$b = new CollectionsTableItem($this->database);
			$b->loadType($row->id, 'board');
			if (!$b->id)
			{
				$b->type        = 'board';
				$b->object_id   = $row->id;
				$b->title       = $row->title;
				$b->description = $row->description;
				if (!$b->check()) 
				{
					$this->setError($b->getError());
				}
				// Store new content
				if (!$b->store()) 
				{
					$this->setError($b->getError());
				}
			}
			$item_id = $b->id;
			$col = $b->object_id;
			$collection_id = 0;
		}
		else
		{
			$post = new CollectionsTablePost($this->database);
			$post->load($post_id);

			$item_id = $post->item_id;
			$col = 0;
		}

		// No board ID selected so present repost form
		if (!$collection_id)
		{
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => $this->_name,
					'name'    => 'edit',
					'layout'  => 'repost'
				)
			);

			$board = new CollectionsTableCollection($this->database);

			$view->myboards = $board->getRecords(array(
				'object_type' => 'member',
				'object_id'   => $this->juser->get('id'),
				'state'       => 1
			));
			if (!$view->myboards)
			{
				$board->setup($this->juser->get('id'), 'member');
				$view->myboards = array($board);
			}

			$view->groupboards = array();

			$usergroups = $this->member->getGroups('members');
			if ($usergroups)
			{
				foreach ($usergroups as $usergroup)
				{
					$groups = $board->getRecords(array(
						'object_type' => 'group',
						'object_id'   => $usergroup->gidNumber,
						'state'       => 1
					));
					if ($groups)
					{
						foreach ($groups as $s)
						{
							if (!isset($view->groupboards[$s->group_alias]))
							{
								$view->groupboards[$s->group_alias] = array();
							}
							if ($s->access == 4 && !$usergroup->manager)
							{
								continue;
							}
							$view->groupboards[$s->group_alias][] = $s;
							asort($view->groupboards[$s->group_alias]);
						}
					}
				}
			}

			asort($view->groupboards);

			$view->name        = $this->_name;
			$view->option      = $this->option;
			$view->member      = $this->member;
			//$view->task        = $this->action;
			//$view->params      = $this->params;
			//$view->authorized  = $this->authorized;

			$view->no_html     = $no_html;
			$view->post_id  = $post_id;
			$view->collection_id = $col;
			$view->item_id  = $item_id;

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

		// Try loading the current board/bulletin to see
		// if this has already been posted to the board (i.e., no duplicates)
		$post = new CollectionsTablePost($this->database);
		$post->loadByBoard($collection_id, $item_id);
		if (!$post->id)
		{
			// No record found -- we're OK to add one
			$post->item_id       = $item_id;
			$post->collection_id = $collection_id;
			$post->description   = JRequest::getVar('description', '');
			if ($stick->check()) 
			{
				// Store new content
				if (!$stick->store()) 
				{
					$this->setError($stick->getError());
				}
			}
		}

		// Display updated bulletin stats if called via AJAX
		if ($no_html)
		{
			echo JText::sprintf('%s reposts', $stick->getCount(array('bulletin_id' => $stick->bulletin_id, 'original' => 0)));
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
		if ($this->juser->get('guest')) 
		{
			//$this->setError(JText::_('GROUPS_LOGIN_NOTICE'));
			return $this->_login();
		}

		if (!$this->params->get('access-create-item')) 
		{
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_boards();
		}

		// Incoming
		$post_id = JRequest::getInt('post', 0);
		$no_html = JRequest::getInt('no_html', 0);

		// Try loading the current collection/item to see
		// if this has already been posted to the collection (i.e., no duplicates)
		$post = new CollectionsTablePost($this->database);
		$post->load($post_id);

		$collection = $this->model->collection($post->collection_id);

		// Can't remove original posts. They must be deleted instead.
		if (!$post->original)
		{
			if (!$post->delete()) 
			{
				$this->setError($post->getError());
			}
		}

		if ($no_html)
		{
			echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name . '&task=' . $collection->get('alias'));
			exit;
		}

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name . '&task=' . $collection->get('alias')));
	}

	/**
	 * Move a bulletin to another board
	 * 
	 * @return     void
	 */
	private function _move()
	{
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		if (!$this->params->get('access-create-item')) 
		{
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_board();
		}

		// Incoming
		$post_id = JRequest::getInt('post', 0);
		$no_html = JRequest::getInt('no_html', 0);

		$stick = new CollectionsTablePost($this->database);
		$stick->load($post_id);
		$stick->collection_id = JRequest::getInt('board', 0);
		if ($stick->check()) 
		{
			// Store new content
			if (!$stick->store()) 
			{
				$this->setError($stick->getError());
			}
		}

		if ($no_html)
		{
			echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name);
			exit;
		}

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name));
	}

	/**
	 * Delete an entry
	 * 
	 * @return     string
	 */
	private function _delete()
	{
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		if (!$this->params->get('access-delete-item')) 
		{
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_board();
		}

		// Incoming
		$no_html = JRequest::getInt('no_html', 0);
		$id = JRequest::getInt('post', 0);
		if (!$id) 
		{
			return $this->_board();
		}

		$process = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');

		// Initiate a whiteboard entry object
		$bulletin = new CollectionsTableItem($this->database);
		$bulletin->load($id);

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
					'name'    => 'delete'
				)
			);
			$view->option   = $this->option;
			$view->member   = $this->member;
			$view->task     = $this->action;
			$view->params   = $this->params;
			$view->bulletin = $bulletin;
			$view->no_html  = $no_html;
			$view->name     = $this->_name;

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
		$bulletin->state = 2;

		if (!$bulletin->store()) 
		{
			$this->setError($bulletin->getError());
		}

		if ($no_html)
		{
			echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name);
			exit;
		}

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name));
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
	 * Upload a file
	 * 
	 * @return     void
	 */
	private function _vote()
	{
		$id = JRequest::getInt('post', 0);

		// Create a vote record
		$vote = new CollectionsTableVote($this->database);
		$vote->loadByBulletin($id, $this->juser->get('id'));

		$like = true;

		if (!$vote->id)
		{
			$vote->bulletin_id = $id;
			// Store the record
			if (!$vote->check())
			{
				$this->setError($vote->getError());
			}
			else
			{
				if (!$vote->store())
				{
					$this->setError(JText::_('Error occurred while saving vote'));
				}
			}
		}
		else
		{
			$like = false;
			// Load the vote record
			if (!$vote->delete())
			{
				$this->setError($vote->getError());
			}
		}

		// Load the bulletin
		$bulletin = new CollectionsTableItem($this->database);
		$bulletin->load($id);
		if (!$this->getError())
		{
			if ($like)
			{
				// Increase like count
				$bulletin->positive++;
			}
			else if ($bulletin->positive > 0) // Make sure we don't go below 0
			{
				// Decrease like count
				$bulletin->positive--;
			}
			$bulletin->store();
		}

		// Display updated bulletin stats if called via AJAX
		$no_html = JRequest::getInt('no_html', 0);
		if ($no_html)
		{
			echo JText::sprintf('%s likes', $bulletin->positive);
			exit;
		}

		// Display the main listing
		return $this->_browse();
	}

	/**
	 * Upload a file
	 * 
	 * @return     void
	 */
	private function _upload($files, $listdir, $type, $descriptions)
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) 
		{
			$this->setError(JText::_('MEMBERS_LOGIN_NOTICE'));
			return false;
		}

		// Ensure we have an ID to work with
		//$listdir = JRequest::getInt('listdir', 0, 'post');
		if (!$listdir) 
		{
			$this->setError(JText::_('WIKI_NO_ID'));
			return false;
		}

		// Build the upload path if it doesn't exist
		$path = JPATH_ROOT . DS . trim($this->params->get('filepath', '/site/bulletins'), DS) . DS . $listdir;

		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setError(JText::_('Error uploading. Unable to create path.'));
				return false;
			}
		}
		jimport('joomla.filesystem.file');
		foreach ($files['name'] as $i => $file)
		{
			// Incoming file
			//$file = JRequest::getVar('upload', '', 'files', 'array');
			if (!$files['name'][$i]) 
			{
				$this->setError(JText::_('WIKI_NO_FILE'));
				return false;
			}

			$ext = strtolower(JFile::getExt($files['name'][$i]));
			if ($type == 'image' && !in_array($ext, array('jpg', 'jpeg', 'jpe', 'gif', 'png')))
			{
				continue;
			}

			// Make the filename safe
			jimport('joomla.filesystem.file');
			$files['name'][$i] = urldecode($files['name'][$i]);
			$files['name'][$i] = JFile::makeSafe($files['name'][$i]);
			$files['name'][$i] = str_replace(' ', '_', $files['name'][$i]);

			// Upload new files
			if (!JFile::upload($files['tmp_name'][$i], $path . DS . $files['name'][$i])) 
			{
				$this->setError(JText::_('ERROR_UPLOADING'));
				return false;
			}
			// File was uploaded 
			else 
			{
				// Create database entry
				$attachment = new CollectionsTableAsset($this->database);
				$attachment->bulletin_id = $listdir;
				$attachment->filename    = $files['name'][$i];
				$attachment->description = (isset($descriptions[$i])) ? $descriptions[$i] : '';

				if (!$attachment->check()) 
				{
					$this->setError($attachment->getError());
					return false;
				}
				if (!$attachment->store()) 
				{
					$this->setError($attachment->getError());
					return false;
				}
			}
		}

		return true;
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

		if ($this->juser->get('guest')) 
		{
			$app->enqueueMessage(JText::_('MEMBERS_LOGIN_NOTICE'), 'warning');
			$app->redirect('/login?return=' . base64_encode($collection));
			return;
		}

		if (!$this->params->get('access-create-collection') && !$this->params->get('access-edit-collection')) 
		{
			$app->enqueueMessage(JText::_('You are not authorized to edit this collection.'), 'error');
			$app->redirect($collection);
			return;
		}

		$no_html = JRequest::getInt('no_html', 0);

		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->_name,
				'name'    => 'collection',
				'layout'  => 'edit'
			)
		);

		$view->name       = $this->_name;
		$view->option     = $this->option;
		$view->member     = $this->member;
		$view->task       = $this->action;
		$view->params     = $this->params;

		if (is_object($row))
		{
			$view->entry = $row;
		}
		else
		{
			$id = JRequest::getVar('board', '');

			$view->entry = $this->model->collection($id); //CollectionsModelCollection::getInstance($id);
		}

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		if ($no_html)
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
		if ($this->juser->get('guest')) 
		{
			//$this->setError(JText::_('MEMBERS_LOGIN_NOTICE'));
			return $this->_login();
		}

		if (!$this->params->get('access-edit-collection') || !$this->params->get('access-create-collection')) 
		{
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_board();
		}

		$fields = JRequest::getVar('fields', array(), 'post');

		$row = new CollectionsModelCollection();
		if (!$row->bind($fields)) 
		{
			$this->setError($row->getError());
			return $this->_editboard($row);
		}

		/*$isNew = false;
		if (!$row->exists())
		{
			$isNew = true;
		}

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			return $this->_editboard($row);
		}*/

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return $this->_editboard($row);
		}

		/*$bulletin = new CollectionsTableItem($this->database);
		$bulletin->loadType($row->id, 'board');
		if (!$bulletin->id)
		{
			$bulletin->type = 'board';
			$bulletin->object_id = $row->id;
			$bulletin->title = $row->title;
			$bulletin->description = $row->description;
			//$bulletin->url = '/' . $row->object_type . 's/' . $object_id . '/' . $this->_name;
			if (!$bulletin->check()) 
			{
				$this->setError($bulletin->getError());
			}
			// Store new content
			if (!$bulletin->store()) 
			{
				$this->setError($bulletin->getError());
			}
		}*/

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name . '&task=boards/' . $row->get('id')));
	}

	/**
	 * Delete an entry
	 * 
	 * @return     string
	 */
	private function _deleteboard()
	{
		if ($this->juser->get('guest')) 
		{
			return $this->_login();
		}

		if (!$this->params->get('access-delete-collection')) 
		{
			$this->setError(JText::_('PLG_MEMBERS_' . strtoupper($this->_name) . '_NOT_AUTHORIZED'));
			return $this->_board();
		}

		// Incoming
		$no_html = JRequest::getInt('no_html', 0);
		$id = JRequest::getVar('board', 0);
		if (!$id) 
		{
			return $this->_board();
		}

		$process = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');

		// Initiate a whiteboard entry object
		$collection = $this->model->collection($id); //CollectionsModelCollection::getInstance($id);

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

		if ($no_html)
		{
			echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name);
			exit;
		}

		$app =& JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->_name));
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
