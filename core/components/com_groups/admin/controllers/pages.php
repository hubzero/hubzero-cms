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

namespace Components\Groups\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\User\Group;
use Components\Groups\Models\Page;
use Components\Groups\Models\Log;
use Components\Groups\Helpers;
use stdClass;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use Date;
use User;
use App;

/**
 * Groups controller class for managing group pages
 */
class Pages extends AdminController
{
	/**
	 * Overload exec method to load group object
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Incoming
		$this->gid = Request::getVar('gid', '');

		// Ensure we have a group ID
		if (!$this->gid)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=manage', false),
				Lang::txt('COM_GROUPS_MISSING_ID'),
				'error'
			);
			return;
		}

		// load group object
		$this->group = Group::getInstance($this->gid);

		// run parent execute
		parent::execute();
	}

	/**
	 * Manage group pages
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// get group pages
		$pageArchive = Page\Archive::getInstance();
		$this->view->pages = $pageArchive->pages('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(0,1,2),
			'orderby'   => 'lft ASC'
		));

		// get page approvers
		$approvers = $this->config->get('approvers', '');
		$approvers = array_map("trim", explode(',', $approvers));

		// are we in the approvers
		$this->view->needsAttention = new \Hubzero\Base\ItemList();
		if (in_array(User::get('username'), $approvers))
		{
			// get group pages
			$pageArchive = Page\Archive::getInstance();
			$this->view->needsAttention = $pageArchive->pages('unapproved', array(
				'gidNumber' => $this->group->get('gidNumber'),
				'state'     => array(0,1),
				'orderby'   => 'lft ASC'
			));
		}

		// pass vars to view
		$this->view->group = $this->group;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a group page
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a group page
	 *
	 * @return  void
	 */
	public function editTask()
	{
		// get request vars
		$id = Request::getVar('id', array(0));
		$id = (is_array($id) ? $id[0] : $id);

		// get the page & version objects
		$this->view->page = new Page($id);
		$this->view->version = $this->view->page->version();
		$this->view->firstversion = $this->view->page->version(1);

		// get a list of all pages for creating module menu
		$pageArchive = Page\Archive::getInstance();
		$this->view->pages = $pageArchive->pages('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(0,1),
			'orderby'   => 'lft ASC'
		));

