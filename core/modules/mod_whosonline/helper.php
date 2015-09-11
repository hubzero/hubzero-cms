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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Whosonline;

use Hubzero\Module\Module;
use Hubzero\Session\Helper as SessionHelper;
use Hubzero\User\Profile;

/**
 * Module class for showing users online
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if (App::isAdmin())
		{
			return $this->displayAdmin();
		}

		return $this->displaySite();
	}

	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function displaySite()
	{
		// Get all sessions
		$sessions = SessionHelper::getAllSessions(array(
			'distinct' => 1,
			'client'   => 0
		));

		// Vars to hold guests & logged in members
		$this->guestCount    = 0;
		$this->loggedInCount = 0;
		$this->loggedInList  = array();

		// Get guest and logged in counts/list
		foreach ($sessions as $session)
		{
			if ($session->guest == 1)
			{
				$this->guestCount++;
			}
			else
			{
				$this->loggedInCount++;
				$profile = Profile::getInstance($session->userid);
				if ($profile)
				{
					$this->loggedInList[] = $profile;
				}
			}
		}

		// Render view
		require $this->getLayoutPath('default');
	}

	/**
	 * Display module contents for Admin
	 *
	 * @return  void
	 */
	public function displayAdmin()
	{
		if (!\App::isAdmin())
		{
			return;
		}

		// get active sessions (users online)
		$this->rows = SessionHelper::getAllSessions(array(
			'guest'    => 0,
			'distinct' => 1
		));

		// Get the view
		require $this->getLayoutPath('default_admin');
	}
}
