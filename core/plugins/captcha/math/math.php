<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for displaying math CAPTCHAs
 */
class plgCaptchaMath extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Displays either a CAPTCHA question
	 *
	 * @param   string  $name   The name of the field. Not Used.
	 * @param   string  $id     The id of the field.
	 * @param   string  $class  The class of the field. This should be passed as 'class="required"'.
	 * @return  string
	 */
	public function onDisplay($name = null, $id = 'image_captcha_1', $class = '')
	{
		// Generate a CAPTCHA
		$lower = intval($this->params->get('lower'), 0);
		$upper = intval($this->params->get('upper'), 10);
		if ($lower > $upper)
		{
			$lower = 0;
		}
		if ($upper == $lower)
		{
			$upper = 10;
		}

		$problem = array(
			'operand1' => rand($lower, $upper),
			'operand2' => rand($lower, $upper)
		);
		$problem['sum'] = $problem['operand1'] + $problem['operand2'];
		$problem['key'] = $this->_generateHash($problem['sum'], date('j'));

		// Build the fields
		$html  = '<div class="form-group">' . "\n";
		$html .= '<label for="captcha-answer">' . "\n";
		$html .= Lang::txt('PLG_CAPTCHA_MATH_TROUBLE_MATH', $problem['operand1'], $problem['operand2']);
		$html .= "\t" . '<input type="text" name="captcha_answer" id="captcha-answer" value="" size="3" id="answer" class="option form-control" /> <span class="required">' . Lang::txt('JREQUIRED') . '</span>' . "\n";
		$html .= "\t" . '<input type="hidden" name="captcha_krhash" id="captcha-krhash" value="' . $problem['key'] . '" />' . "\n";
		$html .= '</label>' . "\n";
		$html .= '</div>' . "\n";

		// Return the HTML
		return $html;
	}

	/**
	 * Checks for a CAPTCHA response and Calls the CAPTCHA validity check
	 *
	 * @param   string   $code  Answer provided by user. Not needed for the Recaptcha implementation
	 * @return  boolean  True if valid CAPTCHA response
	 */
	public function onCheckAnswer($code = null)
	{
		$key    = Request::getInt('captcha_krhash', 0);
		$answer = Request::getInt('captcha_answer', 0);
		$answer = $this->_generateHash($answer, date('j'));

		if ($answer == $key)
		{
			return true;
		}

		return false;
	}

	/**
	 * Generate an answer key
	 *
	 * @param   string  $input  CAPTCHA answer
	 * @param   string  $day    Current day
	 * @return  string
	 */
	private function _generateHash($input, $day)
	{
		// Add date:
		$input .= $day . date('ny');

		// Get MD5 and reverse it
		$enc = strrev(md5($input));

		// Get only a few chars out of the string
		$enc = substr($enc, 26, 1) . substr($enc, 10, 1) . substr($enc, 23, 1) . substr($enc, 3, 1) . substr($enc, 19, 1);

		return $enc;
	}
}
