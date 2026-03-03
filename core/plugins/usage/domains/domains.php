<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Usage plugin class for domains
 */
class plgUsageDomains extends \Hubzero\Plugin\Plugin
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
			'domains' => Lang::txt('PLG_USAGE_DOMAINS')
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

		// Set some vars
		$thisyear = date("Y");

		// Build HTML
		$html  = '<form method="post" action="'. Route::url('index.php?option=' . $option . '&task=' . $task) . '">' . "\n";
		$html .= '</form>' . "\n";
		$html .= $this->toplist($db, 10, 1, $enddate);
		$html .= $this->toplist($db, 17, 2, $enddate);
		$html .= $this->toplist($db, 11, 3, $enddate);
		$html .= $this->toplist($db, 9, 4, $enddate);
		$html .= $this->toplist($db, 12, 5, $enddate);
		$html .= $this->toplist($db, 19, 6, $enddate);
		$html .= $this->toplist($db, 18, 7, $enddate);
		$html .= $this->toplist($db, 7, 8, $enddate);
		// Return HTML
		return $html;
	}

	/**
	 * Print Top X List from Database
	 *
	 * @param   object   $db
	 * @param   integer  $top
	 * @param   mixed    $t
	 * @param   mixed    $enddate
	 * @param   integer  $raw
	 * @return  string
	 */
	private function toplist(&$db, $top, $t=0, $enddate=0, $raw=0)
	{
		if (!$db->tableExists('tops'))
		{
			\Notify::error(Lang::txt('COM_USAGE_ERROR_MISSING_TABLE', 'tops'));
			return false;
		}

		if (!$db->tableExists('topvals'))
		{
			\Notify::error(Lang::txt('COM_USAGE_ERROR_MISSING_TABLE', 'topvals'));
			return false;
		}

		$html = '';

		// Get latest enddate from database
		$sql = "SELECT DATE_FORMAT(max(datetime), '%Y-%m-%d')
				FROM topvals
				WHERE top = " . $db->Quote($top);
		$db->setQuery($sql);
		$result = $db->loadRow();
		if ($result)
		{
			$enddate = strval($result[0]);
		}

		// Look up top list information...
		$topname = '';
		$sql = "SELECT name, valfmt, size FROM tops WHERE top=" . $db->quote($top);
		$db->setQuery($sql);
		$result = $db->loadRow();
		if ($result)
		{
			$topname = $result[0];
			$valfmt = $result[1];
			$size = $result[2];
		}

		if ($topname)
		{
			// Prepare some date ranges...
			$dtmonth = floor(intval(substr($enddate, 5, 2)));
			$dtyear = floor(intval(substr($enddate, 0, 4)));
			$dt = $dtyear . '-' . sprintf("%02d", $dtmonth) . '-00';
			$dtyearnext = $dtyear + 1;
			$dtmonthnext = floor(intval(substr($enddate, 5, 2)) + 1);
			if ($dtmonthnext > 12)
			{
				$dtmonthnext = 1;
				$dtyearnext++;
			}
			$dtyearprior = intval(substr($enddate, 0, 4)) - 1;
			$monthtext   = date("F", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$yeartext    = 'Jan - ' . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$twelvetext  = date("M", mktime(0, 0, 0, $dtmonthnext, 1, $dtyear)) . ' ' . $dtyearprior . ' - ' . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$period = array(
				array('key' => 1,  'name' => $monthtext),
				array('key' => 0,  'name' => $yeartext),
				array('key' => 12, 'name' => $twelvetext)
			);

			// Process each different date/time periods/range...
			$toplist = array();
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				// Calculate the total value for this toplist...
				$toplistset = array();
				$sql = "SELECT topvals.name, topvals.value
						FROM tops, topvals
						WHERE tops.top = topvals.top
						AND tops.top = " . $db->quote($top) . "
						AND topvals.datetime = " . $db->quote($dt) . "
						AND topvals.period = " . $db->quote($period[$pidx]["key"]) . "
						AND topvals.rank = '0'";
				$db->setQuery($sql);
				$results = $db->loadObjectList();
				if ($results)
				{
					foreach ($results as $row)
					{
						$formattedval = \Components\Usage\Helpers\Helper::valformat($row->value, $valfmt);
						if (strstr($formattedval, 'day') !== false)
						{
							$chopchar = strrpos($formattedval, ',');
							if ($chopchar !== false)
							{
								$formattedval = substr($formattedval, 0, $chopchar) . '+';
							}
						}
						array_push($toplistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", 100)));
					}
				}
				if (!count($toplistset))
				{
					array_push($toplistset, array('n/a', 0, 'n/a', 'n/a'));
				}

				// Calculate the top X values for the toplist...
				$rank = 1;
				$sql = "SELECT topvals.rank, topvals.name, topvals.value
						FROM tops, topvals
						WHERE tops.top = topvals.top
						AND tops.top = " . $db->quote($top) . "
						AND datetime = " . $db->quote($dt) . "
						AND topvals.period = " . $db->quote($period[$pidx]["key"]) . "
						AND topvals.rank > '0'
						ORDER BY topvals.rank, topvals.name";
				$db->setQuery($sql);
				$results = $db->loadObjectList();
				if ($results)
				{
					foreach ($results as $row)
					{
						if ($row->rank > 0 && (!$size || $row->rank <= $size))
						{
							while ($rank < $row->rank)
							{
								array_push($toplistset, array('n/a', 0, 'n/a', 'n/a'));
								$rank++;
							}
							$formattedval = \Components\Usage\Helpers\Helper::valformat($row->value, $valfmt);
							if (strstr($formattedval, 'day') !== false)
							{
								$chopchar = strrpos($formattedval, ',');
								if ($chopchar !== false)
								{
									$formattedval = substr($formattedval, 0, $chopchar) . '+';
								}
							}
							if ($toplistset[0][1] > 0)
							{
								array_push($toplistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", (100 * $row->value / $toplistset[0][1]))));
							}
							else
							{
								array_push($toplistset, array($row->name, $row->value, $formattedval, 'n/a'));
							}
							$rank++;
						}
					}
				}
				while ($rank <= $size || $rank == 1)
				{
					array_push($toplistset, array('n/a', 0, 'n/a', 'n/a'));
					$rank++;
				}
				array_push($toplist, $toplistset);
			}

			$cls = 'even';

			// Print top list table...
			$html .= '<table summary="">' . "\n";
			$html .= "\t" . '<caption>Table ' . $t . ': ' . $topname . '</caption>' . "\n";
			$html .= "\t" . '<thead>' . "\n";
			$html .= "\t\t" . '<tr>' . "\n";
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				$html .= "\t\t\t" . '<th colspan="3" scope="colgroup">' . $period[$pidx]["name"] . '</th>' . "\n";
			}
			$html .= "\t\t" . '</tr>' . "\n";
			$html .= "\t" . '</thead>' . "\n";
			$html .= "\t" . '<tbody>' . "\n";
			$html .= "\t\t" . '<tr class="summary">' . "\n";
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				$tdcls = ($pidx != 1) ? ' class="group"' : '';
				$html .= "\t\t\t" . '<th' . $tdcls . ' scope="row">' . $toplist[$pidx][0][0] . '</th>' . "\n";
				$html .= "\t\t\t" . '<td' . $tdcls . '>' . $toplist[$pidx][0][2] . '</td>' . "\n";
				$html .= "\t\t\t" . '<td' . $tdcls . '>' . $toplist[$pidx][0][3] . '</td>' . "\n";
			}
			$html .= "\t\t" . '</tr>' . "\n";
			for ($i = 1; $i < $rank; $i++)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
				$html .= "\t\t" . '<tr class="' . $cls . '">' . "\n";
				for ($pidx = 0; $pidx < count($period); $pidx++)
				{
					$tdcls = ($pidx != 1) ? ' class="group"' : '';
					$html .= "\t\t\t" . '<th' . $tdcls . ' scope="row">' . $toplist[$pidx][$i][0] . '</th>' . "\n";
					$html .= "\t\t\t" . '<td' . $tdcls . '>' . $toplist[$pidx][$i][2] . '</td>' . "\n";
					$html .= "\t\t\t" . '<td' . $tdcls . '>' . $toplist[$pidx][$i][3] . '</td>' . "\n";
				}
				$html .= "\t\t" . '</tr>' . "\n";
			}
			$html .= "\t" . '</tbody>' . "\n";
			$html .= '</table>' . "\n";
		}
		return $html;
	}
}
