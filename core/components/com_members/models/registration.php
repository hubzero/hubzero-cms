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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models;

use Components\Members\Models\Profile\Field;
use Components\Members\Tables;
use Components\Members\Helpers;
use Request;
use User;
use App;

include_once(__DIR__ . DS . 'profile' . DS . 'field.php');
include_once(dirname(__DIR__) . DS . 'helpers' . DS . 'utility.php');

/**
 * Description for ''REG_HIDE''
 */
define('REG_HIDE', 0);

/**
 * Description for ''REG_OPTIONAL''
 */
define('REG_OPTIONAL', 1);

/**
 * Description for ''REG_REQUIRED''
 */
define('REG_REQUIRED', 2);

/**
 * Description for ''REG_READONLY''
 */
define('REG_READONLY', 4);

/**
 * Description for ''PASS_SCORE_BAD''
 */
define('PASS_SCORE_BAD', 0);

/**
 * Description for ''PASS_SCORE_MEDIOCRE''
 */
define('PASS_SCORE_MEDIOCRE', 34);

/**
 * Description for ''PASS_SCORE_GOOD''
 */
define('PASS_SCORE_GOOD', 50);

/**
 * Description for ''PASS_SCORE_STRONG''
 */
define('PASS_SCORE_STRONG', 68);

/**
 * Model class for a registration
 */
class Registration
{
	/**
	 * Description for '_registration'
	 *
	 * @var  array
	 */
	public $_registration;

	/**
	 * Description for '_encoded'
	 *
	 * @var  unknown
	 */
	public $_encoded;

	/**
	 * Description for '_missing'
	 *
	 * @var  array
	 */
	public $_missing;

	/**
	 * Description for '_invalid'
	 *
	 * @var  mixed
	 */
	public $_invalid;

	/**
	 * Description for '_checked'
	 *
	 * @var  boolean
	 */
	public $_checked;

	/**
	 * Clear cached data
	 *
	 * @return  void
	 */
	public function clear()
	{
		$this->_registration = array();
		$this->normalize();
		$this->_missing = array();
		$this->_invalid = false;
		$this->_checked = true;
	}

	/**
	 * Constructor
	 *
	 * @param   string  $login
	 * @return  void
	 */
	public function __construct($login = null)
	{
		$this->clear();
	}

	/**
	 * Normalize data
	 *
	 * @return  void
	 */
	public function normalize()
	{
		$this->_registration['login'] = null;
		$this->_registration['email'] = null;
		$this->_registration['confirmEmail'] = null;
		$this->_registration['name'] = null;
		$this->_registration['givenName'] = null;
		$this->_registration['middleName'] = null;
		$this->_registration['surname'] = null;
		$this->_registration['password'] = null;
		$this->_registration['confirmPassword'] = null;
		$this->_registration['usageAgreement'] = null;
		$this->_registration['sendEmail'] = null;
		$this->_registration['captcha'] = null;
		$this->_registration['orcid'] = null;
		$this->_registration['_profile'] = array();
	}

	/**
	 * Load data from post values
	 *
	 * @return  void
	 */
	public function loadPost()
	{
		// fill out registration data based on current form POST
		//
		// note that a value of null means the field doesn't exist
		// we use this to detect when to delete data when
		// merging registrations with profile data
		//
		// TODO: more cleanup

		$name = Request::getVar('name', array(), 'post');
		if (!is_array($name))
		{
			$name = array();
		}
		if ($name)
		{
			$name['first']  = preg_replace('/\s+/', ' ', trim($name['first']));
			$name['middle'] = preg_replace('/\s+/', ' ', trim($name['middle']));
			$name['last']   = preg_replace('/\s+/', ' ', trim($name['last']));
			$nm  = trim($name['first']);
			$nm .= (isset($name['middle']) && trim($name['middle']) != '') ? ' '.$name['middle'] : '';
			$nm .= ' '.trim($name['last']);
			$this->_registration['name'] = $nm;
			$this->_registration['givenName'] = $name['first'];
			$this->_registration['middleName'] = $name['middle'];
			$this->_registration['surname'] = $name['last'];
		}

		$this->_registration['login'] = strtolower(Request::getVar('login', null, 'post'));
		$this->_registration['email'] = Request::getVar('email', null, 'post');
		$this->_registration['confirmEmail'] = Request::getVar('email2', null, 'post');
		$this->_registration['password'] = Request::getVar('password', null, 'post');
		$this->_registration['confirmPassword'] = Request::getVar('password2', null, 'post');
		$this->_registration['usageAgreement'] = Request::getVar('usageAgreement', null, 'post');
		$this->_registration['sendEmail'] = Request::getVar('sendEmail', null, 'post');

		if ($this->_registration['usageAgreement'] !== null)
		{
			$this->_registration['usageAgreement'] = ($this->_registration['usageAgreement'] === 'unset') ? false : true;
		}

		// Incoming profile edits
		$profile = Request::getVar('profile', array(), 'post', 'none', 2);

		// Compile profile data
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

		$this->_registration['_profile'] = $profile;

		$this->_checked = false;
	}

