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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Cart helper functions
 *
 */
class Cart_Helper
{

	/**
	 * Dump system message (debugging function)
	 *
	 * @param      mixed var: whatever needs to be ptinted
	 * @return     void
	 */
	function dump($var)
	{
		echo '<br>----<br>';
		print_r($var);
		echo '<br>----<br>';
	}

	function getUsStates()
	{
		$states = array('AL' => "Alabama",
						'AK' => "Alaska",
						'AZ' => "Arizona",
						'AR' => "Arkansas",
						'CA' => "California",
						'CO' => "Colorado",
						'CT' => "Connecticut",
						'DE' => "Delaware",
						'DC' => "District Of Columbia",
						'FL' => "Florida",
						'GA' => "Georgia",
						'HI' => "Hawaii",
						'ID' => "Idaho",
						'IL' => "Illinois",
						'IN' => "Indiana",
						'IA' => "Iowa",
						'KS' => "Kansas",
						'KY' => "Kentucky",
						'LA' => "Louisiana",
						'ME' => "Maine",
						'MD' => "Maryland",
						'MA' => "Massachusetts",
						'MI' => "Michigan",
						'MN' => "Minnesota",
						'MS' => "Mississippi",
						'MO' => "Missouri",
						'MT' => "Montana",
						'NE' => "Nebraska",
						'NV' => "Nevada",
						'NH' => "New Hampshire",
						'NJ' => "New Jersey",
						'NM' => "New Mexico",
						'NY' => "New York",
						'NC' => "North Carolina",
						'ND' => "North Dakota",
						'OH' => "Ohio",
						'OK' => "Oklahoma",
						'OR' => "Oregon",
						'PA' => "Pennsylvania",
						'RI' => "Rhode Island",
						'SC' => "South Carolina",
						'SD' => "South Dakota",
						'TN' => "Tennessee",
						'TX' => "Texas",
						'UT' => "Utah",
						'VT' => "Vermont",
						'VA' => "Virginia",
						'WA' => "Washington",
						'WV' => "West Virginia",
						'WI' => "Wisconsin",
						'WY' => "Wyoming");

		return $states;
	}

	/**
	 * Short description for 'is_positiveint'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $x Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function is_positiveint($x)
	{
		if (is_numeric($x) && intval($x) == $x && $x >= 0)
		{
			return(true);
		}
		return(false);
	}

	/**
	 * Short description for 'validemail'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $email Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function validemail($email)
	{
		if (preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/", $email))
		{
			return true;
		}
		return false;
	}

	/**
	 * Short description for 'validurl'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $url Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function validurl($url)
	{
		$ptrn = '/([a-z0-9_\-]{1,5}:\/\/)?(([a-z0-9_\-]{1,}):([a-z0-9_\-]{1,})\@)?((www\.)|([a-z0-9_\-]{1,}\.)+)?([a-z0-9_\-]{3,})(\.[a-z]{2,4})(\/([a-z0-9_\-]{1,}\/)+)?([a-z0-9_\-]{1,})?(\.[a-z]{2,})?(\?)?(((\&)?[a-z0-9_\-]{1,}(\=[a-z0-9_\-]{1,})?)+)?/';
		if (preg_match($ptrn, $url)) {
			return(1);
		} else {
			return(0);
		}
	}

	/**
	 * Short description for 'validphone'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $phone Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function validphone($phone)
	{
		if (preg_match("/^[\ \#\*\+\:\,\.0-9-]*$/", $phone)) {
			return(1);
		} else {
			return(0);
		}
	}

	/**
	 * Short description for 'validzip'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function validZip($zip)
	{
		if (preg_match("/^\d{5}([\-]\d{4})?$/", $zip))
		{
			return true;
		}
		return false;
	}

	/**
	 * Escape value for DB insertino
	 *
	 * @param	string Value to be escaped
	 * @return  string Escaped value
	 */
	public function escapeDb($val)
	{
		$val = mysql_real_escape_string($val);
		return $val;
	}

}