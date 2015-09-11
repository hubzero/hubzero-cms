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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Authy\AuthyApi;
use Hubzero\Auth\Factor;
use Hubzero\Utility\Validate;

/**
 * Factor Auth plugin for authy based identity verification
 */
class plgAuthfactorsAuthy extends \Hubzero\Plugin\Plugin
{
	/**
	 * Renders the auth factor challenge
	 *
	 * @return string
	 **/
	public function onRenderChallenge()
	{
		// Setup our response
		$response = new \Hubzero\Base\Object;

		// Route based on an action
		switch (Request::getWord('action', ''))
		{
			case 'register': $this->register(); break;
			case 'verify':   $this->verify();   break;

			default:         $this->display();  break;
		}

		$response->set('html', $this->view->loadTemplate());

		// Return the response
		return $response;
	}

	/**
	 * Displays the appropriate page for user input, based on whether
	 * or not we know the user's authy account number
	 *
	 * @return void
	 **/
	private function display()
	{
		// If we have a authy user id, go to verify page
		if (Factor::currentOrFailByDomain('authy'))
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
	 * Registers a new authy user
	 *
	 * @return void
	 **/
	private function register()
	{
		$authy = new AuthyApi($this->params->get('key'));

		// Gather and validate inputs
		$email = Request::getVar('email', null);
		$phone = Request::getVar('phone', null);
		$cc    = Request::getInt('country_code', 1);

		if (!Validate::email($email) || !Validate::phone($phone))
		{
			Notify::error("Invalid email or phone provided. Please try again");
			App::redirect(Request::current());
		}

		// Register the user
		$user = $authy->registerUser($email, $phone, $cc);

		// If everything checks out, we store the user id in the database
		if ($user->ok())
		{
			// Store factor domain id in the database
			Factor::oneOrNew(0)->set([
				'user_id'   => User::get('id'),
				'domain'    => 'authy',
				'factor_id' => $user->id(),
				'data'      => json_encode([
					'email'        => $email,
					'phone'        => $phone,
					'country_code' => $cc
				])
			])->save();
		}
		else
		{
			// Return errors
			foreach ($user->errors() as $field => $message)
			{
				Notify::error("{$field}: {$message}");
			}
		}

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
		// Get factor_id and token and verify them
		$authy        = new AuthyApi($this->params->get('key'));
		$factor_id    = Factor::currentOrFailByDomain('authy')->factor_id;
		$verification = $authy->verifyToken($factor_id, Request::getVar('token'));

		// If they pass, update the session
		if ($verification->ok())
		{
			App::get('session')->set('authfactors.status', true);
		}
		else
		{
			// Otherwise, set errors
			foreach ($verification->errors() as $field => $message)
			{
				Notify::error($message);
			}
		}

		// Refresh page to either try verification again or finish up login
		App::redirect(Request::current());
	}
}