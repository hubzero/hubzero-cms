<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HTML helper class for time component
 */
class ChartsHtml
{
	/**
	 * Draw graph, create javascript function to push to the page
	 *
	 * @return string Return javascript
	 */
	public static function drawColumn()
	{
		// Variables
		$jsObj  = "";
		$script = "";
		$db     = JFactory::getDbo();

		// Get records summary
		$records = new TimeRecords($db);
		$limit   = 5;
		$total   = $records->getCount();
		$rows    = $records->getSummaryHours(array('limit'=>$limit));
		$count   = 0;

		// Go through the rows and build the data for the chart
		foreach ($rows as $row)
		{
			$count++;
			$jsObj .= "[\"{$row->pname}\", {$row->hours}]";
			if ($count < $total)
			{
				$jsObj .= ", \n \t\t\t\t";
			}
		}

		// Write script
		$script = "
		google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
		google.setOnLoadCallback(drawChartColumn);

		function drawChartColumn() {
			// Set up our data
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Year');
			data.addColumn('number', 'Hours');
			data.addRows([
				{$jsObj}
			]);

			// Set up our options
			var options = {
				height: 300,
				title: 'Top {$limit} Tasks (by hours consumed)',
				hAxis: {title: 'Task', titleTextStyle: {color: 'black'}},
				vAxis: {title: 'Hours', titleTextStyle: {color: 'black'}},
				chartArea: {width: \"75%\", right: \"50\"},
				titleTextStyle: {color: \"black\", fontSize: 16},
				backgroundColor: {fill: \"transparent\"}
			};

			var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_column'));

			// Call the chart's getSelection() method
			function selectHandler() {
				var selectedItem = chart.getSelection()[0];
				if (selectedItem) {
					var value = data.getProperties(selectedItem.column, selectedItem.row);
					console.log(value);
				}
			}

			// Listen for the 'select' event, and call the selectHandler() when the user selects something on the chart
			google.visualization.events.addListener(chart, 'select', selectHandler);

			chart.draw(data, options);
		}";

		return $script;
	}

	/**
	 * Draw graph, create javascript function to push to the page
	 *
	 * @return string Return javascript
	 */
	public static function drawPieHubs()
	{
		// Variables
		$jsObj  = "";
		$script = "";
		$db     = JFactory::getDbo();

		// Get records summary
		$records = new TimeRecords($db);
		$limit   = 10;
		$total   = $records->getCount();
		$rows    = $records->getSummaryHoursByHub(array('limit'=>$limit));
		$count   = 0;

		// Go through the rows and build the data for the chart
		foreach ($rows as $row)
		{
			$count++;
			$jsObj .= "[\"{$row->hname}\", {$row->hours}]";
			if ($count < $total)
			{
				$jsObj .= ", \n \t\t\t\t";
			}
		}

		// Write script
		$script = "
		google.setOnLoadCallback(drawChartPieHubs);

		function drawChartPieHubs() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Hub');
			data.addColumn('number', 'Total Hours');
			data.addRows([
				{$jsObj}
				]);

			var options = {
				title: 'Total Time Breakdown (for the team)',
				is3D: false,
				chartArea: {width: \"100%\", left: \"15\", top: \"50\"},
				titleTextStyle: {color: \"black\", fontSize: 16},
				backgroundColor: {fill: \"transparent\"}
			};

			var chart = new google.visualization.PieChart(document.getElementById('chart_div_pie_hubs'));
			chart.draw(data, options);
		}";

		return $script;
	}

	/**
	 * Draw graph, create javascript function to push to the page
	 *
	 * @return string Return javascript
	 */
	public static function drawPieUser($uid)
	{
		// Variables
		$jsObj  = "";
		$script = "";
		$db     = JFactory::getDbo();

		// Get records summary
		$records = new TimeRecords($db);
		$limit   = 10;
		$total   = $records->getCount();
		$rows    = $records->getSummaryHours(array('limit'=>$limit, 'uid'=>$uid));
		$count   = 0;

		$user =  JFactory::getUser($uid);

		// Go through the rows and build the data for the chart
		foreach ($rows as $row)
		{
			$count++;
			$jsObj .= "[\"{$row->pname}\", {$row->hours}]";
			if ($count < $total)
			{
				$jsObj .= ", \n \t\t\t\t";
			}
		}

		// Write script
		$script = "
		google.setOnLoadCallback(drawChartPieUser);

		function drawChartPieUser() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'User');
			data.addColumn('number', 'Total Hours');
			data.addRows([
				{$jsObj}
				]);

			var options = {
				title: 'Project Breakdown for {$user->name}',
				is3D: false,
				chartArea: {width: \"100%\", left: \"15\", top: \"50\"},
				titleTextStyle: {color: \"black\", fontSize: 16},
				backgroundColor: {fill: \"transparent\"}
			};

			var chart = new google.visualization.PieChart(document.getElementById('chart_div_pie_user'));
			chart.draw(data, options);
		}";

		return $script;
	}

	/**
	 * Draw graph, create javascript function to push to the page
	 *
	 * @return string Return javascript
	 */
	public static function drawBar()
	{
		// Variables
		$jsObj   = "";
		$columns = "";
		$script  = "";
		$db      = JFactory::getDbo();

		// Get date range
		$date = array();
		$date['end']   = date("Y-m-d");
		$date['start'] = date("Y-m-d", strtotime('-14 days'));

		// Get records summary
		$records = new TimeRecords($db);
		$total   = $records->getCount();
		$rows    = $records->getSummaryEntries($date);

		// Go through the rows and build the data for the chart
		$count   = 0;

		foreach ($rows as $row)
		{
			$count++;
			$jsObj .= "[\"{$row->name}\", {$row->entries}]";
			if ($count < $total)
			{
				$jsObj .= ", \n \t\t\t\t";
			}
		}

		// Write script
		$script = "
		google.setOnLoadCallback(drawChartBar);

		function drawChartBar() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'User');
			data.addColumn('number', 'Records');
			data.addRows([
				{$jsObj}
			]);

		var options = {
			height: 300,
			titleTextStyle: {color: \"black\", fontSize: 16},
			title: 'Time entries Over Previous 14 Days',
			vAxis: {title: 'User',  titleTextStyle: {color: 'black'}},
			backgroundColor: {stroke: \"#E9E9E9\", strokeWidth: \"4\", fill: \"#F4F4F4\"}
			};

		var chart = new google.visualization.BarChart(document.getElementById('chart_div_bar'));
		chart.draw(data, options);
		}";

		return $script;
	}
}