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
 * @author    Ahmed Abdel-Gawad <aabdelga@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Members\Models\Member;
use Components\Members\Models\Profile;
use Exception;
use Request;
use Lang;
use User;

/**
 * Members controller class for ORCIDs
 */
class Orcid extends SiteController
{
	/**
	 * user's name
	 *
	 * @var string
	 */
	protected $_userName;

	/**
	 * user's ORCID ID
	 *
	 * @var string
	 */
	protected $_userOrcidID;

	/**
	 * OAuth AccessTokens
	 *
	 * @var  array
	 */
	protected $_accessToken;

	/**
	 * A list of ORCID services that can be used
	 *
	 * @var  array
	 */
	protected $_services = array(
		'public'  => 'pub.orcid.org',
		'publicsandbox'  => 'pub.sandbox.orcid.org',
		'members' => 'api.orcid.org',
		'sandbox' => 'api.sandbox.orcid.org'
	);

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
	 * Recursively parse XML
	 *
	 * @param   object  $root
	 * @param   array   $fields
	 * @return  void
	 */
	private function _parseXml($root, &$fields)
	{
		foreach ($root->children() as $ch)
		{
			if ($ch->getName() == 'external-identifiers')
			{
				continue;
			}
			if ($ch->count() == 0) // && !empty($ch))
			{
				$fields[$ch->getName()] = $ch->__toString(); //$ch;
			}
			else
			{
				$this->_parseXml($ch, $fields);
			}
		}
	}

