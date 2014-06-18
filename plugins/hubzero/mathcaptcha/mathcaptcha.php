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
		$html .= JText::sprintf('PLG_HUBZERO_MATHCAPTCHA_TROUBLE_MATH', $problem['operand1'], $problem['operand2']);
		$html .= "\t" . '<input type="text" name="captcha_answer" id="captcha_answer" value="" size="3" id="answer" class="option" /> <span class="required">' . JText::_('PLG_HUBZERO_MATHCAPTCHA_REQUIRED') . '</span>' . "\n";
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
		$key = JRequest::getVar('captcha_krhash', 0);
		$answer = JRequest::getInt('captcha_answer', 0);
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
