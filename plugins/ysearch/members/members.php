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
 * Short description for 'ContributionSorter'
 * 
 * Long description (if any) ...
 */
class ContributionSorter
{

	/**
	 * Short description for 'sort'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $a Parameter description (if any) ...
	 * @param      object $b Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function sort($a, $b)
	{
		$sec_diff = strcmp($a->get_section(), $b->get_section());
		if ($sec_diff < 0)
		{
			return -1;
		}
		if ($sec_diff > 0)
		{
			return 1;
		}
		$a_ord = $a->get('ordering');
		$b_ord = $b->get('ordering');
		return $a_ord == $b_ord ? 0 : $a_ord < $b_ord ? -1 : 1;
	}

	/**
	 * Short description for 'sort_weight'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $a Parameter description (if any) ...
	 * @param      object $b Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function sort_weight($a, $b)
	{
		$aw = $a->get_weight();
		$bw = $b->get_weight();
		if ($aw == $bw)
		{
			return 0;
		}
		return $aw > $bw ? -1 : 1;
	}

	/**
	 * Short description for 'sort_title'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $a Parameter description (if any) ...
	 * @param      object $b Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public static function sort_title($a, $b)
	{
		return strcmp($a->get_title(), $b->get_title());
	}
}

/**
 * Search members
 */
class plgYSearchMembers extends YSearchPlugin
{
	/**
	 * Build search query and add it to the $results
	 * 
	 * @param      object $request  YSearchModelRequest
	 * @param      object &$results YSearchModelResultSet
	 * @return     void
	 */
	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = '(match(p.name) against (\'' . join(' ', $terms['stemmed']) . '\') + match(b.bio) against(\'' . join(' ', $terms['stemmed']) . '\'))';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(p.name LIKE '%$mand%' OR b.bio LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(p.name NOT LIKE '%$forb%' AND b.bio NOT LIKE '%$forb%')";
		}

		$results->add(new YSearchResultSQL(
			"SELECT 
				p.uidNumber AS id,
				p.name AS title,
				coalesce(b.bio, '') AS description,
				concat('/members/', CASE WHEN p.uidNumber > 0 THEN p.uidNumber ELSE concat('n', abs(p.uidNumber)) END) AS link,
				$weight AS weight,
				NULL AS date,
				'Members' AS section,
				CASE WHEN p.picture IS NOT NULL THEN concat('/site/members/', lpad(p.uidNumber, 5, '0'), '/', p.picture) ELSE NULL END AS img_href
			FROM #__xprofiles p
			LEFT JOIN #__xprofiles_bio b 
				ON b.uidNumber = p.uidNumber
			WHERE 
				public AND $weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		));
	}

	/**
	 * Build search query and add it to the $results
	 * 
	 * @param      object $request  YSearchModelRequest
	 * @param      object &$results YSearchModelResultSet
	 * @return     void
	 */
	public static function onYSearchCustom($request, &$results)
	{
		if (($section = $request->get_terms()->get_section()) && $section[0] != 'members')
		{
			return;
		}

		$terms = $request->get_term_ar();
		$addtl_where = array();
		foreach (array($terms['mandatory'], $terms['optional']) as $pos)
		{
			foreach ($pos as $term)
			{
				$addtl_where[] = "(p.name LIKE '%$term%')";
			}
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(p.name NOT LIKE '%$forb%')";
		}

		$sql = new YSearchResultSQL(
			"SELECT 
				p.uidNumber AS id,
				p.name AS title,
				coalesce(b.bio, '') AS description,
				concat('/members/', CASE WHEN p.uidNumber > 0 THEN p.uidNumber ELSE concat('n', abs(p.uidNumber)) END) AS link,
				NULL AS date,
				'Members' AS section,
				CASE WHEN p.picture IS NOT NULL THEN concat('/site/members/', lpad(p.uidNumber, 5, '0'), '/', p.picture) ELSE NULL END AS img_href
			FROM #__xprofiles p
			LEFT JOIN #__xprofiles_bio b 
				ON b.uidNumber = p.uidNumber
			WHERE 
				public AND " . join(' AND ', $addtl_where)
		);
		$assoc = $sql->to_associative();
		if (!count($assoc))
		{
			return false;
		}

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$when = "c.alias THEN 
				concat(
					CASE WHEN c.alias THEN concat('/', c.alias) ELSE '' END
				)";
		}
		else 
		{
			$when = "s.name OR ca.name OR c.alias THEN 
				concat(
					CASE WHEN s.name THEN concat('/', s.name) ELSE '' END, 
					CASE WHEN ca.name AND ca.name != s.name THEN concat('/', ca.name) ELSE '' END, 
					CASE WHEN c.alias THEN concat('/', c.alias) ELSE '' END
				)";
		}

