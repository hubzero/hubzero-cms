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
class plgYSearchKB extends YSearchPlugin
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
	 * @param      object $request  YSearchModelRequest
	 * @param      object &$results YSearchModelResultSet
	 * @return     void
	 */
	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(f.title, f.params, f.`fulltext`) against (\'' . join(' ', $terms['stemmed']) . '\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(f.title LIKE '%$mand%' OR f.params LIKE '%$mand%' OR f.`fulltext` LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(f.title NOT LIKE '%$forb%' AND f.params NOT LIKE '%$forb%' AND f.`fulltext` NOT LIKE '%$forb%')";
		}
		
		$user = JFactory::getUser();
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$addtl_where[] = '(f.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . '))';
		}
		else 
		{
			if ($user->guest)
			{
				$addtl_where[] = '(f.access = 0)';
			}
			elseif ($user->usertype != 'Super Administrator')
			{
				$addtl_where[] = '(f.access = 0 OR f.access = 1)';
			}
		}

		$results->add(new YSearchResultSQL(
			"SELECT 
				f.title,
				coalesce(f.`fulltext`, '') AS description,
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
				f.state = 1 AND 
				$weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		));
	}
}

