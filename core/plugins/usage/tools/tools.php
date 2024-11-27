<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Usage plugin class for tools
 */
class plgUsageTools extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the name of the area this plugin retrieves records for
	 *
	 * @return  array
	 */
	public function onUsageAreas()
	{
		return array(
			'tools' => Lang::txt('PLG_USAGE_TOOLs')
		);
	}

	/**
	 * Get the top list for a tool
	 *
	 * @param   object  $database      Database
	 * @param   string  $period        Tiem period (quarterly, yearly, etc)
	 * @param   string  $dthis         Time (YYYY-MM)
	 * @param   string  $s_top         Top value
	 * @param   string  $table_header
	 * @return  string HTML
	 */
	private function gettoplist($database, $period, $dthis, $s_top, $table_header='Value')
	{
		$html = '';

		$sql = "SELECT * FROM `#__stats_topvals` WHERE top = " . $database->quote($s_top) . " AND datetime = " . $database->quote($dthis . '-00') . " AND period = " . $database->quote($period) . " ORDER BY rank";
		$database->setQuery($sql);
		$results = $database->loadObjectList();

		$cls = 'even';
		$html .= "\t" . '<thead>' . "\n";
		$html .= "\t\t" . '<tr>' . "\n";
		$html .= "\t\t\t" . '<th class="numerical-data">' . Lang::txt('#') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th>' . Lang::txt('Tool') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th class="numerical-data">' . Lang::txt($table_header) . '</th>' . "\n";
		$html .= "\t\t\t" . '<th class="numerical-data">' . Lang::txt('Percent') . '</th>' . "\n";
		$html .= "\t\t" . '</tr>' . "\n";
		$html .= "\t" . '</thead>' . "\n";
		$count = 0;

		if ($results)
		{
			foreach ($results as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
				$vl = '-';
				if ($row->rank == '0')
				{
					$total = $row->value;
					if ($s_top == "6" || $s_top == "7" || $s_top == "8")
					{
						$value = $this->time_units($row->value);
					}
					else
					{
						$value = number_format($row->value);
					}
					$html .= "\t" . '<tfoot>' . "\n";
					$html .= "\t\t" . '<tr class="summary">' . "\n";
					$html .= "\t\t\t" . '<th colspan="2" class="numerical-data">' . $row->name . '</th>' . "\n";
					$html .= "\t\t\t" . '<th class="numerical-data">' . $value . '</th>' . "\n";
					if ($total)
					{
						$vl = number_format((($row->value/$total)*100), 2);
					}
					$html .= "\t\t\t" . '<th class="numerical-data">' . $vl . '%</th>' . "\n";
					$html .= "\t\t" . '</tr>' . "\n";
					$html .= "\t" . '</tfoot>' . "\n";
					$html .= "\t" . '<tbody>' . "\n";
				}
				else
				{
					$name = preg_split('/ ~ /', $row->name);
					if ($s_top == "6" || $s_top == "7" || $s_top == "8")
					{
						$value = $this->time_units($row->value);
					}
					else
					{
						$value = number_format($row->value);
					}

					if ($value == 0)
					{
						continue;
					}

					$html .= "\t\t" . '<tr class="' . $cls . '">' . "\n";
					$html .= "\t\t\t" . '<td>' . $row->rank . '</td>' . "\n";
					$html .= "\t\t\t" . '<td class="textual-data"><a href="'.Route::url('index.php?option=com_resources&id=' . $name[0] . '&active=usage') . '">' . $name[1] . '</a></td>' . "\n";
					$html .= "\t\t\t" . '<td>' . $value . '</td>' . "\n";
					if ($total)
					{
						$vl = number_format((($row->value/$total)*100), 2);
					}
					$html .= "\t\t\t" . '<td>' . $vl . '%</td>' . "\n";
					$html .= "\t\t" . '</tr>' . "\n";
				}
				$count++;
			}
		}
		else
		{
			$html .= "\t" . '<tbody>' . "\n";
			$html .= "\t\t" . '<tr class="odd">' . "\n";
			$html .= "\t\t\t" . '<td colspan="4">No data available to display.</td>' . "\n";
			$html .= "\t\t" . '</tr>' . "\n";
		}
		$html .= "\t" . '</tbody>' . "\n";

		return $html;
	}

	/**
	 * Summary for 'gettoprank_tools'
	 *
	 * Description (if any) ...
	 *
	 * @param   object $database Parameter description (if any) ...
	 * @return  string Return description (if any) ...
	 */
	private function gettoprank_tools($database)
	{
		$html = '';
		$count = 1;

		$sql = 'SELECT DISTINCT id, title, published, ranking FROM `#__resources` WHERE published = "1" AND standalone = "1" AND type = "7" AND access != "4" AND access != "1" ORDER BY ranking DESC';
		$database->setQuery($sql);
		$results = $database->loadObjectList();

		if ($results)
		{
			$cls = 'even';
			$html .= "\t" . '<thead>' . "\n";
			$html .= "\t\t" . '<tr>' . "\n";
			$html .= "\t\t\t" . '<th class="numerical-data">' . Lang::txt('#') . '</th>' . "\n";
			$html .= "\t\t\t" . '<th>' . Lang::txt('Tool') . '</th>' . "\n";
			$html .= "\t\t\t" . '<th class="numerical-data">' . Lang::txt('Ranking') . '</th>' . "\n";
			$html .= "\t\t" . '</tr>' . "\n";
			$html .= "\t" . '</thead>' . "\n";
			$html .= "\t" . '<tbody>' . "\n";
			foreach ($results as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
				$ranking = round($row->ranking, 2);

				if ($ranking == 0)
				{
					continue;
				}

				if ($row->published == "1")
				{
					$html .= "\t\t" . '<tr class="' . $cls . '">' . "\n";
					$html .= "\t\t\t" . '<td>' . $count . '</td>' . "\n";
					$html .= "\t\t\t" . '<td class="textual-data"><a href="'.Route::url('index.php?option=com_resources&id=' . $row->id . '&active=usage') . '">' . $row->title . '</a></td>' . "\n";
					$html .= "\t\t\t" . '<td>' . $ranking . '</td>' . "\n";
					$html .= "\t\t" . '</tr>' . "\n";
				}
				else
				{
					$html .= "\t\t" . '<tr class="' . $cls . '">' . "\n";
					$html .= "\t\t\t" . '<td>' . $count . '</td>' . "\n";
					$html .= "\t\t\t" . '<td class="textual-data">' . $row->title . '</td>' . "\n";
					$html .= "\t\t\t" . '<td>' . $ranking . '</td>' . "\n";
					$html .= "\t\t" . '</tr>' . "\n";
				}
				$count++;
			}
			$html .= "\t" . '</tbody>' . "\n";
		}

		if ($count == 1)
		{
			$html = "\t". '<p>No Data Available to Display</p>'. "\n";
		}

		return $html;
	}

	/**
	 * Gets top cited tools
	 *
	 * @param   object $database Database
	 * @return  string HTML
	 */
	private function gettopcited_tools($database)
	{
		$html = '';

		$sql = 'SELECT COUNT(DISTINCT c.id) FROM `#__resources` r, `#__citations` c, `#__citations_assoc` ca WHERE r.id = ca.oid AND ca.cid = c.id AND ca.tbl = "resource" AND r.type = "7" AND r.standalone = "1" AND c.published = "1"';
		$database->setQuery($sql);
		$result = $database->loadResult();

		$html .= "\t" . '<thead>' . "\n";
		$html .= "\t\t" . '<tr>' . "\n";
		$html .= "\t\t\t" . '<th class="numerical-data">' . Lang::txt('#') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th>' . Lang::txt('Tool') . '</th>' . "\n";
		$html .= "\t\t\t" . '<th class="numerical-data">' . Lang::txt('Citations') . '</th>' . "\n";
		$html .= "\t\t" . '</tr>' . "\n";
		$html .= "\t" . '</thead>' . "\n";

		if ($result)
		{
			$html .= "\t" . '<tfoot>' . "\n";
			$html .= "\t\t" . '<tr class="summary">' . "\n";
			$html .= "\t\t\t" . '<th colspan="2" class="numerical-data">' . Lang::txt('Total Tools Citations') . '</th>' . "\n";
			$html .= "\t\t\t" . '<td class="numerical-data">' . $result . '</td>' . "\n";
			$html .= "\t\t" . '</tr>' . "\n";
			$html .= "\t" . '</tfoot>' . "\n";
		}

		$count = 1;
		$sql = 'SELECT DISTINCT r.id, r.title, r.published, COUNT(c.id) AS citations FROM `#__resources` r, `#__citations` c, `#__citations_assoc` ca WHERE r.id = ca.oid AND ca.cid = c.id AND ca.tbl = "resource" AND r.type = "7" AND r.standalone = "1" AND c.published = "1" GROUP BY r.id ORDER BY citations DESC';
		$database->setQuery($sql);
		$results = $database->loadObjectList();

		if ($results)
		{
			$cls = 'even';
			$html .= "\t" . '<tbody>' . "\n";
			foreach ($results as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
				if ($row->citations == 0)
				{
					continue;
				}

				if ($row->published == "1")
				{
					$html .= "\t\t" . '<tr class="' . $cls . '">' . "\n";
					$html .= "\t\t\t" . '<td>' . $count . '</td>' . "\n";
					$html .= "\t\t\t" . '<td class="textual-data"><a href="'.Route::url('index.php?option=com_resources&id=' . $row->id . '&active=usage') . '">' . stripslashes($row->title) . '</a></td>';
					$html .= "\t\t\t" . '<td>' . $row->citations . '</td>' . "\n";
					$html .= "\t\t" . '</tr>' . "\n";
				}
				else
				{
					$html .= "\t\t" . '<tr class="' . $cls . '">' . "\n";
					$html .= "\t\t\t" . '<td>' . $count . '</td>' . "\n";
					$html .= "\t\t\t" . '<td class="textual-data">' . stripslashes($row->title) . '</td>';
					$html .= "\t\t\t" . '<td>' . $row->citations . '</td>' . "\n";
					$html .= "\t\t" . '</tr>' . "\n";
				}
				$count++;
			}
			$html .= "\t" . '</tbody>' . "\n";
		}

		if ($count == 1)
		{
			$html = "\t". 'No Data Available to Display'. "\n";
		}

		return $html;
	}

	/**
	 * Format time
	 *
	 * @param   mixed $time Value to format
	 * @return  string
	 */
	private function time_units($time)
	{
		if ($time < 60)
		{
			$data = $time . ' seconds';
		}
		else if ($time > 60 && $time < 3600)
		{
			$data = number_format(($time/60), 2) . ' minutes';
		}
		else if ($time >= 3600 && $time < 86400)
		{
			$data = number_format(($time/3600), 2) . ' hours';
		}
		else if ($time >= 86400)
		{
			$data = number_format(($time/86400), 2) . ' days';
		}

		return $data;
	}

	/**
	 * Build date selectors
	 *
	 * @param   object &$db    Database
	 * @param   string $period Time period (quarterly, yearly, etc)
	 * @param   string $s_top  Top value
	 * @param   string $dthis  Time (YYYY-MM)
	 * @return  string HTML
	 */
	private function drop_down_dates(&$db, $period, $s_top, $dthis)
	{
		$months = array(
			"01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun",
			"07" => "Jul", "08" => "Aug", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec"
			);
		$monthsReverse = array_reverse($months, true);
		$cur_year = floor(date("Y"));
		$cur_month = floor(date("n"));
		$year_data_start = 2000;

		$html = '<select name="dthis">' . "\n";
		$html .= "<option selected disabled hidden>" . Lang::txt('PLG_USAGE_TOOLS_DEFAULT_DATE_SELECT') . "</option>\n";
		switch ($period)
		{
			case '3':
				$qtd_found = 0;
				foreach ($monthsReverse as $key => $month)
				{
					$value = $cur_year . '-' . $key;
					if (!$qtd_found && $this->check_for_data($value, 3))
					{
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis)
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
				for ($j = $cur_year; $j >= $year_data_start; $j--)
				{
					for ($i = 12; $i > 0; $i = $i - 3)
					{
						$value = $j . '-' . sprintf("%02d", $i);
						if ($this->check_for_data($value, 3))
						{
							$html .= '<option value="' . $value . '"';
							if ($value == $dthis)
							{
								$html .= ' selected="selected"';
							}
							$html .= '>';
							switch ($i)
							{
								case 3:
									$html .= 'Jan';
									break;
								case 6:
									$html .= 'Apr';
									break;
								case 9:
									$html .= 'Jul';
									break;
								default:
									$html .= 'Oct';
									break;
							}
							$html .= ' ' . $j . ' - ';
							switch ($i)
							{
								case 3:
									$html .= 'Mar';
									break;
								case 6:
									$html .= 'Jun';
									break;
								case 9:
									$html .= 'Sep';
									break;
								default:
									$html .= 'Dec';
									break;
							}
							$html .= ' ' . $j . '</option>' . "\n";
						}
					}
				}
			break;

			case '12':
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
						if ($this->check_for_data($value, 12) && (intval($key) < $cur_month || $i < $cur_year))
						{
							$html .= '<option value="' . $value . '"';
							if ($value == $dthis)
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
			case '14':
				for ($i = $cur_year; $i >= $year_data_start; $i--)
				{
					foreach ($monthsReverse as $key => $month)
					{
						$value = $i . '-' . $key;
						if ($this->check_for_data($value, 1))
						{
							$html .= '<option value="' . $value . '"';
							if ($value == $dthis)
							{
								$html .= ' selected="selected"';
							}
							$html .= '>' . $month . ' ' . $i . '</option>' . "\n";
						}
					}
				}
			break;

			case '0':
				$ytd_found = 0;
				foreach ($monthsReverse as $key => $month)
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && $this->check_for_data($value, 0))
					{
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis)
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
					if ($this->check_for_data($value, 0))
					{
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis)
						{
							$html .= ' selected="selected"';
						}
						$html .= '>Jan - Dec ' . $i . '</option>' . "\n";
					}
				}
			break;

			case '13':
				$ytd_found = 0;
				foreach ($monthsReverse as $key => $month)
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && $this->check_for_data($value, 0))
					{
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis)
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
					if ($this->check_for_data($value, 0))
					{
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis) {
							$html .= ' selected="selected"';
						}
						$html .= '>Oct ';
						$html .= $i - 1;
						$html .= ' - Sep ' . $i . '</option>' . "\n";
					}
				}
			break;
		}
		$html .= '</select>' . "\n";

		return $html;
	}

	/**
	 * Returns true if there is data in the database
	 * for the date passed to it, false otherwise.
	 *
	 * @param   string $yearmonth  YYYY-MM
	 * @param   string $period     Time period
	 * @return  boolean
	 */
	private function check_for_data($yearmonth, $period)
	{
                $database = App::get('db');
                if (!isset($this->totals))
                {
                        $sql = "SELECT COUNT(datetime) AS total, DATE_FORMAT(datetime,'%Y-%m') as date FROM `#__stats_topvals` WHERE `period`=" . $database->Quote($period) . "GROUP BY datetime";
                        $database->setQuery($sql);
                        $results = $database->loadObjectList();
                        if ($results)
                        {
                                $this->totals = array();
                                foreach ($results as $row)
                                {
                                        $this->totals[$row->date] = $row->total;
                                }
                        }
                }
                if (isset($this->totals) && isset($this->totals[$yearmonth]) && $this->totals[$yearmonth] > 0)
                {
                        return true;
                }
                return false;
	}

	/**
	 * Build navigation menu
	 *
	 * @param   string $period  Timeperiod
	 * @param   string $top     Top value
	 * @return  string HTML
	 */
	private function navlinks($period='12', $top='')
	{
		$html  = '<div id="sub-sub-menu">' . "\n";
		$html .= "\t" . '<ul>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'prior12' || $period == '12')
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task . '&period=12&top=' . $top) . '"><span>' . Lang::txt('PLG_USAGE_PERIOD_PRIOR12') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'month' || $period == '1')
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task . '&period=1&top=' . $top) . '"><span>' . Lang::txt('PLG_USAGE_PERIOD_MONTH') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'qtr' || $period == '3')
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task . '&period=3&top=' . $top) . '"><span>' . Lang::txt('PLG_USAGE_PERIOD_QTR') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'year' || $period == '0')
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task . '&period=0&top=' . $top) . '"><span>' . Lang::txt('PLG_USAGE_PERIOD_YEAR') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == 'fiscal' || $period == '13')
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task . '&period=13&top=' . $top) . '"><span>' . Lang::txt('PLG_USAGE_PERIOD_FISCAL') . '</span></a></li>' . "\n";
		$html .= "\t\t" . '<li';
		if ($period == '14')
		{
			$html .= ' class="active"';
		}
		$html .= '><a href="'.Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task . '&period=14&top=' . $top) . '"><span>' . Lang::txt('PLG_USAGE_PERIOD_OVERALL') . '</span></a></li>' . "\n";
		$html .= "\t" . '</ul>' . "\n";
		$html .= '</div>' . "\n";

		return $html;
	}

	/**
	 * Get first date range with data
	 *
	 * @param   string  $period
	 * @return  string
	 */
	public function getDateWithData($period)
	{
		$period = intval($period);
		$dthis  = Request::getString('dthis');
		if (!preg_match('/[0-9]{4}\-[0-9]{2}/', $dthis))
		{
			$dthis = '';
		}

		if (!$dthis)
		{
			$cur_year  = floor(date("Y"));
			$cur_month = floor(date("n"));
			$year_data_start = 2000;
			$months = array(
				"01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun",
				"07" => "Jul", "08" => "Aug", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec"
			);
			$monthsReverse = array_reverse($months, true);

			for ($i = $cur_year; $i >= $year_data_start; $i--)
			{
				foreach ($monthsReverse as $key => $month)
				{
					$value = $i . '-' . $key;
					if ($this->check_for_data($value, $period) && (intval($key) < $cur_month || $i < $cur_year))
					{
						$dthis = $value;
						break;
					}
				}
				if ($dthis)
				{
					break;
				}
			}
		}

		return $dthis;
	}

	/**
	 * Event call for displaying usage data
	 *
	 * @param   string $option         Component name
	 * @param   string $task           Component task
	 * @param   object $db             Database
	 * @param   array  $months         Month names (Jan -> Dec)
	 * @param   array  $monthsReverse  Month names in reverse (Dec -> Jan)
	 * @param   string $enddate        Time period
	 * @return  string HTML
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

		$database = App::get('db');

		// Ensure the database table(s) exist
		$tables = $database->getTableList();
		$table  = $database->getPrefix() . 'stats_tops';
		if (!in_array($table, $tables))
		{
			return '<p class="error">' . Lang::txt('Error: Required database table not found . ') . '</p>';
		}

		// Set some vars
		$this->_option = $option;
		$this->_task = $task;

		// Incoming
		$period = Request::getString('period', '12');
		$dthis  = $this->getDateWithData($period);
		$s_top  = Request::getInt('top', '2');

		$html = '';

		// Build the HTML
		if ($s_top < 9)
		{
			$html .= $this->navlinks($period, $s_top);
		}
		$html  = '<form method="post" action="' . Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task . '&period=' . $period) . '">' . "\n";
		$html .= "\t" . '<fieldset class="filters">' . "\n";
		$html .= "\t\t" . '<label>' . "\n";
		$html .= "\t\t\t".Lang::txt('PLG_USAGE_SHOW_DATA_FOR') . ': ' . "\n";
		$html .= "\t\t\t" . '<select name="top">' . "\n";

		$sql = "SELECT * FROM `#__stats_tops` ORDER BY id";
		$database->setQuery($sql);
		$results = $database->loadObjectList();
		if ($results)
		{
			foreach ($results as $row)
			{
				$top = $row->id;
				$data[$top]['id'] = $row->id;
				$data[$top]['name'] = $row->name;
				if ($s_top == $top)
				{
					$html .= "\t\t\t\t" . '<option value="' . $data[$top]['id'] . '" selected="selected">' . htmlentities($data[$top]['name']) . '</option>' . "\n";
					if (isset($row->description))
					{
						$description = $row->description;
					}
				}
				else
				{
					$html .= "\t\t\t\t" . '<option value="' . $data[$top]['id'] . '">' . htmlentities($data[$top]['name']) . '</option>' . "\n";
				}
			}
		}

		$html .= "\t\t\t" . '</select>' . "\n";
		$html .= "\t\t" . '</label> ' . "\n";
		if ($s_top < 9)
		{
			$html .= $this->drop_down_dates($database, $period, $s_top, $dthis) . ' ';
		}
		$html .= "\t\t" . '<input type="submit" value="' . Lang::txt('PLG_USAGE_VIEW') . '" />' . "\n";
		$html .= "\t" . '</fieldset>' . "\n";
		$html .= '</form>' . "\n";

		$s_top_name = '';
		if ($s_top)
		{
			$s_top_name = $data[$s_top]['name'];
			$html .= '<table summary="' . $s_top_name . '">' . "\n";
			$html .= "\t" . '<caption>' . $s_top_name . '</caption>' . "\n";
			if (!empty($description))
			{
				$html .= "\t" . '<p class="info">' . $description . '</p>' . "\n";
			}

			if ($s_top == '9')
			{
				$html .= $this->gettopcited_tools($database);
			}
			else if ($s_top == '1')
			{
				$html .= $this->gettoprank_tools($database);
			}
			else
			{
				// Retrieve the header based on $s_top_name.  This is really a hack:
				// Depends on the drop-down item being "Top Tools by ...", and selects everything after that.
				$table_header = substr($s_top_name, 13);
				$html .= $this->gettoplist($database, $period, $dthis, $s_top, $table_header);
			}
			$html .= '</table>' . "\n";
		}
		else
		{
			$html .= '<p>' . Lang::txt('Please make a selection to view data . ') . '</p>' . "\n";
		}

		return $html;
	}
}
