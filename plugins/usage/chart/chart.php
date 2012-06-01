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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Usage plugin class for charts
 */
class plgUsageChart extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject The object to observe
	 * @param      array  $config   An optional associative array of configuration settings.
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the name of the area this plugin retrieves records for
	 * 
	 * @return     array
	 */
	public function onUsageAreas()
	{
		$areas = array(
			'chart' => '' //JText::_('PLG_USAGE_CHART')
		);
		return $areas;
	}

	/**
	 * Returns TRUE if there is data in the database
	 * for the date passed to it, FALSE otherwise.
	 * 
	 * @param      object &$db       JDatabase
	 * @param      string $yearmonth YYYY-MM
	 * @param      string $period    Time period (monthly, quarterly, etc)
	 * @return     boolean
	 */
	private function check_for_data(&$db, $yearmonth, $period)
	{
		$sql = "SELECT COUNT(datetime) FROM totalvals WHERE datetime LIKE '" . mysql_escape_string($yearmonth) . "-%' AND period = '" . mysql_escape_string($period) . "'";
		$db->setQuery($sql);
		$result = $db->loadResult();

		if ($result && $result > 0) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Get data for building a chart
	 * 
	 * @param      object  $db       JDatabase
	 * @param      integer $id       Item to show data for
	 * @param      string  $period   Time period (monthly, quarterly, etc)
	 * @param      string  $datetime Timestamp
	 * @return     array
	 */
	private function getChartData($db, $id, $period, $datetime)
	{
		$dateFormat = '%b %Y';
		$tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$dateFormat = 'M Y';
			$tz = true;
		}

		$data = array();

		switch ($period)
		{
			case '0':
				$thisyear = date("Y");
				$tp = $thisyear - 2000;
				$limit = $tp * 12;
			break;
			case '3':
				$thisyear = date("Y");
				$tp = $thisyear - 2000;
				$limit = $tp * 12;
			break;
			case '13':
			case '12':
			case '1':
			default:
				$limit = 12;
			break;
		}

		// Pull results
		$sql = "SELECT value, valfmt, datetime FROM summary_user_vals WHERE rowid='$id' AND period='$period' AND datetime<='$datetime' AND colid='$id' ORDER BY datetime DESC LIMIT $limit";
		$db->setQuery($sql);
		$results = $db->loadObjectList();

		if ($results) 
		{
			// Reverse the array (we'll be getting back data in DESC order, we need it in ASC order)
			$results = array_reverse($results);

			// Find the highest value
			$vals = array();
			foreach ($results as $result)
			{
				$vals[] = $result->value;
			}
			asort($vals);

			$highest = array_pop($vals);

			$data['range'] = array();
			$data['range']['start'] = 0;
			$data['range']['end']   = $highest;
			/*if ($highest > 20000) {
				$data['range']['step'] = 10000;
			} else if ($highest > 10000) {
				$data['range']['step'] = 1000;
			} else if ($highest > 1000) {
				$data['range']['step'] = 100;
			} else {
				$data['range']['step'] = 10;
			}*/
			if ($highest > 10000) 
			{
				$t = round($highest, -4);
			} 
			else if ($highest < 10000 && $highest > 1000) 
			{
				$t = round($highest, -3);
			} 
			else if ($highest < 1000 && $highest > 100) 
			{
				$t = round($highest, -2);
			} 
			else 
			{
				$t = round($highest, -1);
			}
			$data['range']['step'] = $t/10;

			$e = end($results);
			$data['title'] = JHTML::_('date', $results[0]->datetime, $dateFormat, $tz) . ' - ' . JHTML::_('date', $e->datetime, $dateFormat, $tz);

			// Generate the sparkline	
			$data['points'] = array();
			$data['dates'] = array();
			$data['datetime'] = array();
			switch ($period)
			{
				case '13':
					foreach ($results as $result)
					{
						$data['datetime'][] = $result->datetime;
						$data['dates'][]    = JHTML::_('date', $result->datetime, $dateFormat, $tz);
						$data['points'][]   = intval($result->value);
					}
				break;
				case '12':
					foreach ($results as $result)
					{
						$data['datetime'][] = $result->datetime;
						$data['dates'][]    = JHTML::_('date', $result->datetime, $dateFormat, $tz);
						$data['points'][]   = intval($result->value);
					}
				break;
				case '3':
					$i = 0;
					$data['datetime'][] = $results[0]->datetime;
					$data['dates'][]    = JHTML::_('date', $results[0]->datetime, $dateFormat, $tz);
					$data['points'][]   = intval($results[0]->value);
					foreach ($results as $result)
					{
						$i++;
						if ($i == 4) 
						{
							$i = 1;
							$data['datetime'][] = $result->datetime;
							$data['dates'][]    = JHTML::_('date', $result->datetime, $dateFormat, $tz);
							$data['points'][]   = intval($result->value);
						}
					}
				break;
				case '0':
					$i = 0;
					$data['datetime'][] = $results[0]->datetime;
					$data['dates'][]    = JHTML::_('date', $results[0]->datetime, $dateFormat, $tz);
					$data['points'][]   = intval($results[0]->value);
					foreach ($results as $result)
					{
						$i++;
						if ($i == 12) 
						{
							$i = 0;
							$data['datetime'][] = $result->datetime;
							$data['dates'][]    = JHTML::_('date', $result->datetime, $dateFormat, $tz);
							$data['points'][]   = intval($result->value);
						}
					}
				break;
				case '1':
				default:
					foreach ($results as $result)
					{
						$data['datetime'][] = $result->datetime;
						$data['dates'][]    = JHTML::_('date', $result->datetime, $dateFormat, $tz);
						$data['points'][]   = intval($result->value);
					}
				break;
			}
		}

		return $data;
	}

	/**
	 * Get data for a pie chart
	 * 
	 * @param      object &$db      JDatabase
	 * @param      string $id       Item to show data for
	 * @param      string $period   Time period (monthly, quarterly, etc)
	 * @param      string $datetime Timestamp
	 * @param      string $group    Group to show data for
	 * @return     array 
	 */
	private function getPieData(&$db, $id, $period, $datetime, $group='residence')
	{
		$data = array();
		$data['title'] = '';
		$data['values'] = array();

		$labels = array();
		$labels[] = JText::_('Totals');
		$labels[] = JText::_('Identified');
		$labels[] = JText::_('US');
		$labels[] = JText::_('Asia');
		$labels[] = JText::_('Europe');
		$labels[] = JText::_('Other');
		$labels[] = JText::_('Identified');
		$labels[] = JText::_('Edu.');
		$labels[] = JText::_('Ind.');
		$labels[] = JText::_('Gov.');
		$labels[] = JText::_('Other');

		$sql = "SELECT value, valfmt FROM summary_user_vals WHERE rowid='" . $id . "' AND period='" . $period . "' AND datetime ='"  .$datetime . "' ORDER BY colid";
		$db->setQuery($sql);
		$results = $db->loadObjectList();
		if ($results) 
		{
			$i = 0;
			$t = 0;
			foreach ($results as $row)
			{
				$i++;
				switch ($group)
				{
					case 'residence':
						if ($i == 2) 
						{
							$data['title'] = JText::_('by Residence');
							$t = $row->value;
						}
						if ($i > 2 && $i < 7) 
						{
							$v = $t * ($row->value * 0.01);
							$data['values'][] = new pie_value($v, $labels[$i-1]);
						}
					break;

					case 'organization':
						if ($i == 7) 
						{
							$data['title'] = JText::_('by Organization');
							$t = $row->value;
						}
						if ($i >= 8) 
						{
							$v = $t * ($row->value * 0.01);
							$data['values'][] = new pie_value($v, $labels[$i-1]);
						}
					break;
				}
			}
		}

		return $data;
	}

	/**
	 * Format a result
	 * 
	 * @param      mixed   $value Value to format
	 * @param      integer $fmt   Format to use
	 * @return     mixed
	 */
	private function fmt_result($value, $fmt)
	{
		$valfmt[0] = '-'; // blank. for future use
		$valfmt[1] = ' '; // no units
		$valfmt[2] = '%';
		$valfmt[3] = 'users';
		$valfmt[4] = 'jobs';
		$valfmt[5] = 'days';
		$valfmt[6] = 'hours';
		$valfmt[7] = ''; // text data. display as is

		if ($fmt == 0) 
		{
			return $valfmt[0];
		} 
		else if ($fmt == 1) 
		{
			$value = number_format($value) . $valfmt[$fmt];
			return $value;
		} 
		else if ($fmt == 2) 
		{
			$value = number_format($value) . $valfmt[$fmt];
			return $value;
		} 
		else if ($fmt == 5) 
		{
			$value = number_format(($value/86400)) . ' ' . $valfmt[$fmt];
			return $value;
		} 
		else if ($fmt == 6) 
		{
			return $value;
		} 
		else 
		{
			$value = number_format($value) . ' ' . $valfmt[$fmt];
			return $value;
		}
	}

	/**
	 * Generate navigation links
	 * 
	 * @param      string $option Component name
	 * @param      string $task   Component task
	 * @param      string $period Time period
	 * @return     string HTML
	 */
	private function navlinks($option, $task, $period='prior12')
	{
		$html  = '<div id="sub-sub-menu">' . "\n";
		$html .= "\t" . '<ul>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'prior12') 
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option=' . $option . '&task=' . $task . '&period=prior12') . '"><span>' . JText::_('USAGE_PERIOD_PRIOR12') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'month') 
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option=' . $option . '&task=' . $task . '&period=month') . '"><span>' . JText::_('USAGE_PERIOD_MONTH') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'qtr') 
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option=' . $option . '&task=' . $task . '&period=qtr') . '"><span>' . JText::_('USAGE_PERIOD_QTR') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'year') 
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option=' . $option . '&task=' . $task . '&period=year') . '"><span>' . JText::_('USAGE_PERIOD_YEAR') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'fiscal') 
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option=' . $option . '&task=' . $task . '&period=fiscal') . '"><span>' . JText::_('USAGE_PERIOD_FISCAL') . '</span></a></li>' . "\n";
		$html .= "\t" . '</ul>' . "\n";
		$html .= '</div>' . "\n";

		return $html;
	}

	/**
	 * Output data as javascript and push to document
	 * 
	 * @param      string  $option   Component name
	 * @param      string  $label    Graph label
	 * @param      object  $db       JDatabase
	 * @param      integer $id       Item to show data for
	 * @param      string  $period   Time period (monthly, quarterly, etc)
	 * @param      string  $datetime Timestamp
	 * @return     void
	 */
	protected function outputData($option, $label, $db, $id, $period, $datetime)
	{
		$dateFormat = '%b %Y';
		$tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$dateFormat = 'M Y';
			$tz = true;
		}

		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'ofc' . DS . 'php-ofc-library' . DS . 'open-flash-chart.php');

		// ------
		// Chart 1

		$data = $this->getChartData($db, $id, $period, $datetime);

		$title1 = new title($data['title']);

		/*$area = new area_hollow();
		$area->set_colour('#5B56B6');
		$area->set_values($data['points']);
		$area->set_key($label, count($data['points']));*/

		$line = new line_hollow();
		$line->set_values($data['points']);
		$line->set_colour('#5B56B6');
		$line->set_halo_size(2);
		$line->set_width(2);
		$line->set_dot_size(5);
		$line->set_on_click('bakeNewPies');
		$line->set_key($label, count($data['points']));

		$chart1 = new open_flash_chart();
		$chart1->set_bg_colour('#ffffff');
		$chart1->set_title($title1);
		$chart1->add_element($line);

		$x_labels = new x_axis_labels();
		$x_labels->set_vertical();
		$x_labels->set_colour('#A2ACBA');
		$x_labels->set_labels($data['dates']);

		$x = new x_axis();
		$x->set_grid_colour('#f1f1f1');
		$x->set_offset(false);
		$x->set_colour('#000000');
		$x->set_labels($x_labels);

		$y = new y_axis();
		$y->set_grid_colour('#f1f1f1');
		$y->set_colour('#000000');
		$y->set_range($data['range']['start'], $data['range']['end'], $data['range']['step']);

		$chart1->set_x_axis($x);
		$chart1->set_y_axis($y);

		// ------
		// Chart 2

		$datetimes = $data['datetime'];

		//$pd = array();
		$piecharts1 = array();
		$piecharts2 = array();
		foreach ($datetimes as $dt)
		{
			$piedata1 = $this->getPieData($db, $id, $period, $dt, 'residence');

			$title = new title(JHTML::_('date', $dt, $dateFormat, $tz) . ' ' . $piedata1['title']);

			$pie = new pie();
			$pie->set_start_angle(35);
			$pie->set_animate(false);
			$pie->set_tooltip('#val# of #total#<br>#percent# of 100%');
			$pie->set_values($piedata1['values']);

			$pchart1 = new open_flash_chart();
			$pchart1->set_title($title);
			$pchart1->set_bg_colour('#ffffff');
			$pchart1->add_element($pie);
			$pchart1->x_axis = null;

			$piecharts1[] = $pchart1;

			$piedata2 = $this->getPieData($db, $id, $period, $dt, 'organization');

			$title3 = new title(JHTML::_('date', $dt, $dateFormat, $tz) . ' ' . $piedata2['title']);

			$pie = new pie();
			$pie->set_start_angle(35);
			$pie->set_animate(false);
			$pie->set_tooltip('#val# of #total#<br>#percent# of 100%');
			$pie->set_values($piedata2['values']);

			$pchart2 = new open_flash_chart();
			$pchart2->set_title($title3);
			$pchart2->set_bg_colour('#ffffff');
			$pchart2->add_element($pie);
			$pchart2->x_axis = null;

			$piecharts2[] = $pchart2;
		}

		// ------
		// Output

		$js = '
		window.addEvent("domready", function(){
			swfobject.embedSWF("/libraries/ofc/open-flash-chart.swf", "chart1", "600", "350", "9.0.0", "expressInstall.swf", {"get-data":"get_data_1"});
			swfobject.embedSWF("/libraries/ofc/open-flash-chart.swf", "chart2", "300", "300", "9.0.0", "expressInstall.swf", {"get-data":"get_data_2"});
			swfobject.embedSWF("/libraries/ofc/open-flash-chart.swf", "chart3", "300", "300", "9.0.0", "expressInstall.swf", {"get-data":"get_data_3"});
		});
		
		var ig = ' . (count($data['points']) - 1) . ';
		
		function get_data_1() 
		{
			return JSON.stringify(data[0]);
		}
		
		function get_data_2() 
		{
			return JSON.stringify(data[1][ig]);
		}
		
		function get_data_3() 
		{
			return JSON.stringify(data[2][ig]);
		}
		
		function bakeNewPies(index)
		{
			ig = index;

			swfobject.embedSWF("/libraries/ofc/open-flash-chart.swf", "chart2", "300", "300", "9.0.0", "expressInstall.swf", {"get-data":"get_data_2"});
			swfobject.embedSWF("/libraries/ofc/open-flash-chart.swf", "chart3", "300", "300", "9.0.0", "expressInstall.swf", {"get-data":"get_data_3"});
		}
		
		var data = new Array();
		data[0] = ' . $chart1->toPrettyString() . '
		data[1] = new Array();
		data[2] = new Array();
		
		';
		$n = count($data['points']);
		for ($i = 0; $n > $i; $i++)
		{
			$js .= 'data[1][' . $i . '] = ' . $piecharts1[$i]->toPrettyString() . ';' . "\n";
			$js .= 'data[2][' . $i . '] = ' . $piecharts2[$i]->toPrettyString() . ';' . "\n";
		}

		$document =& JFactory::getDocument();
		$document->addScriptDeclaration($js);
	}

	/**
	 * Event call for displaying usage data
	 * 
	 * @param      string $option        Component name
	 * @param      string $task          Component task
	 * @param      object $db            JDatabase
	 * @param      array  $months        Month names (Jan -> Dec)
	 * @param      array  $monthsReverse Month names in reverse (Dec -> Jan)
	 * @param      string $enddate       Time period
	 * @return     string HTML
	 */
	public function onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)
	{
		// Check if our task is the area we want to return results for
		if ($task) 
		{
			if (!in_array($task, $this->onUsageAreas())
			 && !in_array($task, array_keys($this->onUsageAreas()))) 
			{
				return '';
			}
		}

		// Incoming
		$period = JRequest::getVar('period', 'prior12');
		$selectedPeriod = JRequest::getVar('selectedPeriod', '');

		if (!$selectedPeriod) 
		{
			$db->setQuery("SELECT MAX(datetime) FROM summary_collab_vals");
			$lastdate = $db->loadResult();
			if ($lastdate) 
			{
				$defaultMonth = substr($lastdate, 5, 2);
				$defaultYear  = substr($lastdate, 0, 4);
			} 
			else 
			{
				$defaultMonth = date("m");
				$defaultYear  = date("Y");
			}
			$selectedPeriod = $defaultYear . '-' . $defaultMonth;
		}
		$checkyear  = substr($selectedPeriod, 0, 4);
		$checkmonth = substr($selectedPeriod, 5, 2);
		if ($checkyear <='2007') 
		{
			if ($checkyear < '2007') 
			{
				$page = 'old';
			} 
			else if ($checkyear == '2007') 
			{
				if ($checkmonth < '10') 
				{
					$page = 'old';
				} 
				else 
				{
					$page = 'new';
				}
			}
		} 
		else 
		{
			$page = 'new';
		}

		if ($period == 'nice') 
		{
			$page = 'old';
			$selectedPeriod = '2007-09';
		}

		// Get and set some vars
		$cur_year = floor(date("Y"));
		$cur_month = floor(date("n"));
		$year_data_start = 2000;
		$datetime = $selectedPeriod.'-00 00:00:00';
		//$enddate = $this->enddate;
		//$months = $this->months;
		//$monthsReverse = $this->monthsReverse;

		$document =& JFactory::getDocument();
		if (is_file('libraries' . DS . 'ofc' . DS . 'js' . DS . 'swfobject.js')) 
		{
			$document->addScript(DS . 'libraries' . DS . 'ofc' . DS . 'js' . DS . 'json' . DS . 'json2.js');
			$document->addScript(DS . 'libraries' . DS . 'ofc' . DS . 'js' . DS . 'swfobject.js');
			//$document->addScriptDeclaration("window.addEvent('domready', HUB.Usage.loadOFC);");
		}

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem(JText::_('USAGE_PERIOD_' . strtoupper($period)), 'index.php?option=' . $option . '&task=' . $task . '&period=' . $period);

		// Build the HTML
		//$html  = '<div id="content-header">' . "\n";
		//$html .= '<h2>' . JText::_(strtoupper($this->_name)) . ': ' . JText::_('USAGE_'.strtoupper($task)) . '</h2>';
		//$html .= '</div><!-- / #content-header -->' . "\n";
		//$html .= '<div id="content-header-extra"><p><a class="map" href="'.JRoute::_('index.php?option=' . $option . '&task=maps&type=online') . '">Unique Locations of Users</a></p></div>';

		$html  = $this->navlinks($option, $task, $period);
		//$html .= '<div class="main section overview" id="statistics">' . "\n";

		//$html .= '<form method="post" action="'. JRoute::_('index.php?option=' . $option . '&task=' . $task . '&period=' . $period) .'">' . "\n";
		//$html .= t.'<fieldset class="filters"><label>' . JText::_('USAGE_SHOW_DATA_FOR') . ': ';
		$html .= '<div class="aside">' . "\n";
		$html .= "\t" . '<p class="help">' . JText::_('Click a data point to see the breakdowns by residence and organization below.') . '</p>' . "\n";
		$html .= '</div><!-- / .aside -->' . "\n";
		$html .= '<div class="subject">' . "\n";
		$html .= '<input type="hidden" name="period" id="period" value="' . $period . '" />' . "\n";
		//$html .= '<select name="selectedPeriod">' . "\n";
		switch ($period)
		{
			case '12':
			case 'prior12':
			case 'nice':
				//$option = 'prior12';
				$period = '12';

				/*$arrayMonths = array_values($months);
				for ($i = $cur_year; $i >= $year_data_start; $i--) 
				{
					foreach ($monthsReverse as $key => $month) 
					{
						if ($key == '12') {
							$nextmonth = 'Jan';
						} else {
							$nextmonth = $arrayMonths[floor(array_search($month, $arrayMonths))+1];
						}
						$value = $i . '-' . $key;
						if ($this->check_for_data($db, $value, 12)) {
							$html .= '<option value="'. $value .'"';
							if ($value == $selectedPeriod) {
								$html .= ' selected="selected"';
							}
							$html .= '>'. $nextmonth .' ';
							if ($key == 12) {
								$html .= $i;
							} else {
								$html .= $i - 1;
							}
						   	$html .= ' - '. $month .' '. $i .'</option>' . "\n";
						}
					}
				}*/
			break;

			case '1':
			case 'month':
				//$option = 'month';
				$period = '1';

				/*for ($i = $cur_year; $i >= $year_data_start; $i--) 
				{
					foreach ($monthsReverse as $key => $month) 
					{
						$value = $i . '-' . $key;
						if ($this->check_for_data($db, $value, 1)) {
							$html .= '<option value="'. $value .'"';
							if ($value == $selectedPeriod) {
								$html .= ' selected="selected"';
							}
							$html .= '>'. $month .' '. $i .'</option>' . "\n";
						}
					}
				}*/
			break;

			case '3':
			case 'qtr':
				//$option = 'qtr';
				$period = '3';

				/*$qtd_found = 0;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$qtd_found && $this->check_for_data($db, $value, 3)) {
						$html .= '<option value="'. $value .'"';
						if ($value == $selectedPeriod) {
							$html .= ' selected="selected"';
						}
						$html .= '>';
						if ($key <= 3) {
							$key = 0;
							$html .= 'Jan';
						} elseif($key <= 6) {
							$key = 3;
							$html .= 'Apr';
						} elseif($key <= 9) {
							$key = 6;
							$html .= 'Jul';
						} else {
							$key = 9;
							$html .= 'Oct';
						}
						$html .= ' '. $cur_year .' - '. $month .' '. $cur_year .'</option>' . "\n";
						$qtd_found = 1;
					}
				}
				$qtr_found = 0;
				for ($j = $cur_year; $j >= $year_data_start; $j--) 
				{
					for ($i = 12; $i > 0; $i = $i - 3) 
					{
						if ($qtr_found && $key) {
							$i = $key;
							$qtd_found = 0;
						}
						$value = $j . '-' . sprintf("%02d", $i);
						if ($this->check_for_data($db, $value, 3)) {
							$html .= '<option value="'. $value .'"';
							if ($value == $selectedPeriod) {
								$html .= ' selected="selected"';
							}
							$html .= '>';
							if ($i == 3) {
								$html .= 'Jan';
							} elseif ($i == 6) {
								$html .= 'Apr';
							} elseif ($i == 9) {
								$html .= 'Jul';
							} else {
								$html .= 'Oct';
							}
							$html .= ' '. $j .' - ';
							if ($i == 3) {
								$html .= 'Mar';
							} elseif ($i == 6) {
								$html .= 'Jun';
							} elseif ($i == 9) {
								$html .= 'Sep';
							} else {
								$html .= 'Dec';
							}
							$html .= ' '. $j .'</option>' . "\n";
						}
					}
				}*/
			break;

			case '13':
			case 'fiscal':
				//$option = 'fiscal';
				$period = '13';

				/*$ytd_found = 0;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && $this->check_for_data($db, $value, 0)) {
						$html .= '<option value="'. $value .'"';
						if ($value == $selectedPeriod) {
							$html .= ' selected="selected"';
						}
						$html .= '>Oct ';
						if ($cur_month >= 9) {
							$html .= $cur_year;
							$full_year = $cur_year;
						} else {
							$html .= $cur_year - 1;
							$full_year = $cur_year - 1;
						}
						$html .= ' - '. $month .' '. $cur_year .'</option>' . "\n";
						$ytd_found = 1;
					}
				}
				for ($i = $full_year; $i >= $year_data_start; $i--) 
				{
					$value = $i . '-09';
					if ($this->check_for_data($db, $value, 0)) {
						$html .= '<option value="'. $value .'"';
						if ($value == $selectedPeriod) {
							$html .= ' selected="selected"';
						}
						$html .= '>Oct ';
						$html .= $i - 1;
						$html .= ' - Sep '. $i .'</option>' . "\n";
					}
				}*/
			break;

			case '0':
			case 'year':
				//$option = 'year';
				$period = '0';

				/*$ytd_found = 0;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && $this->check_for_data($db, $value, 0)) {
						$html .= '<option value="'. $value .'"';
						if ($value == $selectedPeriod) {
							$html .= ' selected="selected"';
						}
						$html .= '>Jan - '. $month .' '. $cur_year .'</option>' . "\n";
						$ytd_found = 1;
					}
				}
				for ($i = $cur_year - 1; $i >= $year_data_start; $i--) 
				{
					$value = $i . '-12';
					if ($this->check_for_data($db, $value, 0)) {
						$html .= '<option value="'. $value .'"';
						if ($value == $selectedPeriod) {
							$html .= ' selected="selected"';
						}
						$html .= '>Jan - Dec '. $i .'</option>' . "\n";
					}
				}*/
			break;
		}
		/*$html .= '</select></label> <input type="submit" value="' . JText::_('USAGE_VIEW') . '" /></fieldset>' . "\n";
		$html .= '</form>' . "\n";*/

		$html .= '<div id="chart1"></div><br />' . "\n";
		$html .= '<div id="chart2"></div>' . "\n";
		$html .= '<div id="chart3"></div>' . "\n";

		//$no_html = JRequest::getInt('no_html', 0);
		//if ($no_html) {
			$sql = "SELECT id, label, plot FROM summary_user WHERE id IN (1,2,3,4,5) ORDER BY id";
			$db->setQuery($sql);
			$results = $db->loadObjectList();
			if ($results) 
			{
				$results[0]->label = preg_replace('/\{(.*)\}/','',$results[0]->label);

				$this->outputData($option, $results[0]->label, $db, $results[0]->id, $period, $datetime);
			}
		//}
		$html .= '</div><!-- / .subject -->' . "\n";
		//$html .= '</div><!-- / .main section -->' . "\n";

		return $html;
	}
}
