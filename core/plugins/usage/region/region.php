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
 * Usage plugin class for regions
 */
class plgUsageRegion extends \Hubzero\Plugin\Plugin
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
			'region' => Lang::txt('PLG_USAGE_REGION')
		);
	}

	/**
	 * Print Region List from Database
	 *
	 * @param      object &$db     Database
	 * @param      string $region  Parameter description (if any) ...
	 * @param      mixed  $t       Parameter description (if any) ...
	 * @param      mixed  $enddate Parameter description (if any) ...
	 * @return     string HTML
	 */
	private function regionlist(&$db, $region, $t=0, $enddate = 0)
	{
		if (!$db->tableExists('regions'))
		{
			\Notify::error('COM_USAGE_ERROR_MISSING_TABLE', 'regions');
			return '';
		}

		if (!$db->tableExists('regionvals'))
		{
			\Notify::error('COM_USAGE_ERROR_MISSING_TABLE', 'regionvals');
			return '';
		}

		// Set region list parameters...
		$hub = 1;
		if (!$enddate)
		{
			$dtmonth = date("m") - 1;
			$dtyear = date("Y");
			if (!$dtmonth)
			{
				$dtmonth = 12;
				$dtyear = $dtyear - 1;
			}
			$enddate = $dtyear . '-' . $dtmonth;
		}

		// Look up region list information...
		$regionname = '';
		$sql = "SELECT name, valfmt, size
				FROM regions
				WHERE region = " . $db->quote($region);
		$db->setQuery($sql);
		$result = $db->loadRow();
		if ($result)
		{
			$regionname = $result[0];
			$valfmt = $result[1];
			$size = $result[2];
		}
		$html = '';
		if ($regionname)
		{
			// Prepare some date ranges...
			$enddate .= "-00";
			$dtmonth = floor(substr($enddate, 5, 2));
			$dtyear  = floor(substr($enddate, 0, 4));
			$dt = $dtyear . '-' . sprintf("%02d", $dtmonth) . '-00';
			$dtmonthnext = floor(substr($enddate, 5, 2) + 1);
			$dtyearnext = $dtyear + 1;
			if ($dtmonthnext > 12)
			{
				$dtmonthnext = 1;
				$dtyearnext++;
			}
			$dtyearprior = substr($enddate, 0, 4) - 1;
			$monthtext   = date("F", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$yeartext    = 'Jan - ' . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$twelvetext  = date("M", mktime(0, 0, 0, $dtmonthnext, 1, $dtyear)) . ' ' . $dtyearprior . ' - ' . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$period = array(
				array('key' => 1,  'name' => $monthtext),
				array('key' => 0,  'name' => $yeartext),
				array('key' => 12, 'name' => $twelvetext)
			);

			// Process each different date/time periods/range...
			$maxrank = 0;
			$regionlist = array();
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				// Calculate the total value for this regionlist...
				$regionlistset = array();
				$sql = "SELECT regionvals.name, regionvals.value
						FROM regions, regionvals
						WHERE regions.region = regionvals.region
						AND regionvals.hub = " . $db->quote($hub) . "
						AND regions.region = " . $db->quote($region) . "
						AND regionvals.datetime = " . $db->quote($dt) . "
						AND regionvals.period = " . $db->quote($period[$pidx]["key"]) . "
						AND regionvals.rank = '0'";
				$db->setQuery($sql);
				$results = $db->loadObjectList();
				if ($results)
				{
					foreach ($results as $row)
					{
						$formattedval = \Components\Usage\Helpers\Helper::valformat($row->value, $valfmt);
						if (strstr($formattedval, "day") !== false)
						{
							$chopchar = strrpos($formattedval, ',');
							if ($chopchar !== false)
							{
								$formattedval = substr($formattedval, 0, $chopchar) . '+';
							}
						}
						array_push($regionlistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", 100)));
					}
				}
				if (!count($regionlistset))
				{
					array_push($regionlistset, array("n/a", 0, "n/a", "n/a"));
				}

				// Calculate the region values for the regionlist...
				$rank = 1;
				$sql = "SELECT regionvals.rank, regionvals.name, regionvals.value
						FROM regions, regionvals
						WHERE regions.region = regionvals.region
						AND regionvals.hub = " . $db->quote($hub) . "
						AND regions.region = " . $db->quote($region) . "
						AND datetime = " . $db->quote($dt) . "
						AND regionvals.period = " . $db->quote($period[$pidx]["key"]) . "
						AND regionvals.rank > '0'
						ORDER BY regionvals.rank, regionvals.name";
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
								array_push($regionlistset, array('n/a', 0, 'n/a', 'n/a'));
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
							if ($regionlistset[0][1] > 0)
							{
								array_push($regionlistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", (100 * $row->value / $regionlistset[0][1]))));
							}
							else
							{
								array_push($regionlistset, array($row->name, $row->value, $formattedval, 'n/a'));
							}
							$rank++;
						}
					}
				}
				while ($rank <= $size || $rank == 1)
				{
					array_push($regionlistset, array('n/a', 0, 'n/a', 'n/a'));
					$rank++;
				}
				array_push($regionlist, $regionlistset);
				if ($rank > $maxrank)
				{
					$maxrank = $rank;
				}
			}

			$cls = 'even';

			// Print region list table...
			$html .= '<table summary="' . $regionname . '">' . "\n";
			$html .= "\t" . '<caption>' . Lang::txt('Table') . ' ' . $t . ': ' . $regionname . '</caption>' . "\n";
			$html .= "\t" . '<thead>' . "\n";
			$html .= "\t\t" . '<tr>' . "\n";
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				$html .= "\t\t\t" . '<th colspan="3" scope="colgroup">'. $period[$pidx]["name"] .'</th>' . "\n";
			}
			$html .= "\t\t" . '</tr>' . "\n";
			$html .= "\t" . '</thead>' . "\n";
			$html .= "\t" . '<tbody>' . "\n";

			$cls = ($cls == 'even') ? 'odd' : 'even';
			$html .= "\t\t" . '<tr class="summary">' . "\n";
			$k = 0;
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				$k++;
				$tdcls = ($k != 2) ? ' class="group"' : '';
				$html .= "\t\t\t" . '<th' . $tdcls . ' scope="row">' . $regionlist[$pidx][0][0] . '</th>' . "\n";
				$html .= "\t\t\t" . '<td' . $tdcls . '>' . $regionlist[$pidx][0][2] . '</td>' . "\n";
				$html .= "\t\t\t" . '<td' . $tdcls . '>' . $regionlist[$pidx][0][3] . '</td>' . "\n";
			}
			$html .= "\t\t" . '</tr>' . "\n";

			for ($i = 1; $i < $maxrank; $i++)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
				$k = 0;
				$html .= "\t\t" . '<tr class="'. $cls .'">' . "\n";
				for ($pidx = 0; $pidx < count($period); $pidx++)
				{
					$k++;
					$tdcls = ($k != 2) ? ' class="group"' : '';
					$html .= "\t\t\t" . '<th' . $tdcls . ' scope="row">';
					$html .= (isset($regionlist[$pidx][$i][0])) ? $regionlist[$pidx][$i][0] : '';
					$html .= '</th>' . "\n";
					$html .= "\t\t\t" . '<td' . $tdcls . '>';
					$html .= (isset($regionlist[$pidx][$i][2])) ? $regionlist[$pidx][$i][2] : '';
					$html .= '</td>' . "\n";
					$html .= "\t\t\t" . '<td' . $tdcls . '>';
					$html .= (isset($regionlist[$pidx][$i][3])) ? $regionlist[$pidx][$i][3] : '';
					$html .= '</td>' . "\n";
				}
				$html .= "\t\t" . '</tr>' . "\n";
			}
			$html .= "\t" . '</tbody>' . "\n";
			$html .= '</table>' . "\n";
		}
		return $html;
	}

	/**
	 * Event call for displaying usage data
	 *
	 * @param      string $option        Component name
	 * @param      string $task          Component task
	 * @param      object $db            Database
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

		// Set tome vars
		$thisyear = date("Y");

		$o = \Components\Usage\Helpers\Helper::options($db, $enddate, $thisyear, $monthsReverse, 'check_for_regiondata');

		// Build HTML
		$html  = '<form method="post" action="'. Route::url('index.php?option=' . $option . '&task=' . $task) .'">' . "\n";
		$html .= "\t" . '<fieldset class="filters">' . "\n";
		$html .= "\t\t" . '<label>' . "\n";
		$html .= "\t\t\t".Lang::txt('COM_USAGE_SHOW_DATA_FOR').': ' . "\n";
		$html .= "\t\t\t" . '<select name="selectedPeriod" id="selectedPeriod">' . "\n";
		$html .= $o;
		$html .= "\t\t\t" . '</select>' . "\n";
		$html .= "\t\t" . '</label> <input type="submit" value="' . Lang::txt('COM_USAGE_VIEW') . '" />' . "\n";
		$html .= "\t" . '</fieldset>' . "\n";
		$html .= '</form>' . "\n";
		$html .= $this->regionlist($db, 1, 1, $enddate);
		$html .= $this->regionlist($db, 2, 2, $enddate);
		$html .= $this->regionlist($db, 5, 3, $enddate);
		$html .= $this->regionlist($db, 4, 4, $enddate);
		$html .= $this->regionlist($db, 6, 5, $enddate);
		$html .= $this->regionlist($db, 3, 6, $enddate);
		$html .= $this->regionlist($db, 7, 7, $enddate);

		// Return HTML
		return $html;
	}
}
