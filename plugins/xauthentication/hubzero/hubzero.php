<?php

/**
 * Short description for 'file'
 * 
 * Long description (if any) ...
 * 
 * PHP versions 4 and 5
 * 
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * + Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * + Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 * + Neither the name of the <ORGANIZATION> nor the names of its contributors
 * may be used to endorse or promote products derived
 * from this software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * @category  CategoryName
 * @package   plgXAuthenticationHubzero
 * @author    Author's name <author@mail.com>
 * @copyright 2011 Author's name
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   CVS: $Id:$
 * @link      http://pear.php.net/package/plgXAuthenticationHubzero
 * @see       References to other sections (if any)...
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

/**
 * Authentication plugin for HUBzero
 */
class plgXAuthenticationHubzero extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param     array  $credentials Array holding the user credentials
	 * @param     array  $options     Array of extra options
	 * @param     object $response    Authentication response object
	 * @return    void
	 */
	public function onAuthenticate($credentials, $options, &$response)
	{
		ximport('Hubzero_User_Profile');

		// For JLog
		$response->type = 'hubzero';

		if (empty($credentials['password']))
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Can not have a blank password';
			return false;
		}

		$profile = Hubzero_User_Profile::getInstance($credentials['username']);

		if (empty($profile)) 
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Username not found';
			return false;
		}

		$passhash = $profile->get('userPassword');

		if (empty($passhash)) 
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Password not found for requested account';
			return false;
		}

		if (Hubzero_User_Helper::encrypt_password($credentials['password']) != $passhash)
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Incorrect username/password';
			return false;
		}

		$response->username = $profile->get('username');
		$response->email = $profile->get('email');
		$response->fullname = $profile->get('name');
		$response->password_clear = $credentials['password'];
		// Were good - So say so.
		$response->status = JAUTHENTICATE_STATUS_SUCCESS;
		$response->error_message = '';
	}
}
