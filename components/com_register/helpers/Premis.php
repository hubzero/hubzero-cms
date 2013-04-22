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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Premis Helper
 * 
 * Long description (if any) ...
 */
class Hubzero_Register_Premis
{
	
	/**
	 * Check if hub member is linked to a PREMIS id
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      	string 		Premis user ID
	 * @return		mixed 		int: User ID if exists, bool False otherwise
	 */
	public static function getPremisUser($premisUsername)
	{
		$db = & JFactory::getDBO();
		
		$sql = 'SELECT `userId` FROM `#__premis_users` WHERE `premisId` = ';
		$sql .= $db->quote($premisUsername);
		
		$db->setQuery($sql);
		$db->query();		
						
		return $db->loadResult();
	}
	
	/**
	 * Short description for 'savePremisUser'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      	array 		Premis user info
	 * @param      	int 		Hub user ID
	 * @return		void
	 */
	public static function savePremisUser($user, $userId)
	{
		$db = & JFactory::getDBO();
		
		$sql = 	'INSERT INTO `#__premis_users` SET ' .
				'`premisId` = ' . $db->quote($user['premisId']) . ', ' .
				'`lName` = ' . $db->quote($user['lName']) . ', ' .
				'`fName` = ' . $db->quote($user['fName']) . ', ' . 
				'`email` = ' . $db->quote($user['email']) . ', ' .
				'`casId` = ' . $db->quote($user['casId']) . ', ' .
				'`userId` = ' . $db->quote($userId);
		$db->setQuery($sql);
		//echo $db->_sql;
		$db->query();	
	}
		
}