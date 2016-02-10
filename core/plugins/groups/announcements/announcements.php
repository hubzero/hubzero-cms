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

// No direct access
defined('_HZEXEC_') or die();

include_once(__DIR__ . DS . 'announcement.php');

/**
 * Group Announcements
 */
class plgGroupsAnnouncements extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
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
	 * @return     string
	 */
	public function onBeforeGroup($group, $authorized)
	{
		//creat view object
		$view = $this->view('sticky', 'browse');

		//vars for view
		$view->authorized = $authorized;
		$view->option     = 'com_groups';
		$view->group      = $group;
		$view->name       = $this->_name;
		$view->database   = App::get('db');

		// get plugin access
		$access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'announcements');

		//if set to nobody make sure cant access
		//check if guest and force login if plugin access is registered or members
		//check to see if user is member and plugin access requires members
		if ($access == 'nobody'
			|| (User::isGuest() && $access == 'registered')
			|| (!in_array(User::get('id'), $group->get('members')) && $access == 'members'))
		{
			return '';
		}

		//build array of filters
		$view->filters              = array();
		$view->filters['scope']     = 'group';
		$view->filters['scope_id']  = $view->group->get('gidNumber');
		$view->filters['state']     = 1;
		$view->filters['sticky']    = 1;
		$view->filters['published'] = 1;

		//create new announcement Object
		$hubzeroAnnouncement = new \Hubzero\Item\Announcement($view->database);
		$view->total = $hubzeroAnnouncement->count($view->filters);
		$view->rows  = $hubzeroAnnouncement->find($view->filters);

		//pass thru permissions for CRUD
		$view->isManager = $group->isManager(User::get('id'));

		//display list of announcements
		return $view->loadTemplate();
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param      object  $group      Current group
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
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

		//creat database object
		$this->database = App::get('db');

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
					$url = Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active);

					App::redirect(
						Route::url('index.php?option=com_users&view=login?return=' . base64_encode($url)),
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
				case 'save':     $arr['html'] .= $this->_save();     break;
				case 'new':      $arr['html'] .= $this->_edit();     break;
				case 'edit':     $arr['html'] .= $this->_edit();     break;
				case 'delete':   $arr['html'] .= $this->_delete();   break;
				default:         $arr['html'] .= $this->_list();
			}
		}

		//filters to get announcement count
		//get count of active
		$filters = array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber'),
			'state'     => 1,
			'published' => 1
		);

		//instantiate announcement object and get count
		$hubzeroAnnouncement = new \Hubzero\Item\Announcement($this->database);
		$total = $hubzeroAnnouncement->count($filters);

		//set metadata for menu
		$arr['metadata']['count'] = $total;
		$arr['metadata']['alert'] = '';

		// Return the output
		return $arr;
	}

	/**
	 * Display a list of all announcements
	 *
	 * @return     string HTML
	 */
	private function _list()
	{
		// Get course members based on their status
		// Note: this needs to happen *after* any potential actions ar performed above
		$view = $this->view('default', 'browse');

		//vars for view
		$view->authorized = $this->authorized;
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->name       = $this->_name;

		//build array of filters
		$view->filters              = array();
		$view->filters['search']    = Request::getVar('q', '');
		$view->filters['limit']     = Request::getInt('limit', $this->params->get('display_limit', 50));
		$view->filters['start']     = Request::getInt('limitstart', 0);
		$view->filters['start']     = ($view->filters['limit'] == 0) ? 0 : $view->filters['start'];
		$view->filters['scope']     = 'group';
		$view->filters['scope_id']  = $this->group->get('gidNumber');
		$view->filters['state']     = 1;
		//$view->filters['sticky']    = 0;

		//only get published announcements for members
		if ($view->authorized != 'manager')
		{
			$view->filters['published'] = 1;
		}

		//create new announcement Object
		$hubzeroAnnouncement = new \Hubzero\Item\Announcement($this->database);
		$view->total = $hubzeroAnnouncement->count($view->filters);
		$view->rows  = $hubzeroAnnouncement->find($view->filters);

		//get any errors
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		//display list of announcements
		return $view->loadTemplate();
	}

	/**
	 * Display a list of all announcements
	 *
	 * @return     string HTML
	 */
	private function _edit()
	{
		//create view object
		$view = $this->view('default', 'edit');

		//get incoming
		$id = Request::getInt('id', 0);

		//create new announcement Object
		$view->announcement = new \Hubzero\Item\Announcement($this->database);

		//if we have an id load that announcemnt
		if (isset($id) && $id != 0)
		{
			$view->announcement->load( $id );
		}

		//make sure its this groups announcement
		if (!$view->announcement->belongsToObject('group', $this->group->get('gidNumber')))
		{
			$this->setError(Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_PERMISSION_DENIED'));
			return $this->_list();
		}

		//pass vars to view
		$view->option = $this->option;
		$view->group  = $this->group;
		$view->name   = $this->_name;

		//get any errors
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		// Display edit form
		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 *
	 * @return     string HTML
	 */
	private function _save()
	{
		// Check for request forgeries
		Request::checkToken();

		//verify were authorized
		if ($this->authorized != 'manager')
		{
			$this->setError( Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_ONLY_MANAGERS_CAN_CREATE') );
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

		if ($fields['publish_up'] > $fields['publish_down'])
		{
			$this->setError(Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_INVALID_PUBLISH_DATES'));
			return $this->_edit($fields);
		}

		//announcement model
		$announcement = new \Hubzero\Item\Announcement($this->database);

		//attempt to save
		if (!$announcement->save($fields))
		{
			$this->setError($announcement->getError());
			return $this->_edit($fields);
		}

		// does user want to email and should we email yet?
		if ($email === true && $announcement->announcementPublishedForDate())
		{
			// email announcement
			$announcement->emailAnnouncement();

			//set that we sent it and resave
			$announcement->sent = 1;
			$announcement->save($announcement);
		}

		//success!
		App::redirect(
			Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=announcements'),
			Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SUCCESSFULLY_CREATED'),
			'success'
		);
		return;
	}

	/**
	 * Mark an entry as deleted
	 *
	 * @return     string HTML
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

		//announcement model
		$announcement = new \Hubzero\Item\Announcement($this->database);
		$announcement->load($id);

		//load created by user profile
		$profile = \Hubzero\User\Profile::getInstance($announcement->created_by);

		//make sure we are the one who created it
		if ($announcement->created_by != User::get('id') && $this->authorized != 'manager')
		{
			$this->setError(Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_ONLY_MANAGER_CAN_DELETE', $profile->get('name')));
			return $this->_list();
		}

		//set to deleted state
		$announcement->archive();

		//attempt to delete announcement
		if (!$announcement->save($announcement))
		{
			$this->setError(Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_UNABLE_TO_DELETE'));
			return $this->_list();
		}

		App::redirect(
			Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=announcements'),
			Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SUCCESSFULLY_DELETED'),
			'success'
		);
		return;
	}
}

