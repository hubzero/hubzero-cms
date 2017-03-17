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
jimport('joomla.database.table');
/**
 * Rest model class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.5
 */
class UsersModelReset extends JModelForm
{
	/**
	 * Method to get the password reset request form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_users.reset_request', 'reset_request', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the password reset complete form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getResetCompleteForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_users.reset_complete', 'reset_complete', $options = array('control' => 'jform'));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the password reset confirm form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getResetConfirmForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_users.reset_confirm', 'reset_confirm', $options = array('control' => 'jform'));
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
		parent::preprocessForm($form, $data, $group);
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
		$params	= JFactory::getApplication()->getParams('com_users');

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * @since	1.6
	 */
	function processResetComplete($data)
	{
		// Get the form.
		$form = $this->getResetCompleteForm();

		// Check for an error.
		if ($form instanceof Exception) {
			return $form;
		}

		// Filter and validate the form data.
		$data   = $form->filter($data);
		$return = $form->validate($data);

		// Check for an error.
		if ($return instanceof Exception) {
			return $return;
		}

		// Check the validation results.
		if ($return === false) {
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message) {
				$this->setError($message);
			}
			return false;
		}

		// Get the token and user id from the confirmation process.
		$app	= JFactory::getApplication();
		$token	= $app->getUserState('com_users.reset.token', null);
		$id		= $app->getUserState('com_users.reset.user', null);

		// Check the token and user id.
		if (empty($token) || empty($id)) {
			return new Exception(Lang::txt('COM_USERS_RESET_COMPLETE_TOKENS_MISSING'), 403);
		}

		// Get the user object.
		$user = User::getInstance($id);

		// Check for a user and that the tokens match.
		if (empty($user) || $user->activation !== $token) {
			$this->setError(Lang::txt('COM_USERS_USER_NOT_FOUND'));
			return false;
		}

		// Make sure the user isn't blocked.
		if ($user->block) {
			$this->setError(Lang::txt('COM_USERS_USER_BLOCKED'));
			return false;
		}

		// Initiate profile classs
		$profile = User::getInstance($id);

		if (\Hubzero\User\Helper::isXDomainUser($user->get('id'))) {
			App::abort( 403, Lang::txt('This is a linked account. To change your password you must change it using the procedures available where the account you are linked to is managed.') );
			return;
		}

		$password_rules = \Hubzero\Password\Rule::all()
					->whereEquals('enabled', 1)
					->rows();

		$password1 = $data['password1'];
		$password2 = $data['password2'];

		if (!empty($password1)) {
			$msg = \Hubzero\Password\Rule::verify($password1,$password_rules,$profile->get('username'));
		} else {
			$msg = array();
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'utility.php');

		if (!$password1 || !$password2) {
			$this->setError( Lang::txt('you must enter your new password twice to ensure we have it correct') );
		} elseif ($password1 != $password2) {
			$this->setError( Lang::txt('the new password and confirmation you entered do not match. Please try again') );
		} elseif (!\Components\Members\Helpers\Utility::validpassword($password1)) {
			$this->setError( Lang::txt('the password you entered was invalid password. You may be using characters that are not allowed') );
		} elseif (!empty($msg)) {
			$this->setError( Lang::txt('the password does not meet site password requirements. Please choose a password meeting all the requirements listed below.') );
		}

		if ($this->getError()) {
			$this->setError( $this->getError() );
			return false;
		}

		// Encrypt the password and update the profile
		$result = \Hubzero\User\Password::changePassword($profile->get('username'), $password1);

		// Save the changes
		if (!$result) {
			$this->setError( Lang::txt('There was an error changing your password.') );
			return false;
		}

		// Flush the user data from the session.
		$app->setUserState('com_users.reset.token', null);
		$app->setUserState('com_users.reset.user', null);

