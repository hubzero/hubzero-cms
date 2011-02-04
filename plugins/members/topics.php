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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_members_topics' );

//-----------

class plgMembersTopics extends JPlugin
{
	public function plgMembersTopics(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'topics' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------

	public function &onMembersContributionsAreas( $authorized )
	{
		$areas = array(
			'topics' => JText::_('PLG_MEMBERS_TOPICS')
		);
		return $areas;
	}

	//-----------

	public function onMembersContributionsCount( $authorized, $user_id='m.uidNumber', $username='m.username' ) 
	{
		//$query  = "SELECT COUNT(*) FROM #__wiki_page AS w WHERE (w.created_by='".$user_id."' OR w.authors LIKE '%".$username."%')";
		$username = ($username == 'm.username') ? $username : "'".$username."'";
		$query = "SELECT COUNT(*) FROM #__wiki_page AS w 
					WHERE (CASE WHEN ".$user_id.">0 THEN (w.authors LIKE CONCAT('%',".$username.",'%') OR w.created_by=".$user_id.") ELSE w.created_by=".$user_id." END)";
		if (!$authorized) {
			$query .= " AND w.access!=1";
		}
		/*$query = "SELECT COUNT(*) FROM (
			SELECT COUNT(DISTINCT v.pageid) FROM #__wiki_page AS w, #__wiki_version AS v 
			WHERE w.id=v.pageid 
			AND v.approved=1 
			AND (w.created_by=m.uidNumber OR w.authors LIKE '%m.username%') ";
		if (!$authorized) {
			$query .= " AND w.access!=1";
		}
		$query .= " GROUP BY pageid 
		) AS f";*/
		return $query;
	}

	//-----------

	public function onMembersContributions( $member, $option, $authorized, $limit=0, $limitstart=0, $sort, $areas=null )
	{
		$database =& JFactory::getDBO();

		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onMembersContributionsAreas( $authorized ) ) && !array_intersect( $areas, array_keys( $this->onMembersContributionsAreas( $authorized ) ) )) {
				return array();
			}
		}

		// Do we have a member ID?
		if (get_class($member) == 'Hubzero_User_Profile') {
			if (!$member->get('uidNumber')) {
				return array();
			} else {
				$uidNumber = $member->get('uidNumber');
				$username = $member->get('username');
			}
		} else {
			if (!$member->uidNumber) {
				return array();
			} else {
				$uidNumber = $member->uidNumber;
				$username = $member->username;
			}
		}

		include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'page.php');
		
		// Instantiate some needed objects
		$wp = new WikiPage( $database );
		
		// Build query
		$filters = array();
		$filters['author'] = $uidNumber;
		$filters['username'] = $username;
		$filters['sortby'] = $sort;
		if ($authorized) {
			$filters['authorized'] = 'admin';
		}

		if (!$limit) {
			$filters['select'] = 'count';

			$database->setQuery( $wp->buildPluginQuery( $filters ) );
			return $database->loadResult();
		} else {
			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;
			
			$database->setQuery( $wp->buildPluginQuery( $filters ) );
			$rows = $database->loadObjectList();

			if ($rows) {
				ximport('Hubzero_View_Helper_Html');
				
				foreach ($rows as $key => $row) 
				{
					if ($row->area != '' && $row->category != '') {
						$rows[$key]->href = JRoute::_('index.php?option=com_groups&scope='.$row->category.'&pagename='.$row->alias);
					} else {
						$rows[$key]->href = JRoute::_('index.php?option=com_topics&scope='.$row->category.'&pagename='.$row->alias);
					}
					$rows[$key]->text = $rows[$key]->itext;
				}
			}

			return $rows;
		}
	}
	
	//-----------
	
	public function out( $row, $authorized=false ) 
	{
		$database =& JFactory::getDBO();

		$html  = "\t".'<li class="resource">'."\n";
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		$html .= "\t\t".'<p class="details">';
		if ($row->area != '' && $row->category != '') {
			$html .= JText::_('PLG_MEMBERS_TOPICS_GROUP_WIKI').': '.$row->area;
		} else {
			$html .= JText::_('PLG_MEMBERS_TOPICS');
		}
		$html .= '</p>'."\n";
		if ($row->text) {
			if ($row->access == 1 && !$authorized) {
				$html .= "\t\t".Hubzero_View_Helper_Html::warning(JText::_('PLG_MEMBERS_TOPICS_NOT_AUTHORIZED'))."\n";
			} else {
				$html .= "\t\t".Hubzero_View_Helper_Html::shortenText(stripslashes($row->text))."\n";
			}
		}
		$html .= "\t".'</li>'."\n";
		return $html;
	}
	
	//-----------
	
	public function &onMembersFavoritesAreas( $authorized )
	{
		return $this->onMembersContributionsAreas( $authorized );
	}

	//-----------

	public function onMembersFavorites( $member, $option, $authorized, $limit=0, $limitstart=0, $areas=null )
	{
		$database =& JFactory::getDBO();

		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onMembersFavoritesAreas( $authorized ) ) && !array_intersect( $areas, array_keys( $this->onMembersContributionsAreas( $authorized ) ) )) {
				return array();
			}
		}

		// Do we have a member ID?
		if (get_class($member) == 'Hubzero_User_Profile') {
			if (!$member->get('uidNumber')) {
				return array();
			} else {
				$uidNumber = $member->get('uidNumber');
				$username = $member->get('username');
			}
		} else {
			if (!$member->get('uidNumber')) {
				return array();
			} else {
				$uidNumber = $member->get('uidNumber');
				$username = $member->get('username');
			}
		}

		$access = " AND w.access!=1";
		if ($authorized) {
			$access = "";
		}

		$f_count = "SELECT COUNT(*) ";
		$f_fields = "SELECT f.id, f.pagetext AS text, 'topics' AS section, 'index.php?option=' AS href, d.scope, d.group, d.access, d.title, d.pagename, d.created ";
		$f_from = "FROM #__wiki_version AS f, 
						(
							SELECT v.pageid, v.created, w.title, w.pagename, w.scope, w.group, w.access, MAX(v.version) AS version
							FROM #__wiki_page AS w, #__wiki_version AS v, #__xfavorites AS x
							WHERE w.id=v.pageid AND v.approved=1 AND x.uid='".$uidNumber."' AND w.id=x.oid AND x.tbl='topics' $access
							GROUP BY pageid
						) AS d
					WHERE f.version=d.version 
					AND f.pageid=d.pageid ";
		$order_by = "ORDER BY id DESC, title LIMIT $limitstart,$limit";

		if (!$limit) {
			//echo $f_count . $f_from;
			$database->setQuery( $f_count . $f_from );
			return $database->loadResult();
		} else {
			$database->setQuery( $f_fields . $f_from . $order_by );
			$rows = $database->loadObjectList();

			if ($rows) {
				ximport('Hubzero_View_Helper_Html');
				
				foreach ($rows as $key => $row) 
				{
					if ($row->group != '' && $row->scope != '') {
						$rows[$key]->href = JRoute::_('index.php?option=com_groups&scope='.$row->scope.'&pagename='.$row->pagename);
					} else {
						$rows[$key]->href = JRoute::_('index.php?option=com_topics&scope='.$row->scope.'&pagename='.$row->pagename);
					}
				}
			}

			return $rows;
		}
	}
}