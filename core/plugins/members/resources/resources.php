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
	public function onMembersContributions($member, $option, $limit=0, $limitstart=0, $sort, $areas=null)
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
			}

			// Get results
			$query = \Components\Resources\Models\Entry::allWithFilters($filters);

			$rows = $query
				->limit($filters['limit'])
				->start($filters['limitstart'])
				->order($query->getTableName() . '.created', 'desc')
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
								$counts[] = \Components\Resources\Models\Entry::allWithFilters($filters)->total();
							}
						}
						else
						{
							$filters['type'] = $cats[$a]['id'];

							// Execute a count query for each area/category
							$counts[] = \Components\Resources\Models\Entry::allWithFilters($filters)->total();
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

		include_once \Component::path('com_resources') . DS . 'helpers' . DS . 'helper.php';
		include_once \Component::path('com_resources') . DS . 'helpers' . DS . 'usage.php';
	}
}
