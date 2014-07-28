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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Groups controller class
 */
class GroupsControllerModules extends \Hubzero\Component\AdminController
{
	/**
	 * Override Execute Method
	 *
	 * @return 	void
	 */
	public function execute()
	{
		// Incoming
		$this->gid = JRequest::getVar('gid', '');

		// Ensure we have a group ID
		if (!$this->gid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=manage',
				JText::_('COM_GROUPS_MISSING_ID'),
				'error'
			);
			return;
		}

		$this->group = \Hubzero\User\Group::getInstance( $this->gid );

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
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=pages&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_NOT_ALLOWED'),
				'warning'
			);
			return;
		}

		// get page approvers
		$approvers = $this->config->get('approvers', '');
		$approvers = array_map("trim", explode(',', $approvers));

		// get modules archive
		$moduleArchive = GroupsModelModuleArchive::getInstance();
		$this->view->modules = $moduleArchive->modules('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(0,1,2),
			'orderby'   => 'position ASC, ordering ASC'
		));

		// are we in the approvers
		$this->view->needsAttention = new \Hubzero\Base\ItemList();
		if (in_array($this->juser->get('username'), $approvers))
		{
			// get group pages
			$moduleArchive = GroupsModelModuleArchive::getInstance();
			$this->view->needsAttention = $moduleArchive->modules('unapproved', array(
				'gidNumber' => $this->group->get('gidNumber'),
				'state'     => array(0,1),
				'orderby'   => 'ordering'
			));
		}

		// pass group to view
		$this->view->group = $this->group;

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
		//set to edit layout
		$this->view->setLayout('edit');

		// get request vars
		$ids = JRequest::getVar('id', array());
		$id  = (isset($ids[0])) ? $ids[0] : null;

		// get the category object
		$this->view->module = new GroupsModelModule( $id );

		// get a list of all pages for creating module menu
		$pageArchive = GroupsModelPageArchive::getInstance();
		$this->view->pages = $pageArchive->pages('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(0,1,2),
			'orderby'   => 'ordering'
		));

		// get a list of all pages for creating module menu
		$moduleArchive = GroupsModelModuleArchive::getInstance();
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
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save Page Category
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// get request vars
		$module = JRequest::getVar('module', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$menu   = JRequest::getVar('menu', array(), 'post');

		// set gid number
		$module['gidNumber'] = $this->group->get('gidNumber');

		// clean title & position
		$module['title']    = preg_replace("/[^-_ a-zA-Z0-9]+/", "", $module['title']);
		$module['position'] = preg_replace("/[^-_a-zA-Z0-9]+/", "", $module['position']);

		// get the category object
		$this->module = new GroupsModelModule( $module['id'] );

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
			$this->addComponentMessage($this->module->getError(), 'error');
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
			$this->module->set('created', JFactory::getDate()->toSql());
			$this->module->set('created_by', JFactory::getUser()->get('id'));
		}

		// set modified
		$this->module->set('modified', JFactory::getDate()->toSql());
		$this->module->set('modified_by', JFactory::getUser()->get('id'));

		// save version settings
		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		if (!$this->module->store(false, $this->group->isSuperGroup()))
		{
			$this->addComponentMessage($this->module->getError(), 'error');
			$this->editTask();
			return;
		}

		// create module menu
		if (!$this->module->buildMenu($menu))
		{
			$this->addComponentMessage($this->module->getError(), 'error');
			$this->editTask();
			return;
		}

		// do we need to reorder
		if ($ordering !== null)
		{
			$move = (int) $ordering - (int) $this->module->get('ordering');
			$this->module->move($move, $this->module->get('position'));
		}

		// log change
		GroupsModelLog::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_module_saved',
			'comments'  => array('module' => $module, 'module_menu' => $menu)
		));

		//inform user & redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
			JText::_('COM_GROUPS_MODULES_SAVED'),
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
		$ids = JRequest::getVar('id', array());

		// delete each module
		foreach ($ids as $moduleid)
		{
			// load modules
			$module = new GroupsModelModule( $moduleid );

			//set to deleted state
			$module->set('state', $module::APP_STATE_DELETED);

			// save module
			if (!$module->store(true))
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
					$module->getError(),
					'error'
				);
				return;
			}
		}

		// log change
		GroupsModelLog::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_modules_deleted',
			'comments'  => $ids
		));

		//inform user & redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
			JText::_('COM_GROUPS_MODULES_DELETED'),
			'passed'
		);
	}

	/**
	 * Output raw content
	 *
	 * @param     $escape    Escape outputted content
	 * @return    string     HTML content
	 */
	public function rawTask( $escape = true )
	{
		// make sure we are approvers
		if (!GroupsHelperPages::isPageApprover())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get reqest vars
		$moduleid  = JRequest::getInt('moduleid', 0, 'get');

		// page object
		$module = new GroupsModelModule( $moduleid );

		// make sure module belongs to this group
		if (!$module->belongsToGroup($this->group))
		{
			JError::raiseError(403, JText::_('COM_GROUPS_MODULES_NOT_AUTHORIZED'));
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
		if (!GroupsHelperPages::isPageApprover())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get reqest vars
		$moduleid  = JRequest::getInt('moduleid', 0, 'get');

		// page object
		$module = new GroupsModelModule( $moduleid );

		// make sure page belongs to this group
		if (!$module->belongsToGroup($this->group))
		{
			JError::raiseError(403, JText::_('COM_GROUPS_MODULES_NOT_AUTHORIZED'));
		}

		// get first module menu's page id
		$pageid = $module->menu()->first()->get('pageid');

		// check if pageid 0
		if ($pageid == 0)
		{
			// get a list of all pages
			$pageArchive = GroupsModelPageArchive::getInstance();
			$pages = $pageArchive->pages('list', array(
				'gidNumber' => $this->group->get('gidNumber'),
				'state'     => array(1),
				'orderby'   => 'ordering'
			));

			// get first page
			$pageid = $pages->first()->get('id');
		}

		// load page
		$page = new GroupsModelPage( $pageid );

		// load page version
		$content = $page->version()->content('parsed');

		// create new group document helper
		$groupDocument = new GroupsHelperDocument();

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
		$pageCss = GroupsHelperView::GetPageCss($this->group);

		$css = '';
		foreach ($pageCss as $p)
		{
			$css .= '<link rel="stylesheet" href="'.$p.'" />';
		}

		// output html
		$html = '<!DOCTYPE html>
				<html>
					<head>
						<title>'.$this->group->get('description').'</title>
						'.$css.'
					</head>
					<body>
						'. $content .'
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
		if (!GroupsHelperPages::isPageApprover())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get request vars
		$id = JRequest::getInt('id', 0);

		// load page
		$module = new GroupsModelModule( $id );

		// make sure version is unapproved
		if ($module->get('approved') == 1)
		{
			//inform user & redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_ALREADY_APPROVED'),
				'warning'
			);
			return;
		}

		// create file for page
		$file    = JPATH_ROOT . DS . 'tmp' . DS . 'group_module_' . $module->get('id') . '.php';
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
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
			JText::_('COM_GROUPS_MODULES_NO_ERRORS'),
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
		if (!GroupsHelperPages::isPageApprover())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		//get request vars
		$module = JRequest::getVar('module', array(), 'post', 'none', JREQUEST_ALLOWRAW);

		// load page
		$groupModule = new GroupsModelModule( $module['id'] );

		// set the new content
		$groupModule->set('content', $module['content']);
		$groupModule->store(false, $this->group->isSuperGroup());

		//go back to error checker
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid . '&task=errors&id=' . $groupModule->get('id')
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
		if (!GroupsHelperPages::isPageApprover())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get request vars
		$id = JRequest::getInt('id', 0);

		// load page
		$module = new GroupsModelModule( $id );

		// make sure version is unapproved
		if ($module->get('approved') == 1)
		{
			//inform user & redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_ALREADY_APPROVED'),
				'warning'
			);
			return;
		}

		// get flags
		$flags = GroupsHelperPages::getCodeFlags();

		// get current versions content by lines
		$content = explode("\n", $module->get('content'));

		// get any issues
		$issues        = new stdClass;
		$issues->count = 0;
		foreach ($flags as $lang => $flag)
		{
			// define level patterns
			$severe   = implode('|', $flag['severe']);
			$elevated = implode('|', $flag['elevated']);
			$minor    = implode('|', $flag['minor']);

			// do case insensitive search for any flags
			$issues->$lang           = new stdClass;
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
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
			JText::_('COM_GROUPS_MODULES_NO_XSS'),
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
		if (!GroupsHelperPages::isPageApprover())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		//get request vars
		$module = JRequest::getVar('module', array(), 'post', 'none', JREQUEST_ALLOWRAW);

		// load module
		$groupModule = new GroupsModelModule( $module['id'] );

		// set the new content
		$groupModule->set('content', $module['content']);
		$groupModule->set('scanned', 1);

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		$groupModule->store(false, $this->group->isSuperGroup());

		// inform user and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
			JText::_('COM_GROUPS_MODULES_SCANNED'),
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
		if (!GroupsHelperPages::isPageApprover())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get request vars
		$module = JRequest::getVar('module', array(), 'post', 'none', JREQUEST_ALLOWRAW);

		// load page
		$groupModule = new GroupsModelModule( $module['id'] );

		// set the new content
		$groupModule->set('content', $module['content']);

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		$groupModule->store(false, $this->group->isSuperGroup());

		//go back to scanner
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid . '&task=scan&id=' . $groupModule->get('id')
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
		if (!GroupsHelperPages::isPageApprover())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_AUTHORIZED_APPROVERS_ONLY'),
				'error'
			);
			return;
		}

		// get request vars
		$id = JRequest::getInt('id', 0);

		// load page
		$module = new GroupsModelModule( $id );

		// make sure version is unapproved
		if ($module->get('approved') == 1)
		{
			//inform user & redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
				JText::_('COM_GROUPS_MODULES_ALREADY_APPROVED'),
				'warning'
			);
			return;
		}

		// set approved and approved date and approver
		$module->set('approved', 1);
		$module->set('approved_on', JFactory::getDate()->toSql());
		$module->set('approved_by', $this->juser->get('id'));

		// DONT RUN CHECK ON STORE METHOD (pass false as first arg to store() method)
		$module->store(false, $this->group->isSuperGroup());

		// send approved notifcation
		GroupsHelperPages::sendApprovedNotification('module', $module);

		// log change
		GroupsModelLog::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_modules_approved',
			'comments'  => array($module->get('id'))
		));

		// inform user and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->gid,
			JText::_('COM_GROUPS_MODULES_APPROVED'),
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
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . JRequest::getVar('gid', '')
		);
	}

	/**
	 * Manage group
	 *
	 * @return void
	 */
	public function manageTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=manage&task=edit&id[]=' . JRequest::getVar('gid', '')
		);
	}
}