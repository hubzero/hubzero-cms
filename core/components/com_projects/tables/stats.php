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

namespace Components\Projects\Tables;

/**
 * Table class for project log history
 */
class Stats extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_stats', 'id', $db);
	}

	/**
	 * Load item
	 *
	 * @param   integer  $year
	 * @param   integer  $month
	 * @param   string   $week
	 * @return  mixed    False if error, Object on success
	 */
	public function loadLog($year = null, $month = null, $week = '')
	{
		if (!$year && !$month && !$week)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE 1=1 ";
		$query .= $year ? " AND year=" . $this->_db->quote($year) : '';
		$query .= $month ? " AND month=" . $this->_db->quote($month) : '';
		$query .= $week ? " AND week=" . $this->_db->quote($week) : '';
		$query .= " ORDER BY processed DESC LIMIT 1";

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Get item
	 *
	 * @param   integer  $year
	 * @param   integer  $month
	 * @param   string   $week
	 * @return  array
	 */
	public function getLog($year = null, $month = null, $week = '')
	{
		if (!$year && !$month)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE 1=1 ";
		$query .= $year ? " AND year=" . $this->_db->quote($year) : '';
		$query .= $month ? " AND month=" . $this->_db->quote($month) : '';
		$query .= $week ? " AND week=" . $this->_db->quote($week) : '';
		$query .= " ORDER BY processed DESC LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Collect monthly stats
	 *
	 * @param   integer  $numMonths
	 * @param   bool     $includeCurrent
	 * @return  mixed    False if error, Object on success
	 */
	public function monthlyStats($numMonths = 3, $includeCurrent = false)
	{
		$stats = array();

		require_once(PATH_CORE . DS . 'components' . DS .'com_publications' . DS . 'tables' . DS . 'publication.php');

		$obj  = new Project($this->_db);
		$objO = new Owner($this->_db);
		$objP = new \Components\Publications\Tables\Publication($this->_db);

		$testProjects    = $obj->getProjectsByTag('test', true, 'id');
		$validProjectIds = $obj->getProjectsByTag('test', false, 'id');

		$n = ($includeCurrent) ? 0 : 1;

		for ($a = $numMonths; $a >= $n; $a--)
		{
			$yearNum  = intval(date('y', strtotime("-" . $a . " month")));
			$monthNum = intval(date('m', strtotime("-" . $a . " month")));

			$log = $this->getLog($yearNum, $monthNum);

			if ($log)
			{
				$stats[date('M', strtotime("-" . $a . " month"))] = json_decode($log[0]->stats, true);

				// Get new users by month
				if (!isset($stats[date('M', strtotime("-" . $a . " month"))]['team']['new']))
				{
					$new = $objO->getTeamStats($testProjects, 'new', date('Y-m', strtotime("-" . $a . " month")));
					$stats[date('M', strtotime("-" . $a . " month"))]['team']['new'] = $new ? $new : 0;
				}

				// Get publication release count by month
				if (!isset($stats[date('M', strtotime("-" . $a . " month"))]['pub']['new']))
				{
					$new = $objP->getPubStats($validProjectIds, 'released', date('Y-m', strtotime("-" . $a . " month")));
					$stats[date('M', strtotime("-" . $a . " month"))]['pub']['new'] = $new ? $new : 0;
				}
			}
		}
		return $stats;
	}

	/**
	 * Collect overall projects stats
	 *
	 * @param   object   $model
	 * @param   bool     $cron
	 * @param   bool     $publishing
	 * @param   string   $period
	 * @param   integer  $limit
	 * @return  array
	 */
	public function getStats($model, $cron = false, $publishing = false, $period = 'alltime', $limit = 3)
	{
		// Incoming
		$period = Request::getVar('period', $period);
		$limit  = Request::getInt('limit', $limit);

		if ($cron == true)
		{
			$publicOnly = false;
			$saveLog    = true;
		}
		else
		{
			$publicOnly = $model->reviewerAccess('admin') ? false : true;
			$saveLog    = false;
		}

		// Collectors
		$stats   = array();
		$updated = null;
		$lastLog = null;

		$pastMonth    = Date::of(time() - (32 * 24 * 60 * 60))->toSql('Y-m-d');
		$thisYearNum  = Date::format('y');
		$thisMonthNum = Date::format('m');
		$thisWeekNum  = Date::format('W');

		// Pull recent stats
		if ($this->loadLog($thisYearNum, $thisMonthNum, $thisWeekNum))
		{
			$lastLog = json_decode($this->stats, true);
			$updated = $this->processed;
		}
		else
		{
			// Save stats
			$saveLog = true;
		}

		// Get project table class
		$tbl = $model->table();

		// Get inlcude /exclude lists
		$exclude = $tbl->getProjectsByTag('test', true, 'id');
		$include = $tbl->getProjectsByTag('test', false, 'id');
		$validProjects = $tbl->getProjectsByTag('test', false, 'alias');
		$validCount = count($validProjects) > 0 ? count($validProjects) : 1;

		// Collect overview stats
		$stats['general'] = array(
			'total'     => $tbl->getCount(array('exclude' => $exclude, 'all' => 1), true),
			'setup'     => $tbl->getCount(array('exclude' => $exclude, 'setup' => 1), true),
			'active'    => $tbl->getCount(array('exclude' => $exclude, 'active' => 1), true),
			'public'    => $tbl->getCount(array('exclude' => $exclude, 'private' => '0'), true),
			'sponsored' => $tbl->getCount(array('exclude' => $exclude, 'reviewer' => 'sponsored'), true),
			'sensitive' => $tbl->getCount(array('exclude' => $exclude, 'reviewer' => 'sensitive'), true),
			'new'       => $tbl->getCount(array('exclude' => $exclude, 'created' => date('Y-m', time()), 'all' => 1), true)
		);
		$active = $stats['general']['active'] ? $stats['general']['active'] : 1;
		$total  = $stats['general']['total'] ? $stats['general']['total'] : 1;

		// Activity stats
		$objAA = new Activity($this->_db);
		$recentlyActive = $tbl->getCount(array('exclude' => $exclude, 'timed' => $pastMonth, 'active' => 1), true);

		$perc = round(($recentlyActive * 100)/$active) . '%';
		$stats['activity'] = array(
			'total'   => $objAA->getActivityStats($include, 'total'),
			'average' => $objAA->getActivityStats($include, 'average'),
			'usage'   => $perc
		);

		$stats['topActiveProjects'] = $objAA->getTopActiveProjects($exclude, 5, $publicOnly);

		// Collect team stats
		$objO = new Owner($this->_db);
		$multiTeam         = $objO->getTeamStats($exclude, 'multi');
		$activeTeam        = $objO->getTeamStats($exclude, 'registered');
		$invitedTeam       = $objO->getTeamStats($exclude, 'invited');
		$multiProjectUsers = $objO->getTeamStats($exclude, 'multiusers');
		$teamTotal         = $activeTeam + $invitedTeam;

		$perc = round(($multiTeam * 100)/$total) . '%';
		$stats['team'] = array(
			'total'      => $teamTotal,
			'average'    => $objO->getTeamStats($exclude, 'average'),
			'multi'      => $perc,
			'multiusers' => $multiProjectUsers
		);

		$stats['topTeamProjects'] = $objO->getTopTeamProjects($exclude, $limit, $publicOnly);

		$stats['files'] = array(
			'total'     => 0,
			'average'   => 0,
			'usage'     => 0,
			'diskspace' => 0,
			'commits'   => 0,
			'pubspace'  => 0
		);

		// Collect files stats
		if ($lastLog)
		{
			$stats['files'] = isset($lastLog['files']) ? $lastLog['files'] : $stats['files'];
		}
		else
		{
			// Get repo model
			require_once(PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'repo.php');

			// Compute
			$repo     = new \Components\Projects\Models\Repo();
			$fTotal   = $repo->getStats($validProjects);
			$fAverage = number_format($fTotal/$validCount, 0);
			$fUsage   = $repo->getStats($validProjects, 'usage');
			$fDSpace  = $repo->getStats($validProjects, 'diskspace');
			$fCommits = $repo->getStats($validProjects, 'commitCount');
			$pDSpace  = $repo->getStats($validProjects, 'pubspace');

			$perc = round(($fUsage * 100)/$active) . '%';

			$stats['files'] = array(
				'total'     => $fTotal,
				'average'   => $fAverage,
				'usage'     => $perc,
				'diskspace' => \Hubzero\Utility\Number::formatBytes($fDSpace),
				'commits'   => $fCommits,
				'pubspace'  => \Hubzero\Utility\Number::formatBytes($pDSpace)
			);
		}

		// Collect publication stats
		if ($publishing)
		{
			$objP  = new \Components\Publications\Tables\Publication($this->_db);
			$objPV = new \Components\Publications\Tables\Version($this->_db);
			$prPub = $objP->getPubStats($include, 'usage');
			$perc  = round(($prPub * 100)/$total) . '%';

			$stats['pub'] = array(
				'total'    => $objP->getPubStats($include, 'total'),
				'average'  => $objP->getPubStats($include, 'average'),
				'usage'    => $perc,
				'released' => $objP->getPubStats($include, 'released'),
				'versions' => $objPV->getPubStats($include)
			);
		}

		// Save weekly stats
		if ($saveLog)
		{
			$this->year      = $thisYearNum;
			$this->month     = $thisMonthNum;
			$this->week      = $thisWeekNum;
			$this->processed = Date::toSql();
			$this->stats     = json_encode($stats);
			$this->store();
		}

		$stats['updated'] = $updated ? $updated : null;

		return $stats;
	}
}
