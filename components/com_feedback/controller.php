<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

class FeedbackController extends JObject
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
		$task = JRequest::getVar( 'task', '', 'post' );
		if (!$task) {
			$task = JRequest::getVar( 'task', '', 'get' );
		}
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		switch ($this->getTask()) 
		{
			// Image management
			case 'upload':          $this->upload();          break;
			case 'img':             $this->img();             break;
			case 'delete':          $this->delete();          break;
			
			// Processors
			case 'sendsuggestions': $this->sendsuggestions(); break;
			case 'sendstory':       $this->sendstory();       break;
			case 'sendreport':      $this->sendreport();      break;
			
			// Views
			case 'suggestions':     $this->suggestions();     break;
			case 'success_story':   $this->success_story();   break;
			case 'report_problems': $this->report_problems(); break;
			case 'poll':            $this->poll();            break;
			case 'main':            $this->main();            break;

			default: $this->main(); break;
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

	//-----------
	
	private function getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file('components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function main() 
	{
		// Set page title
		$title = JText::_(strtoupper($this->_name));
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		$database =& JFactory::getDBO();
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		
		// Push some styles to the template
		$this->getStyles();
		
		// Check if wishlist component entry is there
		$database->setQuery( "SELECT c.id FROM #__components as c WHERE c.option='com_wishlist' AND enabled=1" );
		$wishlist = $database->loadResult();
		$wishlist = $wishlist ? 1 : 0;
		
		// Check if xpoll component entry is there
		$database->setQuery( "SELECT c.id FROM #__components as c WHERE c.option='com_xpoll' AND enabled=1" );
		$xpoll = $database->loadResult();
		$xpoll = $xpoll ? 1 : 0;

		// Output HTML
		echo FeedbackHtml::main( $this->_option, $title, $wishlist, $xpoll );
	}

	//-----------

	protected function success_story()
	{
		$user = $this->getUser();
		
		// Logged-in user?
		$juser =& JFactory::getUser();
		$verified = ($juser->get('guest')) ? 0 : 1;
		
		// Incoming
		$quote = array();
		$quote['long'] = JRequest::getVar('quote', '', 'post');
		$quote['short'] = JRequest::getVar('short_quote', '', 'post');

		// Set page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
		
		// Push some styles to the template
		$this->getStyles();
		
		// Generate a CAPTCHA
		$captcha = array();
		$captcha['operand1'] = rand(0,10);
		$captcha['operand2'] = rand(0,10);
		$captcha['sum'] = $captcha['operand1'] + $captcha['operand2'];
		$captcha['key'] = $this->generate_hash($captcha['sum'],date('j'));
		
		// Output HTML
		echo FeedbackHtml::story( $title, $this->_option, $user, $quote, $captcha, 0, $verified );
	}

	//-----------

	protected function poll()
	{
		// Set page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);

		// Push some styles to the template
		$this->getStyles();

		// Output HTML
		echo FeedbackHtml::poll( $title, $this->_option );	
	}

	//-----------

	protected function suggestions() 
	{
		$user = $this->getUser();
		
		// Incoming
		$for = JRequest::getVar( 'for', '' );
	
		// Logged-in user?
		$juser =& JFactory::getUser();
		$verified = (!$juser->get('guest')) ? 1 : 0;
		
		$suggestion = array('for'=>$for, 'idea'=>'');
	
		// Generate a CAPTCHA
		$suggestion['operand1'] = rand(0,10);
		$suggestion['operand2'] = rand(0,10);
		$suggestion['sum'] = $suggestion['operand1'] + $suggestion['operand2'];
		$suggestion['key'] = $this->generate_hash($suggestion['sum'],date('j'));
	
		// Set page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$document =& JFactory::getDocument();
		$document->setTitle( $title );

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);

		// Push some styles to the template
		$this->getStyles();

		// Output HTML
		echo FeedbackHtml::suggestions( $title, $this->_option, $user, $suggestion, 0, $verified );
	}

	//-----------

	protected function report_problems() 
	{
		$user = $this->getUser();
		
		// Incoming
		$referer = JRequest::getVar( 'HTTP_REFERER', NULL, 'server' ); // What page they came from
		$tool    = JRequest::getVar( 'tool', '' ); // What tool they were using

		// Logged-in user?
		$juser =& JFactory::getUser();
		$verified = (!$juser->get('guest')) ? 1 : 0;
	
		// Get browser info
		list( $os, $os_version, $browser, $browser_ver ) = $this->browsercheck($_SERVER['HTTP_USER_AGENT']);

		$problem = array('os' => $os, 
						 'osver' => $os_version, 
						 'browser' => $browser, 
						 'browserver' => $browser_ver, 
						 'topic' => '',
						 'short' => '', 
						 'long' => '', 
						 'referer' => $referer, 
						 'tool' => $tool);
					 
		// Generate a CAPTCHA
		$problem['operand1'] = rand(0,10);
		$problem['operand2'] = rand(0,10);
		$problem['sum'] = $problem['operand1'] + $problem['operand2'];
		$problem['key'] = $this->generate_hash($problem['sum'],date('j'));

		// Set page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
	
		// Push some styles to the template
		$this->getStyles();
	
		// Output HTML
		echo FeedbackHtml::report( $this->_option, $this->_task, $title, $user, $problem, 0, $verified );
	}

	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------

	public function sendstory() 
	{
		$database =& JFactory::getDBO();
		
		// Trim all posted items
		$_POST = array_map('trim',$_POST);

		// Incoming
		$verified = JRequest::getInt( 'verified', 0 );

		// Set page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_('SUCCESS_STORY');
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_('SUCCESS_STORY'),'index.php?option='.$this->_option.a.'task=success_story');
	
		// Push some styles to the template
		$this->getStyles();
	
		// initiate class and bind posted items to database fields
		$row = new FeedbackQuotes( $database );
		if (!$row->bind( $_POST )) {
			echo FeedbackHtml::alert( $row->getError() );
			exit();
		}
		
		if ($row->quote) {
			// code cleaner for xhtml transitional compliance
			$row->quote = $this->purifyText($row->quote);
			$row->quote = str_replace( '<br>', '<br />', $row->quote );
			
			$row->date  = date( 'Y-m-d H:i:s', time() );
			$useremail = JRequest::getVar( 'useremail', '' );
			
			// check content
			if (!$row->check()) {
				echo FeedbackHtml::alert( $row->getError() );
				exit();
			}

			// store new content
			if (!$row->store()) {
				echo FeedbackHtml::alert( $row->getError() );
				exit();
			}

			// output Thank You message
			$storyteller = array('uid'=>$row->userid, 'name'=>$row->fullname, 'org'=>$row->org, 'email'=>$useremail );
			echo FeedbackHtml::storyThanks( $this->config, $title, $this->_option, $storyteller, $row->quote, $row->picture );
		} else {
			// error
			$err = 1;
			$storyteller = array('uid'=>$row->userid, 'name'=>$row->fullname, 'org'=>$row->org, 'email'=>$row->useremail );
		
			// output form with error messages
			echo FeedbackHtml::story( $title, $storyteller, $row->quote, $err, $row->verified );
		}
	}

	//-----------

	protected function sendsuggestions() 
	{
		// Set page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_('SUGGESTIONS');
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_('SUGGESTIONS'),'index.php?option='.$this->_option.a.'task=suggestions');
		
		// Push some styles to the template
		$this->getStyles();
		
		// Incoming
		$suggester  = array_map('trim', $_POST['suggester']);
		$suggestion = array_map('trim', $_POST['suggestion']);
		$suggester  = array_map(array('FeedbackController','purifyText'), $suggester);
		$suggestion = array_map(array('FeedbackController','purifyText'), $suggestion);
	
		// Make sure email address is valid
		$validemail = $this->check_validEmail($suggester['email']);
	
		// Prep a new math question and hash in case any form validation fails
		$suggestion['operand1'] = rand(0,10);
		$suggestion['operand2'] = rand(0,10);
		$sum = $suggestion['operand1'] + $suggestion['operand2'];
		$suggestion['key'] = $this->generate_hash($sum,date('j'));
	
		if ($suggester['name'] && $suggestion['for'] && $suggestion['idea'] && $validemail) {			
			// Are the logged in?
			$juser =& JFactory::getUser();
			if (!$juser->get('guest')) {
				$verified = 1;
			} else {
				$verified = 0;
			}
			
			// Are the logged in?
			if ($juser->get('guest')) {
				// No - don't trust user
				// Check CAPTCHA
				$key = JRequest::getInt( 'krhash', 0 );
				$answer = JRequest::getInt( 'answer', 0 );
				$answer = $this->generate_hash($answer,date('j'));

				if ($answer != $key) {
					echo FeedbackHtml::suggestions($title, $this->_option, $suggester, $suggestion, 3, $verified);
					return;
				}
			}
			
			// Get user's IP and domain
			$ip = $this->ip_address();
			$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			
			// Quick spam filter
			$spam = $this->detect_spam($suggestion['idea'], $ip);
			if ($spam) {
				// Output form with error messages
				echo FeedbackHtml::suggestions($title, $this->_option, $suggester, $suggestion, 1, $verified);
				return;
			}
			
			// Get some email settings
			$xhub =& XFactory::getHub();
			
			$admin   = $xhub->getCfg('hubSupportEmail');
			$subject = $xhub->getCfg('hubShortName').' '.JText::_('SUGGESTIONS');
			$from    = $xhub->getCfg('hubShortName').' '.JText::_('SUGGESTIONS_FORM');
			$hub     = array('email' => $suggester['email'], 'name' => $from);
			
			// Generate e-mail message
			$message  = ($verified == 1) ? JText::_('VERIFIED_USER').r.n : '';
			$message .= ($suggester['login']) ? JText::_('USERNAME').': '. $suggester['login'] .r.n : '';
			$message .= JText::_('NAME').': '. $suggester['name'] .r.n;
			$message .= JText::_('AFFILIATION').': '. $suggester['org'] .r.n;
			$message .= JText::_('EMAIL').': '. $suggester['email'] .r.n;
			$message .= JText::_('FOR').': '. $suggestion['for'] .r.n;
			$message .= JText::_('IDEA').': '. $suggestion['idea'] .r.n;
	
			// Send e-mail
			ximport('xhubhelper');
			XHubHelper::send_email($admin, $subject, $message);
			
			// Get their browser and OS
			list( $os, $os_version, $browser, $browser_ver ) = $this->browsercheck($_SERVER['HTTP_USER_AGENT']);
		
			// Create new support ticket
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.ticket.php' );
			
			$data = array();
			$data['id']        = NULL;
			$data['status']    = 0;
			$data['created']   = date( "Y-m-d H:i:s" );
			$data['login']     = $suggester['login'];
			$data['severity']  = 'normal';
			$data['owner']     = NULL;
			$data['category']  = 'Suggestion';
			$data['summary']   = $suggestion['for'];
			$data['report']    = $suggestion['idea'];
			$data['resolved']  = NULL;
			$data['email']     = $suggester['email'];
			$data['name']      = $suggester['name'];
			$data['os']        = $os .' '. $os_version;
			$data['browser']   = $browser .' '. $browser_ver;
			$data['ip']        = $ip;
			$data['hostname']  = $hostname;
			$data['uas']       = $_SERVER['HTTP_USER_AGENT'];
			$data['referrer']  = NULL;
			$data['cookies']   = (!empty($_COOKIE['sessioncookie'])) ? 1 : 0;
			$data['instances'] = 1;
			$data['section']   = 1;
			
			$database =& JFactory::getDBO();
			
			$row = new SupportTicket( $database );
			if (!$row->bind( $data )) {
				$err = $row->getError();
			}
			if (!$row->check()) {
				$err = $row->getError();
			}
			if (!$row->store()) {
				$err = $row->getError();
			}

			// Output Thank You message
			echo FeedbackHtml::suggestionsThanks($title, $this->_option, $suggestion['for']);
		} else {
			// Error
			$err = 1;
			if ($validemail == 0) {
				$err = 2;
			}
		
			// Output form with error messages
			echo FeedbackHtml::suggestions($title, $this->_option, $suggester, $suggestion, $err);
		}
	}

	//-----------

	protected function sendreport() 
	{
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.ticket.php' );

		// Incoming
		$no_html  = JRequest::getInt( 'no_html', 0 );
		$reporter = array_map('trim', $_POST['reporter']);
		$problem  = array_map('trim', $_POST['problem']);
		$reporter = array_map(array('FeedbackController','purifyText'), $reporter);
		$problem  = array_map(array('FeedbackController','purifyText'), $problem);
	
		// Make sure email address is valid
		$validemail = $this->check_validEmail($reporter['email']);

		// Set page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_('REPORT_PROBLEMS');
		if (!$no_html) {
			$document =& JFactory::getDocument();
			$document->setTitle( $title );
			
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
			}
			$pathway->addItem(JText::_('REPORT_PROBLEMS'),'index.php?option='.$this->_option.a.'task=report_problems');
		}
	
		// Prep a new math question and hash in case any form validation fails
		$problem['operand1'] = rand(0,10);
		$problem['operand2'] = rand(0,10);
		$sum = $problem['operand1'] + $problem['operand2'];
		$problem['key'] = $this->generate_hash($sum,date('j'));
	
		$juser =& JFactory::getUser();
		$verified = (!$juser->get('guest')) ? 1 : 0;
	
		// Check for some required fields
		if (!$reporter['name'] || !$reporter['email'] || !$validemail) {
			// Output form with error messages
			echo FeedbackHtml::report( $this->_option, 'report_problems', $title, $reporter, $problem, 2, $verified );
			//echo FeedbackHtml::reportForm($this->_option, $reporter, $problem, 1, $verified);
			return;
		}
		
		// Get the user's IP
		$ip = $this->ip_address();
		$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			
		// Are the logged in?
		if ($juser->get('guest')) {
			// No - don't trust user
			// Check CAPTCHA
			$key = JRequest::getVar( 'krhash', 0 );
			$answer = JRequest::getInt( 'answer', 0 );
			$answer = $this->generate_hash($answer,date('j'));
				
			// Quick spam filter
			$spam = $this->detect_spam($problem['long'], $ip);

			if ($answer != $key || $spam) {
				if ($no_html) {
					// Output error messages (AJAX)
					echo FeedbackHtml::reportError();
					return;
				} else {
					// Output form with error messages
					echo FeedbackHtml::report( $this->_option, 'report_problems', $title, $reporter, $problem, 3, $verified );
					//echo FeedbackHtml::reportForm($this->_option, $reporter, $problem, 3);
					return;
				}
			}
		}

		// Get user's city, region and location based on ip
		$source_city    = 'unknown';
		$source_region  = 'unknown';
		$source_country = 'unknown';
		
		ximport('xgeoutils');
		$gdb =& GeoUtils::getGODBO();
		if (is_object($gdb)) {
			$gdb->setQuery( "SELECT countrySHORT, countryLONG, ipREGION, ipCITY FROM ipcitylatlong WHERE INET_ATON('$ip') BETWEEN ipFROM and ipTO" );
			$rows = $gdb->loadObjectList();
			if ($rows && count($rows) > 0) {
				$source_city    = $rows[0]->ipCITY;
				$source_region  = $rows[0]->ipREGION;
				$source_country = $rows[0]->countryLONG;
			}
		}
		
		// Cut suggestion at 70 characters
		if (!$problem['short'] && $problem['long']) {
			$problem['short'] = substr($problem['long'], 0, 70);
			if (strlen($problem['short']) >=70 ) {
				$problem['short'] .= '...';
			}
		}
		
		$tool = $this->getTool( $problem['referer'] );
		if ($tool) {
			$group = $this->getTicketGroup( trim($tool) );
		} else {
			$group = '';
		}
		
		// Build an array of ticket data
		$data = array();
		$data['id']        = NULL;
		$data['status']    = 0;
		$data['created']   = date( "Y-m-d H:i:s" );
		$data['login']     = $reporter['login'];
		$data['severity']  = 'normal';
		$data['owner']     = NULL;
		$data['category']  = (isset($problem['topic'])) ? $problem['topic'] : '';
		$data['summary']   = htmlentities($problem['short'], ENT_COMPAT, 'UTF-8');
		$data['report']    = htmlentities($problem['long'], ENT_COMPAT, 'UTF-8');
		$data['resolved']  = NULL;
		$data['email']     = $reporter['email'];
		$data['name']      = $reporter['name'];
		$data['os']        = $problem['os'] .' '. $problem['osver'];
		$data['browser']   = $problem['browser'] .' '. $problem['browserver'];
		$data['ip']        = $ip;
		$data['hostname']  = $hostname;
		$data['uas']       = $_SERVER['HTTP_USER_AGENT'];
		$data['referrer']  = $problem['referer'];
		$data['cookies']   = (!empty($_COOKIE['sessioncookie'])) ? 1 : 0;
		$data['instances'] = 1;
		$data['section']   = 1;
		$data['group']     = $group;
		
		// Initiate class and bind data to database fields
		$database =& JFactory::getDBO();
		
		$row = new SupportTicket( $database );
		if (!$row->bind( $data )) {
			$err = $row->getError();
		}
		// Check the data
		if (!$row->check()) {
			$err = $row->getError();
		}
		// Save the data
		if (!$row->store()) {
			$err = $row->getError();
		}
		// Retrieve the ticket ID
		if (!$row->id) {
			$row->getId();
		}
		
		// Get some email settings
		$xhub =& XFactory::getHub();
		$admin   = $xhub->getCfg('hubSupportEmail');
		$subject = $xhub->getCfg('hubShortName').' '.JText::_('SUPPORT').', '.JText::sprintf('TICKET_NUMBER',$row->id);
		$from    = $xhub->getCfg('hubShortName').' web-robot';
		$hub     = array('email' => $reporter['email'], 'name' => $from);
		
		// Generate e-mail message
		$message  = (!$juser->get('guest')) ? JText::_('VERIFIED_USER').r.n.r.n : '';
		$message .= ($reporter['login']) ? JText::_('USERNAME').': '. $reporter['login'] .r.n : '';
		$message .= JText::_('NAME').': '. $reporter['name'] .r.n;
		$message .= JText::_('AFFILIATION').': '. $reporter['org'] .r.n;
		$message .= JText::_('EMAIL').': '. $reporter['email'] .r.n;
		$message .= JText::_('IP_HOSTNAME').': '. $ip .' ('.$hostname.')' .r.n;
		$message .= JText::_('REGION').': '.$source_city.', '.$source_region.', '.$source_country .r.n.r.n;
		$message .= JText::_('OS').': '. $problem['os'] .' '. $problem['osver'] .r.n;
		$message .= JText::_('BROWSER').': '. $problem['browser'] .' '. $problem['browserver'] .r.n;
		$message .= JText::_('UAS').': '. $_SERVER['HTTP_USER_AGENT'] .r.n;
		$message .= JText::_('COOKIES').': ';
		$message .= (!empty($_COOKIE['sessioncookie'])) ? JText::_('COOKIES_ENABLED').r.n : JText::_('COOKIES_DISABLED').r.n;
		$message .= JText::_('REFERRER').': '. $problem['referer'] .r.n;
		$message .= ($problem['tool']) ? JText::_('TOOL').': '. $problem['tool'] .r.n.r.n : r.n;
		//$message .= JText::_('TOPIC').': '. $problem['topic'] .r.n;
		//$message .= JText::_('PROBLEM').': '. stripslashes($problem['short']) .r.n;
		$message .= JText::_('PROBLEM_DETAILS').': '. stripslashes($problem['long']) .r.n;

		// Send e-mail
		ximport('xhubhelper');
		XHubHelper::send_email($admin, $subject, $message);

		// Get "thank you" message
		$msg = '';
		//$database->setQuery( "SELECT content FROM #__modules WHERE title='Support Message'" );
		//$msg = $database->loadResult();
		if (!$no_html) {
			// Push some styles to the template
			$this->getStyles();
		}
		// Output Thank You message
		echo FeedbackHtml::reportThanks($msg, $row->id, $no_html, $this->_option);
		//echo 'Testing. Temporarily down.';
	}

	private function getTool( $referrer ) 
	{
		$tool = '';
		
		if (!$referrer) {
			return $tool;
		}
		
		if (substr($referrer,0,3) == '/mw') {
			$bits = explode('/', $referrer);
			if ($bits[2] == 'invoke') {
				$longbits = explode('?',$bits[3]);
				if (is_array($longbits)) {
					$tool = trim($longbits[0]);
				} else {
					$tool = trim($bits[3]);
				}
			} else if ($bits[2] == 'view') {
				$longbits = explode('=',$bits[3]);
				if (is_array($longbits)) {
					$tool = trim(end($longbits));
				} else {
					$tool = trim($bits[3]);
				}
			}
			if (strstr($tool,'_r')) {
				$version = strrchr($tool,'_r');
				$tool = str_replace($version, '', $tool);
			}
			if (strstr($tool,'_dev')) {
				$version = strrchr($tool,'_dev');
				$tool = str_replace($version, '', $tool);
			}
		} else if (substr($referrer,0,6) == '/tools' || substr($referrer,0,10) == '/resources') {
			$bits = explode('/', $referrer);
			$tool = (isset($bits[2])) ? trim($bits[2]) : '';
		} else if (substr($referrer,0,4) == 'http') {
			$bits = explode('/', $referrer);
			$tool = (isset($bits[4])) ? trim($bits[4]) : '';
		}

		return $tool;
	}

	private function getTicketGroup($tool) 
	{
		// Do we have a tool?
		if (!$tool) {
			return '';
		}
		
		$database =& JFactory::getDBO();
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
		$resource = new ResourcesResource( $database );
		$tool = str_replace(':','-',$tool);
		$resource->loadAlias( $tool );
		
		if (!$resource || $resource->type != 7) {
			return '';
		}
			
		// Get tags on the tools
		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.tags.php' );
		$rt = new ResourcesTags($database);
		$tags = $rt->getTags( $resource->id, 0, 0, 1 );

		if (!$tags) {
			return 'app-'.$tool;
		}

		// Get tag/group associations
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
		$tt = new TagsGroup( $database );
		$tgas = $tt->getRecords();
			
		if (!$tgas) {
			return 'app-'.$tool;
		}
		
		/*$ts = array();
		foreach ($tgas as $tga) 
		{
			$ts[] = $tga->tag;
			$tsa[$tga->tag] = $tga->cn;
		}

		// Loop through the tags and see if one of them is a tag/group
		foreach ($tags as $tag) 
		{
			if (in_array($tag->tag, $ts)) {
				// We found one! So set the group
				return $tsa[$tag->tag];
				break;
			}
		}*/
		// Loop through the tags and make a flat array so we can search quickly
		$ts = array();
		foreach ($tags as $tag) 
		{
			$ts[] = $tag->tag;
		}
		// Loop through the tag/group array and see if one of them is in the tags list
		foreach ($tgas as $tga) 
		{
			if (in_array($tga->tag, $ts)) {
				// We found one! So set the group
				return $tga->cn;
				break;
			}
		}
		return 'app-'.$tool;
	}

	//----------------------------------------------------------
	//  Image handling
	//----------------------------------------------------------

	public function upload()
	{
		// Load the component config
		$config = $this->config;

		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('FEEDBACK_NO_ID') );
			$this->img( '', $id );
			return;
		}
		
		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			$this->setError( JText::_('FEEDBACK_NO_FILE') );
			$this->img( '', $id );
			return;
		}

		// Build upload path
		ximport('fileuploadutils');
		$dir  = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		if (substr($config->get('uploadpath'), -1, 1) == DS) {
			$path = substr($config->get('uploadpath'), 0, (strlen($config->get('uploadpath')) - 1));
		}
		$path .= $config->get('uploadpath').DS.$dir;
		
		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
				$this->img( '', $id );
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);
		
		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('ERROR_UPLOADING') );
			$file = $curfile;
		} else {
			// Do we have an old file we're replacing?
			$curfile = JRequest::getVar( 'currentfile', '' );
			
			if ($curfile != '') {
				// Yes - remove it
				if (file_exists($path.DS.$curfile)) {
					if (!JFile::delete($path.DS.$curfile)) {
						$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
						$this->img( $file['name'], $id );
						return;
					}
				}
			}

			$file = $file['name'];
		}

		// Push through to the image view
		$this->img( $file, $id );
	}

	//-----------

	protected function delete()
	{
		// Load the component config
		$config = $this->config;
		
		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('FEEDBACK_NO_ID') );
			$this->img( '', $id );
		}
		
		// Incoming file
		$file = JRequest::getVar( 'file', '' );
		if (!$file) {
			$this->setError( JText::_('FEEDBACK_NO_FILE') );
			$this->img( '', $id );
		}
		
		// Build the file path
		ximport('fileuploadutils');
		$dir  = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		if (substr($config->get('uploadpath'), -1, 1) == DS) {
			$path = substr($config->get('uploadpath'), 0, (strlen($config->get('uploadpath')) - 1));
		}
		$path .= $config->get('uploadpath').DS.$dir;

		if (!file_exists($path.DS.$file) or !$file) { 
			$this->setError( JText::_('FILE_NOT_FOUND') ); 
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$file)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
				$this->img( $file, $id );
			}

			$file = '';
		}
	
		// Push through to the image view
		$this->img( $file, $id );
	}

	//-----------

	protected function img( $file='', $id=0 )
	{
		// Load the component config
		$config = $this->config;
		
		// Get the app
		$app =& JFactory::getApplication();
		
		// Do have an ID or do we need to get one?
		if (!$id) {
			$id = JRequest::getInt( 'id', 0 );
		}
		ximport('fileuploadutils');
		$dir = FileUploadUtils::niceidformat( $id );
		
		// Do we have a file or do we need to get one?
		$file = ($file) 
			  ? $file 
			  : JRequest::getVar( 'file', '' );
			  
		// Build the directory path
		$path = $config->get('uploadpath').DS.$dir;

		FeedbackHtml::writeImage( $app, $this->_option, $config->get('uploadpath'), $config->get('defaultpic'), $dir, $file, $path, $id, $this->getErrors() );
	}

	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------

	private function getUser() 
	{
		$juser =& JFactory::getUser();
		
		$user = array();
		$user['login'] = '';
		$user['name']  = '';
		$user['org']   = '';
		$user['email'] = '';
		$user['uid']   = '';
		
		if (!$juser->get('guest')) {
			ximport('xprofile');
			
			$profile = new XProfile();
			$profile->load( $juser->get('id') );
			
			if (is_object($profile)) {
				$user['login'] = $profile->get('username');
				$user['name']  = $profile->get('name');
				$user['org']   = $profile->get('organization');
				$user['email'] = $profile->get('email');
				$user['uid']   = $profile->get('uidNumber');
			}
		}
		return $user;
	}
	
	//-----------

	private function detect_spam($text, $ip)
	{
		// Spammer IPs (banned)
		$ips = $this->config->get('blacklist');
		if ($ips) {
			$bl = explode(',',$ips);
			array_map('trim',$bl);
		} else {
			$bl = array();
		}
		
		// Bad words
		$words = $this->config->get('badwords');
		if ($words) {
			$badwords = explode(',', $words);
			array_map('trim',$badwords);
		} else {
			$badwords = array();
		}

		// Build an array of patterns to check againts
		$patterns = array('/\[url=(.*?)\](.*?)\[\/url\]/s', '/\[url=(.*?)\[\/url\]/s');
		foreach ($badwords as $badword)
		{
			if (!empty($badword))
			    	$patterns[] = '/(.*?)'.trim($badword).'(.*?)/s';
		}
	
		// Set the splam flag
		$spam = false;
	
		// Check the text against bad words
		foreach ($patterns as $pattern)
		{
			preg_match_all( $pattern, $text, $matches );
			if (count($matches[0]) >=1) {
				$spam = true;
			}
		}
		
		// Check the number of links in the text
		// Very unusual to have 5 or more - usually only spammers
		if (!$spam) {
			$num = substr_count($text, 'http://');
			if ($num >= 5) { // too many links
        	    $spam = true;
			}
		}
		
		// Check the user's IP against the blacklist
		if (in_array($ip, $bl)) {
			$spam = true;
		}
		
		return $spam;
	}

	//-----------

	private function generate_hash($input, $day)
	{
		// Add date:
		$input .= $day . date('ny');

		// Get MD5 and reverse it
		$enc = strrev(md5($input));
	
		// Get only a few chars out of the string
		$enc = substr($enc, 26, 1) . substr($enc, 10, 1) . substr($enc, 23, 1) . substr($enc, 3, 1) . substr($enc, 19, 1);
	
		return $enc;
	}

	//-----------

	/*private function send_email($email, $subject, $message) 
	{
        $xhub =& XFactory::getHub();

		$contact_email = $xhub->getCfg('hubSupportEmail');
		$contact_name  = $xhub->getCfg('hubShortName');

	    if (!empty($contact_email)) {
			$args = "-f '" . $contact_email . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
			$headers .= 'From: ' . $contact_name .' <'. $contact_email . ">\n";
			$headers .= 'Reply-To: ' . $contact_name .' <'. $contact_email . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: '. $contact_name .n;
			if (mail($email, $subject, $message, $headers, $args)) {
				return true;
			}
		}
		return false;
	}*/

	//-----------

	private function check_validLogin($login) 
	{
		if (eregi("^[_0-9a-zA-Z]+$", $login)) {
			return(1);
		} else {
			return(0);
		}
	}

	//-----------

	private function check_validEmail($email) 
	{
		if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
			return(1);
		} else {
			return(0);
		}
	}

	//-----------

	private function purifyText( &$text ) 
	{
		$text = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = strip_tags( $text );

		return $text;
	}

	//-----------

	private function server($index = '')
	{		
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}
		
		return $_SERVER[$index];
	}
	
	//-----------
	
	private function valid_ip($ip)
	{
		return ( ! preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
	}
	
	//-----------

	private function ip_address()
	{
		if ($this->server('REMOTE_ADDR') AND $this->server('HTTP_CLIENT_IP')) {
			 $ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($this->server('REMOTE_ADDR')) {
			 $ip_address = $_SERVER['REMOTE_ADDR'];
		} elseif ($this->server('HTTP_CLIENT_IP')) {
			 $ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($this->server('HTTP_X_FORWARDED_FOR')) {
			 $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		if ($ip_address === FALSE) {
			$ip_address = '0.0.0.0';
			return $ip_address;
		}
		
		if (strstr($ip_address, ',')) {
			$x = explode(',', $ip_address);
			$ip_address = end($x);
		}
		
		if (!$this->valid_ip($ip_address)) {
			$ip_address = '0.0.0.0';
		}
				
		return $ip_address;
	}

	//-----------

	private function browsercheck($sagent) 
	{
		unset($os);
		unset($os_version);
		unset($browser);
		unset($browser_ver);

		// Determine browser and version	
		if (ereg( 'Opera ([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Opera';
		} elseif (ereg( 'Camino/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Camino';
		} elseif (ereg( 'Shiira/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Shiira';
		} elseif (ereg( 'Safari/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			switch($browser_ver)
			{
				case '85.5':    $browser_ver = '1.0';   break;
				case '85.7':    $browser_ver = '1.0.2'; break;
				case '85.8':    $browser_ver = '1.0.3'; break;
				case '125':     $browser_ver = '1.2';   break;
				case '125.7':   $browser_ver = '1.2.2'; break;
				case '125.8':   $browser_ver = '1.2.2'; break;
				case '125.9':   $browser_ver = '1.2.3'; break;
				case '125.11':  $browser_ver = '1.2.4'; break;
				case '125.12':  $browser_ver = '1.2.4'; break;
				case '312':     $browser_ver = '1.3';   break;
				case '312.3':   $browser_ver = '1.3.1'; break;
				case '412':     $browser_ver = '2.0';   break;
				case '412.2':   $browser_ver = '2.0';   break;
				case '412.2.2': $browser_ver = '2.0';   break;
				case '412.5':   $browser_ver = '2.0.1'; break;
				case '522.11.3': $browser_ver = '3.0'; break;
				default: break;
			}
			$browser = 'Safari';
		} elseif (ereg( 'iCab ([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'iCab';
		} elseif (ereg( 'MSIE ([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Internet Explorer';
		} elseif (ereg( 'Firefox/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Firefox';
		} elseif (ereg( 'Netscape/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Netscape';
		} elseif (ereg( 'Mozilla/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Mozilla';
		} else {
			$browser_ver = 0;
			$browser = 'Other';
		}

		// Determine platform
		/*
		packs the os array
		use this order since some navigator user agents will put 'macintosh' in the navigator user agent string
		which would make the nt test register true
		*/
		$a_mac = array( 'mac68k', 'macppc' );// this is not used currently
		// same logic, check in order to catch the os's in order, last is always default item
		$a_unix = array( 'unixware', 'solaris', 'sunos', 'sun4', 'sun5', 'suni86', 'sun', 
			'freebsd', 'openbsd', 'bsd' , 'irix5', 'irix6', 'irix', 'hpux9', 'hpux10', 'hpux11', 'hpux', 'hp-ux', 
			'aix1', 'aix2', 'aix3', 'aix4', 'aix5', 'aix', 'sco', 'unixware', 'mpras', 'reliant',
			'dec', 'sinix', 'unix' );
		// only sometimes will you get a linux distro to id itself...
		$a_linux = array( 'kanotix', 'ubuntu', 'mepis', 'debian', 'suse', 'redhat', 'slackware', 'mandrake', 'gentoo', 'linux' );
		$a_linux_process = array ( 'i386', 'i586', 'i686' );// not use currently
		// note, order of os very important in os array, you will get failed ids if changed
		$a_os = array( 'beos', 'os2', 'amiga', 'webtv', 'mac', 'nt', 'win', $a_unix, $a_linux );

		//os tester
		for ( $i = 0; $i < count( $a_os ); $i++ )
		{
			//unpacks os array, assigns to variable
			$s_os = $a_os[$i];
		
			//assign os to global os variable, os flag true on success
			//!stristr($browser_string, "linux" ) corrects a linux detection bug
			if ( !is_array( $s_os ) && stristr( $sagent, $s_os ) && !stristr( $sagent, "linux" ) )
			{
				$os = $s_os;
	
				switch ( $os )
				{
					case 'win':
						$os = 'Windows';
						if ( stristr( $sagent, '95' ) ) {
							$os_version = '95';
						}
						elseif ( ( stristr( $sagent, '9x 4.9' ) ) || ( stristr( $sagent, 'me' ) ) )
						{
							$os_version = 'me';
						}
						elseif ( stristr( $sagent, '98' ) )
						{
							$os_version = '98';
						}
						elseif ( stristr( $sagent, '2000' ) ) // windows 2000, for opera ID
						{
							$os_version = 5.0;
							$os .= ' NT';
						}
						elseif ( stristr( $sagent, 'xp' ) ) // windows 2000, for opera ID
						{
							$os_version = 5.1;
							$os .= ' NT';
						}
						elseif ( stristr( $sagent, '2003' ) ) // windows server 2003, for opera ID
						{
							$os_version = 5.2;
							$os .= ' NT';
						}
						elseif ( stristr( $sagent, 'ce' ) ) // windows CE
						{
							$os_version = 'ce';
						}
						break;
					case 'nt':
						$os = 'Windows NT';
						if ( stristr( $sagent, 'nt 5.2' ) ) // windows server 2003
						{
							$os_version = 5.2;
						}
						elseif ( stristr( $sagent, 'nt 5.1' ) || stristr( $sagent, 'xp' ) ) // windows xp
						{
							//$os_version = 5.1;
							$os_version = 'XP';
							$os = 'Windows';
						}
						elseif ( stristr( $sagent, 'nt 5' ) || stristr( $sagent, '2000' ) ) // windows 2000
						{
							//$os_version = 5.0;
							$os_version = '2000';
							$os = 'Windows';
						}
						elseif ( stristr( $sagent, 'nt 4' ) ) // nt 4
						{
							$os_version = 4;
						}
						elseif ( stristr( $sagent, 'nt 3' ) ) // nt 4
						{
							$os_version = 3;
						} else {
							$os_version = '';
						}
						break;
					case 'mac':
						$os = 'Mac OS';
						if ( stristr( $sagent, 'os x' ) ) 
						{
							$os_version = 10;
						}
						// this is a crude test for os x, since safari, camino, ie 5.2, & moz >= rv 1.3 
						// are only made for os x
						elseif ( ( $browser == 'Safari' ) || ( $browser == 'Camino' ) || ( $browser == 'Shiira' ) || 
							( ( $browser == 'Mozilla' ) && ( $browser_ver >= 1.3 ) ) || 
							( ( $browser == 'Internet Explorer' ) && ( $browser_ver >= 5.2 ) ) )
						{
							$os_version = 10;
						}
						break;
					default:
						break;
				}
				break;
			}
			// check that it's an array, check it's the second to last item 
			// in the main os array, the unix one that is
			elseif ( is_array( $s_os ) && ( $i == ( count( $a_os ) - 2 ) ) )
			{
				for ($j = 0; $j < count($s_os); $j++)
				{
					if ( stristr( $sagent, $s_os[$j] ) )
					{
						$os = 'Unix'; // if the os is in the unix array, it's unix, obviously...
						$os_version = ( $s_os[$j] != 'unix' ) ? $s_os[$j] : ''; // assign sub unix version from the unix array
						break;
					}
				}
			} 
			// check that it's an array, check it's the last item 
			// in the main os array, the linux one that is
			elseif ( is_array( $s_os ) && ( $i == ( count( $a_os ) - 1 ) ) ) {
				for ($j = 0; $j < count($s_os); $j++)
				{
					if ( stristr( $sagent, $s_os[$j] ) ) {
						$os = 'Linux';
						// assign linux distro from the linux array, there's a default
						//search for 'lin', if it's that, set version to ''
						$os_version = ( $s_os[$j] != 'linux' ) ? $s_os[$j] : '';
						break;
					}
				}
			} 
		}

		$os = ($os) ? $os : 'unknown';
		$os_version = ($os_version) ? $os_version : '';
		$browser = ($browser) ? $browser : 'unknown';
		$browser_ver = ($browser_ver) ? $browser_ver : '';

		// pack the os data array for return to main function
		$data = array( $os, $os_version, $browser, $browser_ver );
		return $data;
	}
}
?>
