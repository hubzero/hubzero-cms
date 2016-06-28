<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die;

jimport('joomla.environment.browser');

/**
 * Recaptcha Plugin.
 * Based on the official recaptcha library( https://developers.google.com/recaptcha/docs/php )
 */
class plgCaptchaRecaptcha extends \Hubzero\Plugin\Plugin
{
	const RECAPTCHA_API_SERVER = "http://www.google.com/recaptcha/api";
	const RECAPTCHA_API_SECURE_SERVER = "https://www.google.com/recaptcha/api";
	const RECAPTCHA_VERIFY_SERVER = "www.google.com";

	/**
	 * Constructor
	 *
	 * @param   object  $subject
	 * @param   array   $config
	 * @return  void
	 */
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Initialise the captcha
	 *
	 * @param   string   $id  The id of the field.
	 * @return  boolean  True on success, false otherwise
	 * @since   2.5
	 */
	public function onInit($id)
	{
		// Initialise variables
		$lang   = $this->_getLanguage();
		$pubkey = $this->params->get('public_key', '');
		$theme  = $this->params->get('theme', 'clean');

		if ($pubkey == null || $pubkey == '')
		{
			throw new Exception(Lang::txt('PLG_RECAPTCHA_ERROR_NO_PUBLIC_KEY'));
		}

		$server = self::RECAPTCHA_API_SERVER;
		if (Request::isSecure())
		{
			$server = self::RECAPTCHA_API_SECURE_SERVER;
		}

		Html::asset('script', $server.'/js/recaptcha_ajax.js');

		Document::addScriptDeclaration('jQuery(document).ready(function($) {
			Recaptcha.create("' . $pubkey . '", "dynamic_recaptcha_1", {theme: "' . $theme . '",' . $lang . 'tabindex: 0});
		});');

		return true;
	}

	/**
	 * Gets the challenge HTML
	 *
	 * @param   string  $name   The name of the field. Not Used.
	 * @param   string  $id     The id of the field.
	 * @param   string  $class  The class of the field. This should be passed as 'class="required"'.
	 * @return  string
	 */
	public function onDisplay($name = null, $id = 'dynamic_recaptcha_1', $class = '')
	{
		return '<div id="' . $id . '"></div>';
	}

	/**
	  * Calls an HTTP POST function to verify if the user's guess was correct
	  *
	 * @param   string   $code  Answer provided by user. Not needed for the Recaptcha implementation
	 * @return  boolean  True if valid CAPTCHA response
	  */
	public function onCheckAnswer($code)
	{
		// Initialise variables
		$privatekey = $this->params->get('private_key');
		$remoteip   = Request::getVar('REMOTE_ADDR', '', 'SERVER');
		$challenge  = Request::getString('recaptcha_challenge_field', '');
		$response   = Request::getString('recaptcha_response_field', '');;

		// Check for Private Key
		if (empty($privatekey))
		{
			$this->_subject->setError(Lang::txt('PLG_RECAPTCHA_ERROR_NO_PRIVATE_KEY'));
			return false;
		}

		// Check for IP
		if (empty($remoteip))
		{
			$this->_subject->setError(Lang::txt('PLG_RECAPTCHA_ERROR_NO_IP'));
			return false;
		}

		// Discard spam submissions
		if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0)
		{
			$this->_subject->setError(Lang::txt('PLG_RECAPTCHA_ERROR_EMPTY_SOLUTION'));
			return false;
		}

		$response = $this->_recaptcha_http_post(
			self::RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/verify",
			array(
				'privatekey' => $privatekey,
				'remoteip'   => $remoteip,
				'challenge'  => $challenge,
				'response'   => $response
			)
		);

		$answers = explode("\n", $response[1]);

		if (trim($answers[0]) == 'true')
		{
			return true;
		}
		else
		{
			//@todo use exceptions here
			$this->_subject->setError(Lang::txt('PLG_RECAPTCHA_ERROR_'.strtoupper(str_replace('-', '_', $answers[1]))));
			return false;
		}
	}

	/**
	 * Encodes the given data into a query string format.
	 *
	 * @param   string  $data  Array of string elements to be encoded
	 * @return  string  Encoded request
	 * @since   2.5
	 */
	private function _recaptcha_qsencode($data)
	{
		$req = '';
		foreach ($data as $key => $value)
		{
			$req .= $key . '=' . urlencode(stripslashes($value)) . '&';
		}

		// Cut the last '&'
		$req = rtrim($req, '&');
		return $req;
	}

	/**
	 * Submits an HTTP POST to a reCAPTCHA server.
	 *
	 * @param   string  $host
	 * @param   string  $path
	 * @param   array   $data
	 * @param   int     $port
	 * @return  array   Response
	 * @since   2.5
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
		if (($fs = @fsockopen($host, $port, $errno, $errstr, 10)) == false )
		{
			die('Could not open socket');
		}

		fwrite($fs, $http_request);

		while (!feof($fs))
		{
			// One TCP-IP packet
			$response .= fgets($fs, 1160);
		}

		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);

		return $response;
	}

	/**
	 * Get the language tag or a custom translation
	 *
	 * @return string
	 *
	 * @since  2.5
	 */
	private function _getLanguage()
	{
		$tag = explode('-', Lang::getTag());
		$tag = $tag[0];
		$available = array('en', 'pt', 'fr', 'de', 'nl', 'ru', 'es', 'tr');

		if (in_array($tag, $available))
		{
			return "lang : '" . $tag . "',";
		}

		// If the default language is not available, let's search for a custom translation
		if ($language->hasKey('PLG_RECAPTCHA_CUSTOM_LANG'))
		{
			$custom[] ='custom_translations : {';
			$custom[] ="\t".'instructions_visual : "' . Lang::txt('PLG_RECAPTCHA_INSTRUCTIONS_VISUAL') . '",';
			$custom[] ="\t".'instructions_audio : "' . Lang::txt('PLG_RECAPTCHA_INSTRUCTIONS_AUDIO') . '",';
			$custom[] ="\t".'play_again : "' . Lang::txt('PLG_RECAPTCHA_PLAY_AGAIN') . '",';
			$custom[] ="\t".'cant_hear_this : "' . Lang::txt('PLG_RECAPTCHA_CANT_HEAR_THIS') . '",';
			$custom[] ="\t".'visual_challenge : "' . Lang::txt('PLG_RECAPTCHA_VISUAL_CHALLENGE') . '",';
			$custom[] ="\t".'audio_challenge : "' . Lang::txt('PLG_RECAPTCHA_AUDIO_CHALLENGE') . '",';
			$custom[] ="\t".'refresh_btn : "' . Lang::txt('PLG_RECAPTCHA_REFRESH_BTN') . '",';
			$custom[] ="\t".'help_btn : "' . Lang::txt('PLG_RECAPTCHA_HELP_BTN') . '",';
			$custom[] ="\t".'incorrect_try_again : "' . Lang::txt('PLG_RECAPTCHA_INCORRECT_TRY_AGAIN') . '",';
			$custom[] ='},';
			$custom[] ="lang : '" . $tag . "',";

			return implode("\n", $custom);
		}

		// If nothing helps fall back to english
		return '';
	}
}
