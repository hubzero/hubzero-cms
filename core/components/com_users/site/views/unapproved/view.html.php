<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

jimport('joomla.application.component.view');

/**
 * Unapproved view
 */
class UsersViewUnapproved extends JViewLegacy
{
	/**
	 * Method to display the view
	 *
	 * @param string the template file to include
	 */
	public function display($tpl = null)
	{
		// Get the user and then check the database to see if the session and database are out of sync
		$real = User::getInstance(User::get('id'));

		if ($real->get('approved'))
		{
			// Update the session and redirect
			$session = App::get('session');

			$sessionUser = $session->get('user');
			$sessionUser->set('approved', $real->get('approved'));
			$session->set('user', $sessionUser);

			// Redirect
			App::redirect(Request::current(true));
		}

		parent::display($tpl);
	}
}
