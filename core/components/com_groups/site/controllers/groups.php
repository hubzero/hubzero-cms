<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Site\Controllers;

use Hubzero\Config\Registry;
use Hubzero\User\Group;
use Hubzero\Utility\Sanitize;
use Components\Groups\Models\Page;
use Components\Groups\Helpers;
use Components\Groups\Models\Tags;
use Components\Groups\Models\Log;
use Components\Groups\Models\Recent;
use Components\Groups\Models\Orm\Field;
use Filesystem;
use Request;
use Config;
use Event;
use Route;
use User;
use Date;
use Lang;
use App;

/**
 * Groups controller class
 */
class Groups extends Base
{
	/**
	 * Override Execute Method
	 *
	 * @return 	void
	 */
	public function execute()
	{
		// Get the cname, active tab, and action for plugins
		$this->cn     = Request::getString('cn', '');
		$this->active = Request::getCmd('active', '');
		$this->action = Request::getCmd('action', '');
		$this->task   = Request::getCmd('task', '');

		// Handles misrouted request
		if ($this->task == 'pages')
		{
			App::redirect(Route::url('index.php?option=' . $this->_option . '&cn='. $this->cn . '&controller=pages'));
		}

		// Are we serving up a file
		$uri = $_SERVER['REQUEST_URI'];
		if (strstr($uri, 'Image:'))
		{
			$file = strstr($uri, 'Image:');
		}
		elseif (strstr($uri, 'File:'))
		{
			$file = strstr($uri, 'File:');
		}

		// If we have a file
		if (isset($file))
		{
			return $this->downloadTask($file);
		}

		// Check in for user
		Helpers\Pages::checkinForUser();

		// Continue with parent execute method
		parent::execute();
	}

	/**
	 * Intro Page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Vars
		$mytags = '';
		$this->view->mygroups = array(
			'members'    => null,
			'invitees'   => null,
			'applicants' => null
		);
		$this->view->populargroups = array();
		$this->view->interestinggroups = array();

		// If we have a users profile load their groups and groups matching their tags
		if (!User::isGuest())
		{
			// Get users tags
			include_once \Component::path('com_members') . DS . 'models' . DS . 'tags.php';
			$mt = new \Components\Members\Models\Tags(User::get('id'));
			$mytags = $mt->render('string');

			// Get users groups
			$this->view->mygroups['members'] = \Hubzero\User\Helper::getGroups(User::get('id'), 'members', 1);
			$this->view->mygroups['invitees'] = \Hubzero\User\Helper::getGroups(User::get('id'), 'invitees', 1);
			$this->view->mygroups['applicants'] = \Hubzero\User\Helper::getGroups(User::get('id'), 'applicants', 1);
			$this->view->mygroups = array_filter($this->view->mygroups);

			// Get groups user may be interested in
			$this->view->interestinggroups = Group\Helper::getGroupsMatchingTagString(
				$mytags,
				\Hubzero\User\Helper::getGroups(User::get('id'))
			);
		}

		// Get the popular groups
		$this->view->populargroups  = Group\Helper::getPopularGroups(3);

		// Get featured groups
		$this->view->featuredgroups = Group\Helper::getFeaturedGroups($this->config->get('intro_featuredgroups_list', ''));

		// Set some vars for view
		$this->view->config = $this->config;
		$this->view->title = $this->_title;

		// Get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		// Display view
		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Browse Groups
	 *
	 * @return  void
	 */
	public function browseTask()
	{
		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Build list of filters
		$filters = array(
			'type'      => array(1, 3),
			'published' => Request::getInt('published', 1),
			'limit'     => 'all',
			'fields'    => array('COUNT(*)'),
			'search'    => Request::getString('search', ''),
			'sortby'    => strtolower(Request::getWord('sortby', 'title')),
			'policy'    => strtolower(Request::getWord('policy', '')),
			'index'     => htmlentities(Request::getString('index', ''))
		);

		if (!in_array($filters['published'], array(1, 2)))
		{
			$filters['published'] = 1;
		}

		// Make sure we have a valid sort filter
		if (!in_array($filters['sortby'], array('alias', 'title')))
		{
			$filters['sortby'] = 'title';
		}

		// Make sure we have a valid policy filter
		if (!in_array($filters['policy'], array('open', 'restricted', 'invite', 'closed')))
		{
			$filters['policy'] = '';
		}

		// Get a record count
		$total = Group::find($filters);

		// Filters for returning results
		$filters['limit']  = Request::getInt('limit', Config::get('list_limit'));
		$filters['limit']  = ($filters['limit']) ? $filters['limit'] : 'all';
		$filters['start']  = Request::getInt('limitstart', 0);
		$filters['fields'] = array('cn', 'description', 'published', 'gidNumber', 'type', 'public_desc', 'join_policy');

		// Get a list of all groups
		$groups = Group::find($filters);

		// Get view notifications
		$notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		// Display view
		$this->view
			->set('total', $total)
			->set('groups', $groups)
			->set('filters', $filters)
			->set('title', $this->_title)
			->set('authorized', $this->_authorize())
			->set('notifications', $notifications)
			->setLayout('browse')
			->display();
	}

