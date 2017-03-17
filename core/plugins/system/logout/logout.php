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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

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

		if (App::isSite() && Request::getString($hash, null , 'cookie'))
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
	 * @param   array  $user     Holds the user data.
	 * @param   array  $options  Array holding options (client, ...).
	 * @return  bool
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
			$renderer = new Hubzero\Error\Renderer\Page(
				App::get('document'),
				App::get('template')->template,
				App::get('config')->get('debug')
			);
			$renderer->render($error);
		}
	}
}
