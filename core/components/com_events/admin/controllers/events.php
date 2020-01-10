<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Admin\Controllers;

use Components\Events\Tables\Configs;
use Components\Events\Tables\Event;
use Components\Events\Tables\Page;
use Components\Events\Tables\Respondent;
use Components\Events\Tables\Category;
use Components\Events\Models\Tags;
use Components\Events\Helpers\Html;
use Hubzero\Component\AdminController;
use Exception;

/**
 * Events controller for entries
 */
class Events extends AdminController
{
	/**
	 * Determine task and attempt to execute it
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->config = new Configs($this->database);
		$this->config->load();

		$tables = $this->database->getTableList();
		$table = $this->database->getPrefix() . 'events_respondent_race_rel';
		if (!in_array($table, $tables))
		{
			$this->database->setQuery("CREATE TABLE `#__events_respondent_race_rel` (
			  `respondent_id` int(11) default null,
			  `race` varchar(255) default null,
			  `tribal_affiliation` varchar(255) default null
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
			if (!$this->database->query())
			{
				echo $this->database->getErrorMsg();
				return false;
			}
		}

		/**
		 * Start day
		 */
		define('_CAL_CONF_STARDAY', $this->config->getCfg('starday'));

		/**
		 * Nav bar color
		 */
		define('_CAL_CONF_DEFCOLOR', $this->config->getCfg('navbarcolor'));

