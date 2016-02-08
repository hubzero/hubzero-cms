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

namespace Components\Members\Models\Import;

use Exception;
use stdClass;
use Component;
use Request;
use Config;
use Lang;
use User;
use Date;

include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'profile.php';
include_once dirname(__DIR__) . DS . 'tags.php';
include_once dirname(__DIR__) . DS . 'registration.php';

/**
 * Member Record importer
 */
class Record extends \Hubzero\Content\Import\Model\Record
{
	/**
	 * Profile
	 *
	 * @var  object
	 */
	private $_profile;

	/**
	 *  Constructor
	 *
	 * @param   mixes   $raw      Raw data
	 * @param   array   $options  Import options
	 * @param   string  $mode     Operation mode (update|patch)
	 * @return  void
	 */
	public function __construct($raw, $options = array(), $mode = 'UPDATE')
	{
		// store our incoming data
		$this->raw      = $raw;
		$this->_options = $options;
		$this->_mode    = strtoupper($mode);

		// Core objects
		$this->_database = \App::get('db');
		$this->_user     = User::getRoot();

		// Create objects
		$this->record = new stdClass;
		$this->record->entry = new \Components\Members\Tables\Profile($this->_database);
		$this->_profile = new \Hubzero\User\Profile();
		$this->record->tags  = array();

		// Messages
		$this->record->errors  = array();
		$this->record->notices = array();

		// bind data
		$this->bind();
	}

	/**
	 * Bind all raw data
	 *
	 * @return  $this  Current object
	 */
	public function bind()
	{
		// Wrap in try catch to avoid breaking in middle of import
		try
		{
			// Map profile data
			$this->_mapEntryData();

			// Map tags
			$this->_mapTagsData();

			// Map groups
			$this->_mapGroupsData();
		}
		catch (Exception $e)
		{
			array_push($this->record->errors, $e->getMessage());
		}

		return $this;
	}

	/**
	 * Check Data integrity
	 *
	 * @return  $this  Current object
	 */
	public function check()
	{
		// Run save check method
		if (!$this->record->entry->check())
		{
			array_push($this->record->errors, $this->record->entry->getError());
			return $this;
		}

		$xregistration = new \Components\Members\Models\Registration();
		$xregistration->loadProfile($this->_profile);

		// Check that required fields were filled in properly
		if (!$xregistration->check('edit', $this->_profile->get('uidNumber'), array()))
		{
			$skip = array();

			if (!empty($xregistration->_missing))
			{
				foreach ($xregistration->_missing as $key => $missing)
				{
					if ($this->_mode == 'PATCH')
					{
						$skip[] = $key;
						continue;
					}

					array_push($this->record->errors, $missing);
				}
			}
			if (!empty($xregistration->_invalid))
			{
				foreach ($xregistration->_invalid as $key => $invalid)
				{
					if (in_array($key, $skip))
					{
						continue;
					}

					array_push($this->record->errors, $invalid);
				}
			}
		}

		return $this;
	}

	/**
	 * Store data
	 *
	 * @param   integer  $dryRun  Dry Run mode
	 * @return  $this    Current object
	 */
	public function store($dryRun = 1)
	{
		// Are we running in dry run mode?
		if ($dryRun || count($this->record->errors) > 0)
		{
			return $this;
		}

		// Attempt to save all data
		// Wrap in try catch to avoid break mid import
		try
		{
			// Save profile
			$this->_saveEntryData();

			// Save tags
			$this->_saveTagsData();

			// Save groups
			$this->_saveGroupsData();
		}
		catch (Exception $e)
		{
			array_push($this->record->errors, $e->getMessage());
		}

		return $this;
	}

