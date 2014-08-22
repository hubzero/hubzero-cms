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
 * Search knowledge base entries
 */
class plgSearchKB extends SearchPlugin
{
	/**
	 * Get the name of the area being searched
	 *
	 * @return     string
	 */
	public static function getName()
	{
		return JText::_('Knowledge Base');
	}

	/**
	 * Build search query and add it to the $results
	 *
	 * @param      object $request  SearchModelRequest
	 * @param      object &$results SearchModelResultSet
	 * @return     void
	 */
	public static function onSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(f.title, f.`fulltxt`) against (\'' . join(' ', $terms['stemmed']) . '\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(f.title LIKE '%$mand%' OR f.`fulltxt` LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(f.title NOT LIKE '%$forb%' AND f.`fulltxt` NOT LIKE '%$forb%')";
		}

		$user = JFactory::getUser();

		$addtl_where[] = '(f.access IN (0,' . implode(',', $user->getAuthorisedViewLevels()) . '))';

		$results->add(new SearchResultSQL(
			"SELECT
				f.title,
				coalesce(f.`fulltxt`, '') AS description,
				concat('index.php?option=com_kb&section=', coalesce(concat(s.alias, '/'), ''), f.alias) AS link,
				$weight AS weight,
				created AS date,
				CASE
					WHEN s.alias IS NULL THEN c.alias
					WHEN c.alias IS NULL THEN s.alias
					ELSE concat(s.alias, ', ', c.alias)
				END AS section
			FROM `#__faq` f
			LEFT JOIN `#__faq_categories` s
				ON s.id = f.section
			LEFT JOIN `#__faq_categories` c
				ON c.id = f.category
			WHERE
				f.state = 1 AND s.state = 1 AND
				$weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		));
	}
}

