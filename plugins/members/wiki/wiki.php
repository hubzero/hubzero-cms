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
 * Members Plugin class for wiki pages
 */
class plgMembersWiki extends JPlugin
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
	 * Return a list of categories
	 * 
	 * @return     array
	 */
	public function &onMembersContributionsAreas()
	{
		$areas = array(
			'wiki' => JText::_('PLG_MEMBERS_WIKI')
		);
		return $areas;
	}

	/**
	 * Build SQL for returning the count of the number of contributions
	 * 
	 * @param      string $user_id  Field to join on user ID
	 * @param      string $username Field to join on username
	 * @return     string
	 */
	public function onMembersContributionsCount($user_id='m.uidNumber', $username='m.username')
	{
		//$query  = "SELECT COUNT(*) FROM #__wiki_page AS w WHERE (w.created_by='".$user_id."' OR w.authors LIKE '%".$username."%')";
		$username = ($username == 'm.username') ? $username : "'" . $username . "'";
		$query = "SELECT COUNT(*) FROM #__wiki_page AS w
					WHERE ((" . $user_id . " > 0 AND (w.created_by = " . $user_id . " OR " . $user_id . " IN (SELECT wpa.user_id FROM #__wiki_page_author AS wpa 
						WHERE wpa.page_id=w.id))) OR (" . $user_id . " <= 0 AND w.created_by = " . $user_id . "))";
		//if (!$authorized) {
		//	$query .= " AND w.access!=1";
		//}
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

	/**
	 * Return either a count or an array of the member's contributions
	 * 
	 * @param      object  $member     Current member
	 * @param      string  $option     Component name
	 * @param      string  $authorized Authorization level
	 * @param      integer $limit      Number of record to return
	 * @param      integer $limitstart Record return start
	 * @param      string  $sort       Field to sort records on
	 * @param      array   $areas      Areas to return data for
	 * @return     array
	 */
	public function onMembersContributions($member, $option, $limit=0, $limitstart=0, $sort, $areas=null)
	{
		$database = JFactory::getDBO();

		if (is_array($areas) && $limit) 
		{
			if (!isset($areas[$this->_name]) 
			  && !in_array($this->_name, $areas) 
			  && !array_intersect($areas, array_keys($this->onMembersContributionsAreas())))
			{
				return array();
			}
		}

		// Do we have a member ID?
		if ($member instanceof \Hubzero\User\Profile) 
		{
			if (!$member->get('uidNumber')) 
			{
				return array();
			} 
			else 
			{
				$uidNumber = $member->get('uidNumber');
				$username = $member->get('username');
			}
		} 
		else 
		{
			if (!$member->uidNumber) 
			{
				return array();
			} 
			else 
			{
				$uidNumber = $member->uidNumber;
				$username = $member->username;
			}
		}

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');

		// Instantiate some needed objects
		$wp = new WikiTablePage($database);

		// Build query
		$filters = array();
		$filters['author'] = $uidNumber;
		$filters['username'] = $username;
		$filters['sortby'] = $sort;
		//if ($authorized) {
		//	$filters['authorized'] = 'admin';
		//}

		if (!$limit) 
		{
			$filters['select'] = 'count';

			$database->setQuery($wp->buildPluginQuery($filters));
			return $database->loadResult();
		} 
		else 
		{
			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;

			$database->setQuery($wp->buildPluginQuery($filters));
			$rows = $database->loadObjectList();

			if ($rows) 
			{
				foreach ($rows as $key => $row)
				{
					if ($row->area != '' && $row->category != '') 
					{
						$rows[$key]->href = JRoute::_('index.php?option=com_groups&scope=' . $row->category . '&pagename=' . $row->alias);
					} 
					else 
					{
						$rows[$key]->href = JRoute::_('index.php?option=com_wiki&scope=' . $row->category . '&pagename=' . $row->alias);
					}
					$rows[$key]->text = $rows[$key]->itext;
				}
			}

			return $rows;
		}
	}

	/**
	 * Static method for formatting results
	 * 
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public static function out($row)
	{
		$database = JFactory::getDBO();

		$html  = "\t" . '<li class="resource">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		$html .= "\t\t" . '<p class="details">';
		if (isset($row->area) && isset($row->category)) 
		{
			$html .= JText::_('PLG_MEMBERS_WIKI_GROUP_WIKI') . ': ' . $row->area;
		} 
		else 
		{
			$html .= JText::_('PLG_MEMBERS_WIKI');
		}
		$html .= '</p>' . "\n";
		if ($row->text) 
		{
			//if ($row->access == 1) {
			//	$html .= "\t\t".'<p class="warning">' . JText::_('PLG_MEMBERS_TOPICS_NOT_AUTHORIZED') . '</p>' ."\n";
			//} else {
				$html .= "\t\t<p>" . \Hubzero\Utility\String::truncate(strip_tags(stripslashes($row->text)), 300) . "</p>\n";
			//}
		}
		$html .= "\t" . '</li>' . "\n";
		return $html;
	}

	/**
	 * Return resource categories
	 * 
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @return     array
	 */
	public function &onMembersFavoritesAreas()
	{
		return $this->onMembersContributionsAreas();
	}

	/**
	 * Return either a count or an array of the member's favorites
	 * 
	 * @param      object  $member     Current member
	 * @param      string  $option     Component name
	 * @param      integer $limit      Number of record to return
	 * @param      integer $limitstart Record return start
	 * @param      array   $areas      Areas to return data for
	 * @return     array
	 */
	public function onMembersFavorites($member, $option, $limit=0, $limitstart=0, $areas=null)
	{
		$database = JFactory::getDBO();

		if (is_array($areas) && $limit) 
		{
			if (!isset($areas[$this->_name]) 
			  && !in_array($this->_name, $areas) 
			  && !array_intersect($areas, array_keys($this->onMembersContributionsAreas())))
			{
				return array();
			}
		}

		// Do we have a member ID?
		if (get_class($member) == '\Hubzero\User\Profile') 
		{
			if (!$member->get('uidNumber')) 
			{
				return array();
			} 
			else 
			{
				$uidNumber = $member->get('uidNumber');
				$username = $member->get('username');
			}
		} 
		else 
		{
			if (!$member->get('id')) 
			{
				return array();
			} 
			else 
			{
				$uidNumber = $member->uidNumber;
				$username = $member->username;
			}
		}

		$access = " AND w.access!=1";
		//if ($authorized) {
		//	$access = "";
		//}

		$f_count = "SELECT COUNT(*) ";
		$f_fields = "SELECT f.id, f.pagetext AS text, 'wiki' AS section, 'index.php?option=' AS href, d.scope, d.group_cn, d.access, d.title, d.pagename, d.created ";
		$f_from = "FROM #__wiki_version AS f, 
						(
							SELECT v.pageid, v.created, w.title, w.pagename, w.scope, w.group_cn, w.access, MAX(v.version) AS version
							FROM #__wiki_page AS w, #__wiki_version AS v, #__xfavorites AS x
							WHERE w.id=v.pageid AND v.approved=1 AND x.uid='" . $uidNumber . "' AND w.id=x.oid AND x.tbl='wiki' $access
							GROUP BY pageid
						) AS d
					WHERE f.version=d.version 
					AND f.pageid=d.pageid ";
		$order_by = "ORDER BY id DESC, title LIMIT $limitstart,$limit";

		if (!$limit) 
		{
			//echo $f_count . $f_from;
			$database->setQuery($f_count . $f_from);
			return $database->loadResult();
		} 
		else 
		{
			$database->setQuery($f_fields . $f_from . $order_by);
			$rows = $database->loadObjectList();

			if ($rows) 
			{
				foreach ($rows as $key => $row)
				{
					if ($row->group_cn != '' && $row->scope != '') 
					{
						$rows[$key]->href = JRoute::_('index.php?option=com_groups&scope=' . $row->scope . '&pagename=' . $row->pagename);
					} 
					else 
					{
						$rows[$key]->href = JRoute::_('index.php?option=com_wiki&scope=' . $row->scope . '&pagename=' . $row->pagename);
					}
				}
			}

			return $rows;
		}
	}
}
