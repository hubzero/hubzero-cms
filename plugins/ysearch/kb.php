<?php
/**
 * @package     hubzero-cms
 * @author      Steve Snyder <snyder13@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class plgYSearchKB extends YSearchPlugin
{
	public static function getName() { return 'Knowledge Base'; }

	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(f.title, f.params, f.`fulltext`) against (\''.join(' ', $terms['stemmed']).'\')';
			
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(f.title LIKE '%$mand%' OR f.params LIKE '%$mand%' OR f.`fulltext` LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(f.title NOT LIKE '%$forb%' AND f.params NOT LIKE '%$forb%' AND f.`fulltext` NOT LIKE '%$forb%')";

		$results->add(new YSearchResultSQL(
			"SELECT 
				f.title,
				concat(coalesce(f.params, ''), coalesce(f.`fulltext`, '')) AS description,
				concat('/kb/', coalesce(concat(s.alias, '/'), ''), f.alias) AS link,
				$weight AS weight,
				created AS date,
				CASE 
					WHEN s.alias IS NULL THEN c.alias
					WHEN c.alias IS NULL THEN s.alias
					ELSE concat(s.alias, ', ', c.alias) 
				END AS section
			FROM jos_faq f
			LEFT JOIN jos_faq_categories s 
				ON s.id = f.section
			LEFT JOIN jos_faq_categories c
				ON c.id = f.category
			WHERE 
				$weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" ORDER BY $weight DESC"
		));
	}
}