		return true;
	}

	/**
	 * @since	1.6
	 */
	function processResetConfirm($data)
	{
		// Get the form.
		$form = $this->getResetConfirmForm();

		// Check for an error.
		if ($form instanceof Exception) {
			return $form;
		}

		// Filter and validate the form data.
		$data	= $form->filter($data);
		$return	= $form->validate($data);

		// Check for an error.
		if ($return instanceof Exception) {
			return $return;
		}

		// Check the validation results.
		if ($return === false) {
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message) {
				$this->setError($message);
			}
			return false;
		}

		// Get the token and user id from the confirmation process.
		$app = JFactory::getApplication();
		$id  = $app->getUserState('com_users.reset.user', null);

		// Get the user object.
		$user = User::getInstance($id);

		$parts = explode( ':', $user->activation );
		$crypt = $parts[0];
		if (!isset($parts[1])) {
			$this->setError(Lang::txt('COM_USERS_USER_NOT_FOUND'));
			return false;
		}
		$salt = $parts[1];
		$testcrypt = JUserHelper::getCryptedPassword($data['token'], $salt);

		// Verify the token
		if (!($crypt == $testcrypt))
		{
			$this->setError(Lang::txt('COM_USERS_USER_NOT_FOUND'));
			return false;
		}

		// Make sure the user isn't blocked.
		if ($user->block) {
			$this->setError(Lang::txt('COM_USERS_USER_BLOCKED'));
			return false;
		}

		// Push the user data into the session.
		$app = JFactory::getApplication();
		$app->setUserState('com_users.reset.token', $crypt.':'.$salt);

		return true;
	}

	/**
	 * Method to start the password reset process.
	 *
	 * @since	1.6
	 */
	public function processResetRequest($data)
	{
		// Get the form.
		$form = $this->getForm();

		// Check for an error.
		if ($form instanceof Exception) {
			return $form;
		}

		// Filter and validate the form data.
		$data   = $form->filter($data);
		$return = $form->validate($data);

		// Check for an error.
		if ($return instanceof Exception) {
			return $return;
		}

		// Check the validation results.
		if ($return === false) {
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message) {
				$this->setError($message);
			}
			return false;
		}

		// Find the user id for the given username
		$db	= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('id');
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('username').' = '.$db->Quote($data['username']));

		// Get the user object.
		$db->setQuery((string) $query);
		$userId = $db->loadResult();

		// Check for an error.
		if ($db->getErrorNum()) {
			$this->setError(Lang::txt('COM_USERS_DATABASE_ERROR', $db->getErrorMsg()), 500);
			return false;
		}

		// Check for a user.
		if (empty($userId)) {
			$this->setError(Lang::txt('COM_USERS_INVALID_USERNAME'));
			return false;
		}

		// Get the user object.
		$user = JUser::getInstance($userId);

		// Make sure the user isn't blocked.
		if ($user->block) {
			$this->setError(Lang::txt('COM_USERS_USER_BLOCKED'));
			return false;
		}

		// Make sure the user isn't a Super Admin.
		if ($user->authorise('core.admin')) {
			$this->setError(Lang::txt('COM_USERS_REMIND_SUPERADMIN_ERROR'));
			return false;
		}

		// Make sure the user has not exceeded the reset limit
		if (!$this->checkResetLimit($user)) {
			$resetLimit = (int) JFactory::getApplication()->getParams()->get('reset_time');
			$this->setError(Lang::txts('COM_USERS_REMIND_LIMIT_ERROR_N_HOURS', $resetLimit));
			return false;
		}
		// Set the confirmation token.
		$token = App::hash(JUserHelper::genRandomPassword());
		$salt = JUserHelper::getSalt('crypt-md5');
		$hashedToken = md5($token.$salt).':'.$salt;

		$user->activation = $hashedToken;

		// Save the user to the database.
		if (!$user->save(true)) {
			return new Exception(Lang::txt('COM_USERS_USER_SAVE_FAILED', $user->getError()), 500);
		}

		// Assemble the password reset confirmation link.
		$mode = Config::get('force_ssl', 0) == 2 ? 1 : -1;
		$itemid = UsersHelperRoute::getLoginRoute();
		$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
		$link = 'index.php?option=com_users&view=reset&layout=confirm'.$itemid;

		// Put together the email template data.
		$data = $user->getProperties();
		$data['fromname']  = Config::get('fromname');
		$data['mailfrom']  = Config::get('mailfrom');
		$data['sitename']  = Config::get('sitename');
		$data['link_text'] = Route::url($link, false, $mode);
		$data['link_html'] = Route::url($link, true, $mode);
		$data['token']     = $token;

		$subject = Lang::txt(
			'COM_USERS_EMAIL_PASSWORD_RESET_SUBJECT',
			$data['sitename']
		);

		$body = Lang::txt(
			'COM_USERS_EMAIL_PASSWORD_RESET_BODY',
			$data['sitename'],
			$data['token'],
			$data['link_text']
		);

		// Send the password reset request email.
		$mailer = new Hubzero\Mail\Message();

		$return = $mailer
			->addFrom($data['mailfrom'], $data['fromname'])
			->addTo($user->email)
			->setSubject($subject)
			->setBody($body)
			->send();

		// Check for an error.
		if ($return !== true)
		{
			return new Exception(Lang::txt('COM_USERS_MAIL_FAILED'), 500);
		}

		// Push the user data into the session.
		$app = JFactory::getApplication();
		$app->setUserState('com_users.reset.user', $user->id);

		return true;
	}
	/**
	 * Method to check if user reset limit has been exceeded within the allowed time period.
	 *
	 * @param   JUser  the user doing the password reset
	 *
	 * @return  boolean true if user can do the reset, false if limit exceeded
	 *
	 * @since	2.5
	 */
	public function checkResetLimit($user)
	{
		$params = JFactory::getApplication()->getParams();
		$maxCount = (int) $params->get('reset_count');
		$resetHours = (int) $params->get('reset_time');
		$result = true;

		$lastResetTime = strtotime($user->lastResetTime) ? strtotime($user->lastResetTime) : 0;
		$hoursSinceLastReset = (strtotime(Date::toSql()) - $lastResetTime) / 3600;

		// If it's been long enough, start a new reset count
		if ($hoursSinceLastReset > $resetHours)
		{
			$user->lastResetTime = Date::toSql();
			$user->resetCount = 1;
		}

		// If we are under the max count, just increment the counter
		elseif ($user->resetCount < $maxCount)
		{
			$user->resetCount;
		}

		// At this point, we know we have exceeded the maximum resets for the time period
		else
		{
			$result = false;
		}
		return $result;
	}
}
