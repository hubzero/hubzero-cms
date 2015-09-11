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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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
		$text = preg_replace('#\b((?<!href=")(https?://www[.]|[\w-]+://?|(?<!://)www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#', '<a class="ext-link" rel="external" href="$1">$1</a>', $text);

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