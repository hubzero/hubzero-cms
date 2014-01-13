<?php
/**
 * @package     hubzero-cms
 * @author      Steven Snyder <snyder13@purdue.edu> 
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

	
/**
 * Resources Plugin class for classroom cluster visualization
 */
class plgResourcesClassrooms extends JPlugin
{
	/**
	 * Return the alias and name for this category of content
	 * 
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function onResourcesAreas($model) {
		if ($model->isTool() && self::any($model->resource->alias)) {
			return array(
				'classrooms' => 'Classroom usage'
			);
		} 
		return array();
	}

	private static function any($alias) {
		static $any = array();
		if (!$alias) {
			return FALSE;
		}
		if (!isset($any[$alias])) {
			$jThrow = JError::$legacy;
			try {
				JError::$legacy = FALSE; // just throw an exception like a normal person, please
				$dbh = JFactory::getDBO();
				$dbh->setQuery('SELECT 1 FROM #__resource_stats_clusters WHERE toolname = '.$dbh->quote($alias).' LIMIT 1');
				list($any[$alias]) = $dbh->loadColumn(0);
			}
			catch (\Exception $_ex) {
				$any[$alias] = FALSE;
			}
			JError::$legacy = $jThrow;
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
	public function onResources($model, $option, $areas, $rtrn='all') {
		$arr = array(
			'area'     => 'classrooms',
			'html'     => array('<div id="no-usage"><p class="warning">'.JText::_('No classroom usage data was found. You may need to enable JavaScript to view this data.').'</p></div>'),
			'metadata' => ''
		);
		if (self::any($model->resource->alias)) {
			$doc = JFactory::getDocument();
			$doc->addStylesheet('/plugins/resources/classrooms/css/classrooms.css');
			$doc->addScript('/plugins/resources/classrooms/js/classrooms.js');
			$doc->addScript('/media/system/js/d3.v2.js');
			$dbh = JFactory::getDBO();
			$dbh->setQuery(
				'SELECT DISTINCT 
					sc2.toolname AS tool,
					sc2.clustersize AS size,
					YEAR(sc2.cluster_start) AS year,
					sc2.cluster_start,
					sc2.cluster_end,
					sc2.first_use,
					SUBSTRING_INDEX(sc2.cluster, \'|\', 1) AS semester,
					CONCAT(SUBSTRING_INDEX(sc2.cluster, \'|\', 1), \'|\', SUBSTRING_INDEX(sc2.cluster, \'|\', -2)) AS cluster,
					SHA1(CONCAT(sc2.uidNumber, '.$dbh->quote(uniqid()).')) AS uid  
				FROM #__resource_stats_clusters sc1 
				LEFT JOIN #__resource_stats_clusters sc2 ON sc2.cluster = sc1.cluster 
				WHERE sc1.toolname = '.$dbh->quote($model->resource->alias).' 
				ORDER BY cluster_start, first_use');

			$nodes = array();
			foreach ($dbh->loadAssocList() as $row) {
				if (!isset($nodes[$row['semester']])) {
					$nodes[$row['semester']] = array();
				}
				foreach (array('cluster_start', 'cluster_end', 'first_use') as $dateCol) {
					$row[$dateCol] = date('r', strtotime($row[$dateCol]));
				}
				$nodes[$row['semester']][] = $row;
			}
			$arr['html'][] = '<span id="cluster-data" data-tool="'.str_replace('"', '&quot;', $model->resource->alias).'" data-seed="'.str_replace('"', '&quot;', json_encode(array_values($nodes))).'"></span>';
		}
		$arr['html'] = implode("\n", $arr['html']);
		return $arr;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onResourcesAreas($model)) 
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($model)))) 
			{
				$rtrn = 'metadata';
			}
		}
		if (!$model->type->params->get('plg_usage')) 
		{
			return $arr;
		}

		// Display only for tools
		if (!$model->isTool()) 
		{
			//return $arr;
			$rtrn == 'metadata';
		}

		// Check if we have a needed database table
		$database = JFactory::getDBO();

		$tables = $database->getTableList();
		$table  = $database->getPrefix() . 'resource_stats_tools';

		if ($model->resource->alias) 
		{
			$url = JRoute::_('index.php?option=' . $option . '&alias=' . $model->resource->alias . '&active=usage');
		} 
		else 
		{
			$url = JRoute::_('index.php?option=' . $option . '&id=' . $model->resource->id . '&active=usage');
		}

		if (!in_array($table, $tables)) 
		{
			$arr['html'] = '<p class="error">'. JText::_('PLG_RESOURCES_USAGE_MISSING_TABLE') . '</p>';
			$arr['metadata'] = '<p class="usage"><a href="' . $url . '">' . JText::_('PLG_RESOURCES_USAGE_DETAILED') . '</a></p>';
			return $arr;
		}

		// Get/set some variables
		$dthis = JRequest::getVar('dthis', date('Y') . '-' . date('m'));
		$period = JRequest::getInt('period', $this->params->get('period', 14));

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $option . DS . 'tables' . DS . 'stats.php');
		if ($model->isTool()) 
		{
			$stats = new ResourcesStatsTools($database);
		} 
		else 
		{
			$stats = new ResourcesStats($database);
		}
		$stats->loadStats($model->resource->id, $period, $dthis);

		$clusters = new ResourcesStatsClusters($database);
		$clusters->loadStats($model->resource->id);

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') 
		{
			$action = JRequest::getVar('action', '');
			if ($action == 'top')
			{
				$this->getTopValues($model->resource->id, JRequest::getVar('datetime', '0000-00-00 00:00:00'));
				return;
			}
			if ($action == 'overview')
			{
				$this->getValues($model->resource->id, JRequest::getInt('period', 13));
				return;
			}
			ximport('Hubzero_Document');
			Hubzero_Document::addComponentStylesheet('com_usage');
	
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => 'usage',
					'name'    => 'browse'
				)
			);

			// Pass the view some info
			$view->option     = $option;
			$view->resource   = $model->resource;
			$view->stats      = $stats;
			$view->chart_path = $this->params->get('chart_path','');
			$view->map_path   = $this->params->get('map_path','');
			$view->dthis      = $dthis;
			$view->period     = $period;
			$view->params     = $this->params;
			if ($this->getError()) 
			{
				$view->setError($this->getError());
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		if ($rtrn == 'all' || $rtrn == 'metadata') 
		{
			if (!$stats->users)
			{
				$stats->users = 0;
			}
			if ($model->isTool()) 
			{
				$arr['metadata'] = '<p class="usage"><a href="' . $url . '">' . JText::sprintf('PLG_RESOURCES_USAGE_NUM_USERS', $stats->users) . '</a></p>';
			} 
			else 
			{
				$arr['metadata'] = '<p class="usage">' . JText::sprintf('%s users', $stats->users) . '</p>';
			}
			if ($clusters->users && $clusters->classes) 
			{
				$arr['metadata'] .= '<p class="usage">' . JText::sprintf('%s users', $clusters->users) . ' in ' . JText::sprintf('%s classes', $clusters->classes) . '</p>';
			}
		}

		return $arr;
	}
}

