<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

/**
 * xHUB Request Class
 **/

Class XRequest
{
	function getPostParam($name, $default = '')
	{
		return XRequest::getParam($name, $default, 'P', 'STRING');
	}
	function getGetParam($name, $default = '')
	{
		return XRequest::getParam($name, $default, 'G', 'STRING');
	}
	function getIntParam($name, $default = 0, $order = 'GP')
	{
		return XRequest::getParam($name, $default, $order, 'INT');
	}
	function getFloatParam($name, $default = 0.0, $order = 'GP')
	{
		return XRequest::getParam($name, $default, $order, 'FLOAT');
	}

	function getParam($name, $default = '', $order = 'GP', $type = 'none')
	{
		$order = "D" . $order;

		$len = strlen($order);

		if (defined('_JEXEC')) {
			for($i = $len; $i >= 0; $i--) {

				$letter = $order[$i];

				if ($letter == 'G') {
					if (array_key_exists($name, $_GET)) {
						$value = JRequest::getVar($name,'','GET');
						break;
					}
				}
				else if ($letter == 'P') {
					if (array_key_exists($name, $_POST)) {
						$value = JRequest::getVar($name,'','POST');
						break;
					}
				}
				else if ($letter == 'C') {
					if (array_key_exists($name, $_COOKIE)) {
						$value = JRequest::getVar($name,'','COOKIE');
						break;
					}
				}
				else if ($letter == 'D') {
					$value = $default;
					break;
				}
			}
		}
		else {
			for($i = $len-1; $i >= 0; $i--) {

				$letter = $order[$i];

				if ($letter == 'G') {
					if (array_key_exists($name, $_GET)) {
						$value = mosGetParam($_GET, $name, null);
						break;
					}
				}
				else if ($letter == 'P') {
					if (array_key_exists($name, $_POST)) {
						$value = mosGetParam($_POST, $name, null);
						break;
					}
				}
				else if ($letter == 'C') {
					if (array_key_exists($name, $_COOKIE)) {
						$value = mosGetParam($_COOKIE, $name, null);
						break;
					}
				}
				else if ($letter == 'D') {
					$value = $default;
					break;
				}
			}

			if (get_magic_quotes_gpc() && ($letter != 'D'))
				$value = stripslashes($value);

		}

		if (!isset($value))
			$value = $default;

		if ($type == 'INT')
			return intval($value);

		if ($type == 'FLOAT')
			return floatval($value);
			
		return $value;
	}
}

?>
