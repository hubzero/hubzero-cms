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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Members Plugin class for usage
 */
class plgMembersUsage extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Event call to determine if this plugin should return data
	 * 
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @return     array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		$areas['usage'] = JText::_('PLG_MEMBERS_USAGE');
		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 * 
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @param      string  $option Component name
	 * @param      string  $areas  Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;

		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member)))) 
			{
				$returnhtml = false;
			}
		}

		$database =& JFactory::getDBO();
		$tables = $database->getTableList();
		$table = $database->getPrefix() . 'author_stats';

		if (!in_array($table,$tables)) 
		{
			ximport('Hubzero_View_Helper_Html');
			$arr['html'] = Hubzero_View_Helper_Html::error(JText::_('USAGE_ERROR_MISSING_TABLE'));
			$arr['metadata'] = '<p class="usage"><a href="' . JRoute::_('index.php?option=' . $option . '&id='.$member->get('uidNumber') . '&active=usage') . '">' . JText::_('PLG_MEMBERS_USAGE_DETAILED_USAGE') . '</a></p>' . "\n";
			return $arr;
		}

		$html = '';
		if ($returnhtml) 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addComponentStylesheet('com_usage');

			//$sort = JRequest::getVar('sort','');

			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => 'usage',
					'name'    => 'summary'
				)
			);

			$view->member = $member;
			$view->option = $option;
			$view->contribution = $this->first_last_contribution($member->get('uidNumber'));
			$view->rank = $this->get_rank($member->get('uidNumber'));

			$view->total_tool_users = $this->get_total_stats($member->get('uidNumber'), 'tool_users',14);
			$view->total_andmore_users = $this->get_total_stats($member->get('uidNumber'), 'andmore_users',14);
			$view->citation_count = $this->get_citationcount(null, $member->get('uidNumber'));
			$cluster = $this->get_classroom_usage($member->get('uidNumber'));
			$view->cluster_classes = $cluster['classes'];
			$view->cluster_users = $cluster['users'];
			$view->cluster_schools = $cluster['schools'];
			
			$sql = 'SELECT DISTINCT r.id, r.title, DATE_FORMAT(r.publish_up, "%d %b %Y") AS publish_up, rt.type FROM #__resources AS r LEFT JOIN #__resource_types AS rt ON r.TYPE=rt.id LEFT JOIN #__author_assoc AS aa ON aa.subid=r.id AND aa.subtable="resources" WHERE r.standalone=1 AND r.published=1 AND r.type=7 AND (aa.authorid="'.$member->get("uidNumber").'") AND (r.access=0 OR r.access=3) ORDER BY r.publish_up DESC';

			$database->setQuery($sql);
			$view->tool_stats = $database->loadObjectList();
			$view->tool_total_12 = $this->get_total_stats($member->get('uidNumber'), 'tool_users', 12);
			$view->tool_total_14 = $this->get_total_stats($member->get('uidNumber'), 'tool_users', 14);

			$sql = 'SELECT DISTINCT r.id, r.title, DATE_FORMAT(r.publish_up, "%d %b %Y") AS publish_up, rt.type FROM #__resources AS r LEFT JOIN #__resource_types AS rt ON r.TYPE=rt.id LEFT JOIN #__author_assoc AS aa ON aa.subid=r.id AND aa.subtable="resources" WHERE r.standalone=1 AND r.published=1 AND r.type<>7 AND (aa.authorid="'.$member->get("uidNumber").'") AND (r.access=0 OR r.access=3) ORDER BY r.publish_up DESC';

			$database->setQuery($sql);
			$view->andmore_stats = $database->loadObjectList();
			$view->andmore_total_12 = $this->get_total_stats($member->get('uidNumber'), 'andmore_users', 12);
			$view->andmore_total_14 = $this->get_total_stats($member->get('uidNumber'), 'andmore_users', 14);

			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}

			$arr['html'] = $view->loadTemplate();
		}

		//$arr['metadata'] = '<p class="usage"><a href="'.JRoute::_('index.php?option='.$option.'&id='.$member->get('uidNumber').'&active=usage').'">'.JText::_('PLG_MEMBERS_USAGE_DETAILED_USAGE').'</a></p>'."\n";
		//if (is_file(JPATH_ROOT . DS . 'site/stats/contributor_impact/impact_'.$this->uid($member->get('uidNumber')).'_th.gif')) {
		//	$arr['metadata'] .= '<p><a rel="lightbox" href="/site/stats/contributor_impact/impact_'.$this->uid($member->get('uidNumber')).'.gif"><img src="/site/stats/contributor_impact/impact_'.$this->uid($member->get('uidNumber')).'_th.gif" alt="'.JText::_('PLG_MEMBERS_USAGE_IMPACT_PLOT').'" /></a></p>'."\n";
		//}

		$arr['metadata'] = "";

		return $arr;
	}

	/**
	 * Convert negative IDs to n IDS (-15 -> n15)
	 * 
	 * @param      number $uid User ID
	 * @return     mixed
	 */
	public function uid($uid)
	{
		if ($uid < 0) 
		{
			return 'n' . -$uid;
		} 
		return $uid;
	}

	/**
	 * Get contributions
	 * 
	 * @param      string $authorid User ID
	 * @return     array
	 */
	public function first_last_contribution($authorid)
	{
		$database =& JFactory::getDBO();

		$sql = 'SELECT COUNT(DISTINCT aa.subid) as contribs, DATE_FORMAT(MIN(res.publish_up), "%d %b %Y") AS first_contrib, DATE_FORMAT(MAX(res.publish_up), "%d %b %Y") AS last_contrib FROM #__resources AS res, #__author_assoc AS aa, #__resource_types AS restypes WHERE res.id = aa.subid AND res.type = restypes.id AND aa.authorid = "' . $authorid . '" AND res.standalone = 1 AND res.published = 1 AND (res.access=0 OR res.access=3) AND aa.subtable = "resources"';

		$database->setQuery($sql);
		$results = $database->loadObjectList();

		$contribution = array();
		$contribution['contribs'] = '';
		$contribution['first']    = '';
		$contribution['last']     = '';

		if ($results) 
		{
			foreach ($results as $row)
			{
				$contribution['contribs'] = $row->contribs;
				$contribution['first']    = $row->first_contrib;
				$contribution['last']     = $row->last_contrib;
	        }
		}

		return $contribution;
	}

	/**
	 * Get simulation count
	 * 
	 * @param      integer $resid  Resource ID
	 * @param      string  $period Time period to find data for
	 * @return     integer
	 */
	public function get_simcount($resid, $period)
	{
		$database =& JFactory::getDBO();

		$sql = 'SELECT jobs FROM #__resource_stats_tools WHERE resid="' . $resid . '" AND period="' . $period . '" ORDER BY datetime DESC LIMIT 1';

		$database->setQuery($sql);
		$result = $database->loadResult();
		if ($result) 
		{
			return $result;
		}

		return 0;
	}

	/**
	 * Get a count of users
	 * 
	 * @param      string $resid   Resource ID
	 * @param      string $period  Time period
	 * @param      string $restype Resource type
	 * @return     string
	 */
	public function get_usercount($resid, $period, $restype='0')
	{
		$database =& JFactory::getDBO();

		if ($restype == '7') 
		{
			$table = "#__resource_stats_tools";
		} 
		else 
		{
			$table = "#__resource_stats";
		}

		$data = '-';
		$sql = 'SELECT MAX(datetime), users FROM ' . $table . ' WHERE resid = "' . $resid . '" AND period = "' . $period . '" GROUP BY datetime ORDER BY datetime DESC LIMIT 1';

		$database->setQuery($sql);
		$results = $database->loadObjectList();
		if ($results) 
		{
			foreach ($results as $row)
			{
				$data = $row->users;
			}
		}

		return $data;
	}

	/**
	 * Get classroom usage
	 * 
	 * @param      mixed  $authorid User ID
	 * @return     array
	 */
	public function get_classroom_usage($authorid) 
	{
		$database =& JFactory::getDBO();
	
		$cluster['classes'] = 0;
		$cluster['users']   = 0;
		$cluster['schools'] = 0;

		$sql = 'SELECT classes FROM `#__metrics_author_cluster` WHERE authorid = "' . $authorid . '"';
		$database->setQuery($sql);
		$result = $database->loadResult();
		if ($result) 
		{
			$cluster['classes'] = $result;
		}

		$sql = 'SELECT users FROM `#__metrics_author_cluster` WHERE authorid = "' . $authorid . '"';
		$database->setQuery($sql);
		$result = $database->loadResult();
		if ($result) 
		{
			$cluster['users'] = $result;
		}

		$sql = 'SELECT schools FROM `#__metrics_author_cluster` WHERE authorid = "' . $authorid . '"';
		$database->setQuery($sql);
		$result = $database->loadResult();
		if ($result) 
		{
			$cluster['schools'] = $result;
		}
		return $cluster;

	}

	/**
	 * Get a count of citations
	 * 
	 * @param      string $resid    Resource ID
	 * @param      mixed  $authorid User ID
	 * @return     string
	 */
	public function get_citationcount($resid, $authorid=0)
	{
		$database =& JFactory::getDBO();

		if ($authorid) 
		{
			$sql = 'SELECT COUNT(DISTINCT (c.id)) 
			FROM #__citations c, #__citations_assoc ca, #__author_assoc aa, #__resources r 
					WHERE c.id = ca.cid AND r.id = ca.oid AND r.id = aa.subid AND  aa.subtable = "resources" AND ca.tbl = "resource" AND r.published=1 
					AND r.standalone=1 AND aa.authorid = "' . $authorid . '"';
		} 
		else 
		{
			$sql = 'SELECT COUNT(DISTINCT (c.id)) AS citations 
					FROM #__resources r, #__citations c, #__citations_assoc ca 
					WHERE r.id = ca.oid AND ca.cid = c.id AND ca.tbl = "resource" AND standalone=1 AND r.id = "' . $resid . '"';
		}

		$database->setQuery($sql);
		$result = $database->loadResult();
		if ($result) 
		{
			return $result;
		}
		return '-';
	}

	/**
	 * Get the user's rank
	 * 
	 * @param      integer $authorid User ID
	 * @return     string
	 */
	public function get_rank($authorid)
	{
		$database =& JFactory::getDBO();

		$rank = 0;
		$i = 1;
		$sql = 'SELECT a.uidNumber AS aid, COUNT(DISTINCT aa.subid) AS contribs 
				FROM #__xprofiles a, #__resources res, #__author_assoc aa 
				WHERE a.uidNumber = aa.authorid AND res.id = aa.subid AND res.published=1 AND (res.access=0 OR res.access=3) AND aa.subtable = "resources" 
				AND res.standalone=1 GROUP BY aid ORDER BY contribs DESC';

		$database->setQuery($sql);
		$results = $database->loadObjectList();

		if ($results) 
		{
			foreach ($results as $row)
			{
				if ($row->aid == $authorid) 
				{
					$rank = $i;
				}
				$i++;
	    	}
		}

		if ($rank) 
		{
			$sql = 'SELECT COUNT(DISTINCT a.uidNumber) as authors 
				FROM #__xprofiles a, #__author_assoc aa, #__resources res 
				WHERE a.uidNumber=aa.authorid AND aa.subid=res.id AND aa.subtable="resources" AND res.published=1 AND (res.access=0 OR res.access=3) 
				AND res.standalone=1';

			$database->setQuery($sql);
			$total_authors = $database->loadResult();

			$rank = $rank . ' / ' . $total_authors;
		} 
		else 
		{
			$rank = '-';
		}
		return $rank;
	}

	/**
	 * Get total stats for a user
	 * 
	 * @param      string $authorid  User ID
	 * @param      string $user_type User type
	 * @param      string $period    Time period
	 * @return     integer
	 */
	public function get_total_stats($authorid, $user_type, $period)
	{
		$database =& JFactory::getDBO();

		$sql = 'SELECT ' . $user_type . ' FROM #__author_stats WHERE authorid = "' . $authorid . '" AND period = "' . $period . '" ORDER BY datetime DESC LIMIT 1';

		$database->setQuery($sql);
		return $database->loadResult();
	}
}

