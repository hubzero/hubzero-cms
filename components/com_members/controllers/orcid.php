<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Ahmed Abdel-Gawad <aabdelga@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Members controller class for ORCIDs
 */
class MembersControllerOrcid extends \Hubzero\Component\SiteController
{
	/**
	 * Recursively parse XML
	 *
	 * @param   object $root
	 * @param   array  $fields
	 * @return  void
	 */
	private function _parseXml($root, &$fields)
	{
		foreach ($root->children() as $ch)
		{
			if ($ch->count() == 0 && !empty($ch))
			{
				$fields[$ch->getName()] = $ch;
			}
			else
			{
				$this->_parseXml($ch, $fields);
			}
		}
	}

	/**
	 * Parse an XML tree and pull results
	 *
	 * @param   object $root
	 * @return  array
	 */
	private function _parseTree($root)
	{
		$records = array();

		foreach ($root->children() as $child)
		{
			if ($child->getName() == 'orcid-search-results')
			{
				foreach ($child->children() as $search_res)
				{
					foreach ($search_res->children() as $profile)
					{
						if ($profile->getName() == 'orcid-profile')
						{
							$this->_parseXml($profile, $fields);
							array_push($records, $fields);
						}
					}
				}
			}
		}
		return $records;
	}

	/**
	 * Search ORCID by name
	 *
	 * @param   string $fname
	 * @param   string $lname
	 * @return  string
	 */
	private function _fetchXml($fname, $lname, $email)
	{
		$url = 'http://api.' . $this->config->get('orcid_service', 'orcid.org') . '/search/orcid-bio?q=';

		$bits = array();

		if ($fname)
		{
			$bits[] = 'given-names:' . $fname;
		}

		if ($lname)
		{
			$bits[] = 'family-name:' . $lname;
		}

		if ($email)
		{
			$bits[] = 'email:' . $email;
		}

		$url .= implode('+AND+', $bits);

		$initedCurl = curl_init();
		curl_setopt($initedCurl, CURLOPT_URL, $url);
		curl_setopt($initedCurl, CURLOPT_HTTPHEADER, array('Accept: application/orcid+xml', 'Authorization: Bearer 8b9f8396-0e9d-4b74-96b0-fbcfdc678716'));
		curl_setopt($initedCurl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($initedCurl, CURLOPT_MAXREDIRS, 3);
		curl_setopt($initedCurl, CURLOPT_RETURNTRANSFER, 1);

		$curlData = curl_exec($initedCurl);

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
			$root = simplexml_load_string($curlData);
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
	 * @param   array  $records
	 * @param   string $callbackPrefix
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
	 * @param   string $header
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
	 * @param   string $orcid
	 * @return  boolean
	 */
	private function _save($orcid)
	{
		$juser = JFactory::getUser();

		// Instantiate a new profile object
		$profile = \Hubzero\User\Profile::getInstance($juser->get('id'));
		if ($profile)
		{
			$profile->set('orcid', $orcid);

			return $profile->update();
		}

		return false;
	}

	/**
	 * Fetch...
	 *
	 * @param   boolean $is_return
	 * @return  void
	 */
	public function fetchTask($is_return = false)
	{
		$first_name  = JRequest::getVar('fname', '');
		$last_name   = JRequest::getVar('lname', '');
		$email       = JRequest::getVar('email', '');
		$returnOrcid = JRequest::getInt('return', 0);
		$isRegister  = $returnOrcid == 1;

		if ($isRegister)
		{
			$callbackPrefix = "HUB.Register.";
		}
		else
		{
			$callbackPrefix = "HUB.Members.Profile.";
		}

		$root = $this->_fetchXml($first_name, $last_name, $email);

		if (!empty($root))
		{
			$records = $this->_parseTree($root);
		}
		else
		{
			$records = array();
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
	 * Show a form for uploading a file
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->fetchTask(true);

		$this->view->display();
	}

	/**
	 * Associate an ID
	 *
	 * @return  void
	 */
	public function associateTask()
	{
		if ($this->juser->get('guest'))
		{
			return;
		}

		$orcid = JRequest::getVar('orcid', '');

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
		$first_name  = JRequest::getVar('fname', '');
		$last_name   = JRequest::getVar('lname', '');
		$email       = JRequest::getVar('email', '');
		$returnOrcid = JRequest::getInt('return', 0);

		$xml_data = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
					'<orcid-message'.
						' xmlns="http://www.orcid.org/ns/orcid"'.
						' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'.
						' xsi:schemaLocation="https://raw.github.com/ORCID/ORCID-Source/master/orcid-model/src/main/resources/orcid-message-1.1.xsd">'.
						'<message-version>1.1</message-version>'.
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

		$url = 'http://api.' . $this->config->get('orcid_service', 'orcid.org') . '/orcid-profile';

		$initedCurl = curl_init($url);
		curl_setopt($initedCurl, CURLOPT_POST, 1);
		curl_setopt($initedCurl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($initedCurl, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/vdn.orcid+xml', 'Authorization: Bearer 8b9f8396-0e9d-4b74-96b0-fbcfdc678716'));
		curl_setopt($initedCurl, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($initedCurl, CURLOPT_MAXREDIRS, 3);
		curl_setopt($initedCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($initedCurl, CURLOPT_HEADER, 1);
		$curl_response = curl_exec($initedCurl);
		curl_close($initedCurl);
		$parsed_response = $this->_http_parse_headers($curl_response);

		$output = new stdClass();
		$output->success = 1;
		$output->orcid   = '';
		$output->message = '';

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
			$output->message = JText::sprintf('Failed in creating account for %s %s (%s).', $first_name, $last_name, $email);
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
}