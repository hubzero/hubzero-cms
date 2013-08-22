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
 * Hubs plugin for time component
 */
class plgTimeHubs extends Hubzero_Plugin
{

	/**
	 * @param  unknown &$subject Parameter description (if any) ...
	 * @param  unknown $config Parameter description (if any) ...
	 * @return void
	 */
	public function plgTimeHubs(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'time', 'hubs' );
		$this->_params = new JParameter( $this->_plugin->params );
		$this->loadLanguage();
	}

	/**
	 * @return array Return
	 */
	public function &onTimeAreas()
	{
		$area = array(
			'name'   => 'hubs',
			'title'  => JText::_('PLG_TIME_HUBS'),
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

		// Include needed DB classes and helpers
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'html.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'hubs.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'tasks.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'records.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'contacts.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'filters.php');

		// Add some styles to the view
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('time','hubs');
		Hubzero_Document::addPluginScript('time','hubs');

		// Only perform the following if this is the active tab/plugin
		if ($return == 'html') {
			switch ($action)
			{
				// Views
				case 'edit':            $arr['html'] = $this->_edit();              break;
				case 'new':             $arr['html'] = $this->_edit();              break;
				case 'view':            $arr['html'] = $this->_view();              break;
				case 'readonly':        $arr['html'] = $this->_read_only();         break;

				// Data management
				case 'save':            $arr['html'] = $this->_save();              break;
				case 'delete':          $arr['html'] = $this->_delete();            break;

				// Contact management
				case 'savecontact':     $arr['html'] = $this->_save_contact();      break;
				case 'deletecontact':   $arr['html'] = $this->_delete_contact();    break;

				// Default
				default:                $arr['html'] = $this->_view();              break;
			}
		}

		// Return the output
		return $arr;
	}

	/**
	 * Primary/default view function
	 * 
	 * @return object Return
	 */
	private function _view()
	{
		// Instantiate hubs class
		$hubs = new TimeHubs($this->db);

		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'time',
				'element'=>'hubs',
				'name'=>'view'
			)
		);

		// Get the total number of hubs (for pagination)
		$view->total               = $hubs->getCount();
		$view->filters['start']    = (JRequest::getInt('start')) ? JRequest::getInt('start') : JRequest::getInt('limitstart', 0);
		$view->filters['limit']    = JRequest::getInt('limit', $this->mainframe->getUserState("$this->option.$this->active.limit"));
		$view->filters['orderby']  = JRequest::getVar('orderby', $this->mainframe->getUserState("$this->option.$this->active.orderby"));
		$view->filters['orderdir'] = JRequest::getVar('orderdir', $this->mainframe->getUserState("$this->option.$this->active.orderdir"));

		// Set sort order, sort direction, and start in session
		$this->mainframe->setUserState("$this->option.$this->active.start", $view->filters['start']);
		$this->mainframe->setUserState("$this->option.$this->active.limit", $view->filters['limit']);
		$this->mainframe->setUserState("$this->option.$this->active.orderby", $view->filters['orderby']);
		$this->mainframe->setUserState("$this->option.$this->active.orderdir", $view->filters['orderdir']);

		// Initiate pagination
		jimport('joomla.html.pagination');

		// Set the list limit to 10 by default, if not set otherwise
		$view->filters['limit'] = (empty($view->filters['limit'])) ? 10 : $view->filters['limit'];
		$pageNav                = new JPagination($view->total, $view->filters['start'], $view->filters['limit']);
		$view->pageNav          = $pageNav->getListFooter();

		// Get the hubs
		$view->hubs = $hubs->getRecords($view->filters);

		// Set a few things for the vew
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
		$view->option        = $this->_option;
		$view->mainframe     = $this->mainframe;
		$view->active        = $this->active;

		return $view->loadTemplate();
	}

	/**
	 * New/Edit function
	 * 
	 * @return object Return
	 */
	private function _edit($hub=null, $contacts=null)
	{
		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'time',
				'element'=>'hubs',
				'name'=>'edit'
			)
		);

		// If we already have a hub, use it, otherwise, instanciate a new object with the request variable
		if (is_object($hub))
		{
			// Use the prexisting object (i.e. we had an error when saving)
			$view->row = $hub;
		}
		else 
		{ // Create a new object (i.e. we're coming in clean)
			// Get the id if we're editing a hub
			$hid = JRequest::getInt('id');

			$hub = new TimeHubs($this->db);
			$hub->load($hid);
			$view->row = $hub;
		}

		// Check if we have a contacts array coming in - if so, use that
		if (is_array($contacts))
		{
			// Use the prexisting object (i.e. we had an error when saving)
			$view->contacts = $contacts;
		}
		else 
		{
			// Get the contacts for the hub (only if we're editing)
			$view->contacts = array();
			if(!empty($view->row->id))
			{
				$contacts          = new TimeContacts($this->db);
				$filters['hub_id'] = $view->row->id;
				$view->contacts    = $contacts->getRecords($filters);
			}
		}

		// Create support level list
		$view->slist = TimeHTML::buildSupportLevelList($view->row->support_level);

		// If viewing an entry from a page other than the first, take the user back to that page if they click "all xxx"
		$view->start = ($this->mainframe->getUserState("$this->option.$this->active.start") != 0) 
			? '&start='.$this->mainframe->getUserState("$this->option.$this->active.start") 
			: '';

		// Set a few things for the vew
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
		$view->option        = $this->_option;
		$view->action        = $this->action;

		return $view->loadTemplate();
	}

	/**
	 * View/read-only hub entry
	 * 
	 * @return object Return
	 */
	private function _read_only()
	{
		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'time',
				'element'=>'hubs',
				'name'=>'edit',
				'layout'=>'readonly'
			)
		);

		// Get the id if we're editing a hub
		$hid = JRequest::getInt('id');

		// Create a new object
		$hub = new TimeHubs($this->db);
		$hub->load($hid);
		$view->row = $hub;

		// Get the contacts for the hub
		$contacts          = new TimeContacts($this->db);
		$filters['hub_id'] = $view->row->id;
		$view->contacts    = $contacts->getRecords($filters);

		// Get the count of active tasks
		$tasks              = new TimeTasks($this->db);
		$tFilters['limit']  = 1000;
		$tFilters['start']  = 0;
		$tFilters['hub']    = $view->row->id;
		$tFilters['active'] = 1;

		$q['q'][0]['column'] = 'hub_id';
		$q['q'][0]['o']      = '=';
		$q['q'][0]['value']  = $view->row->id;
		$q['q'][1]['column'] = 'active';
		$q['q'][1]['o']      = '=';
		$q['q'][1]['value']  = 1;

		$view->activeTasks  = $tasks->getCount($q);

		// Get the summary hours for the hub
		$records          = new TimeRecords($this->db);
		$hours            = $records->getSummaryHoursByHub(1, $view->row->id);
		$view->totalHours = ($hours) ? $hours[0]->hours : 0;

		// Import the wiki parser
		ximport('Hubzero_Wiki_Parser');

		// Set up the wiki configuration
		$wikiconfig = array(
			'option'   => $this->option,
			'scope'    => 'time',
			'pagename' => 'hubs',
			'pageid'   => $view->row->id,
			'filepath' => '',
			'domain'   => $view->row->id
		);

		$p =& Hubzero_Wiki_Parser::getInstance();

		// Parse the notes for the view
		$view->row->notes = $p->parse("\n" . stripslashes($view->row->notes), $wikiconfig);

		// If viewing an entry from a page other than the first, take the user back to that page if they click "all xxx"
		$view->start = ($this->mainframe->getUserState("$this->option.$this->active.start") != 0) 
			? '&start='.$this->mainframe->getUserState("$this->option.$this->active.start") 
			: '';

		// Set a few things for the vew
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
		$view->option        = $this->_option;
		$view->action        = $this->action;

		return $view->loadTemplate();
	}	

	/**
	 * Save new hub and redirect to the hubs page
	 * 
	 * @return void
	 */
	private function _save()
	{
		// Incoming posted data
		$hub      = JRequest::getVar('hub', array(), 'post');
		$hub      = array_map('trim', $hub);
		$contacts = JRequest::getVar('contact', array(), 'post');

		// Create object(s)
		$hubs     = new TimeHubs($this->db);

		// Create variables to capture errors
		$has_errors       = false;
		$contactsObjArray = array();

		// Save the hub info
		if (!$hubs->save($hub))
		{
			// Something went wrong...return errors (probably from 'check')
			$this->addPluginMessage($hubs->getError(), 'error');

			// Set the flag for errors
			$has_errors = true;
		}

		// Save the contacts info
		foreach($contacts as $contact)
		{
			// Add the hub id to the contact array
			$contact['hub_id'] = $hubs->id;

			// Trim
			$contact = array_map('trim', $contact);

			// First check and make sure we don't save an empty contact
			if($contact['name'] == 'name' || $contact['phone'] == 'phone' || $contact['email'] == 'email' || $contact['role'] == 'role')
			{
				break;
			}

			// Create object
			$contactObj = new TimeContacts($this->db);

			// Save the contact info
			if(!$contactObj->save($contact));
			{
				// Something went wrong...return errors (probably from 'check')
				$this->addPluginMessage($contactObj->getError(), 'error');

				// Set the flag for errors
				// @FIXME: why isn't save working?  It's saving, but we're still getting here...
				//$has_errors = true;
			}

			// Add all contacts ojects to a new array to pass back to the edit view if necessary
			$contactsObjArray[] = $contactObj;
		}

		// If we had errors, redirect back to edit
		if($has_errors == true)
		{
			return $this->_edit($hubs, $contactsObjArray);
		}

		// If saving a hub from a page other than the first, take the user back to that page after saving
		$startnum = $this->mainframe->getUserState("$this->option.$this->active.start");
		$start    = ($startnum != 0) ? '&start='.$startnum : '';

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&active=hubs' . $start),
			JText::_('PLG_TIME_HUBS_SAVE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Delete hubs
	 * 
	 * @return void
	 */
	private function _delete()
	{
		// Incoming posted data
		$hub = JRequest::getInt('id');

		// Check if the hub has an active tasks
		$tasks = new TimeTasks($this->db);
		$filters = array('q' => array());
		$filters['q'][0] = array(
			'column' => 'hub_id',
			'o'      => '=',
			'value'  => $hub
		);
		$count = $tasks->getCount($filters);

		// If delete a record from a page other than the first, take the user back to that page after deletion
		$startnum = $this->mainframe->getUserState("$this->option.$this->active.start");
		$start = ($startnum != 0) ? '&start='.$startnum : '';

		// If there are active tasks, don't allow deletion
		if($count > 0)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&active=hubs&action=readonly&id=' . $hub),
				JText::_('PLG_TIME_HUBS_DELETE_HAS_ASSOCIATED_TASKS'),
				'warning'
			);
		}

		// Create object and load hub by id
		$hubs = new TimeHubs($this->db);
		$hubs->load($hub);

		// Get hub contacts
		$contacts          = new TimeContacts($this->db);
		$filters['hub_id'] = $hub;
		$contacts          = $contacts->getRecords($filters);

		// Delete contacts from the hub
		foreach($contacts as $contact)
		{
			$ct = new TimeContacts($this->db);
			$ct->load($contact->id);

			// @FIXME: add logic for displaying any errors!
			$ct->delete();
		}

		// Delete the hub
		// @FIXME: add logic for displaying any errors!
		$hubs->delete();

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&active=hubs' . $start),
			JText::_('PLG_TIME_HUBS_DELETE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Save new contact
	 * 
	 * @return void
	 */
	private function _save_contact()
	{
		// Maybe make this an ajax call for saving a new contact
	}

	/**
	 * Delete a contact
	 * 
	 * @return void
	 */
	private function _delete_contact()
	{
		// Incoming posted data
		$cid = JRequest::getInt('id');

		// Create object and load hub by id
		$contact = new TimeContacts($this->db);
		$contact->load($cid);

		// Get the hub id for the return
		$hid = $contact->hub_id;

		// Delete the hub
		// @FIXME: add logic for displaying any errors!
		$contact->delete();

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&active=hubs&action=edit&id=' . $hid),
			JText::_('PLG_TIME_HUBS_CONTACT_DELETE_SUCCESSFUL'),
			'passed'
		);
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
}