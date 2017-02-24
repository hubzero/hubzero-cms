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

use Components\Resources\Models\Orm\Resource;

require_once PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'orm' . DS . 'resource.php';
include_once children.php;

/**
 * Search plugin for resources
 */
class plgSearchResources extends \Hubzero\Plugin\Plugin
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

		if ($groups)
		{
			$group_list = '(\'' . join('\', \'', $groups) . '\')';
			$access = '(access IN (0,' . $viewlevels . ') OR ((access = 4 OR access = 5) AND r.group_owner IN ' . $group_list . '))';
		}
		else
		{
			$access = '(access IN (0,' . $viewlevels . '))';
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
		foreach ($request->get_tagged_ids('resources') as $id)
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

		$weight = $terms['stemmed'] ? 'match(r.title, r.introtext, r.`fulltxt`) against (\'' . join(' ', $terms['stemmed']) . '\')' : '0';
		foreach ($quoted_terms as $term)
		{
			$weight .= " + (CASE WHEN r.title LIKE '%$term%' OR r.introtext LIKE '%$term%' OR r.`fulltxt` LIKE '%$term%' THEN 1 ELSE 0 END)";
		}

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(r.title LIKE '%$mand%' OR r.introtext LIKE '%$mand%' OR r.`fulltxt` LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(r.title NOT LIKE '%$forb%' AND r.introtext NOT LIKE '%$forb%' AND r.`fulltxt` NOT LIKE '%$forb%')";
		}

		$sql = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				r.id,
				r.title,
				coalesce(r.`fulltxt`, r.introtext, '') AS description,
				concat('index.php?option=com_resources&id=', coalesce(case when r.alias = '' then null else r.alias end, r.id)) AS link,
				$weight AS weight,
				r.publish_up AS date,
				rt.type AS section,
				(SELECT group_concat(u1.name order by anames.ordering separator '\\n') FROM `#__author_assoc` anames LEFT JOIN `#__xprofiles` u1 ON u1.uidNumber = anames.authorid WHERE subtable = 'resources' AND subid = r.id)
				AS contributors,
				(SELECT group_concat(anames.authorid order by anames.ordering separator '\\n') FROM `#__author_assoc` anames WHERE subtable = 'resources' AND subid = r.id)
				AS contributor_ids,
				(select group_concat(concat(parent_id, '|', ordering))
					from `#__resource_assoc` ra2
					left join `#__resources` re3 on re3.id = ra2.parent_id and re3.standalone
					where ra2.child_id = r.id) AS parents
			FROM `#__resources` r
			LEFT JOIN `#__resource_types` rt
				ON rt.id = r.type
			WHERE
				r.published = 1 AND r.standalone AND $access AND (r.publish_up AND UTC_TIMESTAMP() > r.publish_up) AND (NOT r.publish_down OR UTC_TIMESTAMP() < r.publish_down)
				AND ($weight > 0)" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '')
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
			// Find ids of tagged resources that did not match regular fulltxt searching
			foreach ($assoc as $row)
			{
				$id = (int)$row->get('id');
				if (array_key_exists($id, $tag_map))
				{
					$row->add_weight((1 + $tag_map[$id])/12, 'tag bonus from resources plugin');
					unset($tag_map[$id]);
				}
			}
			// Fill in tagged resources that did not match on fulltxt
			if ($tag_map)
			{
				$sql = new \Components\Search\Models\Basic\Result\Sql(
					"SELECT
						r.id,
						r.title,
						coalesce(r.`fulltxt`, r.introtext, '') AS description,
						concat('index.php?option=com_resources&id=', coalesce(case when r.alias = '' then null else r.alias end, r.id)) AS link,
						r.publish_up AS date,
						0.5 as weight,
						rt.type AS section,
						(SELECT group_concat(u1.name order by anames.ordering separator '\\n') FROM `#__author_assoc` anames LEFT JOIN `#__xprofiles` u1 ON u1.uidNumber = anames.authorid WHERE subtable = 'resources' AND subid = r.id)
							AS contributors,
						(SELECT group_concat(anames.authorid order by anames.ordering separator '\\n') FROM `#__author_assoc` anames WHERE subtable = 'resources' AND subid = r.id)
							AS contributor_ids,
						(select group_concat(concat(parent_id, '|', ordering))
							from `#__resource_assoc` ra2
							left join `#__resources` re3 on re3.id = ra2.parent_id and re3.standalone
							where ra2.child_id = r.id) AS parents
					FROM `#__resources` r
					LEFT JOIN `#__resource_types` rt
					ON rt.id = r.type
					WHERE
						r.published = 1 AND r.standalone AND $access AND (r.publish_up AND NOW() > r.publish_up) AND (NOT r.publish_down OR NOW() < r.publish_down)
						AND r.id in (" . implode(',', array_keys($tag_map)) . ")" . ($addtl_where ? ' AND ' . implode(' AND ', $addtl_where) : '')
				);
				foreach ($sql->to_associative() as $row)
				{
					if ($tag_map[$row->get('id')] > 1)
					{
						$row->adjust_weight($tag_map[$row->get('id')]/8, 'tag bonus for non-matching but tagged resources');
					}
					$id_assoc[$row->get('id')] = $row;
				}
			}
		}

		/*
		// Nest child resources
		$section = $request->get_terms()->get_section();
		foreach ($id_assoc as $id=>$row)
		{
			$parents = $row->get('parents');
			if ($parents)
				foreach (explode(',', $parents) as $parent)
				{
					list($parent_id, $ordering) = preg_split('#\|#', $parent);
					if (array_key_exists((int)$parent_id, $id_assoc) && $id_assoc[(int)$parent_id]->is_in_section($section, 'resources'))
					{
						$placed[(int)$id] = $ordering;
						$id_assoc[(int)$parent_id]->add_child($row);
						$id_assoc[(int)$parent_id]->add_weight($row->get_weight()/15, 'propagating child weight');
					}
				}
		}
		*/
		$sorter = new ResourceChildSorter($placed);
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
		$hubtype = 'resource';

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
		if ($type == 'resource')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT #__resources.*, #__resource_types.type as typeName FROM #__resources
					LEFT JOIN #__resource_types
					ON #__resource_types.id = #__resources.type
					WHERE #__resources.id = {$id};";

				$sql = "SELECT * FROM #__resources WHERE id={$id};";
				$row = $db->setQuery($sql)->query()->loadObject();

				// Get the name of the author
				$sql1 = "SELECT name, authorid  FROM #__author_assoc WHERE subid={$id} AND subtable='resources';";
				$authorList = $db->setQuery($sql1)->query()->loadAssocList();

				$owners = array();
				$authors = array();
				foreach ($authorList as $author)
				{
					if ($author['authorid'] > 0)
					{
						array_push($owners, $author['authorid']);
					}
					array_push($authors, $author['name']);
				}

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'resources';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();

				if (!is_object($row) || empty($row))
				{
					return;
				}
				// Determine the path
				if ($row->alias != '' && strtolower($row->type) != 'tool')
				{
					$path = '/resources/' . $row->alias;
				}
				elseif ($row->alias != '' && strtolower($row->type) == 'tool')
				{
					$path = '/tool/' . $row->alias;
				}
				else
				{
					$path = '/resources/' . $row->id;
				}

				// Public condition
				if ($row->published == 1 && $row->access == 0 && $row->standalone == 1)
				{
					$access_level = 'public';
				}
				// Registered condition
				elseif ($row->published == 1 && $row->access == 1 && $row->standalone == 1)
				{
					$access_level = 'registered';
				}
				// Default private
				else
				{
					$access_level = 'private';
				}

				// Who is the owner
				if (isset($row->group_owner) && $row->group_owner != '')
				{
					$owner_type = 'group';
					$owner = $row->group_owner;
				}
				else
				{
					$owner_type = 'user';
					$owner = $owners;
				}

				// Get children
				$sql3 = "SELECT title AS childTitle, path FROM jos_resources
				JOIN jos_resource_assoc
				ON jos_resource_assoc.child_id = jos_resources.id
				WHERE jos_resource_assoc.parent_id = {$id} AND standalone = 0 AND published = 1;";
				$children = $db->setQuery($sql3)->query()->loadAssocList();

				$fileData = '';
				$content = '';
				foreach ($children as $child)
				{
					if (isset($fileScan) && $fileScan == true)
					{
						// Call the helper to read the file
						//$fileData .= /Helper/FileScan::scan($child['path']) . ' ';
					}

					$content .= $child['childTitle'] . ' ';
				}

				// Get the title
				$title = $row->title;

				// Build the description, clean up text
				$content .= $row->introtext . ' ' . $row->fulltxt . ' ' . $fileData;
				$content = preg_replace('/<[^>]*>/', ' ', $content);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->hubtype = $type;
				$record->title = $title;
				$record->description = $description;
				$record->author = $authors;
				$record->tags = $tags;
				$record->path = $path;
				$record->access_level = $access_level;
				$record->owner = $owner;
				$record->owner_type = $owner_type;

				// Return the formatted record
				return $record;
			}
			else
			{
				$db = App::get('db');
				$sql = "SELECT id FROM #__resources WHERE standalone=1;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
	}

}

