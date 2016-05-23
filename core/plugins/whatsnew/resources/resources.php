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
 * What's New Plugin class for com_resources entries
 */
class plgWhatsnewResources extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Resource types and "all" category
	 *
	 * @var array
	 */
	private $_areas = null;

	/**
	 * Resource types
	 *
	 * @var array
	 */
	private $_cats  = null;

	/**
	 * Results total
	 *
	 * @var integer
	 */
	private $_total = null;

	/**
	 * Constructor
	 *
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function onWhatsnewAreas()
	{
		if (is_array($this->_areas))
		{
			return $this->_areas;
		}

		$categories = $this->_cats;
		if (!is_array($categories))
		{
			// Get categories
			$database = App::get('db');
			$rt = new \Components\Resources\Tables\Type($database);
			$this->_cats = $rt->getMajorTypes();
		}

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		for ($i = 0; $i < count($this->_cats); $i++)
		{
			$cats[$this->_cats[$i]->alias] = $this->_cats[$i]->type;
		}

		$this->_areas = array(
			'resources' => $cats
		);

		return $this->_areas;
	}

	/**
	 * Pull a list of records that were created within the time frame ($period)
	 *
	 * @param      object  $period     Time period to pull results for
	 * @param      mixed   $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      array   $areas      Active area(s)
	 * @param      array   $tagids     Array of tag IDs
	 * @return     array
	 */
	public function onWhatsnew($period, $limit=0, $limitstart=0, $areas=null, $tagids=array())
	{
		if (is_array($areas) && $limit)
		{
			$ars = $this->onWhatsnewAreas();
			if (!isset($areas[$this->_name])
			 && !in_array($this->_name, $areas)
			 && !array_intersect($areas, array_keys($ars['resources'])))
			{
				return array();
			}
		}

		// Do we have a time period?
		if (!is_object($period))
		{
			return array();
		}

		$database = App::get('db');

		// Instantiate some needed objects
		$rr = new \Components\Resources\Tables\Resource($database);

		// Build query
		$filters = array(
			'startdate' => $period->cStartDate,
			'enddate'   => $period->cEndDate,
			'sortby'    => 'date'
		);
		if (count($tagids) > 0)
		{
			$filters['tags'] = $tagids;
		}

		$filters['usergroups'] = \Hubzero\User\Helper::getGroups((int)User::get('id', 0), 'all');

		// Get categories
		$categories = $this->_cats;
		if (!is_array($categories))
		{
			$rt = new \Components\Resources\Tables\Type($database);
			$categories = $rt->getMajorTypes();
		}

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		for ($i = 0; $i < count($categories); $i++)
		{
			$cats[$categories[$i]->alias] = array();
			$cats[$categories[$i]->alias]['id'] = $categories[$i]->id;
		}

		$filters['authorized'] = false;

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
				if ($total == 0)
				{
					return array();
				}
			}

			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;

			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			if (count($areas) == 1 && isset($areas[0]) && $areas[0] != 'resources')
			{
				$filters['type'] = $cats[$areas[0]]['id'];
			}

			// Get results
			$database->setQuery($rr->buildPluginQuery($filters));
			$rows = $database->loadObjectList();

			// Did we get any results?
			if ($rows)
			{
				include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');

				// Loop through the results and set each item's HREF
				foreach ($rows as $key => $row)
				{
					$resourceEx = new \Components\Resources\Helpers\Helper($row->id, $database);
					$resourceEx->getContributors();

					$rows[$key]->authors = $resourceEx->contributors;

					if ($row->alias)
					{
						$rows[$key]->href = Route::url('index.php?option=com_resources&alias=' . $row->alias);
					}
					else
					{
						$rows[$key]->href = Route::url('index.php?option=com_resources&id=' . $row->id);
					}
					if ($row->itext)
					{
						$rows[$key]->text = $rows[$key]->itext;
					}
					else if ($row->ftext)
					{
						$rows[$key]->text = $rows[$key]->ftext;
					}
				}
			}

			return $rows;
		}
		else
		{
			$filters['select'] = 'count';

			// Get a count
			$counts = array();
			$ares = $this->onWhatsnewAreas();
			foreach ($ares as $area => $val)
			{
				if (is_array($val))
				{
					foreach ($val as $a=>$t)
					{
						$filters['type'] = $cats[$a]['id'];

						$database->setQuery($rr->buildPluginQuery($filters));
						$counts[] = $database->loadResult();
					}
				}
			}
			// Return the counts
			$this->_total = $counts;
			return $counts;
		}
	}

	/**
	 * Push styles and scripts to the document
	 *
	 * @return     void
	 */
	public static function documents()
	{
		\Hubzero\Document\Assets::addComponentStylesheet('com_resources');
		\Hubzero\Document\Assets::addComponentScript('com_resources');

		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'usage.php');
	}

	/**
	 * Special formatting for results
	 *
	 * @param      object $row    Database row
	 * @param      string $period Time period
	 * @return     string
	 */
	public static function out($row, $period)
	{
		$database = App::get('db');

		$helper = new \Components\Resources\Helpers\Helper($row->id, $database);

		// Instantiate a helper object
		if (!isset($row->authors))
		{
			$helper->getContributors();

			$row->authors = $helper->contributors;
		}

		// Get the component params and merge with resource params
		$config = Component::params('com_resources');

		$rparams = new \Hubzero\Config\Registry($row->params);

		// Set the display date
		switch ($rparams->get('show_date', $config->get('show_date')))
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));    break;
			case 2: $thedate = Date::of($row->modified)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));   break;
			case 3: $thedate = Date::of($row->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); break;
		}

		// Start building HTML
		$html  = "\t" . '<li class="resource">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		if ($rparams->get('show_ranking', $config->get('show_ranking')))
		{
			$helper->getCitationsCount();
			$helper->getLastCitationDate();

			if ($row->area == 'Tools')
			{
				$stats = new \Components\Resources\Helpers\Usage\Stats($database, $row->id, $row->category, $row->rating, $helper->citationsCount, $helper->lastCitationDate);
			}
			else
			{
				$stats = new \Components\Resources\Helpers\Usage\Andmore($database, $row->id, $row->category, $row->rating, $helper->citationsCount, $helper->lastCitationDate);
			}
			$statshtml = $stats->display();

			$row->ranking = round($row->ranking, 1);

			$html .= "\t\t" . '<div class="metadata">' . "\n";

			$r = (10*$row->ranking);
			if (intval($r) < 10)
			{
				$r = '0' . $r;
			}

			$html .= "\t\t\t" . '<dl class="rankinfo">' . "\n";
			$html .= "\t\t\t\t" . '<dt class="ranking"><span class="rank-' . $r . '">' . Lang::txt('PLG_WHATSNEW_RESOURCES_THIS_HAS') . '</span> ' . number_format($row->ranking, 1) . ' ' . Lang::txt('PLG_WHATSNEW_RESOURCES_RANKING') . '</dt>' . "\n";
			$html .= "\t\t\t\t" . '<dd>' . "\n";
			$html .= "\t\t\t\t\t" . '<p>' . Lang::txt('PLG_WHATSNEW_RESOURCES_RANKING_EXPLANATION') . '</p>' . "\n";
			$html .= "\t\t\t\t\t" . '<div>' . "\n";
			$html .= $statshtml;
			$html .= "\t\t\t\t\t" . '</div>' . "\n";
			$html .= "\t\t\t\t" . '</dd>' . "\n";
			$html .= "\t\t\t" . '</dl>' . "\n";
			$html .= "\t\t" . '</div>' . "\n";
		}
		elseif ($rparams->get('show_rating', $config->get('show_rating')))
		{
			switch ($row->rating)
			{
				case 0.5: $class = ' half-stars';      break;
				case 1:   $class = ' one-stars';       break;
				case 1.5: $class = ' onehalf-stars';   break;
				case 2:   $class = ' two-stars';       break;
				case 2.5: $class = ' twohalf-stars';   break;
				case 3:   $class = ' three-stars';     break;
				case 3.5: $class = ' threehalf-stars'; break;
				case 4:   $class = ' four-stars';      break;
				case 4.5: $class = ' fourhalf-stars';  break;
				case 5:   $class = ' five-stars';      break;
				case 0:
				default:  $class = ' no-stars';      break;
			}

			$html .= "\t\t" . '<div class="metadata">' . "\n";
			$html .= "\t\t\t" . '<p class="rating"><span class="avgrating' . $class . '"><span>' . Lang::txt('PLG_WHATSNEW_RESOURCES_OUT_OF_5_STARS', $row->rating) . '</span>&nbsp;</span></p>' . "\n";
			$html .= "\t\t" . '</div>' . "\n";
		}
		$html .= "\t\t" . '<p class="details">' . $thedate . ' <span>|</span> ' . $row->area;
		if ($row->authors)
		{
			$html .= ' <span>|</span> ' . Lang::txt('PLG_WHATSNEW_RESOURCES_CONTRIBUTORS') . ' ' . $row->authors;
		}
		$html .= '</p>' . "\n";
		if ($row->itext)
		{
			$html .= "\t\t" . '<p>' . \Hubzero\Utility\String::truncate(strip_tags(stripslashes($row->itext)), 200) . '</p>' . "\n";
		}
		else if ($row->ftext)
		{
			$html .= "\t\t" . '<p>' . \Hubzero\Utility\String::truncate(strip_tags(stripslashes($row->ftext)), 200) . '</p>' . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . Request::base() . trim($row->href, '/') . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";

		// Return output
		return $html;
	}
}

