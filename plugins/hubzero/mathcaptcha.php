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
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_hubzero_mathcaptcha' );

/**
 * Short description for 'plgHubzeroMathcaptcha'
 * 
 * Long description (if any) ...
 */
class plgHubzeroMathcaptcha extends JPlugin
{

	/**
	 * Short description for 'plgHubzeroMathcaptcha'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgHubzeroMathcaptcha(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'hubzero', 'mathcaptcha' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'onGetCaptcha'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
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

		$html  = '<label for="captcha_answer">'."\n";
		$html .= JText::sprintf('PLG_HUBZERO_MATHCAPTCHA_TROUBLE_MATH', $problem['operand1'], $problem['operand2']);
		$html .= "\t".'<input type="text" name="captcha_answer" id="captcha_answer" value="" size="3" id="answer" class="option" /> <span class="required">'.JText::_('PLG_HUBZERO_MATHCAPTCHA_REQUIRED').'</span>'."\n";
		$html .= "\t".'<input type="hidden" name="captcha_krhash" id="captcha_krhash" value="'.$problem['key'].'" />'."\n";
		$html .= '</label>'."\n";
		// Return the HTML
		return $html;
	}

	/**
	 * Short description for 'onValidateCaptcha'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function onValidateCaptcha()
	{
		$key = JRequest::getVar('captcha_krhash', 0);
		$answer = JRequest::getInt('captcha_answer', 0);
		$answer = $this->_generateHash($answer, date('j'));

		if ($answer == $key) {
			return true;
		}
		return false;
	}

	/**
	 * Short description for '_generateHash'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $input Parameter description (if any) ...
	 * @param      string $day Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
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