	/**
	 * Load data from user profile
	 *
	 * @param   object  $xprofile
	 * @return  void
	 */
	public function loadProfile($xprofile = null)
	{
		$this->clear();

		if (!is_object($xprofile))
		{
			return;
		}

		//get member addresses
		/*require_once(dirname(__DIR__) . DS . 'tables' . DS . 'address.php');
		$database = \App::get('db');
		$membersAddress = new Tables\Address($database);
		$addresses = $membersAddress->getAddressesForMember($xprofile->get("uidNumber"));

		$this->set('countryresident', $xprofile->get('countryresident'));
		$this->set('countryorigin', $xprofile->get('countryorigin'));
		$this->set('nativetribe', $xprofile->get('nativeTribe'));
		$this->set('role', $xprofile->get('role'));
		$this->set('edulevel', $xprofile->get('edulevel'));
		$this->set('hispanic', $xprofile->get('hispanic'));
		$this->set('disability', $xprofile->get('disability'));
		$this->set('race', $xprofile->get('race'));*/
		$this->set('login', $xprofile->get('username'));
		$this->set('email', $xprofile->get('email'));
		$this->set('confirmEmail', $xprofile->get('email'));
		//$this->set('url', $xprofile->get('url'));
		//$this->set('phone', $xprofile->get('phone'));
		$this->set('name', $xprofile->get('name'));
		$this->set('givenName', $xprofile->get('givenName'));
		$this->set('middleName', $xprofile->get('middleName'));
		$this->set('surname', $xprofile->get('surname'));
		//$this->set('orgtype', $xprofile->get('orgtype'));
		//$this->set('org', $xprofile->get('organization'));
		//$this->set('orgtext', '');
		//$this->set('reason', $xprofile->get('reason'));
		//$this->set('reasontxt', '');
		$this->set('password', null);
		$this->set('confirmPassword', null);
		//$this->set('sex', $xprofile->get('gender'));
		$this->set('usageAgreement', $xprofile->get('usageAgreement'));
		$this->set('sendEmail', $xprofile->get('sendEmail'));
		//$this->set('interests', $xprofile->tags('string'));
		//$this->set('address', $addresses);
		$this->set('orcid', $xprofile->get('orcid'));

		$this->_checked = false;
	}

	/**
	 * Load data from user account
	 *
	 * @param   object  $user
	 * @return  void
	 */
	public function loadAccount($user = null)
	{
		$this->clear();

		if (!is_object($user))
		{
			return;
		}

		$this->set('login', $user->get('username'));
		$this->set('name', $user->get('name'));
		$this->set('email', $user->get('email'));

		$this->_checked = false;
	}

	/**
	 * Retrieve a registraion value
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function get($key)
	{
		if (!array_key_exists($key, $this->_registration))
		{
			App::abort(500, __CLASS__ . "::" . __METHOD__ . "() Unknown key: $key \n");
		}

		return $this->_registration[$key];
	}

	/**
	 * Set a registraion value
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  void
	 */
	public function set($key, $value)
	{
		$this->_checked = false;

		if (!array_key_exists($key, $this->_registration))
		{
			App::abort(500, __CLASS__ . "::" . __METHOD__ . "() Unknown key: $key \n");
		}

		$this->_registration[$key] = $value;
	}

