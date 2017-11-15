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
 * What's New Plugin class for com_wiki articles
 */
class plgWhatsnewWiki extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function onWhatsnewAreas()
	{
		return array(
			'wiki' => Lang::txt('PLG_WHATSNEW_WIKI')
		);
	}

	/**
	 * Pull a list of records that were created within the time frame ($period)
	 *
	 * @param   object   $period      Time period to pull results for
	 * @param   mixed    $limit       Number of records to pull
	 * @param   integer  $limitstart  Start of records to pull
	 * @param   array    $areas       Active area(s)
	 * @param   array    $tagids      Array of tag IDs
	 * @return  array
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

		include_once Component::path('com_wiki') . DS . 'models' . DS . 'page.php';

		// @TODO: Move these to separate plugins so Wiki doesn't directly reference other extensions
		Components\Wiki\Models\Page::addAdapterPath(PATH_CORE . '/plugins/groups/wiki/adapters/group.php');
		Components\Wiki\Models\Page::addAdapterPath(PATH_CORE . '/plugins/projects/notes/adapters/project.php');

		if (!$limit)
		{
			return Components\Wiki\Models\Page::all()
				->whereEquals('state', Components\Wiki\Models\Page::STATE_PUBLISHED)
				->where('created', '>=', $period->cStartDate)
				->where('created', '<', $period->cEndDate)
				->order('created', 'desc')
				->count();
		}
		else
		{
			$pages = Components\Wiki\Models\Page::all()
				->whereEquals('state', Components\Wiki\Models\Page::STATE_PUBLISHED)
				->order('created', 'desc')
				->where('created', '>=', $period->cStartDate)
				->where('created', '<', $period->cEndDate)
				->limit($limit)
				->start($limitstart)
				->rows();

			$rows = array();

			foreach ($pages as $page)
			{
				$row = new stdClass;
				$row->title = $page->title;
				$row->href  = Route::url($page->link());
				$row->text  = strip_tags($page->version->get('pagehtml'));
				$row->category = $page->get('scope');
				$row->section = $this->_name;

				$rows[] = $row;
			}

			return $rows;
		}
	}
}