		$resp = array();
		foreach ($assoc as $row)
		{
			$query = "SELECT 
					CASE WHEN aa.subtable = 'resources' THEN
						r.title
					ELSE 
						c.title
					END AS title,
					CASE 
						WHEN aa.subtable = 'resources' THEN
							concat(coalesce(r.introtext, ''), coalesce(r.`fulltxt`, '')) 
						ELSE
							concat(coalesce(c.introtext, ''), coalesce(c.`fulltext`, ''))
					END AS description,
					CASE
						WHEN aa.subtable = 'resources' THEN
							concat('/resources/', r.id)
						ELSE
							CASE
								WHEN $when
								ELSE concat('/content/article/', c.id) 
							END
					END AS link,
					1 AS weight,
					CASE 
						WHEN aa.subtable = 'resources' THEN
							rt.type
						ELSE";
			if (version_compare(JVERSION, '1.6', 'lt'))
			{
						$query .= " s.name";
			}
			else
			{
						$query .= " s.alias";
			}
			$query .= " END AS section,
					CASE
						WHEN aa.subtable = 'resources' THEN
							ra.ordering
						ELSE
							-1
					END AS ordering
					FROM #__author_assoc aa
					LEFT JOIN #__resources r
						ON aa.subtable = 'resources' AND r.id = aa.subid AND r.published = 1
					LEFT JOIN #__resource_assoc ra
						ON ra.child_id = r.id
					LEFT JOIN #__resource_types rt 
						ON rt.id = r.type";
				if (version_compare(JVERSION, '1.6', 'lt'))
				{
					$query .= " LEFT JOIN #__content c
						ON aa.subtable = 'content' AND c.id = aa.subid AND c.state = 1
					LEFT JOIN #__sections s 
						ON s.id = c.sectionid
					LEFT JOIN #__categories ca
						ON ca.id = c.catid";
				} 
				else 
				{
					$query .= " LEFT JOIN #__content c
						ON aa.subtable = 'content' AND c.id = aa.subid AND c.state = 1
					LEFT JOIN #__categories s 
						ON s.id = c.sectionid
					LEFT JOIN #__categories ca
						ON ca.id = c.catid";
				}
			$query .= " WHERE aa.authorid = " . $row->get('id');
			$work = new YSearchResultSQL($query);
			$work_assoc = $work->to_associative();

			$added = array();
			foreach ($work_assoc as $wrow)
			{
				$link = $wrow->get_link();
				if (array_key_exists($link, $added))
				{
					continue;
				}
				$row->add_child($wrow);
				$row->add_weight(1);
				$added[$link] = 1;
			}
			$row->sort_children(array('ContributionSorter', 'sort'));
			$resp[] = $row;
		}
		usort($resp, array('ContributionSorter', 'sort_weight'));
		foreach ($resp as $row)
		{
			$results->add($row);
		}
		return false;
	}

	/**
	 * Generate an <img> tag with the user's picture, if set
	 * Otherwise, use default image
	 * 
	 * @param      object $res YSearchResult
	 * @return     string
	 */
	public static function onBeforeYSearchRenderMembers($res)
	{
		if (!($href = $res->get('img_href')) || !is_file(JPATH_ROOT.$href))
		{
			$href = '/components/com_members/assets/img/profile_thumb.gif';
		}

		return '<img src="' . $href . '" alt="' . htmlentities($res->get_title()) . '" title="' . htmlentities($res->get_title()) . '" />';
	}
}

