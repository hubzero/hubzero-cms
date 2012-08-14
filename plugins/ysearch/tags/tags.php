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
 * Search tag entries
 */
class plgYSearchTags extends YSearchPlugin
{
	/**
	 * Build search query and add it to the $results
	 * 
	 * @param      object $request  YSearchModelRequest
	 * @param      object &$results YSearchModelResultSet
	 * @return     void
	 */
	public static function onYSearchWidget($terms, &$widgets)
	{
		$weight = 'match(t.raw_tag, t.description) against (\'' . join(' ', $terms['stemmed']) . '\')';
			
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(t.raw_tag LIKE '%$mand%' OR t.tag LIKE '%$mand%' OR t.description LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(t.raw_tag NOT LIKE '%$forb%' AND t.tag NOT LIKE '%$forb%' AND t.description NOT LIKE '%$forb%')";
		}

		$tags = new YSearchResultSQL(
			"SELECT 
				t.raw_tag AS title,
				description,
				concat('/tags/', t.tag) AS link,
				$weight AS weight,
				NULL AS date,
				'Tags' AS section
			FROM jos_tags t
			WHERE 
				NOT t.admin
				AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		);

		$tag_html = array();
		foreach ($tags->to_associative() as $tag)
		{
			$tag_html[] = '<li><a href="' . $tag->get_link() . '">' . $tag->get_title() . '</a></li>';
		}
		$widgets->add_html('<ol class="tags">' . join('', $tag_html) . '</ol>');
	}
}
