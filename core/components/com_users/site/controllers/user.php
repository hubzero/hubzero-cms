<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Registration controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class UsersControllerUser extends UsersController
{
	/**
	 * Method to log in a user.
	 *
	 * @since	1.6
	 */
	public function login()
	{
		// Populate the data array:
		$data             = array();
		$options          = array();
		$data['return']   = base64_decode(Request::getVar('return', '', 'POST', 'BASE64'));
		$data['username'] = Request::getVar('username', '', 'method', 'username');
		$data['password'] = Request::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);

		$authenticator    = Request::getVar('authenticator', '', 'method');

		// If a specific authenticator is specified try to call the login method for that plugin
		if (!empty($authenticator))
		{
			Plugin::import('authentication');

			$plugins = Plugin::byType('authentication');

			foreach ($plugins as $plugin)
			{
				$className = 'plg'.$plugin->type.$plugin->name;

				if ($plugin->name != $authenticator)
				{
					continue;
				}

				if (class_exists($className))
				{
					if (method_exists($className,'login'))
					{
						$myplugin = new $className($this,(array)$plugin);

						$myplugin->login($credentials, $options);

						if (isset($options['return']))
						{
							$data['return'] = $options['return'];
						}
					}

					$options['authenticator'] = $authenticator;
					$options['action'] = 'core.login.site';

					break;
				}
			}
		}

		// If no authenticator is specified, or the login method for that plugin did not exist then use joomla default
		if (!isset($myplugin))
		{
			// Check for request forgeries
			Session::checkToken('request');

			if ($return = Request::getVar('return', '', 'method', 'base64'))
			{
				$return = base64_decode($return);
				if (!JURI::isInternal($return))
				{
					$return = '';
				}
			}

			if ($freturn = Request::getVar('freturn', '', 'method', 'base64'))
			{
				$freturn = base64_decode($freturn);
				if (!JURI::isInternal($freturn))
				{
					$freturn = '';
				}
			}

			// Get the log in options.
			$options = array();
			$options['remember'] = Request::getBool('remember', false);
			$options['return'] = $data['return'];
			$options['action'] = 'core.login.site';
			if (!empty($authenticator))
			{
				$options['authenticator'] = $authenticator;
			}

			// Get the log in credentials.
			$credentials = array();
			$credentials['username'] = $data['username'];
			$credentials['password'] = $data['password'];
		}

		// Set the return URL if empty.
		if (empty($data['return']))
		{
			$data['return'] = 'index.php?option=com_members&task=myaccount';
		}

		// Set the return URL in the user state to allow modification by plugins
		User::setState('users.login.form.return', $data['return']);

		try
		{
			$result = App::get('auth')->login($credentials, $options);
		}
		catch (Exception $e)
		{
			$result = $e;
		}

		// Perform the log in.
		if (true === $result)
		{
			// Success
			User::setState('users.login.form.data', array());

			// If no_html is set, return json response
			if (Request::getInt('no_html', 0))
			{
				echo json_encode( array("success" => true, "redirect" => Route::url(User::getState('users.login.form.return'), false)) );
				exit;
			}
			else
			{
				App::redirect(Route::url(User::getState('users.login.form.return'), false));
			}
		}
		else
		{
			// Login failed !
			$data['remember'] = isset($options['remember']) ? (int)$options['remember'] : 0;
			User::setState('users.login.form.data', $data);

			// Facilitate third party login forms
			if (!isset($return) || !$return)
			{
				$return	= Route::url('index.php?option=com_users&view=login');
			}

			if (isset($freturn))
			{
				$return = $freturn;
			}

			$error = ($result) ? $result->getMessage() : 'An unknown error has occurred';

			// If no_html is set, return json response
			if (Request::getInt('no_html', 0))
			{
				echo json_encode( array("error" => $error, "freturn" => Route::url($return, false)) );
				exit;
			}
			else
			{
				// Redirect to a login form
				App::redirect(Route::url($return, false), $error, 'error');
			}
		}
	}

	/**
	 * Method to log out a user.
	 *
	 * @since	1.6
	 */
	public function logout()
	{
		$app = JFactory::getApplication();

		$user = User::getInstance();

		$authenticator = Request::getVar('authenticator', '', 'method');

		// If a specific authenticator is specified try to call the logout method for that plugin
		if (!empty($authenticator))
		{
			Plugin::import('authentication');

			$plugins = Plugin::byType('authentication');

			foreach ($plugins as $plugin)
			{
				$className = 'plg'.$plugin->type.$plugin->name;

				if ($plugin->name != $authenticator)
				{
					continue;
				}

				if (class_exists($className))
				{
					if (method_exists($className,'logout'))
					{
						$myplugin = new $className($this,(array)$plugin);

						$result = $myplugin->logout();
					}

					break;
				}
			}
		}

		// Perform the log in.
		$error = $app->logout();

		// Check if the log out succeeded.
		if (!($error instanceof Exception))
		{
			// If the authenticator is empty, but they have an active third party session,
			// redirect to a page indicating this and offering complete signout
			if (isset($user->auth_link_id) && $user->auth_link_id && empty($authenticator))
			{
				$auth_domain_name = '';
				$auth_domain      = \Hubzero\Auth\Link::find_by_id($user->auth_link_id);

				if (is_object($auth_domain))
				{
					$auth_domain_id   = $auth_domain->auth_domain_id;
					$auth_domain_name = \Hubzero\Auth\Domain::find_by_id($auth_domain_id)->authenticator;
				}

				// Redirect to user third party signout view
				// Only do this for PUCAS for the time being (it's the one that doesn't lose session info after hub logout)
				if ($auth_domain_name == 'pucas')
				{
					// Get plugin params
					$plugin      = Plugin::byType('authentication', $auth_domain_name);

					$pparams = new \Hubzero\Config\Registry($plugin->params);
					$auto_logoff = $pparams->get('auto_logoff', false);

					if ($auto_logoff)
					{
						App::redirect(Route::url('index.php?option=com_users&task=user.logout&authenticator=' . $auth_domain_name, false));
						return;
					}
					else
					{
						App::redirect(Route::url('index.php?option=com_users&view=endsinglesignon&authenticator=' . $auth_domain_name, false));
						return;
					}
				}
			}

			// Get the return url from the request and validate that it is internal.
			$return = Request::getVar('return', '', 'method', 'base64');
			$return = base64_decode($return);
			if (!JURI::isInternal($return))
			{
				$return = '';
			}

			// Redirect the user.
			App::redirect(Route::url($return, false));
		}
		else
		{
			App::redirect(Route::url('index.php?option=com_users&view=login', false));
		}
	}

	/**
	 * Method to register a user.
	 *
	 * @since	1.6
	 */
	public function register()
	{
		Session::checkToken('post');

		// Get the form data.
		$data   = Request::getVar('user', array(), 'post', 'array');

		// Get the model and validate the data.
		$model  = $this->getModel('Registration', 'UsersModel');
		$return = $model->validate($data);

		// Check for errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					Notify::warning($errors[$i]->getMessage());
				}
				else
				{
					Notify::warning($errors[$i]);
				}
			}

			// Save the data in the session.
			User::setState('users.registration.form.data', $data);

			// Redirect back to the registration form.
			$this->setRedirect('index.php?option=com_users&view=registration');
			return false;
		}

		// Finish the registration.
		$return	= $model->register($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			User::setState('users.registration.form.data', $data);

			// Redirect back to the registration form.
			$message = Lang::txt('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_users&view=registration', $message, 'error');
			return false;
		}

		// Flush the data from the session.
		User::setState('users.registration.form.data', null);

		exit;
	}

	/**
	 * Method to login a user.
	 *
	 * @since	1.6
	 */
	public function remind()
	{
		// Check the request token.
		Session::checkToken('post');

		$model = $this->getModel('User', 'UsersModel');
		$data  = Request::getVar('jform', array(), 'post', 'array');

		// Submit the username remind request.
		$return = $model->processRemindRequest($data);

		// Check for a hard error.
		if ($return instanceof Exception)
		{
			// Get the error message to display.
			if (Config::get('error_reporting'))
			{
				$message = $return->getMessage();
			}
			else
			{
				$message = Lang::txt('COM_USERS_REMIND_REQUEST_ERROR');
			}

			// Get the route to the next page.
			$itemid = UsersHelperRoute::getRemindRoute();
			$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
			$route  = 'index.php?option=com_users&view=remind' . $itemid;

			// Go back to the complete form.
			$this->setRedirect(Route::url($route, false), $message, 'error');
			return false;
		}
		elseif ($return === false)
		{
			// Complete failed.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getRemindRoute();
			$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
			$route  = 'index.php?option=com_users&view=remind'.$itemid;

			// Go back to the complete form.
			$message = Lang::txt('COM_USERS_REMIND_REQUEST_FAILED', $model->getError());
			$this->setRedirect(Route::url($route, false), $message, 'notice');
			return false;
		}
		else
		{
			// Complete succeeded.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getLoginRoute();
			$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
			$route  = 'index.php?option=com_users&view=login'.$itemid;

			// Proceed to the login form.
			$message = Lang::txt('COM_USERS_REMIND_REQUEST_SUCCESS');
			$this->setRedirect(Route::url($route, false), $message);
			return true;
		}
	}

	/**
	 * Method to login a user.
	 *
	 * @since	1.6
	 */
	public function resend()
	{
		// Check for request forgeries
		Session::checkToken('post');
	}

	public function link()
	{
		$user = User::getRoot();

		// First, they should already be logged in, so check for that
		if ($user->get('guest'))
		{
			App::abort( 403, Lang::txt( 'You must be logged in to perform this function' ));
			return;
		}

		// Do we have a return
		$return  = '';
		$options = array();
		if ($return = Request::getVar('return', '', 'method', 'base64'))
		{
			$return = base64_decode($return);
			if (!JURI::isInternal($return))
			{
				$return = '';
			}
			else
			{
				$options['return'] = base64_encode($return);
			}
		}

		$authenticator = Request::getVar('authenticator', '', 'method');

		// If a specific authenticator is specified try to call the link method for that plugin
		if (!empty($authenticator))
		{
			Plugin::import('authentication');

			$plugin = Plugin::byType('authentication', $authenticator);

			$className = 'plg'.$plugin->type.$plugin->name;

			if (class_exists($className))
			{
				if (method_exists($className,'link'))
				{
					$myplugin = new $className($this,(array)$plugin);
					$myplugin->link($options);
				}
				else
				{
					// No Link method is availble
					App::redirect(
						Route::url('index.php?option=com_members&id=' . $user->get('id') . '&active=account'),
						'Linked accounts are not currently available for this provider.',
						'error'
					);
				}
			}
		}
		else
		{
			// No authenticator provided...
			App::abort( 400, Lang::txt( 'Missing authenticator' ));
			return;
		}

		// Success!  Redict with message
		App::redirect(
			Route::url('index.php?option=com_members&id=' . $user->get('id') . '&active=account'),
			'Your account has been successfully linked!'
		);
	}

	/**
	 * Consents current user to site terms (ferpa requirement)
	 *
	 * @return void
	 **/
	public function consent()
	{
		Session::set('user_consent', true);
		App::redirect(base64_decode(Request::getVar('return')));
	}

	public function attach()
	{
	}
}
