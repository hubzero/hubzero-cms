<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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