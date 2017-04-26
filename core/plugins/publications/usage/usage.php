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
 * Publications Plugin class for usage
 */
class plgPublicationsUsage extends \Hubzero\Plugin\Plugin
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
	 * @param   object   $publication  Current publication
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function &onPublicationAreas($publication, $version = 'default', $extended = true)
	{
		$areas = array();

		if ($publication->_category->_params->get('plg_usage'))
		{
			$areas['usage'] = Lang::txt('PLG_PUBLICATION_USAGE');
		}

		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   array    $areas        Active area(s)
	 * @param   string   $rtrn         Data to be returned
	 * @param   string 	 $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		$arr = array(
			'html'    => '',
			'metadata'=>''
		);
		$rtrn = 'all';

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onPublicationAreas($publication))
			 && !array_intersect($areas, array_keys($this->onPublicationAreas($publication))))
			{
				$rtrn = 'metadata';
			}
		}

		if (!$publication->_category->_params->get('plg_usage'))
		{
			return $arr;
		}

		// Check if we have a needed database table
		$database = App::get('db');

		$tables = $database->getTableList();
		$table = $database->getPrefix() . 'publication_stats';

		if ($publication->alias)
		{
			$url = Route::url('index.php?option=' . $option . '&alias=' . $publication->alias . '&active=usage');
		}
		else
		{
			$url = Route::url('index.php?option=' . $option . '&id=' . $publication->id . '&active=usage');
		}

		if (!in_array($table, $tables))
		{
			$arr['html']     = '<p class="error">' . Lang::txt('PLG_PUBLICATION_USAGE_MISSING_TABLE') . '</p>';
			$arr['metadata'] = '<p class="usage"><a href="' . $url . '">' . Lang::txt('PLG_PUBLICATION_USAGE_DETAILED') . '</a></p>';
			return $arr;
		}

		// Get/set some variables
		$dthis  = Request::getVar('dthis', date('Y') . '-' . date('m'));
		$period = Request::getInt('period', $this->params->get('period', 14));

		require_once PATH_CORE . DS . 'components' . DS . $option . DS . 'tables' . DS . 'stats.php';
		require_once PATH_CORE . DS . 'components' . DS . $option . DS . 'helpers' . DS . 'usage.php';

		$stats = new \Components\Publications\Tables\Stats($database);
		$stats->loadStats($publication->id, $period, $dthis);

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			$helper = new \Components\Publications\Helpers\Usage($database, $publication->id, $publication->base);

			// Instantiate a view
			$view = $this->view('default', 'browse')
				->set('helper', $helper)
				->set('option', $option)
				->set('publication', $publication)
				->set('stats', $stats)
				->setErrors($this->getErrors());

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		/*if ($rtrn == 'metadata')
		{
			$stats->loadStats($publication->id, $period);

			if ($stats->users)
			{
				$action = $publication->base == 'files' ? '%s download(s)' : '%s view(s)';

				$arr['metadata']  = '<p class="usage">' . Lang::txt('%s user(s)',$stats->users);
				$arr['metadata'] .= $stats->downloads ? ' | ' . Lang::txt($action, $stats->downloads) : '';
				$arr['metadata'] .= '</p>';
			}*/
			$db = App::get('db');
			$db->setQuery(
				"SELECT SUM(page_views)
				FROM `#__publication_logs`
				WHERE `publication_id`=" . $db->quote($publication->id) . " AND `publication_version_id`=" . $db->quote($publication->version->id) . "
				ORDER BY `year` ASC, `month` ASC"
			);
			$views = (int) $db->loadResult();

			$db->setQuery(
				"SELECT SUM(primary_accesses)
				FROM `#__publication_logs`
				WHERE `publication_id`=" . $db->quote($publication->id) . " AND `publication_version_id`=" . $db->quote($publication->version->id) . "
				ORDER BY `year` ASC, `month` ASC"
			);
			$downloads = (int) $db->loadResult();

			$view = $this->view('default', 'metadata')
				->set('publication', $publication)
				->set('views', $views)
				->set('downloads', $downloads);

			$arr['metadata'] = $view->loadTemplate();

			//$arr['metadata'] = '<p class="usage">' . Lang::txt('PLG_PUBLICATIONS_USAGE_TOTALS', $views, $downloads) . '</p>';
		/*}

		if ($stats->users)
		{
			$arr['name']  = 'usage';
			$arr['count'] = $stats->users;
		}*/

		return $arr;
	}

	/**
	 * Round time into nearest second/minutes/hours/days
	 *
	 * @param   integer  $time  Time
	 * @return  string
	 */
	public function timeUnits($time)
	{
		if ($time < 60)
		{
			$data = round($time, 2) . ' ' . Lang::txt('PLG_PUBLICATION_USAGE_SECONDS');
		}
		else if ($time > 60 && $time < 3600)
		{
			$data = round(($time/60), 2) . ' ' . Lang::txt('PLG_PUBLICATION_USAGE_MINUTES');
		}
		else if ($time >= 3600 && $time < 86400)
		{
			$data = round(($time/3600), 2) . ' ' . Lang::txt('PLG_PUBLICATION_USAGE_HOURS');
		}
		else if ($time >= 86400)
		{
			$data = round(($time/86400), 2) . ' ' . Lang::txt('PLG_PUBLICATION_USAGE_DAYS');
		}

		return $data;
	}
}
