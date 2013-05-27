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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Table class for projects
 */
class Project extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         		= NULL;

	/**
	 * Project alias, varchar(30)
	 * 
	 * @var string
	 */	
	var $alias       		= NULL;
	
	/**
	 * Project title, varchar(255)
	 * 
	 * @var string
	 */
	var $title				= NULL;
	
	/**
	 * Project picture, varchar(255)
	 * 
	 * @var string
	 */
	var $picture			= NULL;
	
	/**
	 * Project description, text
	 * 
	 * @var text
	 */
	var $about				= NULL;
	
	/**
	 * Created date, datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $created			= NULL;
	
	/**
	 * Created by user (user id), int(11)
	 * 
	 * @var int
	 */
	var $created_by_user	= NULL;
	
	/**
	 * Owned by user (user id), int(11)
	 * 
	 * @var int
	 */
	var $owned_by_user		= NULL;
	
	/**
	 * Owned by group (group id), int(11)
	 * 
	 * @var int
	 */
	var $owned_by_group		= NULL;
	
	/**
	 * int(3)
	 * 
	 * 0 setup in progress/suspended
	 * 1 active
	 * 2 deleted
	 * 5 pending
	 * 
	 * @var int
	 */	
	var $state				= NULL;
	
	/**
	 * Params
	 * 
	 * @var text
	 */
	var $params				= NULL;
	
	/**
	 * Type
	 * 
	 * @var int
	 */
	var $type				= NULL;
	
	/**
	 * Privacy: 0 - public; 1 - private
	 * 
	 * @var int
	 */
	var $private			= NULL;	
	
	/**
	 * Provisioned: 0 - no; 1 - yes (not full-scale)
	 * 
	 * @var int
	 */
	var $provisioned		= NULL;	
	
	/**
	 * Modified, datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $modified			= NULL;
	
	/**
	 * Modified by user (user id), int(11)
	 * 
	 * @var int
	 */
	var $modified_by		= NULL;
	
	/**
	 * Setup stage
	 * 
	 * @var int
	 */
	var $setup_stage		= NULL;	
	
	/**
	 * Admin notes
	 * 
	 * @var text
	 */
	var $admin_notes		= NULL;	
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__projects', 'id', $db );
	}
	
	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check() 
	{
		if (trim( $this->alias ) == '') 
		{
			$this->setError( JText::_('PROJECT_MUST_HAVE_ALIAS') );
			return false;
		}
		
		if (trim( $this->title ) == '') 
		{
			$this->setError( JText::_('PROJECT_MUST_HAVE_TITLE') );
			return false;
		}

		return true;
	}
	
	/**
	 * Build query
	 * 
	 * @param      array $filters
	 * @param      boolean $admin
	 * @param      integer $uid
	 * @param      boolean $showall
	 * @param      integer $setup_complete
	 * @return     string
	 */
	public function buildQuery( $filters=array(), $admin = false, $uid = 0, $showall = 0, $setup_complete = 3 ) 
	{		
		// Process filters
		$mine 		= isset($filters['mine']) && $filters['mine'] == 1 ? 1: 0;
		$sortby 	= isset($filters['sortby']) ? $filters['sortby'] : 'title';
		$search 	= isset($filters['search']) && $filters['search'] != ''  ? $filters['search'] : '';
		$filterby 	= isset($filters['filterby']) && $filters['filterby'] != ''  ? $filters['filterby'] : '';
		$getowner 	= isset($filters['getowner']) && $filters['getowner'] == 1 ? 1: 0;
		$type 		= isset($filters['type']) ? intval($filters['type']) : NULL;
		$group 		= isset($filters['group']) && intval($filters['group']) > 0 ? $filters['group'] : '';
		$reviewer 	= isset($filters['reviewer']) && $filters['reviewer'] != '' ? $filters['reviewer'] : '';
		$which 		= isset($filters['which']) 
					&& $filters['which'] != '' 
					&& $filters['which'] != 'all' 
					? $filters['which'] : '';
				
		$query  = " FROM $this->_tbl AS p ";
		$query .= " LEFT JOIN #__project_owners AS o ON o.projectid=p.id AND o.userid='$uid' ";
		$query .= " AND o.userid != 0 AND p.state!= 2 ";
		if ($getowner) 
		{
			$query .=  " JOIN #__xprofiles as x ON x.uidNumber=p.created_by_user ";
			$query .=  " LEFT JOIN #__xgroups as g ON g.gidNumber=p.owned_by_group ";
		}
		
		if ($reviewer == 'sensitive')
		{
			$query .= " WHERE ((p.params LIKE '%hipaa_data=yes%' ";
			$query .= " OR p.params LIKE '%ferpa_data=yes%' ";
			$query .= " OR p.params LIKE '%export_data=yes%' ";
			$query .= " OR p.params LIKE 'restricted_data=maybe%' ";
			$query .= " OR p.params LIKE '%followup=yes%') ";
			$query .= " AND p.state != 2 AND p.setup_stage = ".$setup_complete." ) ";
		}
		elseif ($reviewer == 'sponsored')
		{
			$query .= " WHERE ((( p.params LIKE '%grant_title=%' AND p.params NOT LIKE '%grant_title=\\n%') ";
			$query .= " OR ( p.params LIKE '%grant_agency=%' AND p.params NOT LIKE '%grant_agency=\\n%') ";
			$query .= " OR ( p.params LIKE '%grant_budget=%' AND p.params NOT LIKE '%grant_budget=\\n%') ";
			$query .= " ) AND p.state=1 AND p.setup_stage = ".$setup_complete." ) ";
		}		
		elseif ($admin) 
		{	
			$query .= " WHERE p.provisioned = 0 ";
			$query .= $showall ? "" : " AND p.state != 2 ";
			if ($search) 
			{
				$query .= $showall 
						? " AND p.title LIKE '%".$search."%' " 
						: " AND p.title LIKE '%".$search."%' ";
			}		
		}
		else {
			if ($mine) 
			{
				$query .= $uid 
						? " WHERE (o.userid='$uid' AND o.status!=2 
							AND ((p.state != 2 AND p.setup_stage = " . $setup_complete.") 
							OR (o.role = 1 AND p.created_by_user='$uid' ))) " 
						: " WHERE 1=2";
				if ($which == 'owned' && $uid) 
				{
					$query .= " AND (p.created_by_user ='$uid' AND p.owned_by_group = 0) ";
				}
				if ($which == 'other' && $uid) 
				{
					$query .= " AND (p.created_by_user != '$uid' OR p.owned_by_group != 0) ";
				}
			}
			else 
			{
				$query .= $uid 
						? " WHERE (p.state = 1 AND p.private = 0) 
							OR (o.userid='$uid' AND o.status!=2 AND ((p.state = 1 
							AND p.setup_stage = " . $setup_complete . ") 
							OR (o.role = 1 AND p.created_by_user='$uid'))) " 
						: " WHERE p.state = 1 AND p.private = 0 ";	
			}
		}
		if ($type) 
		{
			$query .= " AND p.type = '$type' ";
		}
		if ($group) 
		{
			$query .= " AND p.owned_by_group = '$group' ";
		}
		if (isset($filters['show_prov'])) 
		{
			$query .= $filters['show_prov'] == 1 ? " AND p.provisioned = 1 " : "";
		}
		else 
		{
			$query .= " AND p.provisioned = 0 ";
		}
		if ($filterby == 'pending')
		{
			$query .= $reviewer == 'sponsored' 
					? " AND (p.params NOT LIKE '%grant_status=1%' 
						AND p.params NOT LIKE '%grant_status=2%') " 
					: " AND p.state = 5 ";
		}
	
		if (!$filters['count']) 
		{
			$sort = '';
			$sortdir = isset($filters['sortdir']) ? $filters['sortdir'] : 'ASC';
			
			switch ($sortby) 
			{
				case 'title':		
					$sort .= 'p.title ' . $sortdir . ' ';        
					break;
					
				case 'id':			
					$sort .= 'p.id ' . $sortdir . ' ';        
					break;
					
				case 'myprojects':	
					$sort .= 'p.state DESC, p.setup_stage DESC, p.title ' . $sortdir . ' ';        
					break;
				case 'owner':		
					if ($getowner) 
					{
						$sort .= 'x.name ' . $sortdir . ', g.description ' . $sortdir . ' ';    
					}
					else 
					{
						$sort .= 'p.owned_by_group ' . $sortdir 
							  . ', p.created_by_user ' . $sortdir . ' ';
					}				    
					break;
					
				case 'created':		
					$sort .= 'p.created ' . $sortdir . ' ';        
					break;
					
				case 'type':		
					$sort .= 'p.type ' . $sortdir . ' ';        
					break;
					
				case 'role':		
					$sort .= 'o.role ' . $sortdir . ' ';        
					break;
					
				case 'privacy':		
					$sort .= 'p.private ' . $sortdir . ' ';        
					break;
					
				case 'status':		
					$sort .= 'p.setup_stage ' . $sortdir . ', p.state ' 
						  . $sortdir . ', p.created ' . $sortdir;        
					break;
					
				default: 			
					$sort .= 'p.title ' . $sortdir . ' ';
					break;
			}
			
			$query  .= " ORDER BY $sort ";
		}

		return $query;
	}
	
	/**
	 * Get item count
	 * 
	 * @param      array $filters
	 * @param      boolean $admin
	 * @param      integer $uid
	 * @param      boolean $showall
	 * @param      integer $setup_complete
	 * @return     integer
	 */
	public function getCount( $filters = array(), $admin = false, $uid = 0 , $showall = 0, $setup_complete = 3 )
	{
		$filters['count'] = true;
		$admin = $admin == 'admin' ? true : false;
			
		$query  = "SELECT count(DISTINCT p.id) ";
		$query .= $this->buildQuery( $filters, $admin, $uid, $showall, $setup_complete );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Get records
	 * 
	 * @param      array $filters
	 * @param      boolean $admin
	 * @param      integer $uid
	 * @param      boolean $showall
	 * @param      integer $setup_complete
	 * @return     object
	 */
	public function getRecords( $filters = array(), $admin = false, $uid = 0, $showall = 0, $setup_complete = 3 ) 
	{
		$filters['count'] 	= false;
		$admin 				= $admin == 'admin' ? true : false;
		$updates 			= isset($filters['updates']) && $filters['updates'] == 1 ? 1: 0;
		$getowner 			= isset($filters['getowner']) && $filters['getowner'] == 1 ? 1: 0;
		$activity 			= isset($filters['activity']) && $filters['activity'] == 1 ? 1: 0;
		
		$query  = "SELECT p.*, IFNULL(o.role, 0) as role, o.id as owner, o.added as since, o.status as confirmed ";
		
		if ($getowner) 
		{
			$query .= ", x.name as authorname, g.cn as groupcn, g.description as groupname ";
		}
		$query .= " ,(SELECT t.type FROM #__project_types as t WHERE t.id=p.type) as projecttype ";
		
		if ($updates) 
		{
			$query .= ", (SELECT COUNT(*) FROM #__project_activity AS pa 
						WHERE pa.projectid=p.id AND pa.recorded >= o.lastvisit 
						AND o.lastvisit IS NOT NULL AND o.id IS NOT NULL 
						AND pa.state != 2 AND (pa.managers_only = 0 
						OR (pa.managers_only=1 AND o.role=1)) ) as newactivity ";
		}
		if ($activity) 
		{
			$query .= ", (SELECT COUNT(*) FROM #__project_activity AS pa 
						WHERE pa.projectid=p.id AND pa.state != 2 ) as activity ";
		}
		$query .= $this->buildQuery( $filters, $admin, $uid, $showall, $setup_complete );
		
		if (isset($filters['limit']) && $filters['limit'] != 'all' && $filters['limit'] != 0) 
		{
			$filters['start'] = isset($filters['start']) ? $filters['start'] : 0;
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Get projects stats
	 * 
	 * @param      string 	$period
	 * @param      boolean 	$admin
	 * @param      array 	$config
	 * @param      array 	$exclude
	 * @param      integer 	$publishing
	 * @return     array
	 */
	public function getStats($period, $admin = false, $config = array(), $exclude = array(), $publishing = 0)
	{
		$stats   = array();
		$lastLog = NULL;
		$saveLog = 0;
		$updated = NULL;
		
		$pastMonth 		= date('Y-m-d', time() - (32 * 24 * 60 * 60));		
		
		$thisYearNum 	= date('y', time());
		$thisMonthNum 	= date('m', time());
		$thisWeekNum	= date('W', time());
		
		// Do we have a recent saved stats log?
		$logged = (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_projects' . DS . 'tables' . DS . 'project.stats.php')) ? 1 : 0;
			
		if ($logged)
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_projects' . DS . 'tables' . DS . 'project.stats.php');
			
			$objStats = new ProjectStats($this->_db);
			if ($objStats->loadLog($thisYearNum, $thisMonthNum, $thisWeekNum ))
			{
				$lastLog = json_decode($objStats->stats, true);
				$updated = $objStats->processed;
			}
			else
			{
				// Save stats
				$saveLog = 1;
			}
		}
		
		$validProjects 	 = $this->getValidProjects($exclude, array(), $config, false, 'alias' );
		$validProjectIds = $this->getValidProjects($exclude, array(), $config, false, 'id' );
		
		$active = count($validProjects);
		$setup = $this->getValidProjects($exclude, array('setup' => 1), $config, 1 );
		$total = $active + $setup;
		
		$publicOnly = $admin ? false : true;
		$limit = 3;
		
		// Collect overview stats
		$stats['general'] = array(
			'total' 	=> $total,
			'setup' 	=> $setup,
			'active'	=> $active,
			'public'	=> $this->getValidProjects($exclude, array('private' => '0'), $config, 1 ),
			'sponsored'	=> $this->getValidProjects($exclude, array('sponsored' => 1), $config, 1 ),
			'sensitive'	=> $this->getValidProjects($exclude, array('sensitive' => 1), $config, 1 ),
			'new'		=> $this->getValidProjects($exclude, array('created' => date('Y-m', time()), 'all' => 1), $config, 1 )
		);
		
		// Collect activity stats
		$objAA = new ProjectActivity( $this->_db );
		$recentlyActive = count($this->getValidProjects($exclude, array('timed' => $pastMonth, 'active' => 1), $config ));
		$perc = round(($recentlyActive * 100)/$active) . '%';
		$stats['activity'] = array(
			'total' => $objAA->getActivityStats($validProjectIds, 'total'),
			'average' => $objAA->getActivityStats($validProjectIds, 'average'),
			'usage'=> $perc
		);
		
		$stats['topActiveProjects'] = $objAA->getTopActiveProjects($exclude, 5, $publicOnly);
		
		// Collect team stats
		$objO = new ProjectOwner( $this->_db );
		$multiTeam = $objO->getTeamStats($exclude, 'multi');
		$activeTeam = $objO->getTeamStats($exclude, 'registered');
		$invitedTeam = $objO->getTeamStats($exclude, 'invited');
		$multiProjectUsers = $objO->getTeamStats($exclude, 'multiusers');
		
		$teamTotal = $activeTeam + $invitedTeam;
		
		$perc = round(($multiTeam * 100)/$total) . '%';
		$stats['team'] = array(
			'total' => $teamTotal,
			'average' => $objO->getTeamStats($exclude, 'average'),
			'multi' => $perc,
			'multiusers' => $multiProjectUsers
		);
				
		$stats['topTeamProjects'] = $objO->getTopTeamProjects($exclude, $limit, $publicOnly);
				
		// Collect files stats
		if ($lastLog)
		{
			$stats['files'] = $lastLog['files'];
		}
		else
		{
			// Compute
			JPluginHelper::importPlugin( 'projects', 'files');
			$dispatcher =& JDispatcher::getInstance();
			$fTotal 	= $dispatcher->trigger( 'getStats', array($validProjects) );
			$fTotal 	= $fTotal[0];
			$fAverage 	= number_format($fTotal/count($validProjects), 0);
			$fUsage 	= $dispatcher->trigger( 'getStats', array($validProjects, 'usage') );
			$fUsage 	= $fUsage[0];
			$fDSpace 	= $dispatcher->trigger( 'getStats', array($validProjects, 'diskspace') );
			$fDSpace 	= $fDSpace[0];
			$fCommits 	= $dispatcher->trigger( 'getStats', array($validProjects, 'commitCount') );
			$fCommits 	= $fCommits[0];
			$pDSpace 	= $dispatcher->trigger( 'getStats', array($validProjects, 'pubspace') );
			$pDSpace 	= $pDSpace[0];

			$perc = round(($fUsage * 100)/$active) . '%';

			$stats['files'] = array(
				'total' => $fTotal,
				'average' => $fAverage,
				'usage' => $perc,
				'diskspace' => ProjectsHtml::formatSize($fDSpace),
				'commits' => $fCommits,
				'pubspace' => ProjectsHtml::formatSize($pDSpace)
			);
		}
				
		// Collect publication stats
		if ($publishing)
		{			
			$objP = new Publication( $this->_db );
			$objPV = new PublicationVersion( $this->_db );
			$prPub = $objP->getPubStats($validProjectIds, 'usage');
			$perc = round(($prPub * 100)/$total) . '%';
			
			$stats['pub'] = array(
				'total' => $objP->getPubStats($validProjectIds, 'total'),
				'average' => $objP->getPubStats($validProjectIds, 'average'),
				'usage' => $perc,
				'released' => $objP->getPubStats($validProjectIds, 'released'),
				'versions' => $objPV->getPubStats($validProjectIds)
			);
		}
		
		// Save weekly stats
		if ($saveLog)
		{
			$objStats = new ProjectStats($this->_db);
			$objStats->year 		= $thisYearNum;
			$objStats->month 		= $thisMonthNum;
			$objStats->week 		= $thisWeekNum;
			$objStats->processed 	= date('Y-m-d H:i:s');
			$objStats->stats 		= json_encode($stats);
			$objStats->store();
		}
		
		$stats['updated'] = $updated ? $updated : NULL;
				
		return $stats;
	}
	
	/**
	 * Get test project ids
	 * 
	 * @return     array
	 */
	public function getTestProjects()
	{
		$ids = array();
		
		$query  = "SELECT DISTINCT p.id ";
		$query .= "FROM $this->_tbl AS p ";
		$query .= "LEFT JOIN #__tags_object AS RTA ON p.id = RTA.objectid ";
		$query .= "LEFT JOIN #__tags AS TA ON RTA.tagid = TA.id AND RTA.tbl='projects' ";
		$query .= "WHERE TA.tag = 'test' ";
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if ($result) 
		{
			foreach ($result as $r) 
			{
				$ids[] = $r->id;
			}
		}
		
		return $ids;
	}
	
	/**
	 * Get non-test projects
	 * 
	 * @param      array $exclude
	 * @param      array $filters
	 * @param      array $config
	 * @param      boolean $count
	 * @return     mixed
	 */
	public function getValidProjects($exclude = array(), $filters = array(), $config = array(), $count = false, $get = '' )
	{		
		
		$setup_complete = !empty($config) && $config->get('confirm_step', 0) ? 3 : 2;
		
		$query  = $count ? "SELECT count(DISTINCT p.id) " : "SELECT DISTINCT p.* ";
		$query .= "FROM $this->_tbl AS p ";
		
		if (isset($filters['timed']) && isset($filters['active']))
		{
			$query .= " JOIN #__project_activity AS pa 
						ON pa.projectid=p.id AND pa.state != 2 ";
			$query .= "AND pa.recorded >= ' " . $filters['timed'] . " ' ";
		}
			
		$query .= "WHERE p.state != 2 AND p.provisioned = 0 ";
		
		if (isset($filters['created']))
		{
			$query .= "AND p.created LIKE '" . $filters['created'] . "%' ";
		}
		
		if (isset($filters['setup']) && $filters['setup'] == 1)
		{
			$query .= "AND p.setup_stage < $setup_complete ";
		}
		elseif (isset($filters['all']) && $filters['all'] == 1)
		{
			// all projects
		}
		else
		{
			$query .= "AND p.setup_stage >= $setup_complete ";
		}
		
		if (isset($filters['private']))
		{
			$query .= " AND p.private = " .$filters['private'];
		}
		
		if (isset($filters['sponsored']) && $filters['sponsored'] == 1)
		{
			$query .= " AND (p.params LIKE '%grant_status=1%') ";
		}
		
		if (isset($filters['sensitive']) && $filters['sensitive'] == 1)
		{
			$query .= " AND (p.params LIKE '%hipaa_data=yes%' ";
			$query .= " OR p.params LIKE '%ferpa_data=yes%' ";
			$query .= " OR p.params LIKE '%export_data=yes%' ";
			$query .= " OR p.params LIKE 'restricted_data=maybe%' ";
			$query .= " OR p.params LIKE '%followup=yes%') ";
		}
				
		if (!empty($exclude))
		{
			$query .= " AND p.id NOT IN ( ";

			$tquery = '';
			foreach ($exclude as $ex)
			{
				$tquery .= "'".$ex."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.") ";
		}
		
		if (isset($filters['timed']) && isset($filters['active']))
		{
			$query .= " GROUP BY p.id ";
		}
		
		$this->_db->setQuery( $query );
		if ($count)
		{
			return $this->_db->loadResult();
		}
		
		$results = $this->_db->loadObjectList();
		
		if ($get == 'alias' || $get == 'id')
		{
			$out = array();
			foreach ($results as $r)
			{
				$out[] = $get == 'alias' ? $r->alias : $r->id;
			}
			return $out;
		}	
		return $results;	
	}
	
	/**
	 * Get group projects
	 * 
	 * @param      integer $groupid
	 * @param      integer $uid
	 * @param      array $filters
	 * @param      integer $setup_complete
	 * @return     object
	 */
	public function getGroupProjects ( $groupid = 0, $uid = 0, $filters = array(), $setup_complete = 3 ) 
	{	
		$query  = "SELECT DISTINCT p.*, IFNULL(o.role, 0) as role, o.id as owner, o.added as since, o.status as confirmed ";
		$query .= ", x.name as authorname, g.cn as groupcn, g.description as groupname ";
		$query .= ", (SELECT COUNT(*) FROM #__project_activity AS pa WHERE pa.projectid=p.id 
					AND pa.recorded >= o.lastvisit AND o.lastvisit IS NOT NULL 
					AND o.id IS NOT NULL AND pa.state != 2 ) as newactivity ";
		$query .= " FROM #__project_owners as po, $this->_tbl AS p";
		$query .= " LEFT JOIN #__project_owners AS o ON o.projectid=p.id AND o.userid='$uid' 
					AND o.userid != 0 AND p.state!= 2 ";
		$query .=  " JOIN #__xprofiles as x ON x.uidNumber=p.created_by_user ";
		$query .=  " LEFT JOIN #__xgroups as g ON g.gidNumber=p.owned_by_group ";
		$query .=  " WHERE p.id=po.projectid AND p.state !=2 AND po.status=1 AND po.groupid=" . $groupid;
		
		$filters['which'] = isset($filters['which']) ? $filters['which'] : '';
		if ($filters['which'] == 'owned') 
		{	
			$query .= " AND p.owned_by_group = '$groupid' ";
		}
		else if ($filters['which'] == 'other') 
		{
			$query .= " AND p.owned_by_group != '$groupid' ";
		}
		
		$query .= $uid 
				? " AND (p.state = 1 OR  (o.userid='$uid' AND o.status!=2 
					AND ((p.state = 1 AND p.setup_stage = " . $setup_complete . ") 
					OR (o.role = 1 AND p.created_by_user='$uid')))) " 
				: " AND p.state = 1 ";
		
		// Sorting
		if (!isset($filters['count']) OR $filters['count'] == 0) 
		{
			$sort = '';
			$sortby  = isset($filters['sortby']) ? $filters['sortby'] : 'title';
			$sortdir = isset($filters['sortdir']) ? $filters['sortdir'] : 'ASC';	
			
			switch ($sortby) 
			{
				case 'role':		
					$sort .= 'o.role ' . $sortdir . ' ';        
					break;
					
				case 'status':		
					$sort .= 'p.setup_stage ' . $sortdir . ', p.state ' . $sortdir . ', p.title ASC' ;        
					break;
					
				case 'title':
				default: 			
					$sort .= 'p.title ' . $sortdir . ' ';
					break;
			}
			
			$query  .= " ORDER BY $sort ";
		}
		
		if (isset($filters['count']) && $filters['count'] == 1) 
		{
			$this->_db->setQuery( $query );
			return $this->_db->loadResult();
		}
		else if (isset($filters['limit']) && $filters['limit'] != 'all' && $filters['limit'] != 0) 
		{
			$filters['start'] = isset($filters['start']) ? $filters['start'] : 0;
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Get user project ids
	 * 
	 * @param      integer $uid
	 * @param      integer $active
	 * @param      integer $include_provisioned
	 * @return     array
	 */
	public function getUserProjectIds ( $uid = 0, $active = 1, $include_provisioned = 0 ) 
	{
		$ids = array();
		
		if ($uid) 
		{
			$query  = "SELECT DISTINCT p.id ";
			$query .= " FROM $this->_tbl AS p, #__project_owners as o ";
			$query .= " WHERE p.id=o.projectid ";
			$query .= $active == 1 
					? "AND (p.state=1 OR (o.role = 1 AND p.created_by_user='$uid' AND p.state !=2)) " 
					: "AND p.state !=2 ";
			$query .= $include_provisioned ? "" : " AND p.provisioned=0";
			$query .= " AND o.userid=" . $uid;
			$this->_db->setQuery( $query );
			$result = $this->_db->loadObjectList();
			if ($result) 
			{
				foreach ($result as $r) 
				{
					$ids[] = $r->id;
				}
			}
				
		}
		return $ids;
	}
	
	/**
	 * Get project ids for a group
	 * 
	 * @param      integer $groupid
	 * @param      integer $uid
	 * @param      integer $active
	 * @return     array
	 */
	public function getGroupProjectIds ( $groupid = 0, $uid = 0, $active = 1 ) 
	{
		$ids = array();
		
		if ($uid) 
		{
			$query  = "SELECT DISTINCT p.id ";
			$query .= " FROM #__project_owners as po, $this->_tbl AS p";
			$query .= " LEFT JOIN #__project_owners AS o ON o.projectid=p.id 
						AND o.userid='$uid' AND o.userid != 0  ";
			$query .= " WHERE p.id=po.projectid AND po.status=1 AND po.groupid=" . $groupid;
			$query .= $active == 1 
					? " AND (p.state=1 OR (o.role = 1 AND p.created_by_user='$uid' AND p.state !=2))  " 
					: " AND p.state !=2 ";
			$query .= " AND p.provisioned=0";
			$this->_db->setQuery( $query );
			$result = $this->_db->loadObjectList();
			if ($result) 
			{
				foreach ($result as $r) 
				{
					$ids[] = $r->id;
				}
			}
				
		}
		return $ids;
	}
	
	/**
	 * Get count of new activity since user last visit (multiple projects)
	 * 
	 * @param      array $projects
	 * @param      integer $uid
	 * @return     integer
	 */
	public function getUpdateCount ($projects = array(), $uid = 0) 
	{	
		if (!empty($projects) && $uid != 0) 
		{
			$query  = "SELECT COUNT(*) FROM #__project_activity AS pa ";
			$query .= " JOIN #__project_owners as o ON o.projectid=pa.projectid AND o.userid='$uid' ";
			$query .= " WHERE pa.recorded >= o.lastvisit AND o.lastvisit IS NOT NULL 
						AND pa.state !=2 AND pa.recorded >= o.added";
			$query .= " AND pa.projectid IN ( ";
			
			$tquery = '';
			foreach ($projects as $project)
			{
				$tquery .= "'".$project."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.") ";
			$this->_db->setQuery( $query );
			return $this->_db->loadResult();
		}
		else 
		{
			return 0;
		}
	}
	
	/**
	 * Match invite
	 * 
	 * @param      string $identifier
	 * @param      string $code
	 * @param      string $email
	 * @return     boolean
	 */
	public function matchInvite( $identifier = NULL, $code = '', $email = '' ) 
	{
		if ($identifier === NULL) 
		{
			return false;
		}
		if (!$code or !$email) 
		{
			return false;
		}
		$query  = "SELECT o.id as owner";
		$query .= " FROM $this->_tbl AS p ";
		$query .= " LEFT JOIN #__project_owners AS o ON o.projectid=p.id 
					AND o.userid=0 AND o.status != 2 AND o.invited_email=" . $this->_db->Quote( $email) . " 
					AND o.invited_code=" . $this->_db->Quote( $code );
		$query .= " WHERE ";
		$query .= is_numeric($identifier) ? ' p.id=' . $identifier : ' p.alias="' . $identifier . '"';
		$query .= " LIMIT 1 ";
	
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
		
	}
	
	/**
	 * Get project
	 * 
	 * @param      string $identifier
	 * @param      integer $uid
	 * @param      integer $pubid
	 * @return     mixed (array or false)
	 */	
	public function getProject( $identifier = NULL, $uid = 0, $pubid = 0 ) 
	{
		if ($identifier === NULL && !$pubid) 
		{
			return false;
		}
				
		$query  = "SELECT p.*, IFNULL(o.role, 0) as role, o.groupid as owner_group, 
				   o.id as owner, o.added as since, o.lastvisit, o.num_visits, 
				   o.params as owner_params, o.status as confirmed, ";
		$query .= " x.name, x.username, x.name as fullname ";
		$query .= " ,(SELECT t.type FROM #__project_types as t WHERE t.id=p.type) as projecttype ";
		
		if (intval($pubid) > 0)
		{
			$query .= " FROM #__publications AS pu JOIN $this->_tbl AS p ON pu.project_id=p.id ";	
		}
		else 
		{
			$query .= " FROM $this->_tbl AS p ";	
		}
		$query .= " LEFT JOIN #__project_owners AS o ON o.projectid=p.id ";
		$query .= " AND o.userid='$uid' AND p.state!= 2 AND o.userid != 0 AND o.status !=2"; 
		$query .=  " JOIN #__xprofiles as x ON x.uidNumber=p.created_by_user ";
		$query .= " WHERE ";
		
		if (intval($pubid) > 0)
		{
			$query .= " pu.id=".$pubid;	
		}
		else 
		{
			$query .= is_numeric($identifier) ? ' p.id=' . $identifier : ' p.alias="' . $identifier . '"';
		}
		$query .= " LIMIT 1";	
			
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : NULL;
	}
	
	/**
	 * Select item
	 * 
	 * @param      string $select
	 * @param      string $where
	 * @param      integer $limit
	 * @return     mixed (string or object)
	 */	
	public function selectWhere( $select, $where, $limit = 0 ) 
	{
		$query  = "SELECT $select FROM $this->_tbl WHERE $where";
		$query .= $limit ? " LIMIT 1 " : "";

		$this->_db->setQuery( $query );
		return $limit ? $this->_db->loadResult() : $this->_db->loadObjectList();
	}
	
	/**
	 * Get project alias from ID
	 * 
	 * @param      integer $id
	 * @return     string
	 */	
	public function getAlias( $id = 0 ) 
	{
		if (!$id) 
		{
			return false;
		}
		$query = "SELECT alias FROM $this->_tbl WHERE id=" . $id;
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Save project parameter
	 * 
	 * @param      integer $projectid
	 * @param      string $param
	 * @param      string $value
	 * @return     void
	 */	
	public function saveParam ( $projectid = NULL, $param = '', $value = 0 ) 
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		
		// Clean up value
		$value = preg_replace('/=/', '', $value);
		
		if ($this->loadProject($projectid)) 
		{
			if ($this->params) 
			{
				$params = explode("\n", $this->params);
				$in = '';
				$found = 0;
			
				// Change param
				if (!empty($params)) 
				{
					foreach ($params as $p) 
					{
						if (trim($p) != '' && trim($p) != '=') 
						{				
							$extracted = explode('=', $p);
							if (!empty($extracted)) 
							{
								$in .= $extracted[0] . '=';
								$default = isset($extracted[1]) ? $extracted[1] : 0;
								$in .= $extracted[0] == $param ? $value : $default;
								$in	.= n;
								if ($extracted[0] == $param) {
									$found = 1;
								}
							}
						}
					}
				}
				if (!$found) 
				{
					$in .= n . $param . '=' . $value;	
				}
			} 
			else 
			{
				$in = $param . '=' . $value;
			}
			$this->params = $in;
			$this->store();
		}		
	}

	/**
	 * Load a record and bind to $this
	 * 
	 * @param      string $identifier project id or alias name
	 * @return     object or false
	 */
	public function loadProject ( $identifier = NULL ) 
	{
		if ($identifier === NULL) 
		{
			return false;
		}
		$name = is_numeric($identifier) ? 'id' : 'alias';
		
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE $name='$identifier' LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind( $result );
		} 
		else 
		{
			return false;
		}
	}

	/**
	 * Check if name is unique
	 * 
	 * @param      string $name
	 * @param      integer $pid
	 * @return     boolean
	 */	
	public function checkUniqueName ( $name, $pid = 0 )
	{
		if ($name === NULL) {
			return false;
		}
		$query  =  "SELECT id FROM $this->_tbl WHERE alias='$name' ";
		$query .= $pid ? "AND id!=$pid" : "";
		$query .= " LIMIT 1 ";
		$this->_db->setQuery( $query );
		if ($this->_db->loadResult()) 
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Save setup stage
	 * 
	 * @param      integer $projectid
	 * @param      integer $stage
	 * @return     boolean
	 */	
	public function saveStage ( $projectid = NULL, $stage = 0 ) 
	{	
		if ($projectid === NULL) 
		{
			return false;
		}
		$query  = "SELECT * FROM $this->_tbl WHERE id='$projectid' LIMIT 1";
		$this->_db->setQuery( $query );	 
		
		if ($result = $this->_db->loadAssoc()) 
		{
			$this->bind( $result );
			$this->setup_stage = $stage;
			if (!$this->store()) 
			{
				$this->setError( JText::_('Failed to update setup stage.') );
				return false;
			}			
			return true;			
		}
	}
}
