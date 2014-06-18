<?php
/**
 * HUBzero CMS
 *
 * Copyright 2012 Purdue University. All rights reserved.
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
 * @copyright Copyright 2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.event.plugin');
jimport('Hubzero.Ldap');

/**
 * User plugin for hub users
 */
class plgUserConstantContact extends JPlugin
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
	}

	/**
	 * Method is called after user data is stored in the database
	 *
	 * @param object holds the new profile data (\Hubzero\User\Profile)
	 */
	public function onAfterStoreProfile($user)
	{
		//get the user's email and mail preference option
		$userEmailAddress 			= $user->get('email');
		$userEmailPreferenceOption 	= $user->get('mailPreferenceOption');

		//get values from plugin params
		$_ccUsername 	= $this->params->get('ccUsername','');
		$_ccPassword	= $this->params->get('ccPassword','');
		$_ccApiKey 		= $this->params->get('ccApiKey', '');
		$_ccManagePref	= $this->params->get('ccManageEmailPreference', 0);

		//make sure we want Constant Contact to manage email preferences
		if(!$_ccManagePref)
		{
			return;
		}

		//make sure we have a valid email address
		if(!$userEmailAddress || !filter_var($userEmailAddress, FILTER_VALIDATE_EMAIL))
		{
			return;
		}

		//include constant contact library
		require_once( JPATH_ROOT . DS . 'plugins' . DS . 'user' . DS . 'constantcontact' . DS . 'lib' . DS . 'ConstantContact.php' );


		//build constant contact object
		$ConstantContact = new Constantcontact("basic", $_ccApiKey, $_ccUsername, $_ccPassword);

		//if we are unable to get lists that means authentication stuff is broken
		try
		{
			$ccContactLists = $ConstantContact->getLists();
		}
		catch(CTCTException $e)
		{
			return;
		}

		//get the default list
		$defaultList = $ccContactLists['lists'][0]->id;

		//load contact by email
		$ccContact = $ConstantContact->searchContactsByEmail( $userEmailAddress );

		//create contact if one does not exist
		if(!$ccContact)
		{
			//build new contact
			$Contact = new Contact();
			$Contact->emailAddress = $userEmailAddress;
			$Contact->lists = array( $defaultList );

			//add new contact
			$ccContact = $ConstantContact->addContact( $Contact );
			$ccContact = array($ccContact);
		}

		//if we are wanting to opt in and we currently are on do-not-mail
		if($ccContact[0]->status == 'Do Not Mail' && $userEmailPreferenceOption == 2)
		{
			//load contact
			$Contact = $ConstantContact->getContactDetails( $ccContact[0] );

			//set new contact details
			$Contact->optInSource = "ACTION_BY_CONTACT";
			$Contact->lists = array( $defaultList );

			//update contact
			$ccContact = $ConstantContact->updateContact( $Contact );
		}
		else if($ccContact[0]->status == 'Active' && $userEmailPreferenceOption == 0)
		{
			//load contact
			$Contact = $ConstantContact->getContactDetails( $ccContact[0] );

			//put on do not mail list
			$ccContact = $ConstantContact->deleteContact( $Contact );
		}
	}

	/**
	 * Method is called after user data is deleted from the database
	 *
	 * @param object holds the new profile data (\Hubzero\User\Profile)
	 */
	public function onAfterDeleteProfile($user)
	{
		//get the user's email
		$userEmailAddress 	= $user->get('email');

		//make sure we have a valid email address
		if(!$userEmailAddress || !filter_var($userEmailAddress, FILTER_VALIDATE_EMAIL))
		{
			return;
		}

		//include constant contact library
		require_once( JPATH_ROOT . DS . 'plugins' . DS . 'user' . DS . 'constantcontact' . DS . 'lib' . DS . 'ConstantContact.php' );

		//get values from plugin params
		$_ccUsername 	= $this->params->get('ccUsername','');
		$_ccPassword	= $this->params->get('ccPassword','');
		$_ccApiKey 		= $this->params->get('ccApiKey', '');

		//build constant contact object
		$ConstantContact = new Constantcontact("basic", $_ccApiKey, $_ccUsername, $_ccPassword);

		//if we are unable to get lists that means authentication stuff is broken
		try
		{
			$ccContactLists = $ConstantContact->getLists();
		}
		catch(CTCTException $e)
		{
			return;
		}

		//load contact by email
		$ccContact = $ConstantContact->searchContactsByEmail( $userEmailAddress );

		//if we have contact object
		if($ccContact)
		{
			//load contact
			$Contact = $ConstantContact->getContactDetails( $ccContact[0] );

			//put on do not mail list
			$ccContact = $ConstantContact->deleteContact( $Contact );
		}
	}

}