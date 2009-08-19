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

class AnswersController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $note	= NULL;

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
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$banking = $upconfig->get('bankAccounts');
		$this->banking = $banking;
		
		if ($banking) {
			ximport( 'bankaccount' );
		}
		
		// Get the component parameters
		$aconfig = new AnswersConfig( $this->_option );
		$this->config = $aconfig;
		$this->infolink =  (isset($this->config->parameters['infolink'])) ? $this->config->parameters['infolink'] : '/kb/points/';
		$this->showcomments =  (isset($this->config->parameters['showcomments'])) ? $this->config->parameters['showcomments'] : '1';
	
	
		switch ( $this->getTask() ) 
		{
			case 'new':         $this->create();      break;
			case 'savea':       $this->savea();       break;
			case 'saveq':       $this->saveq();       break;
			case 'answer':      $this->answer();      break;
			case 'tag':         $this->tag();         break;
			case 'question':    $this->question();    break;
			case 'accept':      $this->accept();      break;
			case 'myquestions': $this->myquestions(); break;
			case 'search':      $this->search();      break;
			//case 'start':       $this->start();       break;
			case 'delete':      $this->answer();      break;
			case 'delete_q':    $this->delete_q();    break;
			case 'rateitem':   	$this->rateitem();    break;
			case 'savereply':   $this->savereply();   break;
			case 'reply':      	$this->reply();  	  break;
			case 'math':      	$this->answer();  	  break;
			
			default: $this->search(); break;
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

	private function getStyles($option='', $css='')
	{
		ximport('xdocument');
		if ($option) {
			XDocument::addComponentStylesheet($option, $css);
		} else {
			XDocument::addComponentStylesheet($this->_option);
		}
	}

	
	//-----------
	
	private function getScripts($option='',$name='')
	{
		$document =& JFactory::getDocument();
		
		if ($option) {
			$name = ($name) ? $name : $option;
			if (is_file(JPATH_ROOT.DS.'components'.DS.$option.DS.$name.'.js')) {
				$document->addScript('components'.DS.$option.DS.$name.'.js');
			}
		} else {
			if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
				$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
			}
		}
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function login($msg='') 
	{
		// Set the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_('LOGIN');
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		
		echo AnswersHtml::div( AnswersHtml::hed( 2, $title ), 'full', 'content-header' );
		echo '<div class="main section">'.n;
		if ($msg) {
			echo AnswersHtml::warning( $msg );
		}
		ximport('xmodule');
		XModuleHelper::displayModules('force_mod');
		echo '</div><!-- / .main section -->'.n;
	
	}
	

	//-----------

	protected function start() 
	{		
		// Incoming
		$filters = array();
		$filters['limit']    = JRequest::getInt( 'limit', 25 );
		$filters['start']    = JRequest::getInt( 'limitstart', 0 );
		$filters['tag']      = JRequest::getVar( 'tag', '' );
		$filters['q']        = JRequest::getVar( 'q', '' );
		$filters['filterby'] = JRequest::getVar( 'filterby', '' );
		$filters['sortby']   = JRequest::getVar( 'sortby', 'rewards' );
		
		// Add the CSS to the template
		$this->getStyles();
		
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '. JText::_(strtoupper($this->_task)) : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );

		$database =& JFactory::getDBO();
		
		$aq = new AnswersQuestion( $database );
		//$BT = new BankTransaction( $database );
		
		// Get a record count
		$total = $aq->getCount( $filters );
		
		// Get records
		$results = $aq->getResults( $filters );
		
		// Did we get any results?
		if (count($results) > 0) {
			// Do some processing on the results
			for ($i=0; $i < count($results); $i++) 
			{
				$row =& $results[$i];
				$row->created = $this->mkt($row->created);
				$row->when = $this->timeAgo($row->created);
				$row->points = $row->points ? $row->points : 0;
				$row->reports = $this->get_reports($row->id, 'question');
	
				// Get tags on this question
				$tagging = new AnswersTags( $database );
				$row->tags = $tagging->get_tags_on_object($row->id, 0, 0, 0);
			}
		}
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}

		// Output HTML
		echo AnswersHtml::introduction($title, $results, $pageNav, $this->_option, $filters, $this->infolink, $this->banking);
	}
	
	//-----------
	
	private function savereply()
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		
		// Incoming
		$id      	= JRequest::getInt( 'referenceid', 0 );
		$rid 	 	= JRequest::getInt( 'rid', 0 );
		$ajax    	= JRequest::getInt( 'ajax', 0 );
		$category	= JRequest::getVar( 'category', '' );
		$when 		= date( 'Y-m-d H:i:s');
		
		// trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
		
		if (!$id && !$ajax) {
		
			$title  = JText::_(strtoupper($this->_name));
			//$title .= ($this->_task) ? ': '. JText::_(strtoupper($this->_task)) : '';
			// cannot proceed
			$document =& JFactory::getDocument();
			$document->setTitle( $title );
			
			echo AnswersHtml::hed(2, $title);
			echo AnswersHtml::error( JText::_('No Question ID found.') );
			return;
		}
		
		// is the user logged in?
		if ($juser->get('guest')) {
			$msg = 'Please login to post a comment';
			$this->login($msg);
			return;
		}
		
		if ($id && $category) {
			$row = new XComment( $database );
			if (!$row->bind( $_POST )) {
				echo AnswersHtml::alert( $row->getError() );
				exit();
			}
			
			// Perform some text cleaning, etc.
			$row->comment   = $this->purifyText($row->comment);
			$row->comment   = nl2br($row->comment);
			$row->anonymous = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
			$row->added   	= $when;
			$row->state     = 0;
			$row->added_by 	= $juser->get('id');
			
			// Check for missing (required) fields
			if (!$row->check()) {
				echo AnswersHtml::alert( $row->getError() );
				exit();
			}
			// Save the data
			if (!$row->store()) {
				echo AnswersHtml::alert( $row->getError() );
				exit();
			}
		}
	
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$rid);
	}
	
	//-----------
	
	private function reply()
	{	
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Retrieve a review or comment ID and category
		$id 	 = JRequest::getInt( 'id', 0 );
		$refid 	 = JRequest::getInt( 'refid', 0 );
		$cat 	 = JRequest::getVar( 'category', '' );
		
		// is the user logged in?
		if ($juser->get('guest')) {
			$msg = 'Please login to post a comment';
			$this->login($msg);
			return;
		}
		
		// Do we have an ID?
		if (!$id) {
			// cannot proceed
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		// Do we have a category?
		if (!$cat) {
			// cannot proceed
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id);
			return;
		}
		
			
		// Store the comment object in our registry
		$this->category = $cat;
		$this->referenceid = $refid;
		$this->qid = $id;
		$this->question();
		//$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id);	
	}
	
	//-----------
	
	private function rateitem()
	{		
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		
		$id 	 = JRequest::getInt( 'refid', 0 );
		$ajax 	 = JRequest::getInt( 'ajax', 0 );
		$cat 	 = JRequest::getVar( 'category', '' );
		$vote 	 = JRequest::getVar( 'vote', '' );
		$ip 	 = $this->ip_address();
		
		
		if(!$id) {
			// cannot proceed		
			return;
		}
		
		// is the user logged in?
		if ($juser->get('guest')) {
			$this->login( JText::_('PLEASE_LOGIN_TO_VOTE') );
			return;
		}
		else {
			// load answer
			$row = new AnswersResponse( $database );
			$row->load( $id );
			$qid = $row->qid;
			
			$al = new AnswersLog( $database );
			$voted = $al->checkVote( $id, $ip);
	
			
			if(!$voted && $vote && $row->created_by != $juser->get('username')) {
							
				// record if it was helpful or not
				if ($vote == 'yes'){
					$row->helpful++;
				} elseif($vote == 'no') {
					$row->nothelpful++;
				}
				
				if (!$row->store()) {
					$this->_error = $row->getError();
					return;
				}
				
				// Record user's vote (old way)
				$al->rid = $row->id;
				$al->ip = $ip;
				$al->helpful = $vote;
				if (!$al->check()) {
					$this->setError( $al->getError() );
					return;
				}
				if (!$al->store()) {
					$this->setError( $al->getError() );
					return;
				}
				
				// Record user's vote (new way)
				if($cat) {
					require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'vote.class.php' );
					$v = new Vote( $database );
					$v->referenceid = $row->id;
					$v->category = $cat;
					$v->voter = $juser->get('id');
					$v->ip = $ip;
					$v->voted = date( 'Y-m-d H:i:s', time() );
					$v->helpful = $vote;
					if (!$v->check()) {
						$this->setError( $v->getError() );
						return;
					}
					if (!$v->store()) {
						$this->setError( $v->getError() );
						return;
					}
				}
			}
						
			// update display
			if ($ajax) {
				$response = $row->getResponse( $id, $ip);
				echo AnswersHtml::rateitem($response[0], $juser, $this->_option, $qid);
			} else {				
				$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$qid);
			}
		}
	}
	
	


	//-----------

	protected function myquestions() 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Incoming
		$filters = array();
		$filters['limit']    = JRequest::getInt( 'limit', 25 );
		$filters['start']    = JRequest::getInt( 'limitstart', 0 );
		$filters['tag']      = JRequest::getVar( 'tag', '' );
		$filters['q']        = JRequest::getVar( 'q', '' );
		$filters['filterby'] = JRequest::getVar( 'filterby', 'all' );
		$filters['sortby']   = JRequest::getVar( 'sortby', 'rewards' );
		$filters['interest'] = JRequest::getVar( 'interest', 0 );
		$filters['assigned'] = JRequest::getVar( 'assigned', 0 );
		$filters['interest'] = ($filters['assigned'] == 1) ? 0 : $filters['interest']; 
		
		// is the user logged in?
		if ($juser->get('guest')) {
			$msg = 'Please login to view your questions';
			$this->login($msg);
			return;
		}	
			
		// Get questions of interest
		if($filters['interest']) {
			
			require_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'members.tags.php' );
			
			// Get tags of interest
			$mt = new MembersTags( $database );
			$mytags  = $mt->get_tag_string( $juser->get('id') );

			//$filters['tag'] = ($filters['tag'] && strstr(strtolower($mytags), strtolower($filters['tag']))) ? $filters['tag'] : $mytags;
			$filters['tag'] = ($filters['tag']) ? $filters['tag'] : $mytags;
			
			if(!$filters['tag']) {
				$filters['filterby']   = 'none';
			}		
			$filters['mine'] = 0;
		} 
		
		// Get assigned questions
		if($filters['assigned']) {
			
			require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.author.php' );
			
			// what tools did this user contribute?
			$TA = new ToolAuthor($database); 
			$tools = $TA->getToolContributions($juser->get('id'));
			$mytooltags = '';
			if($tools) {
				foreach($tools as $tool) {
					$mytooltags .= 'tool'.$tool->toolname.',';
				}
			}
			
			//$filters['tag'] = ($filters['tag'] && preg_match( "/".$filters['tag']."/", $mytooltags)) ? $filters['tag'] : $mytooltags;
			$filters['tag'] = ($filters['tag']) ? $filters['tag'] : $mytooltags;
			
			if(!$filters['tag']) {
				$filters['filterby']   = 'none';
			}	
			
				
			$filters['mine'] = 0;
		}
		
		 
		if(!$filters['assigned'] && !$filters['interest']) {
		
			$filters['mine'] = 1;
		}
		
		// Add the CSS to the template
		$this->getStyles();
		
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '. JText::_(strtoupper($this->_task)) : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		$aq = new AnswersQuestion( $database );
		//$BT = new BankTransaction( $database );
				
		// Get records
		$results = $aq->getResults( $filters );
		
		// Get a record count
		$total = $aq->getCount( $filters );
		
		// Did we get any results?
		if (count($results) > 0) {
			// Do some processing on the results
			for ($i=0; $i < count($results); $i++) 
			{
				$row =& $results[$i];
				$row->created = $this->mkt($row->created);
				$row->when = $this->timeAgo($row->created);
				$row->points = $row->points ? $row->points : 0;
				$row->reports = $this->get_reports($row->id, 'question');
	
				// Get tags on this question
				$tagging = new AnswersTags( $database );
				$row->tags = $tagging->get_tags_on_object($row->id, 0, 0, 0);
			}
		}
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task );
		
		$filters['mine'] = 1;

		// Output HTML
		echo AnswersHtml::search($title, $results, $pageNav, $this->_option, $filters, $this->infolink, $this->banking, $this->_task);
	}

	//-----------

	private function search()
	{
		// Incoming
		$filters = array();
		$filters['limit']    = JRequest::getInt( 'limit', 25 );
		$filters['start']    = JRequest::getInt( 'limitstart', 0 );
		$filters['tag']      = JRequest::getVar( 'tags', '' );
		$filters['tag']      = $filters['tag'] ? $filters['tag'] : JRequest::getVar( 'tag', '' );
		$filters['q']        = JRequest::getVar( 'q', '' );
		$filters['filterby'] = JRequest::getVar( 'filterby', '' );
		$filters['sortby']   = JRequest::getVar( 'sortby', 'rewards' );
		
		// Add the CSS to the template
		$this->getStyles();
		
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '. JText::_(strtoupper($this->_task)) : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );

		$database =& JFactory::getDBO();
		
		$aq = new AnswersQuestion( $database );
		//$BT = new BankTransaction( $database );
		
		// Get a record count
		$total = $aq->getCount( $filters );
		
		// Get records
		$results = $aq->getResults( $filters );
		
		// Did we get any results?
		if (count($results) > 0) {
			// Do some processing on the results
			for ($i=0; $i < count($results); $i++) 
			{
				$row =& $results[$i];
				$row->created = $this->mkt($row->created);
				$row->when = $this->timeAgo($row->created);
				$row->points = $row->points ? $row->points : 0;
				$row->reports = $this->get_reports($row->id, 'question');
	
				// Get tags on this question
				$tagging = new AnswersTags( $database );
				$row->tags = $tagging->get_tags_on_object($row->id, 0, 0, 0);
			}
		}
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task );

		// Output HTML
		echo AnswersHtml::search($title, $results, $pageNav, $this->_option, $filters, $this->infolink, $this->banking, $this->_task);
	}
	
	
	//-----------

	protected function question()
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		// Incoming
		$id      = JRequest::getInt( 'id', 0 );
		$note	 = $this->note(JRequest::getInt( 'note', 0));
		$vote	 = JRequest::getVar( 'vote', 0 );
		
		if (isset($this->qid)) {
			$id = $this->qid;
		} 
		
	
		if ( $this->_task=='reply') {
			$addcomment = & new XComment( $database );
			$addcomment->referenceid = $this->referenceid;
			$addcomment->category = $this->category;
				
		} else {
			$addcomment = NULL;
		}
		
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
				
		// Ensure we have an ID to work with
		if (!$id) {
			$document =& JFactory::getDocument();
			$document->setTitle( $title );
			
			echo AnswersHtml::hed(2, $title);
			echo AnswersHtml::error( JText::_('No Question ID found.') );
			return;
		}
		
		
		// did they vote for the question?
		if ($vote) {
			// Login required
			if ($juser->get('guest')) {
				$msg = 'You need to login to recommend a question.';
				$this->login($msg);
				return;
			} else {
				$this->vote( &$database, $id);
			}
		}
		
		// Load the question
		$question = new AnswersQuestion( $database );
		$BT = new BankTransaction( $database);
		$question->load( $id );
		
		// Check if question with this ID exists
		if (!$question->check()) {
			$id = 0;
		}
		
		// Get tags on this question
		$tagging = new AnswersTags( $database );
		$tags = $tagging->get_tags_on_object($id, 0, 0, 0);
			
		// Check reward value of the question 
		$reward = 0;
		if ($this->banking) {
			$reward = $BT->getAmount( 'answers', 'hold', $id );
		}
		
		// Check if person voted
		$voted = 0;
		if (!$juser->get('guest')) {
			$voted = $this->get_vote($id);
		}
		
		// Check for abuse reports
		$question->reports = $this->get_reports($id, 'question');	
			
		// Add the CSS to the template
		$this->getStyles();
		$this->getScripts();
		
		// Thumbs voting CSS & JS
		$this->getStyles($this->_option, 'vote.css');
		$this->getScripts($this->_option, 'vote');
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title.': '.$question->subject );

		// Get the user's IP
		$ip = (!$juser->get('guest')) ? $this->ip_address() : '';
				
		// Get responses
		$ar = new AnswersResponse( $database );
		$responses = $ar->getRecords( array('ip'=>$ip,'qid'=>$id) );
		
		// Calculate max award
		if ($this->banking) {
			$AE = new AnswersEconomy( $database );
			$question->marketvalue = round($AE->calculate_marketvalue($id, 'maxaward'));
			$question->maxaward = round(2* $question->marketvalue/3 + $reward);
			//$question->maxaward    = ($responses && count($responses) > 1) ? round($question->marketvalue/3 + $reward) : round(2* $question->marketvalue/3 + $reward);
		}
		
		// Determines if we're using abuse reports or not
		$abuse = false;
		if (is_file(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.reportabuse.php')) {
			$abuse = true;
		}
				
		// Determines if we're allowing comments
		$reply = false;
		if (is_file(JPATH_ROOT.DS.'plugins'.DS.'xhub'.DS.'xlibraries'.DS.'xcomment.php')) {
			$reply = true;
		}
		
		if ($responses && $reply && $abuse) {
			foreach ($responses as $response) 
			{
				$response->replies = $this->getComments($response, 'answer', 0);
				$response->reports = $this->get_reports($response->id, 'answer');
			}
		}
		
		$title .= ($question) ? ': '. AnswersHtml::shortenText(stripslashes($question->subject), 50, 0) : '';

		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( stripslashes($question->subject), 'index.php?option='.$this->_option.a.'task=question'.a.'id='.$question->id );
		
		// Output HTML
		echo AnswersHtml::question( $juser, $question, $responses, $id, $this->_option, $tags, 0, $reward, $voted, $note, $this->infolink, $this->banking, $title, $addcomment, $this->showcomments);
	}

	//-----------

	protected function answer()
	{
		$database 	=& JFactory::getDBO();
		$juser 		=& JFactory::getUser();
		$document =& JFactory::getDocument();
		
		$responding = ($this->_task == 'delete')   ? 4 : 1;		
		if($this->_task == 'math') { $responding= 6; }
		$note	 	= $this->note(JRequest::getInt( 'note', 0));
		$ip = (!$juser->get('guest')) ? $this->ip_address() : '';
		$id 		= JRequest::getInt( 'id', 0 );
		
		
		// Login required
		if ($juser->get('guest') && $this->_task != 'math') {
				$msg = ($responding == 4) ? '' : JText::_('PLEASE_LOGIN_TO_ANSWER');
				$this->login($msg);
				return;
		}	
			
				
		// Load the question
		$question = new AnswersQuestion( $database );
		$BT = new BankTransaction( $database );
		$question->load( $id );
		
		// check if question with this id exists
		if (!$question->check()) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($question) ? ': '. AnswersHtml::shortenText(stripslashes($question->subject), 50, 0) : '';
		$document->setTitle( $title);
	
		// check if user is attempting to answer his own answer
		if ($question->created_by == $juser->get('username') && $responding==1) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id).'?note=6';
			return;
		} else if ($question->created_by != $juser->get('username') && $responding==4) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id).'?note=7';
			return;
		}
		
		// Get tags on this question
		$tagging = new AnswersTags( $database );
		$tags = $tagging->get_tags_on_object($id, 0, 0, 0);

		// Check reward value of the question 
		
		if ($this->banking) {
			$reward = $BT->getAmount( 'answers', 'hold', $id );
		}
		$reward = $reward ? $reward : 0;
	
		// Check number of votes
		$voted = $this->get_vote($id);
			
		// Check for abuse reports
		$question->reports = $this->get_reports($id, 'question');	
		
		// Get responses
		$ar = new AnswersResponse( $database );
		$responses = $ar->getRecords( array('ip'=>$ip,'qid'=>$id) );
		
		// Determines if we're using abuse reports or not
		$abuse = false;
		if (is_file(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.reportabuse.php')) {
			$abuse = true;
		}
				
		// Determines if we're allowing comments
		$reply = false;
		if (is_file(JPATH_ROOT.DS.'plugins'.DS.'xhub'.DS.'xlibraries'.DS.'xcomment.php')) {
			$reply = true;
		}
		
		if ($responses && $reply && $abuse) {
			foreach ($responses as $response) 
			{
				$response->replies = $this->getComments($response, 'answer', 0);
				$response->reports = $this->get_reports($response->id, 'answer');
			}
		}
		
		// Calculate max award
		if ($this->banking) {
			$AE = new AnswersEconomy( $database );
			$question->marketvalue = round($AE->calculate_marketvalue($id, 'maxaward'));
			$question->maxaward = round(2* $question->marketvalue/3 + $reward);
			//$question->maxaward    = ($responses && count($responses) > 1) ? round($question->marketvalue/3 + $reward) : round(2* $question->marketvalue/3 + $reward);
		}
		
		if (isset($this->comment)) {
			$addcomment =& $this->comment;
		} else {
			$addcomment = NULL;
		}
		
		if ($question->state == 0) {
			// Add the CSS to the template and set the page title
			$this->getStyles();
			// Thumbs voting CSS
			$this->getStyles($this->_option, 'vote.css');
			
			// Set the pathway
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
			}
			$pathway->addItem( stripslashes($question->subject), 'index.php?option='.$this->_option.a.'task=question'.a.'id='.$question->id );
			$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task.a.'id='.$question->id );
			
			// Output HTML
			echo AnswersHtml::question( $juser, $question, $responses, $id, $this->_option, $tags, $responding, $reward, $voted, $note, $this->infolink, $this->banking, $title, $addcomment, $this->showcomments);
		} else {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id);
		}
	}

	//-----------

	protected function create()
	{
		$juser =& JFactory::getUser();
		
		// Incoming
		$tag = JRequest::getVar( 'tag', '' );
		
		// Login required
		if ($juser->get('guest')) {
			$this->login();
			return;
		}

		// Add the CSS to the template
		$this->getStyles();
		
		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': '. JText::_(strtoupper($this->_task)) : '';
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Is banking turned on?
		$funds = 0;
		if ($this->banking) {
			$database =& JFactory::getDBO();
			
			$BTL = new BankTeller( $database, $juser->get('id') );
			$balance = $BTL->summary();
			$credit  = $BTL->credit_summary();
			$funds   = $balance - $credit;			
			$funds   = ($funds > 0) ? $funds : '0';
		} 
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		//$pathway->addItem( JText::_('QUESTION'), 'index.php?option='.$this->_option.a.'task='.$this->_task );
		$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task );
		
		// Output HTML
		echo AnswersHtml::create( $this->_option, $funds, $this->infolink, $this->banking, $tag, $title );
	}

	//----------------------------------------------------------
	// Retrievers
	//----------------------------------------------------------

	private function get_vote($id)
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Get the user's IP address
		$ip = $this->ip_address();
				
		// See if a person from this IP has already voted in the last week
		$aql = new AnswersQuestionsLog( $database );
		$voted = $aql->checkVote($id, $ip, $juser->get('id'));
	
		return $voted;
	}
	
	//-----------
	
	private function get_reports($id, $cat)
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$filters = array();
		$filters['id']  = $id;
		$filters['category']  = $cat;
		$filters['state']  = 0;
		
		// Check for abuse reports on an item
		$ra = new ReportAbuse( $database );
		
		return $ra->getCount( $filters );
	}
	
	//-----------
	
	private function getComments($item, $category, $level, $abuse=true)
	{
		$database =& JFactory::getDBO();
		
		$level++;

		$hc = new XComment( $database );
		$comments = $hc->getResults( array('id'=>$item->id, 'category'=>$category) );
		
		if ($comments) {
			foreach ($comments as $comment) 
			{
				$comment->replies = $this->getComments($comment, 'answercomment', $level, $abuse);
				if ($abuse) {
					$comment->reports = $this->get_reports($comment->id, 'answercomment');
				}
			}
		}
		return $comments;
	}


	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------
	
	private function saveq()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Login required
		if ($juser->get('guest')) {
			$this->login();
			return;
		}

		// trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
			
		
		// Incoming
		$tags  = JRequest::getVar( 'tags', '' );
		$funds = JRequest::getVar( 'funds', '0' );
		$reward = JRequest::getVar( 'reward', '0' );
		
		// If offering a reward, do some checks
		if ($reward) {
			// Is it an actual number?
			if (!is_numeric($reward)) {
				echo AnswersHtml::alert( JText::_('Please make sure the reward is a numeric value') );
				exit();
			}
			// Are they offering more than they can afford?
			if ($reward > $funds) {
				echo AnswersHtml::alert( JText::_('You do not have sufficient funds to set this reward amount') );
				exit();
			}
		}
		
		// Ensure the user added a tag
		if (!$tags) {
			echo AnswersHtml::alert( JText::_('Question must have at least one tag') );
			exit();
		}
		
		// Initiate class and bind posted items to database fields
		$row = new AnswersQuestion( $database );
		if (!$row->bind( $_POST )) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}
		
		$row->subject    = TextFilter::cleanXss($row->subject);
		$row->question   = TextFilter::cleanXss($row->question);
		$row->question   = nl2br($row->question);
		$row->created    = date( 'Y-m-d H:i:s', time() );
		$row->created_by = $juser->get('username');
		$row->state      = 0;
		$row->email      = 1; // force notification
		if ($reward && $this->banking) {
			$row->reward = 1;
		}
		
		// Check content
		if (!$row->check()) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}

		// Hold the reward for this question if we're banking
		if ($reward && $this->banking) {
			$BTL = new BankTeller( $database, $juser->get('id') );
			$BTL->hold($reward, JText::_('Hold reward amount for best answer'), 'answers', $row->id);	
		}
		
		// Add the tags
		$tagging = new AnswersTags( $database );
		$tagging->tag_object($juser->get('id'), $row->id, $tags, 1, 0);
		
		
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$row->id).'?note=5';
			
	}
	
	//-----------
	
	private function savea()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Login required
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		$xuser =& XFactory::getUser();
		
		// Incoming
		$id = JRequest::getInt( 'qid', 0 );
		
		// Trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
	
		// Initiate class and bind posted items to database fields
		$row = new AnswersResponse( $database );
		if (!$row->bind( $_POST )) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}

		$row->answer     = TextFilter::cleanXss($row->answer);
		$row->answer     = nl2br($row->answer);
		$row->created_by = $juser->get('username');
		$row->created    = date( 'Y-m-d H:i:s', time() );
		$row->state      = 0;

		// Check content
		if (!$row->check()) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}
		
		// Load the question
		$question = new AnswersQuestion( $database );
		$question->load( $id );
		
		// Determine if this question has e-mail notifications activated
		/*if ($question->email) {
			$zuser =& XUser::getInstance( $question->created_by );
			$addy = '';
			if (is_object($zuser)) {
				$addy = $zuser->get('email');
			}
			if ($addy && $this->check_validEmail($addy)) {*/
				$juri =& JURI::getInstance();
				$jconfig =& JFactory::getConfig();
				
				$sef = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id);
				if (!strstr($sef,'http')) {
					if (substr($sef,0,1) == '/') {
						$sef = substr($sef,1,strlen($sef));
					}
					$url = $juri->base().$sef;
				}
				
				$admin_email = $jconfig->getValue('config.mailfrom');
				$subject     = $jconfig->getValue('config.sitename').' '.JText::_('ANSWERS').', '.JText::_('QUESTION').' #'.$question->id.' '.JText::_('RESPONSE');
				$from        = $jconfig->getValue('config.sitename').' '.JText::_('ANSWERS');
				$hub         = array('email' => $admin_email, 'name' => $from);
			
				$message  = '----------------------------'.r.n;
				$message .= strtoupper(JText::_('QUESTION')).': '.$question->id.r.n;
				$message .= strtoupper(JText::_('SUMMARY')).': '.$question->subject.r.n;
				$message .= strtoupper(JText::_('CREATED')).': '.$question->created.r.n;
				$message .= '----------------------------'.r.n.r.n;
				$message .= 'A response has been posted to Question #'.$row->id.' by: ';
				$message .= ($row->anonymous) ? 'Anonymous'.r.n : $juser->get('name').r.n;
				$message .= 'Response created: '.$row->created.r.n;
				$message .= 'Response: '.r.n.r.n;
				$message .= '"'.$row->answer.'"'.r.n;
				$message .= 'To view the full question and responses, go to '.$url.r.n.r.n;
			
				/*$this->send_email($hub, $addy, $subject, $message);
			}
		}*/
		$zuser =& JUser::getInstance( $question->created_by );
		
		/*ximport('xmessage');
		if (!XMessageHelper::sendMessage( 'answers_reply_submitted', $subject, $message, $hub, array($zuser->get('id')) )) {
			$this->setError( JText::_('Failed to message user.') );
		}*/
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'answers_reply_submitted', $subject, $message, $hub, array($zuser->get('id')), $this->_option, $question->id, $sef))) {
			$this->setError( JText::_('Failed to message user.') );
		}
		
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id).'?note=4';
		
	}
	
	//-----------
	
	private function delete_q()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$xuser =& JFactory::getUser();
		
		// Incoming
		$id = JRequest::getInt( 'qid', 0 );
		$ip = (!$juser->get('guest')) ? $this->ip_address() : '';

		$BT = new BankTransaction( $database );
		$reward = $BT->getAmount( 'answers', 'hold', $id );
		$email = 0;
		
		// Login required
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		$question = new AnswersQuestion( $database );
		$question->load( $id );
		
		// Check if user is authorized to delete
		if ($question->created_by != $juser->get('username')) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id).'?note=3';
			return;
		} else if ($question->state==1) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id).'?note=2';
			return;
		}
		
		$question->state = 2;  // Deleted by user
		$question->reward = 0;
			
		// Store new content
		if (!$question->store()) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}
		
		// Get all the answers for this question
		$ar = new AnswersResponse( $database );
		$responses = $ar->getRecords( array('ip'=>$ip,'qid'=>$id) );
		
		if ($reward && $this->banking) {
			
			if ($responses) {
				$jconfig =& JFactory::getConfig();
				
				$users = array();
				foreach ($responses as $r) 
				{
					$zuser =& XUser::getInstance( $r->created_by );
					if (!is_object($zuser))  {
						continue;
					}
					/*if ($this->check_validEmail($zuser->get('email')) && $email) {
						$admin_email = $jconfig->getValue('config.mailfrom');
						$subject     = $jconfig->getValue('config.sitename').' '.JText::_('ANSWERS').', '.JText::_('QUESTION').' #'.$id.' '.JText::_('WAS_REMOVED');
						$from        = $jconfig->getValue('config.sitename').' '.JText::_('ANSWERS');
						$hub         = array('email' => $admin_email, 'name' => $from);
							
						$message  = JText::_('EMAIL_Q_REMOVED');
						$message .= JText::_('EMAIL_Q_REMOVED_NO_POINTS').r.n;
						$message .= '----------------------------'.r.n.r.n;
						$message .= strtoupper(JText::_('QUESTION')).': '.$id.r.n;
						$message .= strtoupper(JText::_('SUMMARY')).': '.$question->subject.r.n;
						$message .= '----------------------------'.r.n.r.n;
								
						$this->send_email($hub, $zuser->get('email'), $subject, $message);
					}*/
					$users[] = $zuser->get('id');
				}
				
				$admin_email = $jconfig->getValue('config.mailfrom');
				$subject     = $jconfig->getValue('config.sitename').' '.JText::_('ANSWERS').', '.JText::_('QUESTION').' #'.$id.' '.JText::_('WAS_REMOVED');
				$from        = $jconfig->getValue('config.sitename').' '.JText::_('ANSWERS');
				$hub         = array('email' => $admin_email, 'name' => $from);
					
				$message  = JText::_('EMAIL_Q_REMOVED');
				$message .= JText::_('EMAIL_Q_REMOVED_NO_POINTS').r.n;
				$message .= '----------------------------'.r.n.r.n;
				$message .= strtoupper(JText::_('QUESTION')).': '.$id.r.n;
				$message .= strtoupper(JText::_('SUMMARY')).': '.$question->subject.r.n;
				$message .= '----------------------------'.r.n.r.n;
				
				/*ximport('xmessage');
				if (!XMessageHelper::sendMessage( 'answers_question_deleted', $subject, $message, $hub, $users )) {
					$this->setError( JText::_('Failed to message users.') );
				}*/
				JPluginHelper::importPlugin( 'xmessage' );
				$dispatcher =& JDispatcher::getInstance();
				if (!$dispatcher->trigger( 'onSendMessage', array( 'answers_question_deleted', $subject, $message, $hub, $users, $this->_option ))) {
					$this->setError( JText::_('Failed to message user.') );
				}
			}
			
			// Remove hold
			//$BT = new BankTransaction( $database );
			$BT->deleteRecords( 'answers', 'hold', $id );
					
			// Make credit adjustment
			$BTL_Q = new BankTeller( $database, $juser->get('id') );
			$credit = $BTL_Q->credit_summary();
			$adjusted = $credit - $reward;
			$BTL_Q->credit_adjustment($adjusted);
		}
		
		// Delete all tag associations	
		$tagging = new AnswersTags( $database );
		$tagging->remove_all_tags($id);
		
		// get all the answers for this question		
		if ($responses) {
			$al = new AnswersLog( $database );
			foreach ($responses as $answer)
			{
				// delete votes
				$al->deleteLog( $answer->id );
				
				// delete response
				$ar->deleteResponse($answer->id);
			}
		}
				
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id).'?note=1';
		
	}
	

	//-----------
	
	private function accept()
	{
		$database =& JFactory::getDBO();
		
		$juser =& JFactory::getUser();
		
		// Login required
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Incoming
		$id  = JRequest::getInt( 'id', 0 );
		$rid = JRequest::getInt( 'rid', 0 );
		
		// Load and mark the answer as THE accepted answer
		$answer = new AnswersResponse( $database );
		$answer->load( $rid );
		$answer->state = 1;

		// Check changes
		if (!$answer->check()) {
			$this->setError( $answer->getError() );
		}

		// Save changes
		if (!$answer->store()) {
			$this->setError( $answer->getError() );
		}
		
		// Load and mark the question as closed
		$question = new AnswersQuestion( $database );
		$question->load( $id );
		$question->state = 1;
		$question->reward = 0; // Uncheck reward label
		
		$zuser =& JUser::getInstance( $question->created_by );
		
		// Check changes
		if (!$question->check()) {
			$this->setError( $question->getError() );
		}

		// Save changes
		if (!$question->store()) {
			$this->setError( $question->getError() );
		}
		
		if ($this->banking) {
			// Calculate and distribute earned points
			$AE = new AnswersEconomy( $database );			
			$AE->distribute_points($id, $question->created_by, $answer->created_by, 'closure');
		}
		
		// Load the plugins
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Call the plugin
		if (!$dispatcher->trigger( 'onTakeAction', array( 'answers_reply_submitted', array($zuser->get('id')), $this->_option, $question->id ))) {
			$this->setError( JText::_('Failed to remove alert.')  );
		}

	
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id).'?note=10';	
	
	}
	//-----------
	
	private function vote( &$database, $id)
	{
		$ip = $this->ip_address();
		$juser =& JFactory::getUser();
			
		// Login required
		if ($juser->get('guest')) {
			$this->login( JText::_('PLEASE_LOGIN_TO_VOTE') );
			return;
		}
			
		// See if a person from this IP has already voted
		$al = new AnswersQuestionsLog( $database );
		$voted = $al->checkVote( $id, $ip );
	
		if ($voted) {	
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id).'?note=8';
			return;
		}
				
		// load the resource
		$row = new AnswersQuestion( $database );
		$row->load( $id );
		$this->qid = $id;
		
		// check if user is rating his own question
		if($row->created_by == $juser->get('username')) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id).'?note=9';
			return;
		}
		
		// record vote
		$row->helpful++;
		
		if (!$row->store()) {
			$this->_error = $row->getError();
			return;
		}
		
		$expires = time() + (7 * 24 * 60 * 60); // in a week
		$expires = date( 'Y-m-d H:i:s', $expires );
		
		// Record user's vote
		$al->qid = $id;
		$al->ip = $ip;
		$al->voter = $juser->get('id');
		$al->expires = $expires;
		if (!$al->check()) {
			$this->setError( $al->getError() );
			return;
		}
		if (!$al->store()) {
			$this->setError( $al->getError() );
			return;
		}
			
	}
	

	//----------------------------------------------------------
	// Misc Functions
	//----------------------------------------------------------


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
		return (!preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
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

	public function mkt($stime)
	{
		if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}
	
	//-----------
	
	public function timeAgoo($timestamp)
	{
		// Store the current time
		$current_time = time();
		
		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;
		
		// Set the periods of time
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		
		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);
		
		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);
		
		// Ensure the script has found a match
		if ($val < 0) $val = 0;
		
		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);
		
		// Set the current value to be floored
		$number = floor($number);
		
		// If required create a plural
		if ($number != 1) $periods[$val].= "s";
		
		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);
		
		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)){
			$text .= AnswersController::TimeAgoo($new_time);
		}
		
		return $text;
	}
	
	//-----------
	
	public function timeAgo($timestamp) 
	{
		$text = $this->timeAgoo($timestamp);
		
		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		//$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
		return $text;
	}
	
	//-----------
	
	private function _authorize()
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
	
		// Check if they're a site admin (from LDAP)
		$xuser =& XFactory::getUser();
		if (is_object($xuser)) {
			$app =& JFactory::getApplication();
			if (in_array(strtolower($app->getCfg('sitename')), $xuser->get('admin'))) {
				return 'admin';
			}
		}

		return false;
	}
	
	//-----------

	private function send_email($from, $email, $subject, $message) 
	{
		if ($from) {
			$args = "-f '" . $contact_email . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
			$headers .= 'From: ' . $from['name'] .' <'. $from['email'] . ">\n";
			$headers .= 'Reply-To: ' . $from['name'] .' <'. $from['email'] . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: '. $from['name'] .n;
			if (mail($email, $subject, $message, $headers, $args)) {
				return(1);
			}
		}
		return(0);
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
	
	private function note($type, $note=array('msg'=>'','class'=>'warning')) 
	{
		switch ($type) 
		{
			case '1' :  // question was removed
				$note['msg'] = JText::_('NOTICE_QUESTION_REMOVED');
				$note['class'] = 'info';
			break;
			case '2' : // can't delete a closed question
				$note['msg'] = JText::_('WARNING_CANT_DELETE_CLOSED');
			break;
			case '3' : // not authorized to delete question
				$note['msg'] = JText::_('WARNING_CANT_DELETE');
			break;
			case '4' : // answer posted
				$note['msg'] = JText::_('NOTICE_POSTED_THANKS');
				$note['class'] = 'passed';
			break;
			case '5' : // question posted
				$note['msg'] = JText::_('NOTICE_QUESTION_POSTED_THANKS');
				$note['class'] = 'passed';
			break;
			case '6' : // can't answer own question
				$note['msg'] = JText::_('NOTICE_CANT_ANSWER_OWN_QUESTION');
			break;
			case '7' : // can't delete question
				$note['msg'] = JText::_('NOTICE_CANNOT_DELETE');
			break;
			case '8' : // can't vote again
				$note['msg'] = JText::_('NOTICE_ALREADY_VOTED_FOR_QUESTION');
			break;
			case '9' : // can't vote for own question
				$note['msg'] = JText::_('NOTICE_RECOMMEND_OWN_QUESTION');
			break;
			case '10' : // answer accepted
				$note['msg'] = JText::_('NOTICE_QUESTION_CLOSED');
			break;
		}
		return $note;
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
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = strip_tags( $text );
		return $text;
	}
	
	//-----------

	private function getInterests($cloud=0)
	{
		$database =& JFactory::getDBO();
		$juser 	 =& JFactory::getUser();
		
		require_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'members.tags.php' );
		
		// Get tags of interest
		$mt = new MembersTags( $database );
		if($cloud) {
			$tags = $mt->get_tag_cloud(0,0,$juser->get('id') );
		} else {
			$tags = $mt->get_tag_string( $juser->get('id') );
		}
		
		return $tags;	
	
	}
	//-----------

	public function formatTags($string='', $num=3, $max=25)
	{
		
		$out = '';
		$tags = split(',',$string);

		if(count($tags) > 0) {
			$out .= '<span class="taggi">'."\n";
			$counter = 0;
			
			for($i=0; $i< count($tags); $i++) {
				$counter = $counter + strlen(stripslashes($tags[$i]));	
				if($counter > $max) {
					$num = $num - 1;
				}
				if($i < $num) {
					// display tag
					$normalized = $this->normalize_tag($tags[$i]);
					$out .= "\t".'<a href="'.JRoute::_('index.php?option=com_tags&amp;tag='.$normalized).'">'.stripslashes($tags[$i]).'</a> '."\n";
				}
				
			}
			if($i > $num) {
				$out .= ' (&#8230;)';
			}
			$out .= '</span>'."\n";
		}
		
		return $out;
	
	}
	//-----------
	
	public function normalize_tag($tag) 
	{		
			$normalized_valid_chars = 'a-zA-Z0-9';
			$normalized_tag = preg_replace("/[^$normalized_valid_chars]/", "", $tag);
			return strtolower($normalized_tag);
		
	}
}
?>
