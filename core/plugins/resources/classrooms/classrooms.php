<?php
/**
 * @package     hubzero-cms
 * @author      Steven Snyder <snyder13@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for classroom cluster visualization
 */
class plgResourcesClassrooms extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function onResourcesAreas($model)
	{
		$area = array();

		if ($model->isTool() && self::any($model->resource->alias))
		{
			$area['classrooms'] = Lang::txt('PLG_RESOURCES_CLASSROOMS');
		}

		return $area;
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      string $alias
	 * @return     integer
	 */
	private static function any($alias)
	{
		static $any = array();

		if (!$alias)
		{
			return FALSE;
		}

		if (!isset($any[$alias]))
		{
			try
			{
				$dbh = App::get('db');
				$dbh->setQuery('SELECT 1 FROM `#__resource_stats_clusters` WHERE toolname = ' . $dbh->quote($alias) . ' LIMIT 1');
				list($any[$alias]) = $dbh->loadColumn(0);
			}
			catch (\Exception $_ex)
			{
				$any[$alias] = FALSE;
			}
		}
		return $any[$alias];
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onResourcesAreas($model))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($model))))
			{
				$rtrn = 'metadata';
			}
		}
		if (!is_null($enabled = $model->type->params->get('plg_classrooms')) && !$enabled)
		{
			return $arr;
		}

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			$arr['html'] = array('<div id="no-usage"><p class="warning">' . Lang::txt('PLG_RESOURCES_CLASSROOMS_NO_DATA_FOUND') . '</p></div>');

			if (self::any($model->resource->alias))
			{
				\Hubzero\Document\Assets::addPluginStyleSheet($this->_type, $this->_name);
				\Hubzero\Document\Assets::addPluginScript($this->_type, $this->_name);
				\Hubzero\Document\Assets::addSystemScript('d3.v2.js');

				$dbh = App::get('db');
				$tool = $dbh->quote($model->resource->alias);
				$dbh->setQuery('SELECT DISTINCT
					c2.toolname AS tool,
					c2.clustersize AS size,
					YEAR(c2.cluster_start) AS year,
					c2.cluster_start,
					c2.cluster_end,
					c2.first_use,
					SUBSTRING_INDEX(c2.cluster, \'|\', 1) AS semester,
					CONCAT(SUBSTRING_INDEX(c2.cluster, \'|\', 1), \'|\', SUBSTRING_INDEX(c2.cluster, \'|\', -2)) AS cluster,
					SHA1(CONCAT(c2.uidNumber, '.$dbh->quote(uniqid()).')) AS uid
					FROM
					(SELECT DISTINCT cluster FROM #__resource_stats_clusters WHERE toolname = '.$tool.') AS ct,
					#__resource_stats_clusters AS c2
					WHERE ct.cluster = c2.cluster'
				);
				$nodes = array();
				foreach ($dbh->loadAssocList() as $row)
				{
					if (!isset($nodes[$row['semester']]))
					{
						$nodes[$row['semester']] = array();
					}
					foreach (array('cluster_start', 'cluster_end', 'first_use') as $dateCol)
					{
						$row[$dateCol] = date('r', strtotime($row[$dateCol]));
					}
					$nodes[$row['semester']][] = $row;
				}
				$arr['html'][] = '<span id="cluster-data" data-tool="' . str_replace('"', '&quot;', $model->resource->alias) . '" data-seed="' . str_replace('"', '&quot;', json_encode(array_values($nodes))) . '"></span>';
			}
			$arr['html'] = implode("\n", $arr['html']);
		}

		return $arr;
	}
}

