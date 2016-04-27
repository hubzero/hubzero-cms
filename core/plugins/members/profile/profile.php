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

/**
 * Members Plugin class for profile
 */
class plgMembersProfile extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param      object  $user   User
	 * @param      object  $member MembersProfile
	 * @return     array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		$areas = array(
			'profile' => Lang::txt('PLG_MEMBERS_PROFILE'),
			'icon'    => 'f007'
		);
		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param      object  $user   User
	 * @param      object  $member MembersProfile
	 * @param      string  $option Component name
	 * @param      string  $areas  Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		//include address library
		require_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'address.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'registration.php');

		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		// Build the final HTML
		if ($returnhtml)
		{
			$content = '';
			$this->user   = $user;
			$this->member = $member;
			$this->option = $option;
			$this->areas  = $areas;

			//get task
			$this->task = Request::getVar('action', 'view');
			switch ($this->task)
			{
				case 'addaddress':    $arr['html'] = $this->addAddress();    break;
				case 'editaddress':   $arr['html'] = $this->editAddress();   break;
				case 'saveaddress':   $arr['html'] = $this->saveAddress();   break;
				case 'deleteaddress': $arr['html'] = $this->deleteAddress(); break;
				case 'view':
				default:              $arr['html'] = $this->display();
			}
		}
		return $arr;
	}

	/**
	 * View the profile page
	 *
	 * @return     string
	 */
	private function display()
	{
		// Find out which fields are hidden, optional, or required
		$registration = new \Hubzero\Base\Object();
		$registration->Fullname     = $this->_registrationField('registrationFullname','RRRR','edit');
		$registration->Email        = $this->_registrationField('registrationEmail','RRRR','edit');
		$registration->URL          = $this->_registrationField('registrationURL','HHHH','edit');
		$registration->Phone        = $this->_registrationField('registrationPhone','HHHH','edit');
		$registration->Employment   = $this->_registrationField('registrationEmployment','HHHH','edit');
		$registration->Organization = $this->_registrationField('registrationOrganization','HHHH','edit');
		$registration->Citizenship  = $this->_registrationField('registrationCitizenship','HHHH','edit');
		$registration->Residency    = $this->_registrationField('registrationResidency','HHHH','edit');
		$registration->Sex          = $this->_registrationField('registrationSex','HHHH','edit');
		$registration->Disability   = $this->_registrationField('registrationDisability','HHHH','edit');
		$registration->Hispanic     = $this->_registrationField('registrationHispanic','HHHH','edit');
		$registration->Race         = $this->_registrationField('registrationRace','HHHH','edit');
		$registration->Interests    = $this->_registrationField('registrationInterests','HHHH','edit');
		$registration->Reason       = $this->_registrationField('registrationReason','HHHH','edit');
		$registration->OptIn        = $this->_registrationField('registrationOptIn','HHHH','edit');
		$registration->address      = $this->_registrationField('registrationAddress','OOOO','edit');
		$registration->ORCID        = $this->_registrationField('registrationORCID','OOOO','edit');

		//get member params
		$rparams = new \Hubzero\Config\Registry($this->member->get('params'));

		//get profile plugin's params
		$params = $this->params;
		$params->merge($rparams);

		$this->view = $this->view('default', 'index');

		$registration_update = null;

		if (App::get('session')->get('registration.incomplete'))
		{
			$xreg = new \Components\Members\Models\Registration();

			$xprofile = \Hubzero\User\Profile::getInstance(User::get('id'));
			if (is_object($xprofile))
			{
				$xreg->loadProfile($xprofile);
			}
			else
			{
				$xreg->loadAccount(User::getInstance());
			}

			$check = $xreg->check('update');
			if ($check)
			{
				App::get('session')->set('registration.incomplete', 0);
				App::redirect($_SERVER['REQUEST_URI']);
			}
			else
			{
				$registration_update = $xreg;
			}
		}

		//get profile completeness
		$this->view->completeness = $this->getProfileCompleteness($registration, $this->member);

		$this->view->option = 'com_members';
		$this->view->profile = $this->member;
		$this->view->registration = $registration;
		$this->view->registration_update = $registration_update;
		$this->view->params = $params;

		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		return $this->view->loadTemplate();
	}

	/**
	 * Return if a field is required, option, read only, or hidden
	 *
	 * @param      string  $name    Property name
	 * @param      string  $default Default property value
	 * @param      string  $task    Task to look up value for
	 * @return     string
	 */
	private function _registrationField($name, $default, $task = 'create')
	{
		switch ($task)
		{
			case 'register':
			case 'create': $index = 0; break;
			case 'proxy':  $index = 1; break;
			case 'update': $index = 2; break;
			case 'edit':   $index = 3; break;
			default:       $index = 0; break;
		}

		$hconfig = Component::params('com_members');
		$default = str_pad($default, 4, '-');
		$configured = $hconfig->get($name);

		if (empty($configured))
		{
			$configured = $default;
		}
		$length = strlen($configured);
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
			case 'U': return(REG_READONLY);
			case 'H':
			case '-':
			default : return(REG_HIDE);
		}
	}

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param      array  $fields  Fields filled in
	 * @param      object $profile MembersProfile
	 * @return     integer
	 */
	public function getProfileCompleteness($fields, $profile)
	{
		//default vars
		$num_fields = 0;
		$num_filled_fields = 0;
		$_property_map = array(
			'Fullname' 		=> 'name',
			'Email' 		=> 'email',
			'URL' 			=> 'web',
			'Phone' 		=> 'phone',
			'Employment' 	=> 'orgtype',
			'Organization' 	=> 'org',
			'Citizenship' 	=> 'countryorigin',
			'Residency' 	=> 'countryresident',
			'Sex' 			=> 'sex',
			'Disability' 	=> 'disability',
			'Hispanic'		=> 'hispanic',
			'Race'			=> 'race',
			'Bio'			=> 'bio',
			'Interests' 	=> 'tags',
			'OptIn' 		=> 'mailPreferenceOption',
			'ORCID'			=> 'orcid'
		);

		//unset errors from the fields object
		$fields->setErrors(array());

		//load the user profile
		$registration = new \Components\Members\Models\Registration();
		$registration->loadProfile($profile);

		//add tags to the registration object
		$database = App::get('db');
		$mt = new \Components\Members\Models\Tags($profile->get('uidNumber'));
		$registration->_registration['tags'] = $mt->render('string');

		//add bio to the registration object
		$fields->Bio = REG_OPTIONAL;
		$registration->_registration['bio'] = $profile->get("bio");

		//loop through each field to see if we want to count it
		foreach ($fields as $k => $v)
		{
			//if the field is anything button hidden we want to count it
			if (in_array($v, array(REG_REQUIRED, REG_OPTIONAL, REG_READONLY)))
			{
				//check if we have a mapping (excludes certain unused vars)
				if (isset($_property_map[$k]))
				{
					//add to the number of fields count
					$num_fields++;

					//check to see if we have it filled in
					$value = $registration->get($_property_map[$k]);
					$type = gettype($registration->get($_property_map[$k]));

					if (($type == 'array' && !empty($value)) || ($type == 'string' && $value != ''))
					{
						$num_filled_fields++;
					}
				}
			}
		}

		//return percentage
		return number_format(($num_filled_fields/$num_fields) * 100, 0);
	}

	/**
	 * Method to add a user address
	 *
	 * @return     void
	 */
	public function addAddress()
	{
		return $this->editAddress();
	}

	/**
	 * Method to edit a user address
	 *
	 * @return     void
	 */
	public function editAddress()
	{
		$this->view = $this->view('edit', 'address');

		//get request vars
		$this->view->addressId = Request::getInt('addressid', 0);

		//get member addresses
		$database = App::get('db');
		$this->view->address = new \Components\Members\Tables\Address($database);
		$this->view->address->load($this->view->addressId);

		//are we passing back the vars from save
		if (isset($this->address))
		{
			$this->view->address = $this->address;
		}

		//set vars for view
		$this->view->member = $this->member;

		//set errors and display
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		return $this->view->loadTemplate();
	}

	/**
	 * Method to save a user address
	 *
	 * @return     void
	 */
	public function saveAddress()
	{
		//get request vars
		$address = Request::getVar('address', array());

		//set up objects
		$database = App::get('db');
		$membersAddress = new \Components\Members\Tables\Address($database);

		//create object from vars
		$addressObj = new stdClass;
		$addressObj->id               = $address['id'];
		$addressObj->uidNumber        = User::get('id');
		$addressObj->addressTo        = $address['addressTo'];
		$addressObj->address1         = $address['address1'];
		$addressObj->address2         = $address['address2'];
		$addressObj->addressCity      = $address['addressCity'];
		$addressObj->addressRegion    = $address['addressRegion'];
		$addressObj->addressPostal    = $address['addressPostal'];
		$addressObj->addressCountry   = $address['addressCountry'];
		$addressObj->addressLatitude  = $address['addressLatitude'];
		$addressObj->addressLongitude = $address['addressLongitude'];

		//attempt to save
		if (!$membersAddress->save($addressObj))
		{
			$this->address = $addressObj;
			$this->setError($membersAddress->getError());
			return $this->editAddress();
		}

		//inform and redirect
		App::redirect(
			Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile'),
			Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_SAVED'),
			'passed'
		);
		return;
	}

	/**
	 * Method to delete a user address
	 *
	 * @return     void
	 */
	public function deleteAddress()
	{
		//get request vars
		$addressId = Request::getInt('addressid', 0);

		//set up objects
		$database = App::get('db');
		$membersAddress = new \Components\Members\Tables\Address($database);

		//load address object
		$membersAddress->load($addressId);

		//make sure we have a valid member address object
		if (!is_object($membersAddress) || !$membersAddress->id)
		{
			return $this->view();
		}

		//make sure user can delete this address
		if ($membersAddress->uidNumber != User::get('id'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_PROFILE_ERROR_PERMISSION_DENIED'));
			return $this->view();
		}

		//make sure we dont have another stimulation
		if (!$membersAddress->canDelete())
		{
			$this->setError($membersAddress->getError());
			return $this->view();
		}

		//attempt to delete address
		if (!$membersAddress->delete($addressId))
		{
			$this->setErrror($membersAddress->getError());
			return $this->view();
		}

		//inform and redirect
		App::redirect(
			Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile'),
			Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_REMOVED'),
			'passed'
		);
		return;
	}
}