	/**
	 * View Group
	 *
	 * @return  array
	 */
	public function viewTask()
	{
		// Set the needed layout
		$this->view->setLayout('view');

		// Validate the incoming cname
		if (!$this->_validCn($this->cn, true))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Load the group object
		$this->view->group = Group::getInstance($this->cn);

		// Check to make sure we were able to load group
		if (!is_object($this->view->group)|| !$this->view->group->get('gidNumber') || !$this->view->group->get('cn'))
		{
			$this->suggestNonExistingGroupTask();
			return;
		}

		// Ensure it's an allowable group type to display
		if (!in_array($this->view->group->get('type'), array(1, 3)))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Ensure the group is published
		if (!$this->view->group->get('published'))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Ensure the group has been published or has been approved
		if (!$this->view->group->get('approved'))
		{
			// Get list of members & managers & invitees
			$managers = $this->view->group->get('managers');
			$members  = $this->view->group->get('members');
			$invitees = $this->view->group->get('invitees');
			$members_invitees = array_merge($members, $invitees);
			$managers_members_invitees = array_merge($managers, $members, $invitees);

			// If user is not member, manager, or invitee deny access
			if (!in_array(User::get('id'), $managers_members_invitees))
			{
				$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
			}

			// If user is NOT manager but member or invitee
			if (!in_array(User::get('id'), $managers) && in_array(User::get('id'), $members_invitees))
			{
				return $this->unapprovedGroupTask();
			}

			// Set notification and clear after
			$this->setNotification(Lang::txt('COM_GROUPS_PENDING_APPROVAL_WARNING'), 'warning');
		}

		// Get the group params
		$this->view->gparams = new \Hubzero\Config\Registry($this->view->group->get('params'));

		// Check authorization
		$this->view->authorized = Helpers\View::authorize($this->view->group);

		// Get active tab
		$this->view->tab     = Helpers\View::getTab($this->view->group);
		$this->view->trueTab = strtolower(Request::getString('active', 'overview'));
		if ($this->view->group->get('approved') != 1 && $this->view->trueTab != 'overview')
		{
			return $this->unapprovedGroupTask();
		}

		// Record the user
		if (!User::isGuest() && in_array(User::get('id'), $this->view->group->get('members')))
		{
			include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'recent.php';

			Recent::hit(User::get('id'), $this->view->group->get('gidNumber'));
		}

		// Get group pages if any
		$pageArchive = Page\Archive::getInstance();
		$pages = $pageArchive->pages('list', array(
			'gidNumber' => $this->view->group->get('gidNumber'),
			'state'     => array(0,1),
			'orderby'   => 'lft ASC'
		));

		// Custom error handling for super groups
		Helpers\View::attachCustomErrorHandler($this->view->group);

		// Add the overview content
		$overviewContent = '';
		$activePage      = null;
		if ($this->view->tab == 'overview')
		{
			// Add home page to pages list
			$pages = Helpers\Pages::addHomePage($this->view->group, $pages);

			// Fetch the active page
			$activePage = Helpers\Pages::getActivePage($this->view->group, $pages);

			// Are we on the login?
			if ($this->view->trueTab == 'login')
			{
				$overviewContent = Helpers\View::superGroupLogin($this->view->group);
			}

			// Check to see if we have super group component or PHP page
			if ($overviewContent == null
				&& $this->config->get('super_components', 0))
			{
				$overviewContent = Helpers\View::superGroupComponents($this->view->group, $this->view->trueTab);
			}

			// Do we have group PHP pages?
			if ($overviewContent == null)
			{
				$overviewContent = Helpers\View::superGroupPhpPages($this->view->group);
			}

			// Set overview content
			if ($overviewContent == null)
			{
				$overviewContent = Helpers\Pages::displayPage($this->view->group, $activePage);
			}
		}

		// Build the title
		$this->_buildTitle($pages);

		// Build pathway
		$this->_buildPathway($pages);

		// Set some vars for view
		$this->view->title         = $this->_title;
		$this->view->content       = Helpers\View::displaySectionsContent($this->view->group, $overviewContent);
		$this->view->activePage    = $activePage;
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		// Check session if this is a newly submitted entry. Trigger a proper event if so.
		if (Session::get('newsubmission.group'))
		{
			// Unset the new submission session flag
			Session::set('newsubmission.group');
			Event::trigger('content.onAfterContentSubmission', array('Group'));
		}

		// Is this a super group?
		if ($this->view->group->isSuperGroup())
		{
			// Use group template file if we have it
			Request::setVar('tmpl', 'group');

			// Must call here cause otherwise doesnt load template
			$this->view->css()->js();

			// Load super group template
			// parse & render
			$superGroupTemplate = new Helpers\Template();
			$superGroupTemplate->set('group', $this->view->group)
				               ->set('tab', $this->view->trueTab)
				               ->set('content', $this->view->content)
				               ->set('page', $this->view->activePage)
				               ->parse()
				               ->render();

			// Echo content & stop execution
			return $superGroupTemplate->output(true);
		}

		// Display view
		$this->view->display();
	}

