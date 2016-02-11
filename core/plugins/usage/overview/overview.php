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
 * Usage plugin class for overview
 */
class plgUsageOverview extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the name of the area this plugin retrieves records for
	 *
	 * @return     array
	 */
	public function onUsageAreas()
	{
		return array(
			'overview' => Lang::txt('PLG_USAGE_OVERVIEW')
		);
	}

	/**
	 * Strip Usage GET variables
	 *
	 * @param      string $url URL
	 * @return     string
	 */
	private function _usageurlstrip($url)
	{
		$pvar = strpos($url, 'period=');
		if ($pvar)
		{
			$pvar--;
			$url = substr($url, 0, $pvar);
		}
		return $url;
	}

	/**
	 * Returns TRUE if there is data in the database
	 * for the date passed to it, FALSE otherwise.
	 *
	 * @param      object &$db Parameter description (if any) ...
	 * @param      unknown $yearmonth Parameter description (if any) ...
	 * @param      unknown $period Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _check_for_data(&$db, $yearmonth, $period)
	{
		$sql = "SELECT COUNT(datetime) FROM summary_user_vals WHERE datetime LIKE '$yearmonth-%' AND period='$period'";
		$db->setQuery($sql);
		$result = $db->loadResult();

		if ($result && $result > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Write a row of data as a table row
	 *
	 * @param      object &$db Parameter description (if any) ...
	 * @param      string $id Parameter description (if any) ...
	 * @param      string $period Parameter description (if any) ...
	 * @param      string $datetime Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function _print_user_row(&$db, $id, $period, $datetime)
	{
		$html = '';

		$i = 0;

		$sql = "SELECT value, valfmt FROM summary_user_vals WHERE rowid='$id' AND period='$period' AND datetime='$datetime' ORDER BY colid";
		$db->setQuery($sql);
		$results = $db->loadObjectList();
		if ($results)
		{
			foreach ($results as $row)
			{
				$i++;
				$cls = ($i >= 6) ? ' class="group"' : '';
				if ($i == 1)
				{
					$cls = ' class="highlight"';
				}
				if ($i == 2)
				{
					$res_iden = $row->value;
				}

				if ($i == 7)
				{
					$org_iden = $row->value;
				}

				switch ($i)
				{
					case 3:
					case 4:
					case 5:
					case 6:
						$val = (intval($res_iden) > 0) ? ($row->value/$res_iden) * 100 : 0;
					break;

					case 8:
					case 9:
					case 10:
					case 11:
						$val = (intval($org_iden) > 0) ? ($row->value/$org_iden) * 100 : 0;
					break;

					default:
						$val = $row->value;
					break;
				}
				$html .= "\t\t\t" . '<td' . $cls . '>' . trim($this->_fmt_result($val, $row->valfmt)) . '</td>' . "\n";
			}
		}
		if ($i == 0)
		{
			$html .= $this->_empty_rows(11);
		}
		return $html;
	}

	/**
	 * Generate a sparkline (inline chart)
	 *
	 * @param      object  $db       JDatabase
	 * @param      integer $id       Row ID
	 * @param      string  $period   Time period
	 * @param      string  $datetime Timestamp
	 * @return     string
	 */
	private function _getSparkline($db, $id, $period, $datetime)
	{
		$sparkline = '';

		$thisyear = date("Y");
		$tp = $thisyear - 2000;
		$limit = $tp * 12;
		$limit = 12;

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

			// Generate the sparkline
			$sparkline .= '<span class="sparkline">' . "\n";
			foreach ($results as $result)
			{
				$height = $highest ? round(($result->value / $highest)*100) : 0;
				$sparkline .= "\t" . '<span class="index">';
				$sparkline .= '<span class="count" style="height: ' . $height . '%;" title="' . Date::of($result->datetime)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . ': ' . trim($this->_fmt_result($result->value, $result->valfmt)) . '">';
				$sparkline .= trim($this->_fmt_result($result->value, $result->valfmt));
				$sparkline .= '</span> ';
				$sparkline .= '</span>' . "\n";
			}
			$sparkline .= '</span>' . "\n";
		}

		return $sparkline;
	}

	/**
	 * Generate empty table cells
	 *
	 * @param      integer $n Number of cells
	 * @return     string
	 */
	private function _empty_rows($n)
	{
		$html = '';
		$i = 0;
		for ($i=0, $n; $i < $n; $i++)
		{
			$cls = ($i >= 6) ? ' class="group"' : '';
			$html .= "\t\t\t" . '<td' . $cls . '>-</td>' . "\n";
		}
		return $html;
	}

	/**
	 * Format a result
	 *
	 * @param      mixed   $value Value to format
	 * @param      integer $fmt   Format to use
	 * @return     mixed
	 */
	private function _fmt_result($value, $fmt)
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
			if ($value < '60')
			{
				$val = number_format($value) . ' seconds';
			}
			else if ($value >= '60' && $value < '3600')
			{
				$val = number_format($value/60) . ' minutes';
			}
			else if ($value >= '3600'  && $value < '86400')
			{
				$val = number_format($value/3600) . ' hours';
			}
			else if ($value >= '86400')
			{
				$val = number_format($value/86400) . ' days';
			}
			else
			{
				$val = number_format($value);
			}
			return $val;
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
	private function _navlinks($option, $task, $period='prior12')
	{
		$html  = '<div id="sub-sub-menu">' . "\n";
		$html .= "\t" . '<ul>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'prior12')
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.Route::url('index.php?option=' . $option . '&task=' . $task . '&period=prior12') . '"><span>' . Lang::txt('PLG_USAGE_PERIOD_PRIOR12') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'month')
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.Route::url('index.php?option=' . $option . '&task=' . $task . '&period=month') . '"><span>' . Lang::txt('PLG_USAGE_PERIOD_MONTH') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'qtr')
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.Route::url('index.php?option=' . $option . '&task=' . $task . '&period=qtr') . '"><span>' . Lang::txt('PLG_USAGE_PERIOD_QTR') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'year')
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.Route::url('index.php?option=' . $option . '&task=' . $task . '&period=year') . '"><span>' . Lang::txt('PLG_USAGE_PERIOD_YEAR') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'fiscal')
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.Route::url('index.php?option=' . $option . '&task=' . $task . '&period=fiscal') . '"><span>' . Lang::txt('PLG_USAGE_PERIOD_FISCAL') . '</span></a></li>' . "\n";
		$html .= "\t" . '</ul>' . "\n";
		$html .= '</div>' . "\n";

	    return $html;
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

		$config = Component::params($option);

		// Incoming
		$period = Request::getVar('period', 'prior12');
		$selectedPeriod = Request::getVar('selectedPeriod', '');

		if (!$selectedPeriod)
		{
			$db->setQuery("SELECT MAX(datetime) FROM summary_misc_vals");
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
		if ($checkyear <= '2007')
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
		$datetime = $selectedPeriod . '-00 00:00:00';

		// Set the pathway
		Pathway::append(Lang::txt('PLG_USAGE_PERIOD_' . strtoupper($period)), 'index.php?option=' . $option . '&task=' . $task . '&period=' . $period);

		// Build the HTML
		$html  = $this->_navlinks($option, $task, $period);
		$html .= '<form method="post" action="' . Route::url('index.php?option=' . $option . '&task=' . $task . '&period=' . $period) . '">' . "\n";
		$html .= "\t" . '<fieldset class="filters"><label>' . Lang::txt('PLG_USAGE_SHOW_DATA_FOR') . ': ';

		$html .= '<select name="selectedPeriod">' . "\n";
		switch ($period)
		{
			case '12':
			case 'prior12':
			case 'nice':
				$option = 'prior12';
				$period = '12';

				$arrayMonths = array_values($months);
				for ($i = $cur_year; $i >= $year_data_start; $i--)
				{
					foreach ($monthsReverse as $key => $month)
					{
						if ($key == '12')
						{
							$nextmonth = 'Jan';
						}
						else
						{
							$nextmonth = $arrayMonths[floor(array_search($month, $arrayMonths))+1];
						}
						$value = $i . '-' . $key;
						if ($this->_check_for_data($db, $value, 12))
						{
							$html .= '<option value="' . $value . '"';
							if ($value == $selectedPeriod)
							{
								$html .= ' selected="selected"';
							}
							$html .= '>' . $nextmonth . ' ';
							if ($key == 12)
							{
								$html .= $i;
							}
							else
							{
								$html .= $i - 1;
							}
							$html .= ' - ' . $month . ' ' . $i . '</option>' . "\n";
						}
					}
				}
			break;

			case '1':
			case 'month':
				$option = 'month';
				$period = '1';

				for ($i = $cur_year; $i >= $year_data_start; $i--)
				{
					foreach ($monthsReverse as $key => $month)
					{
						$value = $i . '-' . $key;
						if ($this->_check_for_data($db, $value, 1))
						{
							$html .= '<option value="' . $value . '"';
							if ($value == $selectedPeriod)
							{
								$html .= ' selected="selected"';
							}
							$html .= '>' . $month . ' ' . $i . '</option>' . "\n";
						}
					}
				}
			break;

			case '3':
			case 'qtr':
				$option = 'qtr';
				$period = '3';

				$qtd_found = 0;
				foreach ($monthsReverse as $key => $month)
				{
					$value = $cur_year . '-' . $key;
					if (!$qtd_found && $this->_check_for_data($db, $value, 3))
					{
						$html .= '<option value="' . $value . '"';
						if ($value == $selectedPeriod)
						{
							$html .= ' selected="selected"';
						}
						$html .= '>';
						if ($key <= 3)
						{
							$key = 0;
							$html .= 'Jan';
						}
						elseif ($key <= 6)
						{
							$key = 3;
							$html .= 'Apr';
						}
						elseif ($key <= 9)
						{
							$key = 6;
							$html .= 'Jul';
						}
						else
						{
							$key = 9;
							$html .= 'Oct';
						}
						$html .= ' ' . $cur_year . ' - ' . $month . ' ' . $cur_year . '</option>' . "\n";
						$qtd_found = 1;
					}
				}
				$qtr_found = 0;
				for ($j = $cur_year; $j >= $year_data_start; $j--)
				{
					for ($i = 12; $i > 0; $i = $i - 3)
					{
						if ($qtr_found && $key)
						{
							$i = $key;
							$qtd_found = 0;
						}
						$value = $j . '-' . sprintf("%02d", $i);
						if ($this->_check_for_data($db, $value, 3))
						{
							$html .= '<option value="' . $value . '"';
							if ($value == $selectedPeriod)
							{
								$html .= ' selected="selected"';
							}
							$html .= '>';
							if ($i == 3)
							{
								$html .= 'Jan';
							}
							elseif ($i == 6)
							{
								$html .= 'Apr';
							}
							elseif ($i == 9)
							{
								$html .= 'Jul';
							}
							else
							{
								$html .= 'Oct';
							}
							$html .= ' ' . $j . ' - ';
							if ($i == 3)
							{
								$html .= 'Mar';
							}
							elseif ($i == 6)
							{
								$html .= 'Jun';
							}
							elseif ($i == 9)
							{
								$html .= 'Sep';
							}
							else
							{
								$html .= 'Dec';
							}
							$html .= ' ' . $j . '</option>' . "\n";
						}
					}
				}
			break;

			case '13':
			case 'fiscal':
				$option = 'fiscal';
				$period = '13';

				$ytd_found = 0;
				$full_year = $cur_year - 1;
				foreach ($monthsReverse as $key => $month)
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && $this->_check_for_data($db, $value, 0))
					{
						$html .= '<option value="' . $value . '"';
						if ($value == $selectedPeriod)
						{
							$html .= ' selected="selected"';
						}
						$html .= '>Oct ';
						if ($cur_month >= 9)
						{
							$html .= $cur_year;
							$full_year = $cur_year;
						}
						else
						{
							$html .= $cur_year - 1;
							$full_year = $cur_year - 1;
						}
						$html .= ' - ' . $month . ' ' . $cur_year . '</option>' . "\n";
						$ytd_found = 1;
					}
				}
				for ($i = $full_year; $i >= $year_data_start; $i--)
				{
					$value = $i . '-09';
					if ($this->_check_for_data($db, $value, 0))
					{
						$html .= '<option value="' . $value . '"';
						if ($value == $selectedPeriod)
						{
							$html .= ' selected="selected"';
						}
						$html .= '>Oct ';
						$html .= $i - 1;
						$html .= ' - Sep ' . $i . '</option>' . "\n";
					}
				}
			break;

			case '0':
			case 'year':
				$option = 'year';
				$period = '0';

				$ytd_found = 0;
				foreach ($monthsReverse as $key => $month)
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && $this->_check_for_data($db, $value, 0))
					{
						$html .= '<option value="'. $value .'"';
						if ($value == $selectedPeriod)
						{
							$html .= ' selected="selected"';
						}
						$html .= '>Jan - ' . $month . ' ' . $cur_year . '</option>' . "\n";
						$ytd_found = 1;
					}
				}
				for ($i = $cur_year - 1; $i >= $year_data_start; $i--)
				{
					$value = $i . '-12';
					if ($this->_check_for_data($db, $value, 0))
					{
						$html .= '<option value="' . $value .'"';
						if ($value == $selectedPeriod)
						{
							$html .= ' selected="selected"';
						}
						$html .= '>Jan - Dec ' . $i . '</option>' . "\n";
					}
				}
			break;
		}
		$html .= '</select></label> <input type="submit" value="' . Lang::txt('PLG_USAGE_VIEW') . '" /></fieldset>' . "\n";
		$html .= '</form>' . "\n";

		$tbl_cnt = 0;
		$html .= '<table summary="' . Lang::txt('A break-down of site visitors.') . '">' . "\n";
		$html .= "\t" . '<caption>' . Lang::txt('Table ' . ++$tbl_cnt . ': User statistics') . '</caption>' . "\n";
		$html .= "\t" . '<thead>' . "\n";
		$html .= "\t\t" . '<tr>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" rowspan="2" colspan="2">' . Lang::txt('Users') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" rowspan="2" class="numerical-data">' . Lang::txt('Totals') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="colgroup" colspan="5">' . Lang::txt('Residence') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="colgroup" colspan="5">' . Lang::txt('Organization') . '</th>' . "\n";
		$html .= "\t\t" . '</tr>' . "\n";
		$html .= "\t\t" . '<tr>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('Identified') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('US') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('Asia') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('Europe') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('Other') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="group numerical-data">' . Lang::txt('Identified') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="group numerical-data" abbr="' . Lang::txt('Education') . '">' . Lang::txt('Edu.') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="group numerical-data" abbr="' . Lang::txt('Industry') . '">' . Lang::txt('Ind.') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="group numerical-data" abbr="' . Lang::txt('Government') . '">' . Lang::txt('Gov.') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="group numerical-data">' . Lang::txt('Other') . '</th>' . "\n";
		$html .= "\t\t" . '</tr>' . "\n";
		$html .= "\t" . '</thead>' . "\n";
		$html .= "\t" . '<tbody>' . "\n";

		print (PATH_APP . $config->get('charts_path'));
		$db->setQuery("SELECT id, label, plot FROM summary_user WHERE id IN (1,2,3,4,5) ORDER BY id");
		$results = $db->loadObjectList();
		if ($results)
		{
			$i = 0;
			$cls = 'even';
			foreach ($results as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
				if ($i == 0)
				{
					$cls = 'summary';
				}

				$label = preg_replace('/\{(.*)\}/', '<sup><a href="#fn\\1">\\1</a></sup>', $row->label);

				$sparkline = $this->_getSparkline($db, $row->id, $period, $datetime);

				$html .= "\t\t" . '<tr class="' . $cls . '">' . "\n";
				$html .= "\t\t\t" . '<th scope="row">' . trim($label) . '</th>' . "\n";
				if ($row->plot == '1')
				{
					$img = $config->get('charts_path') . DS . substr($datetime, 0, 7) . '-' . $period . '-u' . $row->id;
					if (is_file(PATH_APP . DS . $img . '.gif'))
					{
						$html .= "\t\t\t" . '<td><a href="/app' . $img . '.gif" title="DOM:users1' . $i . '" class="fixedImgTip" rel="external"><img src="/app' . $img . 'thumb.gif" alt="" /></a><br /><div style="display:none;" id="users1' . $i . '"><img src="/app' . $img . '.gif" alt="" /></div></td>' . "\n";
					}
					else if (isset($sparkline))
					{
						$html .= "\t\t\t" . '<td>' . $sparkline . '</td>' . "\n";
					}
					else
					{
						$html .= "\t\t\t" . '<td>&nbsp;</td>' . "\n";
					}
				}
				else if (isset($sparkline))
				{
					$html .= "\t\t\t" . '<td>' . $sparkline . '</td>' . "\n";
				}
				else
				{
					$html .= "\t\t\t" . '<td>&nbsp;</td>' . "\n";
				}
				$html .= $this->_print_user_row($db, $row->id, $period, $datetime);
				$html .= "\t\t" . '</tr>' . "\n";
				$i++;
			}
		}
		else
		{
			$html .= "\t\t" . '<tr class="odd">' . "\n";
			$html .= "\t\t\t" . '<td colspan="13" class="textual-data">' . Lang::txt('No data found.') . '</td>' . "\n";
			$html .= "\t\t" . '</tr>' . "\n";
		}
		$html .= "\t" . '</tbody>' . "\n";
		$html .= '</table>' . "\n";

		// Start simulation Usage
		$html .= '<table summary="' . Lang::txt('Simulation Usage') . '">' . "\n";
		$html .= "\t" . '<caption>' . Lang::txt('Table ' . ++$tbl_cnt . ': Simulation Usage') . '</caption>' . "\n";
		$html .= "\t" . '<tbody>' . "\n";

		$db->setQuery("SELECT a.label,b.value,b.valfmt,a.plot,a.id FROM summary_simusage AS a, summary_simusage_vals AS b WHERE a.id=b.rowid AND b.period = '$period' AND b.datetime = '$datetime' ORDER BY a.id");
		$results = $db->loadObjectList();
		if ($results)
		{
			$cls = 'even';
			foreach ($results as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				$label = preg_replace('/\{(.*)\}/', '<sup><a href="#fn\\1">\\1</a></sup>', $row->label);

				//$sparkline = $this->_getSparkline($db, $row->id, $period, $datetime);

				$html .= "\t\t" . '<tr class="' . $cls . '">' . "\n";
				$html .= "\t\t\t" . '<th scope="row">' . trim($label) . '</th>' . "\n";
				$html .= "\t\t\t" . '<td>'. $this->_fmt_result($row->value, $row->valfmt) .'</td>' . "\n";
				if ($row->plot == '1')
				{
					$img = $config->get('charts_path') . DS . substr($datetime, 0, 7) . '-' . $period . '-s' . $row->id;
					if (is_file(PATH_APP . DS . $img . '.gif'))
					{
						$html .= "\t\t\t" . '<td><a href="/app' . $img . '.gif" title="DOM:sim' . $i . '" class="fixedImgTip" rel="external"><img src="/app' . $img . 'thumb.gif" alt="" /></a><br /><div style="display:none;" id="sim' . $i . '"><img src="/app' . $img . '.gif" alt="" /></div></td>' . "\n";
					//} else if (isset($sparkline)) {
					//	$html .= "\t\t\t" . '<td>'.$sparkline.'</td>' . "\n";
					}
					else
					{
						$html .= "\t\t\t" . '<td>&nbsp;</td>' . "\n";
					}
				//} else if (isset($sparkline)) {
				//	$html .= "\t\t\t" . '<td>'.$sparkline.'</td>' . "\n";
				}
				else
				{
					$html .= "\t\t\t" . '<td>&nbsp;</td>' . "\n";
				}
				$html .= "\t\t" . '</tr>' . "\n";

				$i++;
			}
		}
		else
		{
			$html .= "\t\t" . '<tr class="odd">' . "\n";
			$html .= "\t\t\t" . '<td colspan="2" class="textual-data">' . Lang::txt('No data found.') . '</td>' . "\n";
			$html .= "\t\t" . '</tr>' . "\n";
		}
		$html .= "\t" . '</tbody>' . "\n";
		$html .= '</table>' . "\n";

		// Start miscellaneous
		$html .= '<table summary="' . Lang::txt('Miscellaneous Statistics') . '">' . "\n";
		$html .= "\t" . '<caption>' . Lang::txt('Table ' . ++$tbl_cnt . ': Miscellaneous Statistics') . '</caption>' . "\n";
		$html .= "\t" . '<tbody>' . "\n";

		$db->setQuery("SELECT a.label,b.value,b.valfmt FROM summary_misc AS a, summary_misc_vals AS b WHERE a.id=b.rowid AND b.period = '$period' AND b.datetime = '$datetime' ORDER BY a.id");
		$results = $db->loadObjectList();
		if ($results)
		{
			$cls = 'even';
			foreach ($results as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				//$label = str_replace('{','<sup>',$row->label);
				//$label = str_replace('}','</sup>',$label);
				$label = preg_replace('/\{(.*)\}/', '<sup><a href="#fn\\1">\\1</a></sup>', $row->label);

				$html .= "\t\t" . '<tr class="' . $cls . '">' . "\n";
				$html .= "\t\t\t" . '<th scope="row">' . trim($label) . '</th>' . "\n";
				$html .= "\t\t\t" . '<td>'. $this->_fmt_result($row->value, $row->valfmt) . '</td>' . "\n";
				$html .= "\t\t" . '</tr>' . "\n";
			}
		}
		else
		{
			$html .= "\t\t" . '<tr class="odd">' . "\n";
			$html .= "\t\t\t" . '<td colspan="2" class="textual-data">' . Lang::txt('No data found.') . '</td>' . "\n";
			$html .= "\t\t" . '</tr>' . "\n";
		}
		$html .= "\t" . '</tbody>' . "\n";
		$html .= '</table>' . "\n";

	/*
		// "and more" Usage
		$html .= '<table summary="' . Lang::txt('&quot;and more&quot; Usage') . '">' . "\n";
		$html .= "\t" . '<caption>' . Lang::txt('Table '.++$tbl_cnt.': &quot;and more&quot; Usage') . '</caption>' . "\n";
		$html .= "\t" . '<thead>' . "\n";
		$html .= "\t\t" . '<tr>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" rowspan="2">' . Lang::txt('Type') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="colgroup" colspan="2">' . Lang::txt('Users') . '</th>' . "\n";
		$html .= "\t\t" . '</tr>' . "\n";
		$html .= "\t\t" . '<tr>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('Interactive') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('Download') . '</th>' . "\n";
		$html .= "\t\t" . '</tr>' . "\n";
		$html .= "\t" . '</thead>' . "\n";
		$html .= "\t" . '<tbody>' . "\n";
		$id = 1;
		$trows = '';
		$cls = 'even';
		while ($id < 9)
		{
			$sql = "SELECT a.id, a.label, b.value,b.colid,b.valfmt FROM summary_andmore a, summary_andmore_vals b WHERE a.id=b.rowid AND period='".$period."' AND datetime='".$datetime."' AND a.id='".$id."' ORDER BY b.colid";
			$db->setQuery($sql);
			$results = $db->loadObjectList();
			if ($results) {
				foreach ($results as $row)
				{
					//$label = str_replace('{','<sup>',$row->label);
					//$label = str_replace('}','</sup>',$label);
					$label = preg_replace('/\{(.*)\}/','<sup><a href="#fn\\1">\\1</a></sup>',$row->label);

					$col = $row->colid;
					if ($col == 1) {
	               		$value1 = $this->_fmt_result($row->value,$row->valfmt);
	                } else if ($col == 2) {
	                	$value2 = $this->_fmt_result($row->value,$row->valfmt);
	                }
	            }
	            $cls = ($cls == 'even') ? 'odd' : 'even';

				$trows .= "\t\t" . '<tr class="'.$cls.'">' . "\n";
	            $trows .= "\t\t\t" . '<th scope="row">'.trim($label).'</th>' . "\n";
	            $trows .= "\t\t\t" . '<td>'.$value1.'</td>' . "\n";
	            $trows .= "\t\t\t" . '<td>'.$value2.'</td>' . "\n";
	            $trows .= "\t\t" . '</tr>' . "\n";
		    }
		    $id++;
		}
		$id = 9;
		while ($id < 13)
		{
			$sql = "SELECT a.id, a.label, b.value,b.colid,b.valfmt FROM summary_andmore a, summary_andmore_vals b WHERE a.id=b.rowid AND period='".$period."' AND datetime='".$datetime."' AND a.id='".$id."' ORDER BY b.colid";
		    $db->setQuery($sql);
			$results = $db->loadObjectList();
			if ($results) {
				foreach ($results as $row)
				{
					//$label = str_replace('{','<sup>',$row->label);
					//$label = str_replace('}','</sup>',$label);
					$label = preg_replace('/\{(.*)\}/','<sup><a href="#fn\\1">\\1</a></sup>',$row->label);

					$col = $row->colid;
	                if ($col == 1) {
	                	$value1 = $this->_fmt_result($row->value,$row->valfmt);
	                } else if ($col == 2) {
	                	$value2 = $this->_fmt_result($row->value,$row->valfmt);
	                }
	            }
	            $cls = ($cls == 'even') ? 'odd' : 'even';

				$trows .= "\t\t" . '<tr class="'.$cls.'">' . "\n";
	            $trows .= "\t\t\t" . '<th scope="row">'.trim($label).'</th>' . "\n";
	            $trows .= "\t\t\t" . '<td>'.$value1.'</td>' . "\n";
	            $trows .= "\t\t\t" . '<td>'.$value2.'</td>' . "\n";
	            $trows .= "\t\t" . '</tr>' . "\n";
			}
		    $id++;
		}
		if ($trows) {
			$html .= $trows;
		} else {
			$html .= "\t\t" . '<tr class="odd">' . "\n";
			$html .= "\t\t\t" . '<td colspan="3" class="textual-data">' . Lang::txt('No data found.') . '</td>' . "\n";
			$html .= "\t\t" . '</tr>' . "\n";
		}
		$html .= "\t" . '</tbody>' . "\n";
		$html .= '</table>' . "\n";


		// Collaboration Usage
		$html .= '<table summary="' . Lang::txt('Collaboration Usage') . '">' . "\n";
		$html .= "\t" . '<caption>' . Lang::txt('Table '.++$tbl_cnt.': Collaboration Usage') . '</caption>' . "\n";
		$html .= "\t" . '<tbody>' . "\n";
		$sql = "SELECT a.label,b.value,b.valfmt FROM summary_collab AS a, summary_collab_vals AS b WHERE a.id=b.rowid AND b.period = '".$period."' AND b.datetime = '".$datetime."' ORDER BY a.id";
		$db->setQuery($sql);
		$results = $db->loadObjectList();
		if ($results) {
			$cls = 'even';
	    	foreach ($results as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				//$label = str_replace('{','<sup>',$row->label);
				//$label = str_replace('}','</sup>',$label);
				$label = preg_replace('/\{(.*)\}/','<sup><a href="#fn\\1">\\1</a></sup>',$row->label);

	       		$html .= "\t\t" . '<tr class="'.$cls.'">' . "\n";
	            $html .= "\t\t\t" . '<th scope="row">'.trim($label).'</th>' . "\n";
	            $html .= "\t\t\t" . '<td>'. $this->_fmt_result($row->value,$row->valfmt) .'</td>' . "\n";
	            $html .= "\t\t" . '</tr>' . "\n";
			}
		} else {
			$html .= "\t\t" . '<tr class="odd">' . "\n";
			$html .= "\t\t\t" . '<td colspan="2" class="textual-data">' . Lang::txt('No data found.') . '</td>' . "\n";
			$html .= "\t\t" . '</tr>' . "\n";
		}
		$html .= "\t" . '</tbody>' . "\n";
		$html .= '</table>' . "\n";

	*/

		$html .= '<table summary="' . Lang::txt('User statistics by registered/unregistered') . '">' . "\n";
		$html .= "\t" . '<caption><a name="tot"></a>' . Lang::txt('Table '. ++$tbl_cnt . ': User statistics by registered/unregistered') . '</caption>' . "\n";
		$html .= "\t" . '<thead>' . "\n";
		$html .= "\t\t" . '<tr>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" rowspan="2" colspan="2">' . Lang::txt('Users') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" rowspan="2" class="numerical-data">' . Lang::txt('Totals') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="colgroup" colspan="5">' . Lang::txt('Residence') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="colgroup" colspan="5">' . Lang::txt('Organization') . '</th>' . "\n";
		$html .= "\t\t" . '</tr>' . "\n";
		$html .= "\t\t" . '<tr>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('Identified') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('US') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('Asia') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('Europe') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="numerical-data">' . Lang::txt('Other') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="group numerical-data">' . Lang::txt('Identified') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="group numerical-data" abbr="' . Lang::txt('Education') . '">' . Lang::txt('Edu.') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="group numerical-data" abbr="' . Lang::txt('Industry') . '">' . Lang::txt('Ind.') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="group numerical-data" abbr="' . Lang::txt('Government') . '">' . Lang::txt('Gov.') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th scope="col" class="group numerical-data">' . Lang::txt('Other') . '</th>' . "\n";
		$html .= "\t\t" . '</tr>' . "\n";
		$html .= "\t" . '</thead>' . "\n";
		$html .= "\t" . '<tbody>' . "\n";

		$db->setQuery("SELECT id, label, plot FROM summary_user WHERE id IN (1,6,7,8) ORDER BY id");
		$results = $db->loadObjectList();
		if ($results)
		{
			$i = 5;
			$cls = 'even';
			foreach ($results as $row)
			{
				//$label = str_replace('{','<sup>',$row->label);
				//$label = str_replace('}','</sup>',$label);
				$label = preg_replace('/\{(.*)\}/', '<sup><a href="#fn\\1">\\1</a></sup>', $row->label);

				$cls = ($cls == 'even') ? 'odd' : 'even';
				if ($i == 5)
				{
					$cls = 'summary';
				}

				$sparkline = $this->_getSparkline($db, $row->id, $period, $datetime);

				$html .= "\t\t" . '<tr class="'.$cls.'">' . "\n";
				$html .= "\t\t\t" . '<th scope="row">'.trim($label).'</th>' . "\n";
				if ($row->plot == '1')
				{
					$img = $config->get('charts_path') . DS . substr($datetime, 0, 7) . '-' . $period . '-u' . $row->id;
					if (is_file(PATH_APP . DS . $img . '.gif'))
					{
						$html .= "\t\t\t" . '<td><a href="/app' . $img . '.gif" title="DOM:users2' . $i . '" class="fixedImgTip" rel="external"><img src="/app' . $img . 'thumb.gif" alt="" /></a><br /><div style="display:none;" id="users2' . $i . '"><img src="/app' . $img . '.gif" alt="" /></div></td>' . "\n";
					}
					else if (isset($sparkline))
					{
						$html .= "\t\t\t" . '<td>' . $sparkline . '</td>' . "\n";
					}
					else
					{
						$html .= "\t\t\t" . '<td>&nbsp;</td>' . "\n";
					}
				}
				else if (isset($sparkline))
				{
					$html .= "\t\t\t" . '<td>'.$sparkline.'</td>' . "\n";
				}
				else
				{
					$html .= "\t\t\t" . '<td>&nbsp;</td>';
				}
				$html .= $this->_print_user_row($db, $row->id, $period, $datetime);
				$html .= "\t\t" . '</tr>' . "\n";
				$i++;
			}
		}
		else
		{
			$html .= "\t\t" . '<tr class="odd">' . "\n";
			$html .= "\t\t\t" . '<td colspan="13" class="textual-data">' . Lang::txt('No data found.') . '</td>' . "\n";
			$html .= "\t\t" . '</tr>' . "\n";
		}
		$html .= "\t" . '</tbody>' . "\n";
		$html .= '</table>' . "\n";

		$html .= '<div class="footnotes">' . "\n";
		$html .= "\t" . '<hr />' . "\n";
		$html .= "\t" . '<ol>' . "\n";
		$html .= "\t\t" . '<li id="fn1"><a name="fn1"></a>Sum of Registered Users<sup><a href="#fn2">2</a></sup>, Unregistered Interactive Users<sup><a href="#fn3">3</a></sup> and Unregistered Download Users<sup><a href="#fn4">4</a></sup></li>' . "\n";
		$html .= "\t\t" . '<li id="fn2"><a name="fn2"></a>Number of Users that logged in. User registration assigns a unique login to each individual user.</li>' . "\n";
		$html .= "\t\t" . '<li id="fn3"><a name="fn3"></a>Number of Unregistered Users, identified by unique hosts/IPs, that had an active Session <sup><a href="#fn10">10</a></sup> without logging in. Does not include known web bots/crawlers.</li>' . "\n";
		$html .= "\t\t" . '<li id="fn4"><a name="fn4"></a>Number of Unregistered users, identified by unique hosts/IPs, that had an active session of less than 15 minutes without logging in and downloaded a non-interactive resource such as PDF or podcast. Does not include web bots/crawlers.</li>' . "\n";
		$html .= "\t\t" . '<li id="fn5"><a name="fn5"></a>Number of Registered Users<sup><a href="#fn2">2</a></sup> that ran one or more simulation runs.</li>' . "\n";
		$html .= "\t\t" . '<li id="fn6"><a name="fn6"></a>All Unregistered Users, identified by unique hosts/IPs, that had an active Session <sup><a href="#fn10">10</a></sup>. Does not include known web bots/crawlers.</li>' . "\n";
		$html .= "\t\t" . '<li id="fn7"><a name="fn7"></a>All Unregistered users, identified by unique hosts/IPs that downloaded a non-interactive resource such as PDF or podcast. Does not include known web bots/crawlers.</li>' . "\n";
		$html .= "\t\t" . '<li id="fn8"><a name="fn8"></a>Sum of Simulation Users <sup><a href="#fn5">5</a></sup> + Unregistered Interactive Users <sup><a href="#fn3">3</a></sup> including web bots/crawlers.</li>' . "\n";
		$html .= "\t\t" . '<li id="fn9"><a name="fn9"></a>Number of Simulation users that returned after a gap of 3 months.</li>' . "\n";
		$html .= "\t\t" . '<li id="fn10"><a name="fn10"></a>Begins when an IP is active on the site for at least 15 minutes. Ends when inactive for more than 30 minutes, including time spent viewing videos.</li>' . "\n";
		$html .= "\t\t" . '<li id="fn11"><a name="fn11"></a>Identified by a unique IP address / hostname.</li>' . "\n";
		$html .= "\t\t" . '<li id="fn12"><a name="fn12"></a>Based on MIT OCW metric of Visits: A visit is activity by a unique visitor delimitated by a 30 minute absence from the site on either side of the activity.<br />These visits correspond to unique visitors <sup><a href="#fn11">11</a></sup>. Does not include known web bots/crawlers</li>' . "\n";
		$html .= "\t\t" . '<li id="fn13"><a name="fn13"></a>Number of Simulation sessions that were shared between 2 or more users.</li>' . "\n";
		$html .= "\t" . '</ol>' . "\n";
		$html .= '</div><!-- / .footnotes -->' . "\n";

		return $html;
	}
}
