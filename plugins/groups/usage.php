<?php
/**
 * @package     HUBzero CMS
 * @author      Christopher <csmoak@purdue.edu>
 * @copyright   Copyright 2005-2011 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
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
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_groups_usage' );

/**
 * Short description for 'plgGroupsUsage'
 * 
 * Long description (if any) ...
 */
class plgGroupsUsage extends JPlugin
{

	/**
	 * Short description for 'plgGroupsUsage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgGroupsUsage(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'usage' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'onGroupAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name' => 'usage',
			'title' => JText::_('USAGE'),
			'default_access' => $this->_params->get('plugin_access','members'),
			'display_menu_tab' => true

		);

		return $area;
	}

	/**
	 * Short description for 'onGroup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $group Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      string $authorized Parameter description (if any) ...
	 * @param      integer $limit Parameter description (if any) ...
	 * @param      integer $limitstart Parameter description (if any) ...
	 * @param      string $action Parameter description (if any) ...
	 * @param      array $access Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null )
	{
		$return = 'html';
		$active = 'usage';

		// The output array we're returning
		$arr = array(
			'html'=>''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if(!in_array($this_area['name'],$areas)) {
				$return = '';
			}
		}

		if ($return == 'html') {

			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//Create user object
			$juser =& JFactory::getUser();

			//get the group members
			$members = $group->get('members');

			//if set to nobody make sure cant access
			if($group_plugin_acl == 'nobody') {
				$arr['html'] = "<p class=\"info\">".JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active))."</p>";
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if ($juser->get('guest') && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members')) {
				ximport('Hubzero_Module_Helper');
				$arr['html']  = "<p class=\"warning\">".JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active))."</p>";
				$arr['html'] .= Hubzero_Module_Helper::renderModules('force_mod');
				return $arr;
			}

			//check to see if user is member and plugin access requires members
			if(!in_array($juser->get('id'),$members) && $group_plugin_acl == 'members' && $authorized != 'admin') {
				$arr['html'] = "<p class=\"info\">".JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active))."</p>";
				return $arr;
			}

			//instantiate the db
			$database =& JFactory::getDBO();

			//reference group for other functions
			$this->group = $group;

			//import the hubzero document library
			ximport('Hubzero_Document');

			//get the joomla document
			$doc =& JFactory::getDocument();

			//add usage stylesheet to view
			Hubzero_Document::addPluginStylesheet('groups', 'usage');

			//add datepicker stylesheet to view
			$doc->addStyleSheet('plugins'.DS.'groups'.DS.'usage'.DS.'datepicker.css');

			//add google js-api
			$doc->addScript('https://www.google.com/jsapi');

			//add jquery from google cdn
			$doc->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js');

			//add usage custom script
			$doc->addScript('plugins'.DS.'groups'.DS.'usage'.DS.'usage.js');

			//add datepicker script
			$doc->addScript('plugins'.DS.'groups'.DS.'usage'.DS.'datepicker.js');

			//get the page id if we want to view stats on a specific page
			$pid = JRequest::getVar('pid', '');

			//get start and end dates
			$start = JRequest::getVar('start', date("Y-m-d 00:00:00",strtotime('-30 DAYS')));
			$end = JRequest::getVar('end', date("Y-m-d 23:59:59"));

			//make sure start date is a full php datetime
			if(strlen($start) != 19) {
				$start .= " 00:00:00";
			}

			//make sure end date is a full php datetime
			if(strlen($end) != 19) {
				$end .= " 23:59:59";
			}

			//generate script to draw chart and push to the page
			$script = $this->drawChart($pid, $start, $end);
			$doc->addScriptDeclaration($script);

			//import and create view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'groups',
					'element'=>'usage',
					'name'=>'index'
				)
			);

			//get the group pages
			$query = "SELECT id, title FROM #__xgroups_pages WHERE gid=".$group->get('gidNumber');
			$database->setQuery($query);
			$view->pages = $database->loadAssocList();

			$view->option = $option;
			$view->group = $group;
			$view->authorized = $authorized;
			$view->database = $database;

			$view->pid = $pid;
			$view->start = $start;
			$view->end = $end;

			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Short description for 'getResourcesCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $gid Parameter description (if any) ...
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getResourcesCount( $gid=NULL, $authorized )
	{
		if (!$gid) {
			return 0;
		}
		$database =& JFactory::getDBO();

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'resource.php' );
		$rr = new ResourcesResource( $database );

		$database->setQuery( "SELECT COUNT(*) FROM ".$rr->getTableName()." AS r WHERE r.group_owner='".$gid."'" );
		return $database->loadResult();
	}

	/**
	 * Short description for 'getWikipageCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $gid Parameter description (if any) ...
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getWikipageCount( $gid=NULL, $authorized )
	{
		if (!$gid) {
			return 0;
		}
		$database =& JFactory::getDBO();

		$database->setQuery( "SELECT COUNT(*) FROM #__wiki_page AS p WHERE p.scope='".$gid.DS.'wiki'."' AND p.group='".$gid."'" );
		return $database->loadResult();
	}

	/**
	 * Short description for 'getWikifileCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $gid Parameter description (if any) ...
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getWikifileCount( $gid=NULL, $authorized )
	{
		if (!$gid) {
			return 0;
		}
		$database =& JFactory::getDBO();

		$database->setQuery( "SELECT id FROM #__wiki_page AS p WHERE p.scope='".$gid.DS.'wiki'."' AND p.group='".$gid."'" );
		$pageids = $database->loadObjectList();
		if ($pageids) {
			$ids = array();
			foreach ($pageids as $pageid)
			{
				$ids[] = $pageid->id;
			}

			$database->setQuery( "SELECT COUNT(*) FROM #__wiki_attachments WHERE pageid IN (".implode(',', $ids).")" );
			return $database->loadResult();
		} else {
			return 0;
		}
	}

	/**
	 * Short description for 'getForumCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $gid Parameter description (if any) ...
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @param      string $state Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getForumCount( $gid=NULL, $authorized, $state='' )
	{
		if (!$gid) {
			return 0;
		}
		$database =& JFactory::getDBO();

		include_once(JPATH_ROOT.DS.'components'.DS.'com_forum'.DS.'tables'.DS.'post.php');

		$filters = array();
		$filters['authorized'] = $authorized;
		switch ($state)
		{
			case 'sticky':
				$filters['sticky'] = 1;
			break;
			case 'closed':
				$filters['state'] = 2;
			break;
			case 'open':
			default:
				$filters['state'] = 1;
			break;
		}
		$filters['start'] = 0;
		$filters['group'] = $gid;

		$forum = new ForumPost( $database );
		return $forum->getCount( $filters );
	}

	/**
	 * Short description for 'getGroupPagesCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $gid Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function getGroupPagesCount($gid)
	{
		if (!$gid) {
			return 0;
		}

		$database =& JFactory::getDBO();

		include_once( JPATH_ROOT.DS.'components'.DS.'com_groups'.DS.'tables'.DS.'pages.php' );

		$gp = new GroupPages( $database );

		$pages = $gp->getPages($gid);

		return count($pages);
	}

	/**
	 * Short description for 'getGroupPageViews'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $gid Parameter description (if any) ...
	 * @param      string $pageid Parameter description (if any) ...
	 * @param      unknown $start Parameter description (if any) ...
	 * @param      unknown $end Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getGroupPageViews( $gid, $pageid = null, $start, $end )
	{
		$database =& JFactory::getDBO();

		if($pageid != '') {
			$query = "SELECT * FROM #__xgroups_pages_hits WHERE gid=".$gid." AND datetime > '{$start}' AND datetime < '{$end}' AND pid={$pageid} ORDER BY datetime ASC";
		} else {
			$query = "SELECT * FROM #__xgroups_pages_hits WHERE gid=".$gid." AND datetime > '{$start}' AND datetime < '{$end}' ORDER BY datetime ASC";
		}
		$database->setQuery($query);
		$views = $database->loadAssocList();

		$s = strtotime($start);
		$e = strtotime($end);
		$diff = round(($e-$s)/60/60/24);

		$page_views = array();

		for($i=0; $i < $diff; $i++) {
			$count = 0;
			$date = date("M d, Y", strtotime("-{$i} DAYS", strtotime($end)));

			$day_s = date("Y-m-d 00:00:00", strtotime("-{$i} DAYS", strtotime($end)));
			$day_e = date("Y-m-d 23:59:59", strtotime("-{$i} DAYS", strtotime($end)));

			foreach($views as $view) {
				$t = $view['datetime'];

				if($t >= $day_s && $t <= $day_e) {
					$count++;
				}
			}

			$page_views[$date] = $count;
		}

		return array_reverse($page_views);
	}

	/**
	 * Short description for 'drawChart'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $pid Parameter description (if any) ...
	 * @param      unknown $start Parameter description (if any) ...
	 * @param      unknown $end Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function drawChart($pid, $start, $end)
	{
		//vars used 
		$jsObj = "";
		$script = "";

		//num pages views
		$page_views = $this->getGroupPageViews($this->group->get('gidNumber'), $pid, $start, $end);
		$total = count($page_views);
		$count = 0;
		foreach($page_views as $d => $c) {
			$count++;
			$jsObj .= "[\"{$d}\", {$c}]";
			if($count < $total) {
				$jsObj .= ", \n \t\t\t\t";
			}
		}

		$script = "
			google.load(\"visualization\", \"1\", {
				packages:[\"corechart\"]
			});

			google.setOnLoadCallback(drawChart);

			function drawChart() {
				var data = new google.visualization.DataTable();
		       	data.addColumn('string', 'Day');
		       	data.addColumn('number', 'Views');
		       	data.addRows([
		         	{$jsObj}
				]);

				var options = {
					height: 240,
					legend: 'none',
					pointSize: 4,
					axisTitlesPosition: 'none',
					hAxis: {
						textPosition:'out',
						slantedText: false,
						slantedTextAngle: 0,
						showTextEvery: 2,
						textStyle: {
							fontSize:10
						}
					},
					vAxis: {
						minValue: 10,
						textPosition: 'out'
					},
					chartArea: {
						width: \"90%\"
					}
				}

		       	var chart = new google.visualization.AreaChart(document.getElementById('page_views_chart'));
		       	chart.draw(data, options);
			}";

		return $script;
	}

	/**
	 * Short description for 'getGroupBlogCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $gid Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function getGroupBlogCount($gid)
	{
		if (!$gid) {
			return 0;
		}

		$database =& JFactory::getDBO();

		include_once(JPATH_ROOT.DS.'components'.DS.'com_blog'.DS.'tables'.DS.'blog.entry.php');

		$filters = array();
		$filters['scope'] = 'group';
		$filters['group_id'] = $gid;

		$gb = new BlogEntry($database);

		$total = $gb->getCount($filters);

		return $total;
	}

	/**
	 * Short description for 'getGroupBlogCommentCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $gid Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function getGroupBlogCommentCount( $gid )
	{
		if (!$gid) {
			return 0;
		}

		$database =& JFactory::getDBO();

		$query = "SELECT count(*) FROM jos_blog_entries as be, jos_blog_comments as bc WHERE be.scope='group' AND be.group_id=".$gid." AND be.id=bc.entry_id";
		$database->setQuery($query);

		$count = $database->loadResult();
		return $count;
	}

	/**
	 * Short description for 'getGroupCalendarCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $gid Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getGroupCalendarCount( $gid )
	{
		$database =& JFactory::getDBO();

		$query = "SELECT COUNT(*) FROM jos_xgroups_events WHERE gidNumber=".$gid;
		$database->setQuery($query);

		$count = $database->loadResult();
		return $count;
	}

}
