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

/**
 * HUBzero plugin class for displaying math CAPTCHAs
 */
class plgHubzeroMathcaptcha extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return a CAPTCHA question
	 *
	 * @return     string HTML
	 */
	public function onGetCaptcha()
	{
		// Generate a CAPTCHA
		$problem = array();
		$problem['operand1'] = rand(0,10);
		$problem['operand2'] = rand(0,10);
		$problem['sum'] = $problem['operand1'] + $problem['operand2'];
		$problem['key'] = $this->_generateHash($problem['sum'], date('j'));

		// Build the fields

		$html  = '<label for="captcha_answer">' . "\n";
		$html .= Lang::txt('PLG_HUBZERO_MATHCAPTCHA_TROUBLE_MATH', $problem['operand1'], $problem['operand2']);
		$html .= "\t" . '<input type="text" name="captcha_answer" id="captcha_answer" value="" size="3" id="answer" class="option" /> <span class="required">' . Lang::txt('PLG_HUBZERO_MATHCAPTCHA_REQUIRED') . '</span>' . "\n";
		$html .= "\t" . '<input type="hidden" name="captcha_krhash" id="captcha_krhash" value="' . $problem['key'] . '" />' . "\n";
		$html .= '</label>' . "\n";

		// Return the HTML
		return $html;
	}

	/**
	 * Compare answer to key
	 *
	 * @return     boolean True if answer is valid
	 */
	public function onValidateCaptcha()
	{
		$key = Request::getVar('captcha_krhash', 0);
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
	 * @param      string $input CAPTCHA answer
	 * @param      string $day   Current day
	 * @return     string
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
