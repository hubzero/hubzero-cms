<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Remind model class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @version		1.5
 */
class UsersModelRemind extends JModelForm
{
	/**
	 * Method to get the username remind request form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_users.remind', 'remind', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		parent::preprocessForm($form, $data, 'user');
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		// Get the application object.
		$app = JFactory::getApplication();
		$params = $app->getParams('com_users');

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * @since	1.6
	 */
	public function processRemindRequest($data)
	{
		// Get the form.
		$form = $this->getForm();

		// Check for an error.
		if (empty($form)) {
			return false;
		}

		// Validate the data.
		$data = $this->validate($form, $data);

		// Check for an error.
		if ($data instanceof Exception) {
			return $return;
		}

		// Check the validation results.
		if ($data === false) {
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message) {
				$this->setError($message);
			}
			return false;
		}

		// Find the user id for the given email address.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('email').' = '.$db->Quote($data['email']));

		// Get the user id.
		$db->setQuery((string) $query);
		$users = $db->loadObjectList('id');
		$usersnames = array();

		// Check for an error.
		if ($db->getErrorNum()) {
			$this->setError(Lang::txt('COM_USERS_DATABASE_ERROR', $db->getErrorMsg()), 500);
			return false;
		}

		// Check for a user.
		if (count($users) < 1) {
			$this->setError(Lang::txt('COM_USERS_USER_NOT_FOUND'));
			return false;
		}

		foreach ($users as $user) {
			// Make sure the user isn't blocked.
			if ($user->block) {
				unset($users[$user->id]);
			} else {
				$usersnames[] = $user->username;
				$email = $user->email;
			}
		}

		// Check for a user.
		if (count($users) < 1) {
			$this->setError(Lang::txt('COM_USERS_USER_NOT_FOUND'));
			return false;
		}

		// Assemble the login link.
		$itemid = UsersHelperRoute::getLoginRoute();
		$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
		$link = 'index.php?option=com_users&view=login'.$itemid;
		$mode = Config::get('force_ssl', 0) == 2 ? 1 : -1;

		// Put together the email template data.
		$data = array();
		$data['username']	= implode(', ', $usersnames);
		$data['fromname']	= Config::get('fromname');
		$data['mailfrom']	= Config::get('mailfrom');
		$data['sitename']	= Config::get('sitename');
		$data['link_text']	= Route::url($link, false, $mode);
		$data['link_html']	= Route::url($link, true, $mode);

		$subject = Lang::txt(
			'COM_USERS_EMAIL_USERNAME_REMINDER_SUBJECT',
			$data['sitename']
		);
		$body = Lang::txt(
			'COM_USERS_EMAIL_USERNAME_REMINDER_BODY',
			$data['sitename'],
			$data['username'],
			$data['link_text']
		);

		// Send the password reset request email.
		$mailer = new Hubzero\Mail\Message();

		$return = $mailer
			->addFrom($data['mailfrom'], $data['fromname'])
			->addTo($email)
			->setSubject($subject)
			->setBody($body)
			->send();

		// Check for an error.
		if ($return !== true) {
			$this->setError(Lang::txt('COM_USERS_MAIL_FAILED'), 500);
			return false;
		}

		return true;
	}
}
