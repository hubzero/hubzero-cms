<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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