<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

class SupportController extends Hubzero_Controller
{
	public function execute()
	{
		// Get the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		$this->_task = JRequest::getVar( 'task', '' );
		
		switch ($this->_task) 
		{
			// Media/file handler
			case 'upload':     $this->upload();     break;
			
			// Categories
			/*case 'newcat':     $this->editcat();    break;
			case 'editcat':    $this->editcat();    break;
			case 'savecat':    $this->savecat();    break;
			case 'deletecat':  $this->deletecat();  break;
			case 'cancelcat':  $this->cancelcat();  break;
			case 'categories': $this->categories(); break;
			
			// Sections
			case 'newsec':     $this->editsec();    break;
			case 'editsec':    $this->editsec();    break;
			case 'savesec':    $this->savesec();    break;
			case 'deletesec':  $this->deletesec();  break;
			case 'cancelsec':  $this->cancelsec();  break;
			case 'sections':   $this->sections();   break;*/
			
			// Auto Group assignment based on tags
			case 'orderup':   $this->reorder();   break;
			case 'orderdown': $this->reorder();   break;
			case 'deletetg':  $this->deletetg();  break;
			case 'savetg':    $this->savetg();    break;
			case 'edittg':    $this->edittg();    break;
			case 'newtg':     $this->edittg();    break;
			case 'canceltg':  $this->canceltg();  break;
			case 'taggroup':  $this->taggroup();  break;
			
			// Abuse reports
			case 'abusereports':  $this->abusereports();  break;
			case 'abusereport':   $this->abusereport();   break;
			case 'releasereport': $this->releasereport(); break;
			case 'deletereport':  $this->deletereport();  break;
			
			// Ticket Messages
			case 'deletemsg':  $this->deletemsg();  break;
			case 'savemsg':    $this->savemsg();    break;
			case 'editmsg':    $this->editmsg();    break;
			case 'newmsg':     $this->editmsg();    break;
			case 'cancelmsg':  $this->cancelmsg();  break;
			case 'messages':   $this->messages();   break;
			
			// Ticket Resolutions
			case 'deleteres':  $this->deleteres();  break;
			case 'saveres':    $this->saveres();    break;
			case 'editres':    $this->editres();    break;
			case 'newres':     $this->editres();    break;
			case 'cancelres':  $this->cancelres();  break;
			case 'resolutions': $this->resolutions(); break;
			
			// Tickets
			case 'add':        $this->add();        break;
			case 'edit':       $this->edit();       break;
			case 'save':       $this->save();       break;
			case 'remove':     $this->remove();     break;
			case 'cancel':     $this->cancel();     break;
			case 'tickets':    $this->tickets();    break;
			
			case 'stats':      $this->stats();      break;
			
			default: $this->tickets(); break;
		}
	}
	
	//----------------------------------------------------------
	//  Views
	//----------------------------------------------------------

	protected function stats() 
	{
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.$this->_name.'.css');
		
		// Instantiate a new view
		$view = new JView( array('name'=>'stats') );
		$view->option = $this->_option;
		$view->title = JText::_(strtoupper($this->_name));
		
		$type = JRequest::getVar('type', 'submitted');
		$view->type = ($type == 'automatic') ? 1 : 0;
		
		$view->group = JRequest::getVar('group', '');
		
		// Set up some dates
		$jconfig =& JFactory::getConfig();
		$this->offset = $jconfig->getValue('config.offset');
		
		$year  = JRequest::getInt('year', strftime("%Y", time()+($this->offset*60*60)));
		$month = strftime("%m", time()+($this->offset*60*60));
		$day   = strftime("%d", time()+($this->offset*60*60));
		if ($day<="9"&ereg("(^[1-9]{1})",$day)) {
			$day = "0$day";
		}
		if ($month<="9"&ereg("(^[1-9]{1})",$month)) {
			$month = "0$month";
		}
		
		$startday = 0;
		$numday = ((date("w",mktime(0,0,0,$month,$day,$year))-$startday)%7);
		if ($numday == -1) {
			$numday = 6;
		} 
		$week_start = mktime(0, 0, 0, $month, ($day - $numday), $year );
		$week = strftime("%d", $week_start );
		
		$view->year = $year;
		$view->opened = array();
		$view->closed = array();
		
		$st = new SupportTicket( $this->database );
		
		// Get opened ticket information
		$view->opened['year'] = $st->getCountOfTicketsOpened($view->type, $year, '01', '01', $view->group);
		
		$view->opened['month'] = $st->getCountOfTicketsOpened($view->type, $year, $month, '01', $view->group);
		
		$view->opened['week'] = $st->getCountOfTicketsOpened($view->type, $year, $month, $week, $view->group);
		
		// Currently open tickets
		$view->opened['open'] = $st->getCountOfOpenTickets($view->type, false, $view->group);
		
		// Currently unassigned tickets
		$view->opened['unassigned'] = $st->getCountOfOpenTickets($view->type, true, $view->group);
		
		// Get closed ticket information
		$view->closed['year'] = $st->getCountOfTicketsClosed($view->type, $year, '01', '01', null, $view->group);
		
		$view->closed['month'] = $st->getCountOfTicketsClosed($view->type, $year, $month, '01', null, $view->group);
		
		$view->closed['week'] = $st->getCountOfTicketsClosed($view->type, $year, $month, $week, null, $view->group);
		
