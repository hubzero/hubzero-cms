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

namespace Components\Members\Site\Controllers;

use Hubzero\Component\SiteController;
use Component;
use Pathway;
use Session;
use Request;
use Config;
use Route;
use Lang;
use User;
use Date;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'registration.php');

/**
 * Controller class for member registration
 */
class Register extends SiteController
{
	/**
	 * Determine task and execute it
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Make sure we're using a secure connection
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
		{
			App::redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '', 'message', true);
			die('insecure connection and redirection failed');
		}

		$this->baseURL = rtrim(Request::base(), '/');

		$this->registerTask('__default', 'create');

		parent::execute();
	}

	/**
	 * Display a form for editing profile info
	 *
	 * @return  void
	 */
	public function editTask()
	{
		if (User::isGuest())
		{
			return App::abort(500, Lang::txt('COM_MEMBERS_REGISTER_ERROR_GUEST_SESSION_EDITING'));
		}

		$xprofile = \Hubzero\User\Profile::getInstance(User::get('id'));

		// Get the return URL
		$return = base64_decode(Request::getVar('return', '',  'method', 'base64'));
		if (!$return)
		{
			$return = Session::get('session.return');

			if (!$return)
			{
				$return = '/';
			}
		}

		$username = Request::getVar('username',$xprofile->get('username'),'get');

		$target_xprofile = \Hubzero\User\Profile::getInstance($username);

		$admin = User::authorize($this->_option, 'manage');
		$self = ($xprofile->get('username') == $username);

		if (!$admin && !$self)
		{
			return App::abort(500, Lang::txt('COM_MEMBERS_REGISTER_ERROR_INVALID_SESSION_EDITING'));
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Instantiate a new registration object
		$xregistration = new \Components\Members\Models\Registration();

		if (Request::getVar('edit', '', 'post'))
		{
			// Load POSTed data
			$xregistration->loadPOST();
		}
		else
		{
			// Load data from the user object
			$xregistration->loadProfile($target_xprofile);
			return $this->_show_registration_form($xregistration, 'edit');
		}

		if ($username != $xregistration->get('login'))
		{
			return App::abort(500, Lang::txt('COM_MEMBERS_REGISTER_ERROR_REGISTRATION_DATA_MISMATCH'));
		}

		if (!$xregistration->check('edit'))
		{
			return $this->_show_registration_form($xregistration, 'edit');
		}

		$target_xprofile->loadRegistration($xregistration);

		$params = Component::params('com_members');

		$hubHomeDir = rtrim($params->get('homedir'),'/');

		$updateEmail     = false;

		if ($target_xprofile->get('homeDirectory') == '')
		{
			$target_xprofile->set('homeDirectory', $hubHomeDir . '/' . $target_xprofile->get('username'));
		}

		if ($target_xprofile->get('regIP') == '')
		{
			$target_xprofile->set('regIP', Request::getVar('REMOTE_ADDR','','server'));
		}

		if ($target_xprofile->get('regHost') == '')
		{
			if (isset($_SERVER['REMOTE_HOST']))
			{
				$target_xprofile->set('regHost', Request::getVar('REMOTE_HOST','','server'));
			}
		}

		if ($target_xprofile->get('registerDate') == '')
		{
			$target_xprofile->set('registerDate', Date::toSql());
		}

		if ($xregistration->get('email') != $target_xprofile->get('email'))
		{
			$target_xprofile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
			$updateEmail = true;
		}

		$target_xprofile->loadRegistration($xregistration);

		$target_xprofile->update();

		if ($self)
		{
			// Notify the user
			if ($updateEmail)
			{
				$subject  = Config::get('sitename') .' '.Lang::txt('COM_MEMBERS_REGISTER_EMAIL_CONFIRMATION');

				$eview = new \Hubzero\Component\View(array(
					'name'   => 'emails',
					'layout' => 'update'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->sitename   = Config::get('sitename');
				$eview->xprofile   = $target_xprofile;
				$eview->baseURL    = $this->baseURL;
				$message = $eview->loadTemplate();
				$message = str_replace("\n", "\r\n", $message);

				$msg = new \Hubzero\Mail\Message();
				$msg->setSubject($subject)
				    ->addTo($target_xprofile->get('email'))
				    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
				    ->addHeader('X-Component', $this->_option)
				    ->setBody($message);

				if (!$msg->send())
				{
					$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_EMAILING_CONFIRMATION'/*, $hubMonitorEmail*/));
					// @FIXME: LOG ERROR CONDITION SOMEWHERE
				}
			}

			// Notify administration
			$subject = Config::get('sitename') .' '.Lang::txt('COM_MEMBERS_REGISTER_EMAIL_ACCOUNT_UPDATE');

			$eaview = new \Hubzero\Component\View(array(
				'name'   => 'emails',
				'layout' => 'adminupdate'
			));
			$eaview->option     = $this->_option;
			$eaview->controller = $this->_controller;
			$eaview->sitename   = Config::get('sitename');
			$eaview->xprofile   = $target_xprofile;
			$eaview->baseURL    = $this->baseURL;
			$message = $eaview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			/*$msg = new \Hubzero\Mail\Message();
			$msg->setSubject($subject)
			    ->addTo($hubMonitorEmail)
			    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
			    ->addHeader('X-Component', $this->_option)
			    ->setBody($message)
			    ->send();*/
			// @FIXME: LOG ACCOUNT UPDATE ACTIVITY SOMEWHERE

			// Determine action based on if the user chaged their email or not
			if (!$updateEmail)
			{
				// Redirect
				Session::clear('session.return');

				App::redirect($return,'','message',true);
			}
		}
		else
		{
			if ($updateEmail)
			{
				$subject  = Config::get('sitename') .' '.Lang::txt('COM_MEMBERS_REGISTER_EMAIL_CONFIRMATION');

				$eview = new \Hubzero\Component\View(array(
					'name'   => 'emails',
					'layout' => 'updateproxy'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->sitename   = Config::get('sitename');
				$eview->xprofile   = $target_profile;
				$eview->baseURL    = $this->baseURL;
				$message = $eview->loadTemplate();
				$message = str_replace("\n", "\r\n", $message);

				$msg = new \Hubzero\Mail\Message();
				$msg->setSubject($subject)
				    ->addTo($target_xprofile->get('email'))
				    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
				    ->addHeader('X-Component', $this->_option)
				    ->setBody($message);

				if (!$msg->send())
				{
					$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_EMAILING_CONFIRMATION'/*, $hubMonitorEmail*/));
					// @FIXME: LOG ERROR CONDITION SOMEWHERE
				}
			}

			// Notify administration
			$subject = Config::get('sitename') .' '.Lang::txt('COM_MEMBERS_REGISTER_EMAIL_ACCOUNT_UPDATE');

			$eaview = new \Hubzero\Component\View(array(
				'name'   => 'emails',
				'layout' => 'adminupdateproxy'
			));
			$eaview->option     = $this->_option;
			$eaview->controller = $this->_controller;
			$eaview->sitename   = Config::get('sitename');
			$eaview->xprofile   = $target_xprofile;
			$eaview->baseURL    = $this->baseURL;
			$message = $eaview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			/*$msg = new \Hubzero\Mail\Message();
			$msg->setSubject($subject)
			    ->addTo($hubMonitorEmail)
			    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
			    ->addHeader('X-Component', $this->_option)
			    ->setBody($message)
			    ->send();*/
			// @FIXME: LOG ACCOUNT UPDATE ACTIVITY SOMEWHERE

			// Determine action based on if the user chaged their email or not
			if (!$updateEmail)
			{
				// Redirect
				Session::clear('session.return');

				App::redirect($return,'','message',true);
			}
		}

		// Instantiate a new view
		$this->view->setLayout('update');
		$this->view->title = Lang::txt('COM_MEMBERS_REGISTER_UPDATE');
		$this->view->sitename = Config::get('sitename');
		$this->view->xprofile = $target_xprofile;
		$this->view->self = $self;
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}

	/**
	 * Display a form for updating profile info
	 *
	 * @return  void
	 */
	public function updateTask()
	{
		// Check if the user is logged in
		if (User::isGuest())
		{
			return App::abort(500, Lang::txt('COM_MEMBERS_REGISTER_ERROR_SESSION_EXPIRED'));
		}

		$force = false;
		$updateEmail = false;

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Instantiate a new registration object
		$xregistration = new \Components\Members\Models\Registration();

		$xprofile = \Hubzero\User\Profile::getInstance(User::get('id'));

		$hzal = \Hubzero\Auth\Link::find_by_id(User::get('auth_link_id'));

		// Get users component config options, specifically whether or not 'simple' registration is enabled
		$method             = Request::getMethod();
		$usersConfig        = Component::params('com_users');
		$simpleRegistration = $usersConfig->get('simple_registration', false);

		if ($method == 'POST')
		{
			// Load POSTed data
			$xregistration->loadPOST();
		}
		else
		{
			// Load data from the user object
			if (is_object($xprofile))
			{
				$xregistration->loadProfile($xprofile);
			}
			else
			{
				$xregistration->loadAccount(User::getInstance());
			}

			$username = User::get('username');
			$email    = User::get('email');

			if ($username[0] == '-' && is_object($hzal))
			{
				$tmp_username = Session::get('auth_link.tmp_username', '');
				$xregistration->set('login', $tmp_username);
				$xregistration->set('orcid', Session::get('auth_link.tmp_orcid', ''));
				$xregistration->set('email', $hzal->email);
				$xregistration->set('confirmEmail', $hzal->email);

				if ($simpleRegistration)
				{
					$force  = false;
					$method = 'POST';
				}
				else
				{
					$force = true;
				}
			}
		}

		$check = $xregistration->check('update');

		if (!$force && $check && $method == 'GET')
		{
			Session::set('registration.incomplete', false);
			if ($_SERVER['REQUEST_URI'] == rtrim(Request::base(true), '/') . '/register/update'
			 || $_SERVER['REQUEST_URI'] == rtrim(Request::base(true), '/') . '/members/register/update')
			{
				App::redirect(rtrim(Request::base(true), '/') . '/');
			}
			else
			{
				App::redirect($_SERVER['REQUEST_URI']);
			}
			return(true);
		}

		if (!$force && $check && $method == 'POST')
		{
			// Before going any further, we need to do a sanity check to make sure username isn't being changed.
			// This really only happens on a race condition where someone is creating the same account
			// using a 3rd party auth service in two different browsers. Yes, it's crazy!
			if ($xregistration->get('login') && substr(User::get('username'), 0, 1) == '-')
			{
				// Make sure the username hasn't since been set in the database
				if (substr(User::getInstance(User::get('id'))->get('username'), 0, 1) != '-')
				{
					App::redirect(
						Route::url('index.php?option=com_users&view=logout'),
						Lang::txt('This account appears to already exist. Please try logging in again.'),
						'warning'
					);
					return;
				}
			}

			//$params = Component::params('com_members');
			$hubHomeDir = rtrim($this->config->get('homedir'),'/');

			$updateEmail     = false;

			if ($xprofile->get('homeDirectory') == '')
			{
				$xprofile->set('homeDirectory', $hubHomeDir . '/' . $xprofile->get('username'));
			}

			if ($xprofile->get('regIP') == '')
			{
				$xprofile->set('regIP', Request::getVar('REMOTE_ADDR','','server'));
			}

			if ($xprofile->get('regHost') == '')
			{
				if (isset($_SERVER['REMOTE_HOST']))
				{
					$xprofile->set('regHost', Request::getVar('REMOTE_HOST','','server'));
				}
			}

			if ($xprofile->get('registerDate') == '')
			{
				$xprofile->set('registerDate', Date::toSql());
			}

			if ($xregistration->get('email') != $xprofile->get('email'))
			{
				if (is_object($hzal) && $xregistration->get('email') == $hzal->email)
				{
					$xprofile->set('emailConfirmed',3);
				}
				else
				{
					$xprofile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
					$updateEmail = true;
				}
			}

			if ($xregistration->get('login') != $xprofile->get('username'))
			{
				$xprofile->set('homeDirectory', $hubHomeDir . '/' . $xregistration->get('login'));
			}

			$xprofile->loadRegistration($xregistration);
			$xprofile->update();

			// Update user table
			// TODO: only update if changed
			$myuser = User::getInstance($xprofile->get('uidNumber'));
			$myuser->set('username', $xprofile->get('username'));
			$myuser->set('email', $xprofile->get('email'));
			$myuser->set('name', $xprofile->get('name'));
			$myuser->save();

			// Update current session if appropriate
			// TODO: update all session of this user
			// TODO: only update if changed
			if ($myuser->get('id') == User::get('id'))
			{
				$suser = Session::get('user');
				$suser->set('username', $xprofile->get('username'));
				$suser->set('email', $xprofile->get('email'));
				$suser->set('name', $xprofile->get('name'));
				Session::set('user', $suser);

				// Get the session object
				$table = \JTable::getInstance('session');
				$table->load(Session::getId());
				$table->username = $xprofile->get('username');
				$table->update();
			}

			Session::set('registration.incomplete', false);

			// Notify the user
			if ($updateEmail)
			{
				$subject  = Config::get('sitename') . ' ' . Lang::txt('COM_MEMBERS_REGISTER_EMAIL_CONFIRMATION');

				$eview = new \Hubzero\Component\View(array(
					'name'   => 'emails',
					'layout' => 'update'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->sitename   = Config::get('sitename');
				$eview->xprofile   = $xprofile;
				$eview->baseURL    = $this->baseURL;
				$message = $eview->loadTemplate();
				$message = str_replace("\n", "\r\n", $message);

				$msg = new \Hubzero\Mail\Message();
				$msg->setSubject($subject)
				    ->addTo($xprofile->get('email'))
				    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
				    ->addHeader('X-Component', $this->_option)
				    ->setBody($message);

				if (!$msg->send())
				{
					$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_EMAILING_CONFIRMATION'/*,$hubMonitorEmail*/));
					// @FIXME: LOG ERROR SOMEWHERE
				}
			}

			// Notify administration
			if ($method == 'POST')
			{
				$subject = Config::get('sitename') .' '.Lang::txt('COM_MEMBERS_REGISTER_EMAIL_ACCOUNT_UPDATE');

				$eaview = new \Hubzero\Component\View(array(
					'name'   => 'emails',
					'layout' => 'adminupdate'
				));
				$eaview->option     = $this->_option;
				$eaview->controller = $this->_controller;
				$eaview->sitename   = Config::get('sitename');
				$eaview->xprofile   = $xprofile;
				$eaview->baseURL    = $this->baseURL;
				$message = $eaview->loadTemplate();
				$message = str_replace("\n", "\r\n", $message);

				/*$msg = new \Hubzero\Mail\Message();
				$msg->setSubject($subject)
				    ->addTo($hubMonitorEmail)
				    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
				    ->addHeader('X-Component', $this->_option)
				    ->setBody($message)
				    ->send();*/
				// @FIXME: LOG ACCOUNT UPDATE ACTIVITY SOMEWHERE
			}

			if (!$updateEmail)
			{
				$suri = Request::getVar('REQUEST_URI', '/', 'server');
				if ($suri == '/register/update' || $suri == '/members/update' || $suri == '/members/register/update')
				{
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&task=myaccount')
					);
				}
				else
				{
					App::redirect(
						$suri
					);
				}
				return;
			}
			else
			{
				// Instantiate a new view
				$this->view->title = Lang::txt('COM_MEMBERS_REGISTER_UPDATE');
				$this->view->sitename = Config::get('sitename');
				$this->view->xprofile = $xprofile;
				$this->view->self = true;
				$this->view->updateEmail = $updateEmail;
				if ($this->getError())
				{
					$this->view->setError($this->getError());
				}
				$this->view->display();
			}

			return true;
		}

		return $this->_show_registration_form($xregistration, 'update');
	}

	/**
	 * Short description for 'create'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	public function createTask()
	{
		if (!User::isGuest() && !User::get('tmp_user'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=myaccount'),
				Lang::txt('COM_MEMBERS_REGISTER_ERROR_NONGUEST_SESSION_CREATION'),
				'warning'
			);
			return;
		}

		if (!isset($this->_taskMap[$this->_task]))
		{
			$this->_task = 'create';
			Request::setVar('task', 'create');
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		$usersConfig = Component::params('com_users');
		if ($usersConfig->get('allowUserRegistration') == '0')
		{
			return App::abort(404, Lang::txt('JGLOBAL_RESOURCE_NOT_FOUND'));
		}

		$hzal = null;
		if (User::get('auth_link_id'))
		{
			$hzal = \Hubzero\Auth\Link::find_by_id(User::get('auth_link_id'));
		}

		// Instantiate a new registration object
		$xregistration = new \Components\Members\Models\Registration();

		if (Request::getMethod() == 'POST')
		{
			// Check for request forgeries
			Request::checkToken();

			// Load POSTed data
			$xregistration->loadPost();

			// Perform field validation
			if ($xregistration->check('create'))
			{
				// Get required system objects
				$user      = clone(User::getInstance());
				$authorize = \JFactory::getACL();

				// If user registration is not allowed, show 403 not authorized.
				if ($usersConfig->get('allowUserRegistration') == '0')
				{
					App::abort(403, Lang::txt('Access Forbidden'));
					return;
				}

				// Initialize new usertype setting
				$newUsertype = $usersConfig->get('new_usertype');
				if (!$newUsertype)
				{
					$db = App::get('db');
					$query = $db->getQuery(true)
						->select('id')
						->from('#__usergroups')
						->where('title = "Registered"');
					$db->setQuery($query);
					$newUsertype = $db->loadResult();
				}

				$user->set('username', $xregistration->get('login'));
				$user->set('name', $xregistration->get('name'));
				$user->set('email', $xregistration->get('email'));
				/*
				// Bind the post array to the user object
				if (!$user->bind(Request::get('post'), 'usertype')) {
					App::abort(500, $user->getError());
				}
				*/

				// Set some initial user values
				$user->set('id', 0);
				$user->set('groups', array($newUsertype));

				$date = Date::of('now');
				$user->set('registerDate', $date->toSql());

				// Check user activation setting
				// 0 = automatically confirmed
				// 1 = require email confirmation (the norm)
				// 2 = require admin confirmation
				$useractivation = $usersConfig->get('useractivation', 1);

				// If requiring admin approval, set user to block
				if ($useractivation == 2)
				{
					$user->set('approved', 0);
				}

				// If there was an error with registration, set the message and display form
				if ($user->save())
				{
					/*
					// Send registration confirmation mail
					$password = Request::getString('password', '', 'post', JREQUEST_ALLOWRAW);
					$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
					UserController::_sendMail($user, $password);

					// Everything went fine, set relevant message depending upon user activation state and display message
					if ($useractivation == 1)
					{
						$message  = Lang::txt('REG_COMPLETE_ACTIVATE');
					}
					else
					{
						$message = Lang::txt('REG_COMPLETE');
					}

					App::redirect(Route::url('index.php'), $message);
					*/

					// Get some settings
					$params = Component::params('com_members');
					$hubHomeDir = rtrim($params->get('homedir'), '/');

					// Attempt to get the new user
					$xprofile = \Hubzero\User\Profile::getInstance($user->get('id'));

					$result = is_object($xprofile);

					// Did we successfully create an account?
					if ($result)
					{
						$xprofile->loadRegistration($xregistration);

						$xprofile->set('public', 0);

						if (is_object($hzal))
						{
							if ($xprofile->get('email') == $hzal->email)
							{
								$xprofile->set('emailConfirmed', 3);
							}
							else
							{
								$xprofile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
							}
						}
						else if ($useractivation == 0)
						{
							$xprofile->set('emailConfirmed', 1);
							$xprofile->set('public', $this->config->get('privacy', 0));
						}

						// Do we have a return URL?
						$regReturn = Request::getVar('return', '');

						if ($regReturn)
						{
							$xprofile->setParam('return', $regReturn);
						}

						// Unset password here so that change password below can be in charge of setting it initially
						$xprofile->set('password', '');
						$result = $xprofile->update();
					}

					// add member interests
					$interests = $xregistration->get('interests');
					$mt = new \Components\Members\Models\Tags($xprofile->get('uidNumber'));
					if (!empty($interests))
					{
						$mt->setTags($interests, $xprofile->get('uidNumber'));
					}

					if ($result)
					{
						$result = \Hubzero\User\Password::changePassword($xprofile->get('uidNumber'), $xregistration->get('password'));
						// Set password back here in case anything else down the line is looking for it
						$xprofile->set('password', $xregistration->get('password'));
					}

					// Did we successfully create/update an account?
					if (!$result)
					{
						return App::abort(500, Lang::txt('COM_MEMBERS_REGISTER_ERROR_CREATING_ACCOUNT'));
					}

					if ($xprofile->get('emailConfirmed') < 0)
					{
						// Notify the user
						$subject  = Config::get('sitename').' '.Lang::txt('COM_MEMBERS_REGISTER_EMAIL_CONFIRMATION');

						$eview = new \Hubzero\Mail\View(array(
							'name'   => 'emails',
							'layout' => 'create'
						));
						$eview->option        = $this->_option;
						$eview->controller    = $this->_controller;
						$eview->sitename      = Config::get('sitename');
						$eview->xprofile      = $xprofile;
						$eview->baseURL       = $this->baseURL;
						$eview->xregistration = $xregistration;

						$msg = new \Hubzero\Mail\Message();
						$msg->setSubject($subject)
						    ->addTo($xprofile->get('email'), $xprofile->get('name'))
						    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
						    ->addHeader('X-Component', $this->_option);

						$message = $eview->loadTemplate(false);
						$message = str_replace("\n", "\r\n", $message);

						$msg->addPart($message, 'text/plain');

						$eview->setLayout('create_html');
						$message = $eview->loadTemplate();
						$message = str_replace("\n", "\r\n", $message);

						$msg->addPart($message, 'text/html');

						if (!$msg->send())
						{
							$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_EMAILING_CONFIRMATION'/*, $hubMonitorEmail*/));
							// @FIXME: LOG ERROR SOMEWHERE
						}
					}

					// Notify administration
					if ($usersConfig->get('mail_to_admin', 0))
					{
						$eview  = new \Hubzero\Mail\View(array(
							'name'   => 'emails',
							'layout' => 'admincreate_plain'
						));
						$eview->option     = $this->_option;
						$eview->controller = $this->_controller;
						$eview->sitename   = Config::get('sitename');
						$eview->xprofile   = $xprofile;
						$eview->baseUrl    = $this->baseURL;

						$plain = $eview->loadTemplate(false);
						$plain = str_replace("\n", "\r\n", $plain);

						// HTML
						$eview->setLayout('admincreate_html');

						$html = $eview->loadTemplate();
						$html = str_replace("\n", "\r\n", $html);

						$hubMonitorEmail = Config::get('mailfrom');

						$message = new \Hubzero\Mail\Message();
						$message->setSubject(Config::get('sitename') . ' ' . Lang::txt('COM_MEMBERS_REGISTER_EMAIL_ACCOUNT_CREATION'))
						        ->addTo($hubMonitorEmail)
						        ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
						        ->addHeader('X-Component', $this->_option)
						        ->addHeader('X-Component-Object', 'user_creation_admin_notification')
						        ->addPart($plain, 'text/plain')
						        ->addPart($html, 'text/html');

						// Send mail
						if (!$message->send())
						{
							\Log::error('Members admin notification email failed: ' . Lang::txt('Failed to mail %s', $hubMonitorEmail));
						}
					}

					// Instantiate a new view
					$this->view->setLayout('create');
					$this->view->title = Lang::txt('COM_MEMBERS_REGISTER_CREATE_ACCOUNT');
					$this->view->sitename = Config::get('sitename');
					$this->view->xprofile = $xprofile;

					if ($this->getError())
					{
						$this->view->setError($this->getError());
					}

					$this->view->display();

					if (is_object($hzal))
					{
						$hzal->user_id = $user->get('id');

						if ($hzal->user_id > 0)
						{
							$hzal->update();
						}
					}

					User::set('auth_link_id',null);
					User::set('tmp_user',null);
					User::set('username', $xregistration->get('login'));
					User::set('email', $xregistration->get('email'));
					User::set('id', $user->get('id'));

					return;
				}
			}
		}

		if (Request::method() == 'GET')
		{
			if (User::get('tmp_user'))
			{
				$xregistration->loadAccount(User::getInstance());

				$username = $xregistration->get('login');
				$email = $xregistration->get('email');
				if (is_object($hzal))
				{
					$xregistration->set('login', $hzal->username);
					$xregistration->set('email', $hzal->email);
					$xregistration->set('confirmEmail', $hzal->email);
				}
			}
		}

		return $this->_show_registration_form($xregistration, 'create');
	}

	/**
	 * Display race/ethnicity info
	 *
	 * @return     void
	 */
	public function raceethnicTask()
	{
		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Instantiate a new view
		$this->view->title = Lang::txt('COM_MEMBERS_REGISTER_SELECT_METHOD');
		$this->view->sitename = Config::get('sitename');
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}

	/**
	 * Short description for '_registrationField'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      unknown $default Parameter description (if any) ...
	 * @param      string $task Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _registrationField($name, $default, $task='create')
	{
		switch ($task)
		{
			case 'register':
			case 'create':      $index = 0; break;
			case 'proxy':       $index = 1; break;
			case 'proxycreate': $index = 1; break;
			case 'update':      $index = 2; break;
			case 'edit':        $index = 3; break;
			default:            $index = 0; break;
		}

		$default = str_pad($default, 4, '-');
		$configured = $this->config->get($name);
		if (empty($configured))
		{
			$configured = $default;
		}
		$length = strlen($configured);
		if ($length > $index)
		{
			$value = substr($configured, $index, 1);
		}
		else
		{
			$value = substr($default, $index, 1);
		}

		switch ($value)
		{
			case 'R': return(REG_REQUIRED);
			case 'O': return(REG_OPTIONAL);
			case 'H': return(REG_HIDE);
			case '-': return(REG_HIDE);
			case 'U': return(REG_READONLY);
			default : return(REG_HIDE);
		}
	}

	/**
	 * Display the form for registering an account
	 *
	 * @param      object &$xregistration
	 * @param      string $task
	 * @return     void
	 */
	private function _show_registration_form(&$xregistration=null, $task='create')
	{
		$this->view->setLayout('default');
		$this->view->title = Lang::txt('COM_MEMBERS_REGISTER');
		$this->view->sitename = Config::get('sitename');

		$username = Request::getVar('username', User::get('username'),'get');
		$this->view->self = (User::get('username') == $username);

		// Get the registration object
		if (!is_object($xregistration))
		{
			$this->view->xregistration = new \Components\Members\Models\Registration();
		}
		else
		{
			$this->view->xregistration = $xregistration;
		}

		// Push some values to the view

		$password_rules = \Hubzero\Password\Rule::getRules();

		$this->view->password_rules = array();

		foreach ($password_rules as $rule)
		{
			if (!empty($rule['description']))
			{
				$this->view->password_rules[] = $rule['description'];
			}
		}

		$this->view->showMissing = true;
		$this->view->registration = $this->view->xregistration->_registration;
		$this->view->registrationUsername = $this->_registrationField('registrationUsername','RROO',$task);
		$this->view->registrationPassword = $this->_registrationField('registrationPassword','RRHH',$task);
		$this->view->registrationConfirmPassword = $this->_registrationField('registrationConfirmPassword','RRHH',$task);
		$this->view->registrationFullname = $this->_registrationField('registrationFullname','RRRR',$task);
		$this->view->registrationEmail = $this->_registrationField('registrationEmail','RRRR',$task);
		$this->view->registrationConfirmEmail = $this->_registrationField('registrationConfirmEmail','RRRR',$task);
		$this->view->registrationURL = $this->_registrationField('registrationURL','HHHH',$task);
		$this->view->registrationPhone = $this->_registrationField('registrationPhone','HHHH',$task);
		$this->view->registrationEmployment = $this->_registrationField('registrationEmployment','HHHH',$task);
		$this->view->registrationOrganization = $this->_registrationField('registrationOrganization','HHHH',$task);
		$this->view->registrationCitizenship = $this->_registrationField('registrationCitizenship','HHHH',$task);
		$this->view->registrationResidency = $this->_registrationField('registrationResidency','HHHH',$task);
		$this->view->registrationSex = $this->_registrationField('registrationSex','HHHH',$task);
		$this->view->registrationDisability = $this->_registrationField('registrationDisability','HHHH',$task);
		$this->view->registrationHispanic = $this->_registrationField('registrationHispanic','HHHH',$task);
		$this->view->registrationRace = $this->_registrationField('registrationRace','HHHH',$task);
		$this->view->registrationInterests = $this->_registrationField('registrationInterests','HHHH',$task);
		$this->view->registrationReason = $this->_registrationField('registrationReason','HHHH',$task);
		$this->view->registrationOptIn = $this->_registrationField('registrationOptIn','HHHH',$task);
		$this->view->registrationCAPTCHA = $this->_registrationField('registrationCAPTCHA','HHHH',$task);
		$this->view->registrationTOU = $this->_registrationField('registrationTOU','HHHH',$task);
		$this->view->registrationORCID = $this->_registrationField('registrationORCID','OOOO',$task);

		if ($this->view->task == 'update')
		{
			if (empty($this->view->xregistration->login))
			{
				$this->view->registrationUsername = REG_REQUIRED;
			}
			else
			{
				$this->view->registrationUsername = REG_READONLY;
			}

			$this->view->registrationPassword = REG_HIDE;
			$this->view->registrationConfirmPassword = REG_HIDE;
		}

		if ($this->view->task == 'edit')
		{
			$this->view->registrationUsername = REG_READONLY;
			$this->view->registrationPassword = REG_HIDE;
			$this->view->registrationConfirmPassword = REG_HIDE;
		}

		if (User::get('auth_link_id') && $this->view->task == 'create')
		{
			$this->view->registrationPassword = REG_HIDE;
			$this->view->registrationConfirmPassword = REG_HIDE;
		}

		/*
		if ($this->view->registrationEmail == REG_REQUIRED || $this->view->registrationEmail == REG_OPTIONAL)
		{
			if (!empty($this->view->xregistration->email))
			{
				$this->view->registration['email'] = $this->view->xregistration->_encoded['email'];
			}
		}

		if ($this->view->registrationConfirmEmail == REG_REQUIRED || $this->view->registrationConfirmEmail == REG_OPTIONAL)
		{
			if (!empty($this->view->xregistration->_encoded['email']))
			{
				$this->view->registration['confirmEmail'] = $this->view->xregistration->_encoded['email'];
			}
		}
		*/

		// Display the view
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->config = $this->config;

		$this->view->display();
	}

	/**
	 * Check the strength of a password
	 *
	 * @return  string
	 */
	public function passwordstrengthTask()
	{
		// Incoming
		$no_html  = Request::getInt('no_html', 0);
		$password = Request::getVar('pass', '', 'post');
		$username = Request::getVar('user', '', 'post');

		// Instantiate a new registration object
		$xregistration = new \Components\Members\Models\Registration();

		// Score the password
		$score = $xregistration->scorePassword($password, $username);

		// Determine strength
		if ($score < PASS_SCORE_MEDIOCRE)
		{
			$cls = 'bad';
			$txt = Lang::txt('COM_MEMBERS_REGISTER_PASS_BAD');
		}
		else if ($score >= PASS_SCORE_MEDIOCRE && $score < PASS_SCORE_GOOD)
		{
			$cls = 'mediocre';
			$txt = Lang::txt('COM_MEMBERS_REGISTER_PASS_MEDIOCRE');
		}
		else if ($score >= PASS_SCORE_GOOD && $score < PASS_SCORE_STRONG)
		{
			$cls = 'good';
			$txt = Lang::txt('COM_MEMBERS_REGISTER_PASS_GOOD');
		}
		else if ($score >= PASS_SCORE_STRONG)
		{
			$cls = 'strong';
			$txt = Lang::txt('COM_MEMBERS_REGISTER_PASS_STRONG');
		}

		// Build the HTML
		$html = '<span id="passwd-meter" style="width:' . $score . '%;" class="' . $cls . '"><span>' . Lang::txt($txt) . '</span></span>';

		// Return the HTML
		if ($no_html)
		{
			echo $html;
		}

		return $html;
	}

	/**
	 * Check if a username is available
	 *
	 * @return  string
	 */
	public function checkusernameTask()
	{
		// Incoming
		$username = Request::getVar('userlogin', '', 'get');

		// Instantiate a new registration object
		$xregistration = new \Components\Members\Models\Registration();

		// Check the username
		$usernamechecked = $xregistration->checkusername($username);

		echo json_encode($usernamechecked);
		die;
	}

	/**
	 * Resend Email (account confirmation)
	 *
	 * @return     void
	 */
	public function resendTask()
	{
		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Check if the user is logged in
		if (User::isGuest())
		{
			$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false),
				Lang::txt('COM_MEMBERS_REGISTER_ERROR_LOGIN_TO_RESEND'),
				'warning'
			);
			return;
		}

		$xprofile = \Hubzero\User\Profile::getInstance(User::get('id'));
		$login = $xprofile->get('username');
		$email = $xprofile->get('email');
		$email_confirmed = $xprofile->get('emailConfirmed');

		// Incoming
		$return = urldecode(Request::getVar('return', '/'));

		if (($email_confirmed != 1) && ($email_confirmed != 3))
		{
			$confirm = \Components\Members\Helpers\Utility::genemailconfirm();

			$xprofile = new \Hubzero\User\Profile();
			$xprofile->load($login);
			$xprofile->set('emailConfirmed', $confirm);
			$xprofile->update();

			$subject  = Config::get('sitename').' '.Lang::txt('COM_MEMBERS_REGISTER_EMAIL_CONFIRMATION');

			$eview = new \Hubzero\Mail\View(array(
				'name'   => 'emails',
				'layout' => 'confirm'
			));
			$eview->option     = $this->_option;
			$eview->controller = $this->_controller;
			$eview->sitename   = Config::get('sitename');
			$eview->login      = $login;
			$eview->name       = $xprofile->get('name');
			$eview->registerDate = $xprofile->get('registerDate');
			$eview->baseURL    = $this->baseURL;
			$eview->confirm    = $confirm;

			$msg = new \Hubzero\Mail\Message();
			$msg->setSubject($subject)
			    ->addTo($email)
			    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
			    ->addHeader('X-Component', $this->_option);

			$message = $eview->loadTemplate(false);
			$message = str_replace("\n", "\r\n", $message);

			$msg->addPart($message, 'text/plain');

			$eview->setLayout('confirm_html');
			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			$msg->addPart($message, 'text/html');

			if (!$msg->send())
			{
				$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_EMAILING_CONFIRMATION', $email));
			}

			$this->view->setLayout('send');
			$this->view->title = Lang::txt('COM_MEMBERS_REGISTER_RESEND');
			$this->view->login = $login;
			$this->view->email = $email;
			$this->view->return = $return;
			$this->view->show_correction_faq = true;
			$this->view->hubName = Config::get('sitename');
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}
			$this->view->display();
		}
		else
		{
			header("Location: " . urlencode($return));
		}
	}

