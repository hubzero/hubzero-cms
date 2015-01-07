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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$recaptcha_ajax_instances = 0;

/**
 * HUBzero plugin class for displaying image CAPTCHAs
 */
class plgHubzeroRecaptcha extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Path to JS library needed for ReCAPTCHA to display
	 *
	 * [!] Must be served over HTTPS
	 * 
	 * @var    string
	 */
	private static $_jsUrl = 'https://www.google.com/recaptcha/api.js';

	/**
	 * Path to JS fallback library needed for ReCAPTCHA to display when JS is disabled
	 *
	 * [!] Must be served over HTTPS
	 * 
	 * @var    string
	 */
	private static $_jsFallbackUrl = 'https://www.google.com/recaptcha/api/fallback?k=';

	/**
	 * ReCAPTCHA verification url
	 * 
	 * @var    string
	 */
	private static $_verifyUrl = 'https://www.google.com/recaptcha/api/siteverify?';

	/**
	 * Displays either a CAPTCHA image or form field
	 *
	 * @return string
	 */
	public function onGetCaptcha($error='')
	{
		// make sure we have the needed recaptcha API keys
		if (!$this->params->get('public') || !$this->params->get('private'))
		{
			return '<p class="error">' . JText::_('PLG_HUBZERO_RECAPTCHA_API_NEEDED') . '</p>';
		}

		// recaptcha html structure
		// this has support for users with js off
		$html  = '<label class="">&nbsp;</label><div class="field-wrap">';
		$html .= '<div class="g-recaptcha" data-type="' . $this->params->get('type', 'image') . '" data-theme="' . $this->params->get('theme', 'light') . '" data-sitekey="' . $this->params->get('public') . '"></div>
				  <noscript>
					  <div style="width: 302px; height: 352px;">
					    <div style="width: 302px; height: 352px; position: relative;">
					      <div style="width: 302px; height: 352px; position: absolute;">
					        <iframe src="' . static::$_jsFallbackUrl . $this->params->get('public') . '"
					                frameborder="0" scrolling="no"
					                style="width: 302px; height:352px; border-style: none;">
					        </iframe>
					      </div>
					      <div style="width: 250px; height: 80px; position: absolute; border-style: none;
					                  bottom: 21px; left: 25px; margin: 0px; padding: 0px; right: 25px;">
					        <textarea id="g-recaptcha-response" name="g-recaptcha-response"
					                  class="g-recaptcha-response"
					                  style="width: 250px; height: 80px; border: 1px solid #c1c1c1;
					                         margin: 0px; padding: 0px; resize: none;" value="">
					        </textarea>
					      </div>
					    </div>
					  </div>
					</noscript>
				  <script type="text/javascript" src="' . static::$_jsUrl . '?hl=' . $this->params->get('language', 'en') . '" async defer></script>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Checks for a CAPTCHA response and Calls the CAPTCHA validity check
	 *
	 * @return boolean True if valid CAPTCHA response
	 */
	public function onValidateCaptcha()
	{
		// get request params
		$response = JRequest::getVar('g-recaptcha-response', null);
		$remoteIp = JRequest::ip();

		// Discard empty solution submissions
		if ($response == null || strlen($response) == 0)
		{
			$this->setError('missing-input');
			return false;
		}

		// perform a get request to the verify server with the needed data
		$verificationResponse = $this->_submitHttpGet(static::$_verifyUrl, array(
			'secret'   => $this->params->get('private'),
			'remoteip' => $remoteIp,
			'response' => $response
		));

		// json decode response
		$verificationResponse = json_decode($verificationResponse);

		// something went wrong
		if ($verificationResponse->success !== true)
		{
			$this->setError($verificationResponse->{'error-codes'});
			return false;
		}

		// success
		return true;
	}

	/**
     * Submits an HTTP GET to a reCAPTCHA server.
     *
     * @param string $url url path to recaptcha server.
     * @param array  $data array of parameters to be sent.
     *
     * @return array response
     */
	private function _submitHttpGet($url, $data)
	{
		return file_get_contents($url . $this->_encodeQS($data));
	}

	/**
	 * Encodes the given data into a query string format
	 *
	 * @param     array $data Array of string elements to be encoded
	 * @return    string Encoded request
	 */
	private function _encodeQs($data)
	{
		$req = '';
		foreach ($data as $key => $value)
		{
			$req .= $key . '=' . urlencode(stripslashes($value)) . '&';
		}

		// Cut the last '&'
		$req = substr($req, 0, strlen($req)-1);
		return $req;
	}
}