	/**
	 * Query the ORCID user's name
	 *
	 * @param   string  $token
	 * @param   string  $orcid
	 * @return  array
	 */
	private function _getUserName($orcid, $token)
	{
		$srv = $this->config->get('orcid_service', 'members');
		$url = Request::scheme() . '://' . $this->_services[$srv] . '/v2.0/' . $orcid . '/person';
		$header = array('Accept: application/vnd.orcid+xml');
		$header[] = 'Authorization: Bearer ' . $token;

		$initedCurl = curl_init();
		curl_setopt($initedCurl, CURLOPT_URL, $url);
		curl_setopt($initedCurl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($initedCurl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($initedCurl, CURLOPT_MAXREDIRS, 3);
		curl_setopt($initedCurl, CURLOPT_RETURNTRANSFER, 1);

		$curlData = curl_exec($initedCurl);

		$xmlStr = htmlentities($curlData);

		$xmlStr = preg_replace('/[a-zA-Z-]+[a-zA-Z]+:([a-zA-Z])/', '$1', $xmlStr);

		$xmlStr = html_entity_decode($xmlStr);

		if (!curl_errno($initedCurl))
		{
			$info = curl_getinfo($initedCurl);
		}
		else
		{
			echo 'Curl error: ' . curl_error($initedCurl);
		}

		curl_close($initedCurl);

		try
		{
			$root = simplexml_load_string($xmlStr);
		}
		catch (Exception $e)
		{
			$root = '';
		}

		$name = array();

		if (!empty($root))
		{
			foreach ($root->children() as $child)
			{
				if ($child->getName() == 'name')
				{
					$name = array('given-names' => (string)$child->{'given-names'}, 'family-name' => (string)$child->{'family-name'});
				}
				break;
			}
		}
		return $name;
	}

	/**
	 * Parse an XML tree and pull results
	 *
	 * @param   object  $root
	 * @return  array
	 */
	private function _parseTree($root)
	{
		$records = array();

		foreach ($root->children() as $child)
		{
			if ($child->getName() == 'result')
			{
				foreach ($child->children() as $search_res)
				{
					$fields = array();
					foreach ($search_res->children() as $profile)
					{
						if (($profile->getName() == 'path'))
						{
							$fields[$profile->getName()] = $profile->__toString();
							$names = $this->_getUserName($profile->__toString(), $this->_accessToken);
							$fields = array_merge($fields, $names);
						}
						else if ($profile->getName() == 'uri')
						{
							$fields[$profile->getName()] = $profile->__toString();
						}
					}
					array_push($records, $fields);
				}
			}
		}
		return $records;
	}

	/**
	 * Get ORCID record searching access token
	 *
	 * @param   None
	 * @return  string
	 */
	private function _getAccessToken()
	{
		// Get ORCID record access token
		$srv = $this->config->get('orcid_service', 'members');
		$clientID = $this->config->get('orcid_' . $srv . '_client_id');
		$clientSecret = $this->config->get('orcid_' . $srv . '_token');
		$oauthToken = $this->_oauthToken[$srv];
		$params = "client_id=" . $clientID . "&client_secret=" . $clientSecret. "&grant_type=client_credentials&scope=/read-public";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $oauthToken);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		if ($result)
		{
			$response = json_decode($result, true);
			$this->_accessToken = $response['access_token'];
			return $this->_accessToken;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Search ORCID by name or email
	 *
	 * @param   string  $fname  First name
	 * @param   string  $lname  Last name
	 * @param   string  $iname  Insitution name
	 * @return  string
	 */
	private function _fetchXml($fname, $lname, $iname)
	{
		$srv = $this->config->get('orcid_service', 'members');

		// Search by first name, last name, institution name
		$url = Request::scheme() . '://' . $this->_services[$srv] . '/v2.0/search/?q=';
		$tkn = $this->_accessToken;

		$bits = array();

		if ($fname)
		{
			$bits[] = 'given-names:' . $fname;
		}

		if ($lname)
		{
			$bits[] = 'family-name:' . $lname;
		}

		if ($iname)
		{
			$bits[] = 'affiliation-org-name:' . $iname;
		}

		$url .= implode('+AND+', $bits);

		$header = array('Accept: application/vnd.orcid+xml');
		if ($srv != 'public')
		{
			$header[] = 'Authorization: Bearer ' . $tkn;
		}

		$initedCurl = curl_init();
		curl_setopt($initedCurl, CURLOPT_URL, $url);
		curl_setopt($initedCurl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($initedCurl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($initedCurl, CURLOPT_MAXREDIRS, 3);
		curl_setopt($initedCurl, CURLOPT_RETURNTRANSFER, 1);
		$curlData = curl_exec($initedCurl);

		$xmlStr = htmlentities($curlData);
		$xmlStr = preg_replace('/[a-zA-Z]+:([a-zA-Z])/', '$1', $xmlStr);
		$xmlStr = html_entity_decode($xmlStr);

		if (!curl_errno($initedCurl))
		{
			$info = curl_getinfo($initedCurl);
		}
		else
		{
			echo 'Curl error: ' . curl_error($initedCurl);
		}

		curl_close($initedCurl);

		try
		{
			$root = simplexml_load_string($xmlStr);
		}
		catch (Exception $e)
		{
			$root = '';
		}
		return $root;
	}

	/**
	 * Format ORCID search results
	 *
	 * @param   array   $records
	 * @param   string  $callbackPrefix
	 * @return  string
	 */
	private function _format($records, $callbackPrefix)
	{
		$view = new \Hubzero\Component\View(array(
			'name'   => $this->_controller,
			'layout' => 'results'
		));
		$view->records = $records;
		$view->callbackPrefix = $callbackPrefix;

		return $view->loadTemplate();
	}

	/**
	 * Parse response headers
	 *
	 * @param   string  $header
	 * @return  string
	 */
	private function _http_parse_headers($header)
	{
		$retVal = array();
		$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
		foreach ($fields as $field)
		{
			if (preg_match('/([^:]+): (.+)/m', $field, $match))
			{
				$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));

				if (isset($retVal[$match[1]]))
				{
					$retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
				}
				else
				{
					$retVal[$match[1]] = trim($match[2]);
				}
			}
		}
		return $retVal;
	}

	/**
	 * Save an ORCID to a profile
	 *
	 * @param   string   $orcid
	 * @return  boolean
	 */
	private function _save($orcid)
	{
		// Instantiate a new profile object
		$profile = Member::oneOrFail(User::get('id'));

		if ($profile)
		{
			$profile->set('orcid', $orcid);

			return $profile->save();
		}

		return false;
	}

	/**
	 * Fetch...
	 *
	 * @param   boolean  $is_return
	 * @return  void
	 */
	public function fetchTask($is_return = false)
	{
		$first_name  = Request::getVar('fname', '');
		$last_name   = Request::getVar('lname', '');
		$returnOrcid = Request::getInt('return', 0);
		$isRegister  = $returnOrcid == 1;
		$records = array();

		$ins_option = $this->config->get('orcid_institution_field_option');

		$ins_name = $this->config->get('orcid_user_institution_name', 'Purdue University');

		$callbackPrefix = 'HUB.Members.Profile.';
		if ($isRegister)
		{
			$callbackPrefix = 'HUB.Register.';
		}

		// The default option is searching by first name and last name. 
		$defSearch = array();

		// The configurable option is searching by first name, last name, and instituation name
		$optSearch = array();

		$firstNameSearch = $lastNameSearch = $firstNameOptSearch = $lastNameOptSearch = array();

		// Get ORCID record public access token
		$token = $this->_getAccessToken();

		if (false == $token)
		{
			return;
		}
		else
		{
			if (!empty($first_name) && !empty($last_name))
			{
				// default searching by first name and last name
				$root = $this->_fetchXml($first_name, $last_name, null);
				if (!empty($root))
				{
					$defSearch = $this->_parseTree($root);
				}

				// Searching by first name, last name, and institution name when the institution option is enabled.
				if ($ins_option)
				{
					$root = $this->_fetchXml($first_name, $last_name, $ins_name);
					if (!empty($root))
					{
						$optSearch = $this->_parseTree($root);
					}
				}
			}
			else
			{
				if (!empty($first_name) && empty($last_name))
				{
					$root = $this->_fetchXml($first_name, null, null);
					if (!empty($root))
					{
						$firstNameSearch = $this->_parseTree($root);
					}
				}

				if (empty($first_name) && !empty($last_name))
				{
					$root = $this->_fetchXml(null, $last_name, null);
					if (!empty($root))
					{
						$lastNameSearch = $this->_parseTree($root);
					}
				}

				if ($ins_option)
				{
					if (!empty($first_name) && empty($last_name))
					{
						$root = $this->_fetchXml($first_name, null, $ins_name);
						if (!empty($root))
						{
							$firstNameOptSearch = $this->_parseTree($root);
						}
					}

					if (empty($first_name) && !empty($last_name))
					{
						$root = $this->_fetchXml(null, $last_name, $ins_name);
						if (!empty($root))
						{
							$lastNameOptSearch = $this->_parseTree($root);
						}
					}
				}
			}
		}

		if ($ins_option)
		{
			if (!empty($first_name) && !empty($last_name))
			{
				$records = array_merge($defSearch, $optSearch);
			}
			else
			{
				if (!empty($first_name) && empty($last_name))
				{
					$records = array_merge($records, $firstNameSearch, $firstNameOptSearch);
				}

				if (empty($first_name) && !empty($last_name))
				{
					$records = array_merge($records, $lastNameSearch, $lastNameOptSearch);
				}
			}
		}
		else
		{
			if (!empty($first_name) && !empty($last_name))
			{
				$records = array_merge($records, $defSearch);
			}

			if (!empty($first_name) && empty($last_name))
			{
				$records = array_merge($records, $firstNameSearch);
			}

			if (empty($first_name) && !empty($last_name))
			{
				$records = array_merge($records, $lastNameSearch);
			}
		}

		ob_end_clean();
		ob_start();

		$this->view->orcid_records_html = $this->_format($records, $callbackPrefix);

		if ($is_return)
		{
			return;
		}

		echo json_encode($this->view->orcid_records_html);

		exit();
	}

	/**
	 * Show a form for searching/creating an ID
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->fetchTask(true);

		$this->view
			->set('config', $this->config)
			->display();
	}

	/**
	 * Associate an ID
	 *
	 * @return  void
	 */
	public function associateTask()
	{
		if (User::isGuest())
		{
			return;
		}

		$orcid = Request::getVar('orcid', '');

		$state = $this->_save($orcid);

		echo json_encode($state);
		exit();
	}

	/**
	 * Create an ID
	 *
	 * @return  void
	 */
	public function createTask()
	{
		$output = new \stdClass();
		$output->success = 1;
		$output->orcid   = '';
		$output->message = '';

		$srv = $this->config->get('orcid_service', 'members');
		$url = Request::scheme() . '://' . $this->_services[$srv] . '/v1.2' . '/orcid-profile';

		$tkn = $this->config->get('orcid_' . $srv . '_token');

		if (!$tkn)
		{
			$output->success = 0;
			$output->message = Lang::txt('Failed in creating account. Service is not configured properly.');

			echo json_encode($output);
			exit();
		}

		$first_name  = Request::getVar('fname', '');
		$last_name   = Request::getVar('lname', '');
		$email       = Request::getVar('email', '');
		$returnOrcid = Request::getInt('return', 0);

		if (!$first_name || !$last_name || !$email)
		{
			$output->success = 0;
			$output->message = Lang::txt('Failed in creating account. Missing information. First name: "%s", Last name: "%s", Email: "%s".', $first_name, $last_name, $email);

			echo json_encode($output);
			exit();
		}

		$xml_data = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
					'<orcid-message'.
						' xmlns="http://www.orcid.org/ns/orcid"'.
						' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'.
						' xsi:schemaLocation="https://raw.github.com/ORCID/ORCID-Source/master/orcid-model/src/main/resources/orcid-message-1.2.xsd">'.
						'<message-version>1.2</message-version>'.
						'<orcid-profile>'.
							'<orcid-bio>'.
								'<personal-details>'.
									'<given-names>' . $first_name . '</given-names>'.
									'<family-name>' . $last_name . '</family-name>'.
								'</personal-details>'.
								'<contact-details>'.
									'<email primary="true">' . $email . '</email>'.
								'</contact-details>'.
							'</orcid-bio>'.
						'</orcid-profile>'.
					'</orcid-message>';

		$initedCurl = curl_init($url);
		curl_setopt($initedCurl, CURLOPT_POST, 1);
		curl_setopt($initedCurl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($initedCurl, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/vdn.orcid+xml', 'Authorization: Bearer ' . $tkn));
		curl_setopt($initedCurl, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($initedCurl, CURLOPT_MAXREDIRS, 3);
		curl_setopt($initedCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($initedCurl, CURLOPT_HEADER, 1);
		$curl_response = curl_exec($initedCurl);
		curl_close($initedCurl);
		$parsed_response = $this->_http_parse_headers($curl_response);

		$pathComponents = array();
		if (isset($parsed_response['Location']))
		{
			$parsed_url = parse_url($parsed_response['Location']);
			$pathComponents = explode('/', trim($parsed_url['path'], '/'));
		}

		if (count($pathComponents) > 0 && !empty($pathComponents[0]))
		{
			$output->orcid = $pathComponents[0];
		}
		else
		{
			$output->success = 0;
			$output->message = Lang::txt('Failed in creating account for %s %s (%s).', $first_name, $last_name, $email);
			if (preg_match('/\<error\-desc\>(.*?)\<\/error\-desc\>/i', $curl_response, $matches))
			{
				$output->message .= ' ' . $matches[1];
			}
		}

		// If returnOrcid == 1, return the ORCID without saving
		if (!$returnOrcid)
		{
			$state = $this->_save($output->orcid);
		}

		echo json_encode($output);
		exit();
	}

	/**
	 * Create an api token
	 *
	 * @return  void
	 */
	public function authorizeTask()
	{
		$srv = $this->config->get('orcid_service', 'members');

		$url = 'https://' . $this->_services[$srv] . '/oauth/token';

		$client_id     = Request::getVar('client_id');
		$client_secret = Request::getVar('client_secret');

		if (!$client_id && !$client_secret)
		{
			throw new Exception(Lang::txt('Missing client ID or secret'), 500);
		}

		$initedCurl = curl_init($url);
		curl_setopt($initedCurl, CURLOPT_POST, 1);
		curl_setopt($initedCurl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($initedCurl, CURLOPT_MAXREDIRS, 3);
		curl_setopt($initedCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($initedCurl, CURLOPT_POSTFIELDS, "client_id=" . $client_id . "&client_secret=" . $client_secret . "&grant_type=client_credentials&scope=/orcid-profile/create");
		$curl_response = curl_exec($initedCurl);
		curl_close($initedCurl);

		print_r($curl_response);
	}
	/*
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
				$this->_userName = $response['name'];
				$this->_userOrcidID = $response['orcid'];
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
	/*
	 * Update or save orcid to #_user_profiles
	 *
	 * @param   string   $name, $orcid
	 * @return  void
	 */
	public static function saveORCIDToProfile($userID, $orcid)
	{
		$row = Profile::oneByKeyAndUser('orcid', $userID);

		// If the record exists, you are just overwriting the existing data.
		// If the record doesn't exist, your are setting for a new entry.
		$row->set('user_id', $userID);
		$row->set('access', $row->get('access', 1));
		$row->set('profile_key', 'orcid');
		$row->set('profile_value', $orcid);
		if (!$row->save())
		{
			\Notify::error($row->getError());
		}
	}

	/**
	 * Show the landing page about user and ORCID ID
	 *
	 * @return  void
	 */
	public function redirectTask()
	{
		$this->excAuth();
		// It means when ORCID posts authorization code back
		if (Request::getVar('code'))
		{
			/*
			* Check if there is such user's id in database. If there is, then save the orcid in #__user_profiles. 
			* Otherwise save the orcid in session for later use.
			*/
			$user_id = User::get('id');
			if ($user_id != 0)
			{
				self::saveORCIDToProfile($user_id, $this->_userOrcidID);
			}
			else
			{
				Session::set('orcid', $this->_userOrcidID);
			}
		}
		$view = new \Hubzero\Component\View(array('name' => 'orcid', 'layout' => 'redirect'));
		$view->set('userName', $this->_userName)->set('userORCID', $this->_userOrcidID);
		$view->display();
	}
}