	/**
	 * Change registered email
	 *
	 * @return     void
	 */
	public function changeTask()
	{
		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Check if the user is logged in
		if (User::isGuest())
		{
			$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false),
				Lang::txt('COM_MEMBERS_REGISTER_ERROR_LOGIN_TO_UPDATE'),
				'warning'
			);
			return;
		}

		$xprofile = \Hubzero\User\Profile::getInstance(User::get('id'));
		$login = $xprofile->get('username');
		$email = $xprofile->get('email');
		$email_confirmed = $xprofile->get('emailConfirmed');

		// Instantiate a new view
		$this->view->title = Lang::txt('COM_MEMBERS_REGISTER_CHANGE');
		$this->view->login = $login;
		$this->view->email = $email;
		$this->view->email_confirmed = $email_confirmed;
		$this->view->success = false;

		// Incoming
		$return = urldecode(Request::getVar('return', '/'));

		$this->view->return = $return;

		// Check if a new email was submitted
		$pemail = Request::getVar('email', '', 'post');
		$update = Request::getVar('update', '', 'post');

		if ($update)
		{
			if (!$pemail)
			{
				$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_INVALID_EMAIL'));
			}
			if ($pemail && \Components\Members\Helpers\Utility::validemail($pemail) /*&& ($newemail != $email)*/)
			{
				// Check if the email address was actually changed
				if ($pemail == $email)
				{
					// Addresses are the same! Redirect
					App::redirect($return,'','message',true);
				}
				else
				{
					// New email submitted - attempt to save it
					$xprofile = \Hubzero\User\Profile::getInstance($login);
					if ($xprofile)
					{
						$dtmodify = Date::toSql();
						$xprofile->set('email',$pemail);
						$xprofile->set('modifiedDate',$dtmodify);
						if ($xprofile->update())
						{
							$user = User::getInstance($login);
							$user->set('email', $pemail);
							$user->save();
						}
						else
						{
							$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_UPDATING_ACCOUNT'));
						}
					}
					else
					{
						$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_UPDATING_ACCOUNT'));
					}

					// Any errors returned?
					if (!$this->getError())
					{
						// No errors
						// Attempt to send a new confirmation code
						$confirm = \Components\Members\Helpers\Utility::genemailconfirm();

						$xprofile = new \Hubzero\User\Profile();
						$xprofile->load($login);
						$xprofile->set('emailConfirmed', $confirm);
						$xprofile->update();

						$subject  = Config::get('sitename').' '.Lang::txt('COM_MEMBERS_REGISTER_EMAIL_CONFIRMATION');

						$eview = new \Hubzero\Mail\View(array(
							'name'   => 'emails',
							'layout' => 'confirm'
						));
						$eview->option     = $this->_option;
						$eview->controller = $this->_controller;
						$eview->sitename   = Config::get('sitename');
						$eview->login      = $login;
						$eview->name       = $xprofile->get('name');
						$eview->registerDate = $xprofile->get('registerDate');
						$eview->baseURL    = $this->baseURL;
						$eview->confirm    = $confirm;

						$msg = new \Hubzero\Mail\Message();
						$msg->setSubject($subject)
						    ->addTo($pemail)
						    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
						    ->addHeader('X-Component', $this->_option);

						$message = $eview->loadTemplate(false);
						$message = str_replace("\n", "\r\n", $message);

						$msg->addPart($message, 'text/plain');

						$eview->setLayout('confirm_html');
						$message = $eview->loadTemplate();
						$message = str_replace("\n", "\r\n", $message);

						$msg->addPart($message, 'text/html');

						if (!$msg->send())
						{
							$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_EMAILING_CONFIRMATION', $pemail));
						}

						// Show the success form
						$this->view->success = true;
					}
				}
			}
			else
			{
				$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_INVALID_EMAIL'));
			}
		}

		// Output the view
		if ($this->getError())
		{
			$this->view->email = $pemail;
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}

	/**
	 * Conform user's registration code
	 *
	 * @return     void
	 */
	public function confirmTask()
	{
		// Incoming
		$code = Request::getVar('confirm', false);
		if (!$code)
		{
			$code = Request::getVar('code', false);
		}

		// Check if the user is logged in
		if (User::isGuest())
		{
			$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task . '&confirm=' . $code, false, true));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false),
				Lang::txt('Please login in so we can confirm your account.'),
				'warning'
			);
			return;
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		$xprofile = \Hubzero\User\Profile::getInstance(User::get('id'));

		$email_confirmed = $xprofile->get('emailConfirmed');

		if (($email_confirmed == 1) || ($email_confirmed == 3))
		{
			// The current user is confirmed - check to see if the incoming code is valid at all
			if (\Components\Members\Helpers\Utility::isActiveCode($code))
			{
				$this->setError('login mismatch');

				// Build logout/login/confirm redirect flow
				$login_return  = base64_encode(Route::url('index.php?option=' . $this->option . '&controller=' . $this->_controller . '&task=' . $this->_task . '&confirm=' . $code));
				$logout_return = base64_encode(Route::url('index.php?option=com_users&view=login&return=' . $login_return));

				$redirect = Route::url('index.php?option=com_users&view=logout&return=' . $logout_return);
			}
		}
		elseif ($email_confirmed < 0 && $email_confirmed == -$code)
		{
			//var to hold return path
			$return = '';

			// get return path
			$cReturn = $this->config->get('ConfirmationReturn');
			if ($cReturn)
			{
				$return = $cReturn;
			}

			//load user profile
			$profile = new \Hubzero\User\Profile();
			$profile->load($xprofile->get('username'));

			//check to see if we have a return param
			$pReturn = base64_decode(urldecode($profile->getParam('return')));
			if ($pReturn)
			{
				$return = $pReturn;
				$profile->setParam('return','');
			}

			// make as confirmed
			$profile->set('emailConfirmed', 1);

			// set public setting
			$profile->set('public', $this->config->get('privacy', '0'));

			// upload profile
			if (!$profile->update())
			{
				$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_CONFIRMING'));
			}

			// if the user just changed their email & confirmed
			// reset 'userchangedemail' key
			if (Session::get('userchangedemail', 0) == 1)
			{
				Session::set('userchangedemail', 0);
			}

			// Redirect
			if (empty($return))
			{
				$r = $this->config->get('ConfirmationReturn');
				$return = ($r) ? $r : Route::url('index.php?option=com_members&task=myaccount');
				// consume cookie (yum) if available to return to whatever action prompted registration
				if (isset($_COOKIE['return']))
				{
					$return = $_COOKIE['return'];
					setcookie('return', '', time() - 3600);
				}
			}

			App::redirect($return,'','message',true);
		}
		else
		{
			$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_INVALID_CONFIRMATION'));
		}

		// Instantiate a new view
		$this->view->title    = Lang::txt('COM_MEMBERS_REGISTER_CONFIRM');
		$this->view->login    = $xprofile->get('username');
		$this->view->email    = $xprofile->get('email');
		$this->view->code     = $code;
		$this->view->redirect = (isset($return) ? $return : '');
		$this->view->sitename = Config::get('sitename');
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}

	/**
	 * Show a "registration unconfirmed" message
	 *
	 * @return     void
	 */
	public function unconfirmedTask()
	{
		$xprofile = \Hubzero\User\Profile::getInstance(User::get('id'));
		$email_confirmed = $xprofile->get('emailConfirmed');

		// Incoming
		$return = Request::getVar('return', urlencode('/'));

		// Check if the email has been confirmed
		if (($email_confirmed != 1) && ($email_confirmed != 3))
		{
			// Set the pathway
			$this->_buildPathway();

			// Set the page title
			$this->_buildTitle();

			// Check if the user is logged in
			if (User::isGuest())
			{
				$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true));
				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . $return, false),
					Lang::txt('COM_MEMBERS_REGISTER_ERROR_LOGIN_TO_CONFIRM'),
					'warning'
				);
				return;
			}

			// Instantiate a new view
			$this->view->title    = Lang::txt('COM_MEMBERS_REGISTER_UNCONFIRMED');
			$this->view->email    = $xprofile->get('email');
			$this->view->return   = $return;
			$this->view->sitename = Config::get('sitename');
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}
			$this->view->display();
		}
		else
		{
			header("Location: " . urldecode($return));
		}
	}

	/**
	 * Build pathway (breadcrumbs)
	 *
	 * @return     void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt('COM_MEMBERS_REGISTER'),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task)
		{
			Pathway::append(
				Lang::txt('COM_MEMBERS_REGISTER_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Set the document title
	 *
	 * @return     void
	 */
	protected function _buildTitle()
	{
		if ($this->_task)
		{
			$title = Lang::txt('COM_MEMBERS_REGISTER_' . strtoupper($this->_task));
		}
		else
		{
			$title = Lang::txt('COM_MEMBERS_REGISTER');
		}
		\Document::setTitle($title);
	}

	/**
	 * Determine if cookies are enabled
	 *
	 * @return     boolean True if cookies are enabled
	 */
	private function _cookie_check()
	{
		$jcookie = Session::getName();

		if (!isset($_COOKIE[$jcookie]))
		{
			if (Request::getVar('cookie', '', 'get') != 'no')
			{
				$uri = \JURI::getInstance();
				$uri->setVar('cookie', 'no');

				App::redirect($uri->toString());
				return;
			}

			return App::abort(500, Lang::txt('COM_MEMBERS_REGISTER_ERROR_COOKIES'));
		}
		else if (Request::getVar('cookie', '', 'get') == 'no')
		{
			$uri = \JURI::getInstance();
			$uri->delVar('cookie');

			App::redirect($uri->toString());
			return;
		}

		return true;
	}
}

