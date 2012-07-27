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
 * Reports plugin for time component
 */
class plgTimeReports extends Hubzero_Plugin
{

	/**
	 * @param  unknown &$subject Parameter description (if any) ...
	 * @param  unknown $config Parameter description (if any) ...
	 * @return void
	 */
	public function plgTimeReports(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'time', 'reports' );
		$this->_params = new JParameter( $this->_plugin->params );
		$this->loadLanguage();
	}

	/**
	 * @return array Return
	 */
	public function &onTimeAreas()
	{
		$area = array(
			'name'   => 'reports',
			'title'  => JText::_('PLG_TIME_REPORTS'),
			'return' => 'html'
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

		$return = 'html';

		// The output array we're returning
		$arr = array(
			'html'=>''
		);

		// Set some values for use later
		$this->_option   =  $option;
		$this->action    =  $action;
		$this->active    =  $active;
		$this->db        =  JFactory::getDBO();
		$this->juser     =& JFactory::getUser();
		$this->mainframe =& JFactory::getApplication();

		// Include needed DB classes
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'tasks.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'hubs.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'records.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'reports.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'html.php');

		// Add some styles to the view
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('time','reports');
		Hubzero_Document::addPluginScript('time','reports');

		// Only perform the following if this is the active tab/plugin
		if ($return == 'html') {
			switch ($action)
			{
				// Views
				case 'view':          $arr['html'] = $this->_view();           break;

				// Bills
				case 'createbill':    $arr['html'] = $this->_create_bill();    break;
				case 'viewbill':      $arr['html'] = $this->_view_bill();      break;
				case 'savebill':      $arr['html'] = $this->_save_bill();      break;
				case 'deletebill':    $arr['html'] = $this->_delete_bill();    break;
				case 'csvbill':       $arr['html'] = $this->_csv_bill();       break;

				// Default
				default:              $arr['html'] = $this->_view();           break;
			}
		}

		// Return the output
		return $arr;
	}

	//---------------------------------------------------------------------
	//	Views (default)
	//---------------------------------------------------------------------

	/**
	 * Primary/default view function
	 * 
	 * @return object Return
	 */
	private function _view()
	{
		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'time',
				'element'=>'reports',
				'name'=>'view'
			)
		);

		// Build list of reports for the current user
		$view->rlist = $this->buildReportsList();

		// Set a few things for the vew
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
		$view->option        = $this->_option;
		$view->mainframe     = $this->mainframe;
		$view->active        = $this->active;

		return $view->loadTemplate();
	}

	//---------------------------------------------------------------------
	//	Bill (report)
	//---------------------------------------------------------------------

	/**
	 * Create a custom bill
	 * 
	 * @return object Return
	 */
	private function _create_bill()
	{
		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder' =>'time',
				'element'=>'reports',
				'name'   =>'bill',
				'layout' =>'create_bill'
			)
		);

		// Generate hubs list
		$view->hlist = TimeHTML::buildHubsList($this->active, 0, 0);

		// Set a few things for the vew
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
		$view->option        = $this->_option;
		$view->mainframe     = $this->mainframe;
		$view->active        = $this->active;
		$view->action        = $this->action;

		return $view->loadTemplate();
	}

	/**
	 * View customized bill
	 * 
	 * @return void
	 */
	private function _view_bill()
	{
		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder' =>'time',
				'element'=>'reports',
				'name'   =>'bill',
				'layout' =>'view_bill'
			)
		);

		// Get the bill id from the request
		$bid = JRequest::getInt('id');

		// Redirect back to reports page if id was not given
		if(empty($bid))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&active=reports'),
				JText::_('PLG_TIME_REPORTS_NO_REPORT_SELECTED'),
				'warning'
			);
		}

		// Create needed objects
		$hub    = new TimeHubs($this->db);
		$report = new TimeReports($this->db);
		$recs   = new TimeRecords($this->db);

		// Get the range of record IDs associated with the current report and fetch those records
		$idRange = implode(",", $report->getRecordIDs($filters = array('report_id'=>$bid)));
		$records = $recs->getRecords($filters = array('start'=>0, 'limit'=>1000, 'id_range'=>$idRange, 'orderby'=>'uname', 'orderdir'=>'ASC'));

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
						$rlist[] = $record;
					}
				}
				// Create master list of records array per user
				$masterlist[$user]['name']    = $rlist[0]->uname;
				$masterlist[$user]['total']   = $recs->getTotalHours($filters = array('id_range'=>$idRange, 'user_id'=>$user));
				$masterlist[$user]['records'] = $rlist;
			}
		}

		// Pass our list to the view
		$view->masterlist = $masterlist;

		// Also load the current report for additional details needed in the view
		$report->load($bid);
		$view->report = $report;

		// Redirect back to reports page if current user doesn't own this report
		if($this->juser->get('id') != $report->user_id)
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&active=reports'),
				JText::_('PLG_TIME_REPORTS_NOT_YOUR_REPORT'),
				'warning'
			);
		}

		// Finally, grab the total hours and hubname for the report
		$view->totalHours = $recs->getTotalHours($filters = array('id_range'=>$idRange));
		$view->hubname    = $hub->getHubNameByReportId($report->id);

		// Set a few things for the vew
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
		$view->option        = $this->_option;
		$view->mainframe     = $this->mainframe;
		$view->active        = $this->active;
		$view->action        = $this->action;

		return $view->loadTemplate();
	}

	/**
	 * Save bill
	 * 
	 * @return void
	 */
	private function _save_bill()
	{
		$results = JRequest::getVar('results');
		$ids     = explode(",", $results);
		$ids     = array_map('trim', $ids);
		$ids     = array_filter($ids);

		// Make sure rows were selected to be included in the bill
		if(empty($ids) || $ids[0] == 0)
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&active=reports&action=createbill'),
				JText::_('PLG_TIME_REPORTS_NO_RECORDS_SELECTED'),
				'warning'
			);
		}

		// Create a report object and save
		$report  = array('report_type'=>'bill', 'user_id'=>$this->juser->get('id'), 'time_stamp'=>date("Y-m-d H:i:s"));
		$reports = new TimeReports($this->db);

		// Save the report
		$reports->save($report);

		// Loop through the ids, updating the billing status for each record
		foreach($ids as $id)
		{
			// Create object and update billed status
			$record = new TimeRecords($this->db);
			$record->load($id);

			$record->billed = 1;
			if (!$record->store()) 
			{
				JError::raiseError(500, $record->getError());
				return;
			}

			// Also, add a new entry in the assoc table
			// @FIXME: where should this really go?
			$query  = "INSERT INTO #__time_reports_records_assoc (`report_id`, `record_id`)";
			$query .= " VALUES ('".$reports->id."', '".$id."')";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&active=reports&action=viewbill&id=' . $reports->id),
			JText::_('PLG_TIME_REPORTS_SAVE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Delete bill
	 * 
	 * @return void
	 */
	private function _delete_bill()
	{
		// Might not ever use this
	}

	/**
	 * Create CSV download of report
	 * 
	 * @return void
	 */
	private function _csv_bill()
	{
		// Get the bill id from the request
		$bid = JRequest::getInt('id');

		// Create needed objects
		$hub     = new TimeHubs($this->db);
		$report  = new TimeReports($this->db);
		$records = new TimeRecords($this->db);

		// Get the range of record IDs associated with the current report and fetch those records
		$idRange = implode(",", $report->getRecordIDs($filters = array('report_id'=>$bid)));
		$recs    = $records->getRecords($filters = array('start'=>0, 'limit'=>1000, 'id_range'=>$idRange));

		// Also load the current report for additional details
		$report->load($bid);

		// Finally, grab the total hours and hubname for the report
		$totalHours = $records->getTotalHours($filters = array('id_range'=>$idRange));
		$hubname    = $hub->getHubNameByReportId($report->id);

		// Grab just the date from the time_stamp (for the filename)
		$date = explode(" ", $report->time_stamp);

		// Set content type headers
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename={$hubname}_{$date[0]}.csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		foreach($recs as $record)
		{
			$row   = array();
			$row[] = $record->id;
			$row[] = $record->uname;
			$row[] = $record->time;
			$row[] = $record->date;
			$row[] = $record->pname;
			$row[] = $record->description;
			echo implode(',', $row) . "\n";
		}

		exit;
	}

	/**
	 * Set redirect
	 * 
	 * @return void
	 */
	private function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}

	/**
	 * Build a select list of reports
	 * 
	 * @return $rlist
	 */
	public function buildReportsList()
	{
		$rlist = array();
		$filters = array('distinct'=>true, 'user_id'=>$this->juser->get('id'));

		$hub     = new TimeHubs($this->db);
		$report  = new TimeReports($this->db);
		$reports = $report->getRecords($filters);

		// Go through all the reports and add a select option for each
		$options[] = JHTML::_('select.option', '', JText::_('PLG_TIME_REPORTS_SELECT_REPORT'), 'value', 'text');
		foreach($reports as $report) 
		{
			$hubname   = $hub->getHubNameByReportId($report->id);
			$date      = explode(" ", $report->time_stamp);
			$display   = $hubname." ".ucfirst($report->report_type)." (".$date[0].")";
			$options[] = JHTML::_('select.option', $report->id, JText::_($display), 'value', 'text');
		}
		$rlist = JHTML::_('select.genericlist', $options, 'id', '', 'value', 'text', '', 'report', false, false);

		return $rlist;
	}
}