	/**
	 * Map raw data to profile object
	 *
	 * @return  void
	 */
	private function _mapEntryData()
	{
		// Do we have an ID?
		// Either passed in the raw data or gotten from the title match
		if (isset($this->raw->uidNumber) && $this->raw->uidNumber > 1)
		{
			$this->record->entry->load($this->raw->uidNumber);
		}
		else if (isset($this->raw->username) && $this->raw->username)
		{
			$this->record->entry->loadByUsername($this->raw->username);
		}

		if (!$this->record->entry->get('uidNumber'))
		{
			$this->raw->registerDate = Date::toSql();
		}

		// Set modified date/user
		$this->raw->modifiedDate = Date::toSql();

		foreach (get_object_vars($this->raw) as $key => $val)
		{
			// These two need some extra loving and care, so we skip them for now...
			if (substr($key, 0, 1) == '_' || $key == 'username' || $key == 'uidNumber' || $key == 'groups')
			{
				continue;
			}

			// In PATCH mode, skip fields with no values
			if ($this->_mode == 'PATCH' && !$val)
			{
				continue;
			}

			$this->record->entry->set($key, $val);
		}

		// Set multi-value fields
		//
		// This will split a string based on delimiter(s) and turn the 
		// values into an array.
		foreach (array('disability', 'race', 'hispanic') as $key)
		{
			if (isset($this->raw->$key))
			{
				// In PATCH mode, skip fields with no values
				if ($this->_mode == 'PATCH' && !$this->raw->$key)
				{
					continue;
				}

				$this->record->$key = $this->_multiValueField($this->raw->$key);
				$this->record->entry->set($key, $this->record->$key);
			}
		}

		// If we have a name but no individual parts...
		if (!$this->record->entry->get('givenName') && !$this->record->entry->get('surame') && $this->record->entry->get('name'))
		{
			$name = explode(' ', $this->record->entry->get('name'));
			$this->record->entry->set('givenName',  array_shift($name));
			$this->record->entry->set('surname',    array_pop($name));
			$this->record->entry->set('middleName', implode(' ', $name));
		}

		// If we have the individual name parts but not the combined whole...
		if (($this->record->entry->get('givenName') || $this->record->entry->get('surame')) && !$this->record->entry->get('name'))
		{
			$name = array(
				$this->record->entry->get('givenName'),
				$this->record->entry->get('middleName'),
				$this->record->entry->get('surname')
			);
			$this->record->entry->set('name', implode(' ', $name));
		}

		// If we're updating an existing record...
		if ($this->record->entry->get('uidNumber'))
		{
			// Check if the username passed if the same for the record we're updating
			$username = $this->record->entry->get('username');
			if ($username && isset($this->raw->username) && $username != $this->raw->username)
			{
				// Uh-oh. Notify the user.
				array_push($this->record->notices, Lang::txt('Usernames for existing members cannot be changed at this time.'));
			}
		}
		else if (isset($this->raw->username) && $this->raw->username)
		{
			$this->record->entry->set('username', $this->raw->username);
		}

		// Bind to the profile object
		foreach ($this->record->entry->getProperties() as $key => $val)
		{
			$this->_profile->set($key, $val);
		}
	}

	/**
	 * Split a string into multiple values based on delimiter(s)
	 *
	 * @param   mixed   $data   String or array of field values
	 * @param   string  $delim  List of delimiters, separated by a pipe "|"
	 * @return  array
	 */
	private function _multiValueField($data, $delim=',|;')
	{
		if (is_string($data))
		{
			$data = array_map('trim', preg_split("/($delim)/", $data));
			$data = array_values(array_filter($data));
		}

		return $data;
	}

