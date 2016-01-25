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

namespace Components\Events\Site\Controllers;

use Components\Events\Tables\Configs;
use Components\Events\Tables\Event;
use Components\Events\Tables\Calendar;
use Components\Events\Tables\Category;
use Components\Events\Tables\Page;
use Components\Events\Models\Tags;
use Components\Events\Helpers\EventsDate;
use Components\Events\Helpers\Html;
use Components\Events\Tables\Respondent;
use Hubzero\Component\SiteController;
use Hubzero\Component\View;
use Hubzero\Utility\Sanitize;
use DateTimezone;
use DateTime;
use Document;
use Exception;
use Request;
use Pathway;
use Route;
use Lang;
use User;
use Date;
use App;

/**
 * Controller class for events
 */
class Events extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->dateFormat = Lang::txt('DATE_FORMAT_HZ1');
		$this->dateFormatShort = 'd M';
		$this->timeFormat = 'h:i A';
		$this->yearFormat  = "Y";
		$this->monthFormat = "m";
		$this->dayFormat   = "d";

		$this->_setup();

		if (!Request::getString('task'))
		{
			Request::setVar('task', $this->config->getCfg('startview', 'month'));
		}

		$this->registerTask('__default', $this->_task);
		$this->registerTask('register', 'eventregister');
		$this->registerTask('add', 'edit');

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		switch ($this->_task)
		{
			case 'year':
				if ($this->year) {
					Pathway::append(
						$this->year,
						'index.php?option=' . $this->_option . '&year='.$this->year
					);
				}
			break;
			case 'month':
				if ($this->year) {
					Pathway::append(
						$this->year,
						'index.php?option=' . $this->_option . '&year='.$this->year
					);
				}
				if ($this->month) {
					Pathway::append(
						$this->month,
						'index.php?option=' . $this->_option . '&year=' . $this->year . '&month='.$this->month
					);
				}
			break;
			case 'day':
				if ($this->year) {
					Pathway::append(
						$this->year,
						'index.php?option=' . $this->_option . '&year='.$this->year
					);
				}
				if ($this->month) {
					Pathway::append(
						$this->month,
						'index.php?option=' . $this->_option . '&year=' . $this->year . '&month='.$this->month
					);
				}
				if ($this->day) {
					Pathway::append(
						$this->day,
						'index.php?option=' . $this->_option . '&year=' . $this->year . '&month=' . $this->month . '&day=' . $this->day
					);
				}
			break;
			case 'week':
				if ($this->year) {
					Pathway::append(
						$this->year,
						'index.php?option=' . $this->_option . '&year='.$this->year
					);
				}
				if ($this->month) {
					Pathway::append(
						$this->month,
						'index.php?option=' . $this->_option . '&year=' . $this->year . '&month='.$this->month
					);
				}
				if ($this->day) {
					Pathway::append(
						$this->day,
						'index.php?option=' . $this->_option . '&year=' . $this->year . '&month=' . $this->month . '&day=' . $this->day
					);
				}
				Pathway::append(
					Lang::txt('EVENTS_WEEK_OF',$this->day),
					'index.php?option=' . $this->_option . '&year=' . $this->year . '&month=' . $this->month . '&day=' . $this->day . '&task=week'
				);
			break;
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle()
	{
		$this->_title = Lang::txt(strtoupper($this->_name));
		switch ($this->_task)
		{
			case 'year':
				if ($this->year)
				{
					$this->_title .= ': ' . $this->year;
				}
			break;
			case 'month':
				if ($this->year)
				{
					$this->_title .= ': ' . $this->year;
				}
				if ($this->month)
				{
					$this->_title .= '/' . $this->month;
				}
			break;
			case 'day':
				if ($this->year)
				{
					$this->_title .= ': ' . $this->year;
				}
				if ($this->month)
				{
					$this->_title .= '/' . $this->month;
				}
				if ($this->day) {
					$this->_title .= '/' . $this->day;
				}
			break;
			case 'week':
				if ($this->year)
				{
					$this->_title .= ': ' . $this->year;
				}
				if ($this->month)
				{
					$this->_title .= '/' . $this->month;
				}
				if ($this->day)
				{
					$this->_title .= '/' . $this->day;
				}
				if ($this->_task && $this->_task == 'week')
				{
					$this->_title .= ': ' . Lang::txt('EVENTS_WEEK_OF', $this->day);
				}
			break;
		}
		Document::setTitle($this->_title);
	}

	/**
	 * Perform some initial setup and set some commonly used vars
	 *
	 * @return     void
	 */
	private function _setup()
	{
		// Load the events configuration
		$config = new Configs($this->database);
		$config->load();

		$this->config = $config;

		// Set some defines

		/**
		 * Description for ''_CAL_CONF_STARDAY''
		 */
		define('_CAL_CONF_STARDAY', $config->getCfg('starday'));

		/**
		 * Description for ''_CAL_CONF_DATEFORMAT''
		 */
		define('_CAL_CONF_DATEFORMAT', $config->getCfg('dateformat'));

		$this->offset = Config::get('offset');

		// Incoming
		$this->year     = Request::getVar('year',  strftime("%Y", time()+($this->offset*60*60)));
		$this->month    = Request::getVar('month', strftime("%m", time()+($this->offset*60*60)));
		$this->day      = Request::getVar('day',   strftime("%d", time()+($this->offset*60*60)));
		$this->category = Request::getInt('category', 0);
		$this->gid      = intval(User::get('gid'));

		// fix single digit day & month
		if ($this->day <= 9 && preg_match("/(^[1-9]{1})/", $this->day))
		{
			$this->day = "0{$this->day}";
		}
		if ($this->month <=9 && preg_match("/(^[1-9]{1})/", $this->month))
		{
			$this->month = "0{$this->month}";
		}

		// make sure we have a valid year
		if ($this->year < 1000)
		{
			App::abort(404, Lang::txt('COM_EVENTS_YEAR_NOT_VALID'));
			return;
		}
		// make sure we have a valid month
		if ($this->month < 1 || $this->month > 12)
		{
			App::abort(404, Lang::txt('COM_EVENTS_MONTH_NOT_VALID'));
			return;
		}
		// make sure we have a valid day
		if ($this->day < 1 || $this->day > 31)
		{
			App::abort(404, Lang::txt('COM_EVENTS_DAY_NOT_VALID'));
			return;
		}
	}

	/**
	 * Default Task
	 *
	 * @return [type] [description]
	 */
	public function displayTask()
	{
		switch ($this->config->getCfg('startview', 'month'))
		{
			case 'week':
				$this->weekTask();
				break;
			case 'year':
				$this->yearTask();
				break;
			case 'month':
			default:
				$this->monthTask();
		}
	}

	/**
	 * List events for a given year
	 *
	 * @return     void
	 */
	public function yearTask()
	{
		// Get some needed info
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$offset = $this->offset;
		$option = $this->_option;
		$gid    = $this->gid;

		// Set some filters
		$filters = array();
		$filters['gid'] = $gid;
		$filters['year'] = $year;
		$filters['category'] = $this->category;
		$filters['scope'] = 'event';

		// Retrieve records
		$ee = new Event($this->database);
		$rows = $ee->getEvents('year', $filters);

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if (User::isGuest())
		{
			$authorized = false;
		}
		if ($this->config->getCfg('adminlevel'))
		{
			$authorized = $this->_authorize();
		}

		// Get a list of categories
		$categories = $this->_getCategories();

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Output HMTL
		$this->view->setLayout('year')->setName('browse');
		$this->view->option = $this->_option;
		$this->view->title = $this->_title;
		$this->view->task = $this->_task;
		$this->view->year = $year;
		$this->view->month = $month;
		$this->view->day = $day;
		$this->view->rows = $rows;
		$this->view->authorized = $authorized;
		$this->view->fields = $this->config->getCfg('fields');
		$this->view->category = $this->category;
		$this->view->categories = $categories;
		$this->view->offset = $offset;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * List events for a given year and month
	 *
	 * @return     void
	 */
	public function monthTask()
	{
		// Get some needed info
		$offset = $this->offset;
		$option = $this->_option;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$gid    = $this->gid;

		// Set some dates
		$select_date = $year . '-' . $month . '-01 00:00:00';
		$select_date_fin = $year . '-' . $month . '-' . date("t",mktime(0, 0, 0, ($month+1), 0, (int) $year)) . ' 23:59:59';
		$select_date = Date::of($select_date, Config::get('offset'));
		$select_date_fin = Date::of($select_date_fin, Config::get('offset'));

		// Set some filters
		$filters = array();
		$filters['gid'] = $gid;
		$filters['select_date'] = $select_date->toSql();
		$filters['select_date_fin'] = $select_date_fin->toSql();
		$filters['category'] = $this->category;
		$filters['scope'] = 'event';

		// Retrieve records
		$ee = new Event($this->database);
		$rows = $ee->getEvents('month', $filters);

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if (User::isGuest())
		{
			$authorized = false;
		}
		if ($this->config->getCfg('adminlevel'))
		{
			$authorized = $this->_authorize();
		}

		// Get a list of categories
		$categories = $this->_getCategories();

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->setLayout('month')->setName('browse');
		$this->view->option = $this->_option;
		$this->view->title = $this->_title;
		$this->view->task = $this->_task;
		$this->view->year = $year;
		$this->view->month = $month;
		$this->view->day = $day;
		$this->view->rows = $rows;
		$this->view->authorized = $authorized;
		$this->view->fields = $this->config->getCfg('fields');
		$this->view->category = $this->category;
		$this->view->categories = $categories;
		$this->view->offset = $offset;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * List events for a given year/month/week
	 *
	 * @return     void
	 */
	public function weekTask()
	{
		// Get some needed info
		$offset = $this->offset;
		$option = $this->_option;
		$year   = intval($this->year);
		$month  = intval($this->month);
		$day    = intval($this->day);

		$startday = _CAL_CONF_STARDAY;
		$numday = ((date("w",mktime(0,0,0,$month,$day,$year))-$startday)%7);
		if ($numday == -1)
		{
			$numday = 6;
		}
		$week_start = mktime(0, 0, 0, $month, ($day - $numday), $year);

		$this_date = new EventsDate();
		$this_date->setDate(strftime("%Y", $week_start), strftime("%m", $week_start), strftime("%d", $week_start));
		$this_enddate = clone($this_date);
		$this_enddate->addDays(+6);

		$sdt = Date::of($this_date->year . '-' . $this_date->month . '-' . $this_date->day . ' 00:00:00')->toLocal($this->dateFormatShort);
		$edt = Date::of($this_enddate->year . '-' . $this_enddate->month . '-' . $this_enddate->day . ' 00:00:00')->toLocal($this->dateFormatShort);

		$this_currentdate = $this_date;

		$categories = $this->_getCategories();

		$filters = array();
		$filters['gid'] = $this->gid;
		$filters['category'] = $this->category;
		$filters['scope'] = 'event';

		$ee = new Event($this->database);

		$rows = array();
		for ($d = 0; $d < 7; $d++)
		{
			if ($d > 0)
			{
				$this_currentdate->addDays(+1);
			}
			$week = array();
			$week['day']   = sprintf("%02d", $this_currentdate->day);
			$week['month'] = sprintf("%02d", $this_currentdate->month);
			$week['year']  = sprintf("%4d",  $this_currentdate->year);

			$select_date     = sprintf("%4d-%02d-%02d 00:00:00", $week['year'], $week['month'], $week['day']);
			$select_date_fin = sprintf("%4d-%02d-%02d 23:59:59", $week['year'], $week['month'], $week['day']);
			$select_date     = Date::of($select_date, Config::get('offset'));
			$select_date_fin = Date::of($select_date_fin, Config::get('offset'));

			$filters['select_date'] = $select_date->toSql();
			$filters['select_date_fin'] = $select_date_fin->toSql();

			$rows[$d] = array();
			$rows[$d]['events'] = $ee->getEvents('day', $filters);
			$rows[$d]['week']   = $week;
		}

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if (User::isGuest())
		{
			$authorized = false;
		}
		if ($this->config->getCfg('adminlevel'))
		{
			$authorized = $this->_authorize();
		}

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Output HTML;
		$this->view->setLayout('week')->setName('browse');
		$this->view->option = $this->_option;
		$this->view->title = $this->_title;
		$this->view->task = $this->_task;
		$this->view->year = $year;
		$this->view->month = $month;
		$this->view->day = $day;
		$this->view->rows = $rows;
		$this->view->authorized = $authorized;
		$this->view->fields = $this->config->getCfg('fields');
		$this->view->category = $this->category;
		$this->view->categories = $categories;
		$this->view->offset = $offset;
		$this->view->startdate = $sdt;
		$this->view->enddate = $edt;
		$this->view->week = $week;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * View events for a given day
	 *
	 * @return     void
	 */
	public function dayTask()
	{
		// Get some needed info
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$offset = $this->offset;
		$option = $this->_option;

		// Get the events for this day
		$filters = array();
		$filters['gid'] = $this->gid;
		$filters['category'] = $this->category;
		$filters['scope'] = 'event';

		$select_date     = sprintf("%4d-%02d-%02d 00:00:00", $year, $month, $day);
		$select_date_fin = sprintf("%4d-%02d-%02d 23:59:59", $year, $month, $day);
		$select_date     = Date::of($select_date, Config::get('offset'));
		$select_date_fin = Date::of($select_date_fin, Config::get('offset'));
		$filters['select_date'] = $select_date->toSql();
		$filters['select_date_fin'] = $select_date_fin->toSql();


		$ee = new Event($this->database);
		$events = $ee->getEvents('day', $filters);

		// Go through each event and ensure it should be displayed
		// $events = array();
		// if (count($rows) > 0)
		// {
		// 	foreach ($rows as $row)
		// 	{
		// 		$checkprint = new EventsRepeat($row, $year, $month, $day);
		// 		if ($checkprint->viewable == true)
		// 		{
		// 			$events[] = $row;
		// 		}
		// 	}
		// }

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if (User::isGuest())
		{
			$authorized = false;
		}
		if ($this->config->getCfg('adminlevel'))
		{
			$authorized = $this->_authorize();
		}

		// Get a list of categories
		$categories = $this->_getCategories();

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->setLayout('day')->setName('browse');
		$this->view->option = $this->_option;
		$this->view->title = $this->_title;
		$this->view->task = $this->_task;
		$this->view->year = $year;
		$this->view->month = $month;
		$this->view->day = $day;
		$this->view->rows = $events;
		$this->view->authorized = $authorized;
		$this->view->fields = $this->config->getCfg('fields');
		$this->view->category = $this->category;
		$this->view->categories = $categories;
		$this->view->offset = $offset;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * View details of an event
	 *
	 * @return     void
	 */
	public function detailsTask()
	{
		// Get some needed info
		$offset = $this->offset;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$option = $this->_option;

		// Incoming
		$id = Request::getInt('id', 0, 'request');

		// Load event
		$row = new Event($this->database);
		$row->load($id);

		// Ensure we have an event
		if (!$row || !$row->id)
		{
			App::abort(404, Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR') . ' ' . Lang::txt('EVENTS_CAL_LANG_THIS_DAY'));
		}

		//is this a group rescricted event
		if ($row->scope == 'group')
		{
			$group = \Hubzero\User\Group::getInstance($row->scope_id);

			//if we have a group and we are a member
			if (is_object($group))
			{
				//redirect to group calendar
				$redirect = Route::url( 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=calendar&action=details&event_id=' . $row->id, false);
				App::redirect($redirect);
				return;
			}
			else
			{
				App::abort(404, Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR') . ' ' . Lang::txt('EVENTS_CAL_LANG_THIS_DAY'));
				return;
			}
		}

		$event_up = new EventsDate($row->publish_up);
		$row->start_date = Html::getDateFormat($event_up->year,$event_up->month,$event_up->day,0);
		$row->start_time = (defined('_CAL_USE_STD_TIME') && _CAL_USE_STD_TIME == 'YES')
						 ? $event_up->get12hrTime()
						 : $event_up->get24hrTime();

		$event_down = new EventsDate($row->publish_down);
		$row->stop_date = Html::getDateFormat($event_down->year,$event_down->month,$event_down->day,0);
		$row->stop_time = (defined('_CAL_USE_STD_TIME') && _CAL_USE_STD_TIME == 'YES')
						? $event_down->get12hrTime()
						: $event_down->get24hrTime();

		// Kludge for overnight events, advance the displayed stop_date by 1 day when an overnighter is detected
		if ($row->stop_time < $row->start_time)
		{
			$event_down->addDays(1);
		}

		// Get time zone name (i.e. not just offset - ex: '-5')
		//$row->time_zone = Html::getTimeZoneName($row->time_zone);

		// Parse http and mailto
		$alphadigit = "([a-z]|[A-Z]|[0-9])";

		// Adresse
		$row->adresse_info = preg_replace("/(mailto:\/\/)?((-|$alphadigit|\.)+)@((-|$alphadigit|\.)+)(\.$alphadigit+)/i", "<a href=\"mailto:$2@$5$8\">$2@$5$8</a>", $row->adresse_info);
		$row->adresse_info = preg_replace("/(http:\/\/|https:\/\/)((-|$alphadigit|\.)+)(\.$alphadigit+)/i", "<a href=\"$1$2$5$8\">$1$2$5$8</a>", $row->adresse_info);

		// Contact
		$row->contact_info = stripslashes(strip_tags($row->contact_info));
		if (substr($row->contact_info, 0, strlen('mailto:')) == 'mailto:')
		{
			$row->contact_info = '<a href="mailto:' . $this->obfuscate(substr($row->contact_info, strlen('mailto:'))) . '">' . $this->obfuscate(substr($row->contact_info, strlen('mailto:'))) . '</a>';
		}
		else if (preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $row->contact_info))
		{
			$row->contact_info = '<a href="mailto:' . $this->obfuscate($row->contact_info) . '">' . $this->obfuscate($row->contact_info) . '</a>';
		}
		//$row->contact_info = preg_replace("/(mailto:\/\/)?((-|$alphadigit|\.)+)@((-|$alphadigit|\.)+)(\.$alphadigit+)/i", "<a href=\"mailto:$2@$5$8\">$2@$5$8</a>", $row->contact_info);
		$row->contact_info = preg_replace("/(http:\/\/|https:\/\/)((-|$alphadigit|\.)+)(\.$alphadigit+)/i", "<a href=\"$1$2$5$8\">$1$2$5$8</a>", $row->contact_info);

		$fields = $this->config->getCfg('fields');
		if (!empty($fields))
		{
			for ($i=0, $n=count($fields); $i < $n; $i++)
			{
				// Explore the text and pull out all matches
				array_push($fields[$i], self::parseTag($row->content, $fields[$i][0]));

				// Clean the original text of any matches
				$row->content = str_replace('<ef:' . $fields[$i][0] . '>' . end($fields[$i]) . '</ef:' . $fields[$i][0] . '>', '', $row->content);
			}
			$row->content = trim($row->content);
		}

		$bits = explode('-', $row->publish_up);
		$eyear = $bits[0];
		$emonth = $bits[1];
		$edbits = explode(' ', $bits[2]);
		$eday = $edbits[0];

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;

		$auth = true;
		if (User::isGuest())
		{
			$auth = false;
		}
		if ($this->config->getCfg('adminlevel'))
		{
			$auth = $this->_authorize();
		}

		// Get a list of categories
		$categories = $this->_getCategories();

		// Get tags on this event
		$rt = new Tags($row->id);
		$tags = $rt->render();

		// Set the page title
		Document::setTitle(Lang::txt(strtoupper($this->_name)) . ': ' . Lang::txt(strtoupper($this->_name) . '_' . strtoupper($this->_task)) . ': ' . stripslashes($row->title));

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt(
				strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		Pathway::append(
			$eyear,
			'index.php?option=' . $this->_option . '&year=' . $eyear
		);
		Pathway::append(
			$emonth,
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth
		);
		Pathway::append(
			$eday,
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth . '&day=' . $eday
		);
		Pathway::append(
			stripslashes($row->title),
			'index.php?option=' . $this->_option . '&task=details&id=' . $row->id
		);

		// Incoming
		$alias = Request::getVar('page', '');

		// Load the current page
		$page = new Page($this->database);
		if ($alias)
		{
			$page->loadFromAlias($alias, $row->id);
		}

		// Get the pages for this workshop
		$pages = $page->loadPages($row->id);

		if ($alias)
		{
			Pathway::append(
				stripslashes($page->title),
				'index.php?option=' . $this->_option . '&task=details&id=' . $row->id . '&page=' . $page->alias
			);
		}

		// Build the HTML
		$this->view->setLayout('default')->setName('details');
		if (Request::getVar('no_html', 0))
		{
			$this->view->setLayout('modal');
		}
		$this->view->option = $this->_option;
		$this->view->title = Lang::txt(strtoupper($this->_name)) . ': ' . Lang::txt(strtoupper($this->_name) . '_' . strtoupper($this->_task));
		$this->view->task = $this->_task;
		$this->view->year = $eyear;
		$this->view->month = $emonth;
		$this->view->day = $eday;
		$this->view->row = $row;
		$this->view->authorized = $authorized;
		$this->view->fields = $fields;
		$this->view->config = $this->config;
		$this->view->categories = $categories;
		$this->view->offset = $offset;
		$this->view->tags = $tags;
		$this->view->auth = $auth;
		$this->view->page = $page;
		$this->view->pages = $pages;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Obfuscate an email adress
	 *
	 * @param      string $email Address to obfuscate
	 * @return     string
	 */
	public function obfuscate($email)
	{
		$length = strlen($email);
		$obfuscatedEmail = '';
		for ($i = 0; $i < $length; $i++)
		{
			$obfuscatedEmail .= '&#' . ord($email[$i]) . ';';
		}

		return $obfuscatedEmail;
	}

	/**
	 * Display a form for registering for an event
	 *
	 * @return     void
	 */
	public function eventregisterTask()
	{
		// Get some needed info
		$offset = $this->offset;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$option = $this->_option;

		// Incoming
		$id = Request::getInt('id', 0, 'request');

		// Ensure we have an ID
		if (!$id)
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
			return;
		}

		// Load event
		$event = new Event($this->database);
		$event->load($id);

		// Ensure we have an event
		if (!$event->title || $event->registerby == '0000-00-00 00:00:00')
		{
			App::Redirect(Route::url('index.php?option=' . $this->_option));
			return;
		}

		$auth = true;
		if ($this->config->getCfg('adminlevel'))
		{
			$auth = $this->_authorize();
		}

		$bits = explode('-', $event->publish_up);
		$eyear = $bits[0];
		$emonth = $bits[1];
		$edbits = explode(' ', $bits[2]);
		$eday = $edbits[0];

		// Set the page title
		Document::setTitle(Lang::txt(strtoupper($this->_name)).': '.Lang::txt('EVENTS_REGISTER').': '.stripslashes($event->title));

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt(
				strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		Pathway::append(
			$eyear,
			'index.php?option=' . $this->_option . '&year=' . $eyear
		);
		Pathway::append(
			$emonth,
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth
		);
		Pathway::append(
			$eday,
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth . '&day=' . $eday
		);
		Pathway::append(
			stripslashes($event->title),
			'index.php?option=' . $this->_option . '&task=details&id=' . $event->id
		);
		Pathway::append(
			Lang::txt('EVENTS_REGISTER'),
			'index.php?option=' . $this->_option . '&task=details&id=' . $event->id . '&page=register'
		);

		$page = new Page($this->database);
		$page->alias = $this->_task;

		// Get the pages for this workshop
		$pages = $page->loadPages($event->id);

		// Check if registration is still open
		$registerby = strtotime($event->registerby);
		$now = time();

		$register = array();
		if (!User::isGuest())
		{
			$profile = new \Hubzero\User\Profile();
			$profile->load(User::get('id'));

			$register['firstname']   = $profile->get('givenName');
			$register['lastname']    = $profile->get('surname');
			$register['affiliation'] = $profile->get('organization');
			$register['email']       = $profile->get('email');
			$register['telephone']   = $profile->get('phone');
			$register['website']     = $profile->get('url');
		}

		// Is the registration open?
		if ($registerby >= $now)
		{
			// Is the registration restricted?
			if ($event->restricted)
			{
				$passwrd = Request::getVar('passwrd', '', 'post');

				if ($event->restricted == $passwrd)
				{
					// Instantiate a view
					$this->view->setLayout('default');
					$this->view->state = 'open';
				}
				else
				{
					// Instantiate a view
					$this->view->setLayout('restricted');
					$this->view->state = 'restricted';
				}
			}
			else
			{
				// Instantiate a view
				$this->view->setLayout('default');
				$this->view->state = 'open';
			}
		}
		else
		{
			// Instantiate a view
			$this->view->setLayout('closed');
			$this->view->state = 'closed';
		}

		// Output HTML
		$this->view->setName('register');
		$this->view->option = $this->_option;
		$this->view->title = Lang::txt(strtoupper($this->_name)) . ': ' . Lang::txt('EVENTS_REGISTER');
		$this->view->task = $this->_task;
		$this->view->year = $year;
		$this->view->month = $month;
		$this->view->day = $day;
		$this->view->offset = $offset;
		$this->view->event = $event;
		$this->view->authorized = $auth;
		$this->view->page = $page;
		$this->view->pages = $pages;
		$this->view->register = $register;
		$this->view->arrival = null;
		$this->view->departure = null;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Process event registration
	 *
	 * @return     void
	 */
	public function processTask()
	{
		// Get some needed info
		$offset = $this->offset;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$option = $this->_option;

		// Incoming
		$id = Request::getInt('id', 0, 'post');

		// Ensure we have an ID
		if (!$id)
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
			return;
		}

		// Load event
		$event = new Event($this->database);
		$event->load($id);
		$this->event = $event;

		// Ensure we have an event
		if (!$event->title)
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
			return;
		}

		$auth = true;
		if ($this->config->getCfg('adminlevel'))
		{
			$auth = $this->_authorize();
		}

		$bits = explode('-', $event->publish_up);
		$eyear  = $bits[0];
		$emonth = $bits[1];
		$edbits = explode(' ', $bits[2]);
		$eday   = $edbits[0];

		$page = new Page($this->database);
		$page->alias = $this->_task;

		// Get the pages for this workshop
		$pages = $page->loadPages($event->id);

		// Set the page title
		Document::setTitle(Lang::txt(strtoupper($this->_name)) . ': ' . Lang::txt('EVENTS_REGISTER') . ': ' . stripslashes($event->title));

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt(
				strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		Pathway::append(
			$eyear,
			'index.php?option=' . $this->_option . '&year=' . $eyear
		);
		Pathway::append(
			$emonth,
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth
		);
		Pathway::append(
			$eday,
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth . '&day=' . $eday
		);
		Pathway::append(
			stripslashes($event->title),
			'index.php?option=' . $this->_option . '&task=details&id=' . $event->id
		);
		Pathway::append(
			Lang::txt('EVENTS_REGISTER'),
			'index.php?option=' . $this->_option . '&task=details&id=' . $event->id . '&page=register'
		);

		// Incoming
		$register   = Request::getVar('register', NULL, 'post');
		$arrival    = Request::getVar('arrival', NULL, 'post');
		$departure  = Request::getVar('departure', NULL, 'post');
		$dietary    = Request::getVar('dietary', NULL, 'post');
		$bos        = Request::getVar('bos', NULL, 'post');
		$dinner     = Request::getVar('dinner', NULL, 'post');
		$disability = Request::getVar('disability', NULL, 'post');
		$race       = Request::getVar('race', NULL, 'post');

		if ($register)
		{
			$register = array_map('trim', $register);
			$register = array_map(array('\\Hubzero\\Utility\\Sanitize', 'stripAll'), $register);

			$validemail = $this->_validEmail($register['email']);
		}
		if ($arrival)
		{
			$arrival = array_map('trim', $arrival);
			$arrival = array_map(array('\\Hubzero\\Utility\\Sanitize', 'stripAll'), $arrival);
		}
		if ($departure)
		{
			$departure = array_map('trim', $departure);
			$departure = array_map(array('\\Hubzero\\Utility\\Sanitize', 'stripAll'), $departure);
		}
		if ($dietary)
		{
			$dietary = array_map('trim', $dietary);
			$dietary = array_map(array('\\Hubzero\\Utility\\Sanitize', 'stripAll'), $dietary);
		}

		// check to make sure this is the only time registering
		if (Respondent::checkUniqueEmailForEvent($register['email'], $event->id) > 0)
		{
			$this->setError(Lang::txt('EVENTS_EVENT_REGISTRATION_PREVIOUS'));
			$validemail = 0;
		}

		if ($register['firstname'] && $register['lastname'] && ($validemail == 1))
		{
			$email = $event->email;
			$subject = Lang::txt('EVENTS_EVENT_REGISTRATION') . ': ' . $event->title;
			$hub = array(
				'email' => $register['email'],
				'name'  => Config::get('sitename') . ' ' . Lang::txt('EVENTS_EVENT_REGISTRATION')
			);

			$eview = new \Hubzero\Component\View(array('name'=>'register','layout'=>'email'));
			$eview->option = $this->_option;
			$eview->sitename = Config::get('sitename');
			$eview->register = $register;
			$eview->race = $race;
			$eview->dietary = $dietary;
			$eview->disability = $disability;
			$eview->arrival = $arrival;
			$eview->departure = $departure;
			$eview->dinner = $dinner;
			$eview->bos = $bos;
			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			// check to see if event manager email is configured
			if ($email != "" || $email != NULL || !isset($email))
			{
				// one for the event manager
				$this->_sendEmail($hub, $email, $subject, $message);
			}

			// one for the attendee
			$this->_sendEmail($hub, $register['email'], $subject, $message);

			$this->_log($register);

			$this->view->setLayout('thanks');
		}
		else
		{
			$this->view->setLayout('default');
		}
		$this->view->setName('register');
		$this->view->state = 'open';
		$this->view->option = $this->_option;
		$this->view->title = Lang::txt(strtoupper($this->_name)) . ': ' . Lang::txt('EVENTS_REGISTER');
		$this->view->task = $this->_task;
		$this->view->year = $year;
		$this->view->month = $month;
		$this->view->day = $day;
		$this->view->offset = $offset;
		$this->view->event = $event;
		$this->view->authorized = $auth;
		$this->view->page = $page;
		$this->view->pages = $pages;
		$this->view->register = $register;
		$this->view->arrival = $arrival;
		$this->view->departure = $departure;
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}

	/**
	 * Log someone registering for an event
	 *
	 * @param      unknown $reg Parameter description (if any) ...
	 * @return     void
	 */
	private function _log($reg)
	{
		$this->database->setQuery(
			'INSERT INTO #__events_respondents(
				event_id,
				first_name, last_name, affiliation, title, city, state, zip, country, telephone, fax, email,
				website, position_description, highest_degree, gender, arrival, departure, disability_needs,
				dietary_needs, attending_dinner, abstract, comment
			)
			VALUES (' .
				$this->event->id . ', ' .
				$this->_getValueString($this->database, $reg, array(
					'firstname', 'lastname', 'affiliation', 'title', 'city', 'state', 'postalcode', 'country', 'telephone', 'fax', 'email',
					'website', 'position', 'degree', 'gender', 'arrival', 'departure', 'disability',
					'dietary', 'dinner', 'additional', 'comments'
				)) .
			')'
		);
		$this->database->query();
		$races = Request::getVar('race', NULL, 'post');
		if (!is_null($races) && (!isset($races['refused']) || !$races['refused']))
		{
			$resp_id = $this->database->insertid();
			foreach (array('nativeamerican', 'asian', 'black', 'hawaiian', 'white', 'hispanic') as $race)
			{
				if (array_key_exists($race, $races) && $races[$race])
				{
					$this->database->execute(
						'INSERT INTO #__events_respondent_race_rel(respondent_id, race, tribal_affiliation)
						VALUES (' . $resp_id . ', \'' . $race . '\', ' . ($race == 'nativeamerican' ? $this->database->quote($races['nativetribe']) : 'NULL') . ')'
					);
				}
			}
		}
	}

	/**
	 * Short description for '_getValueString'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $database Parameter description (if any) ...
	 * @param      array $reg Parameter description (if any) ...
	 * @param      array $values Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function _getValueString($database, $reg, $values)
	{
		$rv = array();
		foreach ($values as $val)
		{
			switch ($val)
			{
				case 'position':
					$rv[] = ((isset($reg['position']) || isset($reg['position_other'])) && ($reg['position'] || $reg['position_other']))
						? $this->database->quote(
							$reg['position'] ? $reg['position'] : $reg['position_other']
						)
						: 'NULL';
				break;
				case 'gender':
					$rv[] = (isset($reg['sex']) && ($reg['sex'] == 'male' || $reg['sex'] == 'female'))
						? '\'' . substr($reg['sex'], 0, 1) . '\''
						: 'NULL';
				break;
				case 'dinner':
					$dinner = Request::getVar('dinner', NULL, 'post');
					$rv[] = is_null($dinner) ? 'NULL' : $dinner ? '1' : '0';
				break;
				case 'dietary':
					$rv[] = (($dietary = Request::getVar('dietary', NULL, 'post')))
						? $this->database->quote($dietary['specific'])
						: 'NULL';
				break;
				case 'arrival': case 'departure':
					$rv[] = ($date = Request::getVar($val, NULL, 'post'))
						? $this->database->quote($date['day'] . ' ' . $date['time'])
						: 'NULL';
				break;
				case 'disability':
					$disability = Request::getVar('disability', NULL, 'post');
					$rv[] = ($disability) ? '1' : '0';
				break;
				default:
					$rv[] = array_key_exists($val, $reg) && isset($reg[$val]) ? $this->database->quote($reg[$val]) : 'NULL';
			}
		}
		return implode($rv, ',');
	}

	/**
	 * Redirect to login form
	 *
	 * @return     void
	 */
	public function loginTask()
	{
		$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
			Lang::txt('EVENTS_LOGIN_NOTICE'),
			'warning'
		);
	}

	/**
	 * Short description for 'edit'
	 *
	 * Long description (if any) ...
	 *
	 * @param      mixed $row Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function editTask($row=NULL)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			if (Pathway::count() <= 0)
			{
				Pathway::append(
					Lang::txt(strtoupper($this->_name)),
					'index.php?option=' . $this->_option
				);
			}
			Pathway::append(
				Lang::txt('EVENTS_CAL_LANG_ADD_TITLE'),
				'index.php?option=' . $this->_option . '&task=add'
			);

			$this->loginTask();
			return;
		}

		// We need at least one category before we can proceed
		$cat = new Category($this->database);
		if ($cat->getCategoryCount($this->_option) < 1)
		{
			throw new Exception(Lang::txt('EVENTS_LANG_NEED_CATEGORY'), 500);
		}

		// Incoming
		$id = Request::getInt('id', 0, 'request');

		// Load event object
		if (!is_object($row))
		{
			$row = new Event($this->database);
			$row->load($id);
		}

		// Do we have an ID?
		if ($row->id)
		{
			// Yes - edit mode

			// Are they authorized to make edits?
			if (!$this->_authorize($row->created_by)
				&& !(User::get('id') == $row->created_by))
			{
				// Not authorized - redirect
				App::redirect(Route::url('index.php?option=' . $this->_option));
				return;
			}

			//get timezone
			$timezone = timezone_name_from_abbr('',$this->offset*3600, NULL);
			$timezone = new DateTimeZone($timezone);

			// get start date and time
			$start_publish = Date::of($row->publish_up, $timezone)->format('Y-m-d');
			$start_time = Date::of($row->publish_up, $timezone)->format('H:i');

			// get end date and time
			$stop_publish = Date::of($row->publish_down, $timezone)->format('Y-m-d');
			$end_time = Date::of($row->publish_down, $timezone)->format('H:i');

			$time_zone = $row->time_zone;

			$registerby_date = Date::of($row->registerby, $timezone)->format('Y-m-d');
			$registerby_time = Date::of($row->registerby, $timezone)->format('H:i');

			$arr = array(
				\Html::select('option', 0, strtolower(Lang::txt('EVENTS_NO')), 'value', 'text'),
				\Html::select('option', 1, strtolower(Lang::txt('EVENTS_YES')), 'value', 'text'),
			);

			$lists['state'] = \Html::select('genericlist', $arr, 'state', '', 'value', 'text', $row->state, false, false);
		}
		else
		{
			if ($row->publish_up && $row->publish_up != '0000-00-00 00:00:00')
			{
				$event_up = new EventsDate($row->publish_up);
				$start_publish = sprintf("%4d-%02d-%02d", $event_up->year, $event_up->month, $event_up->day);
				$start_time = $event_up->hour . ':' . $event_up->minute;

				$event_down = new EventsDate($row->publish_down);
				$stop_publish = sprintf("%4d-%02d-%02d", $event_down->year, $event_down->month, $event_down->day);
				$end_time = $event_down->hour . ':' . $event_down->minute;

				$time_zone = $row->time_zone;

				$event_registerby = new EventsDate($row->registerby);
				$registerby_date = sprintf("%4d-%02d-%02d", $event_registerby->year, $event_registerby->month, $event_registerby->day);
				$registerby_time = $event_registerby->hour . ':' . $event_registerby->minute;
			}
			else
			{
				// No ID - we're creating a new event
				$year  = $this->year;
				$month = $this->month;
				$day   = $this->day;

				if ($year && $month && $day)
				{
					$start_publish = $year . '-' . $month . '-' . $day;
					$stop_publish = $year . '-' . $month . '-' . $day;
					$registerby_date = $year . '-' . $month . '-' . $day;
				}
				else
				{
					$offset = $this->offset;

					$start_publish = strftime("%Y-%m-%d", time()+($offset*60*60)); //date("Y-m-d");
					$stop_publish = strftime("%Y-%m-%d", time()+($offset*60*60));  //date("Y-m-d");
					$registerby_date = strftime("%Y-%m-%d", time()+($offset*60*60));  //date("Y-m-d");
				}

				$start_time = "08:00";
				$end_time = "17:00";
				$registerby_time = "08:00";
				$time_zone = -5;
			}

			// If user hits refresh, try to maintain event form state
			$row->bind($_POST);
			$row->created_by = User::get('id');

			$lists = '';
		}

		// Get custom fields
		$fields = $this->config->getCfg('fields');
		if (!empty($fields))
		{
			for ($i=0, $n=count($fields); $i < $n; $i++)
			{
				// Explore the text and pull out all matches
				array_push($fields[$i], self::parseTag($row->content, $fields[$i][0]));

				// Clean the original text of any matches
				$row->content = str_replace('<ef:' . $fields[$i][0] . '>' . end($fields[$i]) . '</ef:' . $fields[$i][0] . '>', '', $row->content);
			}
			$row->content = trim($row->content);
		}

		list($start_hrs, $start_mins) = explode(':', $start_time);
		list($end_hrs, $end_mins) = explode(':', $end_time);
		list($registerby_hrs, $registerby_mins) = explode(':', $registerby_time);
		$start_pm = false;
		$end_pm = false;
		$registerby_pm = false;
		if ($this->config->getCfg('calUseStdTime') == 'YES')
		{
			$start_hrs = intval($start_hrs);
			if ($start_hrs >= 12) $start_pm = true;
			if ($start_hrs > 12) $start_hrs -= 12;
			else if ($start_hrs == 0) $start_hrs = 12;
			if (strlen($start_mins) == 1) $start_mins = '0'.$start_mins;
			$start_time = $start_hrs . ':' . $start_mins;

			$end_hrs = intval($end_hrs);
			if ($end_hrs >= 12) $end_pm = true;
			if ($end_hrs > 12) $end_hrs -= 12;
			else if ($end_hrs == 0) $end_hrs = 12;

			$registerby_hrs = intval($registerby_hrs);
			if ($registerby_hrs >= 12) $registerby_pm = true;
			if ($registerby_hrs > 12) $registerby_hrs -= 12;
			else if ($registerby_hrs == 0) $registerby_hrs = 12;
			if (strlen($registerby_mins) == 1) $registerby_mins = '0' . $registerby_mins;
			$registerby_time = $registerby_hrs . ':' . $registerby_mins;
		}
		if (strlen($start_mins) == 1) $start_mins = '0' . $start_mins;
		if (strlen($start_hrs) == 1) $start_hrs = '0' . $start_hrs;
		$start_time = $start_hrs . ':' . $start_mins;

		if (strlen($end_mins) == 1) $end_mins = '0' . $end_mins;
		if (strlen($end_hrs) == 1) $end_hrs = '0' . $end_hrs;
		$end_time = $end_hrs . ':' . $end_mins;

		if (strlen($registerby_mins) == 1) $registerby_mins = '0' . $registerby_mins;
		if (strlen($registerby_hrs) == 1) $registerby_hrs = '0' . $registerby_hrs;
		$registerby_time = $registerby_hrs . ':' . $registerby_mins;

		$times = array();
		$times['start_publish'] = $start_publish;
		$times['start_time'] = $start_time;
		$times['start_pm'] = $start_pm;

		$times['stop_publish'] = $stop_publish;
		$times['end_time'] = $end_time;
		$times['end_pm'] = $end_pm;

		$times['time_zone'] = $time_zone;

		$times['registerby_date'] = $registerby_date;
		$times['registerby_time'] = $registerby_time;
		$times['registerby_pm'] = $registerby_pm;

		// Get tags on this event
		$lists['tags'] = '';
		if ($row->id)
		{
			$rt = new Tags($row->id);
			$lists['tags'] = $rt->render('string');
		}

		// get tags passed from failed save
		if (isset($this->tags))
		{
			$lists['tags'] = $this->tags;
		}

		// Set the title
		Document::setTitle(Lang::txt(strtoupper($this->_name)) . ': ' . Lang::txt(strtoupper($this->_name) . '_' . strtoupper($this->_task)));

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		$p = 'index.php?option=' . $this->_option . '&task=' . $this->_task;
		if ($row->id)
		{
			$p .= '&id=' . $row->id;
		}
		Pathway::append(
			Lang::txt(strtoupper($this->_name) . '_' . strtoupper($this->_task)),
			$p
		);
		if ($row->id)
		{
			Pathway::append(
				stripslashes($row->title),
				'index.php?option=' . $this->_option . '&task=details&id=' . $row->id
			);
		}

		// Output HTML
		$this->view->setLayout('default')->setName('edit');
		$this->view->option = $this->_option;
		$this->view->title = Lang::txt(strtoupper($this->_name)) . ': ' . Lang::txt(strtoupper($this->_name) . '_' . strtoupper($this->_task));
		$this->view->task = $this->_task;
		$this->view->config = $this->config;
		$this->view->row = $row;
		$this->view->fields = $fields;
		$this->view->times = $times;
		$this->view->lists = $lists;
		$this->view->gid = $this->gid;
		$this->view->admin = $this->_authorize();
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}

	/**
	 * Delete an event
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		// Incoming
		$id = Request::getInt('id', 0, 'request');

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
			return;
		}

		// Load event object
		$event = new Event($this->database);
		$event->load($id);

		// Are they authorized to delete this event? Do they own it? Own it!
		if (!$this->_authorize($event->created_by)
			&& !(User::get('id') == $event->created_by))
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
			return;
		}

		$event->state = 0; //unpublish the event
		$event->store();

		// Delete the event
		/* [!] No! Don't! True record deletion should only occur on the amdin side! - zooley 10/2013
		$event->delete($id);

		// Delete any associated pages
		$ep = new Page($this->database);
		$ep->deletePages($id);

		// Delete any associated respondents
		$er = new Respondent(array());
		$er->deleteRespondents($id);

		// Delete tags on this event
		$rt = new EventsModelTags($id);
		$rt->removeAll();

		// Load the event's category and update the count
		$category = new Category($this->database);
		$category->updateCount($event->catid);
		*/

		// E-mail subject line
		$subject  = '[' . Config::get('sitename') . ' ' . Lang::txt('EVENTS') . '] - ' . Lang::txt('EVENTS_EVENT_DELETED');

		// Build the message to be e-mailed
		$eview = new View(array(
			'name'   => 'emails',
			'layout' => 'deleted'
		));
		$eview->option = $this->_option;
		$eview->sitename = Config::get('sitename');
		$eview->user = User::getRoot();
		$eview->event = $event;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Send the e-mail
		$this->_sendMail(Config::get('sitename'), Config::get('mailfrom'), $subject, $message);

		// Go back to the default front page
		App::redirect(Route::url('index.php?option=' . $this->_option));
	}

	/**
	 * Save an event
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}


		// good ol' form validation
		Request::checkToken();
		Request::checkHoneypot() or die('Invalid Field Data Detected. Please try again.');

		$offset = $this->offset;

		// Incoming
		$start_time = Request::getVar('start_time', '08:00', 'post');
		$start_time = ($start_time) ? $start_time : '08:00';
		$start_pm   = Request::getInt('start_pm', 0, 'post');
		$end_time   = Request::getVar('end_time', '17:00', 'post');
		$end_time   = ($end_time) ? $end_time : '17:00';
		$end_pm     = Request::getInt('end_pm', 0, 'post');
		$time_zone	= Request::getVar('time_zone', -5, 'post');
		$tags       = Request::getVar('tags', '', 'post');

		// Bind the posted data to an event object
		$row = new Event($this->database);
		if (!$row->bind($_POST))
		{
			throw new Exception($row->getError(), 500);
		}

		// New entry or existing?
		if ($row->id)
		{
			$state = 'edit';

			// Existing - update modified info
			$row->modified = strftime("%Y-%m-%d %H:%M:%S", time()+($offset*60*60));
			if (User::get('id'))
			{
				$row->modified_by = User::get('id');
			}
		}
		else
		{
			$state = 'add';

			// New - set created info
			$row->created = strftime("%Y-%m-%d %H:%M:%S", time()+($offset*60*60));
			if (User::get('id'))
			{
				$row->created_by = User::get('id');
			}
		}

		// Set some fields and do some cleanup work
		if ($row->catid)
		{
			$row->catid = intval($row->catid);
		}

		//$row->title = htmlentities($row->title);

		$row->content = $_POST['econtent'];
		$row->content = \Hubzero\Utility\Sanitize::clean($row->content);

		// Get the custom fields defined in the events configuration
		if (isset($_POST['fields']))
		{
			$fields = $_POST['fields'];
			$fields = array_map('trim', $fields);

			// Wrap up the content of the field and attach it to the event content
			$fs = $this->config->fields;
			foreach ($fields as $param=>$value)
			{
				if (trim($value) != '')
				{
					$row->content .= '<ef:' . $param . '>' . $this->_clean($value) . '</ef:' . $param . '>';
				}
				else
				{
					foreach ($fs as $f)
					{
						if ($f[0] == $param && end($f) == 1)
						{
							throw new Exception(Lang::txt('EVENTS_REQUIRED_FIELD_CHECK', $f[1]), 500);
						}
					}
				}
			}
		}

		// Clean adresse
		$row->adresse_info = $this->_clean($row->adresse_info);

		// Clean contact
		$row->contact_info = $this->_clean($row->contact_info);

		// Clean extra
		$row->extra_info = $this->_clean($row->extra_info);

		// Prepend http:// to URLs without it
		if ($row->extra_info != NULL)
		{
			if ((substr($row->extra_info, 0, 7) != 'http://') && (substr($row->extra_info, 0, 8) != 'https://'))
			{
				$row->extra_info = 'http://' . $row->extra_info;
			}
		}

		// Reformat the time into 24hr format if necessary
		if ($this->config->getCfg('calUseStdTime') =='YES')
		{
			list($hrs, $mins) = explode(':', $start_time);
			$hrs = intval($hrs);
			$mins = intval($mins);
			if ($hrs != 12 && $start_pm) $hrs += 12;
			else if ($hrs == 12 && !$start_pm) $hrs = 0;
			if ($hrs < 10) $hrs = '0' . $hrs;
			if ($mins < 10) $mins = '0' . $mins;
			$start_time = $hrs . ':' . $mins;

			list($hrs, $mins) = explode(':', $end_time);
			$hrs = intval($hrs);
			$mins = intval($mins);
			if ($hrs!= 12 && $end_pm) $hrs += 12;
			else if ($hrs == 12 && !$end_pm) $hrs = 0;
			if ($hrs < 10) $hrs = '0' . $hrs;
			if ($mins < 10) $mins = '0' . $mins;
			$end_time = $hrs . ':' . $mins;
		}

		// hack to fix where timezones cant be found by offset int
		// really need to figure datetimes out
		switch ($row->time_zone)
		{
			case -12:    $tz = 'Pacific/Kwajalein';      break;
			case -9.5:   $tz = 'Pacific/Marquesa';       break;
			case -3.5:   $tz = 'Canada/Newfoundland';    break;
			case -2:     $tz = 'America/Noronha';        break;
			case 3.5:    $tz = 'Asia/Tehran';            break;
			case 4.5:    $tz = 'Asia/Kabul';             break;
			case 6:      $tz = 'Asia/Dhaka';             break;
			case 6.5:    $tz = 'Asia/Rangoon';           break;
			case 8.75:   $tz = 'Asia/Shanghai';          break;
			case 9.5:    $tz = 'Australia/Adelaide';     break;
			case 11:     $tz = 'Asia/Vladivostok';       break;
			case 11.5:   $tz = 'Asia/Vladivostok';       break;
			case 13:     $tz = 'Pacific/Tongatapu';      break;
			case 14:     $tz = 'Pacific/Kiritimati';     break;
			default:     $tz = timezone_name_from_abbr('',$row->time_zone*3600, NULL);
		}

		// create publish up date time string
		$rpup = $row->publish_up;
		$publishtime = date('Y-m-d 00:00:00');
		if ($row->publish_up)
		{
			$publishtime = $row->publish_up . ' ' . $start_time . ':00';
			$row->publish_up = \Date::of($publishtime)->toSql();
		}

		// create publish down date/time string
		$publishtime = date('Y-m-d 00:00:00');
		if ($row->publish_down)
		{
			$publishtime = $row->publish_down . ' ' . $end_time . ':00';
			$row->publish_down = \Date::of($publishtime)->toSql();
		}

		// Always unpublish if no Publisher otherwise publish automatically
		if ($this->config->getCfg('adminlevel'))
		{
			$row->state = 0;
		}
		else
		{
			$row->state = 1;
		}

		$row->state = 1;

		// Verify that the event doesn't start after it ends or ends before it starts.
		$pubdow = strtotime($row->publish_down);
		$pubup = strtotime($row->publish_up);
		if ($pubdow <= $pubup)
		{
			// Set the error message
			$this->setError(Lang::txt('EVENTS_EVENT_MUST_END_AFTER_START'));
			// Fall through to the edit view
			$this->editTask($row);
			return;
		}

		//set the scope to be regular events
		$row->scope = 'event';

		if (!$row->check())
		{
			// Set the error message
			$this->setError($row->getError());
			$this->tags = $tags;

			// Fall through to the edit view
			$this->editTask($row);
			return;
		}
		if (!$row->store())
		{
			// Set the error message
			$this->setError($row->getError());
			$this->tags = $tags;
			// Fall through to the edit view
			$this->editTask($row);

			return;
		}
		$row->checkin();

		// Save the tags
		$rt = new Tags($row->id);
		$rt->setTags($tags, User::get('id'));

		// Build the message to be e-mailed
		if ($state == 'add')
		{
			$subject  = '[' . Config::get('sitename') . ' ' . Lang::txt('EVENTS_CAL_LANG_CAL_TITLE') . '] - ' . Lang::txt('EVENTS_CAL_LANG_MAIL_ADDED');

			$eview = new View(array('name'=>'emails','layout'=>'created'));
		}
		else
		{
			$subject  = '[' . Config::get('sitename') . ' ' . Lang::txt('EVENTS_CAL_LANG_CAL_TITLE') . '] - ' . Lang::txt('EVENTS_CAL_LANG_MAIL_ADDED');

			$eview = new View(array('name'=>'emails','layout'=>'edited'));
		}
		$eview->option = $this->_option;
		$eview->sitename = Config::get('sitename');
		$eview->user = User::getRoot();
		$eview->row = $row;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Send the e-mail
		$this->_sendMail(Config::get('sitename'), Config::get('mailfrom'), $subject, $message);

		// Redirect to the details page for the event we just created
		App::redirect(Route::url('index.php?option=' . $this->_option . '&task=details&id=' . $row->id));
	}

	/**
	 * Send an email
	 *
	 * @param      array &$hub Parameter description (if any) ...
	 * @param      unknown $email Parameter description (if any) ...
	 * @param      unknown $subject Parameter description (if any) ...
	 * @param      unknown $message Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	private function _sendEmail(&$hub, $email, $subject, $message)
	{
		if ($hub)
		{
			$contact_email = $hub['email'];
			$contact_name  = $hub['name'];

			$args = "-f '" . $contact_email . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=iso-8859-1\n";
			$headers .= 'From: ' . $contact_name .' <' . $contact_email . ">\n";
			$headers .= 'Reply-To: ' . $contact_name .' <' . $contact_email . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: '.  Config::get('sitename') ."\n";

			if (mail($email, $subject, $message, $headers, $args))
			{
				return(1);
			}
		}
		return(0);
	}

	/**
	 * Check if an email address is valid
	 *
	 * @param      string $email Email address to check
	 * @return     integer 1 = valid, 0 = invalid
	 */
	private function _validEmail($email)
	{
		if (preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email))
		{
			return(1);
		}
		else
		{
			return(0);
		}
	}

	/**
	 * Get all the events categories
	 *
	 * @return     array
	 */
	private function _getCategories()
	{
		$sql = "SELECT * FROM `#__categories` WHERE extension='" . $this->_option . "' AND published = '1' ORDER BY lft ASC";

		$this->database->setQuery($sql);
		$cats = $this->database->loadObjectList();

		$c = array();
		foreach ($cats as $cat)
		{
			$c[$cat->id] = $cat->title;
		}

		return $c;
	}

	/**
	 * Short description for 'parseTag'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $text Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function parseTag($text, $tag)
	{
		preg_match("#<ef:" . $tag . ">(.*?)</ef:" . $tag . ">#s", $text, $matches);
		if (count($matches) > 0)
		{
			$match = $matches[0];
			$match = str_replace('<ef:' . $tag . '>', '', $match);
			$match = str_replace('</ef:' . $tag . '>', '', $match);
		}
		else
		{
			$match = '';
		}
		return $match;
	}

	/**
	 * Short description for '_clean'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $string Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _clean($string)
	{
		if (get_magic_quotes_gpc())
		{
			$string = stripslashes($string);
		}

		// strip out any KL_PHP, script, style, HTML comments
		$string = preg_replace('/{kl_php}(.*?){\/kl_php}/s', '', $string);
		$string = preg_replace("'<head[^>]*?>.*?</head>'si", '', $string);
		$string = preg_replace("'<body[^>]*?>.*?</body>'si", '', $string);
		$string = preg_replace("'<style[^>]*>.*?</style>'si", '', $string);
		$string = preg_replace("'<script[^>]*>.*?</script>'si", '', $string);
		$string = preg_replace('/<!--.+?-->/', '', $string);

		$string = str_replace(array("&amp;","&lt;","&gt;"),array("&amp;amp;","&amp;lt;","&amp;gt;",),$string);
		// fix &entitiy\n;

		$string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', "$1;", $string);
		$string = preg_replace('#(&\#x*)([0-9A-F]+);*#iu', "$1$2;", $string);
		$string = html_entity_decode($string, ENT_COMPAT, "UTF-8");

		// remove any attribute starting with "on" or xmlns
		$string = preg_replace('#(<[^>]+[\x00-\x20\"\'])(on|xmlns)[^>]*>#iUu', "$1>", $string);
		// remove javascript: and vbscript: protocol
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $string);
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $string);
		//<span style="width: expression(alert('Ping!'));"></span>
		// only works in ie...
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*expression[\x00-\x20]*\([^>]*>#iU', "$1>", $string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*behaviour[\x00-\x20]*\([^>]*>#iU', "$1>", $string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*>#iUu', "$1>", $string);
		//remove namespaced elements (we do not need them...)
		$string = preg_replace('#</*\w+:\w[^>]*>#i',"",$string);
		//remove really unwanted tags
		do {
			$oldstring = $string;
			$string = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|input|select|textarea|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', '', $string);
		} while ($oldstring != $string);

		return $string;
	}

	/**
	 * Short description for '_sendMail'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $name Parameter description (if any) ...
	 * @param      string $email Parameter description (if any) ...
	 * @param      unknown $subject Parameter description (if any) ...
	 * @param      unknown $message Parameter description (if any) ...
	 * @return     void
	 */
	private function _sendMail($name, $email, $subject, $message)
	{
		$name .= ' ' . Lang::txt('EVENTS_ADMINISTRATOR');

		$headers  = "";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "From: " . $name . " <" . $email . ">\r\n";
		$headers .= "Reply-To: <" . $email . ">\r\n";
		$headers .= "X-Priority: 3\r\n";
		$headers .= "X-MSMail-Priority: Low\r\n";
		$headers .= "X-Mailer: Joomla 1.5\r\n";

		@mail($email, $subject, $message, $headers);
	}

	/**
	 * Short description for '_authorize'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	protected function _authorize($id='')
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		// Check if they're a site admin from Joomla
		if (User::authorise('core.admin', $this->_option . '.component')
		 || User::authorise('core.manage', $this->_option . '.component'))
		{
			return true;
		}

		// Check against events configuration
		if (!$this->config->getCfg('adminlevel'))
		{
			if ($id && $id == User::get('id'))
			{
				return true;
			}
		}

		return false;
	}
}

