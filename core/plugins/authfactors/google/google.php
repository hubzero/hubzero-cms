<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Auth\Factor;
use Hubzero\Utility\Validate;
require_once Plugin::path('authfactors', 'google') . DS . 'helpers' . DS . 'GoogleAuthenticator.php';

/**
 * Factor Auth plugin for based identity verification
 */
class plgAuthfactorsGoogle extends \Hubzero\Plugin\Plugin
{
	/**
	 * Renders the auth factor challenge
	 *
	 * @return string
	 **/
	public function onRenderChallenge()
	{
		// Setup our response
		$response = new \Hubzero\Base\Obj;
		// Route based on an action
		switch (Request::getWord('action', ''))
		{
			case 'registered':
				$this->register();
				break;
			case 'verify':
				$this->verify();
				break;

			default:
				$this->display();
				break;
		}

		$response->set('html', $this->view->loadTemplate());

		// Return the response
		return $response;
	}

	/**
	 * Displays the appropriate page for user input
	 *
	 * @return void
	 **/
	private function display()
	{
		// If we have a user id, go to verify page
		if (Factor::currentOrFailByEnrolled())
		{
			$this->view = $this->view('verify', 'challenge');
		}
		else
		{
			// Otherwise, go to the enroll page
			$this->view = $this->view('enroll', 'challenge');
		}
	}

	/**
	 * Registers a new  user
	 *
	 * @return void
	 **/
	private function register()
	{
		Factor::registerUserAsEnrolled();
		// Redirect for verification process to occur
		App::redirect(Request::current());
	}

	/**
	 * Verifies the incoming token against the current user
	 *
	 * @return void
	 **/
	private function verify()
	{
		// Get secret and entered token and verify them
		$ga = new \Google\Authenticator\GoogleAuthenticator();

		$data = json_decode(Factor::currentOrFailByDomain('google')->data);
		$entered_code = Request::getString('token');
		$correct_code = $ga->getCode($data->secret);
		$verification = $ga->checkCode($data->secret, $entered_code);

		// If they pass, update the session
		if ($verification)
		{
			App::get('session')->set('authfactors.status', true);
		}
		else
		{
			// Otherwise, set errors
			Notify::error($verification);
		}

		// Refresh page to either try verification again or finish up login
		App::redirect(Request::current());
	}
}
