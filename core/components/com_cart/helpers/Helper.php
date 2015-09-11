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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Cart helper functions
 *
 */
class Cart_Helper
{

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
	 * Check if the number is a non negative integer
	 *
	 * @param	int 	Integer
	 * @param   bool 	$notZero Flag if zero should be accepted (by default zero is ok)
	 * @return  bool
	 */
	public function isNonNegativeInt($x, $zeroAccepted = true)
	{
		if (isset($x) && is_numeric($x) && intval($x) == $x && $x >= 0)
		{
			if (!$zeroAccepted && $x == 0)
			{
				return false;
			}
			return true;
		}
		return false;
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