	/**
	 * Save profile
	 *
	 * @return  void
	 */
	private function _saveEntryData()
	{
		$isNew = (!$this->_profile->get('uidNumber'));

		if (!isset($this->raw->password))
		{
			$this->raw->password = null;
		}

		if ($isNew)
		{
			if (!$this->_profile->get('username'))
			{
				$valid = false;

				// Try to create from name
				$username = preg_replace('/[^a-z9-0_]/i', '', strtolower($this->_profile->get('name')));
				if (\Hubzero\Utility\Validate::username($username))
				{
					if (!$this->_usernameExists($username))
					{
						$valid = true;
					}
				}

				// Try to create from portion preceeding @ in email address
				if (!$valid)
				{
					$username = strstr($this->_profile->get('email'), '@', true);
					if (\Hubzero\Utility\Validate::username($username))
					{
						if ($this->_usernameExists($username))
						{
							$valid = true;
						}
					}
				}

				// Try to create from whole email address
				if (!$valid)
				{
					for ($i = 0; $i <= 99; $i++)
					{
						$username = preg_replace('/[^a-z9-0_]/i', '', strtolower($this->_profile->get('name'))) . $i;
						if (\Hubzero\Utility\Validate::username($username))
						{
							if ($this->_usernameExists($username))
							{
								$valid = true;
								break;
							}
						}
					}
				}

				if ($valid)
				{
					$this->_profile->set('username', $username);
				}
			}

			if (!$this->raw->password)
			{
				//\Hubzero\User\Helper::random_password();
				$this->raw->password = $this->_profile->get('username');
			}

			$usersConfig = Component::params('com_users');
			$newUsertype = $usersConfig->get('new_usertype');
			if (!$newUsertype)
			{
				$db = \App::get('db');
				$query = $db->getQuery(true)
					->select('id')
					->from('#__usergroups')
					->where('title = "Registered"');
				$db->setQuery($query);
				$newUsertype = $db->loadResult();
			}

			$user = User::getRoot();
			$user->set('username', $this->_profile->get('username'));
			$user->set('name', $this->_profile->get('name'));
			$user->set('email', $this->_profile->get('email'));
			$user->set('id', 0);
			$user->set('groups', array($newUsertype));
			$user->set('registerDate', Date::of('now')->toSql());
			$user->set('password', $this->raw->password);
			$user->set('password_clear', $this->raw->password);
			$user->save();
			$user->set('password_clear', '');

			// Attempt to get the new user
			$profile = \Hubzero\User\Profile::getInstance($user->get('id'));
			$result  = is_object($profile);

			// Did we successfully create an account?
			if ($result)
			{
				if (!$this->record->entry->get('emailConfirmed', null))
				{
					$this->_profile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
				}
				$this->_profile->set('uidNumber', $user->get('id'));
				$this->_profile->set('gidNumber', $profile->get('gidNumber'));

				if (!$this->_profile->get('homeDirectory'))
				{
					$this->_profile->set('homeDirectory', $profile->get('homeDirectory'));
				}
				if (!$this->_profile->get('loginShell'))
				{
					$this->_profile->set('loginShell', $profile->get('loginShell'));
				}
				if (!$this->_profile->get('ftpShell'))
				{
					$this->_profile->set('ftpShell', $profile->get('ftpShell'));
				}
				if (!$this->_profile->get('jobsAllowed'))
				{
					$this->_profile->set('jobsAllowed', $profile->get('jobsAllowed'));
				}
			}
		}

		if (!$this->_profile->store())
		{
			throw new Exception(Lang::txt('Unable to save the entry data.'));
		}

		if ($password = $this->raw->password)
		{
			/*if ($isNew)
			{
				// We need to bypass any hashing
				$this->raw->password = '*';
				\Hubzero\User\Password::changePasshash($this->_profile->get('uidNumber'), $password);
			}
			else
			{*/
				\Hubzero\User\Password::changePassword($this->_profile->get('uidNumber'), $password);
			//}
		}

		\Hubzero\User\Password::expirePassword($this->_profile->get('uidNumber'));

		if ($isNew && $this->_options['emailnew'] == 1)
		{
			$eview = new \Hubzero\Component\View(array(
				'base_path' => PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'site',
				'name'      => 'emails',
				'layout'    => 'confirm'
			));
			$eview->option       = 'com_members';
			$eview->controller   = 'register';
			$eview->sitename     = Config::get('sitename');
			$eview->login        = $this->_profile->get('username');
			$eview->name         = $this->_profile->get('name');
			$eview->registerDate = $this->_profile->get('registerDate');
			$eview->confirm      = $this->_profile->get('emailConfirmed');
			$eview->baseURL      = Request::base();

			$msg = new \Hubzero\Mail\Message();
			$msg->setSubject(Config::get('sitename') . ' ' . Lang::txt('COM_MEMBERS_REGISTER_EMAIL_CONFIRMATION'))
			    ->addTo($this->_profile->get('email'))
			    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
			    ->addHeader('X-Component', 'com_members');

			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			$msg->addPart($message, 'text/plain');

			$eview->setLayout('confirm_html');
			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			$msg->addPart($message, 'text/html');

			if (!$msg->send())
			{
				array_push($this->record->errors, Lang::txt('COM_MEMBERS_REGISTER_ERROR_EMAILING_CONFIRMATION'));
			}
		}
	}

