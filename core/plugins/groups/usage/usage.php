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
			$start = Request::getVar('start', gmdate("Y-m-d 00:00:00", strtotime('-30 DAYS')));
			$end   = Request::getVar('end', gmdate("Y-m-d 23:59:59"));
			if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $start))
			{
				$start = Date::of($start)->format('Y-m-d') . ' 00:00:00';
			}
			if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $end))
			{
				$end = Date::of($end)->format('Y-m-d') . ' 00:00:00';
			}

			//make sure start date is a full php datetime
			if (strlen($start) != 19)
			{
				$start .= " 00:00:00";
			}

			//make sure end date is a full php datetime
			if (strlen($end) != 19)
			{
				$end .= " 23:59:59";
			}

			//generate script to draw chart and push to the page
			$script = $this->drawChart($pid, $start, $end);
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
	public static function getResourcesCount($gid=NULL, $authorized)
	{
		if (!$gid)
		{
			return 0;
		}
		$database = App::get('db');

		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
		$rr = new \Components\Resources\Tables\Resource($database);

		$database->setQuery("SELECT COUNT(*) FROM " . $rr->getTableName() . " AS r WHERE r.group_owner=" . $database->quote($gid));
		return $database->loadResult();
	}

	/**
	 * Get a count of all wiki pages
	 *
	 * @param      integer $gid        Group ID
	 * @param      string  $authorized Authorization level
	 * @return     integer
	 */
	public static function getWikipageCount($gid=NULL, $authorized)
	{
		if (!$gid)
		{
			return 0;
		}
		$database = App::get('db');

		$database->setQuery("SELECT COUNT(*) FROM `#__wiki_page` AS p WHERE p.scope=" . $database->quote($gid . DS . 'wiki') . " AND p.group_cn=" . $database->quote($gid));
		return $database->loadResult();
	}

	/**
	 * Get a count of all wiki attachments
	 *
	 * @param      integer $gid        Group ID
	 * @param      string  $authorized Authorization level
	 * @return     integer
	 */
	public static function getWikifileCount($gid=NULL, $authorized)
	{
		if (!$gid)
		{
			return 0;
		}
		$database = App::get('db');

		$database->setQuery("SELECT id FROM `#__wiki_page` AS p WHERE p.scope=" . $database->quote($gid . DS . 'wiki') . " AND p.group_cn=" . $database->quote($gid));
		$pageids = $database->loadObjectList();
		if ($pageids)
		{
			$ids = array();
			foreach ($pageids as $pageid)
			{
				$ids[] = $pageid->id;
			}

			$database->setQuery("SELECT COUNT(*) FROM `#__wiki_attachments` WHERE pageid IN (" . implode(',', $ids) . ")");
			return $database->loadResult();
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Get a count of all forum posts
	 *
	 * @param      integer $gid        Group ID
	 * @param      string  $authorized Authorization level
	 * @param      string  $state      State of threads
	 * @return     integer
	 */
	public static function getForumCount($gid=NULL, $authorized, $state='')
	{
		if (!$gid)
		{
			return 0;
		}
		$database = App::get('db');

		include_once(PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');

		$filters = array();
		$filters['authorized'] = $authorized;
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
		$filters['group'] = $gid;

		$forum = new \Components\Forum\Tables\Post($database);
		return $forum->getCount($filters);
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
	 * Get a list of all page views over a time period
	 *
	 * @param      string  $gid     Group ID
	 * @param      string  $pageid  Page ID
	 * @param      string  $start   Start date
	 * @param      string  $end     End date
	 * @return     array
	 */
	public function getGroupPageViews($gid, $pageid = null, $start, $end)
	{
		$database = App::get('db');

		if ($pageid != '')
		{
			$query = "SELECT * FROM `#__xgroups_pages_hits` WHERE `gidNumber`=" . $database->quote($gid) . " AND `date` > " . $database->quote($start) . " AND `date` < " . $database->quote($end) . " AND `pageid`=" . $database->quote($pageid) . " GROUP BY ip ORDER BY `date` ASC";
		}
		else
		{
			$query = "SELECT * FROM `#__xgroups_pages_hits` WHERE `gidNumber`=" . $database->quote($gid) . " AND `date` > " . $database->quote($start) . " AND `date` < " . $database->quote($end) . " GROUP BY ip ORDER BY `date` ASC";
		}
		$database->setQuery($query);
		$views = $database->loadAssocList();
		if (!$views)
		{
			$views = array();
		}

		$s = strtotime($start);
		$e = strtotime($end);
		$diff = round(($e-$s)/60/60/24);

		$page_views = array();

		for ($i=0; $i < $diff; $i++)
		{
			$count = 0;
			$date = date("M d, Y", strtotime("-{$i} DAYS", strtotime($end)));

			$day_s = date("Y-m-d 00:00:00", strtotime("-{$i} DAYS", strtotime($end)));
			$day_e = date("Y-m-d 23:59:59", strtotime("-{$i} DAYS", strtotime($end)));

			foreach ($views as $view)
			{
				$t = $view['date'];

				if ($t >= $day_s && $t <= $day_e)
				{
					$count++;
				}
			}

			$page_views[$date] = $count;
		}

		return array_reverse($page_views);
	}

	/**
	 * Draw a chart of page views over time for a specific page
	 *
	 * @param   integer  $pid    Page ID
	 * @param   string   $start  Start date
	 * @param   string   $end    End date
	 * @return  string
	 */
	public function drawChart($pid, $start, $end)
	{
		//vars used
		$jsObj = '';
		$script = '';

		//num pages views
		$page_views = $this->getGroupPageViews($this->group->get('gidNumber'), $pid, $start, $end);
		$total = count($page_views);
		$count = 0;
		foreach ($page_views as $d => $c)
		{
			$count++;
			$jsObj .= "[\"{$d}\", {$c}]";
			if ($count < $total)
			{
				$jsObj .= ", \n \t\t\t\t";
			}
		}

		$script = "
			google.load(\"visualization\", \"1\", {
				packages:[\"corechart\"]
			});

			google.setOnLoadCallback(drawChart);

			function drawChart() {
				var data = new google.visualization.DataTable();
				data.addColumn('string', 'Day');
				data.addColumn('number', 'Views');
				data.addRows([
					{$jsObj}
				]);

				var options = {
					height: 240,
					legend: 'none',
					pointSize: 4,
					axisTitlesPosition: 'none',
					hAxis: {
						textPosition:'out',
						slantedText: false,
						slantedTextAngle: 0,
						showTextEvery: 2,
						textStyle: {
							fontSize:10
						}
					},
					vAxis: {
						minValue: 10,
						textPosition: 'out'
					},
					chartArea: {
						width: \"90%\"
					}
				}

				var chart = new google.visualization.AreaChart(document.getElementById('page_views_chart'));
				chart.draw(data, options);
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

		$database = App::get('db');

		include_once(PATH_CORE . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'entry.php');

		$filters = array();
		$filters['scope'] = 'group';
		$filters['scope_id'] = $gid;

		$gb = new \Components\Blog\Tables\Entry($database);

		$total = $gb->find('count', $filters);

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
