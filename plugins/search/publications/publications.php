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
 * Publications child sorter class
 */
class PublicationChildSorter
{
	/**
	 * Description for 'order'
	 * 
	 * @var array
	 */
	private $order;

	/**
	 * Constructor
	 * 
	 * @param      array $order Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($order)
	{
		$this->order = $order;
	}

	/**
	 * Short description for 'sort'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $a Parameter description (if any) ...
	 * @param      object $b Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function sort($a, $b)
	{
		$a_id = $a->get('id');
		$b_id = $b->get('id');
		$sec_diff = strcmp($a->get_section(), $b->get_section());
		if ($sec_diff < 0)
		{
			return -1;
		}
		if ($sec_diff > 0)
		{
			return 1;
		}
		$a_ord = $this->order[$a_id];
		$b_ord = $this->order[$b_id];
		return $a_ord == $b_ord ? 0 : $a_ord < $b_ord ? -1 : 1;
	}
}

/**
 * Search plugin class for publications
 */
class plgSearchPublications extends SearchPlugin
{
	/**
	 * Execute query and add results to $results object
	 * 
	 * @param      mixed $request Parameter description (if any) ...
	 * @param      object &$results Parameter description (if any) ...
	 * @param      object $authz Parameter description (if any) ...
	 * @return     void
	 */
	public static function onSearch($request, &$results, $authz)
	{
		$dbg = isset($_GET['dbg']);

		$database = JFactory::getDBO();

		// Joomla 1.6+
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$user = JFactory::getUser();

			$groups = array_map(array($database, 'getEscaped'), $authz->get_group_names());
			$viewlevels = implode(',', $user->getAuthorisedViewLevels());

			/*if ($groups)
			{
				$group_list = '(\'' . join('\', \'', $groups) . '\')';
				$access = '(p.access IN (' . $viewlevels . ') OR ((v.access = 4 OR access = 5) AND r.group_owner IN ' . $group_list . '))';
			}
			else 
			{*/
				$access = '(p.access IN (0, ' . $viewlevels . '))';
			//}
		}
		else 
		// Joomla 1.5
		{
			if ($authz->is_guest())
			{
				$access = 'p.access = 0';
			}
			else if ($authz->is_super_admin())
			{
				$access = '1';
			}
			else
			{
				/*$groups = array_map(array($database, 'getEscaped'), $authz->get_group_names());
				if ($groups)
				{
					$group_list = '(\'' . join('\', \'', $groups) . '\')';
					$access = '(access = 0 OR access = 1 OR ((access = 3 OR access = 4) AND r.group_owner IN ' . $group_list . '))';
				}
				else
				{*/
					$access = '(p.access = 0 OR p.access = 1)';
				//}
			}
		}

		$term_parser = $request->get_terms();
		$terms = $request->get_term_ar();
		$quoted_terms = array();
		foreach ($terms['optional'] as $idx => $term)
		{
			if ($term_parser->is_quoted($idx))
			{
				foreach ($terms['stemmed'] as $sidx => $stem)
				{
					if (strpos($term, $stem) === 0 || strpos($stem, $term) === 0)
					{
						unset($terms['stemmed'][$sidx]);
					}
				}
				$quoted_terms[] = $term;
			}
		}

		$tag_map = array();
		foreach ($request->get_tagged_ids('publications') as $id)
		{
			if (array_key_exists($id, $tag_map))
			{
				++$tag_map[$id];
			}
			else
			{
				$tag_map[$id] = 1;
			}
		}

		$weight_authors = 'a.name LIKE \'%'.implode(' ', $terms['optional']).'%\'';
		$weight = $terms['stemmed'] ? 'match(v.title, v.description, v.abstract) against (\'' . join(' ', $terms['stemmed']) . '\')' : '0';
		foreach ($quoted_terms as $term)
		{
			$weight .= " + (CASE WHEN v.title LIKE '%$term%' OR v.description LIKE '%$term%' OR v.abstract LIKE '%$term%' THEN 1 ELSE 0 END)";
		}

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(v.title LIKE '%$mand%' OR v.description LIKE '%$mand%' OR v.abstract LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(v.title NOT LIKE '%$forb%' AND v.description NOT LIKE '%$forb%' AND v.abstract NOT LIKE '%$forb%')";
		}

