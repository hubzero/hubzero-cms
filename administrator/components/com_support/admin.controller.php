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

//----------------------------------------------------------

class SupportController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;

	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
		
	//-----------
	
	private function getTask()
	{
		$task = JRequest::getVar( 'task', '' );
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		// Get the component parameters
		$sconfig = new SupportConfig( $this->_option );
		$this->config = $sconfig;
		
		switch ($this->getTask()) 
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
			case 'tickets':    $this->tickets();     break;
			
			default: $this->tickets(); break;
		}
	}
	
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
	
	//----------------------------------------------------------
	//  Views
	//----------------------------------------------------------

	/*protected function categories()
	{
		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filter = array();
		$filters['limit'] = JRequest::getInt('limit', $config->getValue('config.list_limit'));
		$filters['start'] = JRequest::getInt('limitstart', 0);
		
		$database =& JFactory::getDBO();

		$obj = new SupportCategory( $database );
		
		// Record count
		$total = $obj->getCount( $filters );
		
		// Fetch results
		$rows = $obj->getRecords( $filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		SupportHtml::categories( $rows, $pageNav, $this->_option );
	}
	
	//-----------

	protected function sections()
	{
		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filter = array();
		$filters['limit'] = JRequest::getInt('limit', $config->getValue('config.list_limit'));
		$filters['start'] = JRequest::getInt('limitstart', 0);

		$database =& JFactory::getDBO();

		$obj = new SupportSection( $database );
		
		// Record count
		$total = $obj->getCount( $filters );
		
		// Fetch results
		$rows = $obj->getRecords( $filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		SupportHtml::sections( $rows, $pageNav, $this->_option );
	}*/

	//-----------

	protected function resolutions()
	{
		$app =& JFactory::getApplication();
		
		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filter = array();
		//$filters['limit'] = JRequest::getInt('limit', $config->getValue('config.list_limit'));
		//$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.resolutions.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = $app->getUserStateFromRequest($this->_option.'.resolutions.limitstart', 'limitstart', 0, 'int');

		$database =& JFactory::getDBO();

		$obj = new SupportResolution( $database );
		
		// Record count
		$total = $obj->getCount( $filters );
		
		// Fetch results
		$rows = $obj->getRecords( $filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		SupportHtml::resolutions( $rows, $pageNav, $this->_option );
	}

	//-----------

	private function getFilters()
	{
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Query filters defaults
		$filters = array();
		$filters['search'] = '';
		$filters['status'] = 'open';
		$filters['type'] = 0;
		$filters['owner'] = '';
		$filters['reportedby'] = '';
		$filters['severity'] = 'normal';
		//$filters['sort'] = trim(JRequest::getVar( 'filter_order', 'created' ));
		//$filters['sortdir'] = trim(JRequest::getVar( 'filter_order_Dir', 'DESC' ));
		$filters['severity'] = '';
		//$filters['section'] = 0;
		//$filters['category'] = '';
		$filters['sort'] = trim($app->getUserStateFromRequest($this->_option.'.tickets.sort', 'filter_order', 'created'));
		$filters['sortdir'] = trim($app->getUserStateFromRequest($this->_option.'.tickets.sortdir', 'filter_order_Dir', 'DESC'));
		
		// Paging vars
		//$filters['limit'] = JRequest::getVar( 'limit', 25 );
		//$filters['start'] = JRequest::getInt( 'limitstart', 0 );
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.tickets.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = $app->getUserStateFromRequest($this->_option.'.tickets.limitstart', 'limitstart', 0, 'int');
		
		// Incoming
		//$filters['_find'] = urldecode(trim(JRequest::getVar( 'find', '' )));
		//$filters['_show'] = urldecode(trim(JRequest::getVar( 'show', '' )));
		$filters['_find'] = urldecode(trim($app->getUserStateFromRequest($this->_option.'.tickets.find', 'find', '')));
		$filters['_show'] = urldecode(trim($app->getUserStateFromRequest($this->_option.'.tickets.show', 'show', '')));
		
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
					$allowed = array('open','closed','all','waiting','new');
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
					if (isset($pieces[1])) {
						if ($pieces[1] == 'me') {
							$juser =& JFactory::getUser();
							$pieces[1] = $juser->get('username');
						} else if ($pieces[1] == 'none') {
							$pieces[1] = 'none';
						}
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

	private function getStyles() 
	{
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.'admin.'.$this->_name.'.css');
	}

	//-----------

	protected function tickets()
	{
		// Push some styles to the template
		$this->getStyles();

		// Get filters
		$filters = $this->getFilters();

		// Get configuration
		//$config = JFactory::getConfig();

		$database =& JFactory::getDBO();

		$obj = new SupportTicket( $database );
		
		// Record count
		$total = $obj->getTicketsCount( $filters, true );
		
		// Fetch results
		$rows = $obj->getTickets( $filters, true );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		SupportHtml::tickets( $database, $rows, $pageNav, $this->_option, $filters );
	}

	//-----------

	protected function messages()
	{
		$app =& JFactory::getApplication();
		
		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filter = array();
		//$filters['limit'] = JRequest::getInt('limit', $config->getValue('config.list_limit'));
		//$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.messages.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = $app->getUserStateFromRequest($this->_option.'.messages.limitstart', 'limitstart', 0, 'int');

		$database =& JFactory::getDBO();

		$obj = new SupportMessage( $database );
		
		// Record count
		$total = $obj->getCount( $filters );
		
		// Fetch results
		$rows = $obj->getRecords( $filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// output HTML
		SupportHtml::messages( $rows, $pageNav, $this->_option );
	}

	//-----------
	
	protected function add() 
	{
		$this->edit();
	}

	//-----------

	protected function edit() 
	{
	    $juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		// Push some styles to the template
		$this->getStyles();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		$filters = $this->getFilters();

		// Initiate database class and load info
		$row = new SupportTicket( $database );
		$row->load( $id );

		if ($id) {
			// Editing an existing ticket

			// Get comments
			$sc = new SupportComment( $database );
			$comments = $sc->getComments( 'admin', $row->id );
			
			// Parse comment text for attachment tags
			$xhub =& XFactory::getHub();

			$attach = new SupportAttachment( $database );
			$attach->webpath = $xhub->getCfg('hubLongURL').$this->config->parameters['webpath'].DS.$id;
			$attach->uppath  = JPATH_ROOT.$this->config->parameters['webpath'].DS.$id;
			$attach->output  = 'web';
			for ($i=0; $i < count($comments); $i++) 
			{
				$comment =& $comments[$i];
				$comment->comment = $attach->parse($comment->comment);
			}
			
			$row->statustext = SupportHtml::getStatus($row->status);
			
			// Get the next and previous support tickets
			//$row->prev = $row->getTicketId('prev', $filters, 'admin');
			//$row->next = $row->getTicketId('next', $filters, 'admin');
		} else {
			// Creating a new ticket
			$juser =& JFactory::getUser();
			
			$row->severity = 'normal';
			$row->status   = 0;
			$row->created  = date( 'Y-m-d H:i:s', time() );
			$row->login    = $juser->get('username');
			$row->name     = $juser->get('name');
			$row->email    = $juser->get('email');
			$row->cookies  = 1;
			
			ximport('Hubzero_Browser');
			$browser = new Hubzero_Browser();

			$row->os = $browser->getOs().' '.$browser->getOsVersion();
			$row->browser = $browser->getBrowser().' '.$browser->getBrowserVersion();
			
			$row->uas = $_SERVER['HTTP_USER_AGENT'];
			
			$row->ip = (getenv('HTTP_X_FORWARDED_FOR'))
		    		 ? getenv('HTTP_X_FORWARDED_FOR')
					 : getenv('REMOTE_ADDR');
			$row->hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			$row->section = 1;
			
			$comments = array();
		}
		
		// Do some text cleanup
		$row->summary = html_entity_decode(stripslashes($row->summary), ENT_COMPAT, 'UTF-8');
		$row->summary = str_replace('&quote;','&quot;',$row->summary);
		$row->summary = htmlentities($row->summary, ENT_COMPAT, 'UTF-8');
		
		//$row->report  = stripslashes($row->report);
		$row->report  = html_entity_decode(stripslashes($row->report), ENT_COMPAT, 'UTF-8');
		$row->report  = str_replace('&quote;','&quot;',$row->report);
		//$row->report  = htmlspecialchars($row->report);
		$row->report  = str_replace("<br />","",$row->report);
		$row->report  = htmlentities($row->report, ENT_COMPAT, 'UTF-8');
		$row->report  = nl2br($row->report);
		$row->report  = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$row->report);
		$row->report  = str_replace("    ",'&nbsp;&nbsp;&nbsp;&nbsp;',$row->report);
		
		$lists = array();
		
		// Get resolutions
		$sr = new SupportResolution( $database );
		$lists['resolutions'] = $sr->getResolutions();
		
		// Get messages
		$sm = new SupportMessage( $database );
		$lists['messages'] = $sm->getMessages();

		// Get sections
		//$ss = new SupportSection( $database );
		//$lists['sections'] = $ss->getSections();
		
		// Get categories
		//$sa = new SupportCategory( $database );
		//$lists['categories'] = $sa->getCategories( $row->section );
		
		// Get Tags
		$st = new SupportTags( $database );
		$lists['tags'] = $st->get_tag_string( $row->id, 0, 0, NULL, 0, 1 );
		$lists['tagcloud'] = $st->get_tag_cloud( 3, 1, $row->id );
		
		// Get severities
		$lists['severities'] = $this->config->getSeverities();
		
		//$group = trim($this->config->parameters['group']);
		$group = trim($row->group);
		if ($group) {
			$lists['owner'] = $this->userSelectGroup( 'owner', $row->owner, 1, '', $group );
		} else {
			$lists['owner'] = $this->userSelect( 'owner', $row->owner, 1 );
		}
		
		// Ouput HTML
		SupportHtml::editTicket( $database, $row, $this->_option, $lists, $comments, $filters );
	}
	
	//-----------

	/*protected function editcat() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Initiate database class and load info
		$row = new SupportCategory( $database );
		$row->load( $id );

		// Set action
		if ($id) {
			$action = JText::_('EDIT');
		} else {
			$action = JText::_('NEW');
			$row->category = '';
			$row->section = 1;
		}
		
		// Get support sections
		$ss = new SupportSection( $database );
		$sections = $ss->getSections();

		// Ouput HTML
		SupportHtml::editCategory( $row, $action, $this->_option, $sections );
	}

	//-----------

	protected function editsec() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Initiate database class and load info
		$row = new SupportSection( $database );
		$row->load( $id );

		// Set action
		if ($id) {
			$action = JText::_('EDIT');
		} else {
			$action = JText::_('NEW');
			$row->section = '';
		}

		// Ouput HTML
		SupportHtml::editSection( $row, $action, $this->_option );
	}*/

	//-----------

	protected function editres() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Initiate database class and load info
		$row = new SupportResolution( $database );
		$row->load( $id );

		// Set action
		if ($id) {
			$action = JText::_('EDIT');
		} else {
			$action = JText::_('NEW');
			$row->section = '';
		}

		// Ouput HTML
		SupportHtml::editResolution( $row, $action, $this->_option );
	}

	//-----------

	protected function editmsg() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Initiate database class and load info
		$row = new SupportMessage( $database );
		$row->load( $id );

		// Set action
		if ($id) {
			$action = JText::_('EDIT');
		} else {
			$action = JText::_('NEW');
			$row->title   = '';
			$row->message = '';
		}

		// Ouput HTML
		SupportHtml::editMessage( $row, $action, $this->_option );
	}

	//-----------

	protected function savemsg() 
	{
		$database =& JFactory::getDBO();
	
		// Trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportMessage( $database );
		if (!$row->bind( $_POST )) {
			echo SupportHtml::alert( $row->getError() );
			exit();
		}
	
		// Code cleaner for xhtml transitional compliance
		$row->title   = trim($row->title);
		$row->message = trim($row->message);
		
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
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=messages';
		$this->_message = JText::_('MESSAGE_SUCCESSFULLY_SAVED');
	}

	//-----------

	/*protected function savecat() 
	{
		$database =& JFactory::getDBO();
	
		// Trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportCategory( $database );
		if (!$row->bind( $_POST )) {
			echo SupportHtml::alert( $row->getError() );
			exit();
		}
	
		// Code cleaner for xhtml transitional compliance
		$row->category = trim($row->category);
		
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
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=categories';
		$this->_message = JText::_('CATEGORY_SUCCESSFULLY_SAVED');
	}

	//-----------

	protected function savesec() 
	{
		$database =& JFactory::getDBO();
	
		// Trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportSection( $database );
		if (!$row->bind( $_POST )) {
			echo SupportHtml::alert( $row->getError() );
			exit();
		}
	
		// Code cleaner for xhtml transitional compliance
		$row->section = trim($row->section);
		
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
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=sections';
		$this->_message = JText::_('SECTION_SUCCESSFULLY_SAVED');
	}*/
	
	//-----------

	protected function saveres() 
	{
		$database =& JFactory::getDBO();
	
		// Trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportResolution( $database );
		if (!$row->bind( $_POST )) {
			echo SupportHtml::alert( $row->getError() );
			exit();
		}
	
		// Code cleaner for xhtml transitional compliance
		$row->title = trim($row->title);
		if (!$row->alias) {
			$row->alias = preg_replace("/[^a-zA-Z0-9]/", "", $row->title);
			$row->alias = strtolower($row->alias);
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
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=resolutions';
		$this->_message = JText::_('RESOLUTION_SUCCESSFULLY_SAVED');
	}
	
	//-----------

	protected function save() 
	{
	    $juser =& JFactory::getUser();
		$database =& JFactory::getDBO();

		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Instantiate the tagging class - we'll need this a few times
		$st = new SupportTags( $database );
		
		// Load the old ticket so we can compare for the changelog
		if ($id) {
			$old = new SupportTicket( $database );
			$old->load( $id );
			
			// Get Tags
			$oldtags = $st->get_tag_string( $id, 0, 0, NULL, 0, 1 );
		}
	
		// Trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportTicket( $database );
		if (!$row->bind( $_POST )) {
			echo SupportHtml::alert( $row->getError() );
			exit();
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
		$tags = JRequest::getVar( 'tags', '', 'post' );

		$st->tag_object( $juser->get('id'), $row->id, $tags, 0, false );
		
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
			$log = implode(n,$changelog);
			if ($log != '') {
				$log = '<ul class="changelog">'.n.$log.'</ul>'.n;
			}
			
			$attachment = $this->upload( $row->id );
			$comment .= ($attachment) ? n.n.$attachment : '';
			
			// Create a new support comment object and populate it
			$rowc = new SupportComment( $database );
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
			
				// Only do the following if a comment was posted or ticket was reassigned
				// otherwise, we're only recording a changelog
				if ($comment || $row->owner != $old->owner) {
					$juri =& JURI::getInstance();
					$jconfig =& JFactory::getConfig();
					
					// Parse comments for attachments
					$attach = new SupportAttachment( $database );
					$attach->webpath = $juri->base().$this->config->parameters['webpath'].DS.$id;
					$attach->uppath  = JPATH_ROOT.$this->config->parameters['webpath'].DS.$id;
					$attach->output  = 'email';

					// Build e-mail components
					$admin_email = $jconfig->getValue('config.mailfrom');
					
					$subject = ucfirst($this->_name).', Ticket #'.$row->id.' comment '.md5($row->id);
					
					$from = array();
					$from['name']  = $jconfig->getValue('config.sitename').' '.ucfirst($this->_name);
					$from['email'] = $jconfig->getValue('config.mailfrom');
		
					$message  = '----------------------------'.r.n;
					$message .= strtoupper(JText::_('TICKET')).': '.$row->id.r.n;
					$message .= strtoupper(JText::_('TICKET_DETAILS_SUMMARY')).': '.stripslashes($row->summary).r.n;
					$message .= strtoupper(JText::_('TICKET_DETAILS_CREATED')).': '.$row->created.r.n;
					$message .= strtoupper(JText::_('TICKET_DETAILS_CREATED_BY')).': '.$row->name;
					$message .= ($row->login) ? ' ('.$row->login.')'.r.n : r.n;
					$message .= '----------------------------'.r.n.r.n;
					$message .= JText::sprintf('TICKET_EMAIL_COMMENT_POSTED',$row->id).': '.$rowc->created_by.r.n;
					$message .= JText::_('TICKET_EMAIL_COMMENT_CREATED').': '.$rowc->created.r.n.r.n;
					if ($row->owner != $old->owner) {
						if ($old->owner == '') {
							$message .= JText::_('TICKET_FIELD_OWNER').' '.JText::_('TICKET_SET_TO').' "'.$row->owner.'"'.r.n.r.n;
						} else {
							$message .= JText::_('TICKET_FIELD_OWNER').' '.JText::_('TICKET_CHANGED_FROM').' "'.$old->owner.'" to "'.$row->owner.'"'.r.n.r.n;
						}
					}
					$message .= $attach->parse($comment).r.n.r.n;

					$sef = JRoute::_('index.php?option='.$this->_option.'&task=ticket&id='. $row->id);
					if (substr($sef,0,1) == '/') {
						$sef = substr($sef,1,strlen($sef));
					}
					$base = $juri->base();
					if (substr($base,-14) == 'administrator/') {
						$base = substr($base,0,strlen($base)-14);
					}
					$message .= $base.$sef.r.n;
						
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
							/*if ($row->email && SupportUtils::check_validEmail($row->email)) {
								$emails[] = $row->email;
								$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_SUBMITTER').' - '.$row->email.'</li>';
							}*/
							if (is_object($zuser) && $zuser->get('id')) {
								$type = 'support_reply_submitted';
								if ($row->status == 1) {
									$element = $row->id;
									$description = 'index.php?option='.$this->_option.a.'task=ticket'.a.'id='.$row->id;
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
							} elseif (SupportUtils::check_validEmail($acc)) {
								$emails[] = $acc;
								$emaillog[] = '<li>'.JText::_('TICKET_EMAILED_CC').' - '.$acc.'</li>';
							}
						}
					}

					// Send an e-mail to each address
					foreach ($emails as $email)
					{
						SupportUtils::send_email($email, $subject, $message, $from);
					}
					
					// Were there any changes?
					$elog = implode(n,$emaillog);
					if ($elog != '') {
						$rowc->changelog .= '<ul class="emaillog">'.n.$elog.'</ul>'.n;
						
						// Save the data
						if (!$rowc->store()) {
							echo SupportHtml::alert( $rowc->getError() );
							exit();
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
		
		$database =& JFactory::getDBO();
		
		foreach ($ids as $id) 
		{
			// Delete tags
			$tags = new SupportTags( $database );
			$tags->remove_all_tags( $id );
			
			// Delete comments
			$comment = new SupportComment( $database );
			$comment->deleteComments( $id );
			
			// Delete ticket
			$ticket = new SupportTicket( $database );
			$ticket->delete( $id );
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::sprintf('TICKET_SUCCESSFULLY_DELETED',count($ids));
	}

	//-----------

	protected function deletemsg() 
	{
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
		
		$database =& JFactory::getDBO();
		
		foreach ($ids as $id) 
		{
			// Delete message
			$msg = new SupportMessage( $database );
			$msg->delete( $id );
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=msgs';
		$this->_message = JText::sprintf('MESSAGE_SUCCESSFULLY_DELETED',count($ids));
	}
	
	//-----------

	/*protected function deletecat() 
	{
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
		
		$database =& JFactory::getDBO();
		
		foreach ($ids as $id) 
		{
			// Delete message
			$cat = new SupportCategory( $database );
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
	}*/
	
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
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'support.reportabuse.php' );
		
		$database =& JFactory::getDBO();
	
		// Get configuration
		$config = JFactory::getConfig();
	
		// Incoming
		$filters = array();
		$filters['limit']  = JRequest::getInt( 'limit', $config->getValue('config.list_limit') );
		$filters['start']  = JRequest::getInt( 'limitstart', 0 );
		$filters['state']  = JRequest::getInt( 'state', 0 );
		$filters['sortby'] = JRequest::getVar( 'sortby', 'a.created DESC' );
		
		$ra = new ReportAbuse( $database );
		
		// Get record count
		$total = $ra->getCount( $filters );
		
		// Get records
		$reports = $ra->getRecords( $filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		SupportHtml::abusereports( $database, $reports, $pageNav, $this->_option, $filters );
	}
	
	//-----------

	protected function abusereport()
	{
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'support.reportabuse.php' );
		
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$cat = JRequest::getVar( 'cat', '' );
		
		// Ensure we have an ID to work with
		if (!$id) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task=abusereports';
			return;
		}
		
		// Load the report
		$report = new ReportAbuse( $database );
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

		// Output HTML
		SupportHtml::abusereport( $report, $reported, $this->_option, $parentid, $title );
	}

	//-----------

	protected function releasereport() 
	{
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'support.reportabuse.php' );
		
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Ensure we have an ID to work with
		if (!$id) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		// Load the report
		$report = new ReportAbuse( $database );
		$report->load( $id );
		$report->state = 1;
		if (!$report->store()) {
			echo SupportHtml::alert( $report->getError() );
			exit();
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=abusereports';
		$this->_message = JText::_('ITEM_RELEASED_SUCCESSFULLY');
	}

	//-----------

	protected function deletereport() 
	{
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'support.reportabuse.php' );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$parentid = JRequest::getInt( 'parentid', 0 );
		
		// Ensure we have an ID to work with
		if (!$id) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task=abusereports';
			return;
		}

		$database =& JFactory::getDBO();
		
		$email     = 1; // Turn off/on
		$gratitude = 1; // Turn off/on
		$message   = '';
		$note   = JRequest::getVar( 'note', '' );

		// Load the report
		$report = new ReportAbuse( $database );
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
			echo SupportHtml::alert( $report->getError() );
			exit();
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
				$message .= '---------------------------'.r.n;
				$message .= $note;
				$message .= '---------------------------'.r.n;
			}
			$message .= r.n;
			$message .= JText::_('YOUR_POSTING').': '.r.n;
			$message .= $reported->text.r.n;
			$message .= '---------------------------'.r.n;
			$message .= JText::_('PLEASE_CONTACT_SUPPORT');

			// Send the email
			if (SupportUtils::check_validEmail($juser->get('email'))) {
				SupportUtils::send_email($from, $juser->get('email'), $subject, $message);
			}
		}
		
		// Check the HUB configuration to see if banking is turned on
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$banking = $upconfig->get('bankAccounts');
		
		// Give some points to whoever reported abuse
		if ($banking && $gratitude) {
			ximport('bankaccount');
			
			$BC = new BankConfig( $database );
			$ar = $BC->get('abusereport');  // How many points?
			if ($ar) {
				$ruser =& JUser::getInstance( $report->created_by );
				if (is_object($ruser) && $ruser->get('id')) {
					$BTL = new BankTeller( $database, $ruser->get('id') );
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
		$file_path = JPATH_ROOT.$this->config->parameters['webpath'].DS.$listdir;

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
		$upload->ext_array      = explode(',',$this->config->parameters['file_ext']);
		$upload->max_file_size  = $this->config->parameters['maxAllowed'];
		
		$result = $upload->upload_file_no_validation();
		
		if (!$result) {
			$this->setError(JText::_('ERROR_UPLOADING').' '.$upload->err);
			
			return '';
		} else {
			// File was uploaded
			// Create database entry
			$description = htmlspecialchars($description);
			
			$database =& JFactory::getDBO();
			$row = new SupportAttachment( $database );
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
		
		$database =& JFactory::getDBO();
	
		// Get configuration
		$config = JFactory::getConfig();
	
		// Incoming
		$filters = array();
		$filters['limit']  = JRequest::getInt( 'limit', $config->getValue('config.list_limit') );
		$filters['start']  = JRequest::getInt( 'limitstart', 0 );
		$filters['sortby'] = JRequest::getVar( 'sortby', 'priority ASC' );
		
		$tg = new TagsGroup( $database );
		
		// Get record count
		$total = $tg->getCount();
		
		// Get records
		$rows = $tg->getRecords();

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		SupportHtml::taggroup( $rows, $pageNav, $this->_option );
	}
	
	//-----------

	protected function edittg() 
	{
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
		ximport('xgroup');
		
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Initiate database class and load info
		$row = new TagsGroup( $database );
		$row->load( $id );

		$tag = new TagsTag( $database );
		$tag->load( $row->tagid );

		$group = new XGroup();
		$group->select( $row->groupid );

		// Set action
		if ($id) {
			$action = JText::_('EDIT');
		} else {
			$action = JText::_('NEW');
		}

		// Ouput HTML
		SupportHtml::edittg( $row, $tag, $group, $action, $this->_option );
	}

	//-----------

	protected function savetg() 
	{
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
		ximport('xgroup');
		
		$database =& JFactory::getDBO();
	
		// Initiate class and bind posted items to database fields
		$row = new TagsGroup( $database );
		if (!$row->bind( $_POST )) {
			echo SupportHtml::alert( $row->getError() );
			exit();
		}
	
		// Incoming tag
		$tag = trim(JRequest::getVar( 'tag', '' ));
		
		// Attempt to load the tag
		$ttag = new TagsTag( $database );
		$ttag->loadTag( $tag );
		
		// Set the group ID
		if ($ttag->id) {
			$row->tagid = $ttag->id;
		}
		
		// Incoming group
		$group = trim(JRequest::getVar( 'group', '' ));
		
		// Attempt to load the group
		$xgroup = new XGroup();
		$xgroup->select( $group );
		
		// Set the group ID
		if ($xgroup->get('gidNumber')) {
			$row->groupid = $xgroup->get('gidNumber');
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
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=taggroup';
		$this->_message = JText::_('ENTRY_SUCCESSFULLY_SAVED');
	}
	
	//-----------

	protected function deletetg() 
	{
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
		
		$database =& JFactory::getDBO();
		$tg = new TagsGroup( $database );
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
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
		
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getVar( 'id', array() );
		$id = $id[0];

		// Ensure we have an ID to work with
		if (!$id) {
			echo SupportHtml::alert( JText::_('No entry ID found.') );
			exit;
		}

		// Get the element moving down - item 1
		$tg1 = new TagsGroup( $database );
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
?>