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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Group Announcements
 */
class plgGroupsAnnouncements extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Loads the plugin language file
	 *
	 * @param   string   $extension  The extension for which a language file should be loaded
	 * @param   string   $basePath   The basepath to use
	 * @return  boolean  True, if the file has successfully loaded.
	 */
	public function loadLanguage($extension = '', $basePath = PATH_APP)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}

		$group = \Hubzero\User\Group::getInstance(Request::getCmd('cn'));
		if ($group && $group->isSuperGroup())
		{
			$basePath = PATH_APP . DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');
		}

		$lang = \App::get('language');
		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_CORE . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true);
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => Lang::txt('COM_GROUPS_ANNOUNCEMENTS'),
			'default_access'   => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f095'
		);
		return $area;
	}

	/**
	 * Return content that is to be displayed before group main area
	 *
	 * @param   object  $group
	 * @param   string  $authorized
	 * @return  string
	 */
	public function onBeforeGroup($group, $authorized)
	{
		// Get plugin access
		$access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'announcements');

		// if set to nobody make sure cant access
		// check if guest and force login if plugin access is registered or members
		// check to see if user is member and plugin access requires members
		if ($access == 'nobody'
			|| (User::isGuest() && $access == 'registered')
			|| (!in_array(User::get('id'), $group->get('members')) && $access == 'members'))
		{
			return '';
		}

		// Find announcements
		$rows = \Hubzero\Item\Announcement::all()
			->whereEquals('scope', 'group')
			->whereEquals('scope_id', $group->get('gidNumber'))
			->whereEquals('state', \Hubzero\Item\Announcement::STATE_PUBLISHED)
			->whereEquals('sticky', 1)
			->whereEquals('publish_up', '0000-00-00 00:00:00', 1)
				->orWhere('publish_up', '<=', Date::toSql(), 1)
				->resetDepth()
			->whereEquals('publish_down', '0000-00-00 00:00:00', 1)
				->orWhere('publish_down', '>=', Date::toSql(), 1)
			->rows();

		// Create view and assign data
		$view = $this->view('sticky', 'browse')
			->set('option', 'com_groups')
			->set('authorized', $authorized)
			->set('group', $group)
			->set('name', $this->_name)
			->set('rows', $rows)
			->setError($this->getErrors());

		// Pass thru permissions for CRUD
		$view->set('isManager', $group->isManager(User::get('id')));

		// Display list of announcements
		return $view->loadTemplate();
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param   object   $group       Current group
	 * @param   string   $option      Name of the component
	 * @param   string   $authorized  User's authorization level
	 * @param   integer  $limit       Number of records to pull
	 * @param   integer  $limitstart  Start of records to pull
	 * @param   string   $action      Action to perform
	 * @param   array    $access      What can be accessed
	 * @param   array    $areas       Active area(s)
	 * @return  array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$returnhtml = true;
		$active = 'announcements';

		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$returnhtml = false;
			}
		}

		//get the group members
		$members = $group->get('members');

		// Set some variables so other functions have access
		$this->authorized = $authorized;
		$this->members    = $members;
		$this->group      = $group;
		$this->option     = $option;
		$this->action     = $action;
		$this->access     = $access;

		//if we want to return content
		if ($returnhtml)
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//if were not trying to subscribe
			if ($this->action != 'subscribe')
			{
				//if set to nobody make sure cant access
				if ($group_plugin_acl == 'nobody')
				{
					$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
					return $arr;
				}

				//check if guest and force login if plugin access is registered or members
				if (User::isGuest()
				 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
				{
					$url = Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active, false, true);

					App::redirect(
						Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
						Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
						'warning'
					);
					return;
				}

				//check to see if user is member and plugin access requires members
				if (!in_array(User::get('id'), $members) && $group_plugin_acl == 'members')
				{
					$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
					return $arr;
				}
			}

			//run task based on action
			switch ($this->action)
			{
				case 'save':   $arr['html'] .= $this->_save();   break;
				case 'new':    $arr['html'] .= $this->_edit();   break;
				case 'edit':   $arr['html'] .= $this->_edit();   break;
				case 'delete': $arr['html'] .= $this->_delete(); break;
				default:       $arr['html'] .= $this->_list();
			}
		}

		if (!isset($this->total))
		{
			// Find announcements
			$model = \Hubzero\Item\Announcement::all()
				->whereEquals('scope', 'group')
				->whereEquals('scope_id', $group->get('gidNumber'))
				->whereEquals('state', 1);

			// Only get published announcements for members
			if ($this->authorized != 'manager')
			{
				$model->whereEquals('publish_up', '0000-00-00 00:00:00', 1)
					->orWhere('publish_up', '<=', Date::toSql(), 1)
					->resetDepth()
					->whereEquals('publish_down', '0000-00-00 00:00:00', 1)
					->orWhere('publish_down', '>=', Date::toSql(), 1);
			}

			$this->total = $model->total();
		}

		// Set metadata for menu
		$arr['metadata']['count'] = $this->total;
		$arr['metadata']['alert'] = '';

		// Return the output
		return $arr;
	}

	/**
	 * Display a list of all announcements
	 *
	 * @return  string  HTML
	 */
	private function _list()
	{
		//build array of filters
		$filters = array(
			'search'   => strtolower(Request::getVar('q', '')),
			'scope'    => 'group',
			'scope_id' => $this->group->get('gidNumber'),
			'state'    => 1
		);

		// Find announcements
		$model = \Hubzero\Item\Announcement::all()
			->whereEquals('scope', 'group')
			->whereEquals('scope_id', $this->group->get('gidNumber'))
			->whereEquals('state', \Hubzero\Item\Announcement::STATE_PUBLISHED);

		if ($filters['search'])
		{
			$model->whereLike('content', $filters['search']);
		}

		// Only get published announcements for members
		if ($this->authorized != 'manager')
		{
			$model->whereEquals('publish_up', '0000-00-00 00:00:00', 1)
				->orWhere('publish_up', '<=', Date::toSql(), 1)
				->resetDepth()
				->whereEquals('publish_down', '0000-00-00 00:00:00', 1)
				->orWhere('publish_down', '>=', Date::toSql(), 1);
		}

		$rows = $model->ordered()
			->paginated()
			->rows();

		$this->total = $rows->count();

		// Create view and assign data
		$view = $this->view('default', 'browse')
			->set('option', $this->option)
			->set('authorized', $this->authorized)
			->set('group', $this->group)
			->set('name', $this->_name)
			->set('filters', $filters)
			->set('rows', $rows)
			->setError($this->getErrors());

		// Display list of announcements
		return $view->loadTemplate();
	}

	/**
	 * Display a list of all announcements
	 *
	 * @param   object  $model  Hubzero\Item\Announcement
	 * @return  string  HTML
	 */
	private function _edit($model = null)
	{
		if (!is_object($model))
		{
			// Get incoming
			$id = Request::getInt('id', 0);

			// Create new announcement Object
			$model = \Hubzero\Item\Announcement::oneOrNew($id);
		}

		// Make sure its this groups announcement
		if (!$model->belongsToObject('group', $this->group->get('gidNumber')))
		{
			$this->setError(Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_PERMISSION_DENIED'));

			return $this->_list();
		}

		// Create view and assign data
		$view = $this->view('default', 'edit')
			->set('option', $this->option)
			->set('authorized', $this->authorized)
			->set('group', $this->group)
			->set('name', $this->_name)
			->set('announcement', $model)
			->setError($this->getError());

		// Display edit form
		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 *
	 * @return   mixed  An html view on error, redirects on success
	 */
	private function _save()
	{
		// Check for request forgeries
		Request::checkToken();

		//verify were authorized
		if ($this->authorized != 'manager')
		{
			$this->setError(Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_ONLY_MANAGERS_CAN_CREATE'));
			return $this->_list();
		}

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		// email announcement
		$email = (isset($fields['email']) && $fields['email'] == 1) ? true : false;

		//mark as not sent if we want to email again
		if ($email === true)
		{
			$fields['sent'] = 0;
		}

		// are we creating the announcement?
		if (!isset($fields['id']) || $fields['id'] == 0)
		{
			$fields['id']         = 0;
			$fields['scope']      = 'group';
			$fields['scope_id']   = $this->group->get('gidNumber');
			$fields['created']    = Date::toSql();
			$fields['created_by'] = User::get('id');
		}

		//do we want to mark sticky?
		$fields['sticky'] = (isset($fields['sticky']) && $fields['sticky'] == 1) ? 1 : 0;

		//do we want to mark as high priority
		$fields['priority'] = (isset($fields['priority']) && $fields['priority'] == 1) ? 1 : 0;

		//format publish up
		if (isset($fields['publish_up']) && $fields['publish_up'] != '' && $fields['publish_up'] != '0000-00-00 00:00:00')
		{
			$fields['publish_up'] = Date::of(str_replace('@', '', $fields['publish_up']), Config::get('offset'))->toSql();
		}

		//format publish down
		if (isset($fields['publish_down']) && $fields['publish_down'] != '' && $fields['publish_down'] != '0000-00-00 00:00:00')
		{
			$fields['publish_down'] = Date::of(str_replace('@', '', $fields['publish_down']), Config::get('offset'))->toSql();
		}

		// Bind data
		$model = \Hubzero\Item\Announcement::oneOrNew($fields['id'])->set($fields);

		if ($model->get('publish_down') != '0000-00-00 00:00:00'
		 && $model->get('publish_up') > $model->get('publish_down'))
		{
			$this->setError(Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_INVALID_PUBLISH_DATES'));
			return $this->_edit($model);
		}

		if (!$model->save())
		{
			$this->setError($model->setError());
			return $this->_edit($model);
		}

		// Does user want to email and should we email yet?
		if ($email === true && $model->inPublishWindow())
		{
			// Email announcement
			self::send($model, $this->group);

			// Set that we sent it and resave
			$model->set('sent', 1);
			$model->save();
		}

		$url = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name;

		// Record the activity
		$recipients = array(['group', $this->group->get('gidNumber')]);

		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'announcement',
				'scope_id'    => $model->get('id'),
				'description' => Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_ACTIVITY_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($url) . '">' . \Hubzero\Utility\String::truncate(strip_tags($model->get('content')), 70) . '</a>'),
				'details'     => array(
					'url'   => Route::url($url),
					'id'    => $this->group->get('gidNumber'),
					'alias' => $this->group->get('cn'),
					'title' => $this->group->get('description')
				)
			],
			'recipients' => $recipients
		]);

		// Redirect to the main listing
		App::redirect(
			Route::url($url),
			Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SUCCESSFULLY_SAVED'),
			'success'
		);
	}

	/**
	 * Mark an entry as deleted
	 *
	 * @return  mixed  An html view on error, redirects on success
	 */
	private function _delete()
	{
		//verify were authorized
		if ($this->authorized != 'manager')
		{
			$this->setError(Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_ONLY_MANAGERS_CAN_DELETE'));
			return $this->_list();
		}

		// Incoming
		$id = Request::getInt('id', 0);

		$model = \Hubzero\Item\Announcement::oneOrFail($id);

		// Make sure we are the one who created it
		if ($model->get('created_by') != User::get('id') && $this->authorized != 'manager')
		{
			$this->setError(Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_ONLY_MANAGER_CAN_DELETE', $model->creator()->get('name')));
			return $this->_list();
		}

		// Set to deleted state
		$model->set('state', \Hubzero\Item\Announcement::STATE_DELETED);

		// Attempt to delete announcement
		if (!$model->save())
		{
			$this->setError(Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_UNABLE_TO_DELETE'));
			return $this->_list();
		}

		$url = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name;

		// Record the activity
		$recipients = array(['group', $this->group->get('gidNumber')]);

		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'announcement',
				'scope_id'    => $model->get('id'),
				'description' => Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_ACTIVITY_DELETED', '<a href="' . Route::url($url) . '">' . \Hubzero\Utility\String::truncate(strip_tags($model->get('content')), 70) . '</a>'),
				'details'     => array(
					'url'   => Route::url($url),
					'id'    => $this->group->get('gidNumber'),
					'alias' => $this->group->get('cn'),
					'title' => $this->group->get('description')
				)
			],
			'recipients' => $recipients
		]);

		// Redirect to the main listing
		App::redirect(
			Route::url($url),
			Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SUCCESSFULLY_DELETED'),
			'success'
		);
	}

	/**
	 * Email Announcement
	 *
	 * @param   object  $announcement
	 * @param   object  $group
	 * @return  boolean
	 */
	public static function send($announcement, $group)
	{
		// get all group members
		$groupMembers = array();
		foreach ($group->get('members') as $member)
		{
			if ($profile = User::getInstance($member))
			{
				// Skip invalid emails
				if (preg_match('/^-[0-9]+@invalid$/', $profile->get('email')))
				{
					continue;
				}

				$groupMembers[$profile->get('email')] = $profile->get('name');
			}
		}

		if (!count($groupMembers))
		{
			return true;
		}

		// create view object
		$eview = new \Hubzero\Mail\View(array(
			'base_path' => __DIR__,
			'name'      => 'email',
			'layout'    => 'announcement_plain'
		));

		// plain text
		$eview->set('announcement', $announcement);
		$plain = $eview->loadTemplate();
		$plain = str_replace("\n", "\r\n", $plain);

		// HTML
		$eview->setLayout('announcement_html');
		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// set from address
		$from = array(
			'name'  => Config::get('sitename') . ' Groups',
			'email' => Config::get('mailfrom')
		);

		// define subject
		$subject = $group->get('description') . ' Group Announcement';

		foreach ($groupMembers as $email => $name)
		{
			// create message object
			$message = new \Hubzero\Mail\Message();

			// set message details and send
			$message->setSubject($subject)
					->addReplyTo($from['email'], $from['name'])
					->addFrom($from['email'], $from['name'])
					->setTo($email, $name)
					->addPart($plain, 'text/plain')
					->addPart($html, 'text/html')
					->send();
		}

		// all good
		return true;
	}
}
