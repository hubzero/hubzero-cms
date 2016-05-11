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
	 * @var array
	 */
	var $_registration;

	/**
	 * Description for '_encoded'
	 *
	 * @var unknown
	 */
	var $_encoded;

	/**
	 * Description for '_missing'
	 *
	 * @var array
	 */
	var $_missing;

	/**
	 * Description for '_invalid'
	 *
	 * @var mixed
	 */
	var $_invalid;

	/**
	 * Description for '_checked'
	 *
	 * @var boolean
	 */
	var $_checked;

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
		$this->_registration['countryresident'] = null;
		$this->_registration['countryorigin'] = null;
		$this->_registration['nativetribe'] = null;
		$this->_registration['role'] = null;
		$this->_registration['edulevel'] = null;
		$this->_registration['hispanic'] = null;
		$this->_registration['disability'] = null;
		$this->_registration['race'] = null;
		$this->_registration['login'] = null;
		$this->_registration['email'] = null;
		$this->_registration['confirmEmail'] = null;
		$this->_registration['url'] = null;
		$this->_registration['phone'] = null;
		$this->_registration['name'] = null;
		$this->_registration['givenName'] = null;
		$this->_registration['middleName'] = null;
		$this->_registration['surname'] = null;
		$this->_registration['orgtype'] = null;
		$this->_registration['org'] = null;
		$this->_registration['orgtext'] = null;
		$this->_registration['reason'] = null;
		$this->_registration['reasontxt'] = null;
		$this->_registration['password'] = null;
		$this->_registration['confirmPassword'] = null;
		$this->_registration['sex'] = null;
		$this->_registration['usageAgreement'] = null;
		$this->_registration['sendEmail'] = null;
		$this->_registration['captcha'] = null;
		$this->_registration['interests'] = null;
		$this->_registration['address'] = null;
		$this->_registration['orcid'] = null;
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

		$coriginus_p = Request::getVar('corigin_us', null, 'post');
		$corigin_p = Request::getVar('corigin', null, 'post');
		$cresidentus_p = Request::getVar('cresident_us', null, 'post');
		$cresident_p = Request::getVar('cresident', null, 'post');
		$disability_p = Request::getVar('disability', null, 'post');
		$disabilityblind_p = Request::getVar('disabilityblind', null, 'post');
		$disabilitydeaf_p = Request::getVar('disabilitydeaf', null, 'post');
		$disabilityphysical_p = Request::getVar('disabilityphysical', null, 'post');
		$disabilitylearning_p = Request::getVar('disabilitylearning', null, 'post');
		$disabilityvocal_p = Request::getVar('disabilityvocal', null, 'post');
		$disabilityother_p = Request::getVar('disabilityother', null, 'post');
		$hispanic_p = Request::getVar('hispanic', null, 'post');
		$hispaniccuban_p = Request::getVar('hispaniccuban', null, 'post');
		$hispanicmexican_p = Request::getVar('hispanicmexican', null, 'post');
		$hispanicpuertorican_p = Request::getVar('hispanicpuertorican', null, 'post');
		$hispanicother_p = Request::getVar('hispanicother',null,'post');
		$racenativeamerican_p = Request::getVar('racenativeamerican', null, 'post');
		$racenativetribe_p = Request::getVar('racenativetribe', null, 'post');
		$raceasian_p = Request::getVar('raceasian', null, 'post');
		$raceblack_p = Request::getVar('raceblack', null, 'post');
		$racehawaiian_p = Request::getVar('racehawaiian', null, 'post');
		$racewhite_p = Request::getVar('racewhite', null, 'post');
		$racerefused_p = Request::getVar('racerefused', null, 'post');
		//$interests_p = Request::getVar('interests',null,'post');

		//if ($coriginus_p === null) { // field not on form
		if ($coriginus_p || $corigin_p) { // field not on form
			$corigin = ($coriginus_p == 'yes') ? 'US' : $corigin_p;
		} else {
			$corigin = null;
		}

		//if ($cresident_p === null) { // field not on form
		if ($cresidentus_p || $cresident_p) { // field not on form
			$cresident = ($cresidentus_p == 'yes') ? 'US' : $cresident_p;
		} else {
			$cresident = null;
		}

		if ($disability_p === null) // field not on form
			$disability = null;
		else
		{
			$disability = array();

			if ($disability_p == 'yes')
			{
				if ($disabilityblind_p)
					$disability[] = 'blind';
				if ($disabilitydeaf_p)
					$disability[] = 'deaf';
				if ($disabilityphysical_p)
					$disability[] = 'physical';
				if ($disabilitylearning_p)
					$disability[] = 'learning';
				if ($disabilityvocal_p)
					$disability[] = 'vocal';
				if ($disabilityother_p)
					$disability[] = $disabilityother_p;
				if (empty($disability))
					$disability[] = 'yes';
			}
			else if ($disability_p == 'no')
				$disability[] = 'no';
			else if ($disability_p == 'refused')
				$disability[] = 'refused';
		}

		if ($hispanic_p === null) // field not on form
			$hispanic = null;
		else
		{
			$hispanic = array();

			if ($hispanic_p == 'yes')
			{
				if ($hispaniccuban_p)
					$hispanic[] = 'cuban';
				if ($hispanicmexican_p)
					$hispanic[] = 'mexican';
				if ($hispanicpuertorican_p)
					$hispanic[] = 'puertorican';
				if ($hispanicother_p)
					$hispanic[] = $hispanicother_p;
			}
			else if ($hispanic_p == 'no')
				$hispanic[] = 'no';
			else if ($hispanic_p == 'refused')
				$hispanic[] = 'refused';
		}

		if ($racenativeamerican_p === NULL
		 && $racenativetribe_p === null
		 && $raceasian_p === NULL
		 && $raceblack_p === NULL
		 && $racehawaiian_p === NULL
		 && $racewhite_p === NULL
		 && $racerefused_p === NULL) // field not on form
		{
			$race = null;
			$racenativetribe = null;
		}
		else
		{
			$race = array();
			$racenativetribe = null;

			if ($racenativeamerican_p)
			{
				$race[] = 'nativeamerican';
				$racenativetribe = $racenativetribe_p;
			}
			if ($raceasian_p)
			{
				$race[] = 'asian';
			}
			if ($raceblack_p)
			{
				$race[] = 'black';
			}
			if ($racehawaiian_p)
			{
				$race[] = 'hawaiian';
			}
			if ($racewhite_p)
			{
				$race[] = 'white';
			}
			if ($racerefused_p)
			{
				$race = 'refused';
			}
		}

		// if ($interests_p === null) // field not on form
		// {
		// 	$role = null;
		// 	$edulevel = null;
		// }
		// else
		// {
		// 	$role = array();
		// 	$edulevel = array();

		// 	if ( Request::getVar('rolestudent', '', 'post') )
		// 		$role[] = 'student';

	 // 		if ( Request::getVar('roleeducator', '', 'post') )
		// 		$role[] = 'educator';

		// 	if ( Request::getVar('roleresearcher', '', 'post') )
		// 		$role[] = 'researcher';

		// 	if ( Request::getVar('roledeveloper', '', 'post') )
		// 		$role[] = 'developer';

		// 	if ( Request::getVar('edulevelk12', '', 'post') )
		// 		$edulevel[] = 'k12';

		// 	if ( Request::getVar('edulevelundergraduate', '', 'post') )
		// 		$edulevel[] = 'undergraduate';

		// 	if ( Request::getVar('edulevelgraduate', '', 'post') )
		// 		$edulevel[] = 'graduate';
		// }

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

		$this->_registration['countryresident'] = $cresident;
		$this->_registration['countryorigin']	= $corigin;
		$this->_registration['nativetribe'] = $racenativetribe;
		//$this->_registration['role'] = $role;
		//$this->_registration['edulevel'] = $edulevel;
		$this->_registration['hispanic'] = $hispanic;
		$this->_registration['disability'] = $disability;
		$this->_registration['race'] = $race;
		$this->_registration['login'] = strtolower(Request::getVar('login', null, 'post'));
		$this->_registration['email'] = Request::getVar('email', null, 'post');
		$this->_registration['confirmEmail'] = Request::getVar('email2', null, 'post');
		$this->_registration['url'] = Request::getVar('url', null, 'post');
		$this->_registration['phone'] = Request::getVar('phone', null, 'post');
		//$this->_registration['name'] = Request::getVar('name', null, 'post');
		$this->_registration['orgtype']	= Request::getVar('orgtype', null, 'post');
		$this->_registration['org'] = Request::getVar('org', null, 'post');
		$this->_registration['orgtext']	= Request::getVar('orgtext', null, 'post');
		if (!$this->_registration['org'])
		{
			$this->_registration['org'] = $this->_registration['orgtext'];
		}
		$this->_registration['reason'] = Request::getVar('reason', null, 'post');
		$this->_registration['reasontxt'] = Request::getVar('reasontxt', null, 'post');
		if (!$this->_registration['reason'])
		{
			$this->_registration['reason'] = $this->_registration['reasontxt'];
		}
		$this->_registration['password'] = Request::getVar('password', null, 'post');
		$this->_registration['confirmPassword'] = Request::getVar('password2', null, 'post');
		$this->_registration['usageAgreement'] = Request::getVar('usageAgreement', null, 'post');
		$this->_registration['sendEmail'] = Request::getVar('sendEmail', null, 'post');
		$this->_registration['sex'] = Request::getVar('sex', null, 'post');
		$this->_registration['interests'] = Request::getVar('interests',null,'post');
		$this->_registration['orcid'] = Request::getVar('orcid', null, 'post');

		if ($this->_registration['sex'] !== null)
		{
			if ($this->_registration['sex'] == 'unspecified')
			{
				$this->_registration['sex'] = '';
			}
		}

		if ($this->_registration['usageAgreement'] !== null)
		{
			$this->_registration['usageAgreement'] = ($this->_registration['usageAgreement'] === 'unset') ? false : true;
		}

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

		// get user tags
		$tag_string = $xprofile->tags('string');

		//get member addresses
		require_once(dirname(__DIR__) . DS . 'tables' . DS . 'address.php');
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
		$this->set('race', $xprofile->get('race'));
		$this->set('login', $xprofile->get('username'));
		$this->set('email', $xprofile->get('email'));
		$this->set('confirmEmail', $xprofile->get('email'));
		$this->set('url', $xprofile->get('url'));
		$this->set('phone', $xprofile->get('phone'));
		$this->set('name', $xprofile->get('name'));
		$this->set('givenName', $xprofile->get('givenName'));
		$this->set('middleName', $xprofile->get('middleName'));
		$this->set('surname', $xprofile->get('surname'));
		$this->set('orgtype', $xprofile->get('orgtype'));
		$this->set('org', $xprofile->get('organization'));
		$this->set('orgtext', '');
		$this->set('reason', $xprofile->get('reason'));
		$this->set('reasontxt', '');
		$this->set('password', null);
		$this->set('confirmPassword', null);
		$this->set('sex', $xprofile->get('gender'));
		$this->set('usageAgreement', $xprofile->get('usageAgreement'));
		$this->set('sendEmail', $xprofile->get('sendEmail'));
		$this->set('interests', $xprofile->tags('string'));
		$this->set('address', $addresses);
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
	 * Short description for 'get'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $key Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function get($key)
	{
		//$this->logDebug("self:get($key)");

		if (!array_key_exists($key, $this->_registration))
		{
			die(__CLASS__ . "::" . __METHOD__ . "() Unknown key: $key \n");
		}

		return $this->_registration[$key];
	}

	/**
	 * Short description for 'set'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $key Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function set($key,$value)
	{
		//$this->logDebug("self:set($key,$value)");

		$this->_checked = false;

		if (!array_key_exists($key, $this->_registration))
		{
			die(__CLASS__ . "::" . __METHOD__ . "() Unknown key: $key \n");
		}

		$this->_registration[$key] = $value;

		//if (($key == 'login') || ($key == 'email'))
			//unset($this->_encoded[$key]);
	}

	/**
	 * Short description for 'registrationField'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      unknown $default Parameter description (if any) ...
	 * @param      string $task Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
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
		$_invalid = array();
		$registrationUsername = $this->registrationField('registrationUsername','RROO',$task);
		$registrationPassword = $this->registrationField('registrationPassword','RRHH',$task);
		$registrationConfirmPassword = $this->registrationField('registrationConfirmPassword','RRHH',$task);
		$registrationFullname = $this->registrationField('registrationFullname','RRRR',$task);
		$registrationEmail = $this->registrationField('registrationEmail','RRRR',$task);
		$registrationConfirmEmail = $this->registrationField('registrationConfirmEmail','RRRR',$task);
		$registrationURL = $this->registrationField('registrationURL','HHHH',$task);
		$registrationPhone = $this->registrationField('registrationPhone','HHHH',$task);
		$registrationEmployment = $this->registrationField('registrationEmployment','HHHH',$task);
		$registrationOrganization = $this->registrationField('registrationOrganization','HHHH',$task);
		$registrationCitizenship = $this->registrationField('registrationCitizenship','HHHH',$task);
		$registrationResidency = $this->registrationField('registrationResidency','HHHH',$task);
		$registrationSex = $this->registrationField('registrationSex','HHHH',$task);
		$registrationDisability = $this->registrationField('registrationDisability','HHHH',$task);
		$registrationHispanic = $this->registrationField('registrationHispanic','HHHH',$task);
		$registrationRace = $this->registrationField('registrationRace','HHHH',$task);
		$registrationInterests = $this->registrationField('registrationInterests','HHHH',$task);
		$registrationReason = $this->registrationField('registrationReason','HHHH',$task);
		$registrationOptIn = $this->registrationField('registrationOptIn','HHHH',$task);
		$registrationCAPTCHA = $this->registrationField('registrationCAPTCHA','HHHH',$task);
		$registrationTOU = $this->registrationField('registrationTOU','HHHH',$task);
		$registrationAddress = $this->registrationField('registrationAddress','OOOO',$task);
		$registrationORCID = $this->registrationField('registrationORCID','HHHO',$task);

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
			if (!empty($login) && !Helpers\Utility::validlogin($login, $allowNumericFirstCharacter) )
			{
				$this->_invalid['login'] = 'Invalid login name. Please type at least 2 characters and use only alphanumeric characters.';
			}
		}

		if (!empty($login) && ($task == 'create' || $task == 'proxycreate' || $task == 'update'))
		{
			jimport('joomla.user.helper');

			$uid = \JUserHelper::getUserId($login);

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
			$msg = \Hubzero\Password\Rule::verify($registration['password'],$rules,$login,$registration['name']);
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
				$bits = explode(' ',$registration['name']);
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
			if (!empty($registration['name']) && !Helpers\Utility::validname($registration['name']) )
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
				$usersConfig = \Component::params( 'com_users' );
				$allow_duplicate_emails = $usersConfig->get( 'allow_duplicate_emails' );

				// Check if the email is already in use
				$db = \App::get('db');
				$query = "SELECT `id` FROM `#__users` WHERE `email` = " . $db->quote($email) . " AND `id` != " . (int)$id;
				$db->setQuery($query);
				$xid = intval($db->loadResult());

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
							$db = \App::get('db');
							$query = "SELECT `email` FROM `#__users` WHERE `id` = " . (int)$id;
							$db->setQuery($query);
							$currentEmail = $db->loadResult();

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

		$fields = Field::all()
			->including(['options', function ($option){
				$option
					->select('*')
					->ordered();
			}])
			->where('action_' . $task, '=', Field::STATE_REQUIRED)
			->ordered()
			->rows();

		foreach ($fields as $field)
		{
			if ($field->get('type') != 'hidden')
			{
				$value = $this->get($field->get('name'));

				if (empty($value))
				{
					$check['_missing'] = $field->get('label');
				}
			}
		}

		/*
		if ($registrationURL == REG_REQUIRED)
		{
			if (empty($registration['url']))
			{
				$this->_missing['url'] = 'Personal Web Page';
				$this->_invalid['url'] = 'Please provide a valid urlsite URL';
			}
		}

		if ($registrationURL != REG_HIDE)
		{
			$registration['url'] = trim($registration['url']);
			if (!empty($registration['url']) && (strstr($registration['url'], ' ') || !Helpers\Utility::validurl($registration['url'])))
			{
				$this->_invalid['url'] = 'Invalid url site URL. You may be using characters that are not allowed.';
			}
		}

		if ($registrationORCID == REG_REQUIRED)
		{
			if (empty($registration['orcid']))
			{
				$this->_missing['orcid'] = 'ORCID';
				$this->_invalid['orcid'] = 'Please provide a valid ORCID';
			}
		}

		if ($registrationORCID != REG_HIDE)
		{
			if (!empty($registration['orcid']) && !Helpers\Utility::validorcid($registration['orcid']))
			{
				$this->_invalid['orcid'] = 'Invalid ORCID. It should be in the form of XXXX-XXXX-XXXX-XXXX.';
			}
		}

		if ($registrationPhone == REG_REQUIRED)
		{
			if (empty($registration['phone']))
			{
				$this->_missing['phone'] = 'Phone Number';
				$this->_invalid['phone'] = 'Please provide a valid phone number';
			}
		}

		if ($registrationPhone != REG_HIDE)
		{
			if (!empty($registration['phone']) && !Helpers\Utility::validphone($registration['phone']))
			{
				$this->_invalid['phone'] = 'Invalid phone number. You may be using characters that are not allowed.';
			}
		}

		if ($registrationEmployment == REG_REQUIRED)
		{
			if (empty($registration['orgtype']))
			{
				$this->_missing['orgtype'] = 'Employment Type';
				$this->_invalid['orgtype'] = 'Please make an employment type selection';
			}
		}

		if ($registrationOrganization == REG_REQUIRED)
		{
			if (empty($registration['org']) && empty($registration['orgtext']))
			{
				$this->_missing['org'] = 'Organization';
				$this->_invalid['org'] = 'Invalid affiliation';
			}
		}

		if ($registrationOrganization != REG_HIDE)
		{
			if (!empty($registration['org']) && !Helpers\Utility::validtext($registration['org']))
			{
				$this->_invalid['org'] = 'Invalid affiliation. You may be using characters that are not allowed.';
			}
			elseif (!empty($registration['orgtext']) && !Helpers\Utility::validtext($registration['orgtext']))
			{
				$this->_invalid['org'] = 'Invalid affiliation. You may be using characters that are not allowed.';
			}
		}

		if ($registrationCitizenship == REG_REQUIRED)
		{
			if (empty($registration['countryorigin']))
			{
				$this->_missing['countryorigin'] = 'Country of Citizenship / Permanent Residence';
				$this->_invalid['countryorigin'] = 'Invalid country of origin.';
			}
		}

		if ($registrationCitizenship != REG_HIDE)
		{
			if (!empty($registration['countryorigin']) && !Helpers\Utility::validtext($registration['countryorigin']))
			{
				$this->_invalid['countryorigin'] = 'Invalid country of origin. You may be using characters that are not allowed.';
			}
		}

		if ($registrationResidency == REG_REQUIRED)
		{
			if (empty($registration['countryresident']))
			{
				$this->_missing['countryresident'] = 'Country of Current Residence';
				$this->_invalid['countryresident'] = 'Invalid country of residency';
			}
		}

		if ($registrationResidency != REG_HIDE)
		{
			if (!empty($registration['countryresident']) && !Helpers\Utility::validtext($registration['countryresident']))
			{
				$this->_invalid['countryresident'] = 'Invalid country of residency. You may be using characters that are not allowed.';
			}
		}

		if ($registrationSex == REG_REQUIRED)
		{
			if (empty($registration['sex']))
			{
				$this->_missing['sex'] = 'Gender';
				$this->_invalid['sex'] = 'Please select gender.';
			}
		}

		if ($registrationSex != REG_HIDE)
		{
			if (!empty($registration['sex']) && !Helpers\Utility::validtext($registration['sex']))
			{
				$this->_invalid['sex'] = 'Invalid gender selection.';
			}
		}

		if ($registrationDisability == REG_REQUIRED)
		{
			if (empty($registration['disability']))
			{
				$this->_missing['disability'] = 'Disability Information';
				$this->_invalid['disability'] = 'Please indicate any disabilities you may have.';
			}
		}

		if ($registrationDisability != REG_HIDE)
		{
			if (!empty($registration['disability']) && in_array('yes', $registration['disability']))
			{
				$this->_invalid['disability'] = 'Invalid disability selection.';
			}
		}

		if ($registrationHispanic == REG_REQUIRED)
		{
			if (empty($registration['hispanic']))
			{
				$this->_missing['hispanic'] = 'Hispanic Ethnic Heritage';
				$this->_invalid['hispanic'] = 'Please make a selection or choose not to reveal.';
			}
		}

		if ($registrationRace == REG_REQUIRED)
		{
			if ($task == 'edit')
			{
				$corigin_incoming = (in_array('countryorigin', $field_to_check)) ? true : false;
				$profile = \Hubzero\User\Profile::getInstance(User::get('id'));
			}
			else
			{
				$corigin_incoming = true;
			}

			if (empty($registration['race'])
				&& (($corigin_incoming && strtolower($registration['countryorigin']) == 'us')
					|| (!$corigin_incoming && isset($profile) && strtolower($profile->get('countryorigin')) == 'us'))
			){
				$this->_missing['race'] = 'Racial Background';
				$this->_invalid['race'] = 'Please make a selection or choose not to reveal.';
			}
		}

		if ($registrationInterests == REG_REQUIRED)
		{
			if (empty($registration['interests']) || $registration['interests'] == '')
			{
				$this->_missing['interests'] = 'Interests';
				$this->_invalid['interests'] = 'Please select materials your are interested in';
			}
		}

		if ($registrationReason == REG_REQUIRED)
		{
			if (empty($registration['reason']) && empty($registration['reasontxt']))
			{
				$this->_missing['reason'] = 'Reason for registering';
				$this->_invalid['reason'] = 'Reason for registering';
			}
		}

		if ($registrationReason != REG_HIDE)
		{
			if (!empty($registration['reason']) && !Helpers\Utility::validtext($registration['reason']))
			{
				$this->_invalid['reason'] = 'Invalid reason text. You may be using characters that are not allowed.';
			}
			if (!empty($registration['reasontxt']) && !Helpers\Utility::validtext($registration['reasontxt']))
			{
				$this->_invalid['reason'] = 'Invalid reason text. You may be using characters that are not allowed.';
			}
		}
		*/

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

		/*
		if ($registrationTOU != REG_HIDE)
			if (!empty($registration['usageAgreement']))
				$this->_invalid['usageAgreement'] = 'Usage Agreement has not been Read and Accepted';
		*/

		if ($registrationAddress == REG_REQUIRED)
		{
			if (count($registration['address']) == 0)
			{
				$this->_missing['address'] = 'Member Address';
				$this->_invalid['address'] = 'Member Address';
			}
		}

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
	 * Short description for 'scorePassword'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $password Parameter description (if any) ...
	 * @param      string $username Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
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
	 * @param string Username to check
	 * @return array: status & message
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

		// Initialize database
		$db = \App::get('db');

		$query = 'SELECT id FROM `#__users` WHERE username = ' . $db->Quote( $username );
		$db->setQuery($query);
		$db->query();

		$num_rows = $db->getNumRows();

		if ($num_rows > 0)
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
	 * @param 	string 		Email address or preferrd username
	 * @return	string 		Generated username
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

