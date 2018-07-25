<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Registration controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class UsersControllerRegistration extends UsersController
{
	/**
	 * Method to activate a user.
	 *
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function activate()
	{
		$user    = JFactory::getUser();
		$uParams = Component::params('com_users');

		// If the user is logged in, return them back to the homepage.
		if ($user->get('id')) {
			$this->setRedirect('index.php');
			return true;
		}

		// If user registration or account activation is disabled, throw a 403.
		if ($uParams->get('useractivation') == 0 || $uParams->get('allowUserRegistration') == 0) {
			App::abort(403, Lang::txt('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
			return false;
		}

		$model = $this->getModel('Registration', 'UsersModel');
		$token = Request::getString('token', null);

		// Check that the token is in a valid format.
		if ($token === null || strlen($token) !== 32) {
			App::abort(403, Lang::txt('JINVALID_TOKEN'));
			return false;
		}

		// Attempt to activate the user.
		$return = $model->activate($token);

		// Check for errors.
		if ($return === false) {
			// Redirect back to the homepage.
			$this->setMessage(Lang::txt('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect('index.php');
			return false;
		}

		$useractivation = $uParams->get('useractivation');

		// Redirect to the login screen.
		if ($useractivation == 0)
		{
			$this->setMessage(Lang::txt('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
			$this->setRedirect(Route::url('index.php?option=com_users&view=login', false));
		}
		elseif ($useractivation == 1)
		{
			$this->setMessage(Lang::txt('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS'));
			$this->setRedirect(Route::url('index.php?option=com_users&view=login', false));
		}
		elseif ($return->getParam('activate'))
		{
			$this->setMessage(Lang::txt('COM_USERS_REGISTRATION_VERIFY_SUCCESS'));
			$this->setRedirect(Route::url('index.php?option=com_users&view=registration&layout=complete', false));
		}
		else
		{
			$this->setMessage(Lang::txt('COM_USERS_REGISTRATION_ADMINACTIVATE_SUCCESS'));
			$this->setRedirect(Route::url('index.php?option=com_users&view=registration&layout=complete', false));
		}
		return true;
	}

	/**
	 * Method to register a user.
	 *
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function register()
	{
		App::abort(403, Lang::txt('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
		return false;

		// Check for request forgeries.
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// If registration is disabled - Redirect to login page.
		if (Component::params('com_users')->get('allowUserRegistration') == 0) {
			$this->setRedirect(Route::url('index.php?option=com_users&view=login', false));
			return false;
		}

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('Registration', 'UsersModel');

		// Get the user data.
		$requestData = Request::getArray('jform', array(), 'post');

		// Validate the posted data.
		$form	= $model->getForm();
		if (!$form) {
			App::abort(500, $model->getError());
			return false;
		}
		$data	= $model->validate($form, $requestData);

		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_users.registration.data', $requestData);

			// Redirect back to the registration screen.
			$this->setRedirect(Route::url('index.php?option=com_users&view=registration', false));
			return false;
		}

		// Attempt to save the data.
		$return	= $model->register($data);

		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_users.registration.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage($model->getError(), 'warning');
			$this->setRedirect(Route::url('index.php?option=com_users&view=registration', false));
			return false;
		}

		// Flush the data from the session.
		$app->setUserState('com_users.registration.data', null);

		// Redirect to the profile screen.
		if ($return === 'adminactivate'){
			$this->setMessage(Lang::txt('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
			$this->setRedirect(Route::url('index.php?option=com_users&view=registration&layout=complete', false));
		} elseif ($return === 'useractivate') {
			$this->setMessage(Lang::txt('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
			$this->setRedirect(Route::url('index.php?option=com_users&view=registration&layout=complete', false));
		} else {
			$this->setMessage(Lang::txt('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
			$this->setRedirect(Route::url('index.php?option=com_users&view=login', false));
		}

		return true;
	}
}