	/**
	 * Determine state of a field
	 *
	 * @param   string  $name
	 * @param   string  $default
	 * @param   string  $task
	 * @return  integer
	 */
	private function registrationField($name, $default, $task = 'register')
	{
		switch ($task)
		{
			case 'register':
			case 'create':
			case 'new':
				$index = 0;
			break;

			case 'proxycreate':
				$index = 1;
			break;

			case 'update':
				$index = 2;
			break;

			case 'edit':
				$index = 3;
			break;

			default:
				$index = 0;
			break;
		}

		$hconfig = \Component::params('com_members');

		$default    = str_pad($default, 4, '-');
		$configured = $hconfig->get($name);
		if (empty($configured))
		{
			$configured = $default;
		}
		$length     = strlen($configured);
		if ($length > $index)
		{
			$value = substr($configured, $index, 1);
		}
		else
		{
			$value = substr($default, $index, 1);
		}

		switch ($value)
		{
			case 'R': return(REG_REQUIRED);
			case 'O': return(REG_OPTIONAL);
			case 'H': return(REG_HIDE);
			case '-': return(REG_HIDE);
			case 'U': return(REG_READONLY);
			default : return(REG_HIDE);
		}
	}

	/**
	 * Returns userid if email exists
	 *
	 * @param   string   $email  The email to search on
	 * @return  integer  The user id or 0 if not found
	 */
	public function getEmailId($email)
	{
		return User::getInstance($email)->get('id');
	}

