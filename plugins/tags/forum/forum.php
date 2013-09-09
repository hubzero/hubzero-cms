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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Tags plugin class for forum entries
 */
class plgTagsForum extends JPlugin
{
	/**
	 * Record count
	 * 
	 * @var integer
	 */
	private $_total = null;

	/**
	 * Constructor
	 * 
	 * @param      object &$subject The object to observe
	 * @param      array  $config   An optional associative array of configuration settings.
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the name of the area this plugin retrieves records for
	 * 
	 * @return     array
	 */
	public function onTagAreas()
	{
		$areas = array(
			'forum' => JText::_('PLG_TAGS_FORUM')
		);
		return $areas;
	}

	/**
	 * Get the group IDs for all the groups of a specific user
	 * 
	 * @param      integer $uid User ID
	 * @return     array
	 */
	private function _getGroupIds($uid=0)
	{
		$dbh =& JFactory::getDBO();
		$dbh->setQuery(
			'select distinct gidNumber from #__xgroups_members where uidNumber = ' . $uid . ' union select distinct gidNumber from #__xgroups_managers where uidNumber = ' . $uid
		);
		return $dbh->loadResultArray();
	}

	/**
	 * Retrieve records for items tagged with specific tags
	 * 
	 * @param      array   $tags       Tags to match records against
	 * @param      mixed   $limit      SQL record limit
	 * @param      integer $limitstart SQL record limit start
	 * @param      string  $sort       The field to sort records by
	 * @param      mixed   $areas      An array or string of areas that should retrieve records
	 * @return     mixed Returns integer when counting records, array when retrieving records
	 */
	public function onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)
	{
		if (is_array($areas) && $limit) 
		{
			if (!array_intersect($areas, $this->onTagAreas()) 
			 && !array_intersect($areas, array_keys($this->onTagAreas()))) 
			{
				return array();
			}
		}

		// Do we have a member ID?
		if (empty($tags)) 
		{
			return array();
		}

		$database =& JFactory::getDBO();

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->get('id');
		}
		$ids = implode(',', $ids);

		$addtl_where = array();
		$juser =& JFactory::getUser();
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$gids = $this->_getGroupIds($juser->get('id'));
			if (!$juser->authorise('core.view', 'com_forum'))
			{
				$addtl_where[] = 'e.scope_id IN (0' . ($gids ? ',' . join(',', $gids) : '') . ')';
			}
			else 
			{
				$viewlevels	= implode(',', $juser->getAuthorisedViewLevels());

				if ($gids)
				{
					$addtl_where[] = '(e.access IN (' . $viewlevels . ') OR ((e.access = 4 OR e.access = 5) AND e.scope_id IN (0,' . join(',', $gids) . ')))';
				}
				else 
				{
					$addtl_where[] = '(e.access IN (' . $viewlevels . '))';
				}
			}
		}
		else 
		{
			if ($juser->get('guest'))
			{
				$addtl_where[] = '(e.access = 0)';
			}
			elseif ($juser->usertype != 'Super Administrator')
			{
				$groups = $this->_getGroupIds($juser->get('id'));
				if ($groups)
				{
					$addtl_where[] = '(e.access = 0 OR e.access = 1 OR ((e.access = 3 OR e.access = 4) AND e.scope_id IN (0,' . join(',', $groups) . ')))';
				}
				else
				{
					$addtl_where[] = '(e.access = 0 OR e.access = 1)';
				}
			}
		}
		
		// Build the query
		$e_count = "SELECT COUNT(f.id) FROM (SELECT e.id, COUNT(DISTINCT t.tagid) AS uniques";
		$e_fields = "SELECT e.id, e.title, e.id AS alias, e.comment AS itext, e.comment AS ftext, e.state, e.created, e.created_by, e.modified, e.created AS publish_up, NULL AS publish_down, 
					(CASE WHEN e.scope_id > 0 AND e.scope='group' THEN
						concat('/groups/', g.cn, concat('/forum/', coalesce(concat(s.alias, '/', coalesce(concat(c.alias, '/'), ''))), CASE WHEN e.parent > 0 THEN e.parent ELSE e.id END))
					ELSE
						concat('/forum/', coalesce(concat(s.alias, '/', coalesce(concat(c.alias, '/'), ''))), CASE WHEN e.parent > 0 THEN e.parent ELSE e.id END)
					END) AS href, 
					'forum' AS section, COUNT(DISTINCT t.tagid) AS uniques, NULL AS params, e.last_activity AS rcount, c.alias AS data1, s.alias AS data2, g.cn AS data3 ";
		$e_from  = " FROM #__forum_posts AS e
		 			LEFT JOIN #__forum_categories c ON c.id = e.category_id
					LEFT JOIN #__forum_sections s ON s.id = c.section_id
					LEFT JOIN #__xgroups g ON g.gidNumber = e.scope_id
					LEFT JOIN #__tags_object AS t ON t.objectid=e.id AND t.tbl='forum' AND t.tagid IN ($ids)";
		$e_where  = " WHERE e.state=1 AND e.parent=0" . ($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '');
		$e_where .= " GROUP BY e.id HAVING uniques=" . count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title': $order_by .= 'title ASC, created';  break;
			case 'id':    $order_by .= "id DESC";             break;
			case 'date':
			default:      $order_by .= 'created DESC, title'; break;
		}
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit) 
		{
			// Get a count
			$database->setQuery($e_count . $e_from . $e_where . ") AS f");
			$this->_total = $database->loadResult();
			return $this->_total;
		} 
		else 
		{
			if (count($areas) > 1) 
			{
				return $e_fields . $e_from . $e_where;
			}

			if ($this->_total != null) 
			{
				if ($this->_total == 0) 
				{
					return array();
				}
			}

			// Get results
			$database->setQuery($e_fields . $e_from . $e_where . $order_by);
			$rows = $database->loadObjectList();

			return $rows;
		}
	}

	/**
	 * Static method for formatting results
	 * 
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public function out($row)
	{
		if (strstr($row->href, 'index.php')) 
		{
			$row->href = JRoute::_('index.php?option=com_kb&section=' . $row->data2 . '&category=' . $row->data1 . '&thread=' . $row->alias);
		}
		$juri =& JURI::getInstance();

		// Start building the HTML
		$html  = "\t" . '<li class="kb-entry">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		$html .= "\t\t" . '<p class="details">' . JText::_('PLG_TAGS_FORUM') . ' &rsaquo; ' . stripslashes($row->data2) . ' &rsaquo; ' . stripslashes($row->data1) . '</p>' . "\n";
		if ($row->ftext) 
		{
			$html .= "\t\t" . Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->ftext)), 200) . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . $juri->base() . ltrim($row->href, DS) . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";

		// Return output
		return $html;
	}
}

