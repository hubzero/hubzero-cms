<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

define('REG_HIDE', 0);
define('REG_OPTIONAL', 1);
define('REG_REQUIRED', 2);
define('REG_READONLY', 4);
define('PASS_SCORE_BAD', 0);
define('PASS_SCORE_MEDIOCRE', 34);
define('PASS_SCORE_GOOD', 50);
define('PASS_SCORE_STRONG', 68);
//ximport('account.acct_func');
ximport('misc_func');

class XRegistration
{
	var $_registration;
	var $_encoded;
	var $_missing;
	var $_invalid;
	var $_checked;

	function clear()
	{
		$this->_registration = array();
		$this->normalize();
		$this->_missing = array();
		$this->_invalid = false;
		$this->_checked = true;
	}

	function __construct($login = null)
	{
		//$this->logDebug("XRegistration::__construct()");

		$this->clear();
	}

	function normalize()
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
		$this->_registration['web'] = null;
		$this->_registration['phone'] = null;
		$this->_registration['name'] = null;
		$this->_registration['orgtype'] = null;
		$this->_registration['org'] = null;
		$this->_registration['orgtext'] = null;
		$this->_registration['reason'] = null;
		$this->_registration['reasontxt'] = null;
		$this->_registration['password'] = null;
		$this->_registration['confirmPassword'] = null;
		$this->_registration['sex'] = null;
		$this->_registration['usageAgreement'] = null;
		$this->_registration['mailPreferenceOption'] = null;
	}

	function loadPost()
	{
		// fill out registration data based on current form POST
		//
		// note that a value of null means the field doesn't exist
		// we use this to detect when to delete data when
		// merging registrations with xuser data
		//
		// TODO: more cleanup

		$coriginus_p = JRequest::getVar('corigin_us', null, 'post');
		$corigin_p = JRequest::getVar('corigin', null, 'post');
		$cresidentus_p = JRequest::getVar('cresident_us', null, 'post');
		$cresident_p = JRequest::getVar('cresident', null, 'post');
		$disability_p = JRequest::getVar('disability', null, 'post');
		$disabilityblind_p = JRequest::getVar('disabilityblind', null, 'post');
		$disabilitydeaf_p = JRequest::getVar('disabilitydeaf', null, 'post');
		$disabilityphysical_p = JRequest::getVar('disabilityphysical', null, 'post');
		$disabilitylearning_p = JRequest::getVar('disabilitylearning', null, 'post');
		$disabilityvocal_p = JRequest::getVar('disabilityvocal', null, 'post');
		$disabilityother_p = JRequest::getVar('disabilityother', null, 'post');
		$hispanic_p = JRequest::getVar('hispanic', null, 'post');
		$hispaniccuban_p = JRequest::getVar('hispaniccuban', null, 'post');
		$hispanicmexican_p = JRequest::getVar('hispanicmexican', null, 'post');
		$hispanicpuertorican_p = JRequest::getVar('hispanicpuertorican', null, 'post');
		$hispanicother_p = JRequest::getVar('hispanicother',null,'post');
		$racenativeamerican_p = JRequest::getVar('racenativeamerican', null, 'post');
		$racenativetribe_p = JRequest::getVar('racenativetribe', null, 'post');
		$raceasian_p = JRequest::getVar('raceasian', null, 'post');
		$raceblack_p = JRequest::getVar('raceblack', null, 'post');
		$racehawaiian_p = JRequest::getVar('racehawaiian', null, 'post');
		$racewhite_p = JRequest::getVar('racewhite', null, 'post');
		$racerefused_p = JRequest::getVar('racerefused', null, 'post');
		$interests_p = JRequest::getVar('interests',null,'post');

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

			if (strcasecmp($corigin, 'US') == 0)
			{
				if ($racenativeamerican_p)
				{
					$race[] = 'nativeamerican';
					$racenativetribe = $racenativetribe_p;
				}
				if ($raceasian_p)
					$race[] = 'asian';
				if ($raceblack_p)
					$race[] = 'black';
				if ($racehawaiian_p)
					$race = 'hawaiian';
				if ($racewhite_p)
					$race = 'white';
				if ($racerefused_p)
					$race = 'refused';
			}
		}

		if ($interests_p === null) // field not on form
		{
			$role = null;
			$edulevel = null;
		}
		else
		{
			$role = array();
			$edulevel = array();

			if ( JRequest::getVar('rolestudent', '', 'post') )
				$role[] = 'student';

	 		if ( JRequest::getVar('roleeducator', '', 'post') )
				$role[] = 'educator';

			if ( JRequest::getVar('roleresearcher', '', 'post') )
				$role[] = 'researcher';

			if ( JRequest::getVar('roledeveloper', '', 'post') )
				$role[] = 'developer';

			if ( JRequest::getVar('edulevelk12', '', 'post') )
				$edulevel[] = 'k12';

			if ( JRequest::getVar('edulevelundergraduate', '', 'post') )
				$edulevel[] = 'undergraduate';

			if ( JRequest::getVar('edulevelgraduate', '', 'post') )
				$edulevel[] = 'graduate';
		}

		$name = JRequest::getVar('name', array(), 'post');
		if (!is_array($name)) {
			$name = array();
		}
		$nm  = trim($name['first']);
		$nm .= (isset($name['middle']) && trim($name['middle']) != '') ? ' '.$name['middle'] : '';
		$nm .= ' '.trim($name['last']);
		$this->_registration['name'] = $nm;

		$this->_registration['countryresident'] = $cresident; 
		$this->_registration['countryorigin']	= $corigin;
		$this->_registration['nativetribe'] = $racenativetribe;
		$this->_registration['role'] = $role;
		$this->_registration['edulevel'] = $edulevel;
		$this->_registration['hispanic'] = $hispanic;
		$this->_registration['disability'] = $disability;
		$this->_registration['race'] = $race;
		$this->_registration['login'] = strtolower(JRequest::getVar('login', null, 'post')); 
		$this->_registration['email'] = JRequest::getVar('email', null, 'post');
		$this->_registration['confirmEmail'] = JRequest::getVar('email2', null, 'post');
		$this->_registration['web'] = JRequest::getVar('web', null, 'post');
		$this->_registration['phone'] = JRequest::getVar('phone', null, 'post');
		//$this->_registration['name'] = JRequest::getVar('name', null, 'post');
		$this->_registration['orgtype']	= JRequest::getVar('orgtype', null, 'post');
		$this->_registration['org'] = JRequest::getVar('org', null, 'post'); 
		$this->_registration['orgtext']	= JRequest::getVar('orgtext', null, 'post');
		$this->_registration['reason'] = JRequest::getVar('reason', null, 'post');
		$this->_registration['reasontxt'] = JRequest::getVar('reasontxt', null, 'post');
		$this->_registration['password'] = JRequest::getVar('password', null, 'post');
		$this->_registration['confirmPassword'] = JRequest::getVar('password2', null, 'post');
		$this->_registration['usageAgreement'] = JRequest::getVar('usageAgreement', null, 'post');
		$this->_registration['mailPreferenceOption'] = JRequest::getVar('mailPreferenceOption', null, 'post');
		$this->_registration['sex'] = JRequest::getVar('sex', null, 'post');

		if ($this->_registration['sex'] !== null)
			if ($this->_registration['sex'] == 'unspecified')
				$this->_registration['sex'] = '';

		if ($this->_registration['usageAgreement'] !== null)
			$this->_registration['usageAgreement'] = ($this->_registration['usageAgreement'] === 'unset') ? false : true;

		if ($this->_registration['mailPreferenceOption'] !== null)	
			if ($this->_registration['mailPreferenceOption'] == 'unset')
				$this->_registration['mailPreferenceOption'] = '0';
			else
				$this->_registration['mailPreferenceOption'] = '2';

		$this->_checked = false;
	}

	function loadXUser($xuser = null)
	{
		$this->clear();
		
		if (!is_object($xuser))
			return;

		$this->set('countryresident', $xuser->get('countryresident'));
		$this->set('countryorigin', $xuser->get('countryorigin'));
		$this->set('nativetribe', $xuser->get('nativetribe'));
		$this->set('role', $xuser->get('role'));
		$this->set('edulevel', $xuser->get('edulevel'));
		$this->set('hispanic', $xuser->get('hispanic'));
		$this->set('disability', $xuser->get('disability'));
		$this->set('race', $xuser->get('race'));
		$this->set('login', $xuser->get('login'));
		$this->set('email', $xuser->get('email'));
		$this->set('confirmEmail', $xuser->get('email'));
		$this->set('web', $xuser->get('web'));
		$this->set('phone', $xuser->get('phone'));
		$this->set('name', $xuser->get('name'));
		$this->set('orgtype', $xuser->get('orgtype'));
		$this->set('org', $xuser->get('org'));
		$this->set('orgtext', '');
		$this->set('reason', $xuser->get('reason'));
		$this->set('reasontxt', '');
		$this->set('password', null);
		$this->set('confirmPassword', null);
		$this->set('sex', $xuser->get('sex'));
		$this->set('usageAgreement', $xuser->get('usageagreement'));
		$this->set('mailPreferenceOption', $xuser->get('mailPreferenceOption'));

		$parts = explode(':', $this->_registration['login'] );

		if ( count($parts) == 3 && intval($parts[0]) < 0 )
			$this->_encoded['login'] = pack("H*", $parts[1]);

		if (eregi( "\.localhost\.invalid$", $this->_registration['email']))
		{
			$parts = explode('@', $this->_registration['email']);
			$parts = explode('-', $parts[0]);
			$this->_encoded['email'] = pack("H*", $parts[2]);
		}

		$this->_checked = false;
	}	

	function get($key)
	{
		//$this->logDebug("XRegistration::get($key)");

		if (!array_key_exists($key, $this->_registration))
			die("XRegistration::get() Unknown key: $key \n");

		return $this->_registration[$key];
	}
		
	function set($key,$value)
	{
		//$this->logDebug("XRegistration::set($key,$value)");
			
		$this->_checked = false;

		if (!array_key_exists($key, $this->_registration))
			die("XRegistration::set() Unknown key: $key \n");

		$this->_registration[$key] = $value;

		if (($key == 'login') || ($key == 'email'))
			unset($this->_encoded[$key]);
	}

	private function registrationField($name, $default, $task = 'register')
	{
		$xhub =& XFactory::getHub();

		if (($task == 'register') || ($task == 'create') || ($task == 'new'))
			$index = 0;
		else if ($task == 'proxycreate')
			$index = 1;
		else if ($task == 'update')
			$index = 2;
		else if ($task == 'edit')
			$index = 3;
		else
			$index = 0;

		$default = str_pad($default, '-', 4);
		$configured = $xhub->getCfg($name, $default);
		$length = strlen($configured);

		if ( $length > $index )
			$value = substr($configured, $index, 1);
		else
			$value = substr($default, $index, 1);

		switch($value)
		{
			case 'R': return(REG_REQUIRED);
			case 'O': return(REG_OPTIONAL);
			case 'H': return(REG_HIDE);
			case '-': return(REG_HIDE);
			case 'U': return(REG_READONLY);
			default : return(REG_HIDE);
		}
	}
	
	function validateUsername($username = '')
	{
		$username = trim($username);
		
		if ( trim($username) == '')
			return false;

		if (eregi( "[\<|\>|\"|\'|\%|\;|\(|\)|\&]", $username) || strlen(utf8_decode($username )) < 2)
			return false;

		if (eregi( "[\s]", $username))
			return false;

		if ($username[0] == '-')
			return false;

		if (eregi( "[\:]", $username))
			return false;
	
 		XUserHelper::xuser_exists($username);
	}

	function uniqueUsername($username)
	{
	}

	/**
	 * Returns userid if a email exists
	 *
	 * @param string The email to search on
	 * @return int The user id or 0 if not found
	 */
	function getEmailId($email)
	{
		// Initialize some variables
		$db = & JFactory::getDBO();

		$query = 'SELECT id FROM #__users WHERE email = ' . $db->Quote( $email );
		$db->setQuery($query, 0, 1);
		return $db->loadResult();
	}

	function check($task = 'create', $id = 0)
	{
		ximport('xuserhelper');
		ximport('xregistrationhelper');



		$juser =& JFactory::getUser();
		
		if ($id == 0) {
			$id = $juser->get('id');
		}

		$registration = $this->_registration;
		$xhub =& XFactory::getHub();

		if ($task == 'proxy')
			$task = 'proxycreate';

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
		$registrationTOU = $this->registrationField('registrationTOU','HHHH',$task);

		if ($task == 'update')
		{
			if ($this->_encoded['login'])
				$registrationUsername = REG_REQUIRED;
			else
				$registrationUsername = REG_READONLY;

			$registrationPassword = REG_HIDE;
			$registrationConfirmPassword = REG_HIDE;
			
			if ($this->_encoded['email'])
				$registrationEmail = REG_REQUIRED;
		}

		if ($task == 'edit')
		{
			$registrationUsername = REG_READONLY;
			$registrationPassword = REG_HIDE;
			$registrationConfirmPassword = REG_HIDE;
		}

		if ($this->_encoded['login'])
			$login = $this->_encoded['login'];
		else
			$login = $registration['login'];

		if ($this->_encoded['email'])
		{
			$email = $this->_encoded['email'];
			$confirmEmail = $email;
		}
		else
		{
			$email = $registration['email'];
			$confirmEmail = $registration['confirmEmail'];
		}

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
			if (!empty($login) && !XRegistrationHelper::validlogin($login) )
				$this->_invalid['login'] = 'Invalid login name. Please use only alphanumeric characters.';
		}
		
		if ($task == 'create' || $task == 'proxycreate' || $task == 'update')
		{
			jimport('joomla.user.helper');

			$uid = JUserHelper::getUserId($login);

			if ($uid && $uid != $id)
				$this->_invalid['login'] = 'The user login "'. htmlentities($login) .'" already exists. Please try another.';

			$uid = XUserHelper::getUserId($login);

			if ($uid && $uid != $id)
				$this->_invalid['login'] = 'The user login "'. htmlentities($login) .'" already exists. Please try another.';
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
				$result = XRegistrationHelper::valid_password($registration['password']);

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
			if ($registration['password'] != $registration['confirmPassword'])
				$this->_invalid['confirmPassword'] = 'Passwords do not match. Please correct and try again.';

		if ($registrationPassword == REG_REQUIRED) {
			$score = $this->scorePassword($registration['password'], $registration['login']);
			if ($score < PASS_SCORE_MEDIOCRE) {
				$this->_invalid['password'] = 'Password strength is too weak.';
			} else if ($score >= PASS_SCORE_MEDIOCRE && $score < PASS_SCORE_GOOD) {
				// Mediocre pass
			} else if ($score >= PASS_SCORE_GOOD && $score < PASS_SCORE_STRONG) {
				// Good pass
			} else if ($score >= PASS_SCORE_STRONG) {
				// Strong pass
			}
		}
			
		if ($registrationFullname == REG_REQUIRED)
		{
			if (empty($registration['name']))
			{
				$this->_missing['name'] = 'Full Name';
				$this->_invalid['name'] = 'Please provide a name.';
			} else {
				$bits = explode(' ',$registration['name']);
				$surname = array_pop($bits);
				if (count($bits) >= 1) {
					$givenName = array_shift($bits);
				}
				if (count($bits) >= 1) {
					$middleName = implode(' ',$bits);
				}
				if (!$surname || !$givenName) {
					$this->_missing['name'] = 'Full Name';
					$this->_invalid['name'] = 'Please provide a name.';
				}
			}
		}

		if ($registrationFullname != REG_HIDE)
			if (!empty($registration['name']) && !XRegistrationHelper::validtext($registration['name']) )
				$this->_invalid['name'] = 'Invalid name. You may be using characters that are not allowed.';


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
			if (!empty($email) && !XRegistrationHelper::validemail($email))
				$this->_invalid['email'] = 'Invalid email address. Please correct and try again.';
			else
			{
				$eid = $this->getEmailId($email);

				if ($xhub->getCfg('hubShortName') == 'nanoHUB.org')
					$allow_duplicate_email = true;
				else
					$allow_duplicate_email = false;

				if (!$allow_duplicate_email && ($eid && $eid != $id)) // TODO: RESOLVE NANOHUB MULTIPLE EMAIL ACCOUNT USAGE
				{
					$this->_missing['email'] = 'Valid Email';
					$this->_invalid['email'] = 'An existing account is already using this e-mail address.';
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
			if ($email != $confirmEmail)
			{
				if (empty($this->_invalid['email']))
				{
					$this->_invalid['confirmEmail'] = 'Email addresses do not match. Please correct and try again.';
					$this->_invalid['email'] = 'Email addresses do not match. Please correct and try again.';
				}
			}

		if ($registrationURL == REG_REQUIRED)
		{
			if (empty($registration['web']))
			{
				$this->_missing['web'] = 'Personal Web Page';
				$this->_invalid['web'] = 'Please provide a valid website URL';
			}
		}

		if ($registrationURL != REG_HIDE)
			if (!empty($registration['web']) && !XRegistrationHelper::validurl($registration['web']))
				$this->_invalid['web'] = 'Invalid web site URL. You may be using characters that are not allowed.';

		if ($registrationPhone == REG_REQUIRED)
		{
			if (empty($registration['phone']))
			{
				$this->_missing['phone'] = 'Phone Number';
				$this->_invalid['phone'] = 'Please provide a valid phone number';
			}
		}

		if ($registrationPhone != REG_HIDE)
			if (!empty($registration['phone']) && !XRegistrationHelper::validphone($registration['phone']))
				$this->_invalid['phone'] = 'Invalid phone number. You may be using characters that are not allowed.';

		if (($registrationEmployment == REG_REQUIRED) || ($juser->get('username') == 'nkissebe2'))
		{
			if (empty($registration['orgtype']))
			{
				$this->_missing['orgtype'] = 'Employment Status';
				$this->_invalid['orgtype'] = 'Please make an employment status selection';
			}
		}

/*
		if ($registrationEmployment != REG_HIDE)
			if (empty($registration['orgtype']))
			{
				//if (! XRegistrationHelper::validateOrgType($registration['orgtype']) )
					$this->_invalid['orgtype'] = 'Invalid employment status. Please make a new selection.';
			}
*/

		if ($registrationOrganization == REG_REQUIRED)
		{
			if (empty($registration['org']))
			{
				$this->_missing['org'] = 'Organization';
				$this->_invalid['org'] = 'Invalid school/organization';
			}
		}

		if ($registrationOrganization != REG_HIDE)
			if (!empty($registration['org']) && !XRegistrationHelper::validtext($registration['org']))
				$this->_invalid['org'] = 'Invalid school/organization. You may be using characters that are not allowed.';

		if ($registrationCitizenship == REG_REQUIRED)
		{
			if (empty($registration['countryorigin']))
			{
				$this->_missing['countryorigin'] = 'Country of Citizenship / Permanent Residence';
				$this->_invalid['countryorigin'] = 'Invalid country of origin.';
			}
		}

		if ($registrationCitizenship != REG_HIDE)
			if (!empty($registration['countryorigin']) && !XRegistrationHelper::validtext($registration['countryorigin']))
				$this->_invalid['countryorigin'] = 'Invalid country of origin. You may be using characters that are not allowed.';

		if ($registrationResidency == REG_REQUIRED)
		{
			if (empty($registration['countryresident']))
			{
			$this->_missing['countryresident'] = 'Country of Current Residence';
				$this->_invalid['countryresident'] = 'Invalid country of residency';
			}
		}

		if ($registrationResidency != REG_HIDE)
			if (!empty($registration['countryresident']) && !XRegistrationHelper::validtext($registration['countryresident']))
				$this->_invalid['countryresident'] = 'Invalid country of residency. You may be using characters that are not allowed.'; 

		if ($registrationSex == REG_REQUIRED)
		{
			if (empty($registration['sex']))
			{
				$this->_missing['sex'] = 'Sex';
				$this->_invalid['sex'] = 'Please select gender.';
			}
		}

		if ($registrationSex != REG_HIDE)
			if (!empty($registration['sex']) && !XRegistrationHelper::validtext($registration['sex']))
				$this->_invalid['sex'] = 'Invalid gender selection.';

		if ($registrationDisability == REG_REQUIRED)
		{
			if (empty($registration['disability']))
			{
				$this->_missing['disability'] = 'Disability Information';
				$this->_invalid['disability'] = 'Please indicate any disabilities you may have.';
			}
		}

		if ($registrationDisability != REG_HIDE)
			if (!empty($registration['disability']) && in_array('yes', $registration['disability']))
				$this->_invalid['disability'] = 'Invalid disability selection.';

		if ($registrationHispanic == REG_REQUIRED)
		{
			if (empty($registration['hispanic']))
			{
				$this->_missing['hispanic'] = 'Hispanic Ethnic Heritage';
				$this->_invalid['hispanic'] = 'Please make a selection or choose not to reveal.';
			}
		}

		/*
		if ($registrationHispanic != REG_HIDE)
			if (!empty($registration['hispanic']))
				$this->_invalid['hispanic'] = 'Invalid hispanic heritage selection.';
		*/

		if ($registrationRace == REG_REQUIRED)
		{
			if (empty($registration['race']))
			{
				$this->_missing['race'] = 'Racial Background';
				$this->_invalid['race'] = 'Please make a selection or choose not to reveal.';
			}
		}

		/*
		if ($registrationRace != REG_HIDE)
			if (!empty($registration['race']) && !XRegistrationHelper::validtext($registration['race']))
				$this->_invalid['race'] = 'Invalid racial selection.';
		*/

		if ($registrationInterests == REG_REQUIRED)
		{
			if (empty($registration['edulevel']) && empty($registration['role']))
			{
				$this->_missing['interests'] = 'Interests';
				$this->_invalid['interests'] = 'Please select materials your are interested in';
			}
		}

		/*
		if ($registrationInterests != REG_HIDE)
		{
			if (!empty($registration['edulevel']) && !XRegistrationHelper::validtext($registration['edulevel']))
				$this->_invalid['interests'] = 'Invalid interest selection.';
			if (!empty($registration['role']) && !XRegistrationHelper::validtext($registration['role']))
				$this->_invalid['interests'] = 'Invalid interest selection.';
		}
		*/

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
			if (!empty($registration['reason']) && !XRegistrationHelper::validtext($registration['reason'])) {
				$this->_invalid['reason'] = 'Invalid reason text. You may be using characters that are not allowed.';
			}
			if (!empty($registration['reasontxt']) && !XRegistrationHelper::validtext($registration['reasontxt'])) {
				$this->_invalid['reason'] = 'Invalid reason text. You may be using characters that are not allowed.';
			}
		}

		if ($registrationOptIn == REG_REQUIRED)
		{
			if (empty($registration['mailPreferenceOption']))
			{
				$this->_missing['mailPreferenceOption'] = 'Opt-In for mailings';
				$this->_invalid['mailPreferenceOption'] = 'Registration requires Opt-In of mailings.';
			}
		}

		/*
		if ($registrationOptIn != REG_HIDE)
			if (!empty($registration['mailPreferenceOption']))
				$this->_invalid['mailPreferenceOption'] = 'Opt-In for mailings has not been selected';
		*/

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

		if (empty($this->_missing) && empty($this->_invalid) && empty($this->_encoded))
			return true;

		return false;
	}
	
	function scorePassword($password, $username='') 
	{
		$score = 0;

		if ($username) {
			if (strtolower($password) == strtolower($username)) {
				return $score;
			}
		}

		$seen = array();

		for ( $i = 0 ; $i < strlen($password) ; $i++ ) 
		{
			$char = substr($password, $i, 1);

			if (!isset($seen[$char])) {
				$seen[$char] = 1;
			} else {
				$seen[$char]++;
			}

			if (is_numeric($char)) {
				$s = 16;
			} else {
				$s = 8;
			}

			if ($seen[$char] != 1) {
				for ( $k = 1 ; $k < $seen[$char] ; $k++ ) 
				{
					$s = $s / 2;
				}
			}
			$score += ($s >= 1) ? $s : 0;
		}

		$score = ($score > 100) ? 100 : $score;

		return $score;
	}
}
?>
