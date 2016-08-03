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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\User\Group;
use Components\Groups\Models\Page;
use Components\Groups\Models\Log;
use Components\Groups\Models\Module;
use Components\Groups\Helpers;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use Date;
use User;
use App;

/**
 * Groups controller class
 */
class Modules extends AdminController
{
	/**
	 * Override Execute Method
	 *
	 * @return 	void
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

		$this->group = Group::getInstance($this->gid);

		parent::execute();
	}

	/**
	 * Display Page Modules
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// modules only allowed for super groups or if modules are turned on
		if (!$this->group->isSuperGroup() && $this->config->get('page_modules', 0) == 0)
		{
			//inform user & redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=pages&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_MODULES_NOT_ALLOWED'),
				'warning'
			);
			return;
		}

		// get page approvers
		$approvers = $this->config->get('approvers', '');
		$approvers = array_map("trim", explode(',', $approvers));

		// get modules archive
		$moduleArchive = Module\Archive::getInstance();
		$this->view->modules = $moduleArchive->modules('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(0,1,2),
			'orderby'   => 'position ASC, ordering ASC'
		));

		// are we in the approvers
		$this->view->needsAttention = new \Hubzero\Base\ItemList();
		if (in_array(User::get('username'), $approvers))
		{
			// get group pages
			$moduleArchive = Module\Archive::getInstance();
			$this->view->needsAttention = $moduleArchive->modules('unapproved', array(
				'gidNumber' => $this->group->get('gidNumber'),
				'state'     => array(0,1),
				'orderby'   => 'ordering'
			));
		}

		// pass group to view
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
	 * Add Page Category
	 *
	 * @return void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit Page Module
	 *
	 * @return void
	 */
	public function editTask()
	{
		Request::setVar('hidemainmenu', 1);

		// get request vars
		$id = Request::getVar('id', array(0));
		if (is_array($id) && !empty($id))
		{
			$id = $id[0];
		}

		// get the category object
		$this->view->module = new Module($id);

		// get a list of all pages for creating module menu
		$pageArchive = Page\Archive::getInstance();
		$this->view->pages = $pageArchive->pages('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(0,1,2),
			'orderby'   => 'lft'
		));

