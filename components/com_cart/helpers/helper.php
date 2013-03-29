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

}

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