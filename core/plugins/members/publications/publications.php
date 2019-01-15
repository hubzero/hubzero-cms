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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		/*$areas = $this->_areas;
		if (is_array($areas))
		{
			return $areas;
		}

		if (!$this->_cats)
		{
			// Get categories
			$this->_cats = Components\Publications\Models\Orm\Category::all()
				->whereEquals('state', Components\Publications\Models\Orm\Category::STATE_PUBLISHED)
				->order('name', 'asc')
				->rows();
		}
		$categories = $this->_cats;

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		foreach ($categories as $category)
		{
			$cats[$category->alias] = $category->name;
		}

		$areas = array(
			'publications' => $cats
		);
		$this->_areas = $areas;*/

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
			/*if ($this->_total != null)
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
			}*/

			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;

			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			/*if (count($areas) == 1 && !isset($areas['publications']))
			{
				$filters['type'] = (isset($cats[$areas[0]])) ? $cats[$areas[0]]['id'] : 0;
				if (!$filters['type'])
				{
					unset($filters['type']);
				}
			}*/

			// Get results
			$query = self::allWithFilters($filters);

			/*if (isset($filters['sortby']) && ($filters['sortby'] == 'usage' || $filters['sortby'] == 'users'))
			{
				include_once \Component::path('com_resources') . DS . 'models' . DS . 'stat.php';

				$s = \Components\Resources\Models\Stat::blank()->getTableName();

				$query->select('(SELECT rs.users FROM ' . $s . ' AS rs WHERE rs.resid=' . $query->getTableName() . '.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1)', 'users');
			}*/

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

			// Get a count
			/*$counts = array();
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
			return $counts;*/
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

			/*if (isset($filters['notauthorrole']))
			{
				$query->where($a . '.role', '!=', $filters['notauthorrole']);
			}*/
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
				->orWhere($r . '.published_up', '<=', $filters['now'], 1)
				->resetDepth()
				->whereEquals($r . '.published_down', '0000-00-00 00:00:00', 1)
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
		//$row->set('typetitle', $row->type->get('type'));
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
				$thedate = $row->get('created'); //$line->created();
				break;
			case 2:
				$thedate = $row->get('modified'); //$line->modified();
				break;
			case 3:
				$thedate = $row->get('published_up'); //$line->published();
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
	 * @return     void
	 */
	public static function documents()
	{
		// Push some CSS and JS to the tmeplate that may be needed
		Hubzero\Document\Assets::addComponentStylesheet('com_publications');

		include_once Component::path('com_publications') . '/helpers/usage.php';
	}
}