	/**
	 * Check data
	 *
	 * @param   string   $task
	 * @param   integer  $id
	 * @return  boolean
	 */
	public function check($task = 'create', $id = 0, $field_to_check = array())
	{
		$sitename = Config::get('sitename');

		if ($id == 0)
		{
			$id = User::get('id');
		}

		$registration = $this->_registration;

		if ($task == 'proxy')
		{
			$task = 'proxycreate';
		}

		$this->_missing = array();
		$this->_invalid = array();

		$registrationUsername = $this->registrationField('registrationUsername','RROO',$task);
		$registrationPassword = $this->registrationField('registrationPassword','RRHH',$task);
		$registrationConfirmPassword = $this->registrationField('registrationConfirmPassword','RRHH',$task);
		$registrationFullname = $this->registrationField('registrationFullname','RRRR',$task);
		$registrationEmail = $this->registrationField('registrationEmail','RRRR',$task);
		$registrationConfirmEmail = $this->registrationField('registrationConfirmEmail','RRRR',$task);
		$registrationOptIn = $this->registrationField('registrationOptIn','HHHH',$task);
		$registrationCAPTCHA = $this->registrationField('registrationCAPTCHA','HHHH',$task);
		$registrationTOU = $this->registrationField('registrationTOU','HHHH',$task);

		if ($task == 'update')
		{
			if (empty($registration['login']))
			{
				$registrationUsername = REG_REQUIRED;
			}
			else
			{
				$registrationUsername = REG_READONLY;
			}

			$registrationPassword = REG_HIDE;
			$registrationConfirmPassword = REG_HIDE;

			if (empty($registration['email']))
			{
				$registrationEmail = REG_REQUIRED;
			}
		}

		if ($task == 'edit')
		{
			$registrationUsername = REG_READONLY;
			$registrationPassword = REG_HIDE;
			$registrationConfirmPassword = REG_HIDE;
		}

		if (User::get('auth_link_id') && $task == 'create')
		{
			$registrationPassword = REG_HIDE;
			$registrationConfirmPassword = REG_HIDE;
		}

		$login = $registration['login'];

		$email = $registration['email'];
		$confirmEmail = $registration['confirmEmail'];

		if ($registrationUsername == REG_REQUIRED)
		{
			if (empty($login))
			{
				$this->_missing['login'] = 'User Login';
				$this->_invalid['login'] = 'Please provide a username';
			}
		}

		if ($registrationUsername != REG_HIDE)
		{
			$allowNumericFirstCharacter = ($task == 'update') ? true : false;
			if (!empty($login) && !Helpers\Utility::validlogin($login, $allowNumericFirstCharacter))
			{
				$this->_invalid['login'] = 'Invalid login name. Please type at least 2 characters and use only alphanumeric characters.';
			}
		}

		if (!empty($login) && ($task == 'create' || $task == 'proxycreate' || $task == 'update'))
		{
			$uid = User::getInstance($login)->get('id');

			if ($uid && $uid != $id)
			{
				$this->_invalid['login'] = 'The user login "'. htmlentities($login) .'" already exists. Please try another.';
			}

			if (\Hubzero\Utility\Validate::reserved('username', $login))
			{
				$this->_invalid['login'] = 'The user login "'. htmlentities($login) .'" already exists. Please try another.';
			}

			// system username check
			$puser = posix_getpwnam($login);
			if (!empty($puser) && $uid && $uid != $puser['uid'])
			{
				// log error and display error to user
				\Log::error('System username/userid does not match DB username/password for user: ' . $uid);
				$this->_invalid['login'] = 'Username mismatch error, please contact system administrator to fix your account.';
			}
		}

		if ($registrationPassword == REG_REQUIRED)
		{
			if (empty($registration['password']))
			{
				$this->_missing['password'] = 'Password';
				$this->_invalid['password'] = 'Please provide a password.';
			}
		}

		/*
		if ($registrationPassword != REG_HIDE)
		{
			if (!empty($registration['password']))
			{
				$result = Helpers\Utility::valid_password($registration['password']);

				if ($result)
					$this->_invalid['password'] = $result;
			}
		}
		*/

		if ($registrationConfirmPassword == REG_REQUIRED)
		{
			if (empty($registration['confirmPassword']))
			{
				$this->_missing['confirmPassword'] = 'Password Confirmation';
				$this->_invalid['confirmPassword'] = 'Please provide the password again.';
			}
		}

		if ($registrationPassword != REG_HIDE && $registrationConfirmPassword != REG_HIDE)
		{
			if ($registration['password'] != $registration['confirmPassword'])
			{
				$this->_invalid['confirmPassword'] = 'Passwords do not match. Please correct and try again.';
			}
		}

		if ($registrationPassword == REG_REQUIRED)
		{
			$score = $this->scorePassword($registration['password'], $registration['login']);
			if ($score < PASS_SCORE_MEDIOCRE)
			{
				$this->_invalid['password'] = 'Password strength is too weak.';
			}
			else if ($score >= PASS_SCORE_MEDIOCRE && $score < PASS_SCORE_GOOD)
			{
				// Mediocre pass
			}
			else if ($score >= PASS_SCORE_GOOD && $score < PASS_SCORE_STRONG)
			{
				// Good pass
			}
			else if ($score >= PASS_SCORE_STRONG)
			{
				// Strong pass
			}

			$rules = \Hubzero\Password\Rule::all()
				->whereEquals('enabled', 1)
				->rows();

			$msg = \Hubzero\Password\Rule::verify($registration['password'], $rules, $login, $registration['name']);
			if (!empty($msg))
			{
				$this->_invalid['password'] = $msg;
			}
		}

		if ($registrationFullname == REG_REQUIRED)
		{
			if (empty($registration['name']))
			{
				$this->_missing['name'] = 'Full Name';
				$this->_invalid['name'] = 'Please provide a name.';
			}
			else
			{
				$bits = explode(' ', $registration['name']);
				$surname = null;
				$middleName = null;
				$givenName = null;

				if (count($bits) == 1)
				{
					$givenName = array_shift($bits);
				}
				else
				{
					$surname = array_pop($bits);

					if (count($bits) >= 1)
					{
						$givenName = array_shift($bits);
					}
					if (count($bits) >= 1)
					{
						$middleName = implode(' ',$bits);
					}
				}

				if (!$givenName || !$surname)
				{
					$this->_missing['name'] = 'Full Name';
					$this->_invalid['name'] = 'Please provide a name.';
				}
			}
		}

		if ($registrationFullname != REG_HIDE)
		{
			if (!empty($registration['name']) && !Helpers\Utility::validname($registration['name']))
			{
				$this->_invalid['name'] = 'Invalid name. You may be using characters that are not allowed.';
			}
		}

		if ($registrationEmail == REG_REQUIRED)
		{
			if (empty($email))
			{
				$this->_missing['email'] = 'Valid Email';
				$this->_invalid['email'] = 'Please provide a valid e-mail address.';
			}
		}

		if ($registrationEmail != REG_HIDE)
		{
			if (empty($email))
			{
				$this->_missing['email'] = 'Valid Email';
			}
			elseif (!Helpers\Utility::validemail($email))
			{
				$this->_invalid['email'] = 'Invalid email address. Please correct and try again.';
			}
			else
			{
				$usersConfig = \Component::params('com_users');
				$allow_duplicate_emails = $usersConfig->get('allow_duplicate_emails');

				// Check if the email is already in use
				$row = \Hubzero\User\User::all()
					->whereEquals('email', $email)
					->where('id', '!=', (int)$id)
					->row();

				$xid = intval($row->get('id'));

				// 0 = not allowed
				// 1 = allowed (i.e. no check needed)
				// 2 = only existing accounts (grandfathered)
				if ($xid && ($allow_duplicate_emails == 0 || $allow_duplicate_emails == 2))
				{
					if ($allow_duplicate_emails == 0)
					{
						$this->_invalid['email'] = 'An existing account is already using this e-mail address.';
					}
					else if ($allow_duplicate_emails == 2)
					{
						// If duplicates are only allowed in grandfathered accounts,
						// then new accounts shouldn't be created with the same email.
						if (($task == 'create' || $task == 'proxycreate'))
						{
							$this->_invalid['email'] = 'An existing account is already using this e-mail address.';
						}
						else
						{
							// We also need to catch existing users who might try to change their
							// email to an existing email address on the hub. For that, we need to
							// check and see if their email address is changing with this save.
							$row = \Hubzero\User\User::oneOrNew((int)$id);
							$currentEmail = $row->get('email');

							if ($currentEmail != $email)
							{
								$this->_invalid['email'] = 'An existing account is already using this e-mail address.';
							}
						}
					}
				}
			}
		}

		if ($registrationConfirmEmail == REG_REQUIRED)
		{
			if (empty($confirmEmail) && empty($this->_invalid['email']))
			{
				$this->_missing['confirmEmail'] = 'Valid Email Confirmation';
				$this->_invalid['confirmEmail'] = 'Please provide a valid e-mail address again.';
			}
		}

		if ($registrationConfirmEmail != REG_HIDE)
		{
			if ($email != $confirmEmail)
			{
				if (empty($this->_invalid['email']))
				{
					$this->_invalid['confirmEmail'] = 'Email addresses do not match. Please correct and try again.';
					$this->_invalid['email'] = 'Email addresses do not match. Please correct and try again.';
				}
			}
		}

		if ($registrationOptIn == REG_REQUIRED)
		{
			if (is_null($registration['sendEmail']) || intval($registration['sendEmail']) < 0)
			{
				$this->_missing['sendEmail'] = 'Receive Email Updates';
				$this->_invalid['sendEmail'] = 'Receive Email Updates has not been selected';
			}
		}

		if ($registrationCAPTCHA == REG_REQUIRED)
		{
			$botcheck = Request::getVar('botcheck','');
			if ($botcheck)
			{
				$this->_invalid['captcha'] = 'Error: Invalid CAPTCHA response.';
			}

			$validcaptchas = Event::trigger('captcha.onCheckAnswer');
			if (count($validcaptchas) > 0)
			{
				foreach ($validcaptchas as $validcaptcha)
				{
					if (!$validcaptcha)
					{
						$this->_invalid['captcha'] = 'Error: Invalid CAPTCHA response.';
					}
				}
			}
		}

		if ($registrationTOU == REG_REQUIRED)
		{
			if (empty($registration['usageAgreement']))
			{
				$this->_missing['usageAgreement'] = 'Usage Agreement';
				$this->_invalid['usageAgreement'] = 'Registration requires acceptance of the usage agreement';
			}
		}

		/* Everything below is currently done elsewhere
		   @TODO  Move code to here or refactor?

		if ($registrationAddress == REG_REQUIRED)
		{
			if (count($registration['address']) == 0)
			{
				$this->_missing['address'] = 'Member Address';
				$this->_invalid['address'] = 'Member Address';
			}
		}

		// Load all fields not hidden
		$fields = Field::all()
			->including(['options', function ($option){
				$option
					->select('*')
					->ordered();
			}])
			->where('action_' . $task, '!=', Field::STATE_HIDDEN)
			->ordered()
			->rows();

		if (!isset($registration['_profile']))
		{
			$registration['_profile'] = array();
		}

		// Find missing required fields
		foreach ($fields as $field)
		{
			if ($field->get('type') != 'hidden')
			{
				if (!isset($registration['_profile'][$field->get('name')]))
				{
					continue;
				}

				$value = $registration['_profile'][$field->get('name')];

				if (empty($value) && $field->get('action_' . $task) == Field::STATE_REQUIRED)
				{
					$this->_missing[$field->get('name')] = $field->get('label');
				}
			}
		}

		// Validate input
		$form = new \Hubzero\Form\Form('profile', array('control' => 'profile'));
		$form->load(Field::toXml($fields, $action));
		$form->bind(new \Hubzero\Config\Registry($registration['_profile']));

		if (!$form->validate($registration['_profile']))
		{
			foreach ($form->getErrors() as $error)
			{
				$this->_invalid[] = $error;
			}
		}*/

		// Filter out fields
		if (!empty($field_to_check))
		{
			if ($this->_missing)
			{
				foreach ($this->_missing as $k => $v)
				{
					if (!in_array($k, $field_to_check))
					{
						unset($this->_missing[$k]);
					}
				}
			}

			if ($this->_invalid)
			{
				foreach ($this->_invalid as $k => $v)
				{
					if (!in_array($k, $field_to_check))
					{
						unset($this->_invalid[$k]);
					}
				}
			}
		}

		if (empty($this->_missing) && empty($this->_invalid))
		{
			return true;
		}

		return false;
	}