		// Users
		$query = "SELECT a.username, a.name, a.id"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
			. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id"	// map aro to group
			. "\n INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id"
			. "\n WHERE a.block = '0' AND g.id=25"
			. "\n ORDER BY a.name";
		$this->database->setQuery( $query );
		$view->users = $this->database->loadObjectList();
		if ($view->users) {
			foreach ($view->users as $user) 
			{
				$user->closed = array();
				
				// Get closed ticket information
				$user->closed['year'] = $st->getCountOfTicketsClosed($view->type, $year, '01', '01', $user->username, $view->group);

				$user->closed['month'] = $st->getCountOfTicketsClosed($view->type, $year, $month, '01', $user->username, $view->group);

				$user->closed['week'] = $st->getCountOfTicketsClosed($view->type, $year, $month, $week, $user->username, $view->group);
			}
		}
		
		// Get avgerage lifetime
		$view->lifetime = $st->getAverageLifeOfTicket($view->type, $year, $view->group);
		
		// Tickets over time
		$view->closedmonths = array();
		for ($i = 1; $i <= 12; $i++) 
		{
			$view->closedmonths[$i] = $st->getCountOfTicketsClosedInMonth($view->type, $year, sprintf( "%02d",$i), $view->group);
		}
		
		$view->openedmonths = array();
		for ($i = 1; $i <= 12; $i++) 
		{
			$view->openedmonths[$i] = $st->getCountOfTicketsOpenedInMonth($view->type, $year, sprintf( "%02d",$i), $view->group);
		}
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------