	/**
	 *  Show add group
	 *
	 * @return  void
	 */
	public function newTask()
	{
		if (!User::authorise('core.create', $this->_option))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'),
				'warning'
			);
		}

		$this->editTask();
	}

	/**
	 *  Show group edit
	 *
	 * @return  void
	 */
	public function editTask()
	{
		// set the neeced layout
		$this->view->setLayout('edit');

		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_GROUPS_CREATE_MUST_BE_LOGGED_IN'));
		}

		if (!User::authorise('core.edit', $this->_option))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'),
				'warning'
			);
		}

		// Are we creating a new group?
		if ($this->_task == 'new')
		{
			// Instantiate an Group object
			$this->view->group = new Group();

			// set some group vars for view
			$this->view->group->set('cn', Request::getString('suggested_cn', ''));
			$this->view->group->set('join_policy', $this->config->get('join_policy'));
			$this->view->group->set('discoverability', $this->config->get('discoverability', 0));
			$this->view->group->set('discussion_email_autosubscribe', null);

			$this->view->tags = '';

			// Set title
			$this->view->title = Lang::txt('COM_GROUPS_NEW_TITLE');
		}
		else
		{
			// Check to make sure we have cname
			if (!$this->cn)
			{
				$this->_errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
			}

			// Load the group page
			$this->view->group = Group::getInstance($this->cn);

			// Ensure we found the group info
			if (!$this->view->group || !$this->view->group->get('gidNumber'))
			{
				$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
			}

			// Check authorization
			// Published = 2 = archived. Archived is a read-only mode.
			if ($this->view->group->published == 2 || ($this->_authorize() != 'manager' && !$this->_authorizedForTask('group.edit')))
			{
				$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
			}

			// Get the group's interests (tags)
			$gt = new Tags($this->view->group->get('gidNumber'));
			$this->view->tags = $gt->render('string');

			// Set title
			$this->view->title = Lang::txt('COM_GROUPS_EDIT_TITLE', $this->view->group->get('description'));
		}

		// Create dir for uploads
		$this->view->lid = time() . rand(0, 1000);
		if ($this->lid != '')
		{
			$this->view->lid = $this->lid;
		}
		elseif ($this->view->group->get('gidNumber'))
		{
			$this->view->lid = $this->view->group->get('gidNumber');
		}

		// Are we passing a group from save method
		if ($this->group)
		{
			$this->view->group = $this->group;
			$this->view->tags  = $this->tags;
		}

		// Path to group assets
		$asset_path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $this->view->lid . DS . 'uploads';

		// If path is a directory then load images
		$this->view->logos = array();
		if (is_dir($asset_path))
		{
			// Get all images that are in group asset folder and could be a possible group logo
			$this->view->logos = Filesystem::files($asset_path, '.jpg|.jpeg|.png|.gif|.svg|.PNG|.JPG|.JPEG|.GIF|.SVG', true, true);
		}

		// Trigger the functions that return the areas we'll be using
		// then add overview to array
		$this->view->hub_group_plugins = Event::trigger('groups.onGroupAreas', array());
		array_unshift($this->view->hub_group_plugins, array(
			'name'             => 'overview',
			'title'            => 'Overview',
			'default_access'   => 'anyone',
			'display_menu_tab' => true
		));

		// Get plugin access
		$this->view->group_plugin_access = Group\Helper::getPluginAccess($this->view->group);

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		$this->view->task = $this->_task;
		$this->view->config = $this->config;

		// Get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$this->view->customFields = Field::all()->ordered('ordering');
		$customAnswers = array();
		foreach ($this->view->customFields as $field)
		{
			$fieldName = $field->get('name');
			$customAnswers[$fieldName] = $field->collectGroupAnswers($this->view->group->get('gidNumber'));
		}
		$this->view->customAnswers = $customAnswers;
		// Display view
		$this->view->display();
	}

	/**
	 *  Save group settings
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_GROUPS_CREATE_MUST_BE_LOGGED_IN'));
		}

		Request::checkToken();

		// Incoming
		$g_gidNumber = Request::getInt('gidNumber', 0, 'post');
		$c_gidNumber = Request::getString('gidNumber', 0, 'post');
		if ((string) $g_gidNumber !== (string) $c_gidNumber)
		{
			App::abort(404, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
		}

		if ((!$g_gidNumber && !User::authorise('core.create', $this->_option))
		 || ($g_gidNumber && !User::authorise('core.edit', $this->_option)))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'),
				'warning'
			);
		}

		$g_cn              = trim(Request::getString('cn', '', 'post'));
		$g_description     = preg_replace('/\s+/', ' ', trim(Request::getString('description', Lang::txt('NONE'), 'post')));
		$g_discoverability = Request::getInt('discoverability', 0, 'post');
		$g_public_desc     = Sanitize::clean(trim(Request::getString('public_desc', '', 'post', 'none', 2)));
		$g_private_desc    = Sanitize::clean(trim(Request::getString('private_desc', '', 'post', 'none', 2)));
		$g_restrict_msg    = Sanitize::clean(trim(Request::getString('restrict_msg', '', 'post', 'none', 2)));
		$g_join_policy     = Request::getInt('join_policy', 0, 'post');
		$tags              = trim(Request::getString('tags', ''));
		$lid               = Request::getInt('lid', 0, 'post');
		$customization     = Request::getArray('group', array(), 'POST');
		$plugins           = Request::getArray('group_plugin', '', 'POST');
		$params            = Request::getArray('params', array(), 'POST');

		$g_discussion_email_autosubscribe = Request::getInt('discussion_email_autosubscribe', 0, 'post');

		// Are we editing or creating?
		if ($g_gidNumber)
		{
			$group = Group::getInstance($g_gidNumber);
			$this->_task = 'edit';
			$before = Group::getInstance($g_gidNumber);
		}
		else
		{
			$this->_task = 'new';
			$group  = new Group();
			$before = new Group();
		}

		// Check authorization
		// Published = 2 = archived. Archived is a read-only mode.
		if ($group->published == 2 || ($this->_authorize() != 'manager' && $g_gidNumber != 0 && !$this->_authorizedForTask('group.edit')))
		{
			$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
		}

		// Check for any missing info
		if (!$g_cn)
		{
			$this->setNotification(Lang::txt('COM_GROUPS_SAVE_ERROR_MISSING_INFORMATION') . ': ' . Lang::txt('COM_GROUPS_DETAILS_FIELD_CN'), 'error');
		}
		if (!$g_description)
		{
			$this->setNotification(Lang::txt('COM_GROUPS_SAVE_ERROR_MISSING_INFORMATION') . ': ' . Lang::txt('COM_GROUPS_DETAILS_FIELD_DESCRIPTION'), 'error');
		}

		// Ensure the data passed is valid
		if ($g_cn == 'new' || $g_cn == 'browse')
		{
			$this->setNotification(Lang::txt('COM_GROUPS_SAVE_ERROR_INVALID_ID'), 'error');
		}
		if (!$this->_validCn($g_cn))
		{
			$this->setNotification(Lang::txt('COM_GROUPS_SAVE_ERROR_INVALID_ID'), 'error');
		}
		if ($this->_task == 'new' && Group::exists($g_cn, true))
		{
			$this->setNotification(Lang::txt('COM_GROUPS_SAVE_ERROR_ID_TAKEN'), 'error');
		}

		// Get the logo
		$logo = '';
		if (isset($customization['logo']))
		{
			$logo_path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber') . DS . 'uploads' . DS;
			$logo_path = substr($logo_path, strlen(PATH_ROOT));
			$logo = substr($customization['logo'], strlen($logo_path));
		}

		// Plugin settings
		$plugin_access = '';
		foreach ($plugins as $plugin)
		{
			$plugin_access .= $plugin['name'] . '=' . $plugin['access'] . ',' . "\n";
		}

		// Run content through validation and spam filters
		if (trim($g_public_desc))
		{
			$results = Event::trigger('content.onContentBeforeSave', array(
				'com_groups.group.public_desc',
				&$g_public_desc,
				($this->_task == 'new')
			));
			foreach ($results as $result)
			{
				if ($result === false)
				{
					$this->setNotification(Lang::txt('COM_GROUPS_SAVE_ERROR_FAILED_VALIDATION'), 'error');
					break;
				}
			}
		}

		$customFields = Field::all()->rows();
		$customFieldForm = Request::getArray('customfields', array());
		foreach ($customFields as $field)
		{
			$field->setFormAnswers($customFieldForm);
			if (!$field->validate())
			{
				$this->setNotification($field->getError(), 'error');
			}
		}
		// Push back into edit mode if any errors
		if ($this->getNotifications())
		{
			$group->set('cn', $g_cn);
			$group->set('description', $g_description);
			$group->set('public_desc', $g_public_desc);
			$group->set('private_desc', $g_private_desc);
			$group->set('join_policy', $g_join_policy);
			$group->set('restrict_msg', $g_restrict_msg);
			$group->set('discoverability', $g_discoverability);
			$group->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
			$group->set('logo', $logo);
			$group->set('plugins', $plugin_access);

			$this->lid = $lid;
			$this->group = $group;
			$this->tags = $tags;
			return $this->editTask();
		}

		// Build the e-mail message
		if ($this->_task == 'new')
		{
			$subject = Lang::txt('COM_GROUPS_SAVE_EMAIL_REQUESTED_SUBJECT', $g_cn);
			$type = 'groups_created';
		}
		else
		{
			$subject = Lang::txt('COM_GROUPS_SAVE_EMAIL_UPDATED_SUBJECT', $g_cn);
			$type = 'groups_changed';
		}

		if ($this->_task == 'new')
		{
			$group->set('cn', $g_cn);
			$group->set('type', 1);
			$group->set('published', 1);
			$group->set('approved', $this->config->get('auto_approve', 1));
			$group->set('created', Date::toSql());
			$group->set('created_by', User::get('id'));
			$group->add('managers', array(User::get('id')));
			$group->add('members', array(User::get('id')));
			$group->create();
		}

		// Merge incoming settings with existing params
		$params  = new Registry($params);
		$gParams = new Registry($group->get('params'));
		$gParams->merge($params);

		// Set group vars & Save group
		$group->set('description', $g_description);
		$group->set('public_desc', $g_public_desc);
		$group->set('private_desc', $g_private_desc);
		$group->set('join_policy', $g_join_policy);
		$group->set('restrict_msg', $g_restrict_msg);
		$group->set('discoverability', $g_discoverability);
		$group->set('logo', $logo);
		$group->set('plugins', $plugin_access);
		$group->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
		$group->set('params', $gParams->toString());
		$group->update();

		if (isset($customFields))
		{
			foreach ($customFields as $field)
			{
				$field->saveGroupAnswers($group->get('gidNumber'));
			}
		}
		// Process tags
		$gt = new Tags($group->get('gidNumber'));
		$gt->setTags($tags, User::get('id'));

		// Rename the temporary upload directory if it exist
		$log_comments = '';

		Event::trigger('groups.onGroupAfterSave', array($before, $group));

		if ($this->_task == 'new')
		{
			if ($lid != $group->get('gidNumber'))
			{
				$config = $this->config;
				$bp = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/groups'), DS);
				if (is_dir($bp . DS . $lid))
				{
					rename($bp . DS . $lid, $bp . DS . $group->get('gidNumber'));
				}
			}

			$log_action = 'group_created';

			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = Event::trigger('groups.onGroupNew', array($group));
			if (count($logs) > 0)
			{
				$log_comments .= implode('', $logs);
			}
		}
		else
		{
			$log_action   = 'group_edited';
		}

		// log invites
		Log::log(array(
			'gidNumber' => $group->get('gidNumber'),
			'action'    => $log_action,
			'comments'  => $log_comments
		));

		// Build the e-mail message
		// Note: this is done *before* pushing the changes to the group so we can show, in the message, what was changed
		$eview = new \Hubzero\Mail\View(array(
			'name'   => 'emails',
			'layout' => 'saved_plain'
		));

		$eview->option = $this->_option;
		$eview->user   = User::getInstance();
		$eview->group  = $group;
		$message['plaintext'] = $eview->loadTemplate(false);
		$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

		$eview->setLayout('saved');
		$message['multipart'] = $eview->loadTemplate();
		$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

		// Get the administrator e-mail
		$emailadmin = Config::get('mailfrom');

		// Get the "from" info
		$from = array(
			'name'  => Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_name)),
			'email' => Config::get('mailfrom')
		);

		// Only email managers if updating group
		if ($type == 'groups_changed')
		{
			// build array of managers
			$managers = $group->get('managers');

			// create new message
			Plugin::import('xmessage');

			if (!Event::trigger('onSendMessage', array($type, $subject, $message, $from, $managers, $this->_option)))
			{
				$this->setNotification(Lang::txt('GROUPS_ERROR_EMAIL_MANAGERS_FAILED'), 'error');
			}
		}

		// Only inform site admin if the group wasn't auto-approved
		if (!$this->config->get('auto_approve', 1) && $group->get('approved') == 0)
		{
			// Create approval subject
			$subject = Lang::txt('COM_GROUPS_SAVE_WAITING_APPROVAL', Config::get('sitename'));

			// build approval message
			$link  = 'https://' . trim($_SERVER['HTTP_HOST'], '/') . '/groups/' . $group->get('cn');
			$link2 = 'https://' . trim($_SERVER['HTTP_HOST'], '/') . '/administrator';
			$html  = Lang::txt('COM_GROUPS_SAVE_WAITING_APPROVAL_DESC', $group->get('description'), $link, $link2);
			$plain = Lang::txt('COM_GROUPS_SAVE_WAITING_APPROVAL_DESC', $group->get('description'), $link, $link2);

			// Create new message
			$message = new \Hubzero\Mail\Message();

			// Build message object and send
			$message->setSubject($subject)
					->addFrom($from['email'], $from['name'])
					->setTo($emailadmin)
					->addHeader('X-Mailer', 'PHP/' . phpversion())
					->addHeader('X-Component', 'com_groups')
					->addHeader('X-Component-Object', 'group_pending_approval')
					->addHeader('X-Component-ObjectId', $group->get('gidNumber'))
					->addPart($plain, 'text/plain')
					->addPart($html, 'text/html')
					->send();
		}

		// Create home page
		if ($this->_task == 'new')
		{
			// Create page
			$page = new Page(array(
				'gidNumber' => $group->get('gidNumber'),
				'parent'    => 0,
				'lft'       => 1,
				'rgt'       => 2,
				'depth'     => 0,
				'alias'     => 'overview',
				'title'     => 'Overview',
				'state'     => 1,
				'privacy'   => 'default',
				'home'      => 1
			));
			$page->store(false);

			// Create page version
			$version = new Page\Version(array(
				'pageid'     => $page->get('id'),
				'version'    => 1,
				'content'    => "<!-- {FORMAT:HTML} -->\n<p>[[Group.DefaultHomePage()]]</p>",
				'created'    => Date::toSql(),
				'created_by' => User::get('id'),
				'approved'   => 1
			));
			$version->store(false);
		}

		$url = Route::url('index.php?option=' . $this->_option . '&cn=' . $group->get('cn'));

		// Log activity
		$recipients = array(
			['group', $group->get('gidNumber')],
			['user', User::get('id')]
		);
		foreach ($group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($this->_task == 'new' ? 'created' : 'updated'),
				'scope'       => 'group',
				'scope_id'    => $group->get('gidNumber'),
				'description' => Lang::txt('COM_GROUPS_ACTIVITY_GROUP_' . ($this->_task == 'new' ? 'CREATED' : 'UPDATED'), '<a href="' . $url . '">' . $group->get('description') . '</a>'),
				'details'     => array(
					'title'     => $group->get('description'),
					'url'       => $url,
					'cn'        => $group->get('cn'),
					'gidNumber' => $group->get('gidNumber')
				)
			],
			'recipients' => $recipients
		]);

		// Show success message to user
		if ($this->_task == 'new')
		{
			$this->setNotification(Lang::txt('COM_GROUPS_CREATED_SUCCESS', $group->get('description')), 'passed');
			// If the new group is published, set the session flag indicating the new submission
			Session::set('newsubmission.group', true);
		}
		else
		{
			$this->setNotification(Lang::txt('COM_GROUPS_UPDATED_SUCCESS', $group->get('description')), 'passed');
		}

		// Redirect back to the group page
		App::redirect($url);
	}

	/**
	 * Show confirm delete view
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Set the neeced layout
		$this->view->setLayout('delete');

		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_GROUPS_DELETE_MUST_BE_LOGGED_IN'));
		}

		// Check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = Group::getInstance($this->cn);

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Check authorization
		if ($this->_authorize() != 'manager')
		{
			$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
		}

		// Get the group params
		$gparams = new Registry($this->view->group->get('params'));

		// If membership is managed in separate place disallow action
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(Lang::txt('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
			App::redirect(Route::url('index.php?option=com_groups&cn=' . $this->view->group->get('cn')));
		}

		// Start log
		$this->view->log = Lang::txt('COM_GROUPS_DELETE_MEMBER_LOG', count($this->view->group->get('members')));

		// Trigger the functions that delete associated content
		// Should return logs of what was deleted
		$logs = Event::trigger('groups.onGroupDeleteCount', array($this->view->group));
		if (count($logs) > 0)
		{
			$this->view->log .= '<br />' . implode('<br />', $logs);
		}

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		// Set some vars for view
		$this->view->title = Lang::txt('COM_GROUPS_DELETE_GROUP') . ': ' . $this->view->group->get('description');
		$this->view->msg = Request::getString('msg', '');

		// Display view
		$this->view->display();
	}

	/**
	 * Permanently delete group
	 *
	 * @return  void
	 */
	public function doDeleteTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_GROUPS_DELETE_MUST_BE_LOGGED_IN'));
		}

		// Check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$group = Group::getInstance($this->cn);

		// Ensure we found the group info
		if (!$group || !$group->get('gidNumber'))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Check authorization
		if ($this->_authorize() != 'manager')
		{
			$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
		}

		// Get request vars
		$confirm_delete = Request::getString('confirmdel', '');
		$message = trim(Request::getString('msg', '', 'post'));

		// Check to make sure we have confirmed
		if (!$confirm_delete || $confirm_delete != $group->get('cn'))
		{
			$this->setNotification(Lang::txt('COM_GROUPS_DELETE_MISSING_CONFIRM_MESSAGE'), 'error');
			return $this->deleteTask();
		}

		// Start log
		$log  = Lang::txt('COM_GROUPS_DELETE_MESSAGE_SUBJECT', $group->get('cn')) . "\n";
		$log .= Lang::txt('COM_GROUPS_GROUP_ID') . ': ' . $group->get('gidNumber') . "\n";
		$log .= Lang::txt('COM_GROUPS_GROUP_CNAME') . ': ' . $group->get('cn') . "\n";
		$log .= Lang::txt('COM_GROUPS_GROUP_TITLE') . ': ' . $group->get('description') . "\n";
		$log .= Lang::txt('COM_GROUPS_GROUP_DISCOVERABILITY') . ': ' . $group->get('discoverability') . "\n";
		$log .= Lang::txt('COM_GROUPS_GROUP_PUBLIC_TEXT') . ': ' . stripslashes($group->get('public_desc'))  . "\n";
		$log .= Lang::txt('COM_GROUPS_GROUP_PRIVATE_TEXT') . ': ' . stripslashes($group->get('private_desc'))  . "\n";
		$log .= Lang::txt('COM_GROUPS_GROUP_RESTRICTED_MESSAGE') . ': ' . stripslashes($group->get('restrict_msg')) . "\n";

		// Get number of group members
		$members  = $group->get('members');
		$managers = $group->get('managers');

		// Log ids of group members
		if ($members)
		{
			$log .= Lang::txt('COM_GROUP_MEMBERS') . ': ' . implode(' ', $members) . "\n";
		}
		$log .= Lang::txt('COM_GROUP_MANAGERS') . ': ' . implode(' ', $managers) . "\n";

		// Trigger the functions that delete associated content
		// Should return logs of what was deleted
		$logs = Event::trigger('groups.onGroupDelete', array($group));
		if (count($logs) > 0)
		{
			$log .= implode('', $logs);
		}

		// Build the file path
		$path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber');
		if (is_dir($path))
		{
			// Attempt to delete the file
			if (!Filesystem::deleteDirectory($path))
			{
				$this->setNotification(Lang::txt('UNABLE_TO_DELETE_DIRECTORY'), 'error');
			}
		}

		// Clone the deleted group
		$deletedgroup = clone($group);

		// Delete group
		if (!$group->delete())
		{
			$this->setNotification($group->error, 'error');
			return $this->deleteTask();
		}

		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_name));
		$from['email'] = Config::get('mailfrom');

		// E-mail subject
		$subject = Lang::txt('COM_GROUPS_DELETE_MESSAGE_SUBJECT', $deletedgroup->get('cn'));

		// Build the e-mail message
		$eview = new \Hubzero\Component\View(array('name' => 'emails','layout' => 'deleted'));
		$eview->option   = $this->_option;
		$eview->sitename = Config::get('sitename');
		$eview->user     = User::getInstance();
		$eview->gcn      = $deletedgroup->get('cn');
		$eview->msg      = $message;
		$eview->group    = $deletedgroup;
		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// Build array of email recipients
		$groupMembers = array();
		foreach ($members as $member)
		{
			$profile = User::getInstance($member);
			if ($profile)
			{
				$groupMembers[$profile->get('email')] = $profile->get('name');
			}
		}

		// Create new message
		$message = new \Hubzero\Mail\Message();

		// Build message object and send
		$message->setSubject($subject)
				->addFrom($from['email'], $from['name'])
				->setTo($groupMembers)
				->addHeader('X-Mailer', 'PHP/' . phpversion())
				->addHeader('X-Component', 'com_groups')
				->addHeader('X-Component-Object', 'group_deleted')
				->addHeader('X-Component-ObjectId', $deletedgroup->get('gidNumber'))
				->addPart($html, 'text/plain')
				->send();

		// Log deleted group
		Log::log(array(
			'gidNumber' => $deletedgroup->get('gidNumber'),
			'action'    => 'group_deleted',
			'comments'  => $log
		));

		// Log activity
		$recipients = array(
			['group', $deletedgroup->get('gidNumber')],
			['user', User::get('id')]
		);
		foreach ($managers as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'group',
				'scope_id'    => $deletedgroup->get('gidNumber'),
				'description' => Lang::txt('COM_GROUPS_ACTIVITY_GROUP_DELETED', '<a href="' . Route::url('index.php?option=' . $this->_option . '&cn=' . $deletedgroup->get('cn')) . '">' . $deletedgroup->get('description') . '</a>'),
				'details'     => array(
					'title'     => $deletedgroup->get('description'),
					'url'       => Route::url('index.php?option=' . $this->_option . '&cn=' . $deletedgroup->get('cn')),
					'cn'        => $deletedgroup->get('cn'),
					'gidNumber' => $deletedgroup->get('gidNumber')
				)
			],
			'recipients' => $recipients
		]);

		// Redirect back to the groups page
		$this->setNotification(Lang::txt('COM_GROUPS_DELETE_SUCCESS', $deletedgroup->get('description')), 'passed');
		App::redirect(Route::url('index.php?option=' . $this->_option));
	}

	/**
	 * View to Suggest User to Create Group
	 *
	 * @return  array
	 */
	public function suggestNonExistingGroupTask()
	{
		// Throw 404 error
		header("HTTP/1.0 404 Not Found");

		// Set notification
		$this->setNotification(
			Lang::txt('COM_GROUPS_CREATE_SUGGEST', Route::url('index.php?option=' . $this->_option . '&task=new' . (is_numeric($this->cn) ? '' : '&suggested_cn=' . $this->cn))),
			'warning'
		);

		// Get view notifications
		$notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		// Display view
		$this->view
			->set('title', 'Groups')
			->set('notifications', $notifications)
			->setLayout('suggest')
			->display();
	}

	/**
	 * Group is Unapproved
	 *
	 * @return  array
	 */
	public function unapprovedGroupTask()
	{
		// Get view notifications
		$notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		// Display view
		$this->view
			->set('title', 'Group: Unapproved')
			->set('notifications', $notifications)
			->setLayout('unapproved')
			->display();
	}

	/**
	 * Return data for the autocompleter
	 *
	 * @return  string  JSON
	 */
	public function autocompleteTask()
	{
		$filters = array(
			'limit'  => 20,
			'start'  => 0,
			'search' => trim(Request::getString('value', ''))
		);

		$query = "SELECT t.gidNumber, t.cn, t.description
					FROM `#__xgroups` AS t
					WHERE (t.type=1 OR t.type=3) AND (LOWER(t.cn) LIKE " . $this->database->quote('%' . $filters['search'] . '%') . " OR LOWER(t.description) LIKE " . $this->database->quote('%' . $filters['search'] . '%') . ")
					ORDER BY t.description ASC";

		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$name = str_replace("\n", '', stripslashes(trim($row->description)));
				$name = str_replace("\r", '', $name);

				$item = array(
					'id'   => $row->cn,
					'name' => $name
				);
				$json[] = $item;
			}
		}

		echo json_encode($json);
	}

	/**
	 * Get a list of members
	 *
	 * @return  void
	 */
	public function memberslistTask()
	{
		// Fetch results
		$filters = array();
		$filters['cn'] = trim(Request::getString('group', ''));

		if ($filters['cn'])
		{
			$query = "SELECT u.username, u.name
						FROM `#__users` AS u, `#__xgroups_members` AS m, `#__xgroups` AS g
						WHERE g.cn=" . $this->database->quote($filters['cn']) . " AND g.gidNumber=m.gidNumber AND m.uidNumber=u.id AND u.block = '0'
						ORDER BY u.name ASC";
		}
		else
		{
			$query = "SELECT a.username, a.name
						FROM `#__users` AS a
						WHERE a.block = '0' AND g.id=25
						ORDER BY a.name";
		}

		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();

		// Output search results in JSON format
		$json = array();
		if ($filters['cn'] == '')
		{
			$json[] = '{"username":"","name":"No User"}';
		}
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$json[] = '{"username":"' . $row->username . '","name":"' . htmlentities(stripslashes($row->name), ENT_COMPAT, 'UTF-8') . '"}';
			}
		}

		echo '{"members":[' . implode(',', $json) . ']}';
	}

	/**
	 * Get a group's availability
	 *
	 * @param   object  $group  Group
	 * @return  string
	 */
	public function groupavailabilityTask($group = null)
	{
		//get the group
		$group = (!is_null($group)) ? $group : Request::getString('group', '');
		$group = trim($group);

		if ($group == '')
		{
			return;
		}

		// Ensure the data passed is valid
		if (($group == 'new' || $group == 'browse') || (!$this->_validCn($group)) || (Group::exists($group, true)))
		{
			$availability = false;
		}
		else
		{
			$availability = true;
		}

		if (Request::getInt('no_html', 0) == 1)
		{
			echo json_encode(array('available' => $availability));
			return;
		}

		return $availability;
	}

	/**
	 * Download a file
	 *
	 * @param   string  $filename  File name
	 * @return  void
	 */
	public function downloadTask($filename = '')
	{
		// Get the group
		$group = Group::getInstance($this->cn);

		// Make sure we have a group
		if (!is_object($group))
		{
			return;
		}

		// Authorize
		$authorized = $this->_authorize();

		// Get the file name
		if (substr(strtolower($filename), 0, 5) == 'image')
		{
			$file = urldecode(substr($filename, 6));
		}
		elseif (substr(strtolower($filename), 0, 4) == 'file')
		{
			$file = urldecode(substr($filename, 5));
		}
		else
		{
			return;
		}

		// Clean up file, strip double "uploads" & trim directory sep
		$file = preg_replace('/^\/uploads\//', '', $file);
		$file = ltrim($file, DS);

		// Get extension
		$extension = pathinfo($file, PATHINFO_EXTENSION);

		// If were on the wiki we need to output files a specific way
		// @TODO: Re-implement this! Should not have hard-coded refs to other components
		if ($this->active == 'wiki')
		{
			// Get access level for wiki
			$access = Group\Helper::getPluginAccess($group, 'wiki');

			// Check to make sure user has access to wiki section
			if (($access == 'members' && !in_array(User::get('id'), $group->get('members')))
			 || ($access == 'registered' && User::isGuest()))
			{
				$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
			}

			// Load wiki page from db
			require_once Component::path('com_wiki') . DS . 'models' . DS . 'page.php';
			$page = new \Components\Wiki\Models\Page();

			$pagename = Request::getString('pagename');
			$scope = Request::getString('scope', $group->get('cn') . DS . 'wiki');
			if ($scope)
			{
				$parts = explode('/', $scope);
				if (count($parts) > 2)
				{
					$pagename = array_pop($parts);
					if (strtolower($filename) == strtolower($pagename))
					{
						$pagename = array_pop($parts);
					}
					$scope = implode('/', $parts);
				}
				$scope = str_replace($group->get('cn') . '/wiki', '', $scope);
				$scope = ($scope ? trim($scope, '/') . '/' : $scope);
			}
			$page = \Components\Wiki\Models\Page::oneByPath($scope . $pagename, 'group', $group->get('gidNumber'));

			// Check specific wiki page access
			if ($page->get('access') == 1 && !in_array(User::get('id'), $group->get('members')) && $authorized != 'admin')
			{
				$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
				return;
			}

			// Get the config and build base path
			$wiki_config = \Component::params('com_wiki');
			$base_path = $wiki_config->get('filepath') . DS . $page->get('id');
		}
		elseif ($this->active == 'blog')
		{
			// Get access setting of group blog
			$access = Group\Helper::getPluginAccess($group, 'blog');

			// Make sure user has access to blog
			if (($access == 'members' && !in_array(User::get('id'), $group->get('members')))
			 || ($access == 'registered' && User::isGuest()))
			{
				$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
			}

			// Make sure we have a group id of the proper length
			$groupID = Group\Helper::niceidformat($group->get('gidNumber'));

			// Build path to blog folder
			$base_path = $this->config->get('uploadpath') . DS . $groupID . DS . 'blog';
			if (!file_exists(PATH_APP . DS . $base_path . DS . $file))
			{
				$base_path = $this->config->get('uploadpath') . DS . $group->get('gidNumber') . DS . 'uploads' . DS . 'blog';
			}
		}
		else
		{
			// Get access level for overview or other group pages
			$access = Group\Helper::getPluginAccess($group, 'overview');

			// Check to make sure we can access it
			if (($access == 'members' && !in_array(User::get('id'), $group->get('members')))
			 || ($access == 'registered' && User::isGuest()))
			{
				$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
			}

			// Build the path
			$base_path = $this->config->get('uploadpath');
			$base_path .= DS . $group->get('gidNumber') . DS . 'uploads';
		}

		// Trim base path
		$base_path = ltrim($base_path, DS);

		// Only can serve files from within /site/groups/{group_id}/uploads/
		$pathCheck = PATH_APP . DS . $base_path;

		// Final path of file
		$file_path     = $base_path . DS . $file;
		$alt_file_path = null;

		// If super group offer alt path outside uploads
		if ($group->isSuperGroup())
		{
			$alt_file_path = str_replace('/uploads', '', $base_path) . DS . $file;

			// If super group can serve files anywhere inside /site/groups/{group_id}
			$altPathCheck  = PATH_APP . DS . ltrim($alt_file_path);
		}

		// Ensure the file exist
		if (!file_exists(PATH_APP . DS . $file_path))
		{
			if ($alt_file_path == null || !file_exists(PATH_APP . DS . $alt_file_path))
			{
				$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_FILE_NOT_FOUND'));
				return;
			}
			else
			{
				$file_path = $alt_file_path;
				$pathCheck = $altPathCheck;
			}
		}

		// Get full path, expanding ../
		if ($realPath = realpath(PATH_APP . DS . $file_path))
		{
			// Make sure requested file is within acceptable dir
			if (strpos($realPath, $pathCheck) === false)
			{
				$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
				return;
			}
		}

		// New content server
		$contentServer = new \Hubzero\Content\Server();
		$contentServer->filename(PATH_APP . DS . $file_path);
		$contentServer->disposition('attachment');
		$contentServer->acceptranges(false);

		// Do we need to manually set mime type?
		switch ($extension)
		{
			case 'css':
				$contentServer->setContentType('text/css');
				break;
			case 'js':
				$contentServer->setContentType('application/javascript');
				break;
			default:
				break;
		}

		// Serve up the file
		if (!$contentServer->serve())
		{
			App::abort(404, Lang::txt('COM_GROUPS_SERVER_ERROR'));
		}

		exit;
	}

	/**
	 * Check if a group alias is valid
	 *
	 * @param   integer  $cname        Group alias
	 * @param   boolean  $allowDashes  Allow dashes in cn
	 * @return  boolean  True if valid, false if not
	 */
	private function _validCn($cn, $allowDashes = false)
	{
		$regex = '/^[0-9a-z]+[_0-9a-z]*$/u';
		if ($allowDashes)
		{
			$regex = '/^[0-9a-z]+[-_0-9a-z]*$/u';
		}

		if (\Hubzero\Utility\Validate::reserved('group', $cn))
		{
			return false;
		}

		if (preg_match($regex, $cn))
		{
			if (is_numeric($cn) && intval($cn) == $cn && $cn >= 0)
			{
				return false;
			}
			else
			{
				return true;
			}
		}

		return false;
	}
}
