<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
use Components\Resources\Models\Entry;

defined('_HZEXEC_') or die();

/**
 * Tags plugin class for resources
 */
class plgTagsResources extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 * @return  void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		include_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';
	}

	/**
	 * Retrieve records for items tagged with specific tags
	 * 
	 * @param   array    $tags        Tags to match records against
	 * @param   mixed    $limit       SQL record limit
	 * @param   integer  $limitstart  SQL record limit start
	 * @param   string   $sort        The field to sort records by
	 * @param   mixed    $areas       An array or string of areas that should retrieve records
	 * @return  mixed    Returns integer when counting records, array when retrieving records
	 */
	public function onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)
	{
		$response = array(
			'name'     => $this->_name,
			'title'    => Lang::txt('PLG_TAGS_RESOURCES'),
			'total'    => 0,
			'results'  => null,
			'sql'      => '',
			'children' => array()
		);

		$database = App::get('db');

		foreach (\Components\Resources\Models\Type::getMajorTypes() as $category)
		{
			$response['children'][$category->alias] = array(
				'name'     => $category->alias,
				'title'    => $category->type,
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
			$filters['type'] = $t['id'];

			// Execute a count query for each area/category
			$database->setQuery($this->_buildPluginQuery($filters));
			$response['children'][$k]['total'] = $database->loadResult();
			$response['total'] += $response['children'][$k]['total'];
		}

		if ($areas && ($areas == $response['name'] || isset($response['children'][$areas])))
		{
			// Push some CSS and JS to the tmeplate that may be needed
			\Hubzero\Document\Assets::addComponentStylesheet('com_resources');

			$filters['select']     = 'records';
			$filters['limit']      = $limit;
			$filters['limitstart'] = $limitstart;
			$filters['sortby']     = ($sort) ? $sort : 'date';

			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			if (isset($response['children'][$areas]))
			{
				$filters['type'] = $response['children'][$areas]['id'];

				$database->setQuery($this->_buildPluginQuery($filters));
				$response['children'][$areas]['results'] = $database->loadObjectList();
			}
			else
			{
				unset($filters['type']);

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
				$filters['type'] = $response['children'][$areas]['id'];

				$response['children'][$key]['sql'] = $this->_buildPluginQuery($filters);
			}
			else
			{
				unset($filters['type']);
				$response['sql'] = $this->_buildPluginQuery($filters);
			}
		}

		return $response;
	}

	/**
	 * Build a database query
	 *
	 * @param   array   $filters  Options for building the query
	 * @return  string  SQL
	 */
	private function _buildPluginQuery($filters=array())
	{
		$database = App::get('db');

		$rt = \Components\Resources\Models\Type::blank();

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
			$query = "SELECT DISTINCT r.id, r.title, r.alias, r.introtext AS itext, r.fulltxt AS ftext, r.published AS state, r.created, r.created_by, r.modified, r.publish_up, r.publish_down,
					CONCAT('index.php?option=com_resources&id=', r.id) AS href, 'resources' AS section ";
			if (isset($filters['tags']))
			{
				$query .= ", COUNT(DISTINCT t.tagid) AS uniques ";
			}
			$query .= ", r.params, r.rating AS rcount, r.type AS data1, r.access AS data2, r.ranking data3 ";
		}
		$query .= "FROM `#__resources` AS r ";
		$query .= "LEFT JOIN " . $rt->getTableName() . " AS rt ON r.type=rt.id ";
		if (isset($filters['tag']))
		{
			$query .= ", `#__tags_object` AS t, `#__tags` AS tg ";
		}
		if (isset($filters['tags']))
		{
			$query .= ", `#__tags_object` AS t ";
		}
		$query .= "WHERE r.standalone=1 ";
		if (User::isGuest() || (isset($filters['authorized']) && !$filters['authorized']))
		{
			$query .= "AND r.published=1 AND r.access<4 ";
		}
		if (isset($filters['tag']))
		{
			$query .= "AND t.objectid=r.id AND t.tbl='resources' AND t.tagid=tg.id AND (tg.tag=" . $database->quote($filters['tag']) . " OR tg.alias=" . $database->quote($filters['tag']) . ") ";
		}
		if (isset($filters['tags']))
		{
			$ids = implode(',', $filters['tags']);
			$query .= "AND t.objectid=r.id AND t.tbl='resources' AND t.tagid IN (" . $ids . ") ";
		}
		if (isset($filters['type']) && $filters['type'] != '') {
			$query .= "AND r.type=" . $filters['type'] . " ";
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
					case 'title':
						$query .= 'title ASC, publish_up DESC';
						break;
					case 'rating':
						$query .= "rating DESC, times_rated DESC";
						break;
					case 'ranking':
						$query .= "ranking DESC";
						break;
					case 'relevance':
						$query .= "relevance DESC";
						break;
					case 'users':
					case 'usage':
						$query .= "users DESC";
						break;
					case 'jobs':
						$query .= "jobs DESC";
						break;
					case 'date':
					default:
						$query .= 'publish_up DESC';
						break;
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
	 * @param   object  $row  Database row
	 * @return  string  HTML
	 */
	public static function out($row)
	{
		$row = Entry::all()->whereEquals('id', $row->id)->row();

		// Get the component params and merge with resource params
		$config = Component::params('com_resources');

		$view = new \Hubzero\Component\View(array(
			'base_path' => Component::path('com_resources') . '/site',
			'name'      => 'browse',
			'layout'    => 'item'
		));

		$view->set('line', $row)
			->set('option', 'com_resources')
			->set('config', $config)
			->set('supported', array());

		return $view->loadTemplate();
	}
}
