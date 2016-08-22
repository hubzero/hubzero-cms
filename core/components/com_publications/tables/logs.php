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

namespace Components\Publications\Tables;

/**
 * Table class for publication access logs
 */
class Log extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_logs', 'id', $db );
	}

	/**
	 * Check if bot
	 *
	 * @return     void
	 */
	public function checkBotIp( $ip )
	{
		// Check by hostname
		$hostname = gethostbyaddr($ip);
		$bots = array(
			'feedfetcher',
			'msnbot',
			'gsa-crawler',
			'googlebot',
			'yandex',
			'spider',
			'bot',
			'search',
			'crawl',
			'archive',
			'harvest',
			'slurp',
			'feed',
			'nutch',
			'robot',
			'fetch',
			'findlinks'
		);
		foreach ($bots as $bot)
		{
			if (stripos($hostname, $bot) !== FALSE)
			{
				return true;
			}
		}

		// Metrics db name
		$query = "SELECT DATABASE()";
		$this->_db->setQuery( $query );
		$dbname = $this->_db->loadResult();
		$metrics_db = $dbname . '_metrics';

		// Do we have metrics db and necessary table?
		$query = "SELECT COUNT(*) FROM information_schema.tables
				WHERE table_schema = '$metrics_db'
				AND table_name = 'exclude_list'";
		$this->_db->setQuery( $query );
		$exists = $this->_db->loadResult();

		// So it it bot or real user?
		if ($exists)
		{
			$bits   = explode('.', $ip);
			$n      = array_pop($bits);
			$subnet = trim(implode('.', $bits)) . '.';

			$query = " SELECT COUNT(*) FROM $metrics_db.exclude_list WHERE type='ip' AND filter LIKE " . $this->_db->quote($subnet . '%');
			$this->_db->setQuery( $query );
			return $this->_db->loadResult();
		}

		return false;
	}

	/**
	 * Log access in file
	 *
	 * @param      integer 	$pid		Publication ID
	 * @param      integer 	$vid		Publication version ID
	 * @param      string 	$type		view, primary or support
	 * @return     void
	 */
	public function logAccessFile ( $pid = NULL, $vid = NULL, $type = 'view', $ip = '', $logPath = '' )
	{
		$filename = 'pub-' . $pid . '-v-' . $vid . '.' . Date::format('Y-m') . '.log';

		$uid 	= User::isGuest() ? 'guest' : User::get('id');

		$log = Date::toSql() . "\t" . $ip . "\t" . $uid . "\t" . $type . "\n";

		$handle  = fopen(PATH_APP . $logPath . DS . $filename, 'a');

		if ($handle)
		{
			fwrite($handle, $log);
			fclose($handle);
		}
	}

	/**
	 * Log numbers from parsed text logs
	 *
	 * @return     void
	 */
	public function logParsed ( $pid = NULL, $vid = NULL, $year = NULL,
		$month = NULL, $count = 0, $type = 'view', $category = ''
	)
	{
		if (!$pid || !$vid || !$year || !$month || !$count)
		{
			return false;
		}
		$field = $type == 'view' ? 'page_views' : 'primary_accesses';

		// Load record to update
		if (!$this->loadMonthLog( $pid, $vid, $year, $month ))
		{
			$this->publication_id 			= $pid;
			$this->publication_version_id 	= $vid;
			$this->year						= $year;
			$this->month					= $month;
		}
		if ($category == 'unique')
		{
			$field .= $category ? '_' . $category : '';
		}
		elseif (!$category || $category == 'filtered')
		{
			// Also save unfiltered
			$uField = $field . '_unfiltered';
			$this->$uField = $this->$field;
		}

		// Save new count
		if ($this->$field == $count)
		{
			return true;
		}

		$this->$field = $count;
		$this->modified	= Date::toSql();

		if ($this->store())
		{
			return true;
		}

		return false;
	}

	/**
	 * Log access
	 *
	 * @param      integer 	$pid		Publication ID
	 * @param      integer 	$vid		Publication version ID
	 * @param      string 	$type		view, primary or support
	 * @return     void
	 */
	public function logAccess ( $pid = 0, $vid = 0, $type = 'view', $logPath = '' )
	{
		if (!$pid || !$vid)
		{
			return false;
		}

		// Get IP
		$ip = Request::ip();

		// Check if bot
		if ($this->checkBotIp( $ip ))
		{
			// Do not log a bot
			return false;
		}

		// Log in a file
		if (is_dir(PATH_APP . $logPath))
		{
			$this->logAccessFile( $pid, $vid, $type, $ip, $logPath);
		}

		$thisYearNum 	= Date::format('y');
		$thisMonthNum 	= Date::format('m');

		// Load record to update
		if (!$this->loadMonthLog( $pid, $vid ))
		{
			$this->publication_id 			= $pid;
			$this->publication_version_id 	= $vid;
			$this->year						= $thisYearNum;
			$this->month					= $thisMonthNum;
		}

		$this->modified	= Date::toSql();

		if ($type == 'primary')
		{
			$this->primary_accesses = $this->primary_accesses + 1;
		}
		elseif ($type == 'support')
		{
			$this->support_accesses = $this->support_accesses + 1;
		}
		else
		{
			$this->page_views = $this->page_views + 1;
		}

		if ($this->store())
		{
			return true;
		}

		return false;
	}

	/**
	 * Load log
	 *
	 * @param      integer 	$pid		Publication ID
	 * @param      integer 	$vid		Publication version ID
	 * @return     void
	 */
	public function loadMonthLog ( $pid = NULL, $vid = NULL, $yearNum = NULL, $monthNum = NULL )
	{
		if (!$pid || !$vid)
		{
			return false;
		}

		$thisYearNum 	= $yearNum ? $yearNum : Date::format('y');
		$thisMonthNum 	= $monthNum ? $monthNum : Date::format('m');

		$query  = "SELECT * FROM $this->_tbl WHERE publication_id=" . $this->_db->quote($pid);
		$query .= " AND publication_version_id=" . $this->_db->quote($vid);
		$query .= " AND year= " . $this->_db->quote($thisYearNum) . "
				    AND month=" . $this->_db->quote($thisMonthNum);
		$query .= " ORDER BY modified DESC LIMIT 1";

		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind( $result );
		}
		else
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Load log
	 *
	 * @param      integer 	$pid		Publication ID
	 * @param      integer 	$vid		Publication version ID
	 * @return     void
	 */
	public function getMonthLog ( $pid = NULL, $vid = NULL, $yearNum = NULL, $monthNum = NULL )
	{
		if (!$pid || !$vid)
		{
			return false;
		}

		$thisYearNum 	= $yearNum ? $yearNum : Date::format('y');
		$thisMonthNum 	= $monthNum ? $monthNum : Date::format('m');

		$query  = "SELECT * FROM $this->_tbl WHERE publication_id=" . $this->_db->quote($pid);
		$query .= " AND publication_version_id=" . $this->_db->quote($vid);
		$query .= " AND year= " . $this->_db->quote($thisYearNum) . "
				    AND month=" . $this->_db->quote($thisMonthNum);
		$query .= "ORDER BY modified DESC LIMIT 1";

		$this->_db->setQuery( $query );
		return $this->_db->loadAssoc();
	}

	/**
	 * Get stats for author
	 *
	 * @param      integer 	$uid		User ID
	 * @param      integer 	$limit		Num of entries
	 * @return     void
	 */
	public function getAuthorStats ( $uid = NULL, $limit = 0, $lastmonth = true )
	{
		if (!$uid)
		{
			return false;
		}

		if ($lastmonth == true)
		{
			$thisYearNum 	= intval(Date::of(strtotime("-1 month"))->format('y'));
			$pastMonthNum 	= intval(Date::of(strtotime("-1 month"))->format('m'));
		}
		else
		{
			$thisYearNum 	= intval(Date::format('y'));
			$thisMonthNum 	= intval(Date::format('m'));

			$pastYearNum 	= intval(Date::of(strtotime("-1 month"))->format('y'));
			$pastMonthNum 	= intval(Date::of(strtotime("-1 month"))->format('m'));

			$pastTwoYear 	= intval(Date::of(strtotime("-2 month"))->format('y'));
			$pastTwoMonth 	= intval(Date::of(strtotime("-2 month"))->format('m'));

			$pastThreeYear 	= intval(Date::of(strtotime("-3 month"))->format('y'));
			$pastThreeMonth = intval(Date::of(strtotime("-3 month"))->format('m'));
		}

		$query  = "SELECT V.id as publication_version_id, V.publication_id, V.title, V.version_label,
					V.version_number, V.doi, V.published_up, P.alias as project_alias, P.title as project_title,
					t.url_alias as cat_url, t.name as cat_name, t.alias as cat_alias";

		$query .= ", (SELECT VV.published_up FROM #__publication_versions as VV
			WHERE VV.publication_id=V.publication_id and VV.published_up IS NOT NULL
			ORDER BY VV.published_up ASC limit 1) AS first_published ";

		if ($lastmonth)
		{
			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$thisYearNum' AND month='$pastMonthNum' ) AS monthly_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$thisYearNum' AND month='$pastMonthNum' ) AS monthly_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$thisYearNum' AND month='$pastMonthNum' ) AS monthly_support ";
			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 )  FROM $this->_tbl as L
				WHERE L.publication_id=V.publication_id ) AS total_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L
				WHERE L.publication_id=V.publication_id ) AS total_primary ";
		}
		else
		{
			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_support ";

			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_support ";

			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_support ";

			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
				AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_support ";

			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 )  FROM $this->_tbl as L
				WHERE L.publication_id=V.publication_id ) AS total_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L
				WHERE L.publication_id=V.publication_id ) AS total_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L
				WHERE L.publication_id=V.publication_id ) AS total_support ";
		}

		$query .= " FROM #__publications as C, #__projects as P, #__publication_categories AS t, #__publication_versions as V ";
		$query .= " JOIN #__publication_authors as A ON A.publication_version_id = V.id AND A.status=1 AND A.user_id='$uid' ";

		if ($lastmonth)
		{
			$query .= " JOIN $this->_tbl as L ON L.publication_id = V.publication_id AND L.year='$thisYearNum'
						AND L.month='$pastMonthNum' AND L.page_views > 0  ";
		}

		$query .= " WHERE A.role!='submitter' AND P.id=C.project_id AND C.id=V.publication_id AND C.category = t.id AND
					V.main=1 AND V.state=1 AND V.published_up < '" . Date::toSql() . "'";
		$query .= " GROUP BY V.publication_id ";

		$query .= $lastmonth ? " ORDER BY L.page_views DESC, V.id ASC " :  "ORDER BY V.title ASC ";
		$query .= $limit? "LIMIT " . $limit : '';

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Get date when logs table was created (= date of first log)
	 *
	 * @return     void
	 */
	public function getFirstLogDate()
	{
		// Get approximate time when logs started
		$query = "SELECT modified FROM $this->_tbl ORDER BY id ASC LIMIT 1";
		$this->_db->setQuery( $query );
		$first = $this->_db->loadResult();

		if ($first)
		{
			return Date::of(strtotime($first))->format('Y-m-01 00:00:00');
		}
	}

	/**
	 * Get totals for publication(s) in a project/for an author
	 *
	 * @param      integer 	$projectid		Project ID
	 * @param      integer 	$pid			Publication ID
	 * @return     void
	 */
	public function getTotals($id = 0, $type = 'author')
	{
		$query  = "SELECT COALESCE( SUM(L.page_views) , 0 ) as all_total_views, ";
		$query .= " COALESCE( SUM(L.primary_accesses) , 0 ) as all_total_primary, ";
		$query .= " COALESCE( SUM(L.support_accesses) , 0 ) as all_total_support ";

		$query .= " FROM $this->_tbl as L ";

		if ($type == 'author')
		{
			$query .= " JOIN #__publication_versions as V ON V.publication_id=L.publication_id ";
			$query .= " JOIN #__publication_authors as A ON A.publication_version_id = V.id AND A.user_id=" . $this->_db->quote($id);
		}
		else
		{
			$query .= " JOIN #__publications as P ON P.id = L.publication_id AND P.project_id=" . $this->_db->quote($id);
			$query .= " JOIN #__publication_versions as V ON V.publication_id=L.publication_id ";
		}

		$query .= " WHERE A.role!='submitter' AND V.state=1 AND V.main=1 AND V.published_up < '" . Date::toSql() . "'";

		$this->_db->setQuery( $query );
		$totals = $this->_db->loadObjectList();
		return $totals ? $totals[0] : NULL;
	}

	/**
	 * Get stats for publication(s) for a custom report
	 *
	 * @param      string 	$from		Date from
	 * @param      string 	$to			Date to
	 * @param      array 	$data		Data to extract
	 * @param      string 	$filter		Tag name to match
	 * @return     void
	 */
	public function getCustomStats ( $from = NULL, $to = NULL, $exclude = array(), $filter = NULL )
	{
		// Parse dates
		$parts 	= explode('-', $from);
		$fromY 	= substr($parts[0], -2, 2);
		$fromM 	= intval(end($parts));

		$parts 	= explode('-', $to);
		$toY 	= substr($parts[0], -2, 2);
		$toM 	= intval(end($parts));

		$datequery = $fromY == $toY
					? "AND (L.year=$fromY AND L.month>=$fromM AND L.month<=$toM )"
					: "AND ((L.year=$fromY AND L.month >= $fromM ) OR (L.year=$toY AND L.month <= $toM))";

		$citeFrom  = Date::of($from)->toSql();
		$citeTo    = Date::of($to)->toSql();

		$query  = "SELECT V.publication_id as id, V.title, A.name as author,
					V.version_label as version, V.doi";

		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 )
					FROM $this->_tbl as L
					WHERE L.publication_id=V.publication_id " . $datequery . ") AS downloads ";

		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 )
					FROM $this->_tbl as L
					WHERE L.publication_id=V.publication_id " . $datequery . ") AS views ";

		$query .= ", (SELECT COUNT(*)
					FROM #__citations as C
					JOIN #__citations_assoc as CA ON CA.cid=C.id
					AND tbl='publication'
					WHERE CA.oid=V.publication_id
					AND C.created <= " . $this->_db->quote($citeTo) . " ) AS citations ";

		$query .= "FROM ";
		if ($filter)
		{
			$query .= "#__tags_object AS RTA ";
			$query .= "INNER JOIN #__tags AS TA ON RTA.tagid = TA.id AND RTA.tbl='publications', ";
		}

		$query .= " #__publications as C, #__publication_categories AS t, #__publication_versions as V ";
		$query .= " LEFT JOIN #__publication_authors as A
					ON A.publication_version_id=V.id
					AND A.ordering=1 AND status=1";

		$query .= " WHERE C.id=V.publication_id AND V.state=1 AND C.category = t.id
					AND V.main=1 AND V.published_up < '" . Date::toSql() . "' ";

		if (!empty($exclude))
		{
			$query .= " AND C.project_id NOT IN (";
			$tquery = '';
			foreach ($exclude as $ex)
			{
				$tquery .= "'".$ex."',";
			}
			$tquery = substr($tquery, 0, strlen($tquery) - 1);
			$query .= $tquery . ") ";
		}

		if ($filter)
		{
			include_once( PATH_CORE . DS . 'components' . DS . 'com_publications'
				. DS . 'helpers' . DS . 'tags.php' );
			$tagging = new \Components\Publications\Helpers\Tags( $this->_db );
			$tags = $tagging->_parse_tags($filter);

			$query .= " AND RTA.objectid=C.id AND (TA.tag IN (";
			$tquery = '';
			foreach ($tags as $tagg)
			{
				$tquery .= "'".$tagg."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery."))";
		}

		$query .= " GROUP BY V.publication_id ";
		$query .= " ORDER BY V.publication_id ASC ";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Get stats for publication(s) in a project
	 *
	 * @param      integer 	$projectid		Project ID
	 * @param      integer 	$pubid			Publication ID
	 * @return     void
	 */
	public function getPubStats ( $projectid = NULL, $pubid = 0 )
	{
		if (!$projectid)
		{
			return false;
		}

		$thisYearNum 	= intval(Date::format('y'));
		$thisMonthNum 	= intval(Date::format('m'));

		$pastYearNum 	= intval(Date::of(strtotime("-1 month"))->format('y'));
		$pastMonthNum 	= intval(Date::of(strtotime("-1 month"))->format('m'));

		$pastTwoYear 	= intval(Date::of(strtotime("-2 month"))->format('y'));
		$pastTwoMonth 	= intval(Date::of(strtotime("-2 month"))->format('m'));

		$pastThreeYear 	= intval(Date::of(strtotime("-3 month"))->format('y'));
		$pastThreeMonth = intval(Date::of(strtotime("-3 month"))->format('m'));

		$dthis 			= Date::format('Y') . '-' . Date::format('m');

		$query  = "SELECT V.id as publication_version_id, V.publication_id, V.title, V.version_label,
					V.version_number, V.doi, V.published_up,
					t.url_alias as cat_url, t.name as cat_name, t.alias as cat_alias,
					S.users, S.downloads ";

		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_views ";
		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_primary ";
		$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_support ";

		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_views ";
		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_primary ";
		$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_support ";

		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_views ";
		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_primary ";
		$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_support ";

		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_views ";
		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_primary ";
		$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id
			AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_support ";

		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 )  FROM $this->_tbl as L
			WHERE L.publication_id=V.publication_id ) AS total_views ";
		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L
			WHERE L.publication_id=V.publication_id ) AS total_primary ";
		$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L
			WHERE L.publication_id=V.publication_id ) AS total_support ";

		$query .= ", (SELECT VV.published_up FROM #__publication_versions as VV
			WHERE VV.publication_id=V.publication_id and VV.published_up IS NOT NULL
			ORDER BY VV.published_up ASC limit 1) AS first_published ";

		$query .= " FROM #__publications as C, #__publication_categories AS t, #__publication_versions as V ";
		$query .= " LEFT JOIN #__publication_stats as S ON S.publication_id = V.publication_id AND period='14' AND datetime='" . $dthis . "-01 00:00:00' ";

		$query .= " WHERE C.id=V.publication_id AND V.state=1 AND C.category = t.id
					AND V.main=1 AND V.published_up < '" . Date::toSql() . "' AND C.project_id=$projectid";

		$query .= $pubid ? " AND V.publication_id=" . $this->_db->quote($pubid) : "";
		$query .= " GROUP BY V.publication_id ";
		$query .= " ORDER BY S.users DESC, V.id ASC ";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
