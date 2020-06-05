<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin for client side certificate restrictions
 */
class plgSystemCertificate extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after parsing route
	 *
	 * @return void
	 */
	public function onAfterRoute()
	{
		// First, check for presence of subject dn, which is the minimum required field
		if (!isset($_SERVER['SSL_CLIENT_S_DN']) || !$_SERVER['SSL_CLIENT_S_DN'])
		{
			App::redirect($this->params->get('failure_location', '/invalidcert.php'));
			return;
		}

		if (User::isGuest())
		{
			// If so, redirect to login
			Request::setVar('option', 'com_users');
			Request::setVar('task', 'user.login');
			Request::setVar('authenticator', 'certificate');
			Request::setVar('return', base64_encode(Request::current()));
			return;
		}

		// Check if user is registered and if current session is linked to cert identity
		$hzad = Hubzero\Auth\Domain::getInstance('authentication', 'certificate', $_SERVER['SSL_CLIENT_I_DN_CN']);
		if ($link = Hubzero\Auth\Link::getInstance($hzad->id, $_SERVER['SSL_CLIENT_S_DN_CN']))
		{
			if ($link->user_id == User::get('id'))
			{
				// All clear...return nothing
				return;
			}
		}

		// Otherwise, we have a cert-based user that doesn't match the current user
		Request::setVar('option', 'com_users');
		Request::setVar('task', 'user.logout');

		$this->event->stop();
	}
}