		// get page categories
		$categoryArchive = new Page\Category\Archive();
		$this->view->categories = $categoryArchive->categories('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'orderby'   => 'title'
		));

		// pass vars to view
		$this->view->group = $this->group;

		// get page templates
		$this->view->pageTemplates = Helpers\View::getPageTemplates($this->group);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a group page
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Get the page vars being posted
		$page    = Request::getVar('page', array(), 'post');
		$version = Request::getVar('pageversion', array(), 'post', 'none', JREQUEST_ALLOWRAW);

		// are we updating or creating a new page
		$task = ($page['id']) ? 'update' : 'create';

		// load page and version objects
		$this->page    = new Page($page['id']);
		$this->version = new Page\Version();

		// bind new page properties
		if (!$this->page->bind($page))
		{
			Notify::error($this->page->getError());
			return $this->editTask();
		}

		// bind new page version properties
		if (!$this->version->bind($version))
		{
			Notify::error($this->version->getError());
			return $this->editTask();
		}

		// make sure page belongs to group
		if ($task == 'update' && !$this->page->belongsToGroup($this->group))
		{
			App::abort(403, 'You are not authorized to modify this page.');
		}

		// set page vars
		$this->page->set('gidNumber', $this->group->get('gidNumber'));
		$this->page->set('alias', $this->page->uniqueAlias());

		// save page settings
		if (!$this->page->store(true))
		{
			Notify::error($this->page->getError());
			return $this->editTask();
		}

		// set page version vars
		$this->version->set('pageid', $this->page->get('id'));
		$this->version->set('version', $this->version->get('version') + 1);
		$this->version->set('created', Date::toSql());
		$this->version->set('created_by', User::get('id'));
		$this->version->set('approved', 1);
		$this->version->set('approved_on', Date::toSql());
		$this->version->set('approved_by', User::get('id'));

		// if we have php or script tags we must get page approved by admin
		if (strpos($this->version->get('content'), '<?') !== false ||
			strpos($this->version->get('content'), '<?php') !== false ||
			strpos($this->version->get('content'), '<script') !== false)
		{
			$this->version->set('approved', 0);
			$this->version->set('approved_on', null);
			$this->version->set('approved_by', null);
		}

		if (!is_object($this->group->params))
		{
			$this->group->params = new \Hubzero\Config\Registry($this->group->params);
		}
		$this->version->set('page_trusted', $this->group->params->get('page_trusted', 0));

		// save version settings
		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		if (!$this->version->store(false, $this->group->isSuperGroup()))
		{
			Notify::error($this->version->getError());
			return $this->editTask();
		}

		// get page children
		$children = $this->page->getChildren();

		// if we are publishing/unpublishing
		if (isset($page['state']))
		{
			if ($page['state'] == 0 || $page['state'] == 1)
			{
				// lets mark each child the same as parent
				foreach ($children as $child)
				{
					$child->set('state', $page['state']);
					$child->store(false);
				}
			}

			// if deleting lets set the first childs parent 
			// to be the deleted pages parents
			else if ($page['state'] == 2)
			{
				// update the first childs parent
				if ($firstChild = $children->first())
				{
					$firstChild->set('parent', $this->page->get('parent'));
					$firstChild->store(false);
				}

				// adjust depth foreach child
				// the proper depth is needed when viewing pages
				foreach ($children as $child)
				{
					$child->set('depth', $child->get('depth') - 1);
					$child->store(false);
				}
			}
		}

		// log edit
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_page_saved',
			'comments'  => array('page' => $page, 'version' => $version)
		));

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_PAGES_SAVED'),
			'passed'
		);
	}

	/**
	 * Delete Page
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// get request vars
		$ids = Request::getVar('id', array());

		// delete each module
		foreach ($ids as $pageid)
		{
			// load modules
			$page = new Page($pageid);

			// cant delete home
			if ($page->get('home') == 1)
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
					Lang::txt('COM_GROUPS_PAGES_CANT_DELETE_HOME'),
					'error'
				);
				return;
			}

			//set to deleted state
			$page->set('state', $page::APP_STATE_DELETED);

			// set ordering to 999 when deleting
			$page->set('ordering', 999);

			// save module
			if (!$page->store(true))
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
					$page->getError(),
					'error'
				);
				return;
			}
		}

		// log change
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_page_deleted',
			'comments'  => $ids
		));

		//inform user & redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_PAGES_DELETED'),
			'passed'
		);
	}

	/**
	 * Scan group page for possible issues
	 *
	 * @return  void
	 */
	public function scanTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_PAGES_MUST_BE_AUTHORIZED'),
				'error'
			);
			return;
		}

		// get request vars
		$id = Request::getInt('id', 0);

		// load page
		$page = new Page($id);

		// load current version
		$currentVersion = $page->version();

		// make sure version is unapproved
		if ($currentVersion->get('approved') == 1)
		{
			//inform user & redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_PAGES_ALREADY_APPROVED'),
				'warning'
			);
			return;
		}

		// get flags
		$flags = Helpers\Pages::getCodeFlags();

		// get current versions content by lines
		$content = explode("\n", $currentVersion->get('content'));

		// get any issues
		$issues = new stdClass;
		$issues->count = 0;
		foreach ($flags as $lang => $flag)
		{
			// define level patterns
			$severe   = implode('|', $flag['severe']);
			$elevated = implode('|', $flag['elevated']);
			$minor    = implode('|', $flag['minor']);

			// do case insensitive search for any flags
			if (!isset($issues->$lang))
			{
				$issues->$lang = new stdClass;
			}
			$issues->$lang->severe   = ($severe != '') ? preg_grep("/$severe/i", $content) : array();
			$issues->$lang->elevated = ($elevated != '') ? preg_grep("/$elevated/i", $content) : array();
			$issues->$lang->minor    = ($minor != '') ? preg_grep("/$minor/i", $content) : array();

			// add to issues count
			$issues->count += count($issues->$lang->severe) + count($issues->$lang->elevated) + count($issues->$lang->minor);
		}

		// handle issues
		if ($issues->count != 0)
		{
			$this->view->setLayout('scan');
			$this->view->issues = $issues;
			$this->view->page = $page;
			$this->view->option = $this->_option;
			$this->view->controller = $this->_controller;
			$this->view->group = $this->group;
			$this->view->display();
			return;
		}

		// marked as scanned for potential issues!
		$currentVersion->set('scanned', 1);

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		if (!is_object($this->group->params))
		{
			$this->group->params = new \Hubzero\Config\Registry($this->group->params);
		}
		$currentVersion->set('page_trusted', $this->group->params->get('page_trusted', 0));

		$currentVersion->store(false, 1);

		// were all set
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_PAGES_NO_XSS'),
			'passed'
		);
	}

	/**
	 * Check for PHP Errors
	 *
	 * @return void
	 */
	public function errorsTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_PAGES_MUST_BE_AUTHORIZED'),
				'error'
			);
			return;
		}

		// get request vars
		$id = Request::getInt('id', 0);

		// load page
		$page = new Page($id);

		// load current version
		$currentVersion = $page->version();

		// make sure version is unapproved
		if ($currentVersion->get('approved') == 1)
		{
			//inform user & redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_PAGES_ALREADY_APPROVED'),
				'warning'
			);
			return;
		}

		// create file for page
		$file    = Config::get('tmp_path') . DS . 'group_page_' . $page->get('id') . '.php';
		$content = $currentVersion->get('content');
		file_put_contents($file, $content);

		// basic php lint command
		$cmd = 'php -l ' . escapeshellarg($file) . ' 2>&1';

		// run lint
		exec($cmd, $output, $return);

		// do we get errors?
		if ($return != 0)
		{
			$this->view->setLayout('errors');
			$this->view->error = (isset($output[0])) ? $output[0] : '';
			$this->view->error = str_replace($file, '"' . $page->get('title') . '"', $this->view->error);
			$this->view->page = $page;
			$this->view->option = $this->_option;
			$this->view->controller = $this->_controller;
			$this->view->group = $this->group;
			$this->view->display();
			return;
		}

		// marked as checked for errors!
		$currentVersion->set('checked_errors', 1);

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		if (!is_object($this->group->params))
		{
			$this->group->params = new \Hubzero\Config\Registry($this->group->params);
		}
		$currentVersion->set('page_trusted', $this->group->params->get('page_trusted', 0));

		$currentVersion->store(false, 1);

		// delete temp file
		register_shutdown_function(function($file){
			unlink($file);
		}, $file);

		// were all set
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_PAGES_NO_ERRORS'),
			'passed'
		);
	}

	/**
	 * Mark Page Scanned
	 *
	 * @return  void
	 */
	public function markScannedTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_PAGES_MUST_BE_AUTHORIZED'),
				'error'
			);
			return;
		}

		//get request vars
		$page = Request::getVar('page', array(), 'post', 'none', JREQUEST_ALLOWRAW);

		// load page
		$groupPage = new Page($page['id']);

		// load current version
		$currentVersion = $groupPage->version();

		// set the new content
		$currentVersion->set('content', $page['content']);
		$currentVersion->set('scanned', 1);

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		if (!is_object($this->group->params))
		{
			$this->group->params = new \Hubzero\Config\Registry($this->group->params);
		}
		$currentVersion->set('page_trusted', $this->group->params->get('page_trusted', 0));

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		$currentVersion->store(false, $this->group->isSuperGroup());

		// inform user and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_PAGES_SCANNED'),
			'passed'
		);
	}

	/**
	 * Save content added in textarea & send off to scanner
	 *
	 * @return  void
	 */
	public function scanAgainTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_PAGES_MUST_BE_AUTHORIZED'),
				'error'
			);
			return;
		}

		//get request vars
		$page = Request::getVar('page', array(), 'post', 'none', JREQUEST_ALLOWRAW);

		// load page
		$groupPage = new Page($page['id']);

		// load current version
		$currentVersion = $groupPage->version();

		// set the new content
		$currentVersion->set('content', $page['content']);

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		$currentVersion->store(false, $this->group->isSuperGroup());

		// redirect to scan url
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid . '&task=scan&id=' . $groupPage->get('id'), false)
		);
	}


	/**
	 * Check for Errors again
	 *
	 * @return  void
	 */
	public function errorsCheckAgainTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_PAGES_MUST_BE_AUTHORIZED'),
				'error'
			);
			return;
		}

		//get request vars
		$page = Request::getVar('page', array(), 'post', 'none', JREQUEST_ALLOWRAW);

		// load page
		$groupPage = new Page( $page['id'] );

		// load current version
		$currentVersion = $groupPage->version();

		// set the new content
		$currentVersion->set('content', $page['content']);

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		$currentVersion->store(false, $this->group->isSuperGroup());

		//go back to error checker
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid . '&task=errors&id=' . $groupPage->get('id'), false)
		);
	}

	/**
	 * Approve a group page
	 *
	 * @return  void
	 */
	public function approveTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_PAGES_MUST_BE_AUTHORIZED'),
				'error'
			);
			return;
		}

		// get request vars
		$id = Request::getInt('id', 0);

		// load page
		$page = new Page($id);

		// load current version
		$currentVersion = $page->version();

		// make sure version is unapproved
		if ($currentVersion->get('approved') == 1)
		{
			//inform user & redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_PAGES_ALREADY_APPROVED'),
				'warning'
			);
			return;
		}

		// set approved and approved date and approver
		$currentVersion->set('approved', 1);
		$currentVersion->set('approved_on', Date::toSql());
		$currentVersion->set('approved_by', User::get('id'));

		if (!is_object($this->group->params))
		{
			$this->group->params = new \Hubzero\Config\Registry($this->group->params);
		}
		$currentVersion->set('page_trusted', $this->group->params->get('page_trusted', 0));

		// save version with approved status
		if (!$currentVersion->store(false, 1))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				$currentVersion->getError(),
				'error'
			);
			return;
		}

		// send approved notifcation
		Helpers\Pages::sendApprovedNotification('page', $page);

		// log approval
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_page_approved',
			'comments'  => array($page->get('id'))
		));

		// inform user and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_PAGES_APPROVED'),
			'passed'
		);
	}

	/**
	 * Output raw content
	 *
	 * @param   boolean  $escape  Escape outputted content
	 * @return  void
	 */
	public function rawTask($escape = true)
	{
		// make sure we are approvers
		if (!User::authorise('core.admin') && !Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_PAGES_MUST_BE_AUTHORIZED'),
				'error'
			);
			return;
		}

		// get reqest vars
		$pageid  = Request::getInt('pageid', 0, 'get');
		$version = Request::getInt('version', null, 'get');

		// page object
		$page = new Page($pageid);

		// make sure page belongs to this group
		if (!$page->belongsToGroup($this->group))
		{
			App::abort(403, Lang::txt('COM_GROUPS_PAGES_NOT_AUTH'));
		}

		// load page version
		$pageVersion = $page->version($version);

		// do we have a page version
		if ($pageVersion === null)
		{
			App::abort(404, Lang::txt('COM_GROUPS_PAGES_VERSION_NOT_FOUND'));
		}

		// output page version
		if ($escape)
		{
			echo highlight_string($pageVersion->content('raw'), true);
		}
		else
		{
			echo $pageVersion->content('raw');
		}
		exit();
	}


	/**
	 * Preview Group Page
	 *
	 * @return  void
	 */
	public function previewTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_PAGES_MUST_BE_AUTHORIZED'),
				'error'
			);
			return;
		}

		// get reqest vars
		$pageid  = Request::getInt('pageid', 0, 'get');
		$version = Request::getInt('version', 0, 'get');

		// page object
		$page = new Page( $pageid );

		// make sure page belongs to this group
		if (!$page->belongsToGroup($this->group))
		{
			App::abort(403, Lang::txt('COM_GROUPS_PAGES_NOT_AUTH'));
		}

		// get preview
		echo Helpers\Pages::generatePreview($page, $version);
		exit();
	}

	/**
	 * Cancel a group page task
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . Request::getVar('gid', ''), false)
		);
	}

	/**
	 * Manage group
	 *
	 * @return  void
	 */
	public function manageTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=manage&task=edit&id=' . Request::getVar('gid', ''), false)
		);
	}
}
