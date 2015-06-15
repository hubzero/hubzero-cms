<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Plugin class for logout redirect handling.
 */
class plgSystemLogout extends \Hubzero\Plugin\Plugin
{
	/**
	 * Object Constructor.
	 *
	 * @param   object  $subject  The object to observe -- event dispatcher.
	 * @param   object  $config   The configuration object for the plugin.
	 * @return  void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();

		$hash = App::hash('plgSystemLogout');

		if (App::isSite() and Request::getString($hash, null , 'cookie'))
		{
			// Destroy the cookie
			$cookie_domain = Config::get('config.cookie_domain', '');
			$cookie_path   = Config::get('config.cookie_path', '/');
			setcookie($hash, false, time() - 86400, $cookie_path, $cookie_domain);

			// Set the error handler for E_ALL to be the class handleError method.
			set_exception_handler(array('plgSystemLogout', 'handleError'));
		}
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @param   array   $user     Holds the user data.
	 * @param   array   $options  Array holding options (client, ...).
	 * @return  object  True on success
	 */
	public function onUserLogout($user, $options = array())
	{
		if (App::isSite())
		{
			// Create the cookie
			$hash = App::hash('plgSystemLogout');

			$cookie_domain = Config::get('config.cookie_domain', '');
			$cookie_path   = Config::get('config.cookie_path', '/');

			setcookie($hash, true, time() + 86400, $cookie_path, $cookie_domain);
		}
		return true;
	}

	/**
	 * Handle an error
	 *
	 * @param   object  $error
	 * @return  void
	 */
	public static function handleError(&$error)
	{
		// Make sure the error is a 403 and we are in the frontend.
		if ($error->getCode() == 403 and App::isSite())
		{
			// Redirect to the home page
			App::redirect('index.php', Lang::txt('PLG_SYSTEM_LOGOUT_REDIRECT'), null, true, false);
		}
		else
		{
			// Render the error page.
			$renderer = new \Hubzero\Error\Renderer\Page(
				App::get('document'),
				App::get('template')->template,
				App::get('config')->get('debug')
			);
			$renderer->render($error);
		}
	}
}
