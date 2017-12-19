<?php

namespace Components\Supportstats\Helpers;

use User;
use App;

class AuthHelper
{

	public static function redirectUnlessAuthenticated($controller, $task = '')
	{
		if (User::isGuest())
		{
			$redirectUrl = self::_buildRedirectUrl($controller, $task);

			App::redirect(
				$redirectUrl,
				'Please sign in.',
				'warning'
			);
		}
	}

	protected static function _buildRedirectUrl($controller, $task)
	{
		$redirectUrl = '/login';
		$forwardingUrl = self::_buildForwardingUrl($controller, $task);

		$redirectUrl .= $forwardingUrl;

		return $redirectUrl;
	}

	protected static function _buildForwardingUrl($controller, $task)
	{
		$forwardingUrl = '';

		if ($controller)
		{
			$forwardingUrl = Route::url("index.php?controller=$controller");

			if ($task)
			{
				$forwardingUrl = Route::url("index.php?controller=$controller&task=$task");
			}
		}

		$friendlyForward = '?return=' . base64_encode($forwardingUrl);

		return $friendlyForward;
	}

}