		// get a list of all pages for creating module menu
		$moduleArchive = Module\Archive::getInstance();
		$this->view->order = $moduleArchive->modules('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'position'  => $this->view->module->get('position'),
			'state'     => array(0,1,2),
			'orderby'   => 'ordering'
		));

		// are we passing a category object
		if ($this->module)
		{
			$this->view->module = $this->module;
		}

		// pass group to view
		$this->view->group = $this->group;

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
	 * Save Page Category
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// get request vars
		$module = Request::getVar('module', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$menu   = Request::getVar('menu', array(), 'post');

		// set gid number
		$module['gidNumber'] = $this->group->get('gidNumber');

		// clean title & position
		$module['title']    = preg_replace("/[^-_ a-zA-Z0-9]+/", '', $module['title']);
		$module['position'] = preg_replace("/[^-_a-zA-Z0-9]+/", '', $module['position']);

		// get the category object
		$this->module = new Module($module['id']);

		// ordering change
		$ordering = null;
		if (isset($module['ordering']) && $module['ordering'] != $this->module->get('ordering'))
		{
			$ordering = $module['ordering'];
			unset($module['ordering']);
		}

		// if this is new module or were changing position,
		// get next order possible for position
		if (!isset($module['id']) || ($module['id'] == '')
			|| ($module['position'] != $this->module->get('position')))
		{
			$ordering = null;
			$module['ordering'] = $this->module->getNextOrder($module['position']);
		}

		// bind request vars to module model
		if (!$this->module->bind( $module ))
		{
			Notify::error($this->module->getError());
			return $this->editTask();
		}

		// mark approved unless fails check below
		$this->module->set('approved', 1);

		// if we have php or script tags we must get page approved by admin
		if (strpos($this->module->get('content'), '<?') !== false ||
			strpos($this->module->get('content'), '<?php') !== false ||
			strpos($this->module->get('content'), '<script') !== false)
		{
			$this->module->set('approved', 0);
			$this->module->set('approved_on', NULL);
			$this->module->set('approved_by', NULL);
			$this->module->set('checked_errors', 0);
			$this->module->set('scanned', 0);
		}

		// set created if new module
		if (!$this->module->get('id'))
		{
			$this->module->set('created', Date::toSql());
			$this->module->set('created_by', User::get('id'));
		}

		// set modified
		$this->module->set('modified', Date::toSql());
		$this->module->set('modified_by', User::get('id'));

		if (!is_object($this->group->params))
		{
			$this->group->params = new \Hubzero\Config\Registry($this->group->params);
		}
		$this->module->set('page_trusted', $this->group->params->get('page_trusted', 0));

		// save version settings
		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		if (!$this->module->store(false, $this->group->isSuperGroup()))
		{
			Notify::error($this->module->getError());
			return $this->editTask();
		}

		// create module menu
		if (!$this->module->buildMenu($menu))
		{
			Notify::error($this->module->getError());
			return $this->editTask();
		}

		// do we need to reorder
		if ($ordering !== null)
		{
			$move = (int) $ordering - (int) $this->module->get('ordering');
			$this->module->move($move, $this->module->get('position'));
		}

		// log change
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_module_saved',
			'comments'  => array('module' => $module, 'module_menu' => $menu)
		));

		//inform user & redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_MODULES_SAVED'),
			'passed'
		);
	}

	/**
	 * Delete Page Module
	 *
	 * @return void
	 */
	public function deleteTask()
	{
		// get request vars
		$ids = Request::getVar('id', array());

		// delete each module
		foreach ($ids as $moduleid)
		{
			// load modules
			$module = new Module($moduleid);

			//set to deleted state
			$module->set('state', $module::APP_STATE_DELETED);

			// save module
			if (!$module->store(true))
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
					$module->getError(),
					'error'
				);
				return;
			}
		}

		// log change
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_modules_deleted',
			'comments'  => $ids
		));

		//inform user & redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_MODULES_DELETED'),
			'passed'
		);
	}

	/**
	 * Output raw content
	 *
	 * @param   bool  $escape  Escape outputted content
	 * @return  void
	 */
	public function rawTask($escape = true)
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get reqest vars
		$moduleid  = Request::getInt('moduleid', 0, 'get');

		// page object
		$module = new Module($moduleid);

		// make sure module belongs to this group
		if (!$module->belongsToGroup($this->group))
		{
			App::abort(403, Lang::txt('COM_GROUPS_MODULES_NOT_AUTHORIZED'));
		}

		// output page version
		if ($escape)
		{
			echo highlight_string($module->content('raw'), true);
		}
		else
		{
			echo $module->get('content');
		}
		exit();
	}

	/**
	 * Preview Group Module
	 *
	 * @return void
	 */
	public function previewTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get reqest vars
		$moduleid  = Request::getInt('moduleid', 0, 'get');

		// page object
		$module = new Module($moduleid);

		// make sure page belongs to this group
		if (!$module->belongsToGroup($this->group))
		{
			App::abort(403, Lang::txt('COM_GROUPS_MODULES_NOT_AUTHORIZED'));
		}

		// get first module menu's page id
		$pageid = $module->menu()->first()->get('pageid');

		// check if pageid 0
		if ($pageid == 0)
		{
			// get a list of all pages
			$pageArchive = Page\Archive::getInstance();
			$pages = $pageArchive->pages('list', array(
				'gidNumber' => $this->group->get('gidNumber'),
				'state'     => array(1),
				'orderby'   => 'ordering'
			));

			// get first page
			$pageid = $pages->first()->get('id');
		}

		// load page
		$page = new Page($pageid);

		// load page version
		$content = $page->version()->content('parsed');

		// create new group document helper
		$groupDocument = new Helpers\Document();

		// strip out scripts & php tags if not super group
		if (!$this->group->isSuperGroup())
		{
			$content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
			$content = preg_replace('/<\?[\s\S]*?\?>/', '', $content);
		}

		// are we allowed to display group modules
		if (!$this->group->isSuperGroup() && !$this->config->get('page_modules', 0))
		{
			$groupDocument->set('allowed_tags', array());
		}

		// set group doc needed props
		// parse and render content
		$groupDocument->set('group', $this->group)
			          ->set('page', $page)
			          ->set('document', $content)
			          ->set('allMods', true)
			          ->parse()
			          ->render();

		// get doc content
		$content = $groupDocument->output();

		// only parse php if Super Group
		if ($this->group->isSuperGroup())
		{
			// run as closure to ensure no $this scope
			$eval = function() use ($content)
			{
				ob_start();
				unset($this);
				eval("?> $content <?php ");
				$content = ob_get_clean();
				return $content;
			};
			$content = $eval();
		}

		// get group css
		$pageCss = Helpers\View::getPageCss($this->group);

		$css = '';
		foreach ($pageCss as $p)
		{
			$css .= '<link rel="stylesheet" href="' . $p . '" />';
		}

		// output html
		$html = '<!DOCTYPE html>
				<html>
					<head>
						<title>' . $this->group->get('description') . '</title>
						' . $css . '
					</head>
					<body>
						' . $content . '
					</body>
				</html>';

		//echo content and exit
		echo $html;
		exit();
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
				Lang::txt('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get request vars
		$id = Request::getInt('id', 0);

		// load page
		$module = new Module($id);

		// make sure version is unapproved
		if ($module->get('approved') == 1)
		{
			//inform user & redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_MODULES_ALREADY_APPROVED'),
				'warning'
			);
			return;
		}

		// create file for page
		$file    = Config::get('tmp_path') . DS . 'group_module_' . $module->get('id') . '.php';
		$content = $module->get('content');
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
			$this->view->error = str_replace($file, '"' . $module->get('title') . '"', $this->view->error);
			$this->view->module = $module;
			$this->view->option = $this->_option;
			$this->view->controller = $this->_controller;
			$this->view->group = $this->group;
			$this->view->display();
			return;
		}

		// marked as checked for errors!
		$module->set('checked_errors', 1);
		$module->store(false, $this->group->isSuperGroup());

		// delete temp file
		register_shutdown_function(function($file){
			unlink($file);
		}, $file);

		// were all set
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_MODULES_NO_ERRORS'),
			'passed'
		);
	}

	/**
	 * Check for Errors again
	 *
	 * @return void
	 */
	public function errorsCheckAgainTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		//get request vars
		$module = Request::getVar('module', array(), 'post', 'none', JREQUEST_ALLOWRAW);

		// load page
		$groupModule = new Module($module['id']);

		// set the new content
		$groupModule->set('content', $module['content']);
		$groupModule->store(false, $this->group->isSuperGroup());

		//go back to error checker
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid . '&task=errors&id=' . $groupModule->get('id'), false)
		);
	}

	/**
	 * Scan module content
	 *
	 * @return void
	 */
	public function scanTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get request vars
		$id = Request::getInt('id', 0);

		// load page
		$module = new Module($id);

		// make sure version is unapproved
		if ($module->get('approved') == 1)
		{
			//inform user & redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_MODULES_ALREADY_APPROVED'),
				'warning'
			);
			return;
		}

		// get flags
		$flags = Helpers\Pages::getCodeFlags();

		// get current versions content by lines
		$content = explode("\n", $module->get('content'));

		// get any issues
		$issues        = new \stdClass;
		$issues->count = 0;
		foreach ($flags as $lang => $flag)
		{
			// define level patterns
			$severe   = implode('|', $flag['severe']);
			$elevated = implode('|', $flag['elevated']);
			$minor    = implode('|', $flag['minor']);

			// do case insensitive search for any flags
			$issues->$lang           = new \stdClass;
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
			$this->view->module = $module;
			$this->view->option = $this->_option;
			$this->view->controller = $this->_controller;
			$this->view->group = $this->group;
			$this->view->display();
			return;
		}

		// marked as scanned for potential issues!
		$module->set('scanned', 1);

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		$module->store(false, $this->group->isSuperGroup());

		// were all set
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_MODULES_NO_XSS'),
			'passed'
		);
	}

	/**
	 * Mark Module scanned
	 *
	 * @return void
	 */
	public function markScannedTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		//get request vars
		$module = Request::getVar('module', array(), 'post', 'none', JREQUEST_ALLOWRAW);

		// load module
		$groupModule = new Module($module['id']);

		// set the new content
		$groupModule->set('content', $module['content']);
		$groupModule->set('scanned', 1);

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		$groupModule->store(false, $this->group->isSuperGroup());

		// inform user and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_MODULES_SCANNED'),
			'passed'
		);
	}

	/**
	 * Run module scan again
	 *
	 * @return void
	 */
	public function scanAgainTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get request vars
		$module = Request::getVar('module', array(), 'post', 'none', JREQUEST_ALLOWRAW);

		// load page
		$groupModule = new Module($module['id']);

		// set the new content
		$groupModule->set('content', $module['content']);

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		$groupModule->store(false, $this->group->isSuperGroup());

		//go back to scanner
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid . '&task=scan&id=' . $groupModule->get('id'), false)
		);
	}

	/**
	 * Approve a group page
	 *
	 * @return void
	 */
	public function approveTask()
	{
		// make sure we are approvers
		if (!Helpers\Pages::isPageApprover())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get request vars
		$id = Request::getInt('id', 0);

		// load page
		$module = new Module($id);

		// make sure version is unapproved
		if ($module->get('approved') == 1)
		{
			//inform user & redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
				Lang::txt('COM_GROUPS_MODULES_ALREADY_APPROVED'),
				'warning'
			);
			return;
		}

		// set approved and approved date and approver
		$module->set('approved', 1);
		$module->set('approved_on', Date::toSql());
		$module->set('approved_by', User::get('id'));

		if (!is_object($this->group->params))
		{
			$this->group->params = new \Hubzero\Config\Registry($this->group->params);
		}
		$module->set('page_trusted', $this->group->params->get('page_trusted', 0));

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		$module->store(false, $this->group->isSuperGroup());

		// send approved notifcation
		Helpers\Pages::sendApprovedNotification('module', $module);

		// log change
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_modules_approved',
			'comments'  => array($module->get('id'))
		));

		// inform user and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid, false),
			Lang::txt('COM_GROUPS_MODULES_APPROVED'),
			'passed'
		);
	}

	/**
	 * Cancel a group page module task
	 *
	 * @return void
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
	 * @return void
	 */
	public function manageTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=manage&task=edit&id[]=' . Request::getVar('gid', ''), false)
		);
	}
}