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
	
		$xhub =& XFactory::getHub();
		$banking = $xhub->getCfg('hubBankAccounts');
		$this->banking = $banking;
		
		if ($banking) {
			ximport( 'bankaccount' );
		}
		
		switch ( $this->getTask() ) 
		{
			case 'resethelpful': $this->reset_helpful();   break;
			case 'newq':         $this->edit_question();   break;
			case 'editq':        $this->edit_question();   break;
			case 'saveq':        $this->save_question();   break;
			case 'remove':       $this->delete_question(); break;
			case 'open':         $this->state();           break;
			case 'close':        $this->state();           break;
			case 'newa':         $this->edit_answer();     break;
			case 'edita':        $this->edit_answer();     break;
			case 'savea':        $this->save_answer();     break;
			case 'deletea':      $this->delete_answer();   break;
			case 'accept':       $this->accept();          break;
			case 'reject':       $this->accept();          break;
			case 'cancel':       $this->cancel();          break;
			case 'orphans':      $this->orphans();         break;
			case 'answers':      $this->answers();         break;
			case 'questions':    $this->questions();       break;

			default: $this->questions(); break;
		}
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function questions()
	{
		$database =& JFactory::getDBO();

		// Get Joomla configuration
		$config = JFactory::getConfig();
	

		// Filters
		$filters = array();
		$filters['limit']    = JRequest::getInt( 'limit', 25 );
		$filters['start']    = JRequest::getInt( 'limitstart', 0 );
		$filters['tag']      = JRequest::getVar( 'tag', '' );
		$filters['q']        = JRequest::getVar( 'q', '' );
		$filters['filterby'] = JRequest::getVar( 'filterby', 'all' );
		$filters['sortby']   = JRequest::getVar( 'sortby', 'date' );

		
		$aq = new AnswersQuestion( $database );
		
		// Get a record count
		$total = $aq->getCount( $filters );

		// Get records
		$results = $aq->getResults( $filters );
		
		$ip = $this->ip_address();
		$ar = new AnswersResponse( $database );
		$tagging = new AnswersTags( $database );
		
		// Did we get any results?
		if (count($results) > 0) {
			// Do some processing on the results
			for ($i=0; $i < count($results); $i++) 
			{
				$row =& $results[$i];

				if ($this->banking) {
					$row->points = $this->get_reward($row->id);
				} else {
					$row->points = 0;
				}
				$row->reports = $this->get_reports($row->id, 'question');
	
				// Get tags on this question
				$row->tags = $tagging->get_tags_on_object($row->id, 0, 0, 0);
				
				// Get responses
				$row->answers = count($ar->getRecords( array('ip'=>$ip,'qid'=>$row->id) ));
			}
		}
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
	
		// output HTML
		AnswersHtml::questions( $database, $results, $pageNav, $this->_option, $filters );
	}

	//-----------

	protected function answers()
	{
		$database =& JFactory::getDBO();
		
		// Incoming Question ID
		$qid = JRequest::getInt( 'qid', 0 );

		// Get Joomla configuration
		$config = JFactory::getConfig();

		// Filters
		$filters['limit']    = JRequest::getInt( 'limit', $config->getValue('config.list_limit') );
		$filters['start']     = JRequest::getInt( 'limitstart', 0 );
		$filters['filterby'] = JRequest::getVar( 'filterby', 'all' );
		$filters['sortby']   = JRequest::getVar( 'sortby', 'm.id DESC' );

		switch ($filters['filterby'])
		{
			case 'all': 
				$where = "(m.state=1 OR m.state=0)";
				break;
			case 'accepted': 
				$where = "m.state=1";
				break;
			case 'rejected':
			default: 
				$where = "m.state=0";
				break;
		}
		
		// get record count
		$sqlcount = "SELECT count(*) FROM #__answers_questions AS m WHERE ".$where;
		$database->setQuery( $sqlcount );
		$total = $database->loadResult();

		// initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit']);

		// retrieve answers
		$sql = "SELECT m.id, m.answer, m.created, m.created_by, m.helpful, m.nothelpful, m.state, m.anonymous"
			. "\n FROM #__answers_responses AS m"
			. "\n WHERE m.qid=".$qid." AND ".$where
			. "\n ORDER BY ".$filters['sortby']
			. "\n LIMIT $pageNav->limitstart,$pageNav->limit";
		$database->setQuery( $sql );
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		// get the parent subject for the answers
		$database->setQuery( "SELECT r.subject FROM #__answers_questions AS r WHERE r.id=".$qid );
		if($database->query()) {
			$parent = $database->loadResult();
		}
	
		// output HTML
		AnswersHtml::answers( $parent, $rows, $pageNav, $this->_option, $filters, $qid );
	}

	//-----------

	protected function edit_question() 
	{
		$database =& JFactory::getDBO();

		$ids = JRequest::getVar( 'id', array(0) );

		if (is_array($ids) && !empty($ids)) {
			$id = $ids[0];
		}
		
		// load infor from database
		$row = new AnswersQuestion( $database );
		$row->load( $id );
	
		if ($id) {
			// remove some tags so edit box only displays text (no HTML)
			$row->question = AnswersHtml::unpee($row->question);
		} else {
			// creating new
			$row->subject     = '';
			$row->question    = '';
			$row->created     = date( 'Y-m-d H:i:s', time() );
			$row->created_by  = '';
			$row->state       = 0;
		}

		// get tags
	
		$tags_men = $this->get_tags($id, 0);
		$mytagarray = array();
		foreach ($tags_men as $tag_men)
		{
			$mytagarray[] = $tag_men->raw_tag;
		}
		$tags = implode( ', ', $mytagarray );
	

		// output HTML
		AnswersHtml::editQuestion( $row, $this->_option, $tags );
	}

	//-----------

	protected function edit_answer()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		$qid = JRequest::getInt( 'qid', 0 );
		$ids = JRequest::getVar( 'id', array(0) );
		$task = $this->_task;
	
		if (is_array($ids) && !empty($ids)) {
			$id = $ids[0];
		}
	
		// load infor from database
		$row = new AnswersResponse( $database );
		
		if ($task != 'newa') {
			$row->load( $id );
			$row->answer = AnswersHtml::unpee($row->answer);
		} else {
			$row->load( 0 );
			$row->answer     = '';
			$row->created    = date( 'Y-m-d H:i:s', time() );
			$row->created_by = $juser->get('username');
			if($qid) {
				$row->qid    = $qid;
			} else {
				$row->qid    = $id;
			}
			$row->helpful    = 0;
			$row->nothelpful = 0;
		}

		$question = '';
		$database->setQuery( "SELECT r.subject FROM #__answers_questions AS r WHERE r.id=".$row->qid );
		if ($database->query()) {
			$question = $database->loadResult();
		}
		
		AnswersHtml::editAnswer( $row, $question, $this->_option, $row->qid );
	}

	//----------------------------------------------------------
	//  Processers
	//----------------------------------------------------------
	
	protected function save_question() 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		$_POST = array_map('trim',$_POST);
		$tags = JRequest::getVar( 'tags', '' );
		
		// initiate extended database class
		$row = new AnswersQuestion( $database );
		if (!$row->bind( $_POST )) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}

		if (!$tags) {
			echo AnswersHtml::alert( JText::_('Question must have at least 1 tag') );
			exit();
		}
		
		// updating entry
		$row->created = $row->created ? $row->created : date( "Y-m-d H:i:s" );
		$row->created_by = $row->created_by ? $row->created_by : $juser->get('username');

		// code cleaner
		$row->subject  = TextFilter::cleanXss($row->subject);
		$row->question = TextFilter::cleanXss($row->question);
		$row->question = nl2br($row->question);

		// check content
		if (!$row->check()) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}
		
		// store new content
		if (!$row->store()) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}

		// add the tags
		$tagging = new AnswersTags($database);
		$tagging->tag_object($juser->get('id'), $row->id, $tags, 1, 1);

		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('Question Successfully Saved');
	}

	//-----------

	protected function save_answer() 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$qid = JRequest::getInt('qid', 0 );
		
		$_POST = array_map('trim',$_POST);

		// initiate extended database class
		$row = new AnswersResponse( $database );
		if (!$row->bind( $_POST )) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}

		// code cleaner
		$row->answer = TextFilter::cleanXss($row->answer);
		$row->answer = nl2br($row->answer);
		$row->created = $row->created ? $row->created : date( "Y-m-d H:i:s" );
		$row->created_by = $row->created_by ? $row->created_by : $juser->get('username');

		// check content
		if (!$row->check()) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}

		// store new content
		if (!$row->store()) {
			echo AnswersHtml::alert( $row->getError() );
			exit();
		}

		// close the question if the answer is accepted
		if ($row->state == 1) {
			$aq = new AnswersQuestion( $database );
			$aq->load( $qid );
			$aq->state = 1;
			if (!$aq->store()) {
				echo AnswersHtml::alert( $aq->getError() );
				exit();
			}
		}

		$url  = 'index.php?option='.$this->_option;
		$url .= ($qid) ? '&task=answers&qid='.$qid : '';

		$this->_redirect = $url;
		$this->_message = JText::_('Answer Successfully Saved');
	}

	//-----------

	protected function delete_answer() 
	{
		$database =& JFactory::getDBO();
	
		$qid = JRequest::getInt( 'qid', 0 );
		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}
		
		$ar = new AnswersResponse( $database );
		$al = new AnswersLog( $database );
	
		if ($ids) {
			foreach ($ids as $id) 
			{
				if (!$ar->delete($id)) {
					echo AnswersHtml::alert( $ar->getError() );
					exit;
				}

				if (!$al->deleteLog($id)) {
					echo AnswersHtml::alert( $al->getError() );
					exit;
				}	
			}
		}
		
		$this->_redirect = 'index.php?option='.$this->_option.'&task=answers&qid='.$qid;
	}

	//-----------

	protected function delete_question() 
	{
		$database =& JFactory::getDBO();
		$xuser 		=& XFactory::getUser();

		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}
	
		$aq = new AnswersQuestion( $database );
		$ar = new AnswersResponse( $database );
		$al = new AnswersLog( $database );

		foreach ($ids as $id)
		{
	
			// delete the question
			$aq->load( $id );
			$aq->state = 2;  // Deleted by user
			$aq->reward = 0;
			
			// Store new content
			if (!$aq->store()) {
				echo AnswersHtml::alert( $aq->getError() );
				exit();
			}
			
			if($this->banking) {
				// Remove hold
				$BT = new BankTransaction( $database );
				$reward = $BT->getAmount( 'answers', 'hold', $id );
				$BT->deleteRecords( 'answers', 'hold', $id );
				
				$xuser =& XUser::getInstance( $aq->created_by );
				
				// Make credit adjustment
				if(is_object($xuser)) {
				$BTL = new BankTeller( $database, $xuser->get('uid') );
				$credit = $BTL->credit_summary();
				$adjusted = $credit - $reward;
				$BTL->credit_adjustment($adjusted);
				}
			
			}
			
			// get all the answers for this question
			$ip = $this->ip_address();
			$answers = $ar->getRecords( array('ip'=>$ip,'qid'=>$id));

			if ($answers) {
				foreach ($answers as $answer)
				{
					// delete response's log entry
					if (!$al->deleteLog($answer->id)) {
						echo AnswersHtml::alert( $al->getError() );
						exit;
					}
				
					// delete response
					if (!$ar->deleteResponse($answer->id)) {
						echo AnswersHtml::alert( $ar->getError() );
						exit;
					}
				}
			}
			
			// Delete all tag associations	
			$tagging = new AnswersTags( $database );
			$tags = $tagging->remove_all_tags($id);
			
			
			
		}
		$this->_message = JText::_('Question deleted');		
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------

	protected function state() 
	{
		$database =& JFactory::getDBO();

		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}
		$task = $this->_task;

		$publish = ($task == 'close') ? 1 : 0;

		// check for a resource
		if (count( $ids ) < 1) {
			$action = ($publish == 1) ? 'close' : 'open';
			echo AnswersHtml::alert( JText::_('Select a question to '.$action) );
			exit;
		}

		$total = count( $ids );
		
		foreach ($ids as $id) 
		{
			// update record(s)
			$aq = new AnswersQuestion( $database );
			$aq->load($id);
			$aq->state = $publish;
			if ($publish == '1') {
			$aq->reward = 0;
			}
			if (!$aq->store()) {
				echo AnswersHtml::alert( $aq->getError() );
				exit();
			}
			
			if ($publish == '1') {
				
				$xuser =& XUser::getInstance( $aq->created_by );
				
				if ($this->banking) {
					// Remove hold
					$BT = new BankTransaction( $database );
					$reward = $BT->getAmount( 'answers', 'hold', $id );
					$BT->deleteRecords( 'answers', 'hold', $id );
					
					
					
					// Make credit adjustment
					if(is_object($xuser)) {
					$BTL = new BankTeller( $database, $xuser->get('uid') );
					$credit = $BTL->credit_summary();
					$adjusted = $credit - $reward;
					$BTL->credit_adjustment($adjusted);
					}
				
				}
							
				// Load the plugins
				JPluginHelper::importPlugin( 'xmessage' );
				$dispatcher =& JDispatcher::getInstance();
				
				// Call the plugin
				if (!$dispatcher->trigger( 'onTakeAction', array( 'answers_reply_submitted', array($xuser->get('uid')), $this->_option, $id ))) {
					$this->setError( JText::_('Failed to remove alert.')  );
				}
			
			}
			
		}

		// set message
		if ($publish == '1') {
			$msg = JText::_($total .' Item(s) successfully Closed');
			
		} else if ( $publish == '0' ) {
			$msg = JText::_($total .' Item(s) successfully Opened');
		}

		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = $msg;
	}
	
	//-----------
	
	protected function accept() 
	{
		$database =& JFactory::getDBO();

		$qid = JRequest::getInt( 'qid', 0 );
		$id = JRequest::getVar( 'id', array(0) );
		
		if (!is_array( $id )) {
			$id = array(0);
		}
		$task = $this->_task;

		$publish = ($task == 'accept') ? 1 : 0;

		// check for a resource
		if (count( $id ) < 1) {
			$action = ($publish == 1) ? 'accept' : 'unaccept';
			echo AnswersHtml::alert( JText::_('Select an answer to '.$action) );
			exit;
		} else if (count( $id ) > 1) {
			echo AnswersHtml::alert( JText::_('A question can only have one accepted answer') );
			exit;
		}

		$ar = new AnswersResponse( $database );
		$ar->load($id[0]);
		$ar->state = $publish;
		if (!$ar->store()) {
			echo AnswersHtml::alert( $ar->getError() );
			exit();
		}

		// close the question if the answer is accepted
		if ($publish == 1) {
			$aq = new AnswersQuestion( $database );
			$aq->load($qid);
			$aq->state = 1;
			if ($publish == '1') {
			$aq->reward = 0;
			}
			if (!$aq->store()) {
				echo AnswersHtml::alert( $aq->getError() );
				exit();
			}
			
			if ($this->banking) {
				// Calculate and distribute earned points
				$AE = new AnswersEconomy( $database );			
				$AE->distribute_points($qid, $aq->created_by, $ar->created_by, 'closure');
			
			}
			
			$zuser =& JUser::getInstance( $aq->created_by );
			
			// Load the plugins
			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			
			// Call the plugin
			if (!$dispatcher->trigger( 'onTakeAction', array( 'answers_reply_submitted', array($zuser->get('id')), $this->_option, $qid ))) {
				$this->setError( JText::_('Failed to remove alert.')  );
			}

			
		} else {
			$aq = new AnswersQuestion( $database );
			$aq->load($qid);
			$aq->state = 0;
			if (!$aq->store()) {
				echo AnswersHtml::alert( $aq->getError() );
				exit();
			}
		}

		// set message
		if ($publish == '1') {
			$msg = JText::_('Item successfully Accepted');
		} else if ( $publish == '0' ) {
			$msg = JText::_('Item successfully Unaccepted');
		}

		$url  = 'index.php?option='.$this->_option;
		$url .= ($qid) ? '&task=answers&qid='.$qid : '';

		$this->_redirect = $url;
		$this->_message = $msg;
	}
	
	//-----------

	protected function cancel()
	{
		$qid = JRequest::getInt('qid', 0);
		$id  = JRequest::getInt('id', 0);

		$url  = 'index.php?option='.$this->_option;
		//$url .= ($qid) ? '&task=answers&qid='.$qid : '';

		$this->_redirect = $url;
	}
	

	//-----------
	
	private function get_reward($id)
	{
		$database =& JFactory::getDBO();
		
		// Check if question owner assigned a reward for answering his Q
		$BT = new BankTransaction( $database );
		return $BT->getAmount( 'answers', 'hold', $id );
	}
	
	//-----------
	
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

	protected function reset_helpful()
	{
		$database =& JFactory::getDBO();

		$qid = JRequest::getInt( 'qid', 0 );
		$id  = JRequest::getInt( 'id', 0 );
		
		$ar = new AnswersResponse( $database );
		$ar->load($id);
		$ar->helpful = 0;
		$ar->nothelpful = 0;
		if (!$ar->store()) {
			echo AnswersHtml::alert( $ar->getError() );
			exit();
		}
	
		$al = new AnswersLog( $database );
		if (!$al->deleteLog($id)) {
			echo AnswersHtml::alert( $al->getError() );
			exit();
		}

		$url  = 'index.php?option='.$this->_option;
		$url .= ($qid) ? '&task=answers&qid='.$qid : '';

		$this->_redirect = $url;
	}

	//----------------------------------------------------------
	//  Retrievers
	//----------------------------------------------------------

	private function get_tags($id, $tagger_id=0, $strength=0)
	{
		$database =& JFactory::getDBO();
	
		$sql = "SELECT DISTINCT t.* FROM #__tags AS t, #__tags_object AS rt WHERE rt.objectid=".$id." AND rt.tbl='answers' AND rt.tagid=t.id";
		if ($tagger_id != 0) {
			$sql .= " AND rt.taggerid=".$tagger_id;
		}
		if ($strength) {
			$sql .= " AND rt.strength=".$strength;
		}
		$database->setQuery( $sql );
		if ($database->query()) {
			$tags = $database->loadObjectList();
		} else {
			$tags = NULL;
		}

		return $tags;
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
}
?>
