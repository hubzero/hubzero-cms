<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2017 HUBzero Foundation, LLC.
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
 * @author    Jerry Kuang <kuang5@purdue.edu>
 * @copyright Copyright 2005-2017 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Members\Models\Member;
use Request;

/**
 * Members controller class for ORCIDs landing page
 */
class Redirect extends SiteController
{	
	/**
	* user's name
	*
	*/
    protected $_userName;
	
	/**
	* user's ORCID ID
	*
	*/
    protected $_userOrcidID;
	
	/**
	 * OAuth tokens
	 *
	 * @var  array
	 */
	protected $_oauthToken = array(
		'members' => 'https://orcid.org/oauth/token',
		'sandbox' => 'https://sandbox.orcid.org/oauth/token'
	);
	
	/**
	 * Capture and exchange the 6-digit authorization code, then ORCID ID will be returned.
	 * When deny button is hit, ORCID posts error code back but we do nothing here.
	 *
	 * @param   null
	 * @return  void
	 */
	public function excAuth()
	{
		// Get client ID and client secret
		$srv = $this->config->get('orcid_service', 'members');
		$clientID = $this->config->get('orcid_' . $srv . '_client_id');
		$clientSecret = $this->config->get('orcid_' . $srv . '_token');
		$oauthToken = $this->_oauthToken[$srv];
		
		if (Request::getVar('code'))
		{
			//Build request parameter string
			$params = "client_id=" . $clientID . "&client_secret=" . $clientSecret. "&grant_type=authorization_code&code=" . Request::getVar('code', '');
			
			//Initialize cURL session
			$ch = curl_init();

			//Set cURL options
			curl_setopt($ch, CURLOPT_URL, $oauthToken);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

			//Execute cURL command
			$result = curl_exec($ch);

			//Transform cURL response from json string to php array
			if ($result)
			{
				$response = json_decode($result, true);
				$this->view->name = $this->_userName = $response['name'];
				$this->view->orcidID = $this->_userOrcidID = $response['orcid'];
			}
			
			//Close cURL session
			curl_close($ch);
		}
		else if (Request::getVar('error') && Request::getVar('error_description'))
		{
			//If user clicks on the deny button, it does nothing.
		}
		else
		{
			echo "Unexpected response from ORCID";
		}
		
	}

	/**
	 * Show the landing page about user and ORCID ID
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->excAuth();
		
		// It means normal when ORCID posts authorization code back
		if (Request::getVar('code'))
		{
			//Check if user has profile in database or not
			if (Member::profileExists($this->_userName))
			{
				Member::saveOrcidToProfile($this->_userName, $this->_userOrcidID);
			}
			else
			{
				Member::saveOrcidToFile($this->_userName, $this->_userOrcidID);
			}
		}
		
		$this->view->display();
	}
}
