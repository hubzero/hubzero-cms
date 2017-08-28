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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Login\Admin\Controllers;

use Components\Login\Models\Login as Model;
use Hubzero\Component\AdminController;
use Hubzero\Notification\Handler;
use Hubzero\Notification\Storage\Cookie;
use Exception;
use Request;
use Plugin;
use Notify;
use App;

/**
 * Login Controller
 */
class Login extends AdminController
{
	/**
	 * Default task
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// If authenticator is specified, call the plugin display method,
		// otherwise (or if method does not exist) use default
		$authenticator = Request::getVar('authenticator', '', 'method');

		Plugin::import('authentication');

		$plugins = Plugin::byType('authentication');

		foreach ($plugins as $plugin)
		{
			$className = 'plg' . $plugin->type . $plugin->name;
			$params    = json_decode($plugin->params);

			if (class_exists($className) && isset($params->admin_login) && $params->admin_login)
			{
				$myplugin = new $className($this, (array)$plugin);

				if ($plugin->name != $authenticator)
				{
					continue;
				}

				if (method_exists($className, 'display'))
				{
					$this->view->return = Request::getVar('return', null, 'method', 'base64');

					$result = $myplugin->display($this->view, null);

					return $result;
				}
			}
		}

		// Special treatment is required for this plugin, as this view may be called
		// after a session timeout. We must reset the view and layout prior to display
		// otherwise an error will occur.
		Request::setVar('view', 'login');
		Request::setVar('tmpl', 'login');

		// See if we have any messages available by cookie
		$handler = new Handler(new Cookie(1));

		if ($handler->any())
		{
			foreach ($handler->messages() as $message)
			{
				Notify::{$message['type']}($message['message']);
			}
		}

		$this->view
			->setLayout('default')
			->display();
	}

	/**
	 * Method to log in a user.
	 *
	 * @return  void
	 */
	public function loginTask()
	{
		$model = new Model();
		$model->setState('task', $this->_task);

		$credentials = $model->getState('credentials');
		$return      = $model->getState('return');

		// If a specific authenticator is specified try to call the login method for that plugin
		if ($authenticator = Request::getVar('authenticator', false, 'method'))
		{
			Plugin::import('authentication');

			$plugins = Plugin::byType('authentication');

			foreach ($plugins as $plugin)
			{
				$className = 'plg' . $plugin->type . $plugin->name;

				if ($plugin->name != $authenticator)
				{
					continue;
				}

				if (class_exists($className))
				{
					if (method_exists($className, 'login'))
					{
						$myplugin = new $className($this, (array)$plugin);

						$myplugin->login($credentials, $options);

						if (isset($options['return']))
						{
							$return = $options['return'];
						}
					}

					$options['authenticator'] = $authenticator;

					break;
				}
			}
		}

		$options = array(
			'action'        => 'core.login.admin',
			'authenticator' => $authenticator,
			// The minimum group
			'group'         => 'Public Backend',
			// Make sure users are not autoregistered
			'autoregister'  => false,
			// Set the access control action to check.
			'action'        => 'core.login.admin'
		);

		// Set the application login entry point
		if (!array_key_exists('entry_url', $options))
		{
			$options['entry_url'] = Request::base() . 'index.php?option=com_users&task=login';
		}

		$result = App::get('auth')->login($credentials, $options);

		if (!($result instanceof Exception))
		{
			$lang = preg_replace('/[^A-Z-]/i', '', Request::getCmd('lang'));

			User::setState('application.lang', $lang);
		}
		else
		{
			Notify::error($result->getMessage());
		}

		App::redirect($return);
	}

	/**
	 * Multifactor authentication page
	 *
	 * @return  void
	 **/
	public function factorsTask()
	{
		$factors = Event::trigger('authfactors.onRenderChallenge');

		$this->view
			->set('factors', $factors)
			->display();
	}

	/**
	 * User consent form
	 *
	 * @return  void
	 **/
	public function consentTask()
	{
		$this->view->display();
	}

	/**
	 * Grant user consent
	 *
	 * @return  void
	 **/
	public function grantConsentTask()
	{
		Session::set('user_consent', true);

		App::redirect(base64_decode(Request::getVar('return')));
	}

	/**
	 * Method to log out a user.
	 *
	 * @return  void
	 */
	public function logoutTask()
	{
		$userid = Request::getInt('uid', null);

		$result = App::get('auth')->logout($userid, array(
			'clientid' => ($userid ? 0 : 1)
		));

		if (!($result instanceof Exception))
		{
			$model = new Model();
			$model->setState('task', $this->_task);

			$return = $model->getState('return');

			App::redirect($return);
		}

		$this->displayTask();
	}

	/**
	 * Unused method.
	 *
	 * @return  void
	 */
	public function attach()
	{
	}
}