		$sql = new SearchResultSQL(
			"SELECT
				p.id,
				v.publication_id,
				v.title,
				v.description,
				concat('index.php?option=com_publications&id=', coalesce(case when p.alias = '' then null else p.alias end, p.id)) AS link,
				$weight AS weight,
				v.published_up AS date,
				c.alias AS section,
				(SELECT group_concat(a.name order by a.ordering separator '\\n') FROM #__publication_authors a WHERE a.publication_version_id = v.id AND a.status=1) 
					AS contributors,
				(SELECT group_concat(a.user_id order by a.ordering separator '\\n') FROM #__publication_authors a WHERE a.publication_version_id = v.id AND a.status=1) 
					AS contributor_ids,
				NULL AS parents
			FROM #__publication_versions v
			INNER JOIN #__publications p 
				ON p.id = v.publication_id
			LEFT JOIN #__publication_categories c 
				ON c.id = p.category
			WHERE 
				v.state = 1 AND $access AND (v.published_up AND NOW() > v.published_up) AND (NOT v.published_down OR NOW() < v.published_down) 
				AND ($weight > 0)" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			"UNION
			SELECT 
				p.id,
				v.publication_id,
				v.title,
				v.description,
				concat('index.php?option=com_publications&id=', coalesce(case when p.alias = '' then null else p.alias end, p.id)) AS link,
				1 AS weight,
				v.published_up AS date,
				c.alias AS section,
				(SELECT group_concat(a.name order by a.ordering separator '\\n') FROM #__publication_authors a WHERE a.publication_version_id = v.id AND a.status=1) 
					AS contributors,
				(SELECT group_concat(a.user_id order by a.ordering separator '\\n') FROM #__publication_authors a WHERE a.publication_version_id = v.id AND a.status=1) 
					AS contributor_ids,
				NULL AS parents
			FROM #__publication_authors a
			INNER JOIN #__publication_versions v 
				ON v.id = a.publication_version_id  
			INNER JOIN #__publications p 
				ON p.id = v.publication_id
			LEFT JOIN #__publication_categories c 
				ON c.id = p.category
			WHERE 
				v.state = 1 AND $access AND (v.published_up AND NOW() > v.published_up) AND (NOT v.published_down OR NOW() < v.published_down) 
				AND a.status = 1 AND $weight_authors"
		);
		$assoc = $sql->to_associative();

		$id_assoc = array();
		foreach ($assoc as $row)
		{
			$id_assoc[$row->get('id')] = $row;
		}

		$placed = array();
		if (!$quoted_terms)
		{
			// Find ids of tagged resources that did not match regular fulltext searching
			foreach ($assoc as $row)
			{
				$id = (int)$row->get('id');
				if (array_key_exists($id, $tag_map))
				{
					$row->add_weight((1 + $tag_map[$id])/12, 'tag bonus from publications plugin');
					unset($tag_map[$id]);
				}
			}
			// Fill in tagged resources that did not match on fulltext
			if ($tag_map)
			{
				$sql = new SearchResultSQL(
					"SELECT
						p.id,
						v.publication_id,
						v.title,
						v.description,
						concat('index.php?option=com_publications&id=', coalesce(case when p.alias = '' then null else p.alias end, p.id)) AS link,
						0.5 as weight,
						v.published_up AS date,
						c.alias AS section,
						(SELECT group_concat(a.name order by a.ordering separator '\\n') FROM #__publication_authors a WHERE a.publication_version_id = v.id AND a.status=1) 
							AS contributors,
						(SELECT group_concat(a.user_id order by a.ordering separator '\\n') FROM #__publication_authors a WHERE a.publication_version_id = v.id AND a.status=1) 
							AS contributor_ids,
						NULL AS parents
					FROM #__publication_versions v
					INNER JOIN #__publications p 
						ON p.id = v.publication_id
					LEFT JOIN #__publication_categories c 
						ON c.id = p.category
					WHERE
						v.state = 1 AND $access AND (v.published_up AND NOW() > v.published_up) AND (NOT v.published_down OR NOW() < v.published_down) 
						AND p.id in (" . implode(',', array_keys($tag_map)) . ")" . ($addtl_where ? ' AND ' . implode(' AND ', $addtl_where) : '')
				);
				foreach ($sql->to_associative() as $row)
				{
					$rows = $sql->to_associative();
					foreach ($rows as $row)
					{
						if ($tag_map[$row->get('id')] > 1)
						{
							$row->adjust_weight($tag_map[$row->get('id')]/8, 'tag bonus for non-matching but tagged publications');
						}
						$id_assoc[$row->get('id')] = $row;
					}
				}
			}
		}

		$sorter = new PublicationChildSorter($placed);
		$rows = array();
		foreach ($id_assoc as $id=>$row)
		{
			if (!array_key_exists((int)$id, $placed))
			{
				$row->sort_children(array($sorter, 'sort'));
				$rows[] = $row;
			}
		}

		usort($rows, create_function('$a, $b', 'return (($res = $a->get_weight() - $b->get_weight()) == 0 ? 0 : $res > 0 ? -1 : 1);'));
		foreach ($rows as $row)
		{
			$row->set_link(JRoute::_($row->get_raw_link()));
			$results->add($row);
		}
	}
}

