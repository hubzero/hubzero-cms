<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
			'html' => '',
			'metadata' => array()
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
			$pid = Request::getString('pid', '');

			//get start and end dates
			$start = Request::getString('start', gmdate("Y-m-d 00:00:00", strtotime('-30 DAYS')));
			$end   = Request::getString('end', gmdate("Y-m-d 23:59:59"));
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
