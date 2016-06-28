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

	public $hubtype = 'publication';

	/**
	 * onGetTypes - Announces the available hubtype
	 *
	 * @param mixed $type
	 * @access public
	 * @return void
	 */
	public function onGetTypes($type = null)
	{
		if (isset($type) && $type == $this->hubtype)
		{
			return $this->hubtype;
		}
		elseif (!isset($type))
		{
			return $this->hubtype;
		}
	}

	/**
	 * onGetModel 
	 * 
	 * @param string $type 
	 * @access public
	 * @return void
	 */
	public function onGetModel($type = '')
	{
		if ($type == $this->hubtype)
		{
			return new Publication;
		}
	}

	/**
	 * onProcessFields - Set SearchDocument fields which have conditional processing
	 *
	 * @param mixed $type 
	 * @param mixed $row
	 * @access public
	 * @return void
	 */
	public function onProcessFields($type, $row, &$db)
	{
		if ($type == $this->hubtype)
		{
			// Instantiate new $fields object
			$fields = new stdClass;
			$fields->author = array();

			// Find the latest version of the publication
			$mainVersion = $row->versions()->where('main', '=', '1')->row();

			// Grab the authors
			$authors = $mainVersion->authors()->select('name')->rows()->toArray();

			// Place in the bucket
			foreach ($authors as $author)
			{
				array_push($fields->author, $author['name']);
			}

			// Format the date for SOLR
			$date = Date::of($row->created)->format('Y-m-d');
			$date .= 'T';
			$date .= Date::of($row->created)->format('h:m:s') . 'Z';
			$fields->date = $date;

			// Title is required
			$fields->title = $mainVersion->title;


			$fields->description = strip_tags(htmlspecialchars_decode($mainVersion->description));
			$fields->abstract = $mainVersion->abstract;
			$fields->doi = $mainVersion->doi;


			/**
			 * Each entity should have an owner. 
			 * Owner type can be a user or a group,
			 * where the owner is the ID of the user or group
			 **/
			if ($row->group_owner == 0)
			{
				$fields->owner_type = 'user';
				$fields->owner = $row->created_by;
			}
			else
			{
				$fields->owner_type = 'group';
				$fields->owner = $row->group_owner;
			}

			/** Publications States
			 0 - unpublished
			 1 - published (viable by access level)
			 2 - deleted
			 3 - draft
			 4 - ready
			 5 - pending approval
			 6 - ??
			 7 - changes required
			 **/

			 /** Publication Access Levels
			  0 - Public
				1 - Registered
				2 - Private 
				**/

			/**
			 * A document should have an access level.
			 * This value can be:
			 *  public - all users can view
			 *  registered - only registered users can view
			 *  private - only owners (set above) can view
			 **/
			if ($mainVersion->access == 0 && $mainVersion->state == 1)
			{
				$fields->access_level = 'public';
			}
			else
			{
				$fields->access_level = 'private';
			}

			// The URL this document is accessible through
			$fields->url = '/publications/' . $row->id;

			return $fields;
		}
	}
}

