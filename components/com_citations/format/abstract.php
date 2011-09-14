<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

abstract class CitationsFormatAbstract
{
	public function cleanUrl($url)
	{
		$url = stripslashes($url);
		$url = str_replace('&amp;', '&', $url);
		$url = str_replace('&', '&amp;', $url);

		return $url;
	}

	public function keyExistsOrIsNotEmpty($key,$row)
	{
		if (isset($row->$key)) {
			if ($row->$key != '' && $row->$key != '0') {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function grammarCheck($html, $punct=',')
	{
		if (substr($html,-1) == '"') {
			$html = substr($html,0,strlen($html)-1).$punct.'"';
		} else {
			$html .= $punct;
		}
		return $html;
	}

	public function format($row, $link='none', $highlight='')
	{
		return '';
	}
}

