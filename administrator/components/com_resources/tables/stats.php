<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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


class ResourcesStats extends JTable 
{
	var $id       = NULL;  // @var int(11) Primary key
	var $resid    = NULL;  // @var varchar(250)
	var $restype  = NULL;  // @var int(11)
	var $users    = NULL;
	var $jobs     = NULL;
	var $avg_wall = NULL;
	var $tot_wall = NULL;
	var $avg_cpu  = NULL;
	var $tot_cpu  = NULL;
	var $datetime = NULL;
	var $period   = NULL;
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_stats', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->resid ) == '') {
			$this->setError( JText::_('Your entry must have a resource ID.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function loadStats( $resid=NULL, $period=NULL, $dthis=NULL ) 
	{
		if ($resid == NULL) {
			$resid = $this->resid;
		}
		if ($resid == NULL) {
			return false;
		}
		
		$sql = "SELECT * 
				FROM $this->_tbl
				WHERE datetime='".$dthis."-01 00:00:00' AND period = '".$period."' AND resid = '".$resid."'";
		
		$this->_db->setQuery( $sql );
		//return $this->_db->loadObject( $this );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}


class ResourcesStatsTools extends JTable 
{
	var $id       = NULL;  // @var int(11) Primary key
	var $resid    = NULL;  // @var varchar(250)
	var $restype  = NULL;  // @var int(11)
	var $users    = NULL;
	var $sessions    = NULL;  // @var int(20)
	var $simulations = NULL;  // @var int(20)
	var $jobs     = NULL;
	var $avg_wall = NULL;
	var $tot_wall = NULL;
	var $avg_cpu  = NULL;
	var $tot_cpu  = NULL;
	var $avg_view = NULL;
	var $tot_view = NULL;
	var $avg_cpus = NULL;
	var $tot_cpus = NULL;
	var $datetime = NULL;
	var $period   = NULL;
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_stats_tools', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->resid ) == '') {
			$this->setError( JText::_('Your entry must have a resource ID.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function loadStats( $resid=NULL, $period=NULL, $dthis=NULL ) 
	{
		if ($resid == NULL) {
			$resid = $this->resid;
		}
		if ($resid == NULL) {
			return false;
		}
		
		$sql = "SELECT id, users, sessions, simulations, jobs, avg_wall, tot_wall, avg_cpu, tot_cpu, avg_view, tot_view, avg_wait, tot_wait, avg_cpus, tot_cpus, period, LEFT(datetime,7) as datetime 
				FROM $this->_tbl
				WHERE datetime='".$dthis."-00 00:00:00' AND period = '".$period."' AND resid = '".$resid."'";
		
		$this->_db->setQuery( $sql );
		//return $this->_db->loadObject( $this );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}


class ResourcesStatsToolsTop extends JTable 
{
	var $top    = NULL;  // @var tinyint(4) Primary key
	var $name   = NULL;  // @var varchar(128)
	var $valfmt = NULL;  // @var tinyint(4)
	var $size   = NULL;  // @var tinyint(4)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_stats_tools_tops', 'top', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->name ) == '') {
			$this->setError( JText::_('Your entry must have a name.') );
			return false;
		}
		return true;
	}
}


class ResourcesStatsToolsTopvals extends JTable 
{
	var $id    = NULL;  // @var int(20)
	var $top   = NULL;  // @var tinyint(4)
	var $rank  = NULL;  // @var tinyint(4)
	var $name  = NULL;  // @var varchar(255)
	var $value = NULL;  // @var int(20)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_stats_tools_topvals', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->name ) == '') {
			$this->setError( JText::_('Your entry must have a name.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getTopCountryRes( $id=NULL, $top=NULL )
	{
		if ($id == NULL) {
			$id = $this->id;
		}
		if ($id == NULL) {
			return false;
		}
		if ($top == NULL) {
			$top = $this->top;
		}
		if ($top == NULL) {
			return false;
		}
		
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE id = '".$id."' AND top = '".$top."' ORDER BY rank" );
		return $this->_db->loadObjectList();
	}
}


class ResourcesStatsToolsUsers extends JTable 
{
	var $id          = NULL;  // @var int(20) Primary key
	var $resid       = NULL;  // @var int(20)
	var $restype     = NULL;  // @var int(11)
	var $user        = NULL;  // @var varchar(32)
	var $sessions    = NULL;  // @var int(20)
	var $simulations = NULL;  // @var int(20)
	var $jobs        = NULL;  // @var int(20)
	var $tot_wall    = NULL;  // @var double
	var $tot_cpu     = NULL;  // @var double
	var $tot_view    = NULL;  // @var double
	var $datetime    = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $period      = NULL;  // @var tinyint(4)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_stats_tools_users', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->resid ) == '') {
			$this->setError( JText::_('Your entry must have a resource ID.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getTopUsersRes($resid, $dthis, $period, $top) 
	{
		$sql = "SELECT u.name, s.user, u.email, u.organization, s.jobs, s.sessions, s.simulations, s.tot_wall, s.tot_cpu, s.tot_view 
				FROM $this->_tbl AS s, user AS u 
				WHERE u.user = s.user AND s.datetime='".$dthis."-00' AND s.period ='".$period."' AND s.resid='".$resid."' 
				ORDER BY s.jobs DESC limit 25";
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
}

