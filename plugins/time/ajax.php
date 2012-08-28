<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Plugin');

/**
 * Ajax plugin for time component
 *
 * DEPRECATION NOTICE: deprecate after all are transitioned from the old time dashboard widget
 *                     and after Mike update HUBman.  All internal ajax calls now point to API
 */
class plgTimeAjax extends Hubzero_Plugin
{

	/**
	 * @param  unknown &$subject Parameter description (if any) ...
	 * @param  unknown $config Parameter description (if any) ...
	 * @return void
	 */
	public function plgTimeAjax(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'time', 'ajax' );
		$this->_params = new JParameter( $this->_plugin->params );
		$this->loadLanguage();
	}

	/**
	 * @return array Return
	 */
	public function &onTimeAreas()
	{
		$area = array(
			'name'   => 'ajax',
			'title'  => JText::_('PLG_TIME_AJAX'),
			'return' => 'ajax'
		);

		return $area;
	}

	/**
	 * @param    string $action - plugin action to take (default 'view')
	 * @param    string $option - component option
	 * @param    string $active - active tab
	 * @return   array Return   - $arr with HTML of current active plugin
	 */
	public function onTime($action='', $option, $active='')
	{
		// Get this area details
		$this_area = $this->onTimeAreas();

		// Check if the active tab is the current one, otherwise return
		if ($this_area['name'] != $active)
		{
			return;
		}

		// Set some values for use later
		$this->_option =  $option;
		$this->action  =  $action;
		$this->db      =  JFactory::getDBO();
		$this->juser   =  JFactory::getUser();

		// Include needed DB classes
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'tasks.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'hubs.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'records.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'contacts.php');

		switch ($action)
		{
			// AJAX methods
			case 'save.json':             $this->_save_json();            break;
			case 'tasks.json':            $this->_tasks_json();           break;
			case 'hubs.json':             $this->_hubs_json();            break;
			case 'hub.json':              $this->_hub_json();             break;
			case 'users.json':            $this->_users_json();           break;
			case 'records.json':          $this->_records_json();         break;
			case 'get_values.json':       $this->_get_values_json();      break;
			case 'report_records.json':   $this->_report_records_json();  break;
			case 'savecontact.json':      $this->_save_contact_json();    break;
		}
	}

	/**
	 * JSON save method
	 * 
	 * @return void
	 */
	private function _save_json()
	{
		// Make sure they are authenticated to save records
		if(!$this->authenticate())
		{
			// Not authorized
			header("HTTP/1.0 401 Unauthorized");
			exit;
		}

		// Incoming posted data (grab individually for added security)
		$record = array();
		$record['task_id']     = JRequest::getInt('task_id');
		$record['time']        = JRequest::getCmd('time');
		$record['date']        = JRequest::getCmd('date');
		$record['description'] = JRequest::getString('description');

		$record = array_map('trim', $record);

		// Add user_id to array based on token
		$record['user_id'] = $this->user;

		// Create object and store new content
		$records = new TimeRecords($this->db);
		$records->save($record);

		header("HTTP/1.0 201 Created");
		exit;
	}

	/**
	 * JSON method for getting list of tasks per hub
	 * 
	 * @return void
	 */
	private function _tasks_json()
	{
		// Incoming posted data
		$hub_id  = JRequest::getInt('hid');
		$pactive = JRequest::getInt('pactive', 1);

		// Filters for the query
		$filters = array('limit'=>'100', 'start'=>'0', 'hub'=>$hub_id, 'active'=>$pactive);

		// Get list of tasks
		$task = new TimeTasks($this->db);
		$tasks = $task->getTasks($filters);

		// Output results in array of JSON objects
		$plist = array();
		if (count($tasks) > 0)
		{
			foreach ($tasks as $task)
			{
				$plist[] = '{"objValue":"'.htmlentities(stripslashes($task->id), ENT_QUOTES).
							'","objText":"'.htmlentities(stripslashes($task->name), ENT_QUOTES).'"}';
			}
		}

		// Echo out the results
		echo '['.implode(',',$plist).']';

		header("HTTP/1.0 200 OK");
		exit;
	}

	/**
	 * JSON method for getting list of hubs
	 * 
	 * @return void
	 */
	private function _hubs_json()
	{
		$filters = array('limit'=>'100', 'start'=>'0', 'active'=>'1');

		$hub = new TimeHubs($this->db);
		$hubs = $hub->getRecords($filters);

		// Output results in JSON format
		$hlist = array();
		if (count($hubs) > 0)
		{
			foreach ($hubs as $hub)
			{
				$hlist[] = '{"objValue":"'.$hub->id.'","objText":"'.$hub->name.'"}';
			}
		}

		echo '['.implode(',',$hlist).']';

		header("HTTP/1.0 200 OK");
		exit;
	}

	/**
	 * JSON method for getting list of users
	 * 
	 * @return void
	 */
	private function _users_json()
	{
		// Get group members
		$query  = "SELECT u.id, u.name";
		$query .= " FROM #__xgroups_members AS m";
		$query .= " LEFT JOIN #__xgroups AS g ON m.gidNumber = g.gidNumber";
		$query .= " LEFT JOIN #__users AS u ON u.id = m.uidNumber";
		$query .= " WHERE g.cn = 'time'";
		$query .= " ORDER BY u.name ASC";

		$this->db->setQuery($query);
		$users = $this->db->loadAssocList();

		// Output results in JSON format
		echo json_encode($users);

		header("HTTP/1.0 200 OK");
		exit;
	}

	/**
	 * JSON method for retrieving data about a hub
	 * 
	 * @return void
	 */
	private function _hub_json()
	{
		// Incoming posted data
		$hid = JRequest::getInt('hid');
		$hub = new TimeHubs($this->db);

		if($hub->load($hid))
		{
			$result = '{"hname":"'.$hub->name.
				'","hliaison":"'.$hub->liaison.
				'","hanniversarydate":"'.$hub->anniversary_date.
				'","hsupportlevel":"'.$hub->support_level.'"}';

			// Echo back the result
			echo $result;
		}
		else
		{
			// Add some sort of error
		}

		header("HTTP/1.0 200 OK");
		exit;
	}

	/**
	 * JSON method for getting records (for MMC)
	 * 
	 * @return void
	 */
	private function _records_json()
	{
		// Incoming posted data
		$pid       = JRequest::getInt('pid', 0);
		$startdate = JRequest::getVar('startdate', '2000-01-01');
		$enddate   = JRequest::getVar('enddate', '2100-01-01');
		$token     = JRequest::getVar('token');

		// Make sure they are authenticated to save records
		if(!empty($token) && !$this->authenticate())
		{
			// Not authorized
			header("HTTP/1.0 401 Unauthorized");
			exit;
		}
		elseif(empty($token) && !$this->_authorize())
		{
			// Not authorized
			header("HTTP/1.0 401 Unauthorized");
			exit;
		}

		// Filters for query
		$filters = array('limit'=>'1000', 'start'=>'0', 'pid'=>$pid, 'startdate'=>$startdate, 'enddate'=>$enddate);

		// Create object and get records
		$record  = new TimeRecords($this->db);
		$records = $record->getRecords($filters);

		// Output results in JSON format
		$rlist = array();
		if (count($records) > 0)
		{
			foreach ($records as $record)
			{
				$rlist[] = '{"rid":"'.$record->id.
					'","task":"'.htmlentities(stripslashes($record->pname), ENT_QUOTES).
					'","user":"'.htmlentities(stripslashes($record->uname), ENT_QUOTES).
					'","rtime":"'.htmlentities(stripslashes($record->time), ENT_QUOTES).
					'","rdate":"'.htmlentities(stripslashes($record->date), ENT_QUOTES).
					'","rdescription":"'.htmlentities(stripslashes($record->description), ENT_QUOTES).
					'"}';
			}
		}

		// Echo back results
		echo '['.implode(',',$rlist).']';

		header("HTTP/1.0 200 OK");
		exit;
	}

	/**
	 * JSON method for getting records for bills
	 * 
	 * @return void
	 */
	private function _report_records_json()
	{
		// Incoming posted data
		$pid       = JRequest::getInt('pid', 0);
		$startdate = JRequest::getVar('startdate', '2000-01-01');
		$enddate   = JRequest::getVar('enddate', '2100-01-01');
		$token     = JRequest::getVar('token');

		// Make sure they are authenticated and authorized to save records
		if(!empty($token) && !$this->authenticate())
		{
			// Not authorized
			header("HTTP/1.0 401 Unauthorized");
			exit;
		}
		elseif(empty($token) && !$this->_authorize())
		{
			// Not authorized
			header("HTTP/1.0 401 Unauthorized");
			exit;
		}

		// Filters for query
		$filters['limit']     = '1000';
		$filters['start']     = '0';
		$filters['pid']       = $pid;
		$filters['startdate'] = $startdate;
		$filters['enddate']   = $enddate;
		$filters['orderby']   = 'uname';
		$filters['orderdir']  = 'ASC';

		// Create object and get records
		$record  = new TimeRecords($this->db);
		$records = $record->getRecords($filters);

		// Get users involved in these records
		$users = array();

		// Put those users into an array
		foreach($records as $record)
		{
			$users[] = $record->uid;
		}

		// Get only the unique users from the array
		$users = array_unique($users);

		// Placeholder for our master list array
		$masterlist = array();

		// First make sure we have at least one record
		if (count($records) > 0)
		{
			// Start by looping through the users
			foreach ($users as $user)
			{
				// Placeholder for our records array
				$rlist = array();

				// Then loop through the records
				foreach ($records as $record)
				{
					// If the record belongs to the current user
					if ($record->uid == $user)
					{
						$rlist[] = '{"rid":'.json_encode($record->id).
							',"task":'.json_encode(htmlentities(stripslashes($record->pname), ENT_QUOTES)).
							',"user":'.json_encode(htmlentities(stripslashes($record->uname), ENT_QUOTES)).
							',"rtime":'.json_encode(htmlentities(stripslashes($record->time), ENT_QUOTES)).
							',"rdate":'.json_encode(htmlentities(stripslashes($record->date), ENT_QUOTES)).
							',"rdescription":'.json_encode(htmlentities(stripslashes($record->description), ENT_QUOTES)).
							'}';
					}
				}
				// Create master list of records array per user
				$masterlist[] = '[['.$user.'],['.implode(',',$rlist).']]';
			}
		}

		// Echo back results
		echo '['.implode(',',$masterlist).']';

		header("HTTP/1.0 200 OK");
		exit;
	}

	/**
	 * JSON method for getting possible unique values based on table and column
	 * 
	 * @return void
	 */
	private function _get_values_json()
	{
		// Get table and column values
		$table  = JRequest::getVar('table', '');
		$column = JRequest::getVar('column', '');

		// Make sure those values haven't been tampered with
		$acceptable = array('time_tasks', 'time_records');
		if(!in_array($table, $acceptable))
		{
			echo json_encode(array("error" => "not authorized"));

			header("HTTP/1.0 401 Unauthorized");
			exit;
		}

		// Get group members
		$query  = "SELECT DISTINCT(" . $column . ") as val";
		$query .= " FROM #__" . $table;
		$query .= " ORDER BY val ASC";

		$this->db->setQuery($query);
		$values = $this->db->loadAssocList();

		// Output results in JSON format
		echo json_encode($values);

		header("HTTP/1.0 200 OK");
		exit;
	}

	/**
	 * JSON save contact method
	 * 
	 * @return void
	 */
	private function _save_contact_json()
	{
		// Make sure they are authenticated to save contacts
		// @FIXME: add authentication check for this method (steal from the time controller)

		// Incoming posted data (grab individually for added security)
		$contact = array();
		$contact['name']     = JRequest::getString('name');
		$contact['phone']    = JRequest::getString('phone');
		$contact['email']    = JRequest::getString('email');
		$contact['role']     = JRequest::getString('role');
		$contact['hub_id']   = JRequest::getInt('hid');

		$contact = array_map('trim', $contact);

		// Create object and store new content
		$contacts = new TimeContacts($this->db);
		$contacts->save($contact);

		// Echo back contact id for use by the javascript
		echo '{"cid":'.$contacts->id.'}';

		header("HTTP/1.0 201 Created");
		exit;
	}

	/**
	 * Authentication - check token provided during post
	 * 
	 * @FIXME: replace this with HUBzero API when ready
	 *
	 * @return bool, true or false
	 */
	private function authenticate()
	{
		// Incoming posted data
		$token = JRequest::getVar('token');

		// Perform query for token in the database
		$query  = "SELECT user_id";
		$query .= " FROM #__time_auth_token";
		$query .= " WHERE token='".$token."'";

		$this->db->setQuery($query);
		$user_id = $this->db->loadResult();

		// If the user exists, return true
		if ($user_id)
		{
			$this->user = $user_id;
			return true;
		}

		// Token not found, return false (i.e. not authorized)
		return false;
	}

	/**
	 * Authorize current user
	 * 
	 * @return true or false
	 */
	protected function _authorize()
	{
		// @FIXME: add parameter for group access
		$accessgroup = isset($this->config->parameters['accessgroup']) ? trim($this->config->parameters['accessgroup']) : 'time';

		// Check if they're a member of admin group
		$ugs = Hubzero_User_Helper::getGroups($this->juser->get('id'));
		if ($ugs && count($ugs) > 0)
		{
			foreach ($ugs as $ug)
			{
				if ($ug->cn == $accessgroup)
				{
					return true;
				}
			}
		}

		return false;
	}
}