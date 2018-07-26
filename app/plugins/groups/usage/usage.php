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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Groups Plugin class for usage
 */
class plgGroupsUsage extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Loads the plugin language file
	 *
	 * @param   string   $extension  The extension for which a language file should be loaded
	 * @param   string   $basePath   The basepath to use
	 * @return  boolean  true, if the file has successfully loaded.
	 */
	public function loadLanguage($extension = '', $basePath = PATH_APP)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}

		$group = \Hubzero\User\Group::getInstance(Request::getCmd('cn'));
		if ($group && $group->isSuperGroup())
		{
			$basePath = PATH_APP . DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');
		}

		$lang = \App::get('language');
		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_CORE . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true);
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name' => 'usage',
			'title' => Lang::txt('USAGE'),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon' => 'f080'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param   object   $group       Current group
	 * @param   string   $option      Name of the component
	 * @param   string   $authorized  User's authorization level
	 * @param   integer  $limit       Number of records to pull
	 * @param   integer  $limitstart  Start of records to pull
	 * @param   string   $action      Action to perform
	 * @param   array    $access      What can be accessed
	 * @param   array    $areas       Active area(s)
	 * @return  array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$return = 'html';
		$active = 'usage';

		// The output array we're returning
		$arr = array(
			'html'=>''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$return = '';
			}
		}

		if ($return == 'html')
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//get the group members
			$members = $group->get('members');

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if (User::isGuest()
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$url = Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&active='.$active, false, true);

				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
					Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array(User::get('id'), $members)
			 && $group_plugin_acl == 'members'
			 && $authorized != 'admin')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}

			//instantiate the db
			$database = App::get('db');

			//reference group for other functions
			$this->group = $group;


			//add usage stylesheet to view
			$this->css();

			//add datepicker stylesheet to view
			$this->css('jquery.datepicker.css', 'system')
			->css('jquery.timepicker.css', 'system');

			//add google js-api
			Document::addScript('https://www.google.com/jsapi');

			//add jquery from google cdn
			//Document::addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js');

			//add usage custom script
			$this->js('usage.js');

			//add datepicker script
			//$this->js('datepicker.js');

			//get the page id if we want to view stats on a specific page
			$pid = Request::getVar('pid', '');

			//get start and end dates
			$start = Request::getVar('start', gmdate("Y-m-d", strtotime('-30 DAYS')));
			$end   = Request::getVar('end', gmdate("Y-m-d"));
			if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $start))
			{
				$start = Date::of($start)->format('Y-m-d');
			}
			if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $end))
			{
				$end = Date::of($end)->format('Y-m-d');
			}

			//get window
			$window = Request::getVar('window', 'day');

			//generate script to draw chart and push to the page
			$script = $this->drawChart($pid, $start, $end, $window);
			Document::addScriptDeclaration($script);

			//import and create view
			$view = $this->view('default', 'index');

			//get the group pages
			$query = "SELECT id, title FROM `#__xgroups_pages` WHERE state=1 AND gidNumber=" . $database->quote($group->get('gidNumber'));
			$database->setQuery($query);
			$view->pages = $database->loadAssocList();

			$view->option = $option;
			$view->group = $group;
			$view->authorized = $authorized;
			$view->database = $database;

			$view->pid = $pid;
			$view->start = $start;
			$view->end = $end;
			$view->window = $window;

			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}

			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Get a count of all resources
	 *
	 * @param      integer $gid        Group ID
	 * @param      string  $authorized Authorization level
	 * @return     integer
	 */
	public static function getResourcesCount($gid=null, $authorized)
	{
		if (!$gid)
		{
			return 0;
		}

		include_once \Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

		return \Components\Resources\Models\Entry::all()
			->whereEquals('group_owner', $gid)
			->total();
	}

	/**
	 * Get a count of all wiki pages
	 *
	 * @param   integer  $gid         Group ID
	 * @param   string   $authorized  Authorization level
	 * @return  integer
	 */
	public static function getWikipageCount($gid=null, $authorized)
	{
		if (!$gid)
		{
			return 0;
		}

		$database = App::get('db');
		$database->setQuery("SELECT COUNT(*) FROM `#__wiki_pages` AS p WHERE p.scope=" . $database->quote('group') . " AND p.scope_id=" . $database->quote($gid));
		return $database->loadResult();
	}

	/**
	 * Get a count of all wiki attachments
	 *
	 * @param   integer  $gid         Group ID
	 * @param   string   $authorized  Authorization level
	 * @return  integer
	 */
	public static function getWikifileCount($gid=null, $authorized)
	{
		if (!$gid)
		{
			return 0;
		}

		$database = App::get('db');
		$database->setQuery("SELECT id FROM `#__wiki_pages` AS p WHERE p.scope=" . $database->quote('group') . " AND p.scope_id=" . $database->quote($gid));
		$pageids = $database->loadObjectList();
		if ($pageids)
		{
			$ids = array();
			foreach ($pageids as $pageid)
			{
				$ids[] = $pageid->id;
			}

			$database->setQuery("SELECT COUNT(*) FROM `#__wiki_attachments` WHERE page_id IN (" . implode(',', $ids) . ")");
			return $database->loadResult();
		}

		return 0;
	}

	/**
	 * Get a count of all forum posts
	 *
	 * @param      integer $gid        Group ID
	 * @param      string  $authorized Authorization level
	 * @param      string  $state      State of threads
	 * @return     integer
	 */
	public static function getForumCount($gid=null, $authorized, $state='')
	{
		if (!$gid)
		{
			return 0;
		}

		include_once \Component::path('com_forum') . DS . 'models' . DS . 'manager.php';

		$filters = array();
		switch ($state)
		{
			case 'sticky':
				$filters['sticky'] = 1;
			break;
			case 'closed':
				$filters['state'] = 2;
			break;
			case 'open':
			default:
				$filters['state'] = 1;
			break;
		}
		$filters['start'] = 0;

		$forum = new \Components\Forum\Models\Manager('group', $gid);

		return $forum->posts($filters)->total();
	}

	/**
	 * Get a count of all the group pages
	 *
	 * @param      string $gid Group alias
	 * @return     integer
	 */
	public static function getGroupPagesCount($gid)
	{
		if (!$gid)
		{
			return 0;
		}

		// get group pages if any
		$pageArchive = \Components\Groups\Models\Page\Archive::getInstance();
		$pages = $pageArchive->pages('list', array(
			'gidNumber' => $gid,
			'state'     => array(0,1),
		));

		return count($pages);
	}

	/**
	 * Get a list of all page visit information over a time window
	 * See https://en.wikipedia.org/wiki/Unique_user
	 *
	 * @param      string  $gid     Group ID
	 * @param      string  $pageid  Page ID
	 * @param      string  $start   Start date
	 * @param      string  $end     End date
	 * @param 		 string  $window	Count window (day, week, month)
	 * @return     array
	 */
	public function getGroupPageVisits($gid, $pageid = null, $start, $end, $window)
	{
		$database = App::get('db');

		// See https://stackoverflow.com/a/50918062.  This expression is insane
		//   to make sure days with zero views are included.  Could do this in PHP,
		//   but SQL has a lot of good date/time stuff.  It does a LEFT JOIN with
		//   a constructed table of days.  LEFT JOINs create NULL, so replace with 0
		//	 Consider creating a separate cal_dates table to store all dates.
		if ($window == 'day') {
			$query = "SELECT IF(u.visits is NULL, 0, u.visits) AS visits,
											 IF(u.visitors IS NULL, 0, u.visitors) AS visitors,
											 CONCAT(monthname(b.Days), ' ', dayofmonth(b.Days), ', ', year(b.Days)) as `date`
								FROM
									(SELECT a.Days
								  FROM (
										SELECT " . $database->quote($end) . " - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a)) DAY AS Days
	  			 				  FROM       (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
	  			 				  CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
	  			 				  CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
	  			 				  CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
						 		  ) a
								  WHERE a.Days >= " . $database->quote($start) . " AND a.Days <= " . $database->quote($end) . ") b
								LEFT JOIN (SELECT count(*) AS visits,
																  count(distinct ip) AS visitors,
																	date(date) AS caldate
													 FROM #__xgroups_pages_hits
													 WHERE gidNumber=" . $database->quote($gid) . "
													   AND date(date) >= " . $database->quote($start) . "
														 AND date(date) <= " . $database->quote($end) .
														 ($pageid != '' ? " AND pageid=" . $pageid : "") . "
														 GROUP BY caldate
														 ORDER BY caldate ASC) u
								  ON u.caldate = b.Days
								ORDER BY b.Days ASC";
		} elseif ($window == 'week') {
			$query = "SELECT IF(u.visits is NULL, 0, u.visits) AS visits,
	   									 IF(u.visitors IS NULL, 0, u.visitors) AS visitors,
       				 				 CONCAT(monthname(b.day), ' ', dayofmonth(b.day), ', ', year(b.day)) as `date`
								FROM
									(SELECT DATE(a.Days - INTERVAL (DAYOFWEEK(a.Days) - 1) DAY) as day
    							FROM (
        			 	    SELECT " . $database->quote($end) . " - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a)) DAY AS Days
        						FROM       (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
        						CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
        						CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
        						CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
    							) a
    							WHERE a.Days >= " . $database->quote($start) . " AND a.Days <= " . $database->quote($end) . "
    							GROUP BY day) b
								LEFT JOIN (SELECT count(*) AS visits,
								                  count(distinct ip) AS visitors,
																	DATE(date - INTERVAL (DAYOFWEEK(date) - 1) DAY) as day
													 FROM #__xgroups_pages_hits
													 WHERE gidNumber=" . $database->quote($gid) . "
													   AND date(date) >= " . $database->quote($start) . "
														 AND date(date) <= " . $database->quote($end) .
														 ($pageid != '' ? " AND pageid=" . $pageid : "") . "
														 GROUP BY day
														 ORDER BY day ASC) u
								ON u.day = b.day
							ORDER BY b.day ASC";
		} elseif ($window == 'month') {
			$query = "SELECT IF(u.visits is NULL, 0, u.visits) AS visits,
	   									 IF(u.visitors IS NULL, 0, u.visitors) AS visitors,
       				 				 CONCAT(monthname(b.day), ' 1, ', year(b.day)) as `date`
								FROM
									(SELECT last_day(a.Days) AS day
    							FROM (
        			 	    SELECT " . $database->quote($end) . " - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a)) DAY AS Days
        						FROM       (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
        						CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
        						CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
        						CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
    							) a
    							WHERE a.Days >= " . $database->quote($start) . " AND a.Days <= " . $database->quote($end) . "
    							GROUP BY day) b
								LEFT JOIN (SELECT count(*) AS visits,
								                  count(distinct ip) AS visitors,
																	last_day(date) AS day
													 FROM #__xgroups_pages_hits
													 WHERE gidNumber=" . $database->quote($gid) . "
													   AND date(date) >= " . $database->quote($start) . "
														 AND date(date) <= " . $database->quote($end) .
														 ($pageid != '' ? " AND pageid=" . $pageid : "") . "
														 GROUP BY day
														 ORDER BY day ASC) u
								ON u.day = b.day
							ORDER BY b.day ASC";
		}

		$database->setQuery($query);
		$visits = $database->loadAssocList('date');
		if (!$visits)
		{
			$visits = array();
		}

		return $visits;
	}

	/**
	 * Draw a chart of page visits over time for a group or specific page
	 *
	 * @param   integer  $pid    Page ID
	 * @param   string   $start  Start date
	 * @param   string   $end    End date
	 * @param   string 	 $window Accumulation window (day, week, month)
	 * @return  string
	 */
	public function drawChart($pid, $start, $end, $window)
	{
		//vars used
		$jsObj = '';
		$script = '';

		//num pages views
		$page_visits = $this->getGroupPageVisits($this->group->get('gidNumber'), $pid, $start, $end, $window);
		$total = count($page_visits);
		$count = 0;
		foreach ($page_visits as $d => $c)
		{
			$count++;
			$jsObj .= "[new Date(\"{$d}\"), {$c['visits']}, {$c['visitors']}]";
			if ($count < $total)
			{
				$jsObj .= ", \n \t\t\t\t";
			}
		}

		/* Mimicking google toolbar: https://developers.google.com/chart/interactive/docs/gallery/toolbar
		 * CSV download: https://stackoverflow.com/a/38387061
		 *   For CSV: Format numbers to remove commas
		 *     - https://developers.google.com/chart/interactive/docs/reference#numberformat
		 *     - http://icu-project.org/apiref/icu4c/classDecimalFormat.html#_details)
		 * PNG download: https://developers.google.com/chart/interactive/docs/printing
		 * SVG: $('#page_views_chart svg')[0].outerHTML
		 */
		$script = "
			google.load(\"visualization\", \"1\", {
				packages:[\"corechart\"]
			});

			google.setOnLoadCallback(draw);

			function draw(data) {
				var data = new google.visualization.DataTable();
				var chart = new google.visualization.LineChart(document.getElementById('page_views_chart'));
      	drawChart(data, chart);
      	drawToolbar(data, chart);
    	}

			function drawToolbar(data, chart) {
				var formatter = new google.visualization.NumberFormat({pattern: '0'});
				formatter.format(data, 1);
				formatter.format(data, 2);
				var csvFormattedData = \"date,pageviews,unique_visitors\\n\" + google.visualization.dataTableToCsv(data);
				var encodedUri = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csvFormattedData);
				$('#toolbar-csv').attr('href', encodedUri);

				encodedUri = chart.getImageURI();
				$('#toolbar-png').attr('href', encodedUri);

				var svg = $('#page_views_chart svg')[0].outerHTML;
				var encodedUri = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
				$('#toolbar-svg').attr('href', encodedUri);
	    };

			function drawChart(data, chart) {
				data.addColumn('date', 'Day');
				data.addColumn('number', 'Pageviews');
				data.addColumn('number', 'Unique Visitors');
				data.addRows([
					{$jsObj}
				]);

				var options = {
					height: 240,
					focusTarget: 'category',
					legend: { position: 'top' },
					pointSize: 4,
					axisTitlesPosition: 'none',
					explorer: { actions: ['dragToZoom', 'rightClickToReset'],
					 						keepInBounds: true },
					hAxis: {
						textStyle: {
							fontSize:10
						}
					},
					vAxis: {
						minValue: 5,
					},
					chartArea: {
						width: \"92%\"
					}
				}

				chart.draw(data, options);
			}

			function myFunction() {
				$('#toolbar-dropdown').toggleClass('show');
				$('.dropbtn').toggleClass('open');
			}";

		return $script;
	}

	/**
	 * Get a count of all blog entries
	 *
	 * @param      string $gid Group alias
	 * @return     integer
	 */
	public static function getGroupBlogCount($gid)
	{
		if (!$gid)
		{
			return 0;
		}

		include_once \Component::path('com_blog') . DS . 'models' . DS . 'entry.php';

		$total = \Components\Blog\Models\Entry::all()
			->whereEquals('scope', 'group')
			->whereEquals('scope_id', $gid)
			->where('state', '!=', \Components\Blog\Models\Entry::STATE_DELETED)
			->total();

		return $total;
	}

	/**
	 * Get a count of all blog comments
	 *
	 * @param      string $gid Group alias
	 * @return     integer
	 */
	public static function getGroupBlogCommentCount($gid)
	{
		if (!$gid)
		{
			return 0;
		}

		$database = App::get('db');

		$query = "SELECT count(*) FROM `#__blog_entries` as be, `#__blog_comments` as bc WHERE be.scope='group' AND be.scope_id=" . $database->quote($gid) . " AND be.id=bc.entry_id";
		$database->setQuery($query);

		$count = $database->loadResult();
		return $count;
	}

	/**
	 * Get a count of all the group calendar events
	 *
	 * @param      string $gid Group alias
	 * @return     integer
	 */
	public static function getGroupCalendarCount($gid)
	{
		$database = App::get('db');

		$query = "SELECT COUNT(*) FROM `#__events` WHERE scope=".$database->quote('group')." AND scope_id=" . $database->quote($gid) . " AND state=1";
		$database->setQuery($query);
		return $database->loadResult();
	}
}
