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

namespace Components\Members\Admin\Controllers;

use Components\Members\Models\Member;
use Components\Members\Helpers;
use Components\Members\Models\Profile;
use Components\Members\Models\Profile\Field;
use Components\Members\Models\Profile\Option;
use Hubzero\Access\Group as Accessgroup;
use Hubzero\Access\Access;
use Hubzero\Component\AdminController;
use Hubzero\Utility\Validate;
use Filesystem;
use Request;
use Notify;
use Config;
use Route;
use User;
use Date;
use Lang;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'profile' . DS . 'field.php';
include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'utility.php';

/**
 * Manage site members
 */
class Members extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		Lang::load($this->_option . '.members', dirname(__DIR__));

		$this->registerTask('modal', 'display');
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('confirm', 'state');
		$this->registerTask('unconfirm', 'state');
		$this->registerTask('applyprofile', 'saveprofile');
		$this->registerTask('unblock', 'block');

		parent::execute();
	}

	/**
	 * Display a list of site members
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'registerDate'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			'registerDate' => Request::getState(
				$this->_option . '.' . $this->_controller . '.registerDate',
				'registerDate',
				''
			),
			'activation' => Request::getState(
				$this->_option . '.' . $this->_controller . '.activation',
				'activation',
				0,
				'int'
			),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				'*'
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				0,
				'int'
			),
			'approved' => Request::getState(
				$this->_option . '.' . $this->_controller . '.approved',
				'approved',
				'*'
			),
			'group_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.group_id',
				'group_id',
				0,
				'int'
			),
			'range' => Request::getState(
				$this->_option . '.' . $this->_controller . '.range',
				'range',
				''
			)
		);

		// Build query
		$entries = Member::all();

		$a = $entries->getTableName();
		$b = '#__user_usergroup_map';

		$entries
			->select($a . '.*')
			->including(['accessgroups', function ($accessgroup){
				$accessgroup
					->select('*');
			}])
			->including(['notes', function ($note){
				$note
					->select('id')
					->select('user_id');
			}]);

		if ($filters['group_id'])
		{
			$entries
				->join($b, $b . '.user_id', $a . '.id', 'left')
				->whereEquals($b . '.group_id', (int)$filters['group_id']);
				/*->group($a . '.id')
				->group($a . '.name')
				->group($a . '.username')
				->group($a . '.password')
				->group($a . '.usertype')
				->group($a . '.block')
				->group($a . '.sendEmail')
				->group($a . '.registerDate')
				->group($a . '.lastvisitDate')
				->group($a . '.activation')
				->group($a . '.params')
				->group($a . '.email');*/
		}

		if ($filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$entries->whereEquals($a . '.id', (int)$filters['search']);
			}
			else
			{
				$entries->whereLike($a . '.name', strtolower((string)$filters['search']), 1)
					->orWhereLike($a . '.username', strtolower((string)$filters['search']), 1)
					->orWhereLike($a . '.email', strtolower((string)$filters['search']), 1)
					->resetDepth();
			}
		}

		if ($filters['registerDate'])
		{
			$entries->where($a . '.registerDate', '>=', $filters['registerDate']);
		}

		if ($filters['access'] > 0)
		{
			$entries->whereEquals($a . '.access', (int)$filters['access']);
		}

		if (is_numeric($filters['state']))
		{
			$entries->whereEquals($a . '.block', (int)$filters['state']);
		}

		if (is_numeric($filters['approved']))
		{
			$entries->whereEquals($a . '.approved', (int)$filters['approved']);
		}

		if ($filters['activation'] < 0)
		{
			$entries->where($a . '.activation', '<', 0);
		}
		if ($filters['activation'] > 0)
		{
			$entries->where($a . '.activation', '>', 0);
		}

		// Apply the range filter.
		if ($filters['range'])
		{
			// Get UTC for now.
			$dNow = Date::of('now');
			$dStart = clone $dNow;

			switch ($filters['range'])
			{
				case 'past_week':
					$dStart->modify('-7 day');
					break;

				case 'past_1month':
					$dStart->modify('-1 month');
					break;

				case 'past_3month':
					$dStart->modify('-3 month');
					break;

				case 'past_6month':
					$dStart->modify('-6 month');
					break;

				case 'post_year':
				case 'past_year':
					$dStart->modify('-1 year');
					break;

				case 'today':
					// Ranges that need to align with local 'days' need special treatment.
					$offset = Config::get('offset');

					// Reset the start time to be the beginning of today, local time.
					$dStart = Date::of('now', $offset);
					$dStart->setTime(0, 0, 0);

					// Now change the timezone back to UTC.
					$tz = new \DateTimeZone('GMT');
					$dStart->setTimezone($tz);
					break;
			}

			if ($filters['range'] == 'post_year')
			{
				$entries->where($a . '.registerDate', '<', $dStart->format('Y-m-d H:i:s'));
			}
			else
			{
				$entries->where($a . '.registerDate', '>=', $dStart->format('Y-m-d H:i:s'));
				$entries->where($a . '.registerDate', '<=', $dNow->format('Y-m-d H:i:s'));
			}
		}

		// Get records
		$rows = $entries
			->order($a . '.' . $filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Access groups
		$accessgroups = Accessgroup::all()
			->ordered()
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('accessgroups', $accessgroups)
			->set('filters', $filters)
			->setLayout($this->getTask() == 'modal' ? 'modal' : 'display')
			->display();
	}

	/**
	 * Edit a member's information
	 *
	 * @param   object  $user
	 * @return  void
	 */
	public function editTask($user=null)
	{
		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.create', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			return $this->cancelTask();
		}

		Request::setVar('hidemainmenu', 1);

		if (!$user)
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			// Initiate database class and load info
			$user = Member::oneOrNew($id);
		}

		$password = \Hubzero\User\Password::getInstance($user->get('id'));

		// Get password rules
		// Get the password rule descriptions
		$password_rules = array();
		foreach (\Hubzero\Password\Rule::all()->whereEquals('enabled', 1)->rows() as $rule)
		{
			if (!empty($rule['description']))
			{
				$password_rules[] = $rule['description'];
			}
		}

		// Output the HTML
		$this->view
			->set('profile', $user)
			->set('password', $password)
			->set('password_rules', $password_rules)
			->set('validated', (isset($this->validated) ? $this->validated : false))
			->setErrors($this->getErrors())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry and return to main listing
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.create', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming profile edits
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

		// Load the profile
		$user = Member::oneOrNew($fields['id']);

		// Get the user before changes so we can
		// compare how data changed later on
		$prev = clone $user;

		// Set the incoming data
		$user->set($fields);

		if ($user->isNew())
		{
			$newUsertype = $this->config->get('new_usertype');

			if (!$newUsertype)
			{
				$newUsertype = Accessgroup::oneByTitle('Registered')->get('id');
			}

			$user->set('accessgroups', array($newUsertype));

			// Check that username is filled
			if (!Validate::username($user->get('username')))
			{
				Notify::error(Lang::txt('COM_MEMBERS_MEMBER_USERNAME_INVALID'));
				return $this->editTask($user);
			}

			// Check email is valid
			if (!Validate::email($user->get('email')))
			{
				Notify::error(Lang::txt('COM_MEMBERS_MEMBER_EMAIL_INVALID'));
				return $this->editTask($user);
			}

			// Set home directory
			$hubHomeDir = rtrim($this->config->get('homedir'), '/');
			if (!$hubHomeDir)
			{
				// try to deduce a viable home directory based on sitename or live_site
				$sitename = strtolower(Config::get('sitename'));
				$sitename = preg_replace('/^http[s]{0,1}:\/\//', '', $sitename, 1);
				$sitename = trim($sitename, '/ ');
				$sitename_e = explode('.', $sitename, 2);
				if (isset($sitename_e[1]))
				{
					$sitename = $sitename_e[0];
				}
				if (!preg_match("/^[a-zA-Z]+[\-_0-9a-zA-Z\.]+$/i", $sitename))
				{
					$sitename = '';
				}
				if (empty($sitename))
				{
					$sitename = strtolower(Request::base());
					$sitename = preg_replace('/^http[s]{0,1}:\/\//', '', $sitename, 1);
					$sitename = trim($sitename, '/ ');
					$sitename_e = explode('.', $sitename, 2);
					if (isset($sitename_e[1]))
					{
						$sitename = $sitename_e[0];
					}
					if (!preg_match("/^[a-zA-Z]+[\-_0-9a-zA-Z\.]+$/i", $sitename))
					{
						$sitename = '';
					}
				}

				$hubHomeDir = DS . 'home';

				if (!empty($sitename))
				{
					$hubHomeDir .= DS . $sitename;
				}
			}
			$user->set('homeDirectory', $hubHomeDir . DS . $user->get('username'));
			$user->set('loginShell', '/bin/bash');
			$user->set('ftpShell', '/usr/lib/sftp-server');

			$user->set('registerDate', Date::toSql());
		}

		// Set the new info
		$user->set('givenName', preg_replace('/\s+/', ' ', trim($fields['givenName'])));
		$user->set('middleName', preg_replace('/\s+/', ' ', trim($fields['middleName'])));
		$user->set('surname', preg_replace('/\s+/', ' ', trim($fields['surname'])));

		$name = array(
			$user->get('givenName'),
			$user->get('middleName'),
			$user->get('surname')
		);
		$name = implode(' ', $name);
		$name = preg_replace('/\s+/', ' ', $name);

		$user->set('name', $name);
		$user->set('modifiedDate', Date::toSql());

		if ($ec = Request::getInt('activation', 0, 'post'))
		{
			$user->set('activation', $ec);
		}
		else
		{
			$user->set('activation', Helpers\Utility::genemailconfirm());
		}

		// Can't block yourself
		if ($user->get('block') && $user->get('id') == User::get('id') && !User::get('block'))
		{
			Notify::error(Lang::txt('COM_USERS_USERS_ERROR_CANNOT_BLOCK_SELF'));
			return $this->editTask($user);
		}

		// Make sure that we are not removing ourself from Super Admin group
		$iAmSuperAdmin = User::authorise('core.admin');

		if ($iAmSuperAdmin && User::get('id') == $user->get('id'))
		{
			// Check that at least one of our new groups is Super Admin
			$stillSuperAdmin = false;

			foreach ($fields['accessgroups'] as $group)
			{
				$stillSuperAdmin = ($stillSuperAdmin ? $stillSuperAdmin : Access::checkGroup($group, 'core.admin'));
			}

			if (!$stillSuperAdmin)
			{
				Notify::error(Lang::txt('COM_USERS_USERS_ERROR_CANNOT_DEMOTE_SELF'));
				return $this->editTask($user);
			}
		}

		// Save the changes
		if (!$user->save())
		{
			Notify::error($user->getError());
			return $this->editTask($user);
		}

		// Save profile data
		$profile = Request::getVar('profile', array(), 'post', 'none', 2);
		$access  = Request::getVar('profileaccess', array(), 'post', 'none', 2);

		foreach ($profile as $key => $data)
		{
			if (isset($profile[$key]) && is_array($profile[$key]))
			{
				$profile[$key] = array_filter($profile[$key]);
			}
			if (isset($profile[$key . '_other']) && trim($profile[$key . '_other']))
			{
				if (is_array($profile[$key]))
				{
					$profile[$key][] = $profile[$key . '_other'];
				}
				else
				{
					$profile[$key] = $profile[$key . '_other'];
				}

				unset($profile[$key . '_other']);
			}
		}

		if (!$user->saveProfile($profile, $access))
		{
			Notify::error($user->getError());
			return $this->editTask($user);
		}

		// Do we have a new pass?
		$newpass = trim(Request::getVar('newpass', '', 'post'));

		if ($newpass)
		{
			// Get password rules and validate
			$password_rules = \Hubzero\Password\Rule::all()
					->whereEquals('enabled', 1)
					->rows();

			$validated = \Hubzero\Password\Rule::verify($newpass, $password_rules, $user->get('id'));

			if (!empty($validated))
			{
				// Set error
				Notify::error(Lang::txt('COM_MEMBERS_PASSWORD_DOES_NOT_MEET_REQUIREMENTS'));
				$this->validated = $validated;
				$this->_task = 'apply';
			}
			else
			{
				// Save password
				\Hubzero\User\Password::changePassword($user->get('username'), $newpass);
			}
		}

		$passinfo = \Hubzero\User\Password::getInstance($user->get('id'));

		if (is_object($passinfo))
		{
			// Do we have shadow info to change?
			$shadowMax     = Request::getInt('shadowMax', false, 'post');
			$shadowWarning = Request::getInt('shadowWarning', false, 'post');
			$shadowExpire  = Request::getVar('shadowExpire', '', 'post');

			if ($shadowMax || $shadowWarning || (!is_null($passinfo->get('shadowExpire')) && empty($shadowExpire)))
			{
				if ($shadowMax)
				{
					$passinfo->set('shadowMax', $shadowMax);
				}
				if ($shadowExpire || (!is_null($passinfo->get('shadowExpire')) && empty($shadowExpire)))
				{
					if (preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $shadowExpire))
					{
						$shadowExpire = strtotime($shadowExpire) / 86400;
						$passinfo->set('shadowExpire', $shadowExpire);
					}
					elseif (preg_match("/[0-9]+/", $shadowExpire))
					{
						$passinfo->set('shadowExpire', $shadowExpire);
					}
					elseif (empty($shadowExpire))
					{
						$passinfo->set('shadowExpire', null);
					}
				}
				if ($shadowWarning)
				{
					$passinfo->set('shadowWarning', $shadowWarning);
				}

				$passinfo->update();
			}
		}

		// Check for spam count
		$reputation = Request::getVar('spam_count', null, 'post');

		if (!is_null($reputation))
		{
			$user->reputation->set('spam_count', $reputation);
			$user->reputation->save();
		}

		// Email the user that their account has been approved
		if (!$prev->get('approved') && $this->config->get('useractivation_email'))
		{
			if (!$this->emailApprovedUser($user))
			{
				Notify::error(Lang::txt('COM_MEMBERS_ERROR_EMAIL_FAILED'));
			}
		}

		// Set success message
		Notify::success(Lang::txt('COM_MEMBERS_MEMBER_SAVED'));

		// Drop through to edit form?
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($user);
		}

		if ($this->getTask() == 'save2new')
		{
			return $this->editTask();
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Removes a profile entry, associated picture, and redirects to main listing
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		$i = 0;

		if (!empty($ids))
		{
			// Check if I am a Super Admin
			$iAmSuperAdmin = User::authorise('core.admin');

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				// Remove the profile
				$user = Member::oneOrFail(intval($id));

				// Access checks.
				$allow = User::authorise('core.delete', 'com_members');

				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && Access::check($user->get('id'), 'core.admin')) ? false : $allow;

				if (!$allow)
				{
					Notify::warning(Lang::txt('JERROR_CORE_DELETE_NOT_PERMITTED'));
					continue;
				}

				if (!$user->destroy())
				{
					Notify::error($user->getError());
					continue;
				}

				$i++;
			}
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_MEMBERS_MEMBER_REMOVED'));
		}

		// Output messsage and redirect
		$this->cancelTask();
	}

	/**
	 * Sets the account activation state of a member
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = ($this->getTask() == 'confirm' ? 1 : 0);

		// Incoming user ID
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have an ID?
		if (empty($ids))
		{
			Notify::warning(Lang::txt('COM_MEMBERS_NO_ID'));
			return $this->cancelTask();
		}

		$i = 0;

		foreach ($ids as $id)
		{
			// Load the profile
			$user = Member::oneOrFail(intval($id));

			if ($state)
			{
				$user->set('activation', $state);
			}
			else
			{
				$user->set('activation', Helpers\Utility::genemailconfirm());
			}

			if (!$user->save())
			{
				Notify::error($user->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_MEMBERS_CONFIRMATION_CHANGED'));
		}

		$this->cancelTask();
	}

	/**
	 * Sets the account approved state of a member
	 *
	 * @return  void
	 */
	public function approveTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = ($this->getTask() == 'approve' ? 2 : 0);

		// Incoming user ID
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have an ID?
		if (empty($ids))
		{
			Notify::warning(Lang::txt('COM_MEMBERS_NO_ID'));
			return $this->cancelTask();
		}

		$i = 0;

		foreach ($ids as $id)
		{
			// Load the profile
			$user = Member::oneOrFail(intval($id));

			// Extra, paranoid check that we only approve accounts that need it
			if (!$user->get('approved'))
			{
				$user->set('approved', $state);

				if (!$user->save())
				{
					Notify::error($user->getError());
					continue;
				}

				// Email the user that their account has been approved
				if ($this->config->get('useractivation_email'))
				{
					if (!$this->emailApprovedUser($user))
					{
						Notify::error(Lang::txt('COM_MEMBERS_ERROR_EMAIL_FAILED'));
					}
				}

				$i++;
			}
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_MEMBERS_APPROVED_STATUS_CHANGED'));
		}

		$this->cancelTask();
	}

	/**
	 * Send an email to a user
	 * stating their account has been approved
	 *
	 * @param   object  $user
	 * @return  bool
	 */
	protected function emailApprovedUser($user)
	{
		// Compute the mail subject.
		$emailSubject = Lang::txt(
			'COM_MEMBERS_APPROVED_USER_EMAIL_SUBJECT',
			$user->get('name'),
			Config::get('sitename')
		);

		// Compute the mail body.
		$eview = new \Hubzero\Mail\View(array(
			'base_path' => dirname(dirname(__DIR__)) . DS . 'site',
			'name'      => 'emails',
			'layout'    => 'approved_plain'
		));
		$eview->option     = $this->_option;
		$eview->controller = $this->_controller;
		$eview->config     = $this->config;
		$eview->baseURL    = Request::root();
		$eview->user       = $user;
		$eview->sitename   = Config::get('sitename');

		$plain = $eview->loadTemplate(false);
		$plain = str_replace("\n", "\r\n", $plain);

		$eview->setLayout('approved_html');
		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// Build the message and send it
		$mail = new \Hubzero\Mail\Message();
		$mail
			->addFrom(
				Config::get('mailfrom'),
				Config::get('fromname')
			)
			->addTo($user->get('email'))
			->setSubject($emailSubject);

		$mail->addPart($plain, 'text/plain');
		$mail->addPart($html, 'text/html');

		if (!$mail->send())
		{
			return false;
		}

		return true;
	}

	/**
	 * Sets the account blocked state of a member
	 *
	 * @return  void
	 */
	public function blockTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$state = ($this->getTask() == 'block' ? 1 : 0);

		// Incoming user ID
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have an ID?
		if (empty($ids))
		{
			Notify::warning(Lang::txt('COM_MEMBERS_NO_ID'));
			return $this->cancelTask();
		}

		$i = 0;

		foreach ($ids as $id)
		{
			// Load the profile
			$user = Member::oneOrFail(intval($id));
			$user->set('block', $state);

			if (!$user->save())
			{
				Notify::error($user->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_MEMBERS_BLOCK_STATUS_CHANGED'));
		}

		$this->cancelTask();
	}

	/**
	 * Resets the terms of use agreement for all users (requiring re-agreement)
	 *
	 * @return  void
	 */
	public function clearTermsTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Update registration config value to require re-agreeing upon next login
		$currentTOU = $this->config->get('registrationTOU', 'RHRH');
		$newTOU     = substr_replace($currentTOU, 'R', 2, 1);
		$this->config->set('registrationTOU', $newTOU);

		// Get db object
		$dbo = App::get('db');
		$migration = new \Hubzero\Content\Migration\Base($dbo);

		if (!$migration->saveParams('com_members', $this->config))
		{
			Notify::error(Lang::txt('COM_MEMBERS_FAILED_TO_UPDATE_REGISTRATION_TOU'));

			return $this->cancelTask();
		}

		// Clear all old TOU states
		if (!Member::clearTerms())
		{
			Notify::error(Lang::txt('COM_MEMBERS_FAILED_TO_CLEAR_TOU'));

			return $this->cancelTask();
		}

		// Output message to let admin know everything went well
		Notify::success(Lang::txt('COM_MEMBERS_SUCESSFULLY_CLEARED_TOU'));

		$this->cancelTask();
	}

	/**
	 * Return results for autocompleter
	 *
	 * @return  void
	 */
	public function autocompleteTask()
	{
		if (User::isGuest())
		{
			return;
		}

		$filters = array(
			'limit'  => 20,
			'start'  => 0,
			'search' => strtolower(trim(Request::getString('value', '')))
		);

		// Fetch results
		$entries = Member::all()
			->whereEquals('block', 0);

		if ($filters['search'])
		{
			$entries->whereLike('name', strtolower((string)$filters['search']), 1)
				->orWhereLike('username', strtolower((string)$filters['search']), 1)
				->orWhereLike('email', strtolower((string)$filters['search']), 1)
				->resetDepth();
		}

		$rows = $entries
			->order('name', 'asc')
			->limit($filters['limit'])
			->rows();

		// Output search results in JSON format
		$json = array();

		foreach ($rows as $row)
		{
			$obj = array();
			$obj['id']      = $row->get('id');
			$obj['name']    = str_replace(array("\n", "\r", '\\'), '', $row->get('name'));
			$obj['picture'] = $row->picture();

			$json[] = $obj;
		}

		echo json_encode($json);
	}

	/**
	 * Download a picture
	 *
	 * @return  void
	 */
	public function pictureTask()
	{
		// Get vars
		$id = Request::getInt('id', 0);

		// Check to make sure we have an id
		if (!$id || $id == 0)
		{
			return;
		}

		// Load member
		$member = Member::oneOrFail($id);

		$file  = DS . trim($this->config->get('webpath', '/site/members'), DS);
		$file .= DS . Profile\Helper::niceidformat($member->get('uidNumber'));
		$file .= DS . Request::getVar('image', $member->get('picture'));

		// Ensure the file exist
		if (!file_exists(PATH_APP . DS . $file))
		{
			App::abort(404, Lang::txt('COM_MEMBERS_FILE_NOT_FOUND') . ' ' . $file);
		}

		// Serve up the image
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename(PATH_APP . DS . $file);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support

		// Serve up file
		if (!$xserver->serve())
		{
			// Should only get here on error
			App::abort(404, Lang::txt('COM_MEMBERS_MEDIA_ERROR_SERVING_FILE'));
		}

		exit;
	}

	/**
	 * Debug user permissions
	 *
	 * @return  void
	 */
	public function debuguserTask()
	{
		include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'debug.php';

		// Get filters
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'lft'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'level_start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.filter_level_start',
				'filter_level_start',
				0,
				'int'
			),
			'level_end' => Request::getState(
				$this->_option . '.' . $this->_controller . '.filter_level_end',
				'filter_level_end',
				0,
				'int'
			),
			'component' => Request::getState(
				$this->_option . '.' . $this->_controller . '.filter_component',
				'filter_component',
				''
			)
		);

		if ($filters['level_end'] > 0 && $filters['level_end'] < $filters['level_start'])
		{
			$filters['level_end'] = $filters['level_start'];
		}

		$id = Request::getInt('id', 0);

		// Load member
		$member = Member::oneOrFail($id);

		// Select the required fields from the table.
		$entries = \Hubzero\Access\Asset::all();

		if ($filters['search'])
		{
			$entries->whereLike('name', $filters['search'], 1)
				->orWhereLike('title', $filters['search'], 1)
				->resetDepth();
		}

		if ($filters['level_start'] > 0)
		{
			$entries->where('level', '>=', $filters['level_start']);
		}
		if ($filters['level_end'] > 0)
		{
			$entries->where('level', '<=', $filters['level_end']);
		}

		// Filter the items over the component if set.
		if ($filters['component'])
		{
			$entries->whereEquals('name', $filters['component'], 1)
				->orWhereLike('name', $filters['component'], 1)
				->resetDepth();
		}

		$assets = $entries
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated()
			->rows();

		$actions = \Components\Members\Helpers\Debug::getActions($filters['component']);

		$data = $assets->raw();
		$assets->clear();

		foreach ($data as $key => $asset)
		{
			$checks = array();

			foreach ($actions as $action)
			{
				$name  = $action[0];
				$level = $action[1];

				// Check that we check this action for the level of the asset.
				if ($action[1] === null || $action[1] >= $asset->get('level'))
				{
					// We need to test this action.
					$checks[$name] = Access::check($id, $action[0], $asset->get('name'));
				}
				else
				{
					// We ignore this action.
					$checks[$name] = 'skip';
				}
			}

			$asset->set('checks', $checks);

			$assets->push($asset);
		}

		$levels     = \Components\Members\Helpers\Debug::getLevelsOptions();
		$components = \Components\Members\Helpers\Debug::getComponents();

		// Output the HTML
		$this->view
			->set('user', $member)
			->set('filters', $filters)
			->set('assets', $assets)
			->set('actions', $actions)
			->set('levels', $levels)
			->set('components', $components)
			->display();
	}

	/**
	 * Show a form for building a profile schema
	 *
	 * @return  void
	 */
	public function profileTask()
	{
		Request::setVar('hidemainmenu', 1);

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option))
		{
			return $this->cancelTask();
		}

		$fields = Field::all()
			->including(['options', function ($option){
				$option
					->select('*')
					->ordered();
			}])
			->ordered()
			->rows();

		$this->view
			->set('fields', $fields)
			->setLayout('profile')
			->display();
	}

	/**
	 * Save profile schema
	 *
	 * @return  void
	 */
	public function saveprofileTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option))
		{
			return $this->cancelTask();
		}

		// Incoming data
		$profile = json_decode(Request::getVar('profile', '{}', 'post', 'none', 2));

		// Get the old schema
		$fields = Field::all()
			->including(['options', function ($option){
				$option
					->select('*')
					->ordered();
			}])
			->ordered()
			->rows();

		// Collect old fields
		$oldFields = array();
		foreach ($fields as $oldField)
		{
			$oldFields[$oldField->get('id')] = $oldField;
		}

		foreach ($profile->fields as $i => $element)
		{
			$field = null;

			$fid = (isset($element->field_id) ? $element->field_id : 0);

			if ($fid && isset($oldFields[$fid]))
			{
				$field = $oldFields[$fid];

				// Remove found fields from the list
				// Anything remaining will be deleted
				unset($oldFields[$fid]);
			}

			$field = ($field ?: Field::oneOrNew($fid));
			$field->set(array(
				'type'          => (string) $element->field_type,
				'label'         => (string) $element->label,
				'name'          => (string) $element->name,
				'description'   => (isset($element->field_options->description) ? (string) $element->field_options->description : ''),
				/*'required'     => (isset($element->required) ? (int) $element->required : 0),
				'readonly'     => (isset($element->readonly) ? (int) $element->readonly : 0),
				'disabled'     => (isset($element->disabled) ? (int) $element->disabled : 0),*/
				'ordering'      => ($i + 1),
				'access'        => (isset($element->access) ? (int) $element->access : 0),
				'option_other'  => (isset($element->field_options->include_other_option) ? (int) $element->field_options->include_other_option : ''),
				'option_blank'  => (isset($element->field_options->include_blank_option) ? (int) $element->field_options->include_blank_option : ''),
				'action_create' => (isset($element->create) ? (int) $element->create : 1),
				'action_update' => (isset($element->update) ? (int) $element->update : 1),
				'action_edit'   => (isset($element->edit)   ? (int) $element->edit   : 1),
				'action_browse' => (isset($element->browse) ? (int) $element->browse : 0)
			));

			if ($field->get('type') == 'dropdown')
			{
				$field->set('type', 'select');
			}
			if ($field->get('type') == 'paragraph')
			{
				$field->set('type', 'textarea');
			}

			if (!$field->save())
			{
				Notify::error($field->getError());
				continue;
			}

			// Collect old options
			$oldOptions = array();
			foreach ($field->options as $oldOption)
			{
				$oldOptions[$oldOption->get('id')] = $oldOption;
			}

			// Does this field have any set options?
			if (isset($element->field_options->options))
			{
				foreach ($element->field_options->options as $k => $opt)
				{
					$option = null;

					$oid = (isset($opt->field_id) ? $opt->field_id : 0);

					if ($oid && isset($oldOptions[$oid]))
					{
						$option = $oldOptions[$oid];

						// Remove found options from the list
						// Anything remaining will be deleted
						unset($oldOptions[$oid]);
					}

					$dependents = array();
					if (isset($opt->dependents))
					{
						$dependents = explode(',', trim($opt->dependents));
						$dependents = array_map('trim', $dependents);
						foreach ($dependents as $j => $dependent)
						{
							if (!$dependent)
							{
								unset($dependents[$j]);
							}
						}
					}

					$option = ($option ?: Option::oneOrNew($oid));
					$option->set(array(
						'field_id'   => $field->get('id'),
						'label'      => (string) $opt->label,
						'value'      => (isset($opt->value)   ? (string) $opt->value : ''),
						'checked'    => (isset($opt->checked) ? (int) $opt->checked : 0),
						'ordering'   => ($k + 1),
						'dependents' => json_encode($dependents)
					));

					if (!$option->save())
					{
						Notify::error($option->getError());
						continue;
					}
				}
			}

			// Remove any options not in the incoming list
			foreach ($oldOptions as $option)
			{
				if (!$option->destroy())
				{
					Notify::error($option->getError());
					continue;
				}
			}
		}

		// Remove any fields not in the incoming list
		foreach ($oldFields as $field)
		{
			if (!$field->destroy())
			{
				Notify::error($field->getError());
				continue;
			}
		}

		// Set success message
		Notify::success(Lang::txt('COM_MEMBERS_PROFILE_SCHEMA_SAVED'));

		// Drop through to edit form?
		if ($this->getTask() == 'applyprofile')
		{
			// Redirect, instead of falling through, to avoid caching issues
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=profile', false)
			);
		}

		// Redirect
		$this->cancelTask();
	}
}
