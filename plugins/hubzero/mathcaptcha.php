<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_hubzero_mathcaptcha' );

//-----------

class plgHubzeroMathcaptcha extends JPlugin
{
	public function plgHubzeroMathcaptcha(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'hubzero', 'mathcaptcha' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------
	
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

	//-----------
	
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

	//-----------
	
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