	/**
	 * Score how good a password is
	 *
	 * @param   string  $password
	 * @param   string  $username
	 * @return  integer
	 */
	public function scorePassword($password, $username='')
	{
		$score = 0;

		if ($username)
		{
			if (strtolower($password) == strtolower($username))
			{
				return $score;
			}
		}

		$seen = array();

		for ($i = 0; $i < strlen($password); $i++)
		{
			$char = substr($password, $i, 1);

			if (!isset($seen[$char]))
			{
				$seen[$char] = 1;
			}
			else
			{
				$seen[$char]++;
			}

			if (is_numeric($char))
			{
				$s = 16;
			}
			else
			{
				$s = 8;
			}

			if ($seen[$char] != 1)
			{
				for ($k = 1; $k < $seen[$char]; $k++)
				{
					$s = $s / 2;
				}
			}
			$score += ($s >= 1) ? $s : 0;
		}

		$score = ($score > 100) ? 100 : $score;

		return $score;
	}

	/**
	 * Checks if username already exists
	 *
	 * @param   string  $username  Username to check
	 * @return  array   Status & message
	 */
	public function checkusername($username)
	{
		$ret['status'] = 'error';
		if (empty($username))
		{
			$ret['message'] = 'Please enter a username.';
			return $ret;
		}

		// check the general validity
		if (!Helpers\Utility::validlogin($username))
		{
			$ret['message'] = 'Invalid login name. Please type between 2 and 32 characters and use only lowercase alphanumeric characters.';
			return $ret;
		}

		// Count records with the given username
		$total = \Hubzero\User\User::all()
			->whereEquals('username', $username)
			->total();

		if ($total > 0)
		{
			$ret['message'] = 'User login name is not available. Please select another one.';
			return $ret;
		}

		$ret['status'] = 'ok';
		$ret['message'] = 'User login name is available';

		return $ret;
	}

