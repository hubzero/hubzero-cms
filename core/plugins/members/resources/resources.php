<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for resources
 */
class plgMembersResources extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Resource areas
	 *
	 * @var array
	 */
	private $_areas = null;

	/**
	 * Resource categories
	 *
	 * @var array
	 */
	private $_cats  = null;

	/**
	 * Record count
	 *
	 * @var integer
	 */
	private $_total = null;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  Event observer
	 * @param   array   $config    Optional config values
	 * @return  void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		include_once \Component::path('com_resources') . DS . 'models' . DS . 'entry.php';
	}

	/**
	 * Return a list of categories
	 *
	 * @return  array
	 */
	public function onMembersContributionsAreas()
	{
		$areas = $this->_areas;
		if (is_array($areas))
		{
			return $areas;
		}

		if (!$this->_cats)
		{
			// Get categories
			$this->_cats = \Components\Resources\Models\Type::getMajorTypes();
		}
		$categories = $this->_cats;

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		foreach ($categories as $category)
		{
			$cats[$category->alias] = $category->type;
		}

		$areas = array(
			'resources' => $cats
		);
		$this->_areas = $areas;
		return $areas;
	}

	/**
	 * Build SQL for returning the count of the number of contributions
	 *
	 * @param   string  $user_id   Field to join on user ID
	 * @param   string  $username  Field to join on username
	 * @return  string
	 */
	public function onMembersContributionsCount($user_id='m.uidNumber', $username='m.username')
	{
		$query = "SELECT COUNT(R.id) FROM `#__resources` AS R, `#__author_assoc` AS AA WHERE AA.authorid=" . $user_id . " AND R.id = AA.subid AND AA.subtable = 'resources' AND R.published=1 AND R.standalone=1";
		return $query;
	}

	/**
	 * Return either a count or an array of the member's contributions
	 *
	 * @param   object   $member      Current member
	 * @param   string   $option      Component name
	 * @param   integer  $limit       Number of record to return
	 * @param   integer  $limitstart  Record return start
	 * @param   string   $sort        Field to sort records on
	 * @param   array    $areas       Areas to return data for
	 * @return  array
	 */
	public function onMembersContributions($member, $option, $limit, $limitstart, $sort, $areas=null)
	{
		$database = App::get('db');

		if (is_array($areas) && $limit)
		{
			$ars = $this->onMembersContributionsAreas();
			if (!isset($areas[$this->_name])
			 && !in_array($this->_name, $areas)
			 && !array_intersect($areas, array_keys($ars['resources'])))

			{
				return array();
			}
		}

		// Do we have a member ID?
		if ($member instanceof \Hubzero\User\User)
		{
			if (!$member->get('id'))
			{
				return array();
			}
			else
			{
				$uidNumber = $member->get('id');
			}
		}
		else
		{
			if (!$member->uidNumber)
			{
				return array();
			}
			else
			{
				$uidNumber = $member->uidNumber;
			}
		}

		// Build query
		$filters = array(
			'author'        => $uidNumber,
			'notauthorrole' => 'submitter',
			'sortby'        => $sort,
			'usergroups'    => array(),
			'standalone'    => 1,
			'published'     => 1
		);

		if ($filters['sortby'] == 'date')
		{
			$filters['sortby'] = 'created';
		}
		if ($filters['sortby'] == 'usage')
		{
			$filters['sortby'] = 'users';
		}

		/*$groups = $member->groups();

		if (!empty($groups))
		{
			foreach ($groups as $group)
			{
				if ($group->regconfirmed)
				{
					$filters['usergroups'][] = $group->cn;
				}
			}
		}
		$filters['usergroups'] = array_unique($filters['usergroups']);*/

		// If the visiting user is NOT the same as the member
		// we want to restrict what they can see
		if (User::get('id') != $member->get('id'))
		{
			//$filters['published'] = 1;
			$filters['access'] = array(0, 3);
			if (!\User::isGuest())
			{
				$filters['access'][] = 1;
			}
		}

		// Get categories
		$categories = $this->_cats;
		if (!$categories)
		{
			$categories = \Components\Resources\Models\Type::getMajorTypes();
		}

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		foreach ($categories as $category)
		{
			$cats[$category->alias] = array();
			$cats[$category->alias]['id'] = $category->id;
		}

		if ($limit)
		{
			if ($this->_total != null)
			{
				$total = 0;
				$t = $this->_total;
				foreach ($t as $l)
				{
					$total += $l;
				}
			}
			if ($total == 0)
			{
				return array();
			}

			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;

			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			if (count($areas) == 1 && !isset($areas['resources']))
			{
				$filters['type'] = (isset($cats[$areas[0]])) ? $cats[$areas[0]]['id'] : 0;
				if (!$filters['type'])
				{
					unset($filters['type']);
				}
			}

			// Get results
			$query = \Components\Resources\Models\Entry::allWithFilters($filters);

			if (isset($filters['sortby']) && ($filters['sortby'] == 'usage' || $filters['sortby'] == 'users'))
			{
				include_once \Component::path('com_resources') . DS . 'models' . DS . 'stat.php';

				$s = \Components\Resources\Models\Stat::blank()->getTableName();

				$query->select('(SELECT rs.users FROM ' . $s . ' AS rs WHERE rs.resid=' . $query->getTableName() . '.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1)', 'users');
			}

			$rows = $query
				->limit($filters['limit'])
				->start($filters['limitstart'])
				->order($filters['sortby'], 'desc')
				->rows();

			// Return the results
			return $rows;
		}
		else
		{
			// Get a count
			$counts = array();
			$ares = $this->onMembersContributionsAreas();
			foreach ($ares as $area => $val)
			{
				if (is_array($val))
				{
					$i = 0;
					foreach ($val as $a => $t)
					{
						if ($limitstart == -1)
						{
							$counts[] = 0;

							if ($i == 0)
							{
								$counts[] = self::allWithFilters($filters)->total();
							}
						}
						else
						{
							$filters['type'] = $cats[$a]['id'];

							// Execute a count query for each area/category
							$counts[] = self::allWithFilters($filters)->total();
						}
						$i++;
					}
				}
			}

			// Return the counts
			$this->_total = $counts;
			return $counts;
		}
	}

	/**
	 * Include needed libraries and push scripts and CSS to the document
	 *
	 * @param   array  $filters
	 * @return  object
	 */
	public static function allWithFilters($filters = array())
	{
		$query = \Components\Resources\Models\Entry::all();

		$r = $query->getTableName();
		$a = \Components\Resources\Models\Author::blank()->getTableName();

		$query
			->select($r . '.*');

		if (isset($filters['standalone']))
		{
			$query->whereEquals($r . '.standalone', $filters['standalone']);
		}

		if (isset($filters['published']))
		{
			$query->whereIn($r . '.published', (array) $filters['published']);
		}

		if (isset($filters['group']))
		{
			$query->whereEquals($r . '.group_owner', (string) $filters['group']);
		}

		if (isset($filters['type']))
		{
			if (!is_numeric($filters['type']))
			{
				$filters['type'] = Type::oneByAlias($filters['type'])->get('id');
			}
			$query->whereEquals($r . '.type', $filters['type']);
		}

		if (isset($filters['tag']) && $filters['tag'])
		{
			$to = \Components\Tags\Models\Objct::blank()->getTableName();
			$tg = \Components\Tags\Models\Tag::blank()->getTableName();

			$cloud = new \Components\Resources\Helpers\Tags();
			$tags = $cloud->parse($filters['tag']);

			$query->join($to, $to . '.objectid', $r . '.id');
			$query->join($tg, $tg . '.id', $to . '.tagid', 'inner');
			$query->whereEquals($to . '.tbl', 'resources');
			$query->whereIn($tg . '.tag', $tags);
		}

		if (isset($filters['search']))
		{
			$query->whereLike($r . '.title', $filters['search'], 1)
				->orWhereLike($r . '.fulltxt', $filters['search'], 1)
				->resetDepth();
		}

		if (isset($filters['created_by']))
		{
			$query->whereEquals($r . '.created_by', $filters['created_by']);
		}

		if (isset($filters['author']))
		{
			$query
				->join($a, $a . '.subid', $r . '.id', 'left')
				->whereEquals($a . '.subtable', 'resources')
				->whereEquals($a . '.authorid', $filters['author']);

			if (isset($filters['notauthorrole']))
			{
				$query->where($a . '.role', '!=', $filters['notauthorrole']);
			}
		}

		if (isset($filters['access']) && !empty($filters['access']))
		{
			if (!is_array($filters['access']) && !is_numeric($filters['access']))
			{
				switch ($filters['access'])
				{
					case 'public':
						$filters['access'] = 0;
						break;
					case 'protected':
						$filters['access'] = 3;
						break;
					case 'private':
						$filters['access'] = 4;
						break;
					case 'all':
					default:
						$filters['access'] = array(0, 1, 2, 3, 4);
						break;
				}
			}

			if (isset($filters['usergroups']) && !empty($filters['usergroups']))
			{
				$query->whereIn($r . '.access', (array) $filters['access'], 1)
					->orWhereIn($r . '.group_owner', (array) $filters['usergroups'], 1)
					->resetDepth();
			}
			else
			{
				$query->whereIn($r . '.access', (array) $filters['access']);
			}
		}
		elseif (isset($filters['usergroups']) && !empty($filters['usergroups']))
		{
			$query->whereIn($r . '.group_owner', (array) $filters['usergroups']);
		}

		if (isset($filters['now']))
		{
			$query->whereEquals($r . '.publish_up', '0000-00-00 00:00:00', 1)
				->orWhere($r . '.publish_up', '<=', $filters['now'], 1)
				->resetDepth()
				->whereEquals($r . '.publish_down', '0000-00-00 00:00:00', 1)
				->orWhere($r . '.publish_down', '>=', $filters['now'], 1)
				->resetDepth();
		}

		if (isset($filters['startdate']) && $filters['startdate'])
		{
			$query->where($r . '.publish_up', '>', $filters['startdate']);
		}
		if (isset($filters['enddate']) && $filters['enddate'])
		{
			$query->where($r . '.publish_up', '<', $filters['enddate']);
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
		$row->set('typetitle', $row->type->get('type'));

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

	/**
	 * Include needed libraries and push scripts and CSS to the document
	 *
	 * @return     void
	 */
	public static function documents()
	{
		// Push some CSS and JS to the tmeplate that may be needed
		\Hubzero\Document\Assets::addComponentStylesheet('com_resources');

		include_once \Component::path('com_resources') . DS . 'helpers' . DS . 'usage.php';
	}
}
