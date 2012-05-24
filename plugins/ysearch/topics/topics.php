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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'plgYSearchTopics'
 * 
 * Long description (if any) ...
 */
class plgYSearchTopics extends YSearchPlugin
{

	/**
	 * Short description for 'onYSearch'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $request Parameter description (if any) ...
	 * @param      object &$results Parameter description (if any) ...
	 * @param      object $authz Parameter description (if any) ...
	 * @return     void
	 */
	public static function onYSearch($request, &$results, $authz)
	{
		$terms = $request->get_term_ar();
		$weight = '(match(wp.title) against (\'' . join(' ', $terms['stemmed']) . '\') + match(wv.pagetext) against (\'' . join(' ', $terms['stemmed']) . '\'))';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(wp.title LIKE '%$mand%' OR wv.pagetext LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(wp.title NOT LIKE '%$forb%' AND wv.pagetext NOT LIKE '%$forb%')";
		}

		# TODO
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$user =& JFactory::getUser();
			$viewlevels	= implode(',', $user->getAuthorisedViewLevels());
			
			if (($gids = $authz->get_group_ids()))
			{
				$authorization = '(wp.access IN (' . $viewlevels . ') OR (wp.access = 1 AND xg.gidNumber IN (' . join(',', $gids) . ')))';
			}
			else 
			{
				$authorization = '(wp.access IN (' . $viewlevels . '))';
			}
		}
		else 
		{
			if ($authz->is_guest()) 
			{
				$authorization = 'wp.access = 0';
			}
			elseif ($authz->is_super_admin())
			{
				$authorization = '1';
			}
			elseif (($gids = $authz->get_group_ids()))
			{
				$authorization = '(wp.access = 0 OR (wp.access = 1 AND xg.gidNumber IN (' . join(',', $gids) . ')))';
			}
			else
			{
				$authorization = 'wp.access = 0';
			}
		}

		$rows = new YSearchResultSQL(
			"SELECT 
				wp.title,
				wv.pagetext AS description,
				CASE 
					WHEN wp.group THEN concat('index.php?option=com_groups&scope=', wp.scope, '&pagename=', wp.pagename)
					ELSE concat('index.php?option=com_topics&scope=', wp.scope, '&pagename=', wp.pagename)
				END AS link,
				$weight AS weight,
				wv.created AS date,
				'Topics' AS section
			FROM jos_wiki_version wv
			INNER JOIN jos_wiki_page wp 
				ON wp.id = wv.pageid
			LEFT JOIN jos_xgroups xg ON xg.cn = wp.group
			WHERE
				$authorization AND
				$weight > 0 AND 
				wv.id = (SELECT MAX(wv2.id) FROM jos_wiki_version wv2 WHERE wv2.pageid = wv.pageid) " .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		);
		foreach ($rows->to_associative() as $row)
		{
			if (!$row) 
			{
				continue;
			}
			# rough de-wikifying. probably a bit faster than rendering to html and then stripping the tags, but not perfect
			$row->set_link(JRoute::_($row->get_raw_link()));
			$row->set_description(preg_replace('/(\[+.*?\]+|\{+.*?\}+|[=*])/', '', $row->get_description()));
			$results->add($row);
		}
	}
}

