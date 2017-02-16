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

use Components\Members\Models\Member;
use Hubzero\Utility\Validate;
use Exception;
use stdClass;
use Component;
use Request;
use Config;
use Lang;
use User;
use Date;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'member.php';
include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'profile' . DS . 'field.php';
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
	 * @var  array
	 */
	private $_profile = array();

	/**
	 * Handlers instances container
	 *
	 * @var  array
	 */
	protected static $handlers = array();

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
		//$this->_user    = User::getInstance();
		$this->_profile = array();

		// Create objects
		$this->record = new stdClass;
		$this->record->entry = new \Components\Members\Models\Member();
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

			// Map extras
			$this->_mapExtraData();
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
		/*if (!$this->record->entry->check())
		{
			array_push($this->record->errors, $this->record->entry->getError());
			return $this;
		}*/

		$xregistration = new \Components\Members\Models\Registration();
		$xregistration->loadProfile($this->record->entry);

		// Check that required fields were filled in properly
		if (!$xregistration->check('edit', $this->record->entry->get('id'), array()))
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

		$keys = array();
		if ($this->_mode == 'PATCH')
		{
			foreach ($this->_profile as $k => $p)
			{
				if (!$p)
				{
					continue;
				}

				if (is_array($p) && empty($p))
				{
					continue;
				}

				$keys[] = $k;
			}
		}

		// Validate profile data
		$f = \Components\Members\Models\Profile\Field::all()
			->including(['options', function ($option){
				$option
					->select('*');
			}])
			->where('action_edit', '!=', \Components\Members\Models\Profile\Field::STATE_HIDDEN);

		if (!empty($keys))
		{
			$f->whereIn('name', $keys);
		}

		$fields = $f
			->ordered()
			->rows();

		$form = new \Hubzero\Form\Form('profile', array('control' => 'profile'));
		$form->load(\Components\Members\Models\Profile\Field::toXml($fields, 'edit'));
		$form->bind(new \Hubzero\Config\Registry($this->_profile));

		if (!$form->validate($this->_profile))
		{
			foreach ($form->getErrors() as $key => $error)
			{
				array_push($this->record->errors, (string)$error);
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
			$entry = $this->record->entry->toArray();

			$this->record->entry = new stdClass;

			foreach ($entry as $field => $value)
			{
				$this->record->entry->$field = $value;
			}
			foreach ($this->_profile as $field => $value)
			{
				$this->record->entry->$field = $value;
			}

			return $this;
		}

		// Attempt to save all data
		// Wrap in try catch to avoid break mid import
		try
		{
			// Save profile
			$this->_saveEntryData();

			// Save extras
			$this->_saveExtraData();
		}
		catch (Exception $e)
		{
			array_push($this->record->errors, $e->getMessage());
		}

		$entry = $this->record->entry->toArray();

		$this->record->entry = new stdClass;

		foreach ($entry as $field => $value)
		{
			$this->record->entry->$field = $value;
		}

		//$this->record->entry = $this->record->entry->toObject();
		foreach ($this->_profile as $field => $value)
		{
			$this->record->entry->$field = $value;
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
			$this->record->entry = Member::oneOrNew($this->raw->uidNumber);
		}
		else if (isset($this->raw->id) && $this->raw->id > 1)
		{
			$this->record->entry = Member::oneOrNew($this->raw->id);
		}
		else if (isset($this->raw->username) && $this->raw->username)
		{
			$this->record->entry = Member::oneByUsername($this->raw->username);
		}

		$d = Date::of('now');

		if (isset($this->raw->registerDate))
		{
			try
			{
				$d = Date::of($this->raw->registerDate);
			}
			catch (Exception $e)
			{
				array_push($this->record->errors, $e->getMessage());
			}
			$this->raw->registerDate = $d->toSql();
		}

		if (!$this->record->entry->get('id') && !isset($this->raw->registerDate))
		{
			$this->raw->registerDate = $d->toSql();
		}

		// Set modified date/user
		$this->raw->modifiedDate = Date::of('now')->toSql();

		$columns = $this->record->entry->getStructure()->getTableColumns($this->record->entry->getTableName());

		foreach (get_object_vars($this->raw) as $key => $val)
		{
			// These two need some extra loving and care, so we skip them for now...
			if (substr($key, 0, 1) == '_' || $key == 'username' || $key == 'uidNumber' || $this->hasHandler($key))
			{
				continue;
			}

			if (function_exists('mb_convert_encoding'))
			{
				$val = mb_convert_encoding($val, 'UTF-8');
			}

			// In PATCH mode, skip fields with no values
			if ($this->_mode == 'PATCH' && !$val)
			{
				continue;
			}

			if (isset($columns[$key]))
			{
				$this->record->entry->set($key, $val);
			}
			else
			{
				$this->_profile[$key] = $val;
			}
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
				if ($this->_mode == 'PATCH' && (!isset($this->_profile[$key]) || !$this->_profile[$key]))
				{
					continue;
				}

				$this->_profile[$key] = $this->_multiValueField($this->_profile[$key]);
			}
		}

		// If we have a name but no individual parts...
		if (!$this->record->entry->get('givenName') && !$this->record->entry->get('surame') && $this->record->entry->get('name'))
		{
			$name = explode(' ', $this->record->entry->get('name'));
			$this->record->entry->set('givenName', array_shift($name));
			$this->record->entry->set('surname', array_pop($name));
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
		if ($this->record->entry->get('id'))
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
		$isNew = (!$this->record->entry->get('id'));

		if (!isset($this->raw->password))
		{
			$this->raw->password = null;
		}

		if ($isNew)
		{
			if (!$this->record->entry->get('username'))
			{
				$valid = false;

				// Try to create from name
				$username = preg_replace('/[^a-z9-0_]/i', '', strtolower($this->record->entry->get('name')));
				if (Validate::username($username))
				{
					if (!$this->_usernameExists($username))
					{
						$valid = true;
					}
				}

				// Try to create from portion preceeding @ in email address
				if (!$valid)
				{
					$username = strstr($this->record->entry->get('email'), '@', true);
					if (Validate::username($username))
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
						$username = preg_replace('/[^a-z9-0_]/i', '', strtolower($this->record->entry->get('name'))) . $i;
						if (Validate::username($username))
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
					$this->record->entry->set('username', $username);
				}
			}

			if (!$this->raw->password)
			{
				$this->raw->password = $this->record->entry->get('username');
			}

			$newUsertype = null;

			if (isset($this->raw->usertype))
			{
				if (is_numeric($this->raw->usertype))
				{
					$newUsertype = (int)$this->raw->usertype;
				}
				else
				{
					$db = \App::get('db');
					$query = $db->getQuery()
						->select('id')
						->from('#__usergroups')
						->whereEquals('title', $this->raw->usertype);
					$db->setQuery($query->toString());
					$newUsertype = (int)$db->loadResult();
				}
			}

			if (!$newUsertype)
			{
				$usersConfig = Component::params('com_users');
				$newUsertype = $usersConfig->get('new_usertype');
				if (!$newUsertype)
				{
					$db = \App::get('db');
					$query = $db->getQuery()
						->select('id')
						->from('#__usergroups')
						->whereEquals('title', 'Registered');
					$db->setQuery($query->toString());
					$newUsertype = $db->loadResult();
				}
			}

			$d = Date::of('now');
			if ($this->raw->registerDate)
			{
				try
				{
					$d = Date::of($this->raw->registerDate);
				}
				catch (Exception $e)
				{
					array_push($this->record->errors, $e->getMessage());
				}
			}

			$this->record->entry->set('id', 0);
			$this->record->entry->set('accessgroups', array($newUsertype));
			$this->record->entry->set('registerDate', $d->toSql());
			$this->record->entry->set('password', $this->raw->password);
			if (!$this->record->entry->get('loginShell'))
			{
				$this->record->entry->set('loginShell', '/bin/bash');
			}
			if (!$this->record->entry->get('ftpShell'))
			{
				$this->record->entry->set('ftpShell', '/usr/lib/sftp-server');
			}

			if (!$this->record->entry->get('activation', null))
			{
				$this->record->entry->set('activation', -rand(1, pow(2, 31)-1));
			}
		}

		if (!$this->record->entry->save())
		{
			throw new Exception(Lang::txt('Unable to save the entry data.'));
		}

		if (!empty($this->_profile))
		{
			if (!$this->record->entry->saveProfile($this->_profile))
			{
				throw new Exception($this->record->entry->getError());
			}
		}

		if ($this->raw->password)
		{
			\Hubzero\User\Password::changePassword($this->record->entry->get('id'), $this->raw->password);
			\Hubzero\User\Password::expirePassword($this->record->entry->get('id'));
		}

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
			$eview->login        = $this->record->entry->get('username');
			$eview->name         = $this->record->entry->get('name');
			$eview->registerDate = $this->record->entry->get('registerDate');
			$eview->confirm      = $this->record->entry->get('activation');
			$eview->baseURL      = Request::base();

			$msg = new \Hubzero\Mail\Message();
			$msg->setSubject(Config::get('sitename') . ' ' . Lang::txt('COM_MEMBERS_REGISTER_EMAIL_CONFIRMATION'))
			    ->addTo($this->record->entry->get('email'))
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
	 * Check if a username exists
	 *
	 * @return  integer
	 */
	private function _usernameExists($username)
	{
		return Member::oneByUsername($username)->get('id');
	}

	/**
	 * Map extra data
	 *
	 * @return  void
	 */
	private function _mapExtraData()
	{
		foreach ($this->handlers() as $handler)
		{
			$this->record = $handler->bind($this->raw, $this->record, $this->_mode);

			foreach ($handler->getErrors() as $error)
			{
				array_push($this->record->notices, $error);
			}
		}
	}

	/**
	 * Save extra data
	 *
	 * @return  void
	 */
	private function _saveExtraData()
	{
		foreach ($this->handlers() as $handler)
		{
			$this->record = $handler->store($this->raw, $this->record, $this->_mode);

			foreach ($handler->getErrors() as $error)
			{
				array_push($this->record->notices, $error);
			}
		}
	}

	/**
	 * Return a list of all available processors.
	 *
	 * @return  array
	 */
	public function handlers()
	{
		foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . 'handler' . DIRECTORY_SEPARATOR . '*.php') as $path)
		{
			$type = basename($path, '.php');

			if (!isset(self::$handlers[$type]))
			{
				$class = __NAMESPACE__ . '\\Handler\\' . ucfirst($type);

				if (!class_exists($class))
				{
					include_once $path;
				}

				self::$handlers[$type] = new $class;
			}
		}

		return self::$handlers;
	}

	/**
	 * Is there a handler for this type?
	 *
	 * @param   string  $type
	 * @return  bool
	 */
	public function hasHandler($type)
	{
		return isset(self::$handlers[$type]);
	}

	/**
	 * To String object
	 *
	 * Removes private properties before returning
	 *
	 * @return  string
	 */
	/*public function toString()
	{
		// Reflect on class to get private or protected props
		$privateProperties = with(new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PROTECTED);

		// Remove each private or protected prop
		foreach ($privateProperties as $prop)
		{
			$name = (string) $prop->name;
			unset($this->$name);
		}

		if ($this->record->entry instanceof \Hubzero\User\User)
		{
			$entry = $this->record->entry->toArray();

			$this->record->entry = new stdClass;

			foreach ($entry as $field => $value)
			{
				$this->record->entry->$field = $value;
			}
		}
		foreach ($this->_profile as $field => $value)
		{
			$this->record->entry->$field = $value;
		}

		// Output as json
		return $this;
	}*/
}
