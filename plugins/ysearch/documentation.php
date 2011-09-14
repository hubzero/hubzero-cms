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
 * Short description for 'plgYSearchDocumentation'
 * 
 * Long description (if any) ...
 */
class plgYSearchDocumentation extends YSearchPlugin
{

	/**
	 * Short description for 'onYSearch'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $request Parameter description (if any) ...
	 * @param      object &$results Parameter description (if any) ...
	 * @return     void
	 */
	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(m.title, m.description) against (\''.join(' ', $terms['stemmed']).'\')';
		$s_weight = 'match(s.title, s.content) against (\''.join(' ', $terms['stemmed']).'\')';
		$c_weight = 'match(c.title) against(\''.join(' ', $terms['stemmed']).'\')';

		$addtl_where = array();
		$s_addtl_where = array();
		$c_addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(m.title LIKE '%$mand%' OR m.description LIKE '%$mand%')";
			$s_addtl_where[] = "(s.title LIKE '%$mand%' OR s.content LIKE '%$mand%')";
			$c_addtl_where[] = "(c.title LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(m.title NOT LIKE '%$forb%' AND m.description NOT LIKE '%$forb%')";
			$s_addtl_where[] = "(s.title NOT LIKE '%$forb%' AND s.content NOT LIKE '%$forb%')";
			$c_addtl_where[] = "(c.title NOT LIKE '%$forb%')";
		}

		$sql = new YSearchResultSQL(
			"SELECT
				m.id,
				'manuals' AS type,
				m.title,
				m.description,
				$weight AS weight,
				m.created AS date,
				'Manuals' AS section,
				concat('/documentation/', v.major_version, '.', v.minor_version, '.', v.release, '/', m.alias) AS link,
				NULL AS parent
			FROM jos_doc_manuals m
			INNER JOIN jos_doc_versions v
				ON v.id = m.version_id AND v.released < CURRENT_TIMESTAMP
			WHERE $weight > 0".
			($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" UNION
			SELECT
				c.id,
				'chapters' AS type,
				c.title,
				group_concat(concat(cs.title, ': ', cs.content)) AS description,
				$c_weight AS weight,
				cm.created AS date,
				concat(cm.title, ' Manual') AS section,
				concat('/documentation/', cv.major_version, '.', cv.minor_version, '.', cv.release, '/', cm.alias, '/', coalesce(concat(cp.alias, '.'), ''), c.alias) AS link,
				c.manual_id AS parent
			FROM jos_doc_manual_chapters c
			INNER JOIN jos_doc_manuals cm
				ON cm.id = c.manual_id
			INNER JOIN jos_doc_versions cv
				ON cv.id = cm.version_id AND cv.released < CURRENT_TIMESTAMP
			LEFT JOIN jos_doc_manual_chapters cp
				ON cp.id = c.parent
			LEFT JOIN jos_doc_manual_chapter_sections cs
				ON cs.chapter_id = c.id
			WHERE $c_weight > 0
			GROUP BY c.id".
			($c_addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" UNION
			SELECT
				s.id,
				'sections' AS type,
				s.title AS title,
				s.content AS description,
				$s_weight AS weight,
				s.created AS date,
				concat(sm.title, ' Manual: ', sc.title) AS section,
				concat('/documentation/', sv.major_version, '.', sv.minor_version, '.', sv.release, '/', sm.alias, '/', coalesce(concat(scp.alias, '.'), ''), sc.alias) AS link,
				s.chapter_id AS parent
			FROM jos_doc_manual_chapter_sections s
			INNER JOIN jos_doc_manual_chapters sc
				ON sc.id = s.chapter_id
			INNER JOIN jos_doc_manuals sm
				ON sm.id = sc.manual_id
			INNER JOIN jos_doc_versions sv
				ON sv.id = sm.version_id AND sv.released < CURRENT_TIMESTAMP
			LEFT JOIN jos_doc_manual_chapters scp
				ON scp.id = sc.parent
			WHERE $s_weight > 0".
			($s_addtl_where ? ' AND ' . join(' AND ', $s_addtl_where) : '').
			" ORDER BY weight DESC"
		);
		$selected = array(
			'manuals' => array(),
			'chapters' => array(),
			'sections' => array()
		);
		$needed = array(
			'manuals' => array(),
			'chapters' => array(),
			'sections' => array()
		);
		$assoc = $sql->to_associative();

		foreach ($assoc as $row)
		{
		}

		$results->add($sql);
	}
}

