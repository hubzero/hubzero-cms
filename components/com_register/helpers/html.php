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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HTML helper class
 *
 * @package       hubzero-cms
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright     Copyright 2005-2011 Purdue University. All rights reserved.
 * @license       http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */
class RegistrationHelperHtml
{
	/**
	 * Returns a message wrapped in HTML tags with an "error" class
	 *
	 * @param	string	$msg		Message to be displayed
	 * @param	string	$tag		HTML tag to use
	 * @return	string
	 */
	public function error($msg, $tag = 'p')
	{
		if (empty($msg)) {
			return '';
		}
		return '<' . $tag . ' class="error">' . $msg . '</' . $tag . '>' . "\n";
	}

	/**
	 * Returns an obfuscated email address
	 *
	 * @param	string	$email	Address to be obfuscated
	 * @return	string
	 */
	public function obfuscate($email)
	{
		$length = strlen($email);
		$obfuscatedEmail = '';
		for ($i = 0; $i < $length; $i++) {
			$obfuscatedEmail .= '&#' . ord($email[$i]) . ';';
		}
		return $obfuscatedEmail;
	}

	/**
	 * Returns a message wrapped in HTML tags with a "warning" class
	 *
	 * @param	string	$msg		Message to be displayed
	 * @param	string	$tag		HTML tag to use
	 * @return	string
	 */
	public function warning($msg, $tag = 'p')
	{
		return '<' . $tag . ' class="warning">' . $msg . '</' . $tag . '>' . "\n";
	}

	/**
	 * Returns a radio input field
	 *
	 * @param	string	$name		Name of the input field
	 * @param	string	$value		Value of the input field
	 * @param	string	$class		CSS class of the input field
	 * @param	string	$checked	IF the field is checked or not
	 * @param	string	$id			ID of the input field
	 * @return	string
	 */
	public function radio($name, $value, $class = '', $checked = '', $id = '')
	{
		$o  = '<input type="radio" name="' . $name . '" value="' . $value . '"';
		$o .= ($id) ? ' id="' . $id . '"' : '';
		$o .= ($class) ? ' class="' . $class . '"' : '';
		$o .= ($checked == $value) ? ' checked="checked"' : '';
		$o .= ' />';
		return $o;
	}
}
