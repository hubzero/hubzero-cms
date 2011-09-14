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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'ResourcesStats'
 * 
 * Long description (if any) ...
 */
class ResourcesStats extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id       = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'resid'
	 * 
	 * @var unknown
	 */
	var $resid    = NULL;  // @var varchar(250)


	/**
	 * Description for 'restype'
	 * 
	 * @var unknown
	 */
	var $restype  = NULL;  // @var int(11)


	/**
	 * Description for 'users'
	 * 
	 * @var unknown
	 */
	var $users    = NULL;

	/**
	 * Description for 'jobs'
	 * 
	 * @var unknown
	 */
	var $jobs     = NULL;

	/**
	 * Description for 'avg_wall'
	 * 
	 * @var unknown
	 */
	var $avg_wall = NULL;

	/**
	 * Description for 'tot_wall'
	 * 
	 * @var unknown
	 */
	var $tot_wall = NULL;

	/**
	 * Description for 'avg_cpu'
	 * 
	 * @var unknown
	 */
	var $avg_cpu  = NULL;

	/**
	 * Description for 'tot_cpu'
	 * 
	 * @var unknown
	 */
	var $tot_cpu  = NULL;

	/**
	 * Description for 'datetime'
	 * 
	 * @var unknown
	 */
	var $datetime = NULL;

	/**
	 * Description for 'period'
	 * 
	 * @var unknown
	 */
	var $period   = NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_stats', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->resid ) == '') {
			$this->setError( JText::_('Your entry must have a resource ID.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'loadStats'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $resid Parameter description (if any) ...
	 * @param      string $period Parameter description (if any) ...
	 * @param      string $dthis Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class ResourcesStatsTools extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id       = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'resid'
	 * 
	 * @var unknown
	 */
	var $resid    = NULL;  // @var varchar(250)


	/**
	 * Description for 'restype'
	 * 
	 * @var unknown
	 */
	var $restype  = NULL;  // @var int(11)


	/**
	 * Description for 'users'
	 * 
	 * @var unknown
	 */
	var $users    = NULL;

	/**
	 * Description for 'sessions'
	 * 
	 * @var unknown
	 */
	var $sessions    = NULL;  // @var int(20)


	/**
	 * Description for 'simulations'
	 * 
	 * @var unknown
	 */
	var $simulations = NULL;  // @var int(20)


	/**
	 * Description for 'jobs'
	 * 
	 * @var unknown
	 */
	var $jobs     = NULL;

	/**
	 * Description for 'avg_wall'
	 * 
	 * @var unknown
	 */
	var $avg_wall = NULL;

	/**
	 * Description for 'tot_wall'
	 * 
	 * @var unknown
	 */
	var $tot_wall = NULL;

	/**
	 * Description for 'avg_cpu'
	 * 
	 * @var unknown
	 */
	var $avg_cpu  = NULL;

	/**
	 * Description for 'tot_cpu'
	 * 
	 * @var unknown
	 */
	var $tot_cpu  = NULL;

	/**
	 * Description for 'avg_view'
	 * 
	 * @var unknown
	 */
	var $avg_view = NULL;

	/**
	 * Description for 'tot_view'
	 * 
	 * @var unknown
	 */
	var $tot_view = NULL;

	/**
	 * Description for 'avg_cpus'
	 * 
	 * @var unknown
	 */
	var $avg_cpus = NULL;

	/**
	 * Description for 'tot_cpus'
	 * 
	 * @var unknown
	 */
	var $tot_cpus = NULL;

	/**
	 * Description for 'datetime'
	 * 
	 * @var unknown
	 */
	var $datetime = NULL;

	/**
	 * Description for 'period'
	 * 
	 * @var unknown
	 */
	var $period   = NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_stats_tools', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->resid ) == '') {
			$this->setError( JText::_('Your entry must have a resource ID.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'loadStats'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $resid Parameter description (if any) ...
	 * @param      string $period Parameter description (if any) ...
	 * @param      string $dthis Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

/**
 * Short description for 'ResourcesStatsToolsTop'
 * 
 * Long description (if any) ...
 */
class ResourcesStatsToolsTop extends JTable
{

	/**
	 * Description for 'top'
	 * 
	 * @var unknown
	 */
	var $top    = NULL;  // @var tinyint(4) Primary key


	/**
	 * Description for 'name'
	 * 
	 * @var unknown
	 */
	var $name   = NULL;  // @var varchar(128)


	/**
	 * Description for 'valfmt'
	 * 
	 * @var unknown
	 */
	var $valfmt = NULL;  // @var tinyint(4)


	/**
	 * Description for 'size'
	 * 
	 * @var unknown
	 */
	var $size   = NULL;  // @var tinyint(4)

	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_stats_tools_tops', 'top', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->name ) == '') {
			$this->setError( JText::_('Your entry must have a name.') );
			return false;
		}
		return true;
	}
}

/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class ResourcesStatsToolsTopvals extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id    = NULL;  // @var int(20)


	/**
	 * Description for 'top'
	 * 
	 * @var unknown
	 */
	var $top   = NULL;  // @var tinyint(4)


	/**
	 * Description for 'rank'
	 * 
	 * @var unknown
	 */
	var $rank  = NULL;  // @var tinyint(4)


	/**
	 * Description for 'name'
	 * 
	 * @var unknown
	 */
	var $name  = NULL;  // @var varchar(255)


	/**
	 * Description for 'value'
	 * 
	 * @var unknown
	 */
	var $value = NULL;  // @var int(20)

	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_stats_tools_topvals', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->name ) == '') {
			$this->setError( JText::_('Your entry must have a name.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getTopCountryRes'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @param      string $top Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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

/**
 * Short description for 'ResourcesStatsToolsUsers'
 * 
 * Long description (if any) ...
 */
class ResourcesStatsToolsUsers extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id          = NULL;  // @var int(20) Primary key


	/**
	 * Description for 'resid'
	 * 
	 * @var unknown
	 */
	var $resid       = NULL;  // @var int(20)


	/**
	 * Description for 'restype'
	 * 
	 * @var unknown
	 */
	var $restype     = NULL;  // @var int(11)


	/**
	 * Description for 'user'
	 * 
	 * @var unknown
	 */
	var $user        = NULL;  // @var varchar(32)


	/**
	 * Description for 'sessions'
	 * 
	 * @var unknown
	 */
	var $sessions    = NULL;  // @var int(20)


	/**
	 * Description for 'simulations'
	 * 
	 * @var unknown
	 */
	var $simulations = NULL;  // @var int(20)


	/**
	 * Description for 'jobs'
	 * 
	 * @var unknown
	 */
	var $jobs        = NULL;  // @var int(20)


	/**
	 * Description for 'tot_wall'
	 * 
	 * @var unknown
	 */
	var $tot_wall    = NULL;  // @var double


	/**
	 * Description for 'tot_cpu'
	 * 
	 * @var unknown
	 */
	var $tot_cpu     = NULL;  // @var double


	/**
	 * Description for 'tot_view'
	 * 
	 * @var unknown
	 */
	var $tot_view    = NULL;  // @var double


	/**
	 * Description for 'datetime'
	 * 
	 * @var unknown
	 */
	var $datetime    = NULL;  // @var datetime(0000-00-00 00:00:00)


	/**
	 * Description for 'period'
	 * 
	 * @var unknown
	 */
	var $period      = NULL;  // @var tinyint(4)

	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_stats_tools_users', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->resid ) == '') {
			$this->setError( JText::_('Your entry must have a resource ID.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getTopUsersRes'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $resid Parameter description (if any) ...
	 * @param      string $dthis Parameter description (if any) ...
	 * @param      string $period Parameter description (if any) ...
	 * @param      unknown $top Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
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

/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class ResourcesStatsClusters extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id       		= NULL;	// @var bigint(20) Primary key


	/**
	 * Description for 'cluster'
	 * 
	 * @var unknown
	 */
	var $cluster  		= NULL;	// @var varchar(255)


	/**
	 * Description for 'username'
	 * 
	 * @var unknown
	 */
	var $username 		= NULL; // @var varchar(32)


	/**
	 * Description for 'uidNumber'
	 * 
	 * @var unknown
	 */
	var $uidNumber 		= NULL;	// @var int(11)


	/**
	 * Description for 'toolname'
	 * 
	 * @var unknown
	 */
	var $toolname 		= NULL;	// @var varchar(80)


	/**
	 * Description for 'resid'
	 * 
	 * @var unknown
	 */
 	var $resid   		= NULL;	// @var int(11)


	/**
	 * Description for 'clustersize'
	 * 
	 * @var unknown
	 */
	var $clustersize  	= NULL;	// @var varchar(255)


	/**
	 * Description for 'cluster_start'
	 * 
	 * @var unknown
	 */
	var $cluster_start 	= NULL;	// @var datetime


	/**
	 * Description for 'cluster_end'
	 * 
	 * @var unknown
	 */
	var $cluster_end 	= NULL;	// @var datetime


	/**
	 * Description for 'instituion'
	 * 
	 * @var unknown
	 */
	var $instituion 	= NULL;	// @var varchar(255)


	/**
	 * Description for 'users'
	 * 
	 * @var unknown
	 */
	var $users		= NULL;

	/**
	 * Description for 'classes'
	 * 
	 * @var unknown
	 */
	var $classes 	= NULL;
	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_stats_clusters', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->resid ) == '') {
			$this->setError( JText::_('Your entry must have a resource ID.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'loadStats'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $resid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadStats( $resid=NULL )
	{
		if ($resid == NULL) {
			$resid = $this->resid;
		}
		if ($resid == NULL) {
			return false;
		}

		$sql = "SELECT COUNT(DISTINCT uidNumber, username) AS users, COUNT(DISTINCT cluster) AS classes 
				FROM $this->_tbl
				WHERE resid = '".$resid."'";

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
