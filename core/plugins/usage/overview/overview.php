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
	 * Event call for displaying usage data
	 *
	 * @param   string  $option         Component name
	 * @param   string  $task           Component task
	 * @param   object  $db             Database
	 * @param   array   $months         Month names (Jan -> Dec)
	 * @param   array   $monthsReverse  Month names in reverse (Dec -> Jan)
	 * @param   string  $enddate        Time period
	 * @return  string  HTML
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

		if ($action = Request::getCmd('action'))
		{
			if (method_exists($this, $action))
			{
				return $this->$action();
			}
		}

		$period = (int)$this->periodToInt(Request::getCmd('period', $this->params->get('period', 'prior12')));

		$cur_year  = $this->params->get('currentYear', floor(date("Y")));
		$cur_month = $this->params->get('currentMonth', floor(date("n")));

		$datetime = $cur_year . '-' . $cur_month;

		$view = $this->view('default', 'usage')
			->set('element', $this->_name)
			->set('period', $period)
			->set('datetime', $datetime)
			->set('message', $this->params->get('message', ''));

		return $view->loadTemplate();
	}

	/**
	 * Turn a text period value into its integer equiv.
	 *
	 * @param   mixed    $period
	 * @return  integer
	 */
	protected function periodToInt($period)
	{
		switch ($period)
		{
			case 12:
			case 'prior12':
			case 'nice':
				$period = 12;
			break;

			case 1:
			case 'month':
				$period = 1;
			break;

			case '3':
			case 'qtr':
				$period = 3;
			break;

			case '13':
			case 'fiscal':
				$period = 13;
			break;

			case '0':
			case 'year':
				$period = 0;
			break;

			default:
				$period = 12;
			break;
		}

		return $period;
	}

	/**
	 * Get data for orgs, countries, domains for a given time period
	 * (1 = country, 2 = domain, 3 = org)
	 *
	 * @return  void
	 */
	public function getUsageForDate()
	{
		$period   = $this->periodToInt(Request::getVar('period', $this->params->get('period', 'prior12')));
		$datetime = Request::getVar('datetime', date("Y") . '-' . date("m")) . '-00 00:00:00';

		$db = Components\Usage\Helpers\Helper::getUDBO();

		$sql = "SELECT value, valfmt
				FROM `summary_user_vals`
				WHERE rowid=" . $db->quote(1) . " AND period=" . $db->quote($period) . " AND datetime=" . $db->quote($datetime) . "
				ORDER BY colid";
		$db->setQuery($sql);
		$results = $db->loadObjectList();

		$res_iden = 1;
		$org_iden = 1;

		$i = 0;

		$highest = 1;

		foreach ($results as $row)
		{
			$i++;

			if ($i == 2)
			{
				$highest = $row->value;
			}

			if ($i == 7)
			{
				$highest = $row->value;
			}

			if ($row->valfmt == 2 && $highest > 100)
			{
				$row->value = number_format(($row->value / $highest) * 100);
			}

			switch ($i)
			{
				case 3:
					$residence[] = array(
						'column' => 'US',
						'value'  => $row->value,
						'key'    => 'users-visits-res-us'
					);
				break;
				case 4:
					$residence[] = array(
						'column' => 'Asia',
						'value'  => $row->value,
						'key'    => 'users-visits-res-asia'
					);
				break;
				case 5:
					$residence[] = array(
						'column' => 'Europe',
						'value'  => $row->value,
						'key'    => 'users-visits-res-europe'
					);
				break;
				case 6:
					$residence[] = array(
						'column' => 'Other',
						'value'  => $row->value,
						'key'    => 'users-visits-res-other'
					);
				break;

				case 8:
					$organization[] = array(
						'column' => 'Education',
						'value'  => $row->value,
						'key'    => 'users-visits-org-education'
					);
				break;
				case 9:
					$organization[] = array(
						'column' => 'Industry',
						'value'  => $row->value,
						'key'    => 'users-visits-org-industry'
					);
				break;
				case 10:
					$organization[] = array(
						'column' => 'Government',
						'value'  => $row->value,
						'key'    => 'users-visits-org-government'
					);
				break;
				case 11:
					$organization[] = array(
						'column' => 'Other',
						'value'  => $row->value,
						'key'    => 'users-visits-org-other'
					);
				break;
				break;

				default:
				break;
			}
		}

		$sql = "SELECT value
			FROM `summary_user_vals`
			WHERE rowid=" . $db->quote(1) . " AND period=" . $db->quote($period) . " AND datetime=" . $db->quote($datetime) . " AND colid=" . $db->quote(1) . "
			ORDER BY datetime ASC";
		$db->setQuery($sql);
		$result = $db->loadResult();

		$data = new stdClass;
		$data->visits = new stdClass;
		$data->visits->total        = number_format($result);
		$data->visits->residence    = $residence;
		$data->visits->organization = $organization;

		$sql = "SELECT value, valfmt
				FROM `summary_user_vals`
				WHERE rowid=" . $db->quote(4) . " AND period=" . $db->quote($period) . " AND datetime=" . $db->quote($datetime) . "
				ORDER BY colid";
		$db->setQuery($sql);
		$results = $db->loadObjectList();

		$res_iden = 1;
		$org_iden = 1;

		$i = 0;

		$highest = 1;

		foreach ($results as $row)
		{
			$i++;

			if ($i == 2)
			{
				$highest = $row->value;
			}

			if ($i == 7)
			{
				$highest = $row->value;
			}

			if ($row->valfmt == 2 && $highest > 100)
			{
				$row->value = number_format(($row->value / $highest) * 100);
			}

			switch ($i)
			{
				case 3:
					$residence[] = array(
						'column' => 'US',
						'value'  => $row->value,
						'key'    => 'users-downloads-res-us'
					);
				break;
				case 4:
					$residence[] = array(
						'column' => 'Asia',
						'value'  => $row->value,
						'key'    => 'users-downloads-res-asia'
					);
				break;
				case 5:
					$residence[] = array(
						'column' => 'Europe',
						'value'  => $row->value,
						'key'    => 'users-downloads-res-europe'
					);
				break;
				case 6:
					$residence[] = array(
						'column' => 'Other',
						'value'  => $row->value,
						'key'    => 'users-downloads-res-other'
					);
				break;

				case 8:
					$organization[] = array(
						'column' => 'Education',
						'value'  => $row->value,
						'key'    => 'users-downloads-org-education'
					);
				break;
				case 9:
					$organization[] = array(
						'column' => 'Industry',
						'value'  => $row->value,
						'key'    => 'users-downloads-org-industry'
					);
				break;
				case 10:
					$organization[] = array(
						'column' => 'Government',
						'value'  => $row->value,
						'key'    => 'users-downloads-org-government'
					);
				break;
				case 11:
					$organization[] = array(
						'column' => 'Other',
						'value'  => $row->value,
						'key'    => 'users-downloads-org-other'
					);
				break;
				break;

				default:
				break;
			}
		}

		$sql = "SELECT value
			FROM `summary_user_vals`
			WHERE rowid=" . $db->quote(4) . " AND period=" . $db->quote($period) . " AND datetime=" . $db->quote($datetime) . " AND colid=" . $db->quote(1) . "
			ORDER BY datetime ASC";
		$db->setQuery($sql);
		$result = $db->loadResult();

		$data->downloads = new stdClass;
		$data->downloads->total        = number_format($result);
		$data->downloads->residence    = $residence;
		$data->downloads->organization = $organization;

		$db->setQuery(
			"SELECT value
			FROM `summary_simusage_vals`
			WHERE `period` = " . $db->quote($period) . "
			AND `rowid`= " . $db->quote(1) . "
			AND `datetime`=" . $db->quote($datetime) . "
			ORDER BY `datetime` ASC"
		);
		$data->simulation_users = number_format($db->loadResult());

		$db->setQuery(
			"SELECT value
			FROM `summary_simusage_vals`
			WHERE `period` = " . $db->quote($period) . "
			AND `rowid`= " . $db->quote(2) . "
			AND `datetime`=" . $db->quote($datetime) . "
			ORDER BY `datetime` ASC"
		);
		$data->simulation_jobs = number_format($db->loadResult());

		$db->setQuery(
			"SELECT a.label,b.value,b.valfmt,a.plot,a.id
			FROM `summary_simusage` AS a
			INNER JOIN `summary_simusage_vals` AS b
			WHERE a.id=b.rowid AND b.period = " . $db->quote($period) . " AND b.datetime = " . $db->quote($datetime) . " AND a.id > 2
			ORDER BY a.id"
		);
		$data->simulation = $db->loadObjectList();

		foreach ($data->simulation as $key => $sim)
		{
			$sim->key   = 'simulation-' . preg_replace('/[^a-z0-9\-_]/', '', strtolower($sim->label));
			$sim->value = self::formatValue($sim->value, $sim->valfmt);

			$data->simulation[$key] = $sim;
		}

		//ob_clean();
		echo json_encode($data);
		die();
	}

	/**
	 * Format a given value
	 *
	 * @param   mixed   $value
	 * @param   string  $fmt
	 * @return  string
	 */
	public static function formatValue($value, $fmt)
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
}
