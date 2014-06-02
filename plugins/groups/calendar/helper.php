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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Helper Class
 */
class plgGroupsCalendarHelper
{
	/**
	 * Link string patterns that ook like URLs or email addresses
	 * 
	 * @param      string $text Text to autolink
	 * @return     string
	 */
	public static function autoLinkText($text)
	{
		//replace email links
		$text = preg_replace('/([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})/', '<a href="mailto:$1">$1</a>', $text);
		
		//replace url links
		$text = preg_replace('#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#', '<a class="ext-link" rel="external" href="$1">$1</a>', $text);
		
		//return auto-linked text
		return $text;
	}

	/**
	 * Gets an array of timezone abbreviation and name
	 * based on supplied offset (to UTC) value
	 * 
	 * @param      string $timezone Timezone offset
	 * @return     array
	 */
	public static function getTimezoneNameAndAbbreviation($timezone)
	{
		$abbreviations = array(
			'-12'   => array('abbreviation' => 'IDLW',  'name' => 'International Date Line West'),
			'-11'   => array('abbreviation' => 'MART',  'name' => 'Midway Island, Samoa Time'),
			'-10'   => array('abbreviation' => 'HAST',  'name' => 'Hawaii-Aleutian Standard Time'),
			'-9.5'  => array('abbreviation' => 'MART',  'name' => 'Taiohae, Marquesas Islands Time'),
			'-9'    => array('abbreviation' => 'AKST',  'name' => 'Alaska Standard Time'),
			'-8'    => array('abbreviation' => 'PST',   'name' => 'Pacific Standard Time'),
			'-7'    => array('abbreviation' => 'MST',   'name' => 'Mountain Standard Time'),
			'-6'    => array('abbreviation' => 'CST',   'name' => 'Central Standard Time'),
			'-5'    => array('abbreviation' => 'EST',   'name' => 'Eastern Standard Time'),
			'-4.5'  => array('abbreviation' => 'VET',   'name' => 'Venezuelan Standard Time'),
			'-4'    => array('abbreviation' => 'AST',   'name' => 'Atlantic Standard Time'),
			'-3.5'  => array('abbreviation' => 'NST',   'name' => 'Newfoundland Standard Time'),
			'-3'    => array('abbreviation' => 'WGT',   'name' => 'West Greenland Time'),
			'-2'    => array('abbreviation' => 'MAT',   'name' => 'Mid Atlantic Time'),
			'-1'    => array('abbreviation' => 'EGT',   'name' => 'East Greenland Time'),
			'0'     => array('abbreviation' => 'GMT',   'name' => 'Greenwich Mean Time'),
			'1'     => array('abbreviation' => 'CET',   'name' => 'Central European Time'),
			'2'     => array('abbreviation' => 'EET',   'name' => 'Eastern European Time'),
			'3'     => array('abbreviation' => 'EAT',   'name' => 'East Africa Time'),
			'3.5'   => array('abbreviation' => 'IRST',  'name' => 'Iran Standard Time'),
			'4'     => array('abbreviation' => 'GET',   'name' => 'Georgia Standard Time'),
			'4.5'   => array('abbreviation' => 'AFT',   'name' => 'Afghanistan Time'),
			'5'     => array('abbreviation' => 'PKT',   'name' => 'Pakistan Standard Time'),
			'5.5'   => array('abbreviation' => 'IST',   'name' => 'India Standard Time'),
			'5.75'  => array('abbreviation' => 'NPT',   'name' => 'Nepal Time'),
			'6'     => array('abbreviation' => 'BST',   'name' => 'Bangladesh Standard Time'),
			'6.5'   => array('abbreviation' => 'MMT',   'name' => 'Myanmar Time'),
			'7'     => array('abbreviation' => 'ICT',   'name' => 'Indochina Time'),
			'8'     => array('abbreviation' => 'HKT',   'name' => 'Hong Kong Time'),
			'8.75'  => array('abbreviation' => 'ULAT',  'name' => 'Ulaanbaatar Time'),
			'9'     => array('abbreviation' => 'JST',   'name' => 'Japan Standard Time'),
			'9.5'   => array('abbreviation' => 'ACST',  'name' => 'Australian Central Standard Time'),
			'10'    => array('abbreviation' => 'AEST',  'name' => 'Australian Eastern Standard Time'),
			'10.5'  => array('abbreviation' => 'LHST',  'name' => 'Lord Howe Standard Time'),
			'11'    => array('abbreviation' => 'NCT',   'name' => 'New Caledonia Time'),
			'11.5'  => array('abbreviation' => 'NFT',   'name' => 'Norfolk Time'),
			'12'    => array('abbreviation' => 'NZST',  'name' => 'New Zealand Standard Time'),
			'12.75' => array('abbreviation' => 'CHAST', 'name' => 'Chatham Island Standard Time'),
			'13'    => array('abbreviation' => 'TOT',   'name' => 'Tonga Time'),
			'14'    => array('abbreviation' => 'LINT',  'name' => 'Line Islands Time')
		);

		return (isset($abbreviations[$timezone])) ? $abbreviations[$timezone] : null;
	}
}