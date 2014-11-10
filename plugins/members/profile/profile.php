<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @return     array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		$areas = array(
			'profile' => JText::_('PLG_MEMBERS_PROFILE'),
			'icon'    => 'f007'
		);
		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param      object  $user   JUser
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
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'address.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'registration.php');

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
			$this->task = JRequest::getVar('action', 'view');
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
		$app = JFactory::getApplication();

		// Find out which fields are hidden, optional, or required
		$registration = new JObject();
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
		$rparams = new JRegistry($this->member->get('params'));

		//get profile plugin's params
		//$plugin = JPluginHelper::getPlugin("members", "profile");
		$params = $this->params; //new JRegistry($plugin->params);
		$params->merge($rparams);

		$this->view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'index'
			)
		);

		$registration_update = null;

		$session = JFactory::getSession();
		if ($session->get('registration.incomplete'))
		{
			$xreg = new MembersModelRegistration();
			$juser =  JFactory::getUser();
			$xprofile = \Hubzero\User\Profile::getInstance($juser->get('id'));

			if (is_object($xprofile))
			{
				$xreg->loadProfile($xprofile);
			}
			else
			{
				$xreg->loadAccount($juser);
			}

			$check = $xreg->check('update');
			if ($check)
			{
				$session->set('registration.incomplete', 0);
				$app->redirect($_SERVER['REQUEST_URI']);
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

		$hconfig = JComponentHelper::getParams('com_members');
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
		unset($fields->_errors);

		//load the user profile
		$registration = new MembersModelRegistration();
		$registration->loadProfile($profile);

		//add tags to the registration object
		$database = JFactory::getDBO();
		$mt = new MembersModelTags($profile->get('uidNumber'));
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
		$this->view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'profile',
				'name'    => 'address',
				'layout'  => 'edit'
			)
		);

		//get request vars
		$this->view->addressId = JRequest::getInt('addressid', 0);

		//get member addresses
		$this->view->address = new MembersAddress(JFactory::getDBO());
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
		$address = JRequest::getVar('address', array());

		//set up objects
		$database       = JFactory::getDBO();
		$juser          = JFactory::getUser();
		$membersAddress = new MembersAddress($database);

		//create object from vars
		$addressObj = new stdClass;
		$addressObj->id               = $address['id'];
		$addressObj->uidNumber        = $juser->get('id');
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
		$this->redirect(
			JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=profile'),
			JText::_('PLG_MEMBERS_PROFILE_ADDRESS_SAVED'),
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
		$addressId = JRequest::getInt('addressid', 0);

		//set up objects
		$database       = JFactory::getDBO();
		$juser          = JFactory::getUser();
		$membersAddress = new MembersAddress($database);

		//load address object
		$membersAddress->load($addressId);

		//make sure we have a valid member address object
		if (!is_object($membersAddress) || !$membersAddress->id)
		{
			return $this->view();
		}

		//make sure user can delete this address
		if ($membersAddress->uidNumber != $juser->get('id'))
		{
			$this->setError(JText::_('PLG_MEMBERS_PROFILE_ERROR_PERMISSION_DENIED'));
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
		$this->redirect(
			JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=profile'),
			JText::_('PLG_MEMBERS_PROFILE_ADDRESS_REMOVED'),
			'passed'
		);
		return;
	}
}
