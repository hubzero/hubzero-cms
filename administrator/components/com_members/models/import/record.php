<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Members\Models\Import;

use Exception;
use stdClass;
use JText;
use JFactory;
use JUser;
use JRoute;
use JURI;
use JParameter;
use JComponentHelper;

include_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'profile.php';
include_once JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'tags.php';
include_once JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'registration.php';

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
		$this->_mode    = $mode;

		// Core objects
		$this->_database = JFactory::getDBO();
		$this->_user     = JFactory::getUser();

		// Create objects
		$this->record = new stdClass;
		$this->record->entry = new \MembersProfile($this->_database);
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

		$xregistration = new \MembersModelRegistration();
		$xregistration->loadProfile($this->_profile);

		// Check that required fields were filled in properly
		if (!$xregistration->check('edit', $this->_profile->get('uidNumber'), array()))
		{
			if (!empty($xregistration->_missing))
			{
				foreach ($xregistration->_missing as $missing)
				{
					array_push($this->record->errors, $missing);
				}
			}
			if (!empty($xregistration->_invalid))
			{
				foreach ($xregistration->_invalid as $invalid)
				{
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
			$this->raw->registerDate = JFactory::getDate()->toSql();
		}

		// Set modified date/user
		$this->raw->modifiedDate = JFactory::getDate()->toSql();

		foreach (get_object_vars($this->raw) as $key => $val)
		{
			// These two need some extra loving and care, so we skip them for now...
			if (substr($key, 0, 1) == '_' || $key == 'username' || $key == 'uidNumber')
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
			if ($username && $username != $this->raw->username)
			{
				// Uh-oh. Notify the user.
				array_push($this->record->notices, JText::_('Usernames for existing members cannot be changed at this time.'));
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

		if ($isNew && !$this->raw->password)
		{
			//\Hubzero\User\Helper::random_password();
			$this->raw->password = $this->_profile->get('username');
		}

		if ($isNew)
		{
			$usersConfig = JComponentHelper::getParams('com_users');
			$newUsertype = $usersConfig->get('new_usertype');
			if (!$newUsertype)
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select('id')
					->from('#__usergroups')
					->where('title = "Registered"');
				$db->setQuery($query);
				$newUsertype = $db->loadResult();
			}

			$date = JFactory::getDate();
			$user = JUser::getInstance();
			$user->set('username', $this->_profile->get('username'));
			$user->set('name', $this->_profile->get('name'));
			$user->set('email', $this->_profile->get('email'));
			$user->set('id', 0);
			$user->set('groups', array($newUsertype));
			$user->set('registerDate', $date->toMySQL());
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
				$this->_profile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
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
			throw new Exception(JText::_('Unable to save the entry data.'));
		}

		if ($password = $this->raw->password)
		{
			if ($isNew)
			{
				// We need to bypass any hashing
				$this->raw->password = '*';
				\Hubzero\User\Password::changePasshash($this->_profile->get('uidNumber'), $password);
			}
			else
			{
				\Hubzero\User\Password::changePassword($this->_profile->get('uidNumber'), $password);
			}
		}

		if ($isNew && $this->_options['emailnew'] == 1)
		{
			$jconfig = JFactory::getConfig();

			$eview = new \Hubzero\Component\View(array(
				'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_members',
				'name'      => 'emails',
				'layout'    => 'confirm'
			));
			$eview->option       = 'com_members';
			$eview->controller   = 'register';
			$eview->sitename     = $jconfig->getValue('config.sitename');
			$eview->login        = $this->_profile->get('username');
			$eview->name         = $this->_profile->get('name');
			$eview->registerDate = $this->_profile->get('registerDate');
			$eview->confirm      = $this->_profile->get('emailConfirmed');
			$eview->baseURL      = JURI::base();

			$msg = new \Hubzero\Mail\Message();
			$msg->setSubject($jconfig->getValue('config.sitename') .' ' . JText::_('COM_MEMBERS_REGISTER_EMAIL_CONFIRMATION'))
			    ->addTo($this->_profile->get('email'))
			    ->addFrom($jconfig->getValue('config.mailfrom'), $jconfig->getValue('config.sitename') . ' Administrator')
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
				array_push($this->record->errors, JText::sprintf('COM_MEMBERS_REGISTER_ERROR_EMAILING_CONFIRMATION'));
			}
		}
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
		// save tags
		$tags = new \MembersModelTags($this->_profile->get('uidNumber'));
		$tags->setTags($this->record->tags, $this->_user->get('id'));
	}
}