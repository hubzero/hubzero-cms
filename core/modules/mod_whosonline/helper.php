<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
