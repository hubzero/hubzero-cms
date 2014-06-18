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
	 * API server
	 * @var    string
	 */
	public $apiServer = 'http://www.google.com/recaptcha/api';

	/**
	 * API secure server
	 * @var    string
	 */
	public $apiSecureServer = 'https://www.google.com/recaptcha/api'; //'https://api-secure.recaptcha.net';

	/**
	 * API verify server
	 * @var    string
	 */
	public $apiVerifyServer = 'www.google.com';

	/**
	 * Displays either a CAPTCHA image or form field
	 *
	 * @return string
	 */
	public function onGetCaptcha($error='')
	{
		if (!$this->params->get('public')) 
		{
			return JText::_('PLG_HUBZERO_RECAPTCHA_API_NEEDED');
		}

		$use_ssl = true;
		$server = $this->apiSecureServer;
		
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
		{
			$use_ssl = false;
		}
		if (!$use_ssl) 
		{
			$server = $this->apiServer;
		}

		$errorpart = '';
		if ($error) 
		{
			$errorpart = '&amp;error=' . $error;
		}

		$html = '<div class="field-wrap">';
		if ($this->params->get('ajax', 1)) 
		{
			global $recaptcha_ajax_instances;
			$i = $recaptcha_ajax_instances;
			$i++;
			$id = 'recaptcha_ajax_instance_' . $i;

			$html .= '
					<div id="recaptcha_ajax_instance_' . $i . '"></div>
					<script type="text/javascript" src="' . $server . '/js/recaptcha_ajax.js"></script>
					<script type="text/javascript">
						(function(){
							function loadRecaptcha() { 
								Recaptcha.create("' . $this->params->get('public') . '","' . $id . '", {theme: "' . $this->params->get('theme', 'clean') . '"});
							}
							if (window.addEvent) {
								window.addEvent("domready", loadRecaptcha);
							} else {
								if (window.addEventListener) {
									window.addEventListener("load", loadRecaptcha);
								} else if (window.attachEvent) {
									window.attachEvent("onload", loadRecaptcha);
								} else {
									old = window.onload; 
									window.onload = function() {
										if (old && typeof old == "function") {
											old();
										}
										loadRecaptcha();
									};
								}
							}
						})();
					</script>
					';
		}
		else
		{
			$html .= '<script type="text/javascript"> var RecaptchaOptions = { theme: "' . $this->params->get('theme', 'clean') . '"  }; </script>
					<script type="text/javascript" src="'. $server . '/challenge?k=' . $this->params->get('public') . $errorpart . '"></script>';
		}

		$html .= '<noscript>
					<iframe src="'. $server . '/noscript?k=' . $this->params->get('public') . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br />
					<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
					<input type="hidden" name="recaptcha_response_field" value="manual_challenge" />
				</noscript>';
		
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
		$resp = $this->_recaptcha_check_answer(
			$this->params->get('private'),
			JRequest::getVar('REMOTE_ADDR', '', 'server'),
			JRequest::getVar('recaptcha_challenge_field'),
			JRequest::getVar('recaptcha_response_field')
		);

		if ($resp->is_valid) 
		{
			return true;
		} 
		else 
		{
			$this->setError($resp->error);
			return false;
		}
		return false;
	}

	/**
	  * Calls an HTTP POST function to verify if the user's guess was correct
	  * @param string $privkey
	  * @param string $remoteip
	  * @param string $challenge
	  * @param string $response
	  * @param array $extra_params an array of extra variables to post to the server
	  * @return ReCaptchaResponse
	  */
	private function _recaptcha_check_answer($privkey, $remoteip, $challenge, $response, $extra_params = array())
	{
		if ($privkey == null || $privkey == '') 
		{
			$this->setError('PLG_HUBZERO_RECAPTCHA_API_NEEDED');
			return;
		}

		if ($remoteip == null || $remoteip == '') 
		{
			$this->setError('PLG_HUBZERO_RECAPTCHA_REMOTE_IP_NEEDED');
			return;
		}

		//discard spam submissions
		if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) 
		{
			$recaptcha_response = new ReCaptchaResponse();
			$recaptcha_response->is_valid = false;
			$recaptcha_response->error = 'incorrect-captcha-sol';
			return $recaptcha_response;
		}

		$response = $this->_recaptcha_http_post(
			$this->apiVerifyServer, 
			'/recaptcha/api/verify',
			array(
				'privatekey' => $privkey,
				'remoteip'   => $remoteip,
				'challenge'  => $challenge,
				'response'   => $response
			) + $extra_params
		);

		$answers = explode("\n", $response [1]);
		$recaptcha_response = new ReCaptchaResponse();

		if (trim($answers [0]) == 'true') 
		{
			$recaptcha_response->is_valid = true;
		}
		else 
		{
			$recaptcha_response->is_valid = false;
			$recaptcha_response->error = $answers[1];
		}
		return $recaptcha_response;
	}

	/**
	 * Encodes the given data into a query string format
	 *
	 * @param     array $data Array of string elements to be encoded
	 * @return    string Encoded request
	 */
	private function _recaptcha_qsencode($data) 
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

	/**
	 * Submits an HTTP POST to a reCAPTCHA server
	 * 
	 * @param  string $host
	 * @param  string $path
	 * @param  array  $data
	 * @param  int    $port
	 * @return array response
	 */
	private function _recaptcha_http_post($host, $path, $data, $port = 80) 
	{
		$req = $this->_recaptcha_qsencode($data);

		$http_request  = "POST $path HTTP/1.0\r\n";
		$http_request .= "Host: $host\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
		$http_request .= "Content-Length: " . strlen($req) . "\r\n";
		$http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
		$http_request .= "\r\n";
		$http_request .= $req;

		$response = '';
		if (false == ($fs = @fsockopen($host, $port, $errno, $errstr, 10))) 
		{
			$this->setError('PLG_HUBZERO_RECAPTCHA_COULD_NOT_OPEN_SOCKET');
			return $response;
		}

		fwrite($fs, $http_request);

		while (!feof($fs))
		{
			$response .= fgets($fs, 1160); // One TCP-IP packet
		}
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);

		return $response;
	}
}

/**
 * A ReCaptchaResponse is returned from recaptcha_check_answer()
 */
class ReCaptchaResponse 
{
	var $is_valid;
	var $error;
}