	protected function categories()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'categories') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.categories.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.categories.limitstart', 'limitstart', 0, 'int');
		
		$obj = new SupportCategory( $this->database );
		
		// Record count
		$view->total = $obj->getCount( $view->filters );
		
		// Fetch results
		$view->rows = $obj->getRecords( $view->filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//-----------

	protected function sections()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'sections') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.sections.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.sections.limitstart', 'limitstart', 0, 'int');

		$obj = new SupportSection( $this->database );
		
		// Record count
		$view->total = $obj->getCount( $view->filters );
		
		// Fetch results
		$view->rows = $obj->getRecords( $view->filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function resolutions()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'resolutions') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.resolutions.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.resolutions.limitstart', 'limitstart', 0, 'int');

		$obj = new SupportResolution( $this->database );
		
		// Record count
		$view->total = $obj->getCount( $view->filters );
		
		// Fetch results
		$view->rows = $obj->getRecords( $view->filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function tickets()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'tickets') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.$this->_name.'.css');

		// Get filters
		$view->filters = SupportUtilities::getFilters();

		$obj = new SupportTicket( $this->database );
		
		// Record count
		$view->total = $obj->getTicketsCount( $view->filters, true );
		
		// Fetch results
		$view->rows = $obj->getTickets( $view->filters, true );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function messages()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'messages') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filter = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.messages.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.messages.limitstart', 'limitstart', 0, 'int');

		$obj = new SupportMessage( $this->database );
		
		// Record count
		$view->total = $obj->getCount( $view->filters );
		
		// Fetch results
		$view->rows = $obj->getRecords( $view->filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------
	
	protected function add() 
	{
		$this->edit();
	}

	//-----------

	protected function edit() 
	{
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.$this->_name.'.css');
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		$filters = SupportUtilities::getFilters();

		// Initiate database class and load info
		$row = new SupportTicket( $this->database );
		$row->load( $id );

		if ($id) {
			// Editing an existing ticket

			// Get comments
			$sc = new SupportComment( $this->database );
			$comments = $sc->getComments( 'admin', $row->id );
			
			// Parse comment text for attachment tags
			$juri =& JURI::getInstance();
			$webpath = str_replace('/administrator/','/',$juri->base().$this->config->get('webpath').DS.$id);
			$webpath = str_replace('//','/',$webpath);
			if (isset( $_SERVER['HTTPS'] )) {
				$webpath = str_replace('http:','https:',$webpath);
			}
			if (!strstr( $webpath, '://' )) {
				$webpath = str_replace(':/','://',$webpath);
			}

			$attach = new SupportAttachment( $this->database );
			$attach->webpath = $webpath;
			$attach->uppath  = JPATH_ROOT.$this->config->get('webpath').DS.$id;
			$attach->output  = 'web';
			for ($i=0; $i < count($comments); $i++) 
			{
				$comment =& $comments[$i];
				$comment->comment = $attach->parse($comment->comment);
			}
			
			$row->statustext = SupportHtml::getStatus($row->status);
		} else {
			// Creating a new ticket
			$row->severity = 'normal';
			$row->status   = 0;
			$row->created  = date( 'Y-m-d H:i:s', time() );
			$row->login    = $this->juser->get('username');
			$row->name     = $this->juser->get('name');
			$row->email    = $this->juser->get('email');
			$row->cookies  = 1;
			
			ximport('Hubzero_Browser');
			$browser = new Hubzero_Browser();

			$row->os = $browser->getOs().' '.$browser->getOsVersion();
			$row->browser = $browser->getBrowser().' '.$browser->getBrowserVersion();
			
			$row->uas = JRequest::getVar('HTTP_USER_AGENT','','server');
			
			$row->ip = (getenv('HTTP_X_FORWARDED_FOR'))
		    		 ? getenv('HTTP_X_FORWARDED_FOR')
					 : getenv('REMOTE_ADDR');
			$row->hostname = gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server'));
			$row->section = 1;
			
			$comments = array();
		}
		
		// Do some text cleanup
		$row->summary = html_entity_decode(stripslashes($row->summary), ENT_COMPAT, 'UTF-8');
		$row->summary = str_replace('&quote;','&quot;',$row->summary);
		$row->summary = htmlentities($row->summary, ENT_COMPAT, 'UTF-8');
		
		$row->report  = html_entity_decode(stripslashes($row->report), ENT_COMPAT, 'UTF-8');
		$row->report  = str_replace('&quote;','&quot;',$row->report);
		$row->report  = str_replace("<br />","",$row->report);
		$row->report  = htmlentities($row->report, ENT_COMPAT, 'UTF-8');
		$row->report  = nl2br($row->report);
		$row->report  = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$row->report);
		$row->report  = str_replace("    ",'&nbsp;&nbsp;&nbsp;&nbsp;',$row->report);
		
		if ($id) {
			$row->report = $attach->parse($row->report);
		}
		
		$lists = array();
		
		// Get resolutions
		$sr = new SupportResolution( $this->database );
		$lists['resolutions'] = $sr->getResolutions();
		
		// Get messages
		$sm = new SupportMessage( $this->database );
		$lists['messages'] = $sm->getMessages();

		// Get sections
		//$ss = new SupportSection( $this->database );
		//$lists['sections'] = $ss->getSections();
		
		// Get categories
		//$sa = new SupportCategory( $this->database );
		//$lists['categories'] = $sa->getCategories( $row->section );
		
		// Get Tags
		$st = new SupportTags( $this->database );
		$lists['tags'] = $st->get_tag_string( $row->id, 0, 0, NULL, 0, 1 );
		$lists['tagcloud'] = $st->get_tag_cloud( 3, 1, $row->id );
		
		// Get severities
		$lists['severities'] = SupportUtilities::getSeverities($this->config->get('severities'));
		
		//$group = trim($this->config->get('group'));
		$group = trim($row->group);
		if ($group) {
			$lists['owner'] = $this->userSelectGroup( 'owner', $row->owner, 1, '', $group );
		} else {
			$lists['owner'] = $this->userSelect( 'owner', $row->owner, 1 );
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'ticket') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		$view->row = $row;
		$view->lists = $lists;
		$view->comments = $comments;
		$view->filters = $filters;
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//-----------

	protected function editcat() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'category') );
		$view->option = $this->_option;
		$view->task = $this->_task;
	
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Initiate database class and load info
		$view->row = new SupportCategory( $this->database );
		$view->row->load( $id );

		// Set action
		if (!$id) {
			$view->row->category = '';
			$view->row->section = 1;
		}
		
		// Get support sections
		$ss = new SupportSection( $this->database );
		$view->sections = $ss->getSections();

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function editsec() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'section') );
		$view->option = $this->_option;
		$view->task = $this->_task;
	
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Initiate database class and load info
		$view->row = new SupportSection( $this->database );
		$view->row->load( $id );

		// Set action
		if (!$id) {
			$view->row->section = '';
		}

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function editres() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'resolution') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Initiate database class and load info
		$view->row = new SupportResolution( $this->database );
		$view->row->load( $id );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function editmsg() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'message') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Initiate database class and load info
		$view->row = new SupportMessage( $this->database );
		$view->row->load( $id );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function savemsg() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
	
		// Trim and addslashes all posted items
		$msg = JRequest::getVar('msg', array(), 'post');
		$msg = array_map('trim',$msg);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportMessage( $this->database );
		if (!$row->bind( $msg )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
	
		// Code cleaner for xhtml transitional compliance
		$row->title   = trim($row->title);
		$row->message = trim($row->message);
		
		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=messages';
		$this->_message = JText::_('MESSAGE_SUCCESSFULLY_SAVED');
	}

	//-----------

	protected function savecat() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
	
		// Trim and addslashes all posted items
		$cat = JRequest::getVar('cat', array(), 'post');
		$cat = array_map('trim',$cat);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportCategory( $this->database );
		if (!$row->bind( $cat )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
	
		// Code cleaner for xhtml transitional compliance
		$row->category = trim($row->category);
		
		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=categories';
		$this->_message = JText::_('CATEGORY_SUCCESSFULLY_SAVED');
	}

	//-----------

	protected function savesec() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Trim and addslashes all posted items
		$sec = JRequest::getVar('sec', array(), 'post');
		$sec = array_map('trim',$sec);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportSection( $this->database );
		if (!$row->bind( $sec )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
	
		// Code cleaner for xhtml transitional compliance
		$row->section = trim($row->section);
		
		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=sections';
		$this->_message = JText::_('SECTION_SUCCESSFULLY_SAVED');
	}
	
	//-----------

	protected function saveres() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
	
		// Trim and addslashes all posted items
		$res = JRequest::getVar('res', array(), 'post');
		$res = array_map('trim',$res);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportResolution( $this->database );
		if (!$row->bind( $res )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
	
		// Code cleaner for xhtml transitional compliance
		$row->title = trim($row->title);
		if (!$row->alias) {
			$row->alias = preg_replace("/[^a-zA-Z0-9]/", "", $row->title);
			$row->alias = strtolower($row->alias);
		}
		
		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=resolutions';
		$this->_message = JText::_('RESOLUTION_SUCCESSFULLY_SAVED');
	}
	
	//-----------

	protected function save() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Instantiate the tagging class - we'll need this a few times
		$st = new SupportTags( $this->database );
		
		// Load the old ticket so we can compare for the changelog
		if ($id) {
			$old = new SupportTicket( $this->database );
			$old->load( $id );
			
			// Get Tags
			$oldtags = $st->get_tag_string( $id, 0, 0, NULL, 0, 1 );
		}
	
		// Trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportTicket( $this->database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		if (!$row->id && !trim($row->summary)) {
			$row->summary = substr($row->report, 0, 70);
			if (strlen($row->summary) >=70 ) {
				$row->summary .= '...';
			}
		}
		if (!$row->id && (!$row->created || $row->created == '0000-00-00 00:00:00')) {
			$row->created = date( "Y-m-d H:i:s" );
		}
		
		//$bits = explode(':',$row->category);
		//$row->category = end($bits);
		//$row->section = $bits[0];
		
		// Set the status of the ticket
		if ($row->resolved) {
			if ($row->resolved == 1) {
				// "waiting user response"
				$row->status = 1;
			} else {
				// If there's a resolution, close the ticket
				$row->status = 2;
			}
		} else {
			$row->status = 0;
		}
		
		// Set the status to just "open" if no owner and no resolution
		if (!$row->owner && !$row->resolved) {
			$row->status = 0;
		}
		
		// If status is "open" or "waiting", ensure the resolution is empty
		if ($row->status == 0 || $row->status == 1) {
			$row->resolved = '';
		}

		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		$row->load( $id );
		
		// Save the tags
		$tags = JRequest::getVar( 'tags', '', 'post' );

		$st->tag_object( $this->juser->get('id'), $row->id, $tags, 0, true );
		
		// We must have a ticket ID before we can do anything else
		if ($id) {
			// Incoming comment
			$comment = JRequest::getVar( 'comment', '' );
			$comment = TextFilter::cleanXss($comment);
			if ($comment) {
				// If a comment was posted to a closed ticket, re-open it.
				if ($old->status == 2 && $row->status == 2) {
					$row->status = 0;
					$row->resolved = '';
					$row->store();
				}
				// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
				$ccreated_by = JRequest::getVar( 'username', '' );
				if ($row->status == 1 && $ccreated_by == $row->login) {
					$row->status = 0;
					$row->resolved = '';
					$row->store();
				}
			}
			
			// Compare fields to find out what has changed for this ticket
			// and build a changelog
			$changelog = array();

			/*if ($row->section != $old->section) {
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_SECTION').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$old->section.'</em> to <em>'.$row->section.'</em></li>';
			}
			if ($row->category != $old->category) {
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_CATEGORY').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$old->category.'</em> to <em>'.$row->category.'</em></li>';
			}*/
			if ($tags != $oldtags) {
				$oldtags = (trim($oldtags) == '') ? JText::_('BLANK') : $oldtags;
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_TAGS').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$oldtags.'</em> to <em>'.$tags.'</em></li>';
			}
			if ($row->group != $old->group) {
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_GROUP').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$old->group.'</em> to <em>'.$row->group.'</em></li>';
			}
			if ($row->severity != $old->severity) {
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_SEVERITY').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$old->severity.'</em> to <em>'.$row->severity.'</em></li>';
			}
			if ($row->owner != $old->owner) {
				if ($old->owner == '') {
					$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_OWNER').'</strong> '.JText::_('TICKET_SET_TO').' <em>'.$row->owner.'</em></li>';
				} else {
					$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_OWNER').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$old->owner.'</em> to <em>'.$row->owner.'</em></li>';
				}
			}
			if ($row->resolved != $old->resolved) {
				if ($old->resolved == '') {
					$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_RESOLUTION').'</strong> '.JText::_('TICKET_SET_TO').' <em>'.$row->resolved.'</em></li>';
				} else {
					// This will happen if someone is reopening a closed ticket
					$row->resolved = ($row->resolved) ? $row->resolved : '[unresolved]';
					$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_RESOLUTION').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.$old->resolved.'</em> to <em>'.$row->resolved.'</em></li>';
				}
			}
			if ($row->status != $old->status) {
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_STATUS').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>'.SupportHtml::getStatus($old->status).'</em> to <em>'.SupportHtml::getStatus($row->status).'</em></li>';
			}

			// Were there any changes?
			$log = implode("\n",$changelog);
			if ($log != '') {
				$log = '<ul class="changelog">'."\n".$log.'</ul>'."\n";
			}
			
			$attachment = $this->upload( $row->id );
			$comment .= ($attachment) ? "\n\n".$attachment : '';
			
			// Create a new support comment object and populate it
			$rowc = new SupportComment( $this->database );
			$rowc->ticket     = $id;
			$rowc->comment    = nl2br($comment);
			$rowc->comment    = str_replace( '<br>', '<br />', $rowc->comment );
			$rowc->created    = date( 'Y-m-d H:i:s', time() );
			$rowc->created_by = JRequest::getVar( 'username', '' );
			$rowc->changelog  = $log;
			$rowc->access     = JRequest::getInt( 'access', 0 );

			if ($rowc->check()) {
				// If we're only recording a changelog, make it private
				if ($rowc->changelog && !$rowc->comment) {
					$rowc->access = 1;
				}
				// Save the data
				if (!$rowc->store()) {
					JError::raiseError( 500, $rowc->getError() );
					return;
				}
			
				// Only do the following if a comment was posted or ticket was reassigned
				// otherwise, we're only recording a changelog
				if ($comment || $row->owner != $old->owner) {
					$juri =& JURI::getInstance();
					$jconfig =& JFactory::getConfig();
					
					// Parse comments for attachments
					$attach = new SupportAttachment( $this->database );
					$attach->webpath = $juri->base().$this->config->get('webpath').DS.$id;
					$attach->uppath  = JPATH_ROOT.$this->config->get('webpath').DS.$id;
					$attach->output  = 'email';

					// Build e-mail components
					$admin_email = $jconfig->getValue('config.mailfrom');
					
					$subject = ucfirst($this->_name).', Ticket #'.$row->id.' comment '.md5($row->id);
					
					$from = array();
					$from['name']  = $jconfig->getValue('config.sitename').' '.ucfirst($this->_name);
					$from['email'] = $jconfig->getValue('config.mailfrom');
		
					$message  = '----------------------------'."\r\n";
					$message .= strtoupper(JText::_('TICKET')).': '.$row->id."\r\n";
					$message .= strtoupper(JText::_('TICKET_DETAILS_SUMMARY')).': '.stripslashes($row->summary)."\r\n";
					$message .= strtoupper(JText::_('TICKET_DETAILS_CREATED')).': '.$row->created."\r\n";
					$message .= strtoupper(JText::_('TICKET_DETAILS_CREATED_BY')).': '.$row->name;
					$message .= ($row->login) ? ' ('.$row->login.')'."\r\n" : "\r\n";
					$message .= '----------------------------'."\r\n\r\n";
					$message .= JText::sprintf('TICKET_EMAIL_COMMENT_POSTED',$row->id).': '.$rowc->created_by."\r\n";
					$message .= JText::_('TICKET_EMAIL_COMMENT_CREATED').': '.$rowc->created."\r\n\r\n";
					if ($row->owner != $old->owner) {
						if ($old->owner == '') {
							$message .= JText::_('TICKET_FIELD_OWNER').' '.JText::_('TICKET_SET_TO').' "'.$row->owner.'"'."\r\n\r\n";
						} else {
							$message .= JText::_('TICKET_FIELD_OWNER').' '.JText::_('TICKET_CHANGED_FROM').' "'.$old->owner.'" to "'.$row->owner.'"'."\r\n\r\n";
						}
					}
					$message .= $attach->parse($comment)."\r\n\r\n";

					//$sef = JRoute::_('index.php?option='.$this->_option.'&task=ticket&id='. $row->id);
					$sef = $this->_name.'/ticket/'. $row->id;
					if (substr($sef,0,1) == '/') {
						$sef = substr($sef,1,strlen($sef));
					}
					$base = $juri->base();
					if (substr($base,-14) == 'administrator/') {
						$base = substr($base,0,strlen($base)-14);
					}
					$message .= $base.$sef."\r\n";
						
					// An array for all the addresses to be e-mailed
					$emails = array();
					$emaillog = array();
					
					// Send e-mail to admin?
					JPluginHelper::importPlugin( 'xmessage' );
					$dispatcher =& JDispatcher::getInstance();
					
					// Send e-mail to ticket submitter?
					$email_submitter = JRequest::getInt( 'email_submitter', 0 );
					if ($email_submitter == 1) {
						// Is the comment private? If so, we do NOT send e-mail to the 
						// submitter regardless of the above setting
						if ($rowc->access != 1) {
							$zuser =& JUser::getInstance($row->login);
							// Make sure there even IS an e-mail and it's valid
							if (is_object($zuser) && $zuser->get('id')) {
								$type = 'support_reply_submitted';
								if ($row->status == 1) {
									$element = $row->id;
									$description = 'index.php?option='.$this->_option.'&task=ticket&id='.$row->id;
								} else {
									$element = null;
									$description = '';
									if ($row->status == 2) {
										$type = 'support_close_submitted';
									}
								}

								if (!$dispatcher->trigger( 'onSendMessage', array( $type, $subject, $message, $from, array($zuser->get('id')), $this->_option ))) {
									$this->setError( JText::_('Failed to message ticket submitter.') );
								} else {
									$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_SUBMITTER').' - '.$row->email.'</li>';
								}
							} else if ($row->email && SupportUtilities::check_validEmail($row->email)) {
								$emails[] = $row->email;
								$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_SUBMITTER').' - '.$row->email.'</li>';
							}
						}
					}
					
					// Send e-mail to ticket owner?
					$email_owner = JRequest::getInt( 'email_owner', 0 );
					if ($email_owner == 1) {
						if ($row->owner) {
							$juser =& JUser::getInstance($row->owner);
							
							if (!$dispatcher->trigger( 'onSendMessage', array( 'support_reply_assigned', $subject, $message, $from, array($juser->get('id')), $this->_option ))) {
								$this->setError( JText::_('Failed to message ticket owner.') );
							} else {
								$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_OWNER').' - '.$juser->get('email').'</li>';
							}
						}
					}
					
					// Add any CCs to the e-mail list
					$cc = JRequest::getVar( 'cc', '' );
					if (trim($cc)) {
						$cc = explode(',',$cc);
						foreach ($cc as $acc)
						{
							$acc = trim($acc);
							
							// Is this a username or email address?
							if (!strstr( $acc, '@' )) {
								// Username - load the user
								$juser =& JUser::getInstance( strtolower($acc) );
								// Did we find an account?
								if (is_object($juser)) {
									// Get the user's email address
									//$acc = $juser->get('email');
									//if (!XMessageHelper::sendMessage( 'support_reply_assigned', $subject, $message, $from, array($juser->get('id')) )) {
									if (!$dispatcher->trigger( 'onSendMessage', array( 'support_reply_assigned', $subject, $message, $from, array($juser->get('id')), $this->_option ))) {
										$this->setError( JText::_('Failed to message ticket owner.') );
									}
									$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_CC').' - '.$acc.'</li>';
								} else {
									// Move on - nothing else we can do here
									continue;
								}
							// Make sure it's a valid e-mail address
							} elseif (SupportUtilities::checkValidEmail($acc)) {
								$emails[] = $acc;
								$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_CC').' - '.$acc.'</li>';
							}
						}
					}

					// Send an e-mail to each address
					foreach ($emails as $email)
					{
						SupportUtilities::sendEmail($email, $subject, $message, $from);
					}
					
					// Were there any changes?
					$elog = implode("\n",$emaillog);
					if ($elog != '') {
						$rowc->changelog .= '<ul class="emaillog">'."\n".$elog.'</ul>'."\n";
						
						// Save the data
						if (!$rowc->store()) {
							JError::raiseError( 500, $rowc->getError() );
							return;
						}
					}
				}
			}
		}

		$filters = JRequest::getVar( 'filters', '' );
		$filters = str_replace('&amp;','&',$filters);

		// output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&'.$filters;
		$this->_message = JText::sprintf('TICKET_SUCCESSFULLY_SAVED',$row->id);
	}
	
	//-----------

	protected function remove() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}
	
		// Check for an ID
		if (count($ids) < 1) {
			echo SupportHtml::alert( JText::_('SUPPORT_ERROR_SELECT_TICKET_TO_DELETE') );
			exit;
		}
		
		foreach ($ids as $id) 
		{
			// Delete tags
			$tags = new SupportTags( $this->database );
			$tags->remove_all_tags( $id );
			
			// Delete comments
			$comment = new SupportComment( $this->database );
			$comment->deleteComments( $id );
			
			// Delete ticket
			$ticket = new SupportTicket( $this->database );
			$ticket->delete( $id );
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::sprintf('TICKET_SUCCESSFULLY_DELETED',count($ids));
	}

	//-----------

	protected function deletemsg() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}
	
		// Check for an ID
		if (count($ids) < 1) {
			echo SupportHtml::alert( JText::_('SUPPORT_ERROR_SELECT_MESSAGE_TO_DELETE') );
			exit;
		}
		
		foreach ($ids as $id) 
		{
			// Delete message
			$msg = new SupportMessage( $this->database );
			$msg->delete( $id );
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=msgs';
		$this->_message = JText::sprintf('MESSAGE_SUCCESSFULLY_DELETED',count($ids));
	}
	
	//-----------

	protected function deletecat() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}
	
		// Check for an ID
		if (count($ids) < 1) {
			echo SupportHtml::alert( JText::_('SUPPORT_ERROR_SELECT_CATEGORY_TO_DELETE') );
			exit;
		}
		
		foreach ($ids as $id) 
		{
			// Delete message
			$cat = new SupportCategory( $this->database );
			$cat->delete( $id );
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
		$this->_message = JText::sprintf('CATEGORY_SUCCESSFULLY_DELETED',count($ids));
	}
	
	//-----------
	
	protected function cancelsec() 
	{
		$this->cancel('sections');
	}
	
	//-----------
	
	protected function cancelcat() 
	{
		$this->cancel('categories');
	}
	
	//-----------
	
	protected function cancelmsg() 
	{
		$this->cancel('messages');
	}
	
	//-----------
	
	protected function cancelres() 
	{
		$this->cancel('resolutions');
	}
	
	//-----------

	protected function cancel($task='')
	{
		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$limit = JRequest::getInt('limit', $config->getValue('config.list_limit'));
		$start = JRequest::getInt('limitstart', 0);
		
		// Get filters
		$filterby  = JRequest::getVar( 'filterby', '' );
		$filterby2 = JRequest::getVar( 'filterby2', '' );
		$sortby    = JRequest::getVar( 'sortby', '' );
		$searchin  = JRequest::getVar( 'searchin', '' );
		
		// Build the return URL
		$url  = 'index.php?option='.$this->_option;
		$url .= ($task)      ? '&task='.$task           : '';
		$url .= ($limit)     ? '&limit='.$limit         : '';
		$url .= ($start)     ? '&limitstart='.$start    : '';
		$url .= ($filterby)  ? '&filterby='.$filterby   : '';
		$url .= ($sortby)    ? '&sortby='.$sortby       : '';
		$url .= ($filterby2) ? '&filterby2='.$filterby2 : '';
		$url .= ($searchin)  ? '&searchin='.$searchin   : '';
		
		$this->_redirect = $url;
	}
	
	//----------------------------------------------------------
	//  Abuse reports
	//----------------------------------------------------------

	protected function abusereports()
	{
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'tables'.DS.'reportabuse.php' );
		
		// Instantiate a new view
		$view = new JView( array('name'=>'reports') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
	
		// Incoming
		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.reports.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.reports.limitstart', 'limitstart', 0, 'int');
		$view->filters['state']  = JRequest::getInt( 'state', 0 );
		$view->filters['sortby'] = JRequest::getVar( 'sortby', 'a.created DESC' );
		
		$ra = new ReportAbuse( $this->database );
		
		// Get record count
		$view->total = $ra->getCount( $view->filters );
		
		// Get records
		$view->rows = $ra->getRecords( $view->filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//-----------

	protected function abusereport()
	{
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'tables'.DS.'reportabuse.php' );
		
		// Instantiate a new view
		$view = new JView( array('name'=>'report') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$cat = JRequest::getVar( 'cat', '' );
		
		// Ensure we have an ID to work with
		if (!$id) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task=abusereports';
			return;
		}
		
		// Load the report
		$report = new ReportAbuse( $this->database );
		$report->load( $id );
		
		// Load plugins
		JPluginHelper::importPlugin('support');
		$dispatcher =& JDispatcher::getInstance();

		// Get the parent ID
		$results = $dispatcher->trigger( 'getParentId', array(
				$report->referenceid,
				$report->category)
			);

		// Check the results returned for a parent ID
		$parentid = 0;
		if ($results) {
			foreach ($results as $result) 
			{
				if ($result) {
					$parentid = $result;
				}
			}
		}

		// Get the reported item
		$results = $dispatcher->trigger( 'getReportedItem', array(
				$report->referenceid,
				$cat,
				$parentid)
			);
		
		// Check the results returned for a reported item
		$reported = null;
		if ($results) {
			foreach ($results as $result) 
			{
				if ($result) {
					$reported = $result[0];
				}
			}
		}

		// Get the title
		$titles = $dispatcher->trigger( 'getTitle', array(
				$report->category,
				$parentid)
			);
		
		// Check the results returned for a title
		$title = null;
		if ($titles) {
			foreach ($titles as $t) 
			{
				if ($t) {
					$title = $t;
				}
			}
		}

		$view->report = $report;
		$view->reported = $reported;
		$view->parentid = $parentid;
		$view->title = $title;
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function releasereport() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'tables'.DS.'reportabuse.php' );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Ensure we have an ID to work with
		if (!$id) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		// Load the report
		$report = new ReportAbuse( $this->database );
		$report->load( $id );
		$report->state = 1;
		if (!$report->store()) {
			JError::raiseError( 500, $report->getError() );
			return;
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=abusereports';
		$this->_message = JText::_('ITEM_RELEASED_SUCCESSFULLY');
	}

	//-----------

	protected function deletereport() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'tables'.DS.'reportabuse.php' );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$parentid = JRequest::getInt( 'parentid', 0 );
		
		// Ensure we have an ID to work with
		if (!$id) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task=abusereports';
			return;
		}

		$email     = 1; // Turn off/on
		$gratitude = 1; // Turn off/on
		$message   = '';
		$note   = JRequest::getVar( 'note', '' );

		// Load the report
		$report = new ReportAbuse( $this->database );
		$report->load( $id );

		// Load plugins
		JPluginHelper::importPlugin('support');
		$dispatcher =& JDispatcher::getInstance();
		
		// Get the reported item
		$results = $dispatcher->trigger( 'getReportedItem', array(
				$report->referenceid,
				$report->category,
				$parentid)
			);
		
		// Check the results returned for a reported item
		$reported = null;
		if ($results) {
			foreach ($results as $result) 
			{
				if ($result) {
					$reported = $result[0];
				}
			}
		}
		
		// Remove the reported item and any other related processes that need be performed
		$results = $dispatcher->trigger( 'deleteReportedItem', array(
				$report->referenceid,
				$parentid,
				$report->category,
				$message)
			);
		
		if ($results) {
			foreach ($results as $result) 
			{
				if ($result) {
					$message .= $result;
				}
			}
		}
		
		// Mark abuse report as deleted	
		$report->state = 2;
		if (!$report->store()) {
			JError::raiseError( 500, $report->getError() );
			return;
		}
		
		$jconfig =& JFactory::getConfig();
		
		// Notify item owner
		if ($email) {			
			$juser =& JUser::getInstance($reported->author);
			
			// Email "from" info
			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('SUPPORT');
			$from['email'] = $jconfig->getValue('config.mailfrom');
			
			// Email subject
			$subject = JText::sprintf('REPORT_ABUSE_EMAIL_SUBJECT',$jconfig->getValue('config.sitename'));
			
			// Build the email message
			if ($note) {
				$message .= '---------------------------'."\r\n";
				$message .= $note;
				$message .= '---------------------------'."\r\n";
			}
			$message .= "\r\n";
			$message .= JText::_('YOUR_POSTING').': '."\r\n";
			$message .= $reported->text."\r\n";
			$message .= '---------------------------'."\r\n";
			$message .= JText::_('PLEASE_CONTACT_SUPPORT');

			// Send the email
			if (SupportUtilities::checkValidEmail($juser->get('email'))) {
				SupportUtilities::sendEmail($from, $juser->get('email'), $subject, $message);
			}
		}
		
		// Check the HUB configuration to see if banking is turned on
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$banking = $upconfig->get('bankAccounts');
		
		// Give some points to whoever reported abuse
		if ($banking && $gratitude) {
			ximport('bankaccount');
			
			$BC = new BankConfig( $this->database );
			$ar = $BC->get('abusereport');  // How many points?
			if ($ar) {
				$ruser =& JUser::getInstance( $report->created_by );
				if (is_object($ruser) && $ruser->get('id')) {
					$BTL = new BankTeller( $this->database, $ruser->get('id') );
					$BTL->deposit($ar, JText::_('ACKNOWLEDGMENT_FOR_VALID_REPORT'), 'abusereport', $id);
				}
			}
		}
		
		// Redirect
		$this->_message = JTexT::_('ITEM_TAKEN_DOWN');
		$this->_redirect = 'index.php?option='.$this->_option.'&task=abusereports';
	}

	//----------------------------------------------------------
	// Misc. Functions
	//----------------------------------------------------------
	
	private function userSelect( $name, $active, $nouser=0, $javascript=NULL, $order='a.name' ) 
	{
		$database =& JFactory::getDBO();

		$query = "SELECT a.username AS value, a.name AS text, g.name AS groupname"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
			. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id"	// map aro to group
			. "\n INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id"
			. "\n WHERE a.block = '0' AND g.id=25"
			. "\n ORDER BY ". $order;

		$database->setQuery( $query );
		if ($nouser) {
			$users[] = JHTML::_('select.option', '', 'No User', 'value', 'text');
			$users = array_merge( $users, $database->loadObjectList() );
		} else {
			$users = $database->loadObjectList();
		}

		$users = JHTML::_('select.genericlist', $users, $name, ' '. $javascript, 'value', 'text', $active, false, false );

		return $users;
	}
	
	//-----------
	
	private function userSelectGroup( $name, $active, $nouser=0, $javascript=NULL, $group='' ) 
	{
		ximport('xgroup');
		
		$xgroup = new XGroup();
		$xgroup->select( $group );
		
		$users = array();
		if ($nouser) {
			$users[] = JHTML::_('select.option', '', 'No User', 'value', 'text');
		}
		
		if ($xgroup->get('gidNumber')) {
			$members = $xgroup->get('members');

			foreach ($members as $member) 
			{
				$u =& JUser::getInstance($member);
				if (!is_object($u)) {
					continue;
				}

				$m = new stdClass();
				$m->value = $u->get('username');
				$m->text  = $u->get('name');
				$m->groupname = $group;

				$users[] = $m;
			}
		}
		
		$users = JHTML::_('select.genericlist', $users, $name, ' '. $javascript, 'value', 'text', $active, false, false );

		return $users;
	}
	
	//----------------------------------------------------------
	// media manager
	//----------------------------------------------------------

	protected function upload( $listdir )
	{
		// Incoming
		$description = JRequest::getVar( 'description', '' );

		if (!$listdir || $_FILES['upload']['name'] == '') {
			return '';
		}

		// Construct our file path
		$file_path = JPATH_ROOT.$this->config->get('webpath').DS.$listdir;

		ximport('fileupload');
		ximport('fileuploadutils');
		
		// Build the path if it doesn't exist
		if (!is_dir( $file_path )) {
			FileUploadUtils::make_path( $file_path );
		}
		
		// Upload new files
		$upload = new FileUpload;
		$upload->upload_dir     = $file_path;
		$upload->temp_file_name = trim($_FILES['upload']['tmp_name']);
		$upload->file_name      = trim(strtolower($_FILES['upload']['name']));
		$upload->file_name      = str_replace(' ', '_', $upload->file_name);
		$upload->ext_array      = explode(',',$this->config->get('file_ext'));
		$upload->max_file_size  = $this->config->get('maxAllowed');
		
		$result = $upload->upload_file_no_validation();
		
		if (!$result) {
			$this->setError(JText::_('ERROR_UPLOADING').' '.$upload->err);
			
			return '';
		} else {
			// File was uploaded
			// Create database entry
			$description = htmlspecialchars($description);
			
			$row = new SupportAttachment( $this->database );
			$row->bind( array('id'=>0,'ticket'=>$listdir,'filename'=>$upload->file_name,'description'=>$description) );
			if (!$row->check()) {
				$this->setError($row->getError());
			}
			if (!$row->store()) {
				$this->setError($row->getError());
			}
			if (!$row->id) {
				$row->getID();
			}
			
			return '{attachment#'.$row->id.'}';
		}
	}
	
	//----------------------------------------------------------
	//  Tag/Group autoassignment
	//----------------------------------------------------------

	protected function taggroup()
	{
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
		
		// Instantiate a new view
		$view = new JView( array('name'=>'taggroups') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
	
		// Incoming
		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.taggroups.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.taggroups.limitstart', 'limitstart', 0, 'int');
		$view->filters['sortby'] = JRequest::getVar( 'sortby', 'priority ASC' );
		
		$tg = new TagsGroup( $this->database );
		
		// Get record count
		$view->total = $tg->getCount();
		
		// Get records
		$view->rows = $tg->getRecords();

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//-----------

	protected function edittg() 
	{
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
		ximport('xgroup');
		
		// Instantiate a new view
		$view = new JView( array('name'=>'taggroup') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Initiate database class and load info
		$view->row = new TagsGroup( $this->database );
		$view->row->load( $id );

		$view->tag = new TagsTag( $this->database );
		$view->tag->load( $view->row->tagid );

		$view->group = new XGroup();
		$view->group->select( $view->row->groupid );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function savetg() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
		ximport('xgroup');
		
		$taggroup = JRequest::getVar('taggroup', array(), 'post');
		
		// Initiate class and bind posted items to database fields
		$row = new TagsGroup( $this->database );
		if (!$row->bind( $taggroup )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
	
		// Incoming tag
		$tag = trim(JRequest::getVar( 'tag', '', 'post' ));
		
		// Attempt to load the tag
		$ttag = new TagsTag( $this->database );
		$ttag->loadTag( $tag );
		
		// Set the group ID
		if ($ttag->id) {
			$row->tagid = $ttag->id;
		}
		
		// Incoming group
		$group = trim(JRequest::getVar( 'group', '', 'post' ));
		
		// Attempt to load the group
		$xgroup = new XGroup();
		$xgroup->select( $group );
		
		// Set the group ID
		if ($xgroup->get('gidNumber')) {
			$row->groupid = $xgroup->get('gidNumber');
		}
		
		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=taggroup';
		$this->_message = JText::_('ENTRY_SUCCESSFULLY_SAVED');
	}
	
	//-----------

	protected function deletetg() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
		
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}
	
		// Check for an ID
		if (count($ids) < 1) {
			echo SupportHtml::alert( JText::_('SUPPORT_ERROR_SELECT_ENTRY_TO_DELETE') );
			exit;
		}
		
		$tg = new TagsGroup( $this->database );
		foreach ($ids as $id) 
		{
			// Delete entry
			$tg->delete( $id );
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=taggroup';
		$this->_message = JText::sprintf('ENTRY_SUCCESSFULLY_DELETED',count($ids));
	}
	
	//-----------
	
	protected function canceltg() 
	{
		$this->cancel('taggroup');
	}
	
	//-----------

	protected function reorder() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
		
		// Incoming
		$id = JRequest::getVar( 'id', array() );
		$id = $id[0];

		// Ensure we have an ID to work with
		if (!$id) {
			JError::raiseError( 500, JText::_('No entry ID found.') );
			return;
		}

		// Get the element moving down - item 1
		$tg1 = new TagsGroup( $this->database );
		$tg1->load( $id );

		// Get the element directly after it in ordering - item 2
		$tg2 = clone( $tg1 );
		$tg2->getNeighbor( $this->_task );

		switch ($this->_task) 
		{
			case 'orderup':				
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $tg2->priority;
				$orderdn = $tg1->priority;
				
				$tg1->priority = $orderup;
				$tg2->priority = $orderdn;
				break;
			
			case 'orderdown':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $tg1->priority;
				$orderdn = $tg2->priority;
				
				$tg1->priority = $orderdn;
				$tg2->priority = $orderup;
				break;
		}
		
		// Save changes
		$tg1->store();
		$tg2->store();
		
		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option .'&task=taggroup';
	}
}
