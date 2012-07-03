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

jimport('joomla.plugin.plugin');

/**
 * Members Plugin class for profile
 */
class plgMembersProfile extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Event call to determine if this plugin should return data
	 * 
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @return     array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		$areas = array();
		
		$areas['profile'] = JText::_('PLG_MEMBERS_PROFILE');
				
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
			$arr['html'] = $this->view();
		}
		return $arr;
	} 

	/**
	 * View the profile page
	 * 
	 * @return     string
	 */
	private function view()
	{
		$app = JFactory::getApplication();
		
		// Load some needed libraries
		ximport('Hubzero_Registration');

		// Find out which fields are hidden, optional, or required
		$registration = new JObject();
		$registration->Fullname = $this->_registrationField('registrationFullname','RRRR','edit');
		$registration->Email = $this->_registrationField('registrationEmail','RRRR','edit');
		$registration->URL = $this->_registrationField('registrationURL','HHHH','edit');
		$registration->Phone = $this->_registrationField('registrationPhone','HHHH','edit');
		$registration->Employment = $this->_registrationField('registrationEmployment','HHHH','edit');
		$registration->Organization = $this->_registrationField('registrationOrganization','HHHH','edit');
		$registration->Citizenship = $this->_registrationField('registrationCitizenship','HHHH','edit');
		$registration->Residency = $this->_registrationField('registrationResidency','HHHH','edit');
		$registration->Sex = $this->_registrationField('registrationSex','HHHH','edit');
		$registration->Disability = $this->_registrationField('registrationDisability','HHHH','edit');
		$registration->Hispanic = $this->_registrationField('registrationHispanic','HHHH','edit');
		$registration->Race = $this->_registrationField('registrationRace','HHHH','edit');
		$registration->Interests = $this->_registrationField('registrationInterests','HHHH','edit');
		$registration->Reason = $this->_registrationField('registrationReason','HHHH','edit');
		$registration->OptIn = $this->_registrationField('registrationOptIn','HHHH','edit');

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}
		
		//get member params
		$rparams = new $paramsClass($this->member->get('params'));
		
		//get profile plugin's params
		$plugin = JPluginHelper::getPlugin("members", "profile");
		$params = new $paramsClass( $plugin->params );
		$params->merge($rparams);
        
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('members', 'profile');
		Hubzero_Document::addPluginScript('members', 'profile');
		
		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			$document =& JFactory::getDocument();
			$document->addScript("/media/system/js/jquery.fileuploader.js");
		}

		ximport('Hubzero_Plugin_View');
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'profile',
				'name'    => 'index'
			)
		);

		$registration_update = null;

		$session =& JFactory::getSession();
		if ($session->get('registration.incomplete'))
		{
			$xreg = new Hubzero_Registration();
			$jsuer = & JFactory::getUser(); 
			$xprofile =& Hubzero_Factory::getProfile();

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

		$this->view->option = "com_members";
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

		$hconfig =& JComponentHelper::getParams('com_register');
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
			case 'H': return(REG_HIDE);
			case '-': return(REG_HIDE);
			case 'U': return(REG_READONLY);
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
			'OptIn' 		=> 'mailPreferenceOption'
		);

		//unset errors from the fields object
		unset($fields->_errors);

		//load the user profile
		$registration = new Hubzero_Registration();
		$registration->loadProfile($profile);

		//add tags to the registration object
		$database =& JFactory::getDBO();
		$mt = new MembersTags($database);
		$registration->_registration['tags'] = $mt->get_tag_string($profile->get('uidNumber'));

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
}
