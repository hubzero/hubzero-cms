<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Whosonline;

use Hubzero\Module\Module;
use Hubzero\Session\Helper as SessionHelper;
use Hubzero\User\User;

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
				$profile = User::oneOrNew($session->userid);
				if ($profile->get('id'))
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
