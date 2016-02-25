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

namespace Components\Wishlist\Site\Controllers;

use Components\Wishlist\Models\Wishlist;
use Components\Wishlist\Models\Wish;
use Components\Wishlist\Models\Attachment;
use Components\Wishlist\Models\Comment;
use Components\Wishlist\Models\Plan;
use Components\Wishlist\Tables;
use Components\Wishlist\Helpers\Economy;
use Hubzero\Component\SiteController;
use Hubzero\Utility\String;
use Hubzero\Content\Server;
use Hubzero\Bank\Teller;
use Exception;
use Component;
use Request;
use Pathway;
use Config;
use Event;
use Lang;
use User;
use Date;
use App;

/**
 * Wishlist controller class
 */
class Wishlists extends SiteController
{
	/**
	 * Determine task and execute
	 *
	 * @return  void
	 */
	public function execute()
	{
		$upconfig = Component::params('com_members');
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
		$this->_title = Lang::txt(strtoupper($this->_option));

		if ($this->_list_title)
		{
			$this->_title .= ' - ' . $this->_list_title;
		}
		if ($this->_task && in_array($this->_task, array('settings', 'add')))
		{
			$this->_title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		\Document::setTitle($this->_title);
	}

	/**
	 * Build the breadcrumbs
	 *
	 * @param      object $wishlist Wishlist
	 * @return     void
	 */
	protected function _buildPathway($wishlist)
	{
		Pathway::clear();

		$comtitle  = Lang::txt(strtoupper($this->_option));
		$comtitle .= $this->_list_title ? ' - ' . $this->_list_title : '';

		$wishlist->pathway();

		if ($this->_task)
		{
			switch ($this->_task)
			{
				case 'wish':
					Pathway::append(
						$this->_wishtitle,
						$this->_wishpath
					);
				break;
				case 'add':
				case 'savewish':
				case 'editwish':
					Pathway::append(
						$this->_taskname,
						$this->_taskpath
					);
				break;
				case 'settings':
					Pathway::append(
						Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
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
				case 'display':
					// nothing
				break;

				default:
					// XSS fix, passing raw user supplied/maniuplatable data to function that creates link. See ticket 1420
					Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
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
		if (User::isGuest())
		{
			$return = base64_encode(Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task, false, true), 'server'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false),
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
		$params = App::get('menu.params');

		// Incoming
		$id     = Request::getInt('id', $params->get('id', 0));
		$refid  = Request::getInt('rid', $params->get('rid', 1));
		$cat   	= Request::getWord('category', $params->get('category', 'general'));
		$saved  = Request::getInt('saved', 0);

		// are we viewing this from within a plugin?
		$plugin = (isset($this->plugin) && $this->plugin!='') ? $this->plugin : '';

		$cats = $this->config->get('categories', 'general, resource');
		if ($cat && !preg_replace("/" . $cat . "/", '', $cats) && !$plugin)
		{
			// oups, this looks like a wrong URL
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		if ($id)
		{
			$model = Wishlist::getInstance($id);
		}
		else
		{
			$model = Wishlist::getInstance($refid, $cat);
			if (!$model->exists())
			{
				$model->set('referenceid', $refid);
				$model->setup();
			}
		}

		// cannot find this list
		if (!$model->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		// remember list id for plugin use
		$this->listid = isset($this->listid) ? $this->listid : $id;

		// get admin priviliges
		$this->authorize_admin();

		// Authorize list owners
		if (!User::isGuest())
		{
			if (in_array(User::get('id'), $model->owners('individuals')))
			{
				$this->_admin = 2;
			}
			else if (in_array(User::get('id'), $model->owners('advisory')))
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
		if (!$model->isPublic() && User::isGuest())
		{
			if (!$plugin)
			{
				$this->_msg = Lang::txt('COM_WISHLIST_WARNING_WISHLIST_PRIVATE_LOGIN_REQUIRED');
				$this->loginTask();
				return;
			}
			else
			{
				// not authorized
				throw new Exception(Lang::txt('COM_WISHLIST_ALERTNOTAUTH'), 403);
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
		$model->set('granted_count', $model->wishes('count', $sp_filters, true)); //$objWish->get_count($model->get('id'), $sp_filters, $this->_admin, User::getRoot());
		$model->set('granted_percentage', ($total > 0 && $model->get('granted_count') > 0 ? round(($model->get('granted_count')/$total) * 100, 0) : 0));

		// Some extras
		$model->set('saved', $saved);
		$model->set('banking', ($this->banking ? $this->banking : 0));
		$model->set('banking', ($model->get('category') == 'user' ? 0 : $this->banking)); // do not allow points for individual wish lists

		Request::setVar('id', $id);

		$this->view->setLayout('display');
		$this->view->title    = $this->_title;
		$this->view->config   = $this->config;
		$this->view->option   = $this->_option;
		$this->view->task     = $this->_task;
		$this->view->wishlist = $model;
		$this->view->total    = $total;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Show an entry and associated content
	 *
	 * @return  void
	 */
	public function wishTask()
	{
		$wishid  = Request::getInt('wishid', 0);
		$id      = Request::getInt('id', 0);
		$refid   = Request::getInt('rid', 0);
		$cat     = Request::getVar('category', '');
		$action  = Request::getVar('action', '');
		$com     = Request::getInt('com', 0, 'get');
		$canedit = false;
		$saved   = Request::getInt('saved', 0);

		//$wishid = $this->wishid && !$wishid ? $this->wishid : $wishid;

		$wish = Wish::getInstance($wishid);

		if (!$wish->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND'), 404);
		}

		// Get wishlist info
		$wishlist = Wishlist::getInstance($wish->get('wishlist'));
		if (!$wishlist->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
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
			$this->_wishpath  = $wish->link();
			$this->_wishtitle = String::truncate($wish->get('subject'), 80);
			$this->_buildPathway($wishlist);

			// Go through some access checks
			if (User::isGuest() && $action)
			{
				$this->_msg = ($action=="addbonus") ? Lang::txt('COM_WISHLIST_MSG_LOGIN_TO_ADD_POINTS') : '';
				$this->loginTask();
				return;
			}

			if (!$wishlist->isPublic() && User::isGuest())
			{
				// need to log in to private list
				$this->_msg = Lang::txt('COM_WISHLIST_WARNING_WISHLIST_PRIVATE_LOGIN_REQUIRED');
				$this->loginTask();
				return;
			}

			if ($wish->isPrivate() && User::isGuest())
			{
				// need to log in to view private wish
				$this->_msg = Lang::txt('COM_WISHLIST_WARNING_LOGIN_PRIVATE_WISH');
				$this->loginTask();
				return;
			}

			// Deleted wish
			if ($wish->isDeleted() && !$wish->access('manage'))
			{
				throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND'), 404);
			}

			// Need to be admin to view private wish
			if ($wish->isPrivate() && !$wish->access('view'))
			{
				throw new Exception(Lang::txt('COM_WISHLIST_ALERTNOTAUTH'), 403);
			}

			// Get list filters
			$filters = self::getFilters($wish->get('admin'));

			// Update average value for importance (this is tricky MySQL)
		//	if (count($wishlist->owners('advisory')) > 0 && $this->config->get('votesplit', 0))
		//	{
				//$objR = new Tables\Wish\Rank($this->database);
				$votes = $wish->rankings(); //$objR->get_votes($wish->get('id'));

				// first consider votes by list owners
				if ($votes && count($votes) > 0)
				{
					$imp     = 0;
					$divisor = 0;
					$co_adv  = 0.8;
					$co_reg  = 0.2;
					$effort  = 0;
					$counter = 0;

					foreach ($votes as $vote)
					{
						if (count($wishlist->owners('advisory')) > 0 && $this->config->get('votesplit', 0) && in_array($vote->get('userid'), $wishlist->owners('advisory')))
						{
							$imp += $vote->get('importance') * $co_adv;
							$divisor += $co_adv;
						}
						else
						{
							$imp += $vote->get('importance') * $co_reg;
							$divisor += $co_reg;
						}
						if ($vote->get('effort') != 6)
						{
							$effort += $vote->get('effort');
							$counter++;
						}
					}

					// weighted average
					$wish->set('average_imp', ($imp/$divisor));

					// Set average effort
					if ($counter)
					{
						$wish->set('average_effort', ($effort/$counter));
					}
					else
					{
						$wish->set('average_effort', 7);
					}
				}
		//	}

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
				$BTL = new Teller(User::get('id'));
				$balance = $BTL->summary();
				$credit  = $BTL->credit_summary();
				$funds   = $balance - $credit;
				$funds   = ($funds > 0) ? $funds : '0';
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
		//	$this->view->setLayout('private'); // Where did this layout go?? - throws an error is group wishes
		}

		$this->view->title      = $this->_title;
		$this->view->config     = $this->config;
		$this->view->admin      = $this->_admin;
		$this->view->wishlist   = $wishlist;
		$this->view->wish       = $wish;
		$this->view->filters    = $filters;

		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->setLayout('wish')->display();
	}

	/**
	 * Save wishlist settings
	 *
	 * @return     void
	 */
	public function savesettingsTask()
	{
		$listid  = Request::getInt('listid', 0);
		$action  = Request::getVar('action', '');

		// Make sure we have list id
		if (!$listid)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		$wishlist = Wishlist::getInstance($listid);

		if (!$wishlist->access('manage'))
		{
			App::abort(403, Lang::txt('COM_WISHLIST_ALERTNOTAUTH'));
			return;
		}

		// Deeleting a user/group
		if ($action == 'delete')
		{
			$user  = Request::getInt('user', 0);
			$group = Request::getInt('group', 0);

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

			App::redirect(
				Route::url($wishlist->link('', '&saved=1'))
			);
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		if (!$wishlist->bind(Request::getVar('fields', array(), 'post')))
		{
			throw new Exception($obj->getError(), 500);
		}

		// store new content
		if (!$wishlist->store())
		{
			throw new Exception($wishlist->getError(), 500);
		}

		// Save new owners
		if ($newowners = Request::getVar('newowners', '', 'post'))
		{
			$wishlist->add('individuals', $newowners);
		}
		if ($newadvisory = Request::getVar('newadvisory', '', 'post'))
		{
			$wishlist->add('advisory', $newadvisory);
		}
		if ($newgroups = Request::getVar('newgroups', '', 'post'))
		{
			$wishlist->add('groups', $newgroups);
		}

		// update priority on all wishes
		$wishlist->rank();

		App::redirect(
			Route::url($wishlist->link('', '&saved=1'))
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
		$id  = Request::getInt('id', 0);

		$wishlist = new Wishlist($id);

		if (!$wishlist->exists())
		{
			// list not found
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 500);
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
		if (User::isGuest())
		{
			$this->_msg = Lang::txt('COM_WISHLIST_WARNING_LOGIN_MANAGE_SETTINGS');
			$this->loginTask();
			return;
		}

		$this->view->title    = $this->_title;
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
		$wishid = Request::getInt('wishid', 0);

		// Make sure we have wish id
		if (!$wishid)
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND'), 404);
		}

		$objWish = new Wish($wishid);
		if (!$objWish->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND'), 404);
		}

		$wishlist = Wishlist::getInstance($objWish->get('wishlist'));
		if (!$wishlist->exists())
		{
			// list not found
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
		}

		// Get List Title
		$this->_list_title = $wishlist->get('title');

		// Login required
		if (User::isGuest())
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

		$pageid = Request::getInt('pageid', 0, 'post');

		// Initiate extended database class
		$page = new Plan($pageid);
		$old  = new Plan($pageid);

		$page->set('version', Request::getInt('version', 1, 'post'));

		$create_revision = Request::getInt('create_revision', 0, 'post');
		if ($create_revision)
		{
			$page->set('id', 0);
			$page->set('version', $old->get('version') + 1);
		}

		$page->set('wishid', $wishid);
		$page->set('created_by', Request::getInt('created_by', User::get('id'), 'post'));
		$page->set('created', Date::toSql());
		$page->set('approved', 1);
		$page->set('pagetext', Request::getVar('pagetext', '', 'post', 'none'));

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
				throw new Exception($page->getError(), 500);
			}
		}

		// do we have a due date?
		$isdue  = Request::getInt('isdue', 0);
		$due    = Request::getVar('publish_up', '');

		if ($due)
		{
			$publishtime = $due . ' 00:00:00';
			$due = Date::of(strtotime($publishtime))->toSql();
		}

		//is this wish assigned to anyone?
		$assignedto = Request::getInt('assigned', 0);

		$new_assignee = ($assignedto && $objWish->get('assigned') != $assignedto) ? 1 : 0;

		$objWish->set('due', ($due ? $due : '0000-00-00 00:00:00'));
		$objWish->set('assigned', ($assignedto ? $assignedto : 0));

		// store our due date
		if (!$objWish->store())
		{
			throw new Exception($objWish->getError(), 500);
		}
		else if ($new_assignee)
		{
			// Build e-mail components
			$admin_email = Config::get('mailfrom');

			// to wish assignee
			$subject = Lang::txt(strtoupper($this->_option)) . ', ' . Lang::txt('COM_WISHLIST_WISH') . ' #' . $wishid . ' ' . Lang::txt('COM_WISHLIST_MSG_HAS_BEEN_ASSIGNED_TO_YOU');

			$from = array(
				'name'  => Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_option)),
				'email' => Config::get('mailfrom')
			);

			$message = array();

			// Plain text email
			$eview = new \Hubzero\Mail\View(array(
				'name'   => 'emails',
				'layout' => 'wish_plain'
			));
			$eview->option     = $this->_option;
			$eview->controller = $this->_controller;
			$eview->wish       = $objWish;
			$eview->wishlist   = $wishlist;
			$eview->action     = 'assigned';

			$message['plaintext'] = $eview->loadTemplate(false);
			$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

			// HTML email
			$eview->setLayout('wish_html');

			$message['multipart'] = $eview->loadTemplate();
			$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

			// Send message
			if (!Event::trigger('xmessage.onSendMessage', array('wishlist_wish_assigned', $subject, $message, $from, array($objWish->get('assigned')), $this->_option)))
			{
				$this->setError(Lang::txt('COM_WISHLIST_ERROR_FAILED_MSG_ASSIGNEE'));
			}
		}

		App::redirect(
			Route::url($objWish->link('plan'))
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
		$wishid   = Request::getInt('wishid', 0);
		$listid   = Request::getInt('id', 0);
		$refid    = Request::getInt('rid', 0);
		$category = Request::getVar('category', '');

		$wish = new Wish($wishid);

		if (!$listid && $refid)
		{
			if (!$category)
			{
				throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
			}

			$wishlist = Wishlist::getInstance($refid, $category);
			if (!$wishlist->exists())
			{
				$wishlist->set('category', $category);
				$wishlist->set('referenceid', $refid);
				$wishlist->setup();
			}
		}
		else
		{
			$wishlist = Wishlist::getInstance($listid);
		}

		if (!$wishlist->exists())
		{
			// list not found
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
		}

		// Build page title
		$this->_list_title = ($wishlist->isPublic() or (!$wishlist->isPublic() && $wishlist->access('manage'))) ? $wishlist->get('title') : '';
		$this->_buildTitle();

		// Set the pathway
		$this->_taskpath = $wish->exists()
							? $wish->link('edit')
							: 'index.php?option=' . $this->_option . '&task=add&category=' . $category . '&rid=' . $refid;
		$this->_taskname = $wish->exists()
							? Lang::txt('COM_WISHLIST_EDITWISH')
							: Lang::txt('COM_WISHLIST_ADD');
		$this->_buildPathway($wishlist);

		// Login required
		if (User::isGuest())
		{
			$this->_msg = Lang::txt('COM_WISHLIST_WARNING_WISHLIST_LOGIN_TO_ADD');
			$this->loginTask();
			return;
		}

		// get admin priviliges
		if (!$wishlist->isPublic() && !$wishlist->access('manage'))
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ALERTNOTAUTH'), 403);
		}

		// Get some defaults
		if (!$wish->exists())
		{
			$wish->set('proposed_by', User::get('id'));
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
			$BTL = new Teller(User::get('id'));
			$balance = $BTL->summary();
			$credit  = $BTL->credit_summary();
			$funds   = $balance - $credit;
			$funds   = ($funds > 0) ? $funds : '0';
		}

		// Output HTML
		$this->view->title    = $this->_title;
		$this->view->config   = $this->config;
		$this->view->admin    = $this->_admin;
		$this->view->wishlist = $wishlist;
		$this->view->wish     = $wish;

		// Get URL to page explaining virtual economy
		$aconfig = Component::params('com_answers');
		$this->view->infolink = $aconfig->get('infolink', Request::base(true) . '/kb/points/');
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
		$listid = Request::getInt('wishlist', 0);
		$wishid = Request::getInt('id', 0);
		$reward = Request::getVar('reward', '');
		$funds  = Request::getVar('funds', '0');
		$tags   = Request::getVar('tags', '');

		// Login required
		if (User::isGuest())
		{
			$this->_msg = Lang::txt('COM_WISHLIST_WARNING_WISHLIST_LOGIN_TO_ADD');
			$this->loginTask();
			return;
		}

		// Get wish list info
		$wishlist = Wishlist::getInstance($listid);
		if (!$wishlist->exists())
		{
			// list not found
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
		}

		// trim and addslashes all posted items
		$_POST = array_map('trim', $_POST);

		// initiate class and bind posted items to database fields
		$row = new Wish($_POST);

		// If we are editing
		$by = Request::getVar('by', '', 'post');
		if ($by)
		{
			$ruser = User::getInstance($by);
			if (is_object($ruser))
			{
				$row->set('proposed_by', $ruser->get('id'));
			}
			else
			{
				$this->setError(Lang::txt('COM_WISHLIST_ERROR_INVALID_USER_NAME'));
			}
		}

		// If offering a reward, do some checks
		if ($reward)
		{
			// Is it an actual number?
			if (!is_numeric($reward))
			{
				$this->setError(Lang::txt('COM_WISHLIST_ERROR_INVALID_AMOUNT'));
			}
			// Are they offering more than they can afford?
			if ($reward > $funds)
			{
				$this->setError(Lang::txt('COM_WISHLIST_ERROR_NO_FUNDS'));
			}
		}

		// Verify subject is in, before it hits the table
		$subject = $row->get('subject');
		if (!isset($subject) || $subject == '')
		{
			$this->setError(Lang::txt('COM_WISHLIST_ERROR_NO_SUBJECT'));
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
								? Lang::txt('COM_WISHLIST_EDITWISH')
								: Lang::txt('COM_WISHLIST_ADD');
			$this->_buildPathway($wishlist);

			// Get URL to page explaining virtual economy
			$aconfig = Component::params('com_answers');
			$infolink = $aconfig->get('infolink', \Request::base(true) . '/kb/points/');

			$this->view->title    = Lang::txt(strtoupper($this->_option));
			$this->view->config   = $this->config;
			$this->view->admin    = $this->_admin;
			$this->view->wishlist = $wishlist;
			$this->view->wish     = $row;
			$this->view->infolink = $infolink;
			$this->view->funds    = $funds;
			$this->view->banking  = $this->banking;
			$this->view->setError($this->getError());
			$this->view->setLayout('editwish')->display();
			return;
		}

		$row->set('anonymous', Request::getInt('anonymous', 0));
		$row->set('private', Request::getInt('private', 0));
		//$row->set('about', Sanitize::clean($row->get('about')));
		$row->set('proposed', ($wishid ? $row->get('proposed') : Date::toSql()));

		// store new content
		if (!$row->store(true))
		{
			throw new Exception($row->getError(), 500);
		}

		// Add/change the tags
		$row->tag($tags);

		// send message about a new wish
		if (!$wishid)
		{
			// Build e-mail components
			$admin_email = Config::get('mailfrom');

			// Get author name
			$name  = $row->proposer('name', Lang::txt('COM_WISHLIST_UNKNOWN'));
			$login = $row->proposer('username', Lang::txt('COM_WISHLIST_UNKNOWN'));

			if ($row->get('anonymous'))
			{
				$name  = Lang::txt('COM_WISHLIST_ANONYMOUS');
				$login = Lang::txt('COM_WISHLIST_ANONYMOUS');
			}

			$this->_list_title = $wishlist->get('title');

			$subject = Lang::txt(strtoupper($this->_option)).', '.Lang::txt('COM_WISHLIST_NEW_WISH').' '.Lang::txt('COM_WISHLIST_FOR').' '. $this->_list_title.' '.Lang::txt('from').' '.$name;
			$from = array(
				'name'  => Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_option)),
				'email' => Config::get('mailfrom')
			);

			$message = array();

			// Plain text email
			$eview = new \Hubzero\Mail\View(array(
				'name'   => 'emails',
				'layout' => 'wish_plain'
			));
			$eview->option     = $this->_option;
			$eview->controller = $this->_controller;
			$eview->wish       = $row;
			$eview->wishlist   = $wishlist;
			$eview->action     = 'created';

			$message['plaintext'] = $eview->loadTemplate(false);
			$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

			// HTML email
			$eview->setLayout('wish_html');

			$message['multipart'] = $eview->loadTemplate();
			$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

			if (!Event::trigger('xmessage.onSendMessage', array('wishlist_new_wish', $subject, $message, $from, $wishlist->owners('individuals'), $this->_option)))
			{
				$this->setError(Lang::txt('COM_WISHLIST_ERROR_FAILED_MESSAGE_OWNERS'));
			}
		}

		if ($reward && $this->banking)
		{
			// put the  amount on hold
			$BTL = new Teller(User::get('id'));
			$BTL->hold($reward, Lang::txt('COM_WISHLIST_BANKING_HOLD') . ' #' . $row->get('id') . ' ' . Lang::txt('COM_WISHLIST_FOR') . ' ' . $this->_list_title, 'wish', $row->get('id'));
		}

		$saved = $wishid ? 2 : 3;
		App::redirect(
			Route::url($row->link('permalink', array('saved' => $saved)))
		);
	}

	/**
	 * Show a form for editing a wish
	 *
	 * @return     void
	 */
	public function editwishTask()
	{
		$refid  = Request::getInt('rid', 0);
		$cat    = Request::getVar('category', '');
		$status = Request::getVar('status', '');
		$vid    = Request::getInt('vid', 0);

		// Check if wish exists on this list
		if ($id = Request::getInt('id', 0))
		{
			$wishlist = Wishlist::getInstance(Request::getInt('id', 0));
		}
		else
		{
			$wishlist = Wishlist::getInstance($refid, $cat);
		}
		if (!$wishlist->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
		}

		// load wish
		$wish = new Wish(Request::getInt('wishid', 0));
		if (!$wish->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND'), 404);
		}

		$changed = false;

		// Login required
		if (User::isGuest())
		{
			// Set page title
			$this->_list_title = ($wishlist->isPublic() or (!$wishlist->isPublic() && $wishlist->get('admin') == 2)) ? $wishlist->get('title') : '';
			$this->_buildTitle();

			// Set the pathway
			$this->_taskpath = $wish->link();
			$this->_taskname = Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
			$this->_buildPathway($wishlist);
			$this->loginTask();
			return;
		}

		if (!$wishlist->access('manage') && $wish->get('proposed_by') != User::get('id'))
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ALERTNOTAUTH'), 403);
		}

		if ($this->_task == 'editprivacy')
		{
			$private = Request::getInt('private', 0, 'get');
			if ($wish->get('private') != $private)
			{
				$wish->set('private', $private);
				$changed = true;
			}
		}

		if ($this->_task == 'editwish' && ($status = Request::getVar('status', '')))
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
					$wish->set('assigned', User::get('id')); // assign to person who accepted the wish
				break;

				case 'rejected':
					$wish->set('accepted', 0);
					$wish->set('status', 3);

					// return bonuses
					if ($this->banking)
					{
						$WE = new Economy($this->database);
						$WE->cleanupBonus($wish->get('id'));
					}
				break;

				case 'granted':
					$wish->set('status', 1);
					$wish->set('granted', Date::toSql());
					$wish->set('granted_by', User::get('id'));
					$wish->set('granted_vid', ($vid ? $vid : 0));

					$objWish = new Tables\Wish($this->database);
					$w = $objWish->get_wish($wish->get('id'), User::get('id'));
					$wish->set('points', $w->bonus);

					if ($this->banking)
					{
						// Distribute bonus and earned points
						$WE = new Economy($this->database);
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

				// to wish author
				$subject1 = Lang::txt(strtoupper($this->_option)) . ', ' . Lang::txt('COM_WISHLIST_YOUR_WISH') . ' #' . $wish->get('id') . ' is ' . $status;

				// to wish assignee
				$subject2 = Lang::txt(strtoupper($this->_option)) . ', ' . Lang::txt('COM_WISHLIST_WISH') . ' #' . $wish->get('id') . ' ' . Lang::txt('COM_WISHLIST_HAS_BEEN') . ' ' . Lang::txt('COM_WISHLIST_MSG_ASSIGNED_TO_YOU');

				$from = array(
					'name'  => Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_option)),
					'email' => Config::get('mailfrom')
				);

				$message = array();

				// Plain text email
				$eview = new \Hubzero\Mail\View(array(
					'name'   => 'emails',
					'layout' => 'wish_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->wish       = $wish;
				$eview->wishlist   = $wishlist;
				$eview->action     = 'updated';
				$eview->status     = $status;

				$message['plaintext'] = $eview->loadTemplate(false);
				$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

				// HTML email
				$eview->setLayout('wish_html');

				$message['multipart'] = $eview->loadTemplate();
				$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);
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
				throw new Exception($wish->getError(), 500);
			}
			else if ($this->_task == 'editwish')
			{
				if (!Event::trigger('xmessage.onSendMessage', array('wishlist_status_changed', $subject1, $message, $from, array($wish->get('proposed_by')), $this->_option)))
				{
					$this->setError(Lang::txt('COM_WISHLIST_ERROR_FAILED_MSG_AUTHOR'));
				}

				if ($wish->get('assigned')
				 && $wish->get('proposed_by') != $wish->get('assigned')
				 && $status == 'accepted')
				{
					if (!Event::trigger('xmessage.onSendMessage', array('wishlist_wish_assigned', $subject2, $as_mes, $from, array($wish->get('assigned')), $this->_option)))
					{
						$this->setError(Lang::txt('COM_WISHLIST_ERROR_FAILED_MSG_ASSIGNEE'));
					}
				}
			}
		}

		App::redirect(
			Route::url($wish->link())
		);
	}

	/**
	 * Move a wish
	 *
	 * @return     void
	 */
	public function movewishTask()
	{
		$listid   = Request::getInt('wishlist', 0);
		$wishid   = Request::getInt('wish', 0);
		$category = Request::getVar('type', '');
		$refid    = Request::getInt('resource', 0);
		if ($category == 'group')
		{
			$refid    = Request::getVar('group', '');
		}

		// some transfer options
		$options = array();
		$options['keepplan']     = Request::getInt('keepplan', 0);
		$options['keepcomments'] = Request::getInt('keepcomments', 0);
		$options['keepstatus']   = Request::getInt('keepstatus', 0);
		$options['keepfeedback'] = Request::getInt('keepfeedback', 0);

		// missing wish id
		if (!$wishid)
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND'), 404);
		}

		// missing or invalid resource ID
		if ($category == 'general')
		{
			$refid = 1; // default to main wish list
		}

		if ($category == 'question' or $category == 'ticket')
		{
			// move to a question or a ticket
			Event::trigger('support.transferItem', array(
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
			$oldlist = Wishlist::getInstance($listid);

			// Where do we put this wish?
			$newlist = Wishlist::getInstance($refid, $category);
			if (!$newlist->exists())
			{
				// Create wishlist for resource if doesn't exist
				if (!$newlist->setup())
				{
					throw new Exception($newlist->getError(), 500);
				}
			}

			// cannot add a wish to a non-found list
			if (!$newlist->exists())
			{
				throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
			}

			if ($listid != $newlist->get('id'))
			{
				// Transfer wish
				$wish = new Wish($wishid);
				$wish->set('wishlist', $newlist->get('id'));
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
					throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_MOVE_FAILED'), 500);
				}

				// also delete all previous owner votes for this wish
				if (!$wish->purge('rankings'))
				{
					throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_MOVE_FAILED'), 500);
				}

				// delete plan if option chosen
				if (!$options['keepplan'])
				{
					if (!$wish->purge('plan'))
					{
						throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_MOVE_FAILED'), 500);
					}
				}

				// delete comments if option chosen
				if (!$options['keepcomments'])
				{
					if (!$wish->purge('comments'))
					{
						throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_MOVE_FAILED'), 500);
					}
				}

				// delete community votes if option chosen
				if (!$options['keepfeedback'])
				{
					if (!$wish->purge('votes'))
					{
						throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_MOVE_FAILED'), 500);
					}
				}

				// send message about transferred wish

				$oldtitle = $oldlist->get('title'); //$objWishlist->getTitle($listid);
				$newtitle = $newlist->get('title'); //$objWishlist->getTitle($newlist);

				$name  = $wish->proposer('name', Lang::txt('COM_WISHLIST_UNKNOWN'));
				$login = $wish->proposer('username', Lang::txt('COM_WISHLIST_UNKNOWN'));

				if ($wish->get('anonymous'))
				{
					$name = Lang::txt('COM_WISHLIST_ANONYMOUS');
				}

				$subject1 = Lang::txt(strtoupper($this->_option)).', '.Lang::txt('COM_WISHLIST_NEW_WISH').' '.Lang::txt('COM_WISHLIST_FOR').' '.$newtitle.' '.Lang::txt('COM_WISHLIST_FROM').' '.$name.' - '.Lang::txt('COM_WISHLIST_TRANSFERRED');
				$subject2 = Lang::txt(strtoupper($this->_option)).', '.Lang::txt('COM_WISHLIST_YOUR_WISH').' #'.$wishid.' '.Lang::txt('COM_WISHLIST_WISH_TRANSFERRED_TO_DIFFERENT_LIST');

				$from = array(
					'name'  => Config::get('sitename').' '.Lang::txt(strtoupper($this->_option)),
					'email' => Config::get('mailfrom')
				);

				$message = array();

				// Plain text email
				$eview = new \Hubzero\Mail\View(array(
					'name'   => 'emails',
					'layout' => 'wish_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->wish       = $wish;
				$eview->wishlist   = $newlist;
				$eview->action     = 'moved';
				$eview->oldlist    = $oldlist;

				$message['plaintext'] = $eview->loadTemplate(false);
				$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

				// HTML email
				$eview->setLayout('wish_html');

				$message['multipart'] = $eview->loadTemplate();
				$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

				if (!Event::trigger('xmessage.onSendMessage', array('wishlist_new_wish', $subject1, $message, $from, $newlist->owners('individuals'), $this->_option)))
				{
					$this->setError(Lang::txt('COM_WISHLIST_ERROR_FAILED_MESSAGE_OWNERS'));
				}

				if (!Event::trigger('xmessage.onSendMessage', array('support_item_transferred', $subject2, $message, $from, array($wish->get('proposed_by')), $this->_option)))
				{
					$this->setError(Lang::txt('COM_WISHLIST_ERROR_FAILED_MSG_AUTHOR'));
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
		//$listid = Request::getInt('wishlist', 0);
		$wishid = Request::getInt('wish', 0);
		$amount = Request::getInt('amount', 0);

		// missing wish id
		/*if (!$wishid or !$listid)
		{
			App::abort(404, Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}*/

		//$objWishlist = new Wishlist($this->database);
		//$objWish = new Wish($this->database);

		$wishlist = new Wishlist(Request::getInt('wishlist', 0));
		if (!$wishlist->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
		}

		$wish = new Wish(Request::getInt('wish', 0));
		if (!$wish->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND'), 404);
		}

		// Login required
		if (User::isGuest())
		{
			// Set page title
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
		$BTL = new \Hubzero\Bank\Teller(User::get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance - $credit;
		$funds   = ($funds > 0) ? $funds : '0';

		// missing amount
		if ($amount == 0)
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_INVALID_AMOUNT'), 500);
		}
		if ($amount < 0)
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_NEGATIVE_BONUS'), 500);
		}
		else if ($amount > $funds)
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_NO_FUNDS'), 500);
		}

		// put the  amount on hold
		$BTL = new Teller(User::get('id'));
		$BTL->hold(
			$amount,
			Lang::txt('COM_WISHLIST_BANKING_HOLD') . ' #' . $wish->get('id') . ' ' . Lang::txt('COM_WISHLIST_FOR') . ' ' . $wishlist->get('title'),
			'wish',
			$wish->get('id')
		);

		App::redirect(
			Route::url($wish->link())
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
		$wishlist = new Wishlist(
			Request::getInt('rid', 0),
			Request::getVar('category', '')
		);
		if (!$wishlist->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND_ON_LIST'), 404);
		}

		$wish = new Wish(Request::getInt('wishid', 0));
		if (!$wish->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND'), 404);
		}

		// Login required
		if (User::isGuest())
		{
			// Set page title
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
		if (!$wishlist->access('manage') && $wish->get('proposed_by') != User::get('id'))
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ALERTNOTAUTH'), 403);
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
				$WE = new Economy($this->database);
				$WE->cleanupBonus($wish->get('id'));
			}
		}
		else
		{
			$this->setError(Lang::txt('COM_WISHLIST_ERROR_WISH_DELETE_FAILED'));
		}

		// go back to the wishlist
		App::redirect(
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
		Request::checkToken();

		$refid    = Request::getInt('rid', 0);
		$category = Request::getVar('category', '');

		$wishlist = Wishlist::getInstance($refid, $category);
		if (!$wishlist->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
		}

		$wishid   = Request::getInt('wishid', 0);

		$wish = Wish::getInstance($wishid);
		if (!$wish->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND_ON_LIST'), 404);
		}

		//$objWishlist = new Tables\Wishlist($this->database);
		//$objWish = new Tables\Wish($this->database);
		//$objR = new Tables\Wish\Rank($this->database);

		// figure list id
		/*if ($category && $refid)
		{
			$listid = $objWishlist->get_wishlistID($refid, $category);
		}

		// cannot rank a wish if list/wish is not found
		if (!$listid or !$wishid)
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
		}

		$wishlist = $objWishlist->get_wishlist($listid);
		$item = $objWish->get_wish($wishid, User::get('id'));

		// cannot proceed if wish id is not found
		if (!$wishlist or !$item)
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
		}

		// is this wish on correct list?
		if ($listid != $wishlist->id)
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND_ON_LIST'), 404);
		}*/

		// get vote
		$effort     = Request::getVar('effort', '', 'post');
		$importance = Request::getVar('importance', '', 'post');

		// Login required
		if (User::isGuest())
		{
			// Set page title
			if (!$wishlist->isPublic() && !$wishlist->access('manage'))
			{
				$this->_list_title = '';
			}
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);
			$this->_msg = Lang::txt('COM_WISHLIST_WARNING_LOGIN_TO_RANK');
			$this->loginTask();
			return;
		}

		// Need to be list admin
		if (!$wishlist->access('manage') || $wishlist->get('admin') == 1)
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ALERTNOTAUTH_ACTION'), 403);
		}

		// did user make selections?
		if (!$effort or !$importance)
		{
			App::redirect(
				Route::url($wish->link()),
				Lang::txt('Please be sure to provide both an importance and an effort'),
				'error'
			);
			return;
		}

		// is the wish ranked already?
		if (!$wish->rank($effort, $importance))
		{
			App::redirect(
				Route::url($wish->link()),
				$wish->getError(),
				'error'
			);
			return;
		}

		// update priority on all wishes
		if (!$wishlist->rank())
		{
			throw new Exception($wishlist->getError(), 500);
		}

		App::redirect(
			Route::url($wish->link())
		);
	}

	/**
	 * Save a wish comment
	 *
	 * @return     void
	 */
	public function savereplyTask()
	{
		Request::checkToken();

		// Incoming
		$id       = Request::getInt('referenceid', 0);
		$listid   = Request::getInt('listid', 0);
		$wishid   = Request::getInt('wishid', 0);
		$ajax     = Request::getInt('ajax', 0);
		$category = Request::getVar('cat', '');
		$when     = Date::toSql();

		// Get wishlist info
		$wishlist = Wishlist::getInstance($listid);

		if (!$wishlist->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'), 404);
		}

		$objWish = new Wish($wishid);

		// Get List Title
		$this->_list_title = $wishlist->get('title');

		// Build page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway($wishlist);

		if (!$id && !$ajax)
		{
			// cannot proceed
			throw new Exception(Lang::txt('COM_WISHLIST_ERROR_WISH_NOT_FOUND'), 404);
		}

		// is the user logged in?
		if (User::isGuest())
		{
			$this->_msg = Lang::txt('COM_WISHLIST_WARNING_LOGIN_TO_ADD_COMMENT');
			$this->loginTask();
			return;
		}

		if ($id && $category)
		{
			$fields = Request::getVar('comment', array(), 'post');

			$row = Comment::blank()->set($fields);

			// Perform some text cleaning, etc.
			$row->set(
				'content',
				(
					$row->get('content') == Lang::txt('COM_WISHLIST_ENTER_COMMENTS')
						? ''
						: $row->get('content')
				)
			);

			if ($attachment = $this->uploadTask($wishid))
			{
				$row->set('content', $row->get('content') . "\n" . $attachment);
			}

			$row->set('anonymous', ($row->get('anonymous') ? $row->get('anonymous') : 0));
			$row->set('state', 1);
			$row->set('item_type', $category);

			// Save the data
			if (!$row->save())
			{
				throw new Exception($row->getError(), 500);
			}

			// Build e-mail components
			$name  = $row->creator()->get('name', Lang::txt('UNKNOWN'));
			$login = $row->creator()->get('username', Lang::txt('UNKNOWN'));

			if ($row->get('anonymous'))
			{
				$name = Lang::txt('ANONYMOUS');
			}

			$subject = Lang::txt(strtoupper($this->_option)) . ', ' . Lang::txt('COM_WISHLIST_MSG_COMENT_POSTED_YOUR_WISH') . ' #' . $wishid . ' ' . Lang::txt('BY') . ' ' . $name;

			// email components
			$from = array(
				'name'  => Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_option)),
				'email' => Config::get('mailfrom')
			);

			// for the wish owner
			$subject1 = Lang::txt(strtoupper($this->_option)).', '.$name.' '.Lang::txt('COM_WISHLIST_MSG_COMMENTED_YOUR_WISH').' #'.$wishid;

			// for the person to whom wish is assigned
			$subject2 = Lang::txt(strtoupper($this->_option)).', '.$name.' '.Lang::txt('COM_WISHLIST_MSG_COMMENTED_ON_WISH').' #'.$wishid.' '.Lang::txt('COM_WISHLIST_MSG_ASSIGNED_TO_YOU');

			// for original commentor
			$subject3 = Lang::txt(strtoupper($this->_option)).', '.$name.' '.Lang::txt('COM_WISHLIST_MSG_REPLIED_YOUR_COMMENT').' #'.$wishid;

			// for others included in the conversation thread.
			$subject4 = Lang::txt(strtoupper($this->_option)).', '.$name.' '.Lang::txt('COM_WISHLIST_MSG_COMMENTED_AFTER_YOU').' #'.$wishid;

			$message = array();

			// Plain text email
			$eview = new \Hubzero\Mail\View(array(
				'name'   => 'emails',
				'layout' => 'comment_plain'
			));
			$eview->option     = $this->_option;
			$eview->controller = $this->_controller;
			$eview->wish       = $objWish;
			$eview->wishlist   = $wishlist;
			$eview->comment    = $row;

			$message['plaintext'] = $eview->loadTemplate(false);
			$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

			// HTML email
			$eview->setLayout('comment_html');

			$message['multipart'] = $eview->loadTemplate();
			$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

			// collect ids of people who were already emailed
			$contacted = array();

			if ($objWish->get('proposed_by') != $row->get('added_by'))
			{
				$contacted[] = $objWish->get('proposed_by');

				// send message to wish owner
				if (!Event::trigger('xmessage.onSendMessage', array('wishlist_comment_posted', $subject1, $message, $from, array($objWish->get('proposed_by')), $this->_option)))
				{
					$this->setError(Lang::txt('COM_WISHLIST_ERROR_FAILED_MSG_AUTHOR'));
				}
			} // -- end send to wish author

			if ($objWish->get('assigned')
			 && $objWish->get('assigned') != $row->get('added_by')
			 && !in_array($objWish->get('assigned'), $contacted))
			{
				$contacted[] = $objWish->get('assigned');

				// send message to person to who wish is assigned
				if (!Event::trigger('xmessage.onSendMessage', array('wishlist_comment_posted', $subject2, $message, $from, array($objWish->get('assigned')), $this->_option)))
				{
					$this->setError(Lang::txt('COM_WISHLIST_ERROR_FAILED_MSG_ASSIGNEE'));
				}
			} // -- end send message to person to who wish is assigned

			// get comment author if reply is posted to a comment
			if ($category == 'wishcomment')
			{
				$parent = Comment::oneOrNew($id);

				// send message to comment author
				if ($parent->get('created_by') != $row->get('created_by')
				 && !in_array($parent->get('created_by'), $contacted))
				{
					$contacted[] = $parent->get('created_by');
					if (!Event::trigger('xmessage.onSendMessage', array('wishlist_comment_thread', $subject3, $message, $from, array($parent->get('created_by')), $this->_option)))
					{
						$this->setError(Lang::txt('COM_WISHLIST_ERROR_FAILED_MSG_COMMENTOR'));
					}
				}
			}

			// get all users who commented
			$commentors = $objWish->comments('authors');
			$comm = array_diff($commentors, $contacted);

			if (count($comm) > 0)
			{
				if (!Event::trigger('xmessage.onSendMessage', array('wishlist_comment_thread', $subject4, $message, $from, $comm, $this->_option)))
				{
					$this->setError(Lang::txt('COM_WISHLIST_ERROR_FAILED_MSG_COMMENTOR'));
				}
			}
		} // -- end if id & category

		App::redirect(
			Route::url($objWish->link())
		);
	}

	/**
	 * Delete a comment
	 *
	 * @return  void
	 */
	public function deletereplyTask()
	{
		// Incoming
		$row = Comment::oneOrFail(
			Request::getInt('replyid', 0)
		);

		// Do we have a reply ID?
		if (!$row->get('id'))
		{
			$this->setError(Lang::txt('COM_WISHLIST_ERROR_REPLY_NOT_FOUND'));
			return;
		}

		if ($row->get('created_by') != User::get('id'))
		{
			App::redirect(
				Request::getVar('HTTP_REFERER', NULL, 'server'),
				Lang::txt('COM_WISHLIST_ERROR_CANNOT_DELETE_REPLY'),
				'error'
			);
			return;
		}

		// Delete the comment
		$row->set('state', $row::STATE_DELETED);

		if (!$row->save())
		{
			throw new Exception($row->getError(), 500);
		}

		// Go back to the page
		App::redirect(
			Request::getVar('HTTP_REFERER', NULL, 'server')
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
		if (User::isGuest())
		{
			// Get wishlist info
			$wishlist = new Wishlist(
				Request::getInt('refid', 0),
				Request::getVar('cat', '')
			);

			// Set page title
			$this->_list_title = ($wishlist->isPublic() or (!$wishlist->isPublic() && $wishlist->get('admin') == 2)) ? $wishlist->get('title') : '';
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);
			$this->_msg = Lang::txt('COM_WISHLIST_WARNING_LOGIN_TO_ADD_COMMENT');
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
		$wish = new Wish(
			Request::getInt('refid', 1)
		);

		if (!$wish->exists())
		{
			// cannot proceed
			return;
		}

		// Load the wishlist
		$wishlist = Wishlist::getInstance($wish->get('wishlist'));

		// Login required
		if (User::isGuest())
		{
			// Get List Title
			$this->_list_title = ($wishlist->isPublic() or (!$wishlist->isPublic() && $wishlist->access('manage'))) ? $wishlist->get('title') : '';
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);

			$this->_msg = Lang::txt('COM_WISHLIST_WARNING_WISHLIST_LOGIN_TO_RATE');
			$this->loginTask();
			return;
		}

		// Incoming
		$page = Request::getVar('page', 'wishlist');
		$vote = Request::getWord('vote', ''); // assuming text only vote. Fix for sql injection ticket 1182

		//$this->authorize_admin($listid);
		$filters = self::getFilters($wishlist->access('manage'));

		if ($wish->vote($vote))
		{
			$wishlist->rank();
		}

		// update display
		if (Request::getInt('ajax', 0))
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
			App::redirect(
				str_replace('&amp;', '&', Route::url($wishlist->link() . '&filterby='.$filters['filterby'].'&sortby='.$filters['sortby'].'&limitstart='.$filters['start'].'&limit='.$filters['limit'].'&tags='.$filters['tag']))
			);
		}
		else
		{
			App::redirect(
				str_replace('&amp;', '&', Route::url($wish->link() . '&filterby='.$filters['filterby'].'&sortby='.$filters['sortby'].'&limitstart='.$filters['start'].'&limit='.$filters['limit'].'&tags='.$filters['tag']))
			);
		}
	}

	/**
	 * Get an array of filters from the request
	 *
	 * @param   integer  $admin
	 * @return  array
	 */
	public function getFilters($admin=0)
	{
		// Query filters defaults
		$filters = array();
		$filters['sortby']   = Request::getVar('sortby', '');
		$filters['filterby'] = Request::getVar('filterby', 'all');
		$filters['search']   = Request::getVar('search', '');
		$filters['tag']      = Request::getVar('tags', '');

		if ($admin)
		{
			$filters['sortby'] = ($filters['sortby']) ? $filters['sortby'] : 'ranking';
		}
		else
		{
			$default = isset($this->banking) && $this->banking ? 'bonus' : 'date';
			$filters['sortby'] = ($filters['sortby']) ? $filters['sortby'] : $default;
		}

		if (!in_array($filters['sortby'], array('date', 'submitter', 'feedback', 'ranking')))
		{
			$filters['sortby'] = 'date';
		}

		if (!in_array($filters['filterby'], array('all', 'open', 'accepted', 'rejected', 'granted', 'submitter', 'public', 'private')))
		{
			$filters['filterby'] = 'all';
		}

		// Paging vars
		$filters['limit']    = Request::getInt('limit', Config::get('list_limit'));
		$filters['start']    = Request::getInt('limitstart', 0);
		$filters['new']      = Request::getInt('newsearch', 0);
		$filters['start']    = $filters['new'] ? 0 : $filters['start'];
		$filters['comments'] = Request::getVar('comments', 1, 'get');

		// Return the array
		return $filters;
	}

	/**
	 * Authorize administrator access
	 *
	 * @param   integer  $listid  Wish list ID
	 * @param   integer  $admin   If the use ris an admin
	 * @return  void
	 */
	public function authorize_admin($listid = 0, $admin = 0)
	{
		// Check if they're a site admin
		if (User::authorize($this->_option, 'manage'))
		{
			$admin = 1;
		}

		if ($listid)
		{
			$admingroup = $this->config->get('group', 'hubadmin');

			// Get list administrators
			$objOwner = new Tables\Owner($this->database);
			$owners = $objOwner->get_owners($listid,  $admingroup);
			$managers =  $owners['individuals'];
			$advisory =  $owners['advisory'];

			if (!User::isGuest())
			{
				if (in_array(User::get('id'), $managers))
				{
					$admin = 2;  // individual group manager
				}
				if (in_array(User::get('id'), $advisory))
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
		$database = \App::get('db');

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
			$users[] = \Html::select('option', '', 'No User', 'value', 'text');
			$users = array_merge($users, $database->loadObjectList());
		}
		else
		{
			$users = $database->loadObjectList();
		}

		$users = \Html::select('genericlist', $users, $name, ' ' . $javascript, 'value', 'text', $active, false, false);

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
			$this->setError(Lang::txt('COM_WISHLIST_ERROR_NO_UPLOAD_DIRECTORY'));
			return '';
		}

		// Incoming file
		$file = Request::getVar('upload', array(), 'files', 'array');
		if (!isset($file['name']) || !$file['name'])
		{
			$this->setError(Lang::txt('COM_WISHLIST_ERROR_NO_FILE'));
			return '';
		}

		// Make the filename safe
		$file['name'] = \Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		//make sure that file is acceptable type
		$attachment = new Attachment(array(
			'id'          => 0,
			'description' => Request::getVar('description', ''),
			'wish'        => $listdir,
			'filename'    => $file['name']
		));

		// make sure that file is acceptable type
		if (!$attachment->isAllowedType())
		{
			$this->setError(Lang::txt('ATTACHMENT: Incorrect file type.'));
			return Lang::txt('ATTACHMENT: Incorrect file type.');
		}

		$path = $attachment->link('dir');

		// Build the path if it doesn't exist
		if (!is_dir($path))
		{
			if (!\Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_WISHLIST_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return 'ATTACHMENT: ' . Lang::txt('COM_WISHLIST_UNABLE_TO_CREATE_UPLOAD_PATH');
			}
		}

		// Perform the upload
		if (!\Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_WISHLIST_ERROR_UPLOADING'));
			return 'ATTACHMENT: ' . Lang::txt('COM_WISHLIST_ERROR_UPLOADING');
		}
		else
		{
			// Scan for viruses
			$path = $path . DS . $file['name']; //PATH_CORE . DS . 'virustest';

			if (!\Filesystem::isSafe($path))
			{
				if (\Filesystem::delete($path))
				{
					$this->setError(Lang::txt('ATTACHMENT: File rejected because the anti-virus scan failed.'));
					return Lang::txt('ATTACHMENT: File rejected because the anti-virus scan failed.');
				}
			}

			if (!$attachment->store(true))
			{
				$this->setError($attachment->getError());
			}

			return '{attachment#' . $attachment->get('id') . '}';
		}
	}

	/**
	 * Download an attachment
	 *
	 * @return     void
	 */
	public function downloadTask()
	{
		$file   = Request::getVar('file', '');
		$wishid = Request::getInt('wishid', 0);

		$wish = new Wish($wishid);

		// Ensure we have a path
		if (!$wish->exists() || $wish->isDeleted() || $wish->isWithdrawn())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_FILE_NOT_FOUND'), 404);
		}

		$attachment = new Attachment($file, $wishid);

		// Ensure we have a path
		if (!$attachment->exists())
		{
			throw new Exception(Lang::txt('COM_WISHLIST_FILE_NOT_FOUND'), 404);
		}

		//make sure that file is acceptable type
		if (!$attachment->isAllowedType())
		{
			throw new Exception(Lang::txt('Unknown file type.'), 404);
		}

		// Add PATH_CORE
		$filename = $attachment->link('file');

		// Ensure the file exist
		if (!file_exists($filename))
		{
			throw new Exception(Lang::txt('COM_WISHLIST_FILE_NOT_FOUND') . ' ' . $filename, 404);
		}

		// Initiate a new content server and serve up the file
		$xserver = new Server();
		$xserver->filename($filename);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			throw new Exception(Lang::txt('COM_WISHLIST_SERVER_ERROR'), 500);
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

		$due['immediate'] = Date::of(time() + $i)->toSql();
		$due['warning']   = Date::of(time() + $w)->toSql();

		return $due;
	}
}
