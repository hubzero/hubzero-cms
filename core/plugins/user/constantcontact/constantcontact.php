<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * User plugin for hub users
 */
class plgUserConstantContact extends \Hubzero\Plugin\Plugin
{
	/**
	 * Method is called after user data is stored in the database
	 *
	 * @param   object  $user  holds the new profile data (\Hubzero\User\User)
	 * @return  void
	 */
	public function onAfterStoreProfile($user)
	{
		//get the user's email and mail preference option
		$userEmailAddress          = $user->get('email');
		$userEmailPreferenceOption = $user->get('sendEmail');

		//get values from plugin params
		$_ccUsername   = $this->params->get('ccUsername', '');
		$_ccPassword   = $this->params->get('ccPassword', '');
		$_ccApiKey     = $this->params->get('ccApiKey', '');
		$_ccManagePref = $this->params->get('ccManageEmailPreference', 0);

		//make sure we want Constant Contact to manage email preferences
		if (!$_ccManagePref)
		{
			return;
		}

		//make sure we have a valid email address
		if (!$userEmailAddress || !filter_var($userEmailAddress, FILTER_VALIDATE_EMAIL))
		{
			return;
		}

		//include constant contact library
		require_once __DIR__ . DS . 'lib' . DS . 'ConstantContact.php';

		//build constant contact object
		$ConstantContact = new Constantcontact('basic', $_ccApiKey, $_ccUsername, $_ccPassword);

		//if we are unable to get lists that means authentication stuff is broken
		try
		{
			$ccContactLists = $ConstantContact->getLists();
		}
		catch (CTCTException $e)
		{
			return;
		}

		//get the default list
		$defaultList = $ccContactLists['lists'][0]->id;

		//load contact by email
		$ccContact = $ConstantContact->searchContactsByEmail($userEmailAddress);

		//create contact if one does not exist
		if (!$ccContact)
		{
			//build new contact
			$Contact = new Contact();
			$Contact->emailAddress = $userEmailAddress;
			$Contact->lists = array($defaultList);

			//add new contact
			$ccContact = $ConstantContact->addContact($Contact);
			$ccContact = array($ccContact);
		}

		//if we are wanting to opt in and we currently are on do-not-mail
		if ($ccContact[0]->status == 'Do Not Mail' && $userEmailPreferenceOption == 2)
		{
			//load contact
			$Contact = $ConstantContact->getContactDetails($ccContact[0]);

			//set new contact details
			$Contact->optInSource = 'ACTION_BY_CONTACT';
			$Contact->lists = array($defaultList);

			//update contact
			$ccContact = $ConstantContact->updateContact($Contact);
		}
		else if ($ccContact[0]->status == 'Active' && $userEmailPreferenceOption == 0)
		{
			//load contact
			$Contact = $ConstantContact->getContactDetails($ccContact[0]);

			//put on do not mail list
			$ccContact = $ConstantContact->deleteContact($Contact);
		}
	}

	/**
	 * Method is called after user data is deleted from the database
	 *
	 * @param   object  $user  holds the new profile data (\Hubzero\User\User)
	 * @return  void
	 */
	public function onAfterDeleteProfile($user)
	{
		//get the user's email
		$userEmailAddress = $user->get('email');

		//make sure we have a valid email address
		if (!$userEmailAddress || !filter_var($userEmailAddress, FILTER_VALIDATE_EMAIL))
		{
			return;
		}

		//include constant contact library
		require_once __DIR__ . DS . 'lib' . DS . 'ConstantContact.php';

		//get values from plugin params
		$_ccUsername = $this->params->get('ccUsername', '');
		$_ccPassword = $this->params->get('ccPassword', '');
		$_ccApiKey   = $this->params->get('ccApiKey', '');

		//build constant contact object
		$ConstantContact = new Constantcontact("basic", $_ccApiKey, $_ccUsername, $_ccPassword);

		//if we are unable to get lists that means authentication stuff is broken
		try
		{
			$ccContactLists = $ConstantContact->getLists();
		}
		catch (CTCTException $e)
		{
			return;
		}

		//load contact by email
		$ccContact = $ConstantContact->searchContactsByEmail($userEmailAddress);

		//if we have contact object
		if ($ccContact)
		{
			//load contact
			$Contact = $ConstantContact->getContactDetails($ccContact[0]);

			//put on do not mail list
			$ccContact = $ConstantContact->deleteContact($Contact);
		}
	}
}
