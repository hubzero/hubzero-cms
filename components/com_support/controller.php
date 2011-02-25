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
		$this->acl = SupportACL::getACL();
		
		$this->_task = JRequest::getVar( 'task', '', 'post' );
		if (!$this->_task) {
			$this->_task = JRequest::getVar( 'task', '', 'get' );
		}
		
		switch ($this->_task) 
		{
			case 'upload':     $this->upload();     break;
			case 'save':       $this->save();       break;
			case 'ticket':     $this->ticket();     break;
			case 'tickets':    $this->tickets();    break;
			case 'login':      $this->login();      break;
			case 'create':     $this->create();     break;
			case 'feed':       $this->feed();       break;
			case 'delete':     $this->delete();     break;
			case 'index':      $this->index();      break;
			case 'stats':      $this->stats();      break;
			
			case 'reportabuse': $this->reportabuse(); break;
			case 'savereport':  $this->savereport();  break;
			
			default: $this->index(); break;
		}
	}
	
	//-----------
	
	protected function _buildPathway($ticket=null) 
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option='.$this->_option
			);
		}
		if (count($pathway->getPathWay()) == 1  && $this->_task) {
			if ($this->_task == 'ticket') {
				$this->_task = 'tickets';
			}
			$pathway->addItem(
				JText::_(strtoupper($this->_task)),
				'index.php?option='.$this->_option.'&task='.$this->_task
			);
		}
		if (is_object($ticket) && $ticket->id) {
			$pathway->addItem(
				'#'.$ticket->id,
				'index.php?option='.$this->_option.'&task=ticket&id='.$ticket->id
			);
		}
	}
	
	//-----------
	
	protected function _buildTitle($ticket=null) 
	{
		$this->_title = JText::_(strtoupper($this->_name));
		if ($this->_task) {
			$this->_title .= ': '.JText::_(strtoupper($this->_task));
		}
		if (is_object($ticket) && $ticket->id) {
			$this->_title .= ' #'.$ticket->id;
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $this->_title );
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------
	
	protected function stats() 
	{
		// Check authorization
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return $this->login();
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'stats') );
		$view->option = $this->_option;
		$view->title = JText::_(strtoupper($this->_name));
		
		$view->authorized = $this->authorize();
		//if ($view->authorized != 'admin') {
		if (!$this->acl->check('read','tickets')) {
			$this->_return = JRoute::_('index.php?option='.$this->_option.'&task=tickets');
		}
		
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles();
		
		$type = JRequest::getVar('type', 'submitted');
		$view->type = ($type == 'automatic') ? 1 : 0;
		
		$view->group = JRequest::getVar('group', '');
		
		$view->sort = JRequest::getVar('sort', 'name');
		
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
		$view->users = null;
		
		if ($view->group) {
			$query = "SELECT a.username, a.name, a.id"
				. "\n FROM #__users AS a, #__xgroups AS g, #__xgroups_members AS gm"
				. "\n WHERE g.cn='".$view->group."' AND g.gidNumber=gm.gidNumber AND gm.uidNumber=a.id"
				. "\n ORDER BY a.name";
		} else {
			$query = "SELECT a.username, a.name, a.id"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
				. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id"	// map aro to group
				. "\n INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id"
				. "\n WHERE a.block = '0' AND g.id=25"
				. "\n ORDER BY a.name";
		}
		
		$this->database->setQuery( $query );
		$users = $this->database->loadObjectList();
		if ($users) {
			$u = array();
			$p = array();
			$g = array();
			foreach ($users as $user) 
			{
				$user->closed = array();
				
				// Get closed ticket information
				$user->closed['year'] = $st->getCountOfTicketsClosed($view->type, $year, '01', '01', $user->username, $view->group);

				$user->closed['month'] = $st->getCountOfTicketsClosed($view->type, $year, $month, '01', $user->username, $view->group);

				$user->closed['week'] = $st->getCountOfTicketsClosed($view->type, $year, $month, $week, $user->username, $view->group);
				
				$p[$user->id] = $user;
				switch ($view->sort) 
				{
					case 'year':
						$u[$user->id] = $user->closed['year'];
					break;
					case 'month':
						$u[$user->id] = $user->closed['month'];
					break;
					case 'week':
						$u[$user->id] = $user->closed['week'];
					break;
					case 'name':
					default:
						$u[$user->id] = $user->name;
					break;
				}
			}
			if ($view->sort != 'name') {
				arsort($u);
			} else {
				asort($u);
			}
			foreach ($u as $k=>$v) 
			{
				$g[] = $p[$k];
			}
			
			$view->users = $g;
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
	
	protected function index() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'index') );
		$view->title = JText::_(strtoupper($this->_name));
		
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function login()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'login') );
		$view->title = ucfirst($this->_name).': '.ucfirst($this->_task);
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------
	
	protected function tickets() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'tickets') );
		$view->title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$view->option = $this->_option;
		$view->database = $this->database;

		// Incoming
		$view->filters = $this->_getFilters();

		// Check authorization
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return $this->login();
		}
	
		if (!$this->acl->check('read','tickets')) {
			$view->filters['owner'] = $juser->get('username');
			$view->filters['reportedby'] = $juser->get('username');
		}
		$view->authorized = $this->authorize();

		// Create a Ticket object
		$obj = new SupportTicket( $this->database );

		// Record count
		$total = $obj->getTicketsCount( $view->filters, $this->acl->check('read','tickets') );

		// Fetch results
		$view->rows = $obj->getTickets( $view->filters, $this->acl->check('read','tickets') );

		// Initiate paging class
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $total, $view->filters['start'], $view->filters['limit'] );
		
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Get some needed styles
		$this->_getStyles();
		
		// Get some needed scripts
		$this->_getScripts();
		
		$view->acl = $this->acl;
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function ticket() 
	{
		// Check authorization
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return $this->login();
		}
	
		// Get the ticket ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			JError::raiseError( 404, JText::_('SUPPORT_NO_TICKET_ID') );
			return;
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'ticket') );
		$view->title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$view->option = $this->_option;
		$view->database = $this->database;
		
		// Incoming
		$view->filters = $this->_getFilters();

		// Initiate database class and load info
		$view->row = new SupportTicket( $this->database );
		$view->row->load( $id );
		
		if (!$view->row->report) {
			JError::raiseError( 404, JText::_('SUPPORT_TICKET_NOT_FOUND') );
			return;
		}
		
		if ($view->row->login == $juser->get('username') 
		 || $view->row->owner == $juser->get('username')) {
			if (!$this->acl->check('read','tickets')) {
				$this->acl->setAccess('read','tickets',1);
			}
			if (!$this->acl->check('update','tickets')) {
				$this->acl->setAccess('update','tickets',-1);
			}
			if (!$this->acl->check('create','comments')) {
				$this->acl->setAccess('create','comments',-1);
			}
			if (!$this->acl->check('read','comments')) {
				$this->acl->setAccess('read','comments',1);
			}
		}
		
		if ($this->acl->authorize($view->row->group)) {
			$this->acl->setAccess('read','tickets',1);
			$this->acl->setAccess('update','tickets',1);
			$this->acl->setAccess('delete','tickets',1);
			$this->acl->setAccess('create','comments',1);
			$this->acl->setAccess('read','comments',1);
			$this->acl->setAccess('create','private_comments',1);
			$this->acl->setAccess('read','private_comments',1);
		}
		
		// Ensure the user is authorized to view this ticket
		$view->authorized = $this->authorize($view->row->group);
		/*if ($view->row->login != $juser->get('username') 
		 && $view->row->owner != $juser->get('username') 
		 && !$view->authorized 
		 && $view->row->section!=2) {
			JError::raiseError( 403, JText::_('SUPPORT_NOT_AUTH') );
			return;
		}*/
		if (!$this->acl->check('read','tickets')) {
			JError::raiseError( 403, JText::_('SUPPORT_NOT_AUTH') );
			return;
		}
		
		// Get the next and previous support tickets
		$view->row->prev = $view->row->getTicketId('prev', $view->filters);
		$view->row->next = $view->row->getTicketId('next', $view->filters);

		$summary = substr($view->row->report, 0, 70);
		if (strlen($summary) >=70 ) {
			$summary .= '...';
		}
		if ($view->row->summary == $summary) {
			$view->row->summary = '';
		} else {
			// Do some text cleanup
			$view->row->summary = html_entity_decode(stripslashes($view->row->summary), ENT_COMPAT, 'UTF-8');
			$view->row->summary = str_replace('&quote;','&quot;',$view->row->summary);
			$view->row->summary = htmlentities($view->row->summary, ENT_COMPAT, 'UTF-8');
		}
		
		$view->row->report = html_entity_decode(stripslashes($view->row->report), ENT_COMPAT, 'UTF-8');
		$view->row->report = str_replace('&quote;','&quot;',$view->row->report);
		if (!strstr( $view->row->report, '</p>' ) && !strstr( $view->row->report, '<pre class="wiki">' )) {
			$view->row->report = str_replace("<br />","",$view->row->report);
			$view->row->report = htmlentities($view->row->report, ENT_COMPAT, 'UTF-8');
			$view->row->report = nl2br($view->row->report);
			$view->row->report = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$view->row->report);
			$view->row->report = str_replace("    ",'&nbsp;&nbsp;&nbsp;&nbsp;',$view->row->report);
		}
		
		$view->lists = array();
		
		// Get resolutions
		$sr = new SupportResolution( $this->database );
		$view->lists['resolutions'] = $sr->getResolutions();
		
		// Get messages
		$sm = new SupportMessage( $this->database );
		$view->lists['messages'] = $sm->getMessages();

		// Get Tags
		$st = new SupportTags( $this->database );
		$view->lists['tags'] = $st->get_tag_string( $view->row->id, 0, 0, NULL, 0, 1 );
		$view->lists['tagcloud'] = $st->get_tag_cloud( 3, 1, $view->row->id );
		
		// Get comments
		$sc = new SupportComment( $this->database );
		$view->comments = $sc->getComments( $this->acl->check('read','private_comments'), $view->row->id );

		// Parse comment text for attachment tags
		$juri =& JURI::getInstance();
		
		$webpath = str_replace('//','/',$juri->base().$this->config->get('webpath').DS.$id);
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
		for ($i=0; $i < count($view->comments); $i++) 
		{
			$comment =& $view->comments[$i];
			$comment->comment = stripslashes($comment->comment);
			if (!strstr( $comment->comment, '</p>' ) && !strstr( $comment->comment, '<pre class="wiki">' )) {
				$comment->comment = str_replace("<br />","",$comment->comment);
				$comment->comment = htmlentities($comment->comment, ENT_COMPAT, 'UTF-8');
				$comment->comment = nl2br($comment->comment);
				$comment->comment = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$comment->comment);
			}
			$comment->comment = $attach->parse($comment->comment);
		}
		
		$view->row->report = $attach->parse($view->row->report);
		
		// Get severities
		$view->lists['severities'] = SupportUtilities::getSeverities($this->config->get('severities'));
		
		// Populate the list of assignees based on if the ticket belongs to a group or not
		if (trim($view->row->group)) {
			$view->lists['owner'] = $this->_userSelectGroup( 'ticket[owner]', $view->row->owner, 1, '', trim($view->row->group) );
		} elseif (trim($this->config->get('group'))) {
			$view->lists['owner'] = $this->_userSelectGroup( 'ticket[owner]', $view->row->owner, 1, '', trim($this->config->get('group')) );
		} else {
			$view->lists['owner'] = $this->_userSelect( 'ticket[owner]', $view->row->owner, 1 );
		}
		
		// Set the pathway
		$this->_buildPathway($view->row);
		
		// Set the page title
		$this->_buildTitle($view->row);
		
		// Get some needed styles
		$this->_getStyles();
		
		// Get some needed scripts
		$this->_getScripts();
		
		$view->acl = $this->acl;
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function feed() 
	{
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'feed'.DS.'feed.php' );
		
		global $mainframe;

		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		$params =& $mainframe->getParams();
		$doc->link = JRoute::_('index.php?option='.$this->_option.'&task=tickets');

		// Incoming
		$filters = $this->_getFilters();

		// Create a Ticket object
		$obj = new SupportTicket( $this->database );
	
		// Fetch results
		$rows = $obj->getTickets( $filters, true );

		$xhub =& Hubzero_Factory::getHub();
		
		$doc->title = $xhub->getCfg('hubShortName').' '.JText::_('SUPPORT_RSS_TITLE');
		$doc->description = JText::sprintf('SUPPORT_RSS_DESCRIPTION',$xhub->getCfg('hubShortName'));
		$doc->copyright = JText::sprintf('SUPPORT_RSS_COPYRIGHT', date("Y"), $xhub->getCfg('hubShortURL'));
		$doc->category = JText::_('SUPPORT_RSS_CATEGORY');

		foreach ($rows as $row)
		{
			// Prepare the title
			$title = strip_tags(stripslashes($row->summary));
			$title = html_entity_decode($title);

			// URL link to article
			// & used instead of &amp; as this is converted by feed creator
			$link = JRoute::_('index.php?option='.$this->_option.'&task=ticket&id='. $row->id );

			// Strip html from feed item description text
			$description = html_entity_decode(stripslashes($row->report)); //SupportHtml::shortenText($row->report);
			$author      = ($row->login) ? $row->name.' ('.$row->login.')' : $row->name;
			@$date       = ( $row->created ? date( 'r', strtotime($row->created) ) : '' );

			// Load individual item creator class
			$item = new JFeedItem();
			$item->title       = $title;
			$item->link        = $link;
			$item->description = $description;
			$item->date        = $date;
			$item->category    = $row->category;
			$item->author      = $author;
			$item->authorEmail = $row->email;

			// Loads item info into rss array
			$doc->addItem( $item );
		}

		// Output the feed
		echo $doc->render();
	}

	//-----------

	protected function save() 
	{
	    $juser =& JFactory::getUser();
		
		// Make sure we are still logged in
		if ($juser->get('guest')) {
			return $this->login();
		}
		
		// Incoming
		$incoming = JRequest::getVar('ticket', array(), 'post');
		
		// Trim all posted items
		$incoming = array_map('trim',$incoming);
	
		$id = JRequest::getInt('id', 0, 'post');
		if (!$id) {
			JError::raiseError( 500, JText::_('No Ticket ID provided.') );
			return;
		}
	
		// Instantiate the tagging class - we'll need this a few times
		$st = new SupportTags( $this->database );
	
		// Load the old ticket so we can compare for the changelog
		if ($id) {
			$old = new SupportTicket( $this->database );
			$old->load( $id );
			
			// Get Tags
			$oldtags = $st->get_tag_string( $id, 0, 0, NULL, 0, 1 );
		}
	
		// Initiate class and bind posted items to database fields
		$row = new SupportTicket( $this->database );
		if (!$row->bind( $incoming )) {
			echo SupportHtml::alert( $row->getError() );
			exit();
		}
		
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
		if (!$row->resolved) {
			$row->status = 0;
		}
		
		// If status is "open" or "waiting", ensure the resolution is empty
		if ($row->status == 0 || $row->status == 1) {
			$row->resolved = '';
		}

		// Check content
		if (!$row->check()) {
			echo SupportHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo SupportHtml::alert( $row->getError() );
			exit();
		}
		
		$row->load( $id );
		
		// Save the tags
		$tags = trim(JRequest::getVar( 'tags', '', 'post' ));
		//if ($tags) {
			$st->tag_object( $juser->get('id'), $row->id, $tags, 0, true );
		//}

		// We must have a ticket ID before we can do anything else
		if ($id) {
			// Incoming comment
			$comment = JRequest::getVar( 'comment', '', 'post', 'none', 2 );
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
			
			// Compare fields to find out what has changed for this ticket and build a changelog
			$changelog = array();

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
					echo SupportHtml::alert( $rowc->getError() );
					exit();
				}
			
				// Only do the following if a comment was posted
				// otherwise, we're only recording a changelog
				if ($comment || $row->owner != $old->owner) {
					$xhub =& Hubzero_Factory::getHub();
					$jconfig =& JFactory::getConfig();
					
					// Parse comments for attachments
					$attach = new SupportAttachment( $this->database );
					$attach->webpath = $xhub->getCfg('hubLongURL').$this->config->get('webpath').DS.$id;
					$attach->uppath  = JPATH_ROOT.$this->config->get('webpath').DS.$id;
					$attach->output  = 'email';

					// Build e-mail components
					$admin_email = $jconfig->getValue('config.mailfrom');
					
					$subject = JText::_(strtoupper($this->_name)).', '.JText::_('TICKET').' #'.$row->id.' comment '.md5($row->id);
					
					$from = array();
					$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
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

					$juri =& JURI::getInstance();
					$sef = JRoute::_('index.php?option='.$this->_option.'&task=ticket&id='. $row->id);
					if (substr($sef,0,1) == '/') {
						$sef = substr($sef,1,strlen($sef));
					}
					$message .= $juri->base().$sef."\r\n";

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
							} else if ($row->email && SupportUtilities::checkValidEmail($row->email)) {
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
									if (!$dispatcher->trigger( 'onSendMessage', array( 'support_reply_assigned', $subject, $message, $from, array($juser->get('id')), $this->_option ))) {
										$this->setError( JText::_('Failed to message ticket owner.') );
									}
									$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_CC').' - '.$acc.'</li>';
								} else {
									// Move on - nothing else we can do here
									continue;
								}
							// Make sure it's a valid e-mail address
							} else if (SupportUtilities::checkValidEmail($acc)) {
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
							echo SupportHtml::alert( $rowc->getError() );
							exit();
						}
					}
				}
			}
		}

		// Display the ticket with changes, new comment
		$this->ticket();
	}

	//-----------

	protected function delete() 
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
	
		// Check for an ID
		if (!$id) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=tickets');
			return;
		}
		
		// Delete tags
		$tags = new SupportTags( $this->database );
		$tags->remove_all_tags( $id );
		
		// Delete comments
		$comment = new SupportComment( $this->database );
		$comment->deleteComments( $id );
			
		// Delete ticket
		$ticket = new SupportTicket( $this->database );
		$ticket->delete( $id );
		
		// Output messsage and redirect
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=tickets');
	}

	//-----------

	protected function create()
	{
		/*
		option  = 'com_support';
		task    = 'create';
		no_html = 1;
		type    = 1;
		sesstoken (optional)
		
		login    (optional) default: automated
		severity (optional) default: normal
		category (optional) default: Tools
		summary  (optional) default: first 75 characters of report
		report
		email    (optional) default: $xhub->getCfg('hubSupportEmail')
		name     (optional) default: Automated Error Report
		os       (optional)
		browser  (optional)
		ip       (optional)
		hostname (optional)
		uas      (optional)
		referrer (optional)
		cookies  (optional) default: 1 (since it's coming from rappture we assume they're already logged in and thus have cookies enabled)
		section  (optional)
		*/
		
		// trim and addslashes all posted items
		$incoming = array_map('trim',$_POST);
		$incoming = array_map('addslashes',$incoming);
	
		// initiate class and bind posted items to database fields
		$row = new SupportTicket( $this->database );
		if (!$row->bind( $incoming )) {
			return $row->getError();
		}
		
		// Check for a session token
		$sess = JRequest::getVar( 'sesstoken', '' );
		$sessnum = '';
		if ($sess) {
			include_once( JPATH_ROOT.DS.'components'.DS.'com_tools'.DS.'mw.utils.php' );
			$mwdb =& MwUtils::getMWDBO();
			
			// retrieve the username and IP from session with this session token
			$query = "SELECT * FROM session WHERE session.sesstoken='".$sess."' LIMIT 1";
			$mwdb->setQuery($query);
			$viewperms = $mwdb->loadObjectList();

			if ($viewperms) {
				foreach ($viewperms as $sinfo)
				{
					$row->login = $sinfo->username;
					$row->ip    = $sinfo->remoteip;
					$sessnum    = $sinfo->sessnum;
				}
				
				// get user's infor from login
				$juser =& JUser::getInstance( $row->login );
				$row->name  = $juser->get('name');
				$row->email = $juser->get('email');
			}
		}
			
		$row->login = ($row->login) ? $row->login : 'automated';

		if (strstr($row->summary, '"') || strstr($row->summary, "'")) {
			$summary = str_replace("\'","\\\\\\\\\'", $row->summary);
			$summary = str_replace('\"','\\\\\\\\\"', $summary);
			$query = "SELECT id FROM #__support_tickets WHERE LOWER(summary) LIKE '%".strtolower($summary)."%' AND type=1 LIMIT 1";
		} else {
			$query = "SELECT id FROM #__support_tickets WHERE LOWER(summary) LIKE '%".strtolower($row->summary)."%' AND type=1 LIMIT 1";
		}
		// check for an existing ticket with this report
		$this->database->setQuery( $query );
		$ticket = $this->database->loadResult();
		if ($this->database->getErrorNum()) {
			return $this->database->stderr();
		}
		
		if ($ticket) {
			$log = '';
			
			// open existing ticket if closed
			$oldticket = new SupportTicket( $this->database );
			$oldticket->load( $ticket );
			$oldticket->instances++;
			if ($oldticket->status == 2) {
				$oldticket->status = 0;
				$oldticket->resolved = 'reopened';

				$changelog = array();
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_STATUS').'</strong> '.JText::_('TICKET_CHANGED_FROM').' <em>closed</em> '.JText::_('TO').' <em>open</em></li>';
				$changelog[] = '<li><strong>'.JText::_('TICKET_FIELD_INSTANCE').'</strong> increased</li>';
				$log = implode("\n",$changelog);
				if ($log != '') {
					$log = '<ul>'."\n".$log.'</ul>'."\n";
				}
			}
			
			// check content
			if (!$oldticket->check()) {
				return $oldticket->getError();
			}

			// store new content
			if (!$oldticket->store()) {
				return $oldticket->getError();
			}
			
			// make a log note if we had to reopen the ticket
			if ($log) {
				$rowc = new SupportComment( $this->database );
				$rowc->ticket     = $ticket;
				$rowc->comment    = '';
				$rowc->created    = date( 'Y-m-d H:i:s', time() );
				$rowc->created_by = $row->login;
				$rowc->changelog  = $log;
				$rowc->access     = 1;

				if ($rowc->check()) {
					if (!$rowc->store()) {
						return $rowc->getError();
					}
				}
			}
			
			$status = ($oldticket->resolved) ? $oldticket->resolved : 'open';
			$count  = $oldticket->instances;
		} else {
			// set some defaults
			$row->status    = 0;
			$row->created   = date( 'Y-m-d H:i:s', time() );
			$row->severity  = ($row->severity) ? $row->severity : 'normal';
			$row->category  = ($row->category) ? $row->category : JText::_('CATEGORY_TOOLS');
			$row->resolved  = '';
			$row->email     = ($row->email)    ? $row->email    : $this->_data['supportemail'];
			$row->name      = ($row->name)     ? $row->name     : JText::_('AUTOMATED_REPORT');
			$row->cookies   = ($row->cookies)  ? $row->cookies  : 1;
			$row->instances = 1;
			$row->section   = ($row->section)  ? $row->section  : 1;
			$row->type      = 1;

			if (!$row->summary) {
				$row->summary = $this->txt_shorten($row->report, 75);
			}
			
			// clean any cross-site scripting from report
			ximport('Hubzero_Filter');
			$row->summary = Hubzero_Filter::cleanXss($row->summary);
			$row->report  = Hubzero_Filter::cleanXss($row->report);
			$row->report  = str_replace( '<br>', '<br />', $row->report );
			$row->report  = ''.$row->report;
			
			// check content
			if (!$row->check()) {
				return $row->getError();
			}

			// store new content
			if (!$row->store()) {
				return $row->getError();
			}

			if (!$row->id) {
				$query = "SELECT id FROM #__support_tickets 
							WHERE created='".$row->created."' 
							AND category='".$row->category."' 
							AND email='".$row->email."' 
							AND name='".$row->name."' 
							AND summary='".$row->summary."' 
							AND report='".$row->report."'";
				$this->database->setQuery( $query );
				$row->id = $this->database->loadResult();
			}
			
			$ticket = $row->id;
			$status = 'new';
			$count  = 1;
		}
		
		echo 'Ticket #'.$ticket.' ('.$status.') '.$count.' times';
	}
	
	//----------------------------------------------------------
	// Report abuse
	//----------------------------------------------------------

	private function reportabuse()
	{
		// Login required
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'reportabuse') );
		$view->title = ucfirst($this->_name).': '.ucfirst($this->_task);
		$view->option = $this->_option;
		$view->juser = $juser;
		
		// Incoming
		$view->refid = JRequest::getInt( 'id', 0 );
		$view->parentid = JRequest::getInt( 'parent', 0 );
		$view->cat = JRequest::getVar( 'category', '' );
		
		// Check for a reference ID
		if (!$view->refid) {
			JError::raiseError( 404, JText::_('REFERENCE_ID_NOT_FOUND') );
			return;
		}
		
		// Check for a category
		if (!$view->cat) {
			JError::raiseError( 404, JText::_('CATEGORY_NOT_FOUND') );
			return;
		}
		
		// Load plugins
		JPluginHelper::importPlugin('support');
		$dispatcher =& JDispatcher::getInstance();
		
		// Get the search result totals
		$results = $dispatcher->trigger( 'getReportedItem', array(
				$view->refid,
				$view->cat,
				$view->parentid)
			);
		
		// Check the results returned for a reported item
		$report = null;
		if ($results) {
			foreach ($results as $result) 
			{
				if ($result) {
					$view->report = $result[0];
				}
			}
		}
		
		// Ensure we found a reported item
		if (!$view->report) {
			$this->setError( JText::_('ERROR_REPORTED_ITEM_NOT_FOUND') );
		}

		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Add the CSS to the template and set the page title
		$this->_getStyles();
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	private function savereport()
	{
		$juser =& JFactory::getUser();
		
		$email = 0; // turn off
		
		// Instantiate a new view
		$view = new JView( array('name'=>'thanks') );
		$view->title = ucfirst($this->_name).': '.ucfirst($this->_task);
		$view->option = $this->_option;
		
		// Incoming
		$view->cat = JRequest::getVar( 'category', '' );
		$view->refid = JRequest::getInt( 'referenceid', 0 );
		$view->returnlink = JRequest::getVar( 'link', '' );
			
		// Trim and addslashes all posted items
		$incoming = array_map('trim',$_POST);
	
		// Initiate class and bind posted items to database fields
		$row = new ReportAbuse( $this->database );
		if (!$row->bind( $incoming )) {
			echo SupportHtml::alert( $row->getError() );
			exit();
		}

		ximport('Hubzero_Filter');
		$row->report     = Hubzero_Filter::cleanXss($row->report);
		$row->report     = nl2br($row->report);
		$row->created_by = $juser->get('id');
		$row->created    = date( 'Y-m-d H:i:s', time() );
		$row->state      = 0;

		// Check content
		if (!$row->check()) {
			echo SupportHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo SupportHtml::alert( $row->getError() );
			exit();
		}
		
		// Send notification email 
		if ($email) {
			$jconfig =& JFactory::getConfig();
			
			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('REPORTABUSE');
			$from['email'] = $jconfig->getValue('config.mailfrom');
			
			$subject = $jconfig->getValue('config.sitename').' '.JText::_('REPORTABUSE');
			
			$message = '';
			
			$tos = array();
			
			// Get administration e-mail
			$tos[] = $jconfig->getValue('config.mailfrom');
			
			// Get the user's e-mail
			$tos[] = $juser->get('email');
			
			foreach ($tos as $to) 
			{
				if (SupportUtilities::checkValidEmail($to)) {
					if (!SupportUtilities::sendEmail($from, $to, $subject, $message)) {
						$this->setError( JText::sprintf('ERROR_FAILED_TO_SEND_EMAIL',$to) );
					}
				}
			}
		}
		
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Push some needed styles to the template
		$this->_getStyles();
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// General functions
	//----------------------------------------------------------

	private function _getFilters()
	{
		// Query filters defaults
		$filters = array();
		$filters['search'] = '';
		$filters['status'] = 'open';
		$filters['type'] = 0;
		$filters['owner'] = '';
		$filters['reportedby'] = '';
		$filters['severity'] = 'normal';
		$filters['sort'] = trim(JRequest::getVar( 'filter_order', 'created' ));
		$filters['sortdir'] = trim(JRequest::getVar( 'filter_order_Dir', 'DESC' ));
		//$filters['section'] = 0;
		//$filters['category'] = '';
		$filters['severity'] = '';
		
		// Paging vars
		$filters['limit'] = JRequest::getInt( 'limit', 25 );
		$filters['start'] = JRequest::getInt( 'limitstart', 0 );
		
		// Incoming
		$filters['_find'] = urldecode(trim(JRequest::getVar( 'find', '', 'post' )));
		$filters['_show'] = urldecode(trim(JRequest::getVar( 'show', '', 'post' )));
		
		if ($filters['_find'] != '' || $filters['_show'] != '') {
			$filters['start'] = 0;
		} else {
			$filters['_find'] = urldecode(trim(JRequest::getVar( 'find', '', 'get' )));
			$filters['_show'] = urldecode(trim(JRequest::getVar( 'show', '', 'get' )));
		}
		
		// Break it apart so we can get our filters
		// Starting string hsould look like "filter:option filter:option"
		if ($filters['_find'] != '') {
			$chunks = explode(' ', $filters['_find']);
			$filters['_show'] = '';
		} else {
			$chunks = explode(' ', $filters['_show']);
		}
		
		// Loop through each chunk (filter:option)
		foreach ($chunks as $chunk) 
		{
			if (!strstr($chunk,':')) {
				if ((substr($chunk, 0, 1) == '"' 
				 || substr($chunk, 0, 1) == "'") 
				 && (substr($chunk, -1) == '"' 
				 || substr($chunk, -1) == "'")) {
					$chunk = substr($chunk, 1, -1);  // Remove any surrounding quotes
				}

				$filters['search'] = $chunk;
				continue;
			}
			
			// Break each chunk into its pieces (filter, option)
			$pieces = explode(':', $chunk);
			
			// Find matching filters and ensure the vaule provided is valid
			switch ($pieces[0])
			{
				case 'q':
					$pieces[0] = 'search';
					if (isset($pieces[1])) {
						// Queries must be in quotes. If they're not, we ignore it
						if ((substr($pieces[1], 0, 1) == '"' 
						|| substr($pieces[1], 0, 1) == "'") 
						&& (substr($pieces[1], -1) == '"' 
						|| substr($pieces[1], -1) == "'")) {
							$pieces[1] = substr($pieces[1], 1, -1);  // Remove any surrounding quotes
						}
					} else {
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
				case 'status':
					$allowed = array('open','closed','all','new','waiting');
					if (!in_array($pieces[1],$allowed)) {
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
				case 'type':
					$allowed = array('submitted'=>0,'automatic'=>1,'none'=>2,'tool'=>3);
					if (in_array($pieces[1],$allowed)) {
						//$pieces[1] = ($pieces[1] == $allowed[0]) ? 0 : 1;
						$pieces[1] = $allowed[$pieces[1]];
					} else {
						$pieces[1] = 0;
					}
				break;
				case 'owner':
				case 'reportedby':
					if (isset($pieces[1]) && $pieces[1] == 'me') {
						$juser =& JFactory::getUser();
						$pieces[1] = $juser->get('username');
					}
				break;
				case 'severity':
					$allowed = array('critical', 'major', 'normal', 'minor', 'trivial');
					if (!in_array($pieces[1],$allowed)) {
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
			}
			
			$filters[$pieces[0]] = (isset($pieces[1])) ? $pieces[1] : '';
		}

		// Check if we have a section:category
		/*$secat = trim(JRequest::getVar( 'category', '' ));
		if ($secat) {
			// Break it apart to get the individual pieces
			$bits = explode(':',$filters['category']);
			$filters['category'] = end($bits);
			$filters['section'] = $bits[0];
		}*/
		
		// Return the array
		return $filters;
	}
	
	//-----------

	private function _userSelect( $name, $active, $nouser=0, $javascript=NULL, $order='a.name' ) 
	{
		$query = "SELECT a.username AS value, a.name AS text, g.name AS groupname"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
			. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id"	// map aro to group
			. "\n INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id"
			. "\n WHERE a.block = '0' AND g.id=25"
			. "\n ORDER BY ". $order;

		$this->database->setQuery( $query );
		if ( $nouser ) {
			$users[] = JHTML::_('select.option', '', 'No User', 'value', 'text');
			$users = array_merge( $users, $this->database->loadObjectList() );
		} else {
			$users = $this->database->loadObjectList();
		}
		
		$users = JHTML::_('select.genericlist', $users, $name, ' '. $javascript, 'value', 'text', $active, false, false );

		return $users;
	}
	
	//-----------
	
	private function _userSelectGroup( $name, $active, $nouser=0, $javascript=NULL, $group='' ) 
	{
		$users = array();
		if ($nouser) {
			$users[] = JHTML::_('select.option', '', 'No User', 'value', 'text');
		}
		
		ximport('Hubzero_Group');
		
		if (strstr($group,',')) {
			$groups = explode(',',$group);
			if (is_array($groups)) {
				foreach ($groups as $g) 
				{
					$hzg = Hubzero_Group::getInstance( trim($g) );

					if ($hzg->get('gidNumber')) {
						$members = $hzg->get('members');

						//$users[] = '<optgroup title="'.stripslashes($hzg->description).'">';
						$users[] = JHTML::_('select.optgroup', stripslashes($hzg->description) );
						foreach ($members as $member) 
						{
							$u =& JUser::getInstance($member);
							if (!is_object($u)) {
								continue;
							}

							$m = new stdClass();
							$m->value = $u->get('username');
							$m->text  = $u->get('name');
							$m->groupname = $g;

							$users[] = $m;
						}
						//$users[] = '</optgroup>';
						$users[] = JHTML::_('select.option', '</OPTGROUP>' );
					}
				}
			}
		} else {
			$hzg = Hubzero_Group::getInstance( $group );

			if ($hzg->get('gidNumber')) {
				$members = $hzg->get('members');

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
		}
		
		$users = JHTML::_('select.genericlist', $users, $name, ' '. $javascript, 'value', 'text', $active, false, false );

		return $users;
	}
	
	//----------------------------------------------------------
	// media manager
	//----------------------------------------------------------

	protected function upload( $listdir )
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return '';
		}
		
		if (!$listdir) {
			$this->setError( JText::_('SUPPORT_NO_UPLOAD_DIRECTORY') );
			return '';
		}
		
		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			//$this->setError( JText::_('SUPPORT_NO_FILE') );
			return '';
		}
		
		// Incoming
		$description = JRequest::getVar( 'description', '' );
		
		// Construct our file path
		$path = JPATH_ROOT.$this->config->get('webpath').DS.$listdir;
		
		// Build the path if it doesn't exist
		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
				return '';
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('ERROR_UPLOADING') );
			return '';
		} else {
			// File was uploaded
			// Create database entry
			$description = htmlspecialchars($description);
			
			$row = new SupportAttachment( $this->database );
			$row->bind( array('id'=>0,'ticket'=>$listdir,'filename'=>$file['name'],'description'=>$description) );
			if (!$row->check()) {
				$this->setError( $row->getError() );
			}
			if (!$row->store()) {
				$this->setError( $row->getError() );
			}
			if (!$row->id) {
				$row->getID();
			}
			
			return '{attachment#'.$row->id.'}';
		}
	}
	
	//----------------------------------------------------------
	// misc.
	//----------------------------------------------------------

	private function authorize($toolgroup='') 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return 'admin';
		}
		
		// Was a specific group set in the config?
		$group = trim($this->config->get('group'));
		if ($group or $toolgroup) {
			ximport('Hubzero_User_Helper');
			
			// Check if they're a member of this group
			$ugs = Hubzero_User_Helper::getGroups( $juser->get('id') );
			if ($ugs && count($ugs) > 0) {
				foreach ($ugs as $ug) 
				{
					if ($group && $ug->cn == $this->gid) {
						return true;
					}
					if ($toolgroup && $ug->cn == $toolgroup) {
						return true;
					}
				}
			}
		}
		
		return false;
	}
}
