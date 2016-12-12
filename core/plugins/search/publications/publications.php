<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Publications\Models\Orm\Publication;

require_once Component::path('com_publications') . DS . 'models' . DS . 'orm' . DS . 'publication.php';

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
class plgSearchPublications extends \Hubzero\Plugin\Plugin
{
	/**
	 * Build search query and add it to the $results
	 *
	 * @param      object $request  \Components\Search\Models\Basic\Request
	 * @param      object &$results \Components\Search\Models\Basic\Result\Set
	 * @param      object $authz    \Components\Search\Models\Basic\Authorization
	 * @return     void
	 */
	public static function onSearch($request, &$results, $authz)
	{
		$dbg = isset($_GET['dbg']);

		$database = App::get('db');

		$groups = array_map(array($database, 'escape'), $authz->get_group_names());
		$viewlevels = implode(',', User::getAuthorisedViewLevels());

		/*if ($groups)
		{
			$group_list = '(\'' . join('\', \'', $groups) . '\')';
			$access = '(p.access IN (' . $viewlevels . ') OR ((v.access = 4 OR access = 5) AND r.group_owner IN ' . $group_list . '))';
		}
		else
		{*/
			$access = '(p.access IN (0, ' . $viewlevels . '))';
		//}

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

		$sql = new \Components\Search\Models\Basic\Result\Sql(
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
				$sql = new \Components\Search\Models\Basic\Result\Sql(
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
			$row->set_link(Route::url($row->get_raw_link()));
			$results->add($row);
		}
	}
	/**
	 * onGetTypes - Announces the available hubtype
	 * 
	 * @param mixed $type 
	 * @access public
	 * @return void
	 */
	public function onGetTypes($type = null)
	{
		// The name of the hubtype
		$hubtype = 'publication';

		if (isset($type) && $type == $hubtype)
		{
			return $hubtype;
		}
		elseif (!isset($type))
		{
			return $hubtype;
		}
	}

	/**
	 * onIndex 
	 * 
	 * @param string $type
	 * @param integer $id 
	 * @param boolean $run 
	 * @access public
	 * @return void
	 */
	public function onIndex($type, $id, $run = false)
	{
		if ($type == 'publication')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT
					#__publications.id,
					alias,
					#__publications.access,
					master_doi,
					published_up,
					#__publications.created_by,
					abstract,
					description,
					title,
					doi,
					state,
					release_notes,
					MAX(#__publication_versions.id) as latestVersion
					FROM #__publications 
				LEFT JOIN #__publication_versions
				ON #__publications.id = #__publication_versions.publication_id
				WHERE #__publications.id = {$id};";
				$row = $db->setQuery($sql)->query()->loadObject();

				// Get the name of the author
				$sql1 = "SELECT user_id, name FROM #__publication_authors WHERE publication_version_id={$row->latestVersion} AND role != 'submitter';";
				$authors = $db->setQuery($sql1)->query()->loadAssocList();

				// @TODO: PHP 5.5 includes array_column()
				$owners = array();
				foreach ($authors as $author)
				{
					array_push($owners, $author['user_id']);
				}

				$authorNames = array();
				foreach ($authors as $author)
				{
					array_push($authorNames, $author['name']);
				}

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$row->latestVersion} AND #__tags_object.tbl = 'publications';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();

				// Determine the path
				if ($row->alias != '')
				{
					$path = '/publications/' . $row->alias;
				}
				else
				{
					$path = '/publications/' . $id;
				}

				// Public condition
				if ($row->state == 1 && $row->access == 0)
				{
					$access_level = 'public';
				}
				// Registered condition
				elseif ($row->state == 1 && $row->access == 1)
				{
					$access_level = 'registered';
				}
				// Default private
				else
				{
					$access_level = 'private';
				}

				// Authors have access
				$owner_type = 'user';

				// So does submitter;
				array_push($owners, $row->created_by);

				// Get the title
				$title = $row->title;

				// Build the description, clean up text
				$content = $row->abstract . ' ' . $row->description . ' ' . $row->release_notes;
				$content = preg_replace('/<[^>]*>/', ' ', $content);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Grab the DOI if there exists one
				if (isset($row->doi))
				{
					$doi = $row->doi;
				}
				else
				{
					$doi = '';
				}

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->hubtype = $type;
				$record->title = $title;
				$record->description = $description;
				$record->doi = $doi;
				$record->author = $authorNames;
				$record->tags = $tags;
				$record->path = $path;
				$record->access_level = $access_level;
				$record->owner = $owners;
				$record->owner_type = $owner_type;

				// Return the formatted record
				return $record;
			}
			else
			{
				$db = App::get('db');
				$sql = "SELECT id FROM #__publications;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
	}
}