	/**
	 * Map Tags
	 *
	 * @return  void
	 */
	private function _usernameExists($username)
	{
		$db = \App::get('db');
		$query = $db->getQuery(true)
			->select('id')
			->from('#__users')
			->where('username=' . $qb->quote($username));
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Map Tags
	 *
	 * @return  void
	 */
	private function _mapTagsData()
	{
		if (isset($this->raw->interests))
		{
			$this->record->tags = $this->_multiValueField($this->raw->interests);
		}
	}

	/**
	 * Save tags
	 *
	 * @return  void
	 */
	private function _saveTagsData()
	{
		if ($this->_mode == 'PATCH' && !$this->record->tags)
		{
			return;
		}

		// save tags
		$tags = new \Components\Members\Models\Tags($this->_profile->get('uidNumber'));
		$tags->setTags($this->record->tags, $this->_user->get('id'));
	}

	/**
	 * Map groups
	 *
	 * @return  void
	 */
	private function _mapGroupsData()
	{
		if (isset($this->raw->groups))
		{
			$this->record->groups = (array)$this->_multiValueField($this->raw->groups);

			foreach ($this->record->groups as $i => $gid)
			{
				$gid = trim($gid, '"');
				$gid = trim($gid, "'");

				$this->record->groups[$i] = $gid;

				$group = \Hubzero\User\Group::getInstance($gid);
				if (!$group || !$group->get('gidNumber'))
				{
					array_push($this->record->errors, Lang::txt('COM_MEMBERS_IMPORT_ERROR_GROUP_NOT_FOUND', $gid));
				}
			}
		}
	}

	/**
	 * Save groups
	 *
	 * @return  void
	 */
	private function _saveGroupsData()
	{
		if (!isset($this->record->groups))
		{
			return;
		}

		if ($this->_mode == 'PATCH' && !$this->record->groups)
		{
			return;
		}

		$id = $this->_profile->get('uidNumber');

		// Get all the user's current groups
		$existing = $this->_profile->getGroups();
		$gids = array();
		if ($existing)
		{
			foreach ($existing as $e)
			{
				$gids[] = $existing->gidNumber;
			}
		}

		// Add user to specified groups
		$added = array();
		foreach ($this->record->groups as $gid)
		{
			$group = \Hubzero\User\Group::getInstance($gid);
			if (!$group || !$group->get('gidNumber'))
			{
				array_push($this->record->errors, Lang::txt('COM_MEMBERS_IMPORT_ERROR_GROUP_NOT_FOUND', $gid));
			}

			// Track groups added to
			$added[] = $group->get('gidNumber');

			// No need to add if already in the group
			if (in_array($group->get('gidNumber'), $gids))
			{
				continue;
			}

			$group->add('members', array($id));
			$group->update();
		}

		// Remove user from all old groups that weren't in the new list
		foreach ($gids as $gid)
		{
			if (in_array($gid, $added))
			{
				continue;
			}

			$group = \Hubzero\User\Group::getInstance($gid);
			if (!$group || !$group->get('gidNumber'))
			{
				continue;
			}

			$group->remove('members', array($id));
			$group->remove('managers', array($id));
			$group->remove('invitees', array($id));
			$group->remove('applicants', array($id));
			$group->update();
		}
	}
}