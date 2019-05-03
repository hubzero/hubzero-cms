<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Users\Api\Controllers;

use Hubzero\Component\ApiController;

class CurrentUserv1_0 extends ApiController
{

	/**
	 * Indicates whether current user is authenticated
	 *
	 * @apiMethod GET
	 * @apiUri    /api/v1.0/users/current_user/isAuthenticated
	 * @return    bool
	 */
	function isAuthenticatedTask()
	{
		$isAuthenticated = !User::isGuest();

		$result = [
			'isAuthenticated' => $isAuthenticated
		];

		$this->send($result);
	}

}
