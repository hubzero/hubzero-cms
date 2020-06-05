<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for publications
 */
class plgMembersPublications extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Resource areas
	 *
	 * @var  array
	 */
	private $_areas = null;

	/**
	 * Types
	 *
	 * @var  array
	 */
	private $_cats  = null;

	/**
	 * Record count
	 *
	 * @var  integer
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

		include_once Component::path('com_publications') . '/models/orm/publication.php';
		include_once Component::path('com_publications') . '/tables/author.php';
		include_once Component::path('com_publications') . '/helpers/html.php';
	}

	/**
	 * Return a list of categories
	 *
	 * @return  array
	 */
	public function onMembersContributionsAreas()
	{
		$areas = array(
			'publications' => Lang::txt('PLG_MEMBERS_PUBLICATIONS')
		);

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
		$query = "SELECT COUNT(R.id)
				FROM `#__publication_versions` AS R
				LEFT JOIN `#__publication_authors` AS AA ON AA.publication_version_id = R.id
				WHERE AA.user_id=" . $user_id . " AND
				AND R.state=1";
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
	public function onMembersContributions($member, $option, $limit=0, $limitstart=0, $sort, $areas=null)
	{
		$database = App::get('db');

		if (is_array($areas) && $limit)
		{
			$ars = $this->onMembersContributionsAreas();
			if (!isset($areas[$this->_name])
			 && !in_array($this->_name, $areas)
			 && !array_intersect($areas, array_keys($ars)))
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
			'published'     => Components\Publications\Models\Orm\Version::STATE_PUBLISHED
		);

		if ($filters['sortby'] == 'date')
		{
			$filters['sortby'] = 'created';
		}
		if ($filters['sortby'] == 'usage')
		{
			$filters['sortby'] = 'created'; //'users';
		}

		// If the visiting user is NOT the same as the member
		// we want to restrict what they can see
		if (User::get('id') != $member->get('id'))
		{
			//$filters['published'] = 1;
			$filters['access'] = array(0, 3);
			if (!User::isGuest())
			{
				$filters['access'][] = 1;
			}
		}

		// Get categories
		$categories = $this->_cats;
		if (!$categories)
		{
			$categories = Components\Publications\Models\Orm\Category::all()
				->whereEquals('state', Components\Publications\Models\Orm\Category::STATE_PUBLISHED)
				->order('name', 'asc')
				->rows();
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
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;

			// Get results
			$query = self::allWithFilters($filters);

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
			return self::allWithFilters($filters)->total();
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
		$query = Components\Publications\Models\Orm\Version::all();

		$r = $query->getTableName();
		$p = Components\Publications\Models\Orm\Publication::blank()->getTableName();
		$a = Components\Publications\Models\Orm\Author::blank()->getTableName();

		$query
			->select($r . '.*')
			->join($p, $p . '.id', $r . '.publication_id', 'inner');

		if (isset($filters['published']))
		{
			$query->whereIn($r . '.state', (array) $filters['published']);
		}

		if (isset($filters['type']))
		{
			if (!is_numeric($filters['type']))
			{
				$filters['type'] = Components\Publications\Models\Orm\Category::oneByAlias($filters['type'])->get('id');
			}
			$query->whereEquals($p . '.category', $filters['type']);
		}

		if (isset($filters['tag']) && $filters['tag'])
		{
			$to = Components\Tags\Models\Objct::blank()->getTableName();
			$tg = Components\Tags\Models\Tag::blank()->getTableName();

			$cloud = new Components\Publications\Helpers\Tags();
			$tags = $cloud->parse($filters['tag']);

			$query->join($to, $to . '.objectid', $r . '.id');
			$query->join($tg, $tg . '.id', $to . '.tagid', 'inner');
			$query->whereEquals($to . '.tbl', 'publications');
			$query->whereIn($tg . '.tag', $tags);
		}

		if (isset($filters['search']))
		{
			$query->whereLike($r . '.title', $filters['search'], 1)
				->orWhereLike($r . '.description', $filters['search'], 1)
				->resetDepth();
		}

		if (isset($filters['created_by']))
		{
			$query->whereEquals($r . '.created_by', $filters['created_by']);
		}

		if (isset($filters['author']))
		{
			$query
				->join($a, $a . '.publication_version_id', $r . '.id', 'left')
				->whereEquals($a . '.user_id', $filters['author']);

			if (isset($filters['notauthorrole']))
			{
				$query->where($a . '.role', 'IS', null, 'and', 1)
					->orWhere($a . '.role', '!=', $filters['notauthorrole'], 1)
					->resetDepth();
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
					->orWhereIn($p . '.group_owner', (array) $filters['usergroups'], 1)
					->resetDepth();
			}
			else
			{
				$query->whereIn($r . '.access', (array) $filters['access']);
			}
		}
		elseif (isset($filters['usergroups']) && !empty($filters['usergroups']))
		{
			$query->whereIn($p . '.group_owner', (array) $filters['usergroups']);
		}

		if (isset($filters['now']))
		{
			$query->whereEquals($r . '.published_up', '0000-00-00 00:00:00', 1)
				->orWhere($r . '.published_up', 'IS', null, 1)
				->orWhere($r . '.published_up', '<=', $filters['now'], 1)
				->resetDepth()
				->whereEquals($r . '.published_down', '0000-00-00 00:00:00', 1)
				->orWhere($r . '.published_down', 'IS', null, 1)
				->orWhere($r . '.published_down', '>=', $filters['now'], 1)
				->resetDepth();
		}

		if (isset($filters['startdate']) && $filters['startdate'])
		{
			$query->where($r . '.published_up', '>', $filters['startdate']);
		}
		if (isset($filters['enddate']) && $filters['enddate'])
		{
			$query->where($r . '.published_up', '<', $filters['enddate']);
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
		$database = App::get('db');
		$pa = new \Components\Publications\Tables\Author($database);

		// Get the component params and merge with resource params
		$config = Component::params('com_publications');

		$authors = $pa->getAuthors($row->get('id'));

		// Get parameters
		$params = clone($config);
		$rparams = new Hubzero\Config\Registry($row->get('params'));
		$params->merge($rparams);

		$show_date = 3;

		// Set the display date
		switch ($show_date)
		{
			case 0:
				$thedate = '';
				break;
			case 1:
				$thedate = $row->get('created');
				break;
			case 2:
				$thedate = $row->get('modified');
				break;
			case 3:
				$thedate = $row->get('published_up');
				break;
		}

		$thedate = Date::of($thedate)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));

		$view = new Hubzero\Component\View(array(
			'base_path' => Component::path('com_publications') . '/site',
			'name'      => 'browse',
			'layout'    => 'item'
		));
		$view->set('line', $row)
			->set('option', 'com_publications')
			->set('config', $config)
			->set('thedate', $thedate)
			->set('authors', $authors)
			->set('params', $params);

		return $view->loadTemplate();
	}

	/**
	 * Include needed libraries and push scripts and CSS to the document
	 *
	 * @return  void
	 */
	public static function documents()
	{
		// Push some CSS and JS to the tmeplate that may be needed
		Hubzero\Document\Assets::addComponentStylesheet('com_publications');

		include_once Component::path('com_publications') . '/helpers/usage.php';
	}
}
