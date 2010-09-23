<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class JobAdmin extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $jid		= NULL;  // @var int(11)
	var $uid		= NULL;  // @var int(11)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_admins', 'id', $db );
	}
	
	//-----------
	
	public function isAdmin($uid,  $jid)
	{
		if ($uid === NULL or $jid === NULL) {
			return false;
		}
		
		$query  = "SELECT id ";
		$query .= "FROM #__jobs_admins  ";
		$query .= "WHERE uid = '".$uid."' AND jid = '".$jid."'";
		$this->_db->setQuery( $query );
		if ($this->_db->loadResult()) {
			return true;
		} else {
			return false;
		}		
	}
	
	//-----------
	
	public function getAdmins($jid)
	{
		if ($jid === NULL) {
			return false;
		}
		
		$admins = array();
		
		$query  = "SELECT uid ";
		$query .= "FROM #__jobs_admins  ";
		$query .= "WHERE jid = '".$jid."'";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if ($result) {
			foreach ($result as $r) 
			{
				$admins[] = $r->uid;
			}
		}
		
		return $admins;		
	}
}
