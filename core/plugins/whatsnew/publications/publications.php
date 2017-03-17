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
 * What's New Plugin class for com_publications entries
 */
class plgWhatsnewPublications extends \Hubzero\Plugin\Plugin
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

		include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'category.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'author.php');
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function onWhatsnewAreas()
	{
		return array(
			'publications' => Lang::txt('PLG_WHATSNEW_PUBLICATIONS')
		);
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
			if (!isset($areas[$this->_name])
			 && !in_array($this->_name, $areas))
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
		$rr = new \Components\Publications\Tables\Publication($database);

		// Build query
		$filters = array(
			'startdate' => $period->cStartDate,
			'enddate'   => $period->cEndDate,
			'sortby'    => 'date'
		);
		if (count($tagids) > 0)
		{
			$filters['tag'] = $tagids;
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
				if ($total == 0)
				{
					return array();
				}
			}

			$filters['limit'] = $limit;
			$filters['start'] = $limitstart;

			// Get results
			$rows = $rr->getRecords($filters);

			// Did we get any results?
			if ($rows)
			{
				// Loop through the results and set each item's HREF
				foreach ($rows as $key => $row)
				{
					$rows[$key]->text = NULL;
					if ($row->alias)
					{
						$rows[$key]->href = Route::url('index.php?option=com_publications&alias=' . $row->alias);
					}
					else
					{
						$rows[$key]->href = Route::url('index.php?option=com_publications&id=' . $row->id);
					}
					if ($row->abstract)
					{
						$rows[$key]->text = $rows[$key]->abstract;
					}
					$rows[$key]->section = NULL;
					$rows[$key]->area = $row->cat_name;
					$rows[$key]->publish_up = $row->published_up;
				}
			}

			return $rows;
		}
		else
		{
			// Get a count
			$counts = array();

			// Execute count query
			$results = $rr->getCount($filters);

			return ($results && is_array($results)) ? count($results) : $results;
		}
	}

	/**
	 * Push styles and scripts to the document
	 *
	 * @return     void
	 */
	public static function documents()
	{
		\Hubzero\Document\Assets::addComponentStylesheet('com_publications');

		require_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'html.php');
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

		$config = Component::params('com_publications');

		// Get version authors
		$pa = new \Components\Publications\Tables\Author($database);
		$authors = $pa->getAuthors($row->version_id);

		// Start building HTML
		$html  = "\t" . '<li class="publication">' . "\n";
		$html .= "\t\t" . '<p><span class="pub-thumb"><img src="' . Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_id) . '/Image:thumb' . '" alt="" /></span>';
		$html .= '<span class="pub-details"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a>' . "\n";
		$html .= "\t\t" . '<span class="block details">' . Date::of($row->published_up)->toLocal('d M Y') . ' <span>|</span> ' . $row->cat_name;
		if ($authors)
		{
			$html .= ' <span>|</span> ' . Lang::txt('PLG_WHATSNEW_PUBLICATIONS_CONTRIBUTORS') . ' ' . \Components\Publications\Helpers\Html::showContributors($authors, false, true);
		}
		$html .= '</span></span></p>' . "\n";
		if ($row->text)
		{
			$html .= "\t\t" . '<p>' . \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->text)), 200) . '</p>' . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . Request::base() . trim($row->href, '/') . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";

		// Return output
		return $html;
	}
}