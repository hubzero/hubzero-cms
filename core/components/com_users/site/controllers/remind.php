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
 * Reset controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @version		1.6
 */
class UsersControllerRemind extends UsersController
{
	/**
	 * Method to request a username reminder.
	 *
	 * @since	1.6
	 */
	public function remind()
	{
		// Check the request token.
		Session::checkToken('post') or exit(Lang::txt('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$model = $this->getModel('Remind', 'UsersModel');
		$data  = Request::getVar('jform', array(), 'post', 'array');

		// Submit the password reset request.
		$return	= $model->processRemindRequest($data);

		// Check for a hard error.
		if ($return == false)
		{
			// The request failed.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getRemindRoute();
			$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
			$route	= 'index.php?option=com_users&view=remind'.$itemid;

			// Go back to the request form.
			$message = Lang::txt('COM_USERS_REMIND_REQUEST_FAILED', $model->getError());
			$this->setRedirect(Route::url($route, false), $message, 'warning');
			return false;
		}
		else
		{
			// The request succeeded.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getRemindRoute();
			$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
			$route  = 'index.php?option=com_users&view=login'.$itemid;

			// Proceed to step two.
			$message = Lang::txt('COM_USERS_REMIND_REQUEST_SUCCESS');
			$this->setRedirect(Route::url($route, false), $message);
			return true;
		}
	}
}
