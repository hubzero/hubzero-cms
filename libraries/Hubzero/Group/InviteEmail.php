<?php
/**
 * @package     hubzero-cms
 * @author      Christopher Smoak <csmoak@purdue.edu>
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
?>

<?php

Class Hubzero_Group_Invite_Email extends JTable
{
	var $id = NULL;
	var $email = NULL;
	var $gidNumber = NULL;
	var $token = NULL;
	
	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__xgroups_inviteemails', 'id', $db );
	}
	
	//-----------
	
	public function getInviteEmails( $gid, $email_only = false )
	{
		$final = array();
		$db =& JFactory::getDBO();
		
		$sql = "SELECT * FROM $this->_tbl WHERE gidNumber=".$db->Quote($gid);
		$db->setQuery($sql);
		$invitees = $db->loadAssocList();
		
		if($email_only) {
			foreach($invitees as $invitee) {
				$final[] = $invitee['email'];
			}
		} else {
			$final = $invitees;
		}
		
		return $final;
	}
	
	//-----------
}

?>
