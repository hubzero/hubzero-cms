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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Tags plugin class for publications
 */
class plgTagsPublications extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param      object &$subject The object to observe
	 * @param      array  $config   An optional associative array of configuration settings.
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		include_once(Component::path('com_publications') . DS . 'tables' . DS . 'category.php');
		include_once(Component::path('com_publications') . DS . 'tables' . DS . 'publication.php');
	}

	/**
	 * Retrieve records for items tagged with specific tags
	 *
	 * @param      array   $tags       Tags to match records against
	 * @param      mixed   $limit      SQL record limit
	 * @param      integer $limitstart SQL record limit start
	 * @param      string  $sort       The field to sort records by
	 * @param      mixed   $areas      An array or string of areas that should retrieve records
	 * @return     mixed Returns integer when counting records, array when retrieving records
	 */
	public function onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)
	{
		$response = array(
			'name'     => $this->_name,
			'title'    => Lang::txt('PLG_TAGS_PUBLICATIONS'),
			'total'    => 0,
			'results'  => null,
			'sql'      => '',
			'children' => array()
		);

		$database = App::get('db');
		$rt = new \Components\Publications\Tables\Category($database);
		foreach ($rt->getCategories() as $category)
		{
			$response['children'][$category->url_alias] = array(
				'name'     => $category->url_alias,
				'title'    => $category->name,
				'total'    => 0,
				'results'  => null,
				'sql'      => '',
				'id'       => $category->id
			);
		}

		if (empty($tags))
		{
			return $response;
		}

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->get('id');
		}

		// Instantiate some needed objects
		$rr = new \Components\Publications\Tables\Publication($database);

		// Build query
		$filters = array();
		$filters['tags'] = $ids;
		$filters['now'] = Date::toSql();
		$filters['sortby'] = ($sort) ? $sort : 'ranking';
		$filters['authorized'] = false;

		$filters['usergroups'] = \Hubzero\User\Helper::getGroups((int)User::get('id', 0), 'all');

		$filters['select'] = 'count';

		foreach ($response['children'] as $k => $t)
		{
			$filters['category'] = $t['id'];

			// Execute a count query for each area/category
			$database->setQuery($this->_buildPluginQuery($filters));
			$response['children'][$k]['total'] = $database->loadResult();
			$response['total'] += $response['children'][$k]['total'];
		}

		if ($areas && ($areas == $response['name'] || isset($response['children'][$areas])))
		{
			$filters['select']     = 'records';
			$filters['limit']      = $limit;
			$filters['limitstart'] = $limitstart;
			$filters['sortby']     = ($sort) ? $sort : 'date';

			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			if (isset($response['children'][$areas]))
			{
				$filters['category'] = $response['children'][$areas]['id'];

				$database->setQuery($this->_buildPluginQuery($filters));
				$response['children'][$areas]['results'] = $database->loadObjectList();
			}
			else
			{
				unset($filters['category']);

				$database->setQuery($this->_buildPluginQuery($filters));
				$response['results'] = $database->loadObjectList();
			}
		}
		else
		{
			$filters['select']     = 'records';
			$filters['limit']      = 'all';
			$filters['limitstart'] = $limitstart;
			$filters['sortby']     = ($sort) ? $sort : 'date';

			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			if (isset($response['children'][$areas]))
			{
				$filters['category'] = $response['children'][$areas]['id'];

				$response['children'][$key]['sql'] = $this->_buildPluginQuery($filters);
			}
			else
			{
				unset($filters['category']);
				$response['sql'] = $this->_buildPluginQuery($filters);
			}
		}

		return $response;
	}

	/**
	 * Build a database query
	 *
	 * @param      array $filters Options for building the query
	 * @return     string SQL
	 */
	private function _buildPluginQuery($filters=array())
	{
		$database = App::get('db');

		include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'category.php');
		$rt = new \Components\Publications\Tables\Category($database);

		if (isset($filters['select']) && $filters['select'] == 'count')
		{
			if (isset($filters['tags']))
			{
				$query = "SELECT count(f.id) FROM (SELECT r.id, COUNT(DISTINCT t.tagid) AS uniques ";
			}
			else
			{
				$query = "SELECT count(DISTINCT r.id) ";
			}
		}
		else
		{
			$query = "SELECT DISTINCT r.id, V.title, V.version_number as alias,
					V.abstract as itext, V.id as ftext, V.state AS state, V.created, V.created_by,
					V.modified, V.published_up as publish_up, V.published_down as publish_down,
					CONCAT('index.php?option=com_publications&id=', r.id) AS href, 'publications' AS section ";
			if (isset($filters['tags']))
			{
				$query .= ", COUNT(DISTINCT t.tagid) AS uniques ";
			}
			$query .= ", V.params, r.rating AS rcount, r.category AS data1, rt.name AS data2, r.ranking data3 ";
		}
		$query .= "FROM #__publication_versions as V, #__publications AS r ";
		$query .= "LEFT JOIN " . $rt->getTableName() . " AS rt ON r.category=rt.id";
		if (isset($filters['tag']))
		{
			$query .= ", #__tags_object AS t, #__tags AS tg ";
		}
		if (isset($filters['tags']))
		{
			$query .= ", #__tags_object AS t ";
		}
		$query .= "WHERE V.publication_id=r.id AND V.state=1 AND V.main = 1 ";
		if (isset($filters['tag']))
		{
			$query .= "AND t.objectid=r.id AND t.tbl='publications' AND t.tagid=tg.id AND (tg.tag='" . $filters['tag'] . "' OR tg.alias='" . $filters['tag'] . "') ";
		}
		if (isset($filters['tags']))
		{
			$ids = implode(',', $filters['tags']);
			$query .= "AND t.objectid=r.id AND t.tbl='publications' AND t.tagid IN (" . $ids . ") ";
		}
		if (isset($filters['category']) && $filters['category'] != '') {
			$query .= "AND r.category=" . $filters['category'] . " ";
		}

		if (isset($filters['tags']))
		{
			$query .= " GROUP BY r.id HAVING uniques=" . count($filters['tags']) . " ";
		}
		if (isset($filters['select']) && $filters['select'] != 'count')
		{
			if (isset($filters['sortby']))
			{
				if (isset($filters['groupby']))
				{
					$query .= "GROUP BY r.id ";
				}
				$query .= "ORDER BY ";
				switch ($filters['sortby'])
				{
					case 'title':   $query .= 'title ASC, publish_up DESC';    break;
					case 'rating':  $query .= "rating DESC, times_rated DESC"; break;
					case 'ranking': $query .= "ranking DESC";                  break;
					case 'relevance': $query .= "relevance DESC";              break;
					case 'users':
					case 'usage':   $query .= "users DESC";                    break;
					case 'jobs':    $query .= "jobs DESC";                     break;
					case 'date':
					default: $query .= 'publish_up DESC';               break;
				}
			}
			if (isset($filters['limit']) && $filters['limit'] != 'all')
			{
				$query .= " LIMIT " . $filters['limitstart'] . "," . $filters['limit'];
			}
		}
		if (isset($filters['select']) && $filters['select'] == 'count')
		{
			if (isset($filters['tags']))
			{
				$query .= ") AS f";
			}
		}

		return $query;
	}

	/**
	 * Static method for formatting results
	 *
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public static function out($row)
	{
		include_once(Component::path('com_publications') . DS . 'tables' . DS . 'author.php');
		require_once(Component::path('com_publications') . DS . 'helpers' . DS . 'html.php');

		$row->href = Route::url('index.php?option=com_publications&id=' . $row->id);

		$database = App::get('db');

		// Get version authors
		$pa = new \Components\Publications\Tables\Author($database);
		$authors = $pa->getAuthors($row->ftext);

		// Get the component params
		$config = Component::params('com_publications');

		$row->rating   = $row->rcount;
		$row->category = $row->data1;
		$row->area     = $row->data2;
		$row->ranking  = $row->data3;

		// Set the display date
		switch ($config->get('show_date'))
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));    break;
			case 2: $thedate = Date::of($row->modified)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));    break;
			case 3: $thedate = Date::of($row->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));    break;
		}

		if (strstr($row->href, 'index.php'))
		{
			$row->href = Route::url($row->href);
		}

		// Start building the HTML
		$html  = "\t".'<li class="';
		$html .= 'publication">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '/?v=' . $row->alias . '">'
			. stripslashes($row->title) . '</a></p>' . "\n";

		$html .= "\t\t" . '<p class="details">' . $thedate . ' <span>|</span> ' . $row->area;
		if ($authors)
		{
			$html .= ' <span>|</span> ' . Lang::txt('PLG_TAGS_PUBLICATIONS_CONTRIBUTORS')
				. ' ' . stripslashes(\Components\Publications\Helpers\Html::showContributors( $authors, true, false ));
		}
		$html .= '</p>' . "\n";
		if ($row->itext)
		{
			$html .= "\t\t" . '<p>' . \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->itext)), 200) . '</p>' . "\n";
		}

		$html .= "\t\t" . '<p class="href">' . Request::base() . trim($row->href . '/?v=' . $row->alias, '/') . '</p>' . "\n";
		$html .= "\t" . '</li>'."\n";

		// Return output
		return $html;

	}
}