		parent::execute();
	}

	/**
	 * Display a list of entries
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']    = Request::getState(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$this->view->filters['start']    = Request::getState(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']   = urldecode(Request::getState(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		));
		$this->view->filters['catid']    = Request::getInt('catid', 0, '', 'int');
		$this->view->filters['scope_id'] = Request::getInt('group_id', 0, '', 'int');

		$ee = new Event($this->database);

		// Get a record count
		$this->view->total = $ee->getCount($this->view->filters);

		// Get records
		$this->view->rows = $ee->getRecords($this->view->filters);

		// Get list of categories
		$categories[] = \Html::select('option', '0', '- ' . Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ALLCAT'), 'value', 'text');
		$this->database->setQuery("SELECT id AS value, title AS text FROM `#__categories` WHERE extension='$this->_option'");

		$categories = array_merge($categories, $this->database->loadObjectList());
		$this->view->clist = \Html::select('genericlist', $categories, 'catid', 'class="inputbox"', 'value', 'text', $this->view->filters['catid'], false, false);

		//get list of groups
		$groups[] = \Html::select('option', '0', '- ' . Lang::txt('COM_EVENTS_ALL_GROUPS'), 'value', 'text');
		$sql = "SELECT DISTINCT(g.gidNumber) AS value, g.description AS text
				FROM `#__events` AS e, `#__xgroups` AS g
				WHERE e.scope='group'
				AND e.scope_id=g.gidNumber";
		$this->database->setQuery($sql);
		$groups = array_merge($groups, $this->database->loadObjectList());
		$this->view->glist = \Html::select('genericlist', $groups, 'group_id', 'class="inputbox"', 'value', 'text', $this->view->filters['scope_id'], false, false);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Show a form for adding an entry
	 *
	 * @return     void
	 */
	public function addpageTask()
	{
		$ids = Request::getArray('id', array(0));

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=pages&task=add&id[]=' . $ids[0], false)
		);
	}

	/**
	 * Show a form for adding an entry
	 *
	 * @return     void
	 */
	public function respondentsTask()
	{
		$ids = Request::getArray('id', array(0));

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=respondents&id[]=' . $ids[0], false)
		);
	}

	/**
	 * Show a form for adding an entry
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Display a form for editing an entry
	 *
	 * @return     void
	 */
	public function editTask()
	{
		Request::setVar('hidemainmenu', 1);

		// Instantiate a new view
		$this->view->setLayout('edit');

		$this->view->config = $this->config;

		$offset = Config::get('offset');

		// We need at least one category before we can proceed
		$cat = new Category($this->database);
		if ($cat->getCategoryCount($this->_option) < 1)
		{
			throw new Exception(Lang::txt('COM_EVENTS_LANG_NEED_CATEGORY'), 500);
		}

		// Incoming
		$id = Request::getArray('id', array(), 'request');
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : 0;
		}

		// Load the event object
		$this->view->row = new Event($this->database);
		$this->view->row->load($id);

		// Fail if checked out not by 'me'
		if ($this->view->row->checked_out
		 && $this->view->row->checked_out <> User::get('id'))
		{
			$this->_redirect = 'index.php?option=' . $this->_option;
			$this->_message = Lang::txt('COM_EVENTS_CAL_LANG_WARN_CHECKEDOUT');
		}

		$this->css('calendar.css');

		if ($this->view->row->id)
		{
			$this->view->row->checkout(User::get('id'));

			if (!$this->view->row->publish_down || trim($this->view->row->publish_down) == '0000-00-00 00:00:00')
			{
				$this->view->row->publish_down = Lang::txt('COM_EVENTS_CAL_LANG_NEVER');
			}

			$start_publish = Date::of($this->view->row->publish_up)->toLocal('Y-m-d');
			$start_time = Date::of($this->view->row->publish_up)->toLocal('H:i');

			// make sure the never setting doesn't throw an error
			if ($this->view->row->publish_down != 'Never')
			{
				$stop_publish = Date::of($this->view->row->publish_down)->toLocal('Y-m-d');
				$end_time = Date::of($this->view->row->publish_down)->toLocal('H:i');
			}
			else
			{
				$end_time = '00:00';
				$stop_publish = '0000-00-00';
			}
		}
		else
		{
			$this->view->row->state = 0;
			$this->view->row->time_zone = -5;
			$start_publish = Date::format('Y-m-d');
			$stop_publish = Date::format('Y-m-d');
			$start_time = "08:00";
			$end_time = "17:00";
		}

		// Get list of groups
		$this->database->setQuery("SELECT 0 AS value, 'Public' AS text");
		$groups = $this->database->loadObjectList();

		$this->view->fields = $this->config->getCfg('fields');
		if (!empty($this->view->fields))
		{
			for ($i=0, $n=count($this->view->fields); $i < $n; $i++)
			{
				// explore the text and pull out all matches
				array_push($this->view->fields[$i], $this->parseTag($this->view->row->content, $this->view->fields[$i][0]));
				// clean the original text of any matches
				$this->view->row->content = str_replace('<ef:' . $this->view->fields[$i][0] . '>' . end($this->view->fields[$i]) . '</ef:' . $this->view->fields[$i][0] . '>', '', $this->view->row->content);
			}
			$this->view->row->content = trim($this->view->row->content);
		}

		list($start_hrs, $start_mins) = explode(':', $start_time);
		list($end_hrs, $end_mins) = explode(':', $end_time);
		$start_pm = false;
		$end_pm = false;
		if ($this->config->getCfg('calUseStdTime') == 'YES')
		{
			$start_hrs = intval($start_hrs);
			if ($start_hrs >= 12)
			{
				$start_pm = true;
			}
			if ($start_hrs > 12)
			{
				$start_hrs -= 12;
			}
			else if ($start_hrs == 0)
			{
				$start_hrs = 12;
			}

			$end_hrs = intval($end_hrs);
			if ($end_hrs >= 12)
			{
				$end_pm = true;
			}
			if ($end_hrs > 12)
			{
				$end_hrs -= 12;
			}
			else if ($end_hrs == 0)
			{
				$end_hrs = 12;
			}
		}
		if (strlen($start_mins) == 1)
		{
			$start_mins = '0' . $start_mins;
		}
		if (strlen($start_hrs) == 1)
		{
			$start_hrs = '0' . $start_hrs;
		}
		$start_time = $start_hrs . ':' . $start_mins;
		if (strlen($end_mins) == 1)
		{
			$end_mins = '0' . $end_mins;
		}
		if (strlen($end_hrs) == 1)
		{
			$end_hrs = '0' . $end_hrs;
		}
		$end_time = $end_hrs . ':' . $end_mins;

		$this->view->times = array();
		$this->view->times['start_publish'] = $start_publish;
		$this->view->times['start_time'] = $start_time;
		$this->view->times['start_pm'] = $start_pm;
		$this->view->times['stop_publish'] = $stop_publish;
		$this->view->times['end_time'] = $end_time;
		$this->view->times['end_pm'] = $end_pm;

		// only load tags if the event exists already
		if ($this->view->row->id != null)
		{
			// Get tags on this event
			$rt = new Tags($this->view->row->id);
			$this->view->tags = $rt->render('string');
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
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
	private function parseTag($text, $tag)
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
	 * Cancel a task by redirecting to main page
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Incoming
		if (($id = Request::getInt('id', 0)))
		{
			// Check in the event
			$event = new Event($this->database);
			$event->load($id);
			$event->checkin();
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Save an entry
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		//$offset = Config::get('offset');

		// Bind the posted data to an event object
		$row = new Event($this->database);
		if (!$row->bind($_POST))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
		}

		// New entry or existing?
		if ($row->id)
		{
			// Existing - update modified info
			$row->modified = Date::toSql();
			$row->modified_by = User::get('id');
		}
		else
		{
			// New - set created info
			$row->created = Date::toSql();
			$row->created_by = User::get('id');
		}

		// Set some fields and do some cleanup work
		if ($row->catid)
		{
			$row->catid = intval($row->catid);
		}
		elseif (!$row->catid)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('EVENT_CAL_LANG_EVENT_REQUIRED'),
				'error'
			);
		}

		//$row->title = $this->view->escape($row->title);

		//$row->content = Request::getString('econtent', '', 'post');
		$row->content = $_POST['econtent'];
		$row->content = $this->_clean($row->content);

		// Get the custom fields defined in the events configuration
		$fields = Request::getArray('fields', array(), 'post');
		$fields = array_map('trim', $fields);
		// Wrap up the content of the field and attach it to the event content
		$fs = $this->config->fields;
		foreach ($fields as $param => $value)
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
						App::redirect(
							Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
							Lang::txt('COM_EVENTS_REQUIRED_FIELD_CHECK', $f[1]),
							'error'
						);
						exit();
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
		if ($row->extra_info != null)
		{
			if ((substr($row->extra_info, 0, 7) != 'http://')
			 && (substr($row->extra_info, 0, 8) != 'https://'))
			{
				$row->extra_info = 'http://' . $row->extra_info;
			}
		}

		// make sure we have a start date
		if (!$row->publish_up)
		{
			$row->publish_up = \Date::toSql();
		}

		// If this is a new event, publish it, otherwise retain its state
		if (!$row->id)
		{
			$row->state = 1;
		}

		// Get parameters
		$params = Request::getArray('params', '', 'post');
		if (is_array($params))
		{
			//email is reaquired
			$params['show_email'] = 1;
			$p = new \Hubzero\Config\Registry($params);
			$row->params = $p->toString();
		}

		if (!$row->check())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
		}
		if (!$row->store())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
		}
		$row->checkin();

		// Incoming tags
		$tags = Request::getString('tags', '', 'post');

		// Save the tags
		$rt = new Tags($row->id);
		$rt->setTags($tags, User::get('id'));

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_EVENTS_CAL_LANG_SAVED')
		);
	}

	/**
	 * Strip some unwanted content.
	 * This includes script tags, HTML comments, sctyle tags, etc.
	 *
	 * @param      string $string String to clean
	 * @return     string
	 */
	private function _clean($string)
	{
		return \Hubzero\Utility\Sanitize::clean($string);
	}

	/**
	 * Publish one or more entries
	 *
	 * @return     void
	 */
	public function publishTask()
	{
		// Incoming
		$ids = Request::getArray('id', array());
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		// Instantiate an event object
		$event = new Event($this->database);

		// Loop through the IDs and publish the event
		foreach ($ids as $id)
		{
			$event->publish($id);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_EVENTS_CAL_LANG_PUBLISHED')
		);
	}

	/**
	 * Unpublish one or more entries
	 *
	 * @return     void
	 */
	public function unpublishTask()
	{
		// Incoming
		$ids = Request::getArray('id', array());
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		// Instantiate an event object
		$event = new Event($this->database);

		// Loop through the IDs and unpublish the event
		foreach ($ids as $id)
		{
			$event->unpublish($id);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_EVENTS_CAL_LANG_UNPUBLISHED')
		);
	}

	/**
	 * Set the event type
	 *
	 * @return     void
	 */
	public function settypeTask()
	{
		// Incoming
		$ids = Request::getArray('id', array());
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		switch (strtolower(Request::getString('type', 'event')))
		{
			case 'announcement':
				$v = 1;
			break;

			case 'event':
			default:
				$v = 0;
			break;
		}

		// Loop through the IDs and publish the event
		foreach ($ids as $id)
		{
			// Instantiate an event object
			$event = new Event($this->database);
			$event->load($id);
			$event->announcement = $v;
			if (!$event->store())
			{
				throw new Exception($event->getError(), 500);
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Remove one or more entries for an event
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Incoming
		$ids = Request::getArray('id', array());
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		// Instantiate an event object
		$event = new Event($this->database);

		// Instantiate a page object
		$ep = new Page($this->database);

		// Instantiate a respondent object
		$er = new Respondent(array());

		// Loop through the IDs and unpublish the event
		foreach ($ids as $id)
		{
			// Instantiate an event tags object
			$rt = new Tags($id);
			// Delete tags on this event
			$rt->removeAll();

			// Delete the event
			$event->delete($id);

			// Delete any associated pages
			$ep->deletePages($id);

			// Delete any associated respondents
			$er->deleteRespondents($id);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_EVENTS_CAL_LANG_REMOVED')
		);
	}
}
