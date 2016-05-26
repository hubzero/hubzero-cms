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

/**
 * Short description for 'ResourceChildSorter'
 *
 * Long description (if any) ...
 */
class ResourceChildSorter
{
	/**
	 * Description for 'order'
	 *
	 * @var array
	 */
	private $order;

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $order Parameter description (if any) ...
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

/************************************************
 *
 * HubSearch Required Methods
 * @author Kevin Wojkovich <kevinw@purdue.edu>
 *
 ***********************************************/

	/****************************
	Query-time / General Methods
	****************************/

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

	public function onGetModel($type = '')
	{
		if ($type == 'resource')
		{
			return new Resource;
		}
	}
	/*********************
		Index-time methods
	*********************/
	/**
	 * onProcessFields - Set SearchDocument fields which have conditional processing
	 *
	 * @param mixed $type 
	 * @param mixed $row
	 * @access public
	 * @return void
	 */
	public function onProcessFields($type, $row)
	{
		if ($type == 'resource')
		{
			/*
			// Determine the author of the Entry
			$owner = User::getInstance($row->created_by);
			$authorArr = array();
			array_push($authorArr, $user->name);
			*/

			// Instantiate new $fields object
			$fields = new stdClass;

			// Calculate Permissions

			/** @FIXME legacy access values
			/*  0 - Public
			/*	1 - Registered
			/*  2 - Special
			/*  3 - Protected
			/*  4 - Private
			****************************************/

			// Public condition
			if ($row->state == 1 && $row->access == 0 && $row->standalone == 1)
			{
				$fields->access_level = 'public';
			}
			// Registered condition
			elseif ($row->state == 1 && $row->access == 1 && $row->standalone == 1)
			{
				$fields->access_level = 'registered';
			}
			// Default private
			else
			{
				$fields->access_level = 'private';
			}

			// Who is the owner
			if (isset($row->group_owner) && $row->scope != 'group')
			{
				$fields->owner_type = 'group';
				$fields->owner = $row->group_owner;
			}
			else
			{
				$fields->owner_type = 'user';
				$fields->owner = $row->created_by;
			}

			// Build out path
			$path = '/resources/';
			if (isset($row->alias) && $row->alias != '')
			{
				$path .= $row->alias;
			}
			else
			{
				$path .= $row->id;
			}

			$fields->url = $path;

			// Extract author names
			$authors  = array();
			foreach ($row->authors() as $author)
			{
				array_push($authors, $author->name);
			}
			$fields->author = $authors;

			$fields->tags = $row->tags('list');

			$fields->title = $row->title;
			$fields->alias = $row->alias;

			$abstract = $row->introtext;
			$abstract = html_entity_decode($abstract);
			$abstract = strip_tags($abstract);
			$fields->abstract = $abstract;

			$fulltext = $row->fulltxt;
			$fulltext = html_entity_decode($fulltext);
			$fields->fulltext = $fulltext;

			// Format the date for SOLR
			$date = Date::of($row->publish_up)->format('Y-m-d');
			$date .= 'T';
			$date .= Date::of($row->publish_up)->format('h:m:s') . 'Z';
			$fields->date = $date;

			$fields->description = $abstract;
			$fields->doi = $row->master_doi;

			return $fields;
		}
	}
}

