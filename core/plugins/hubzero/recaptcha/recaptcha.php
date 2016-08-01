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

// No direct access
defined('_HZEXEC_') or die();

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
			return '<p class="error">' . Lang::txt('PLG_HUBZERO_RECAPTCHA_API_NEEDED') . '</p>';
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
		$response = Request::getVar('g-recaptcha-response', null);
		$remoteIp = Request::ip();

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
			if (isset($verificationResponse->{'error-codes'}))
			{
				$this->setError($verificationResponse->{'error-codes'});
			}
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