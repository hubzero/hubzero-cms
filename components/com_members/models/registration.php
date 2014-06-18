<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'utility.php');

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
class MembersModelRegistration
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
	 * Short description for 'clear'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
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
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $login Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($login = null)
	{
		//$this->logDebug("self::__construct()");

		$this->clear();
	}

	/**
	 * Short description for 'normalize'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
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
		$this->_registration['web'] = null;
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
		$this->_registration['mailPreferenceOption'] = null;
		$this->_registration['captcha'] = null;
		$this->_registration['interests'] = null;
		$this->_registration['address'] = null;
		$this->_registration['orcid'] = null;
	}

	/**
	 * Short description for 'loadPost'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
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
		//$interests_p = JRequest::getVar('interests',null,'post');

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

		// 	if ( JRequest::getVar('rolestudent', '', 'post') )
		// 		$role[] = 'student';

	 // 		if ( JRequest::getVar('roleeducator', '', 'post') )
		// 		$role[] = 'educator';

		// 	if ( JRequest::getVar('roleresearcher', '', 'post') )
		// 		$role[] = 'researcher';

		// 	if ( JRequest::getVar('roledeveloper', '', 'post') )
		// 		$role[] = 'developer';

		// 	if ( JRequest::getVar('edulevelk12', '', 'post') )
		// 		$edulevel[] = 'k12';

		// 	if ( JRequest::getVar('edulevelundergraduate', '', 'post') )
		// 		$edulevel[] = 'undergraduate';

		// 	if ( JRequest::getVar('edulevelgraduate', '', 'post') )
		// 		$edulevel[] = 'graduate';
		// }

		$name = JRequest::getVar('name', array(), 'post');
		if (!is_array($name)) {
			$name = array();
		}
		if($name)
		{
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
		$this->_registration['login'] = strtolower(JRequest::getVar('login', null, 'post'));
		$this->_registration['email'] = JRequest::getVar('email', null, 'post');
		$this->_registration['confirmEmail'] = JRequest::getVar('email2', null, 'post');
		$this->_registration['web'] = JRequest::getVar('web', null, 'post');
		$this->_registration['phone'] = JRequest::getVar('phone', null, 'post');
		//$this->_registration['name'] = JRequest::getVar('name', null, 'post');
		$this->_registration['orgtype']	= JRequest::getVar('orgtype', null, 'post');
		$this->_registration['org'] = JRequest::getVar('org', null, 'post');
		$this->_registration['orgtext']	= JRequest::getVar('orgtext', null, 'post');
		if (!$this->_registration['org'])
		{
			$this->_registration['org'] = $this->_registration['orgtext'];
		}
		$this->_registration['reason'] = JRequest::getVar('reason', null, 'post');
		$this->_registration['reasontxt'] = JRequest::getVar('reasontxt', null, 'post');
		$this->_registration['password'] = JRequest::getVar('password', null, 'post');
		$this->_registration['confirmPassword'] = JRequest::getVar('password2', null, 'post');
		$this->_registration['usageAgreement'] = JRequest::getVar('usageAgreement', null, 'post');
		$this->_registration['mailPreferenceOption'] = JRequest::getVar('mailPreferenceOption', null, 'post');
		$this->_registration['sex'] = JRequest::getVar('sex', null, 'post');
		$this->_registration['interests'] = JRequest::getVar('interests',null,'post');
		$this->_registration['orcid'] = JRequest::getVar('orcid', null, 'post');

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
	 * Short description for 'loadProfile'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $xprofile Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function loadProfile($xprofile = null)
	{
		$this->clear();

		if (!is_object($xprofile)) {
			return;
		}

		//get user tags
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'tags.php' );
		$database = JFactory::getDBO();
		$mt = new MembersTags($database);
		$tag_string = $mt->get_tag_string( $xprofile->get('uidNumber') );

		//get member addresses
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'address.php');
		$membersAddress = new MembersAddress( JFactory::getDBO() );
		$addresses = $membersAddress->getAddressesForMember( $xprofile->get("uidNumber") );

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
		$this->set('web', $xprofile->get('url'));
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
		$this->set('mailPreferenceOption', $xprofile->get('mailPreferenceOption'));
		$this->set('interests', $tag_string);
		$this->set('address', $addresses);
		$this->set('orcid', $xprofile->get('orcid'));

		$this->_checked = false;
	}

	/**
	 * Short description for 'loadAccount'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $juser Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	function loadAccount($juser = null)
	{
		$this->clear();

		if (!is_object($juser)) {
			return;
		}

		$this->set('login', $juser->get('username'));
		$this->set('name', $juser->get('name'));
		$this->set('email', $juser->get('email'));

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
			die(__CLASS__ . "::" . __METHOD__ . "() Unknown key: $key \n");

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
			die(__CLASS__ . "::" . __METHOD__ . "() Unknown key: $key \n");

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

		$hconfig = JComponentHelper::getParams('com_members');

		$default    = str_pad($default, 4, '-');
		$configured  = $hconfig->get($name);
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
	 * @param string The email to search on
	 * @return int The user id or 0 if not found
	 */
	public function getEmailId($email)
	{
		// Initialize some variables
		$db =  JFactory::getDBO();

		$query = 'SELECT id FROM #__users WHERE email = ' . $db->Quote( $email );
		$db->setQuery($query, 0, 1);
		return $db->loadResult();
	}

	/**
	 * Short description for 'check'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $task Parameter description (if any) ...
	 * @param      integer $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function check($task = 'create', $id = 0, $field_to_check = array())
	{
		$jconfig = JFactory::getConfig();
		$sitename = $jconfig->getValue('config.sitename');

		$juser = JFactory::getUser();

		if ($id == 0) {
			$id = $juser->get('id');
		}

		$registration = $this->_registration;

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
		$registrationCAPTCHA = $this->registrationField('registrationCAPTCHA','HHHH',$task);
		$registrationTOU = $this->registrationField('registrationTOU','HHHH',$task);
		$registrationAddress = $this->registrationField('registrationAddress','OOOO',$task);
		$registrationORCID = $this->registrationField('registrationORCID','OOOO',$task);

		if ($task == 'update')
		{
			if (empty($registration['login'])) {
				$registrationUsername = REG_REQUIRED;
			}
			else {
				$registrationUsername = REG_READONLY;
			}

			$registrationPassword = REG_HIDE;
			$registrationConfirmPassword = REG_HIDE;

			if (empty($registration['email'])) {
				$registrationEmail = REG_REQUIRED;
			}
		}

		if ($task == 'edit')
		{
			$registrationUsername = REG_READONLY;
			$registrationPassword = REG_HIDE;
			$registrationConfirmPassword = REG_HIDE;
		}

		if ($juser->get('auth_link_id') && $task == 'create') {
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
			if (!empty($login) && !MembersHelperUtility::validlogin($login, $allowNumericFirstCharacter) )
				$this->_invalid['login'] = 'Invalid login name. Please type at least 2 characters and use only alphanumeric characters.';
		}

		if (!empty($login) && ($task == 'create' || $task == 'proxycreate' || $task == 'update'))
		{
			jimport('joomla.user.helper');

			$uid = JUserHelper::getUserId($login);

			if ($uid && $uid != $id)
				$this->_invalid['login'] = 'The user login "'. htmlentities($login) .'" already exists. Please try another.';

			if (\Hubzero\Utility\Validate::reserved('username', $login))
				$this->_invalid['login'] = 'The user login "'. htmlentities($login) .'" already exists. Please try another.';

			$puser = posix_getpwnam($login);

			if (!empty($puser) && $uid && $uid != $puser['uid'])
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
				$result = MembersHelperUtility::valid_password($registration['password']);

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

			$rules = \Hubzero\Password\Rule::getRules();
			$msg = \Hubzero\Password\Rule::validate($registration['password'],$rules,$login,$registration['name']);
			if (!empty($msg))
				$this->_invalid['password'] = $msg;
		}

		if ($registrationFullname == REG_REQUIRED)
		{
			if (empty($registration['name']))
			{
				$this->_missing['name'] = 'Full Name';
				$this->_invalid['name'] = 'Please provide a name.';
			} else {
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

					if (count($bits) >= 1) {
						$givenName = array_shift($bits);
					}
					if (count($bits) >= 1) {
						$middleName = implode(' ',$bits);
					}
				}

				if (!$givenName) {
					$this->_missing['name'] = 'Full Name';
					$this->_invalid['name'] = 'Please provide a name.';
				}
			}
		}

		if ($registrationFullname != REG_HIDE)
			if (!empty($registration['name']) && !MembersHelperUtility::validtext($registration['name']) )
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
			if (empty($email))
			{
				$this->_missing['email'] = 'Valid Email';
			}
			elseif(!MembersHelperUtility::validemail($email))
			{
				$this->_invalid['email'] = 'Invalid email address. Please correct and try again.';
			}
			else
			{
				$eid = $this->getEmailId($email);

				$usersConfig =  JComponentHelper::getParams( 'com_users' );

				$allow_duplicate_emails = $usersConfig->get( 'allow_duplicate_emails' );

				if (!$allow_duplicate_emails && ($eid && $eid != $id)) // TODO: RESOLVE MULTIPLE EMAIL ACCOUNT USAGE
				{
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

		if ($registrationURL == REG_REQUIRED)
		{
			if (empty($registration['web']))
			{
				$this->_missing['web'] = 'Personal Web Page';
				$this->_invalid['web'] = 'Please provide a valid website URL';
			}
		}

		if ($registrationURL != REG_HIDE)
		{
			if (!empty($registration['web']) && !MembersHelperUtility::validurl($registration['web']))
			{
				$this->_invalid['web'] = 'Invalid web site URL. You may be using characters that are not allowed.';
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
			if (!empty($registration['orcid']) && !MembersHelperUtility::validorcid($registration['orcid']))
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
			if (!empty($registration['phone']) && !MembersHelperUtility::validphone($registration['phone']))
				$this->_invalid['phone'] = 'Invalid phone number. You may be using characters that are not allowed.';

		if ($registrationEmployment == REG_REQUIRED)
		{
			if (empty($registration['orgtype']))
			{
				$this->_missing['orgtype'] = 'Employment Type';
				$this->_invalid['orgtype'] = 'Please make an employment type selection';
			}
		}

		/*
		if ($registrationEmployment != REG_HIDE)
			if (empty($registration['orgtype']))
			{
				//if (!MembersHelperUtility::validateOrgType($registration['orgtype']) )
					$this->_invalid['orgtype'] = 'Invalid employment status. Please make a new selection.';
			}
		*/

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
			if (!empty($registration['org']) && !MembersHelperUtility::validtext($registration['org'])) {
				$this->_invalid['org'] = 'Invalid affiliation. You may be using characters that are not allowed.';
			} elseif (!empty($registration['orgtext']) && !MembersHelperUtility::validtext($registration['orgtext'])) {
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
			if (!empty($registration['countryorigin']) && !MembersHelperUtility::validtext($registration['countryorigin']))
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
			if (!empty($registration['countryresident']) && !MembersHelperUtility::validtext($registration['countryresident']))
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
			if (!empty($registration['sex']) && !MembersHelperUtility::validtext($registration['sex']))
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
		{
			if (empty($registration['hispanic']))
			{
				$this->_invalid['hispanic'] = 'Invalid hispanic heritage selection.';
			}
		}
		*/

		if ($registrationRace == REG_REQUIRED)
		{
			if($task == 'edit')
			{
				$corigin_incoming = (in_array('countryorigin', $field_to_check)) ? true : false;
				$profile = \Hubzero\User\Profile::getInstance($juser->get('id'));
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

		/*
		if ($registrationRace != REG_HIDE)
		{
			if (!empty($registration['race']) || !MembersHelperUtility::validtext($registration['race']))
			{
				$this->_invalid['race'] = 'Invalid racial selection.';
			}
		}
		*/

		if ($registrationInterests == REG_REQUIRED)
		{
			if (empty($registration['interests']) || $registration['interests'] == '')
			{
				$this->_missing['interests'] = 'Interests';
				$this->_invalid['interests'] = 'Please select materials your are interested in';
			}
		}

		/*
		if ($registrationInterests != REG_HIDE)
		{
			if (!empty($registration['edulevel']) && !MembersHelperUtility::validtext($registration['edulevel']))
				$this->_invalid['interests'] = 'Invalid interest selection.';
			if (!empty($registration['role']) && !MembersHelperUtility::validtext($registration['role']))
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
			if (!empty($registration['reason']) && !MembersHelperUtility::validtext($registration['reason'])) {
				$this->_invalid['reason'] = 'Invalid reason text. You may be using characters that are not allowed.';
			}
			if (!empty($registration['reasontxt']) && !MembersHelperUtility::validtext($registration['reasontxt'])) {
				$this->_invalid['reason'] = 'Invalid reason text. You may be using characters that are not allowed.';
			}
		}

		if ($registrationOptIn == REG_REQUIRED)
		{
			if (is_null($registration['mailPreferenceOption']) || intval($registration['mailPreferenceOption']) < 0)
			{
				$this->_missing['mailPreferenceOption'] = 'Opt-In for mailings';
				$this->_invalid['mailPreferenceOption'] = 'Opt-In for mailings has not been selected'; //'Registration requires Opt-In of mailings.';
			}
		}

		if ($registrationCAPTCHA == REG_REQUIRED)
		{
			$botcheck = JRequest::getVar('botcheck','');
			if ($botcheck) {
				$this->_invalid['captcha'] = 'Error: Invalid CAPTCHA response.';
			}
			JPluginHelper::importPlugin( 'hubzero' );
			$dispatcher = JDispatcher::getInstance();
			$validcaptchas = $dispatcher->trigger( 'onValidateCaptcha' );
			if (count($validcaptchas) > 0) {
				foreach ($validcaptchas as $validcaptcha) {
					if (!$validcaptcha) {
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
			foreach ($field_to_check as $f)
			{
				if ($this->_missing)
				{
					foreach ($this->_missing as $k => $v)
					{
						if ($k != $f)
						{
							unset($this->_missing[$k]);
						}
					}
				}

				if ($this->_invalid)
				{
					foreach ($this->_invalid as $k => $v)
					{
						if ($k != $f)
						{
							unset($this->_invalid[$k]);
						}
					}
				}
			}
		}

		if (empty($this->_missing) && empty($this->_invalid))
			return true;

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
		if (!MembersHelperUtility::validlogin($username))
		{
			$ret['message'] = 'Invalid login name. Please type between 2 and 32 characters and use only lowercase alphanumeric characters.';
			return $ret;
		}

		// Initialize database
		$db =  JFactory::getDBO();

		$query = 'SELECT id FROM #__users WHERE username = ' . $db->Quote( $username );
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
		if (MembersHelperUtility::validlogin($login) && $logincheck['status'] == 'ok')
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
		if (MembersHelperUtility::validlogin($login) && $logincheck['status'] == 'ok')
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
			if (MembersHelperUtility::validlogin($login) && $logincheck['status'] == 'ok')
			{
				return $login;
			}
		}

		return false;
	}
}