	/**
	 * Generates new available username based on email address
	 *
	 * @param   string  $email  Email address or preferrd username
	 * @return  string  Generated username
	 */
	public function generateUsername($email)
	{
		$loginMaxLen = 32;
		$email = strtolower($email);

		$email = explode('@', $email);

		$local = $email[0];
		$domain = '';
		if (!empty($email[1]))
		{
			$domain = $email[1];
		}

		// strip bad characters
		$local = preg_replace("/[^A-Za-z0-9_\.]/", '', $local);
		$domain = preg_replace("/[^A-Za-z0-9_\.]/", '', $domain);

		// Try just the local part of an address
		$login = $local;
		// Make sure login username is no longer than max allowed by DB
		$login = substr($login, 0, $loginMaxLen);
		$logincheck = self::checkusername($login);
		if (Helpers\Utility::validlogin($login) && $logincheck['status'] == 'ok')
		{
			return $login;
		}

		// try full email address with @ replaced with '_'
		if (!empty($domain))
		{
			$login = $local . '_' . $domain;
		}
		// Make sure login username is no longer than max allowed by DB
		$login = substr($login, 0, $loginMaxLen);
		$logincheck = self::checkusername($login);
		if (Helpers\Utility::validlogin($login) && $logincheck['status'] == 'ok')
		{
			return $login;
		}

		// generate username by simply appending a sequential number to local part of an address until there is an avilable username available
		for ($i = 1; true; $i++)
		{
			// Make sure login username is no longer than max allowed by DB
			$numberLen = strlen($i);

			$login = substr($local, 0, $loginMaxLen - $numberLen) . $i;
			$logincheck = self::checkusername($login);
			if (Helpers\Utility::validlogin($login) && $logincheck['status'] == 'ok')
			{
				return $login;
			}
		}

		return false;
	}
}
