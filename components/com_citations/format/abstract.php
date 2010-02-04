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

abstract class CitationsFormatAbstract
{
	public function cleanUrl($url) 
	{
		$url = stripslashes($url);
		$url = str_replace('&amp;', '&', $url);
		$url = str_replace('&', '&amp;', $url);
		
		return $url;
	}
	
	//-----------

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
	
	//-----------
	
	public function grammarCheck($html, $punct=',') 
	{
		if (substr($html,-1) == '"') {
			$html = substr($html,0,strlen($html)-1).$punct.'"';
		} else {
			$html .= $punct;
		}
		return $html;
	}
	
	//-----------
	
	public function format($row, $link='none', $highlight='')
	{
		return '';
	}
}
