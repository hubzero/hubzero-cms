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

/**
 * Short description for 'CitationsFormatAbstract'
 * 
 * Long description (if any) ...
 */
abstract class CitationsFormatAbstract
{

	/**
	 * Short description for 'cleanUrl'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $url Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function cleanUrl($url)
	{
		$url = stripslashes($url);
		$url = str_replace('&amp;', '&', $url);
		$url = str_replace('&', '&amp;', $url);

		return $url;
	}

	/**
	 * Short description for 'keyExistsOrIsNotEmpty'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $key Parameter description (if any) ...
	 * @param      object $row Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for 'grammarCheck'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $html Parameter description (if any) ...
	 * @param      string $punct Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function grammarCheck($html, $punct=',')
	{
		if (substr($html,-1) == '"') {
			$html = substr($html,0,strlen($html)-1).$punct.'"';
		} else {
			$html .= $punct;
		}
		return $html;
	}

	/**
	 * Short description for 'format'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $row Parameter description (if any) ...
	 * @param      string $link Parameter description (if any) ...
	 * @param      string $highlight Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function format($row, $link='none', $highlight='')
	{
		return '';
	}
}

