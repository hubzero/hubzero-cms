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
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wishlist controller class
 */
class WishlistControllerWishlist extends \Hubzero\Component\SiteController
{
	/**
	 * Determine task and execute
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function execute()
	{
		$upconfig = JComponentHelper::getParams('com_members');
		$this->banking = $this->config->get('banking', $upconfig->get('bankAccounts', 0));

		$this->registerTask('__default', 'wishlist');
		$this->registerTask('editprivacy', 'editwish');
		$this->registerTask('grantwish', 'editwish');
		$this->registerTask('withdraw', 'deletewish');
		$this->registerTask('add', 'addwish');

		parent::execute();
	}

	/**
	 * Build the page title
	 *
	 * @return     void
	 */
	protected function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_option));

		if ($this->_list_title)
		{
			$this->_title .= ' - ' . $this->_list_title;
		}
		if ($this->_task && in_array($this->_task, array('settings', 'add')))
		{
			$this->_title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Build the breadcrumbs
	 *
	 * @param      object $wishlist Wishlist
	 * @return     void
	 */
	protected function _buildPathway($wishlist)
	{
		$pathway = JFactory::getApplication()->getPathway();
		$pathway->setPathway(array());

		$comtitle  = JText::_(strtoupper($this->_option));
		$comtitle .= $this->_list_title ? ' - ' . $this->_list_title : '';

		$wishlist->pathway();

		if ($this->_task)
		{
			switch ($this->_task)
			{
				case 'wish':
					$pathway->addItem(
						$this->_wishtitle,
						$this->_wishpath
					);
				break;
				case 'add':
				case 'savewish':
				case 'editwish':
					$pathway->addItem(
						$this->_taskname,
						$this->_taskpath
					);
				break;
				case 'settings':
					$pathway->addItem(
						JText::_(strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&task=settings&id=' . $this->_listid
					);
				break;
				case 'view':
				case 'cancel':
				case 'reply':
				case 'rateitem':
				case 'savereply':
				case 'savevote':
				case 'saveplan':
				case 'movewish':
				case 'editprivacy':
				case 'grantwish':
				case 'deletewish':
				case 'withdraw':
				case 'addbonus':
				case 'wishlist':
					// nothing
				break;

				default:
					// XSS fix, passing raw user supplied/maniuplatable data to function that creates link. See ticket 1420
					$pathway->addItem(
						JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&task=' . htmlentities($this->_task)
					);
				break;
			}
		}
	}

	/**
	 * Display a login form
	 *
	 * @return     void
	 */
	public function loginTask()
	{
		if (JFactory::getUser()->get('guest'))
		{
			$return = base64_encode(JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->getTask(), false, true), 'server'));
			JFactory::getApplication()->redirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . $return),
				$this->_msg,
				'warning'
			);
			return;
		}
	}

	/**
	 * Show a list of entries for this list
	 *
	 * @return     void
	 */
	public function wishlistTask()
	{
		// Incoming
		$id     = JRequest::getInt('id', 0);
		$refid  = JRequest::getInt('rid', 1);
		$cat   	= JRequest::getVar('category', 'general');
		$saved  = JRequest::getInt('saved', 0);

		// are we viewing this from within a plugin?
		$plugin = (isset($this->plugin) && $this->plugin!='') ? $this->plugin : '';

		$cats = $this->config->get('categories', 'general, resource');
		if ($cat && !preg_replace("/" . $cat . "/", '', $cats) && !$plugin)
		{
			// oups, this looks like a wrong URL
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		if ($id)
		{
			$model = WishlistModelWishlist::getInstance($id);
		}
		else
		{
			$model = WishlistModelWishlist::getInstance($refid, $cat);
			if (!$model->exists())
			{
				$model->setup();
			}
		}

		// cannot find this list
		if (!$model->exists())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		// remember list id for plugin use
		$this->listid = isset($this->listid) ? $this->listid : $id;

		// get admin priviliges
		$this->authorize_admin();

		// Authorize list owners
		if (!$this->juser->get('guest'))
		{
			if (in_array($this->juser->get('id'), $model->owners('individuals')))
			{
				$this->_admin = 2;
			}
			else if (in_array($this->juser->get('id'), $model->owners('advisory')))
			{
				$this->_admin = 3;
			}
		}

		$model->set('admin', $this->_admin);

		// Set page title
		$this->_list_title = ($model->isPublic() or (!$model->isPublic() && $this->_admin == 2)) ? $model->get('title') : '';
		$this->_subtitle   = ($model->isPublic() or (!$model->isPublic() && $this->_admin == 2)) ? $model->get('title') : '';
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway($model);

		// need to log in to private list
		if (!$model->isPublic() && $this->juser->get('guest'))
		{
			if (!$plugin)
			{
				$this->_msg = JText::_('COM_WISHLIST_WARNING_WISHLIST_PRIVATE_LOGIN_REQUIRED');
				$this->loginTask();
				return;
			}
			else
			{
				// not authorized
				JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
				return;
			}
			return;
		}

		// Get list filters
		$this->view->filters = $this->getFilters($this->_admin);
		$this->view->filters['limit'] = (isset($this->limit)) ? $this->limit : $this->view->filters['limit'];

		// Get individual wishes
		$total = $model->wishes('count', $this->view->filters);

		// Get count of granted wishes
		$sp_filters = $this->view->filters;
		$sp_filters['filterby'] = 'granted';
		$model->set('granted_count', $model->wishes('count', $sp_filters, true)); //$objWish->get_count($model->get('id'), $sp_filters, $this->_admin, $this->juser);
		$model->set('granted_percentage', ($total > 0 && $model->get('granted_count') > 0 ? round(($model->get('granted_count')/$total) * 100, 0) : 0));

		// Some extras
		$model->set('saved', $saved);
		$model->set('banking', ($this->banking ? $this->banking : 0));
		$model->set('banking', ($model->get('category') == 'user' ? 0 : $this->banking)); // do not allow points for individual wish lists

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		JRequest::setVar('id', $id);

		$this->view->setLayout('display');
		$this->view->title    = $this->_title;
		$this->view->config   = $this->config;
		$this->view->option   = $this->_option;
		$this->view->task     = $this->_task;
		$this->view->juser    = $this->juser;
		$this->view->wishlist = $model;

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Show an entry and associated content
	 *
	 * @return     void
	 */
	public function wishTask()
	{
		$wishid  	= JRequest::getInt('wishid', 0);
		$id  		= JRequest::getInt('id', 0);
		$refid  	= JRequest::getInt('rid', 0);
		$cat   		= JRequest::getVar('category', '');
		$action     = JRequest::getVar('action', '');
		$com   		= JRequest::getInt('com', 0, 'get');
		$canedit 	= false;
		$saved  	= JRequest::getInt('saved', 0);

		//$wishid = $this->wishid && !$wishid ? $this->wishid : $wishid;

		$wish = WishlistModelWish::getInstance($wishid);

		if (!$wish->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		// Get wishlist info
		$wishlist = WishlistModelWishlist::getInstance($wish->get('wishlist'));
		if (!$wishlist->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// Pass off some data
		$wish->set('category', $wishlist->get('category'));
		$wish->set('referenceid', $wishlist->get('referenceid'));

			// get admin priviliges
			$this->authorize_admin();

			// Set page title
			$this->_list_title = $wishlist->title();

			if (!$wishlist->isPublic() && !$this->_admin)
			{
				$this->_list_title = '';
			}
			$this->_buildTitle();

			// Set the pathway
			$this->_wishpath  = $wish->link(); //'index.php?option=' . $this->_option . '&task=wish&category=' . $cat . '&rid=' . $refid . '&wishid='.$wishid;
			$this->_wishtitle = \Hubzero\Utility\String::truncate($wish->get('subject'), 80);
			$this->_buildPathway($wishlist);

			// Go through some access checks
			if ($this->juser->get('guest') && $action)
			{
				$this->_msg = ($action=="addbonus") ? JText::_('COM_WISHLIST_MSG_LOGIN_TO_ADD_POINTS') : '';
				$this->loginTask();
				return;
			}

			if (!$wishlist->isPublic() && $this->juser->get('guest'))
			{
				// need to log in to private list
				$this->_msg = JText::_('COM_WISHLIST_WARNING_WISHLIST_PRIVATE_LOGIN_REQUIRED');
				$this->loginTask();
				return;
			}

			if ($wish->isPrivate() && $this->juser->get('guest'))
			{
				// need to log in to view private wish
				$this->_msg = JText::_('COM_WISHLIST_WARNING_LOGIN_PRIVATE_WISH');
				$this->loginTask();
				return;
			}

			// Deleted wish
			if ($wish->isDeleted() && !$wish->access('manage'))
			{
				JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
				return;
			}

			// Need to be admin to view private wish
			if ($wish->isPrivate() && !$wish->access('view'))
			{
				JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
				return;
			}

			// Get list filters
			$filters = self::getFilters($wish->get('admin'));

			// Update average value for importance (this is tricky MySQL)
			if (count($wishlist->owners('advisory')) > 0 && $this->config->get('votesplit', 0))
			{
				//$objR = new WishRank($this->database);
				$votes = $wish->rankings(); //$objR->get_votes($wish->get('id'));

				// first consider votes by list owners
				if ($votes)
				{
					$imp 		= 0;
					$divisor 	= 0;
					$co_adv 	= 0.8;
					$co_reg 	= 0.2;

					foreach ($votes as $vote)
					{
						if (in_array($vote->get('userid'), $wishlist->owners('advisory')))
						{
							$imp += $vote->get('importance') * $co_adv;
							$divisor += $co_adv;
						}
						else
						{
							$imp += $vote->get('importance') * $co_reg;
							$divisor += $co_reg;
						}
					}

					// weighted average
					$wish->set('average_imp', ($imp/$divisor));
				}
			}

			// Build owners drop-down for assigning wishes
			$wish->set('assignlist', $this->userSelect('assigned', $wishlist->owners('individuals'), $wish->get('assigned'), 1));

			// Do we have a due date?
			$wish->set('urgent', 0);
			if ($wish->get('due') != '0000-00-00 00:00:00')
			{
				$delivery = $this->convertTime($wish->get('average_effort'));
				if ($wish->get('due') < $delivery['warning'])
				{
					$wish->set('urgent', 1);
				}
				if ($wish->get('due') < $delivery['immediate'])
				{
					$wish->set('urgent', 2);
				}
			}

			// check available user funds
			if ($action == 'addbonus' && $this->banking)
			{
				$BTL 		= new \Hubzero\Bank\Teller($this->database, $this->juser->get('id'));
				$balance 	= $BTL->summary();
				$credit 	= $BTL->credit_summary();
				$funds 		= $balance - $credit;
				$funds 		= ($funds > 0) ? $funds : '0';
				$wish->set('funds', $funds);
			}

			if ($action == 'move')
			{
				$wish->set('cats', $this->config->get('categories', 'general, resource'));
			}

			// Record some extra actions
			$wish->set('action', $action);
			$wish->set('saved', $saved);
			$wish->set('com', $com);
		//}

		// Turn on/off banking
		$wishlist->set('banking', ($wishlist->get('category') == 'user' ? 0 : $this->banking));

		if (!$wishlist->isPublic() && !$wish->get('admin'))
		{
			$this->view->setLayout('private');
		}

		$this->view->title      = $this->_title;
		$this->view->config     = $this->config;
		$this->view->admin      = $this->_admin;
		$this->view->juser      = $this->juser;
		$this->view->wishlist   = $wishlist;
		$this->view->wish       = $wish;
		$this->view->filters    = $filters;

		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}

	/**
	 * Save wishlist settings
	 *
	 * @return     void
	 */
	public function savesettingsTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$listid  = JRequest::getInt('listid', 0);
		$action  = JRequest::getVar('action', '');

		// Make sure we have list id
		if (!$listid)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		$wishlist = WishlistModelWishlist::getInstance($listid);

		if (!$wishlist->access('manage'))
		{
			JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
			return;
		}

		// Deeleting a user/group
		if ($action == 'delete')
		{
			$user  = JRequest::getInt('user', 0);
			$group = JRequest::getInt('group', 0);

			if ($user)
			{
				$wishlist->remove('individuals', $user);
			}
			else if ($group)
			{
				$wishlist->remove('group', $group);
			}

			// update priority on all wishes
			$wishlist->rank();

			$this->setRedirect(
				JRoute::_($wishlist->link('', '&saved=1'))
			);
			return;
		}

		if (!$wishlist->bind(JRequest::getVar('fields', array(), 'post')))
		{
			JError::raiseError(500, $obj->getError());
			return;
		}

		// store new content
		if (!$wishlist->store())
		{
			JError::raiseError(500, $wishlist->getError());
			return;
		}

		// Save new owners
		if ($newowners = JRequest::getVar('newowners', '', 'post'))
		{
			$wishlist->add('individuals', $newowners);
		}
		if ($newadvisory = JRequest::getVar('newadvisory', '', 'post'))
		{
			$wishlist->add('advisory', $newadvisory);
		}
		if ($newgroups = JRequest::getVar('newgroups', '', 'post'))
		{
			$wishlist->add('groups', $newgroups);
		}

		// update priority on all wishes
		$wishlist->rank();

		$this->setRedirect(
			JRoute::_($wishlist->link('', '&saved=1'))
		);
	}

	/**
	 * Display wishlist settings
	 *
	 * @return     void
	 */
	public function settingsTask()
	{
		// get list id
		$id  = JRequest::getInt('id', 0);

		$wishlist = new WishlistModelWishlist($id);

		if (!$wishlist->exists())
		{
			// list not found
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// Get List Title
		$this->_list_title = $wishlist->get('title');

		if (!$wishlist->isPublic() && !$wishlist->access('manage'))
		{
			$this->_list_title = '';
		}
		$this->_buildTitle();

		// Set the pathway
		$this->_listid = $id;
		$this->_buildPathway($wishlist);

		// Login required
		if ($this->juser->get('guest'))
		{
			$this->_msg = JText::_('COM_WISHLIST_WARNING_LOGIN_MANAGE_SETTINGS');
			$this->loginTask();
			return;
		}

		$this->view->title    = $this->_title;
		$this->view->juser    = $this->juser;
		$this->view->wishlist = $wishlist;
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}

	/**
	 * Save a wish's implementation plan
	 *
	 * @return     void
	 */
	public function saveplanTask()
	{
		$wishid = JRequest::getInt('wishid', 0);

		// Make sure we have wish id
		if (!$wishid)
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		$objWish = new WishlistModelWish($wishid);
		if (!$objWish->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		$wishlist = WishlistModelWishlist::getInstance($objWish->get('wishlist'));
		if (!$wishlist->exists())
		{
			// list not found
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// Get List Title
		$this->_list_title = $wishlist->get('title');

		// Login required
		if ($this->juser->get('guest'))
		{
			// Set page title
			$this->_list_title = $wishlist->get('title');
			if (!$wishlist->isPublic() && !$wishlist->access('manage'))
			{
				$this->_list_title = '';
			}
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);
			$this->loginTask();
			return;
		}

		$pageid = JRequest::getInt('pageid', 0, 'post');

		// Initiate extended database class
		$page = new WishlistModelPlan($pageid);
		$old  = new WishlistModelPlan($pageid);

		$page->set('version', JRequest::getInt('version', 1, 'post'));

		$create_revision = JRequest::getInt('create_revision', 0, 'post');
		if ($create_revision)
		{
			$page->set('id', 0);
			$page->set('version', $old->get('version') + 1);
		}

		$page->set('wishid', $wishid);
		$page->set('created_by', JRequest::getInt('created_by', $this->juser->get('id'), 'post'));
		$page->set('created', JFactory::getDate()->toSql());
		$page->set('approved', 1);
		$page->set('pagetext', JRequest::getVar('pagetext', '', 'post', 'none'));

		// Stripslashes just to make sure
		$oldpagetext = rtrim(stripslashes($old->get('pagetext')));
		$newpagetext = rtrim(stripslashes($page->get('pagetext')));

		// Compare against previous revision
		// We don't want to create a whole new revision if just the tags were changed
		if ($oldpagetext != $newpagetext or (!$create_revision && $pageid))
		{
			$page->set('pagehtml', $page->content('parsed'));

			// Store content
			if (!$page->store())
			{
				JError::raiseError(500, $page->getError());
				return;
			}
		}

		// do we have a due date?
		$isdue  = JRequest::getInt('isdue', 0);
		$due    = JRequest::getVar('publish_up', '');

		if ($due)
		{
			$publishtime = $due . ' 00:00:00';
			$due = JFactory::getDate(strtotime($publishtime))->toSql();
		}

		//is this wish assigned to anyone?
		$assignedto = JRequest::getInt('assigned', 0);

		$new_assignee = ($assignedto && $objWish->get('assigned') != $assignedto) ? 1 : 0;

		$objWish->set('due', ($due ? $due : '0000-00-00 00:00:00'));
		$objWish->set('assigned', ($assignedto ? $assignedto : 0));

		// store our due date
		if (!$objWish->store())
		{
			JError::raiseError(500, $objWish->getError());
			return;
		}
		else if ($new_assignee)
		{
			// Build e-mail components
			$jconfig = JFactory::getConfig();
			$admin_email = $jconfig->getValue('config.mailfrom');

			// to wish assignee
			$subject = JText::_(strtoupper($this->_name)) . ', ' . JText::_('COM_WISHLIST_WISH') . ' #' . $wishid . ' ' . JText::_('COM_WISHLIST_MSG_HAS_BEEN_ASSIGNED_TO_YOU');

			$from = array(
				'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name)),
				'email' => $jconfig->getValue('config.mailfrom')
			);

			$name  = $objWish->proposer('name', JText::_('COM_WISHLIST_UNKNOWN'));
			$login = $objWish->proposer('username', JText::_('COM_WISHLIST_UNKNOWN'));
			if ($objWish->get('anonymous'))
			{
				$name  = JText::_('COM_WISHLIST_ANONYMOUS');
				$login = JText::_('COM_WISHLIST_ANONYMOUS');
			}

			$message  = '----------------------------'."\r\n";
			$message .= JText::_('COM_WISHLIST_WISH').' #'.$objWish->get('id').', '.$this->_list_title.' '.JText::_('WISHLIST')."\r\n";
			$message .= JText::_('COM_WISHLIST_WISH_DETAILS_SUMMARY').': '.stripslashes($objWish->get('subject'))."\r\n";
			$message .= JText::_('COM_WISHLIST_PROPOSED_ON').' '.$objWish->proposed();
			$message .= ' '.JText::_('COM_WISHLIST_BY').' '.$name.' ';
			$message .= $objWish->get('anonymous') ? '' : '('.$login.')';
			$message .= "\r\n\r\n";

			$message .= '----------------------------'."\r\n";
			$url = rtrim(JURI::base(), '/') . '/' . ltrim(JRoute::_($objWish->link()), '/');
			$message  .= JText::_('GO_TO').' '.$url.' '.JText::_('COM_WISHLIST_TO_VIEW_YOUR_ASSIGNED_WISH').'.';

			JPluginHelper::importPlugin('xmessage');
			$dispatcher = JDispatcher::getInstance();

			if (!$dispatcher->trigger('onSendMessage', array('wishlist_wish_assigned', $subject, $message, $from, array($objWish->get('assigned')), $this->_option)))
			{
				$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_ASSIGNEE'));
			}
		}

		$this->setRedirect(
			JRoute::_($objWish->link('plan'))
		);
	}

	/**
	 * Display a form for creating a wish
	 *
	 * @return     void
	 */
	public function addwishTask()
	{
		// Incoming
		$wishid   = JRequest::getInt('wishid', 0);
		$listid   = JRequest::getInt('id', 0);
		$refid    = JRequest::getInt('rid', 0);
		$category = JRequest::getVar('category', '');

		$wish = new WishlistModelWish($wishid);

		if (!$listid && $refid)
		{
			if (!$category)
			{
				JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
				return;
			}

			$wishlist = WishlistModelWishlist::getInstance($refid, $category);
			if (!$wishlist->exists())
			{
				$wishlist->set('category', $category);
				$wishlist->set('referenceid', $refid);
				$wishlist->setup();
			}
		}
		else
		{
			$wishlist = WishlistModelWishlist::getInstance($listid);
		}

		if (!$wishlist->exists())
		{
			// list not found
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// Build page title
		$this->_list_title = ($wishlist->isPublic() or (!$wishlist->isPublic() && $wishlist->access('manage'))) ? $wishlist->get('title') : '';
		$this->_buildTitle();

		// Set the pathway
		$this->_taskpath = $wish->exists()
							? $wish->link('edit')
							: 'index.php?option=' . $this->_option . '&task=add&category=' . $category . '&rid=' . $refid;
		$this->_taskname = $wish->exists()
							? JText::_('COM_WISHLIST_EDITWISH')
							: JText::_('COM_WISHLIST_ADD');
		$this->_buildPathway($wishlist);

		// Login required
		if ($this->juser->get('guest'))
		{
			$this->_msg = JText::_('COM_WISHLIST_WARNING_WISHLIST_LOGIN_TO_ADD');
			$this->loginTask();
			return;
		}

		// get admin priviliges
		if (!$wishlist->isPublic() && !$wishlist->access('manage'))
		{
			JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
			return;
		}

		// Get some defaults
		if (!$wish->exists())
		{
			$wish->set('proposed_by', $this->juser->get('id'));
			$wish->set('status', 0);
			$wish->set('anonymous', 0);
			$wish->set('private', 0);
			$wish->set('wishlist', $wishlist->get('id'));
			$wish->set('category', $wishlist->get('category'));
			$wish->set('referenceid', $wishlist->get('referenceid'));
		}

		// do not allow points for individual wish lists
		$this->banking = $wishlist->get('category') == 'user' ? 0 : $this->banking;

		// Is banking turned on?
		$funds = 0;
		if ($this->banking)
		{
			$BTL = new \Hubzero\Bank\Teller($this->database, $this->juser->get('id'));
			$balance = $BTL->summary();
			$credit  = $BTL->credit_summary();
			$funds   = $balance - $credit;
			$funds   = ($funds > 0) ? $funds : '0';
		}

		// Output HTML
		$this->view->title    = $this->_title;
		$this->view->config   = $this->config;
		$this->view->admin    = $this->_admin;
		$this->view->juser    = $this->juser;
		$this->view->wishlist = $wishlist;
		$this->view->wish     = $wish;

		// Get URL to page explaining virtual economy
		$aconfig = JComponentHelper::getParams('com_answers');
		$this->view->infolink = $aconfig->get('infolink', JURI::base(true) . '/kb/points/');
		$this->view->funds    = $funds;
		$this->view->banking  = $this->banking;

		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->setLayout('editwish')->display();
	}

	/**
	 * Save chanegs to a wish
	 *
	 * @return     void
	 */
	public function savewishTask()
	{
		$listid = JRequest::getInt('wishlist', 0);
		$wishid = JRequest::getInt('id', 0);
		$reward = JRequest::getVar('reward', '');
		$funds  = JRequest::getVar('funds', '0');
		$tags   = JRequest::getVar('tags', '');

		// Login required
		if ($this->juser->get('guest'))
		{
			$this->_msg = JText::_('COM_WISHLIST_WARNING_WISHLIST_LOGIN_TO_ADD');
			$this->loginTask();
			return;
		}

		// Get wish list info
		$wishlist = WishlistModelWishlist::getInstance($listid);
		if (!$wishlist->exists())
		{
			// list not found
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// trim and addslashes all posted items
		$_POST = array_map('trim', $_POST);

		// initiate class and bind posted items to database fields
		$row = new WishlistModelWish($_POST);

		// If we are editing
		$by = JRequest::getVar('by', '', 'post');
		if ($by)
		{
			$ruser = JUser::getInstance($by);
			if (is_object($ruser))
			{
				$row->set('proposed_by', $ruser->get('id'));
			}
			else
			{
				$this->setError(JText::_('COM_WISHLIST_ERROR_INVALID_USER_NAME'));
			}
		}

		// If offering a reward, do some checks
		if ($reward)
		{
			// Is it an actual number?
			if (!is_numeric($reward))
			{
				$this->setError(JText::_('COM_WISHLIST_ERROR_INVALID_AMOUNT'));
			}
			// Are they offering more than they can afford?
			if ($reward > $funds)
			{
				$this->setError(JText::_('COM_WISHLIST_ERROR_NO_FUNDS'));
			}
		}

		// Error view
		if ($this->getError())
		{
			// Set the pathway
			$this->_taskpath = $wishid
							? 'index.php?option=' . $this->_option . '&task=editwish&category='
							. $wishlist->get('category') . '&rid=' . $wishlist->get('referenceid') . '&wishid=' . $wishid
							: 'index.php?option=' . $this->_option . '&task=add&category='
							. $wishlist->get('category') . '&rid=' . $wishlist->get('referenceid');
			$this->_taskname = $wishid
								? JText::_('COM_WISHLIST_EDITWISH')
								: JText::_('COM_WISHLIST_ADD');
			$this->_buildPathway($wishlist);

			// Get URL to page explaining virtual economy
			$aconfig = JComponentHelper::getParams('com_answers');
			$infolink = $aconfig->get('infolink', JURI::base(true) . '/kb/points/');

			$this->view->title    = JText::_(strtoupper($this->_name));
			$this->view->config   = $this->config;
			$this->view->admin    = $this->_admin;
			$this->view->juser    = $this->juser;
			$this->view->wishlist = $wishlist;
			$this->view->wish     = $row;
			$this->view->infolink = $infolink;
			$this->view->funds    = $funds;
			$this->view->banking  = $this->banking;
			$this->view->setError($this->getError());
			$this->view->setLayout('editwish')->display();
			return;
		}

		$row->set('anonymous', JRequest::getInt('anonymous', 0));
		$row->set('private', JRequest::getInt('private', 0));
		$row->set('about', \Hubzero\Utility\Sanitize::clean($row->get('about')));
		$row->set('proposed', ($wishid ? $row->get('proposed') : JFactory::getDate()->toSql()));

		// store new content
		if (!$row->store(true))
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Add/change the tags
		$row->tag($tags);

		// send message about a new wish
		if (!$wishid)
		{
			// Build e-mail components
			$jconfig = JFactory::getConfig();
			$admin_email = $jconfig->getValue('config.mailfrom');

			// Get author name
			$name  = $row->proposer('name', JText::_('COM_WISHLIST_UNKNOWN'));
			$login = $row->proposer('username', JText::_('COM_WISHLIST_UNKNOWN'));

			if ($row->get('anonymous'))
			{
				$name  = JText::_('COM_WISHLIST_ANONYMOUS');
				$login = JText::_('COM_WISHLIST_ANONYMOUS');
			}

			$this->_list_title = $wishlist->get('title');

			$subject = JText::_(strtoupper($this->_name)).', '.JText::_('COM_WISHLIST_NEW_WISH').' '.JText::_('COM_WISHLIST_FOR').' '. $this->_list_title.' '.JText::_('from').' '.$name;
			$from = array(
				'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name)),
				'email' => $jconfig->getValue('config.mailfrom')
			);

			$message  = '----------------------------'."\r\n";
			$message .= JText::_('COM_WISHLIST_WISH').' #'.$row->get('id').', '.$this->_list_title.' '.JText::_('COM_WISHLIST_WISHLIST')."\r\n";
			$message .= JText::_('COM_WISHLIST_WISH_DETAILS_SUMMARY').': '.stripslashes($row->get('subject'))."\r\n";
			$message .= JText::_('COM_WISHLIST_PROPOSED_ON') . ' ' . $row->proposed();
			$message .= ' '.JText::_('COM_WISHLIST_BY').' '.$name.' ';
			$message .= $row->get('anonymous') ? '' : '('.$login.')';
			$message .= "\r\n";
			$message .= '----------------------------'."\r\n\r\n";
			if (!$wishid)
			{
				$message .= html_entity_decode(strip_tags($row->content('raw')), ENT_COMPAT, 'UTF-8');
				$message .= "\r\n\r\n";
			}

			$url = rtrim(JURI::base(), '/') . '/' . ltrim(JRoute::_($row->link()), '/');
			$message .= JText::_('COM_WISHLIST_GO_TO').' '.$url.' '.JText::_('COM_WISHLIST_TO_VIEW_THIS_WISH').'.';

			JPluginHelper::importPlugin('xmessage');
			$dispatcher = JDispatcher::getInstance();

			if (!$dispatcher->trigger('onSendMessage', array('wishlist_new_wish', $subject, $message, $from, $wishlist->owners('individuals'), $this->_option)))
			{
				$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MESSAGE_OWNERS'));
			}
		}

		if ($reward && $this->banking)
		{
			// put the  amount on hold
			$BTL = new \Hubzero\Bank\Teller($this->database, $this->juser->get('id'));
			$BTL->hold($reward, JText::_('COM_WISHLIST_BANKING_HOLD') . ' #' . $row->get('id') . ' ' . JText::_('COM_WISHLIST_FOR') . ' ' . $this->_list_title, 'wish', $row->get('id'));
		}

		$saved = $wishid ? 2 : 3;
		$this->setRedirect(
			JRoute::_($row->link('permalink', array('saved' => $saved)))
		);
	}

	/**
	 * Show a form for editing a wish
	 *
	 * @return     void
	 */
	public function editwishTask()
	{
		$refid  = JRequest::getInt('rid', 0);
		$cat    = JRequest::getVar('category', '');
		$status = JRequest::getVar('status', '');
		$vid    = JRequest::getInt('vid', 0);

		// Check if wish exists on this list
		if ($id = JRequest::getInt('id', 0))
		{
			$wishlist = WishlistModelWishlist::getInstance(JRequest::getInt('id', 0));
		}
		else
		{
			$wishlist = WishlistModelWishlist::getInstance($refid, $cat);
		}
		if (!$wishlist->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// load wish
		$wish = new WishlistModelWish(JRequest::getInt('wishid', 0));
		if (!$wish->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		$changed = false;

		// Login required
		if ($this->juser->get('guest'))
		{
			// Set page title
			$this->_list_title = ($wishlist->isPublic() or (!$wishlist->isPublic() && $wishlist->get('admin') == 2)) ? $wishlist->get('title') : '';
			$this->_buildTitle();

			// Set the pathway
			$this->_taskpath = $wish->link();
			$this->_taskname = JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
			$this->_buildPathway($wishlist);
			$this->loginTask();
			return;
		}

		if (!$wishlist->access('manage') && $wish->get('proposed_by') != $this->juser->get('id'))
		{
			JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
			return;
		}

		if ($this->_task == 'editprivacy')
		{
			$private = JRequest::getInt('private', 0, 'get');
			if ($wish->get('private') != $private)
			{
				$wish->set('private', $private);
				$changed = true;
			}
		}

		if ($this->_task == 'editwish' && ($status = JRequest::getVar('status', '')))
		{
			$former_status   = $wish->get('status');
			$former_accepted = $wish->get('accepted');
			switch ($status)
			{
				case 'pending':
					$wish->set('status', 0);
					$wish->set('accepted', 0);
				break;

				case 'accepted':
					$wish->set('status', 0);
					$wish->set('accepted', 1);
					$wish->set('assigned', $this->juser->get('id')); // assign to person who accepted the wish
				break;

				case 'rejected':
					$wish->set('accepted', 0);
					$wish->set('status', 3);

					// return bonuses
					if ($this->banking)
					{
						$WE = new WishlistEconomy($this->database);
						$WE->cleanupBonus($wish->get('id'));
					}
				break;

				case 'granted':
					$wish->set('status', 1);
					$wish->set('granted', JFactory::getDate()->toSql());
					$wish->set('granted_by', $this->juser->get('id'));
					$wish->set('granted_vid', ($vid ? $vid : 0));

					$objWish = new Wish($this->database);
					$w = $objWish->get_wish($wish->get('id'), $this->juser->get('id'));
					$wish->set('points', $w->bonus);

					if ($this->banking)
					{
						// Distribute bonus and earned points
						$WE = new WishlistEconomy($this->database);
						$WE->distribute_points($wish->get('id'));
					}
				break;
			}

			if ($former_status != $wish->get('status')
			 or $former_accepted != $wish->get('accepted'))
			{
				$changed = true;
			}

			if ($changed)
			{
				// Build e-mail components
				$jconfig = JFactory::getConfig();
				//$admin_email = $jconfig->getValue('config.mailfrom');

				// to wish author
				$subject1 = JText::_(strtoupper($this->_name)) . ', ' . JText::_('COM_WISHLIST_YOUR_WISH') . ' #' . $wish->get('id') . ' is ' . $status;

				// to wish assignee
				$subject2 = JText::_(strtoupper($this->_name)) . ', ' . JText::_('COM_WISHLIST_WISH') . ' #' . $wish->get('id') . ' ' . JText::_('COM_WISHLIST_HAS_BEEN') . ' ' . JText::_('COM_WISHLIST_MSG_ASSIGNED_TO_YOU');

				$from = array(
					'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name)),
					'email' => $jconfig->getValue('config.mailfrom')
				);

				if ($wish->get('anonymous'))
				{
					$name  = JText::_('COM_WISHLIST_ANONYMOUS');
					$login = JText::_('COM_WISHLIST_ANONYMOUS');
				}
				else
				{
					$name  = $wish->proposer('name', JText::_('COM_WISHLIST_UNKNOWN'));
					$login = $wish->proposer('username', JText::_('COM_WISHLIST_UNKNOWN'));
				}

				$message  = '----------------------------'."\r\n";
				$message .= JText::_('COM_WISHLIST_WISH').' #'.$wish->get('id').', '.$wishlist->get('title').' '.JText::_('COM_WISHLIST')."\r\n";
				$message .= JText::_('COM_WISHLIST_WISH_DETAILS_SUMMARY').': '.stripslashes($wish->get('subject'))."\r\n";
				$message .= JText::_('COM_WISHLIST_PROPOSED_ON').' '. $wish->proposed();
				$message .= ' '.JText::_('COM_WISHLIST_BY').' '.$name.' ';
				$message .= $wish->get('anonymous') ? '' : '('.$login.')';
				$message .= "\r\n\r\n";
				$message .= '----------------------------'."\r\n";
				$as_mes = $message;
				if ($status!='pending')
				{
					$message .= JText::_('COM_WISHLIST_YOUR_WISH').' '.JText::_('COM_WISHLIST_HAS_BEEN').' '.$status.' '.JText::_('COM_WISHLIST_BY_LIST_ADMINS').'.'."\r\n";
				}
				else
				{
					$message .= JText::_('COM_WISHLIST_MSG_WISH_STATUS_CHANGED_TO').' '.$status.' '.JText::_('COM_WISHLIST_BY_LIST_ADMINS').'.'."\r\n";
				}
				$url = rtrim(JURI::base(), '/') . JRoute::_($wish->link());
				$message .= JText::_('COM_WISHLIST_GO_TO').' '.$url.' '.JText::_('COM_WISHLIST_TO_VIEW_YOUR_WISH').'.';
				$as_mes  .= JText::_('COM_WISHLIST_GO_TO').' '.$url.' '.JText::_('COM_WISHLIST_TO_VIEW_YOUR_ASSIGNED_WISH').'.';
			}
		}
		// no status change, only information
		else if ($this->_task == 'editwish')
		{
			$this->addwishTask($wish->get('id'));
			return;
		}

		if ($changed)
		{
			// save changes
			if (!$wish->store())
			{
				JError::raiseError(500, $wish->getError());
				return;
			}
			else if ($this->_task == 'editwish')
			{
				JPluginHelper::importPlugin('xmessage');
				$dispatcher = JDispatcher::getInstance();

				if (!$dispatcher->trigger('onSendMessage', array('wishlist_status_changed', $subject1, $message, $from, array($wish->get('proposed_by')), $this->_option)))
				{
					$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_AUTHOR'));
				}

				if ($wish->get('assigned')
				 && $wish->get('proposed_by') != $wish->get('assigned')
				 && $status == 'accepted')
				{
					if (!$dispatcher->trigger('onSendMessage', array('wishlist_wish_assigned', $subject2, $as_mes, $from, array($wish->get('assigned')), $this->_option)))
					{
						$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_ASSIGNEE'));
					}
				}
			}
		}

		$this->setRedirect(
			JRoute::_($wish->link())
		);
	}

	/**
	 * Move a wish
	 *
	 * @return     void
	 */
	public function movewishTask()
	{
		$listid   = JRequest::getInt('wishlist', 0);
		$wishid   = JRequest::getInt('wish', 0);
		$category = JRequest::getVar('type', '');
		$refid    = JRequest::getInt('resource', 0);

		// some transfer options
		$options = array();
		$options['keepplan']     = JRequest::getInt('keepplan', 0);
		$options['keepcomments'] = JRequest::getInt('keepcomments', 0);
		$options['keepstatus']   = JRequest::getInt('keepstatus', 0);
		$options['keepfeedback'] = JRequest::getInt('keepfeedback', 0);

		// missing wish id
		if (!$wishid)
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		// missing or invalid resource ID
		if ($category == 'general')
		{
			$refid = 1; // default to main wish list
		}

		if ($category == 'question' or $category == 'ticket')
		{
			// move to a question or a ticket
			JPluginHelper::importPlugin('support' , 'transfer');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('transferItem', array(
					'wish',
					$wishid,
					$category,
					$options
				)
			);
		}
		else
		{
			// moving to another list
			$oldlist = WishlistModelWishlist::getInstance($listid);

			// Where do we put this wish?
			$newlist = WishlistModelWishlist::getInstance($refid, $category);
			if (!$newlist->exists())
			{
				// Create wishlist for resource if doesn't exist
				if (!$newlist->setup())
				{
					JError::raiseError(500, $newlist->getError());
					return;
				}
			}

			// cannot add a wish to a non-found list
			if (!$newlist->exists())
			{
				JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
				return;
			}

			if ($listid != $newlist->get('id'))
			{
				// Transfer wish
				$wish = new WishlistModelWish($wishid);
				$wish->set('wishlist', $newlist);
				$wish->set('assigned', 0); // moved wish is not assigned to anyone yet
				$wish->set('ranking', 0); // zero ranking
				$wish->set('due', '0000-00-00 00:00:00');

				// renew state if option chosen
				if (!$options['keepstatus'])
				{
					$wish->set('status', 0);
					$wish->set('accepted', 0);
				}

				if (!$wish->store())
				{
					JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_WISH_MOVE_FAILED'));
					return;
				}

				// also delete all previous owner votes for this wish
				if (!$wish->purge('rankings'))
				{
					JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_WISH_MOVE_FAILED'));
					return;
				}

				// delete plan if option chosen
				if (!$options['keepplan'])
				{
					if (!$wish->purge('plan'))
					{
						JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_WISH_MOVE_FAILED'));
						return;
					}
				}

				// delete comments if option chosen
				if (!$options['keepcomments'])
				{
					if (!$wish->purge('comments'))
					{
						JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_WISH_MOVE_FAILED'));
						return;
					}
				}

				// delete community votes if option chosen
				if (!$options['keepfeedback'])
				{
					if (!$wish->purge('votes'))
					{
						JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_WISH_MOVE_FAILED'));
						return;
					}
				}

					// send message about transferred wish
				$jconfig = JFactory::getConfig();

				$oldtitle = $oldlist->get('title'); //$objWishlist->getTitle($listid);
				$newtitle = $newlist->get('title'); //$objWishlist->getTitle($newlist);

				$name  = $wish->proposer('name', JText::_('COM_WISHLIST_UNKNOWN'));
				$login = $wish->proposer('username', JText::_('COM_WISHLIST_UNKNOWN'));

				if ($wish->get('anonymous'))
				{
					$name = JText::_('COM_WISHLIST_ANONYMOUS');
				}

				$subject1 = JText::_(strtoupper($this->_name)).', '.JText::_('COM_WISHLIST_NEW_WISH').' '.JText::_('COM_WISHLIST_FOR').' '.$newtitle.' '.JText::_('COM_WISHLIST_FROM').' '.$name.' - '.JText::_('COM_WISHLIST_TRANSFERRED');
				$subject2 = JText::_(strtoupper($this->_name)).', '.JText::_('COM_WISHLIST_YOUR_WISH').' #'.$wishid.' '.JText::_('COM_WISHLIST_WISH_TRANSFERRED_TO_DIFFERENT_LIST');

				$from = array(
					'name'  => $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name)),
					'email' => $jconfig->getValue('config.mailfrom')
				);

				$message  = '----------------------------' . "\r\n";
				$message .= JText::_('COM_WISHLIST_WISH').' #'.$wishid.', '.$newtitle.' '.JText::_('COM_WISHLIST') . "\r\n";
				$message .= JText::_('COM_WISHLIST_WISH_DETAILS_SUMMARY').': '.stripslashes($wish->get('subject')) . "\r\n";
				$message .= JText::_('COM_WISHLIST_PROPOSED_ON').' '.$wish->proposed();
				$message .= ' '.JText::_('COM_WISHLIST_BY').' '.$name.' ';
				$message .= $wish->get('anonymous') ? '' : '('.$login.')' . "\r\n";
				$message .= JText::_('COM_WISHLIST_WISH_TRANSFERRED_FROM_WISHLIST').' "'.$oldtitle.'"';
				$message .= "\r\n\r\n";
				$message .= '----------------------------' . "\r\n";
				$message .= JText::_('COM_WISHLIST_GO_TO').' '. rtrim(JURI::base(), '/') . JRoute::_($wish->link()) .' '.JText::_('COM_WISHLIST_TO_VIEW_THIS_WISH').'.';

				JPluginHelper::importPlugin('xmessage');
				$dispatcher = JDispatcher::getInstance();

				if (!$dispatcher->trigger('onSendMessage', array('wishlist_new_wish', $subject1, $message, $from, $newlist->owners('individuals'), $this->_option)))
				{
					$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MESSAGE_OWNERS'));
				}

				if (!$dispatcher->trigger('onSendMessage', array('support_item_transferred', $subject2, $message, $from, array($wish->get('proposed_by')), $this->_option)))
				{
					$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_AUTHOR'));
				}
			}

			if ($listid == $newlist->get('id'))
			{
				// nothing changed
				$this->_task = 'wishlist';
			}
		} // end if move within Wish List component

		// go back to wishlist
		$this->wishlistTask();
	}

	/**
	 * Assign a point bonus to a wish
	 *
	 * @return     void
	 */
	public function addbonusTask()
	{
		//$listid = JRequest::getInt('wishlist', 0);
		$wishid = JRequest::getInt('wish', 0);
		$amount = JRequest::getInt('amount', 0);

		// missing wish id
		/*if (!$wishid or !$listid)
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}*/

		//$objWishlist = new Wishlist($this->database);
		//$objWish = new Wish($this->database);

		$wishlist = new WishlistModelWishlist(JRequest::getInt('wishlist', 0));
		if (!$wishlist->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		$wish = new WishlistModelWish(JRequest::getInt('wish', 0));
		if (!$wish->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		// Login required
		if ($this->juser->get('guest'))
		{
			// Set page title
			/*$this->_list_title =(isset($wishlist->resource) && $wishlist->resource->type=='7'  && isset($wishlist->resource->alias))
						? 'tool "' . $wishlist->resource->alias . '"'
						: $wishlist->get('title');*/
			if (!$wishlist->isPublic() && !$wishlist->access('manage'))
			{
				$this->_list_title = '';
			}
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);
			$this->login();
			return;
		}

		// check available user funds
		$BTL = new \Hubzero\Bank\Teller($this->database, $this->juser->get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance - $credit;
		$funds   = ($funds > 0) ? $funds : '0';

		// missing amount
		if ($amount == 0)
		{
			JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_INVALID_AMOUNT'));
			return;
		}
		if ($amount < 0)
		{
			JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_NEGATIVE_BONUS'));
			return;
		}
		else if ($amount > $funds)
		{
			JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_NO_FUNDS'));
			return;
		}

		// put the  amount on hold
		$BTL = new \Hubzero\Bank\Teller($this->database, $this->juser->get('id'));
		$BTL->hold(
			$amount,
			JText::_('COM_WISHLIST_BANKING_HOLD') . ' #' . $wish->get('id') . ' ' . JText::_('COM_WISHLIST_FOR') . ' ' . $wishlist->get('title'),
			'wish',
			$wish->get('id')
		);

		$this->setRedirect(
			JRoute::_($wish->link())
		);
	}

	/**
	 * Mark a wish as deleted
	 *
	 * @return     void
	 */
	public function deletewishTask()
	{
		// Check if wish exists on this list
		$wishlist = new WishlistModelWishlist(
			JRequest::getInt('rid', 0),
			JRequest::getVar('category', '')
		);
		if (!$wishlist->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND_ON_LIST'));
			return;
		}

		$wish = new WishlistModelWish(JRequest::getInt('wishid', 0));
		if (!$wish->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		// Login required
		if ($this->juser->get('guest'))
		{
			// Set page title
			/*$this->_list_title =(isset($wishlist->resource) && $wishlist->resource->type=='7' && isset($wishlist->resource->alias))
						? 'tool "' . $wishlist->item('alias') . '"'
						: $wishlist->title;*/
			if (!$wishlist->isPublic() && !$wishlist->access('manage'))
			{
				$this->_list_title = '';
			}
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);
			$this->loginTask();
			return;
		}

		// get admin priviliges
		//$this->authorize_admin($wishlist->id);

		//$objWish->load($wishid);
		if (!$wishlist->access('manage') && $wish->get('proposed_by') != $this->juser->get('id'))
		{
			JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
			return;
		}

		//$withdraw = 0; //$this->_task=='withdraw' ? 1 : 0; /* [!] zooley - Mark as deleted instead of withdrawn? Seems to cause confusion if wish still appears in lists. */

		$wish->set('status', 2);
		if ($wish->store()) //$objWish->delete_wish($wishid, $withdraw))
		{
			// also delete all votes for this wish
			/*$objR = new WishRank($this->database);

			if ($objR->remove_vote($wishid))
			{
				// re-calculate rankings of remaining wishes
				$this->listid = $wishlist->id;
				$wishlist->rank();
			}*/

			// return bonuses
			if ($this->banking)
			{
				$WE = new WishlistEconomy($this->database);
				$WE->cleanupBonus($wish->get('id'));
			}
		}
		else
		{
			$this->setError(JText::_('COM_WISHLIST_ERROR_WISH_DELETE_FAILED'));
		}

		// go back to the wishlist
		$this->setRedirect(
			$wishlist->link(),
			$this->getError(),
			($this->getError() ? 'error' : null)
		);
	}

	/**
	 * Save a vote for a wish
	 *
	 * @return     void
	 */
	public function savevoteTask()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		//$this->database =& JFactory::getDBO();
		//$juser =& JFactory::getUser();

		$refid    = JRequest::getInt('rid', 0);
		$category = JRequest::getVar('category', '');

		$wishlist = WishlistModelWishlist::getInstance($refid, $category);
		if (!$wishlist->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		$wishid   = JRequest::getInt('wishid', 0);

		$wish = WishlistModelWish::getInstance($wishid);
		if (!$wish->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND_ON_LIST'));
			return;
		}

		//$objWishlist = new Wishlist($this->database);
		//$objWish = new Wish($this->database);
		//$objR = new WishRank($this->database);

		// figure list id
		/*if ($category && $refid)
		{
			$listid = $objWishlist->get_wishlistID($refid, $category);
		}

		// cannot rank a wish if list/wish is not found
		if (!$listid or !$wishid)
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		$wishlist = $objWishlist->get_wishlist($listid);
		$item = $objWish->get_wish($wishid, $juser->get('id'));

		// cannot proceed if wish id is not found
		if (!$wishlist or !$item)
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// is this wish on correct list?
		if ($listid != $wishlist->id)
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND_ON_LIST'));
			return;
		}*/

		// get vote
		$effort     = JRequest::getVar('effort', '', 'post');
		$importance = JRequest::getVar('importance', '', 'post');

		// Login required
		if ($this->juser->get('guest'))
		{
			// Set page title
			/*$this->_list_title =(isset($wishlist->resource) && $wishlist->resource->type=='7' && isset($wishlist->resource->alias))
						? 'tool "' . $wishlist->resource->alias . '"'
						: $wishlist->title;*/
			if (!$wishlist->isPublic() && !$wishlist->access('manage'))
			{
				$this->_list_title = '';
			}
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);
			$this->_msg = JText::_('COM_WISHLIST_WARNING_LOGIN_TO_RANK');
			$this->loginTask();
			return;
		}

		// get admin priviliges
		//$this->authorize_admin($listid);

		// Need to be list admin
		if (!$wishlist->access('manage'))
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ALERTNOTAUTH_ACTION'));
			return;
		}

		// did user make selections?
		if (!$effort or !$importance)
		{
			$this->setRedirect(
				JRoute::_($wish->link()),
				JText::_('Please be sure to provide both an importance and an effort'),
				'error'
			);
			return;
		}

		// is the wish ranked already?
		/*if (isset($item->ranked) && !$item->ranked)
		{
			$objR->wishid = $wishid;
			$objR->userid = $juser->get('id');
		}
		else
		{
			// edit rating
			$objR->load_vote($juser->get('id'), $wishid);
		}*/
		if (!$wish->rank($effort, $importance))
		{
			$this->setRedirect(
				JRoute::_($wish->link()),
				$wish->getError(),
				'error'
			);
			return;
		}

		// update priority on all wishes
		if (!$wishlist->rank())
		{
			JError::raiseError(500, $wishlist->getError());
			return;
		}

		$this->setRedirect(
			JRoute::_($wish->link())
		);
	}

	/**
	 * Save a wish comment
	 *
	 * @return     void
	 */
	public function savereplyTask()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id       = JRequest::getInt('referenceid', 0);
		$listid   = JRequest::getInt('listid', 0);
		$wishid   = JRequest::getInt('wishid', 0);
		$ajax     = JRequest::getInt('ajax', 0);
		$category = JRequest::getVar('cat', '');
		$when     = JFactory::getDate()->toSql();

		// Get wishlist info
		$wishlist = WishlistModelWishlist::getInstance($listid);

		if (!$wishlist->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		$objWish = new WishlistModelWish($wishid);

		// Get List Title
		$this->_list_title = $wishlist->get('title');

		// Build page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway($wishlist);

		if (!$id && !$ajax)
		{
			// cannot proceed
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		// is the user logged in?
		if ($this->juser->get('guest'))
		{
			$this->_msg = JText::_('COM_WISHLIST_WARNING_LOGIN_TO_ADD_COMMENT');
			$this->loginTask();
			return;
		}

		if ($id && $category)
		{
			$row = new WishlistModelComment();
			if (!$row->bind($_POST))
			{
				JError::raiseError(500, $row->getError());
				return;
			}

			// Perform some text cleaning, etc.
			$row->set(
				'comment',
				(
					$row->get('comment') == JText::_('COM_WISHLIST_ENTER_COMMENTS')
						? ''
						: $row->get('comment')
				)
			);

			if ($attachment = $this->uploadTask($wishid))
			{
				$row->set('comment', $row->get('comment') . "\n" . $attachment);
			}

			$row->set('anonymous', ($row->get('anonymous') ? $row->get('anonymous') : 0));
			$row->set('added', JFactory::getDate()->toSql());
			$row->set('state', 0);
			$row->set('category', $category);
			$row->set('added_by', $this->juser->get('id'));

			// Save the data
			if (!$row->store(true))
			{
				JError::raiseError(500, $row->getError());
				return;
			}

			// Build e-mail components
			$jconfig = JFactory::getConfig();

			$name  = $row->creator('name', JText::_('UNKNOWN'));
			$login = $row->creator('username', JText::_('UNKNOWN'));

			if ($row->get('anonymous'))
			{
				$name = JText::_('ANONYMOUS');
			}

			$subject = JText::_(strtoupper($this->_name)) . ', ' . JText::_('COM_WISHLIST_MSG_COMENT_POSTED_YOUR_WISH') . ' #' . $wishid . ' ' . JText::_('BY') . ' ' . $name;

			// email components
			$from = array(
				'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name)),
				'email' => $jconfig->getValue('config.mailfrom')
			);

			// for the wish owner
			$subject1 = JText::_(strtoupper($this->_name)).', '.$name.' '.JText::_('COM_WISHLIST_MSG_COMMENTED_YOUR_WISH').' #'.$wishid;

			// for the person to whom wish is assigned
			$subject2 = JText::_(strtoupper($this->_name)).', '.$name.' '.JText::_('COM_WISHLIST_MSG_COMMENTED_ON_WISH').' #'.$wishid.' '.JText::_('COM_WISHLIST_MSG_ASSIGNED_TO_YOU');

			// for original commentor
			$subject3 = JText::_(strtoupper($this->_name)).', '.$name.' '.JText::_('COM_WISHLIST_MSG_REPLIED_YOUR_COMMENT').' #'.$wishid;

			// for others included in the conversation thread.
			$subject4 = JText::_(strtoupper($this->_name)).', '.$name.' '.JText::_('COM_WISHLIST_MSG_COMMENTED_AFTER_YOU').' #'.$wishid;

			$message  = JText::_('COM_WISHLIST_WISH') . ' #' . $wishid . ', ' . $wishlist->get('title') . ' ' . JText::_('COM_WISHLIST') . "\r\n";
			$message .= JText::_('COM_WISHLIST_WISH_DETAILS_SUMMARY') . ': ' . stripslashes($objWish->get('subject')) . "\r\n";
			$message .= '----------------------------' . "\r\n";
			$message .= JText::_('COM_WISHLIST_MSG_COMMENT_BY') . ' ' . $name . ' ';
			$message .= $row->get('anonymous') ? '' : '(' . $login . ')';
			$message .= ' ' . JText::_('COM_WISHLIST_MSG_POSTED_ON').' '. $row->created() . ':' . "\r\n";
			$message .= $row->content('clean') . "\r\n";
			$message .= $row->get('attachment') . "\r\n\r\n";
			$message .= "\r\n";
			$message .= '----------------------------' . "\r\n";
			$message .= JText::_('COM_WISHLIST_GO_TO') . ' ' . rtrim(JURI::base(), '/') . '/' . ltrim(JRoute::_($objWish->link()), '/') . ' ' . JText::_('COM_WISHLIST_TO_VIEW_THIS_WISH') . '.';

			JPluginHelper::importPlugin('xmessage');
			$dispatcher = JDispatcher::getInstance();

				// collect ids of people who were already emailed
			$contacted = array();

			if ($objWish->get('proposed_by') != $row->get('added_by'))
			{
				$contacted[] = $objWish->get('proposed_by');

				// send message to wish owner
				if (!$dispatcher->trigger('onSendMessage', array('wishlist_comment_posted', $subject1, $message, $from, array($objWish->get('proposed_by')), $this->_option)))
				{
					$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_AUTHOR'));
				}
			} // -- end send to wish author

			if ($objWish->get('assigned')
			 && $objWish->get('assigned') != $row->get('added_by')
			 && !in_array($objWish->get('assigned'), $contacted))
			{
				$contacted[] = $objWish->get('assigned');

				// send message to person to who wish is assigned
				if (!$dispatcher->trigger('onSendMessage', array('wishlist_comment_posted', $subject2, $message, $from, array($objWish->get('assigned')), $this->_option)))
				{
					$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_ASSIGNEE'));
				}
			} // -- end send message to person to who wish is assigned

			// get comment author if reply is posted to a comment
			if ($category == 'wishcomment')
			{
				$parent = new WishlistModelComment($id);

				// send message to comment author
				if ($parent->get('added_by') != $row->get('added_by')
				 && !in_array($parent->get('added_by'), $contacted))
				{
					$contacted[] = $parent->get('added_by');
					if (!$dispatcher->trigger('onSendMessage', array('wishlist_comment_thread', $subject3, $message, $from, array($parent->get('added_by')), $this->_option)))
					{
						$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_COMMENTOR'));
					}
				}
			}

			// get all users who commented
			$commentors = $objWish->comments('authors');
			$comm = array_diff($commentors, $contacted);

			if (count($comm) > 0)
			{
				if (!$dispatcher->trigger('onSendMessage', array('wishlist_comment_thread', $subject4, $message, $from, $comm, $this->_option)))
				{
					$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_COMMENTOR'));
				}
			}
		} // -- end if id & category

		$this->setRedirect(
			JRoute::_($objWish->link())
		);
	}

	/**
	 * Delete a comment
	 *
	 * @return     void
	 */
	public function deletereplyTask()
	{
		// Incoming
		$row = new WishlistModelComment(
			JRequest::getInt('replyid', 0)
		);

		// Do we have a reply ID?
		if (!$row->exists())
		{
			$this->setError(JText::_('COM_WISHLIST_ERROR_REPLY_NOT_FOUND'));
			return;
		}

		if ($row->get('added_by') != $this->juser->get('id'))
		{
			$this->setRedirect(
				JRequest::getVar('HTTP_REFERER', NULL, 'server'),
				JText::_('COM_WISHLIST_ERROR_CANNOT_DELETE_REPLY'),
				'error'
			);
			return;
		}

		// Delete the comment
		$row->set('state', 4);

		if (!$row->store())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Go back to the page
		$this->setRedirect(
			JRequest::getVar('HTTP_REFERER', NULL, 'server')
		);
	}

	/**
	 * Reply to a comment
	 *
	 * @return     void
	 */
	public function replyTask()
	{
		// is the user logged in?
		if ($this->juser->get('guest'))
		{
			// Get wishlist info
			$wishlist = new WishlistModelWishlist(
				JRequest::getInt('refid', 0),
				JRequest::getVar('cat', '')
			);

			// Set page title
			$this->_list_title = ($wishlist->isPublic() or (!$wishlist->isPublic() && $wishlist->get('admin') == 2)) ? $wishlist->get('title') : '';
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);
			$this->_msg = JText::_('COM_WISHLIST_WARNING_LOGIN_TO_ADD_COMMENT');
			$this->loginTask();
			return;
		}

		$this->wishTask();
	}

	/**
	 * Vote for a wish
	 *
	 * @return     void
	 */
	public function rateitemTask()
	{
		$wish = new WishlistModelWish(
			JRequest::getInt('refid', 1)
		);

		if (!$wish->exists())
		{
			// cannot proceed
			return;
		}

		// Load the wishlist
		$wishlist = WishlistModelWishlist::getInstance($wish->get('wishlist'));

		// Login required
		if ($this->juser->get('guest'))
		{
			// Get List Title
			$this->_list_title = ($wishlist->isPublic() or (!$wishlist->isPublic() && $wishlist->access('manage'))) ? $wishlist->get('title') : '';
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);

			$this->_msg = JText::_('COM_WISHLIST_WARNING_WISHLIST_LOGIN_TO_RATE');
			$this->loginTask();
			return;
		}

		// Incoming
		$page = JRequest::getVar('page', 'wishlist');
		$vote = JRequest::getWord('vote', ''); // assuming text only vote. Fix for sql injection ticket 1182

		//$this->authorize_admin($listid);
		$filters = self::getFilters($wishlist->access('manage'));

		if ($wish->vote($vote))
		{
			$wishlist->rank();
		}

		// update display
		if (JRequest::getInt('ajax', 0))
		{
			$this->view->setLayout('_vote');

			$this->view->item    = $wish;
			$this->view->item->set('vote', $vote);

			$this->view->option  = $this->_option;
			$this->view->page    = 'wishlist';
			$this->view->filters = $filters;
			$this->view->display();
			return;
		}

		if ($page == 'wishlist')
		{
			$this->setRedirect(
				str_replace('&amp;', '&', JRoute::_($wishlist->link() . '&filterby='.$filters['filterby'].'&sortby='.$filters['sortby'].'&limitstart='.$filters['start'].'&limit='.$filters['limit'].'&tags='.$filters['tag']))
			);
		}
		else
		{
			$this->setRedirect(
				str_replace('&amp;', '&', JRoute::_($wish->link() . '&filterby='.$filters['filterby'].'&sortby='.$filters['sortby'].'&limitstart='.$filters['start'].'&limit='.$filters['limit'].'&tags='.$filters['tag']))
			);
		}
	}

	/**
	 * Get an array of filters from the request
	 *
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     array
	 */
	public function getFilters($admin=0)
	{
		// Query filters defaults
		$filters = array();
		$filters['sortby']   = JRequest::getVar('sortby', '');
		$filters['filterby'] = JRequest::getVar('filterby', 'all');
		$filters['search']   = JRequest::getVar('search', '');
		$filters['tag']      = JRequest::getVar('tags', '');

		if ($admin)
		{
			$filters['sortby'] = ($filters['sortby']) ? $filters['sortby'] : 'ranking';
		}
		else
		{
			$default = isset($this->banking) && $this->banking ? 'bonus' : 'date';
			$filters['sortby'] = ($filters['sortby']) ? $filters['sortby'] : $default;
		}

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Paging vars
		$filters['limit']    = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$filters['start']    = JRequest::getInt('limitstart', 0);
		$filters['new']      = JRequest::getInt('newsearch', 0);
		$filters['start']    = $filters['new'] ? 0 : $filters['start'];
		$filters['comments'] = JRequest::getVar('comments', 1, 'get');

		// Return the array
		return $filters;
	}

	/**
	 * Authorize administrator access
	 *
	 * @param      integer $listid Wish list ID
	 * @param      integer $admin  If the use ris an admin
	 * @return     void
	 */
	public function authorize_admin($listid = 0, $admin = 0)
	{
		// Check if they're a site admin (from Joomla)
		if ($this->juser->authorize($this->_option, 'manage'))
		{
			$admin = 1;
		}

		if ($listid)
		{
			$admingroup = $this->config->get('group', 'hubadmin');

			// Get list administrators
			$objOwner = new WishlistOwner($this->database);
			$owners = $objOwner->get_owners($listid,  $admingroup);
			$managers =  $owners['individuals'];
			$advisory =  $owners['advisory'];

			if (!$juser->get('guest'))
			{
				if (in_array($this->juser->get('id'), $managers))
				{
					$admin = 2;  // individual group manager
				}
				if (in_array($this->juser->get('id'), $advisory))
				{
					$admin = 3;  // advisory committee member
				}
			}
		}

		$this->_admin = $admin;
	}

	/**
	 * Build a select list of users
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      array   $ownerids Parameter description (if any) ...
	 * @param      unknown $active Parameter description (if any) ...
	 * @param      integer $nouser Parameter description (if any) ...
	 * @param      string  $javascript Parameter description (if any) ...
	 * @param      string  $order Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function userSelect($name, $ownerids, $active, $nouser=0, $javascript=NULL, $order='a.name')
	{
		$database = JFactory::getDBO();

		$query = "SELECT a.id AS value, a.name AS text"
			  . "\n FROM #__users AS a"
			  . "\n WHERE a.block = '0' ";
		if (count($ownerids) > 0)
		{
			$query .= "AND (a.id IN (";
			$tquery = '';
			foreach ($ownerids as $owner)
			{
				$tquery .= "'" . $owner . "',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);

			$query .= $tquery . ")) ";
		}
		else
		{
			$query .= " AND 2=1 ";
		}
		$query .= "\n ORDER BY " . $order;

		$database->setQuery($query);
		if ($nouser)
		{
			$users[] = JHTML::_('select.option', '', 'No User', 'value', 'text');
			$users = array_merge($users, $database->loadObjectList());
		}
		else
		{
			$users = $database->loadObjectList();
		}

		$users = JHTML::_('select.genericlist', $users, $name, ' ' . $javascript, 'value', 'text', $active, false, false);

		return $users;
	}

	/**
	 * Upload a file
	 *
	 * @param      integer $listdir Wish ID
	 * @return     string
	 */
	public function uploadTask($listdir)
	{
		if (!$listdir)
		{
			$this->setError(JText::_('COM_WISHLIST_ERROR_NO_UPLOAD_DIRECTORY'));
			return '';
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(JText::_('COM_WISHLIST_ERROR_NO_FILE'));
			return '';
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		//make sure that file is acceptable type
		$attachment = new WishlistModelAttachment(array(
			'id'          => 0,
			'description' => JRequest::getVar('description', ''),
			'wish'        => $listdir,
			'filename'    => $file['name']
		));

		// make sure that file is acceptable type
		if (!$attachment->isAllowedType())
		{
			$this->setError(JText::_('ATTACHMENT: Incorrect file type.'));
			return JText::_('ATTACHMENT: Incorrect file type.');
		}

		$path = $attachment->link('dir');

		// Build the path if it doesn't exist
		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				$this->setError(JText::_('COM_WISHLIST_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return 'ATTACHMENT: ' . JText::_('COM_WISHLIST_UNABLE_TO_CREATE_UPLOAD_PATH');
			}
		}

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(JText::_('COM_WISHLIST_ERROR_UPLOADING'));
			return 'ATTACHMENT: ' . JText::_('COM_WISHLIST_ERROR_UPLOADING');
		}
		else
		{
			// Scan for viruses
			$path = $path . DS . $file['name']; //JPATH_ROOT . DS . 'virustest';
			exec("clamscan -i --no-summary --block-encrypted $path", $output, $status);
			if ($status == 1)
			{
				if (JFile::delete($path))
				{
					$this->setError(JText::_('ATTACHMENT: File rejected because the anti-virus scan failed.'));
					return JText::_('ATTACHMENT: File rejected because the anti-virus scan failed.');
				}
			}

			if (!$attachment->store(true))
			{
				$this->setError($attachment->getError());
			}

			return '{attachment#' . $row->id . '}';
		}
	}

	/**
	 * Download an attachment
	 *
	 * @return     void
	 */
	public function downloadTask()
	{
		$file   = JRequest::getVar('file', '');
		$wishid = JRequest::getInt('wishid', 0);

		$wish = new WishlistModelWish($wishid);

		// Ensure we have a path
		if (!$wish->exists() || $wish->isDeleted() || $wish->isWithdrawn())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_FILE_NOT_FOUND'));
			return;
		}

		$attachment = new WishlistModelAttachment($file, $wishid);

		// Ensure we have a path
		if (!$attachment->exists())
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_FILE_NOT_FOUND'));
			return;
		}

		//make sure that file is acceptable type
		if (!$attachment->isAllowedType())
		{
			JError::raiseError(404, JText::_('Unknown file type.'));
			return;
		}

		// Add JPATH_ROOT
		$filename = $attachment->link('file');

		// Ensure the file exist
		if (!file_exists($filename))
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_FILE_NOT_FOUND') . ' ' . $filename);
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($filename);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			JError::raiseError(500, JText::_('COM_WISHLIST_SERVER_ERROR'));
		}
		else
		{
			exit;
		}
		return;
	}

	/**
	 * Convert effort value to a time
	 *
	 * @param      float $rawnum Number to convert
	 * @param      array $due    Array to populate
	 * @return     array
	 */
	public function convertTime($rawnum, $due=array())
	{
		$rawnum = round($rawnum);
		switch ($rawnum)
		{
			case 0:
				$i = (62 * 24 * 60 * 60);
				$w = (120 * 24 * 60 * 60);
			break; // 2 months

			case 1:
				$i = (14 * 24 * 60 * 60);
				$w = (32 * 24 * 60 * 60);
			break; // 2 weeks

			case 2:
				$i = (7 * 24 * 60 * 60);
				$w = (14 * 24 * 60 * 60);
			break; // 1 week

			case 3:
				$i = (2 * 24 * 60 * 60);
				$w = (6 * 24 * 60 * 60);
			break; // 2 days

			case 4:
				$i = (24 * 60 * 60);
				$w = (2 * 24 * 60 * 60);
			 break; // 1 day

			case 5:
				$i = (24 * 60 * 60);
				$w = (2 * 24 * 60 * 60);
			break; // 4 hours
		}

		$due['immediate'] = JFactory::getDate(time() + $i)->toSql();
		$due['warning']   = JFactory::getDate(time() + $w)->toSql();

		return $due;
	}
}
