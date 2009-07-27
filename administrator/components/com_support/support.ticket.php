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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

class SupportTicket extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $status     = NULL;  // @var int(3)  --  0 = new, 1 = accepted, 2 = closed
	var $created    = NULL;  // @var datetime
	var $login      = NULL;  // @var string(200)
	var $severity   = NULL;  // @var string(30)
	var $owner      = NULL;  // @var string(50)
	var $category   = NULL;  // @var string(50)
	var $summary    = NULL;  // @var string(250)
	var $report     = NULL;  // @var text
	var $resolved   = NULL;  // @var string(50)
	var $email      = NULL;  // @var string(200)
	var $name       = NULL;  // @var string(200)
	var $os         = NULL;  // @var string(50)
	var $browser    = NULL;  // @var string(50)
	var $ip         = NULL;  // @var string(200)
	var $hostname   = NULL;  // @var string(200)
	var $uas        = NULL;  // @var string(250)
	var $referrer   = NULL;  // @var string(250)
	var $cookies    = NULL;  // @var int(3)
	var $instances  = NULL;  // @var int(11)
	var $section    = NULL;  // @var int(11)
	var $type       = NULL;  // @var int(3)
	var $group      = NULL;  // @var string(250)

	//-----------

	function __construct( &$db ) 
	{
		parent::__construct( '#__support_tickets', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (!$this->id && trim( $this->report ) == '') {
			$this->setError( JText::_('SUPPORt_ERROR_BLANK_REPORT') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	function buildQuery( $filters, $admin ) 
	{
		//$juser =& JFactory::getUser();
		
		$filter = " WHERE report!=''";
		//$filter = " WHERE (section=1 OR section=0)";
		/*if ($admin) {
			$filter .= " WHERE (section=1 OR section=0)";
		} else {
			$filter .= " WHERE (section=1 OR section=0) AND login='".$juser->get('username')."'";
		}*/
		switch ($filters['status'])
		{
			case 'open':   $filter .= " AND (status=0 OR status=1)"; break;
			case 'closed': $filter .= " AND status=2";               break;
			case 'all':    $filter .= "";                            break;
		}
		if (isset($filters['severity']) && $filters['severity'] != '') {
			$filter .= " AND severity='".$filters['severity']."'";
		}
		if ($admin) {
			switch ($filters['type'])
			{
				case '3': $filter .= " AND type=3"; break;
				case '2': $filter .= ""; break;
				case '1': $filter .= " AND type=1"; break;
				case '0': 
				default:  $filter .= " AND type=0"; break;
			}
		} else {
			$filter .= " AND type=0";
		}
		if (isset($filters['category']) && $filters['category'] != '') {
			$filter .= " AND category='".$filters['category']."'";
		}
		if (isset($filters['owner']) && $filters['owner'] != '') {
			$filter .= " AND (owner='".$filters['owner']."'";
			if (!isset($filters['reportedby']) || $filters['reportedby'] == '') {
				$filter .= ")";
			}
		}
		if (isset($filters['group']) && $filters['group'] != '') {
			$filter .= " AND `group`='".$filters['group']."'";
		}
		if (isset($filters['reportedby']) && $filters['reportedby'] != '') {
			if (isset($filters['owner']) && $filters['owner'] != '') {
				$filter .= " OR ";
			} else {
				$filter .= " AND ";
			}
			$filter .= "login='".$filters['reportedby']."'";
		}
		if ($admin == false && (!isset($filters['owner']) || $filters['owner'] != '') && (!isset($filters['reportedby']) || $filters['reportedby'] != '')) {
			ximport('xuserhelper');
			$juser =& JFactory::getUser();
			$xgroups = XUserHelper::getGroups($juser->get('id'), 'members');
			$groups = '';
			if ($xgroups) {
				$g = array();
				foreach ($xgroups as $xgroup) 
				{
					$g[] = $xgroup->cn;
				}
				$groups = implode("','",$g);
			}
			$filter .= " OR `group` IN ('$groups')";
		}
		if ($admin == false) {
			$filter .= ")";
		}
		
		/*switch ($filters['searchin'])
		{
			case 'reporter':
				if ($filters['search']) {
					$filter .= " AND (LOWER( name ) LIKE '%".$filters['search']."%' OR LOWER( login ) LIKE '%".$filters['search']."%')";
				}
				break;
			case 'owner':
				if ($filters['search']) {
					$filter .= " AND (LOWER( owner ) LIKE '%".$filters['search']."%')";
				}
				break;
			case 'report': 
			default:
				if ($filters['search']) {
					$filter .= " AND (LOWER( summary ) LIKE '%".$filters['search']."%'";
					$filter .= " OR LOWER( report ) LIKE '%".$filters['search']."%'";
					if(is_numeric($filters['search'])) {
						$filter .= " OR id=".$filters['search'];
					}
					$filter .= ")";
				}
				break;
		}*/
		if (isset($filters['search']) && $filters['search'] != '') {
			$from = "(
						( SELECT f.id, f.summary, f.report, f.category, f.status, f.severity, f.resolved, f.owner, f.created, f.login, f.name, f.type, f.section, f.group 
							FROM $this->_tbl AS f ";
			if (isset($filters['tag']) && $filters['tag'] != '') {
				$from .= ", #__support_tags AS st, #__tags as t ";
			}
			if (isset($filters['search']) && $filters['search'] != '') {
				$from .= "WHERE ";
				$from .= "(LOWER( f.summary ) LIKE '%".$filters['search']."%' 
						OR LOWER( f.report ) LIKE '%".$filters['search']."%' 
						OR LOWER( f.owner ) LIKE '%".$filters['search']."%' 
						OR LOWER( f.name ) LIKE '%".$filters['search']."%' 
						OR LOWER( f.login ) LIKE '%".$filters['search']."%'";

				if (is_numeric($filters['search'])) {
					$from .= " OR ";
					$from .= "id=".$filters['search'];
				}
				$from .= ") ";
			}
			if (isset($filters['tag']) && $filters['tag'] != '') {
				if (!isset($filters['search']) || $filters['search'] == '') {
					$from .= "WHERE ";
				} else {
					$from .= " AND ";
				}
				$from .= "st.ticketid=f.id AND st.tagid=t.id AND t.tag='".$filters['tag']."'";
			}
			$from .= ") UNION (
				SELECT g.id, g.summary, g.report, g.category, g.status, g.severity, g.resolved, g.owner, g.created, g.login, g.name, g.type, g.section, g.group
				FROM #__support_comments AS w, $this->_tbl AS g
				WHERE w.ticket=g.id";
			if (isset($filters['search']) && $filters['search'] != '') {
				$from .= " AND LOWER( w.comment ) LIKE '%".$filters['search']."%'";
			}
			$from .= ")) AS h";
		} else {
			$from = "$this->_tbl AS f";
			if (isset($filters['tag']) && $filters['tag'] != '') {
				$from .= ", #__support_tags AS st, #__tags as t";
			}
			if (isset($filters['tag']) && $filters['tag'] != '') {
				$filter .= " AND st.ticketid=f.id AND st.tagid=t.id AND t.tag='".$filters['tag']."'";
			}
		}
		
		$query = $from." ".$filter;
		
		return $query;
	}
	
	//-----------
	
	function getTicketsCount( $filters=array(), $admin=false ) 
	{
		$filter = $this->buildQuery( $filters, $admin );
		
		$sql = "SELECT count(*) FROM $filter";

		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getTickets( $filters=array(), $admin=false ) 
	{
		$filter = $this->buildQuery( $filters, $admin );
		
		if (isset($filters['search']) && $filters['search'] != '') {
			$sql = "SELECT `id`, `summary`, `report`, `category`, `status`, `severity`, `resolved`, `owner`, `created`, `login`, `name`, `group`";
		} else {
			$sql = "SELECT f.id, f.summary, f.report, f.category, f.status, f.severity, f.resolved, f.group, f.owner, f.created, f.login, f.name";
		}
		$sql .= " FROM $filter";
		$sql .= " ORDER BY ".$filters['sort'].' '.$filters['sortdir'];
		$sql .= ($filters['limit']) ? " LIMIT ".$filters['start'].",".$filters['limit'] : "";

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function getTicketId($which, $filters, $authorized)
	{
		$filter = $this->buildQuery( $filters, $authorized );
		
		if ($which == 'prev') {
			$filter .= " AND id < $this->id";
			$filters['sortby'] = "id DESC";
		} elseif ($which == 'next') {
			$filter .= " AND id > $this->id";
			$filters['sortby'] = "id ASC";
		}
	
		$this->_db->setQuery( "SELECT id FROM $filter ORDER BY ".$filters['sortby']." LIMIT 1" );
		return $this->_db->loadResult();
	}
}
?>