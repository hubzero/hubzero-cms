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

ximport('Hubzero_Controller');

class AnswersController extends Hubzero_Controller
{
	public function execute()
	{
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$this->banking = $upconfig->get('bankAccounts');
		
		if ($this->banking) {
			ximport('bankaccount');
		}
		
		$this->_task = strtolower(JRequest::getVar('task', '', 'request'));
		
		switch ($this->_task) 
		{
			// Questions
			case 'newq':         $this->editQuestion();   break;
			case 'editq':        $this->editQuestion();   break;
			case 'saveq':        $this->saveQuestion();   break;
			case 'remove':       $this->deleteQuestion(); break;
			case 'open':         $this->state();          break;
			case 'close':        $this->state();          break;
			case 'questions':    $this->questions();      break;
			
			// Answers
			case 'resethelpful': $this->resetHelpful();   break;
			case 'newa':         $this->editAnswer();     break;
			case 'edita':        $this->editAnswer();     break;
			case 'savea':        $this->saveAnswer();     break;
			case 'deletea':      $this->deleteAnswer();   break;
			case 'accept':       $this->accept();         break;
			case 'reject':       $this->accept();         break;
			case 'answers':      $this->answers();        break;
			
			case 'cancel':       $this->cancel();         break;

			default: $this->questions(); break;
		}
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function questions()
	{
		// Get Joomla configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();
	
		// Instantiate a new view
		$view = new JView( array('name'=>'questions') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Filters
		$view->filters = array();
		$view->filters['limit']    = $app->getUserStateFromRequest($this->_option.'.questions.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']    = $app->getUserStateFromRequest($this->_option.'.questions.limitstart', 'limitstart', 0, 'int');
		$view->filters['tag']      = JRequest::getVar( 'tag', '' );
		$view->filters['q']        = JRequest::getVar( 'q', '' );
		$view->filters['filterby'] = JRequest::getVar( 'filterby', 'all' );
		$view->filters['sortby']   = JRequest::getVar( 'sortby', 'date' );

		$aq = new AnswersQuestion( $this->database );
		
		// Get a record count
		$view->total = $aq->getCount( $view->filters );

		// Get records
		$view->results = $aq->getResults( $view->filters );
		
		// Did we get any results?
		if (count($view->results) > 0) {
			$ip = Hubzero_Environment::ipAddress();
			$ar = new AnswersResponse( $this->database );
			$at = new AnswersTags( $this->database );
			
			// Do some processing on the results
			for ($i=0; $i < count($view->results); $i++) 
			{
				$row =& $view->results[$i];

				if ($this->banking) {
					$row->points = $this->_getPointReward($row->id);
				} else {
					$row->points = 0;
				}
				
				$row->reports = $this->_getAbuseReports($row->id, 'question');
	
				// Get tags on this question
				$row->tags = $at->get_tags_on_object($row->id, 0, 0, 0);
				
				// Get responses
				$row->answers = count($ar->getRecords( array('ip'=>$ip,'qid'=>$row->id) ));
			}
		}
		
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

	protected function answers()
	{
		// Get Joomla configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();
		
		// Instantiate a new view
		$view = new JView( array('name'=>'answers') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Filters
		$view->filters = array();
		$view->filters['limit']    = $app->getUserStateFromRequest($this->_option.'.answers.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']    = $app->getUserStateFromRequest($this->_option.'.answers.limitstart', 'limitstart', 0, 'int');
		$view->filters['filterby'] = JRequest::getVar( 'filterby', 'all' );
		$view->filters['sortby']   = JRequest::getVar( 'sortby', 'm.id DESC' );
		$view->filters['qid']      = JRequest::getInt( 'qid', 0 );

		$view->question = new AnswersQuestion( $this->database );
		$view->question->load($view->filters['qid']);

		$ar = new AnswersResponse( $this->database );
		
		// Get a record count
		$view->total = $ar->getCount( $view->filters );

		// Get records
		$view->results = $ar->getResults( $view->filters );

		// initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination($view->total, $view->filters['start'], $view->filters['limit']);

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function editQuestion() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'question') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		if (is_array($ids) && !empty($ids)) {
			$id = $ids[0];
		}
		
		// Load object
		$view->row = new AnswersQuestion( $this->database );
		$view->row->load( $id );
	
		if ($id) {
			// Remove some tags so edit box only displays text (no HTML)
			$view->row->question = AnswersHtml::unpee($view->row->question);
		} else {
			// Creating new
			$view->row->subject     = '';
			$view->row->question    = '';
			$view->row->created     = date( 'Y-m-d H:i:s', time() );
			$view->row->created_by  = '';
			$view->row->state       = 0;
		}

		// Get tags
		$tags_men = $this->get_tags($id, 0);
		$mytagarray = array();
		foreach ($tags_men as $tag_men)
		{
			$mytagarray[] = $tag_men->raw_tag;
		}
		$view->tags = implode( ', ', $mytagarray );
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function editAnswer()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'answer') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Incoming
		$qid = JRequest::getInt( 'qid', 0 );
		$ids = JRequest::getVar( 'id', array(0) );
		if (is_array($ids) && !empty($ids)) {
			$id = $ids[0];
		}
		if (!$qid) {
			$qid = $id;
			$id = 0;
		}
	
		// load infor from database
		$view->row = new AnswersResponse( $this->database );
		$view->row->load( $id );
		
		if ($this->_task == 'newa') {
			$view->row->answer     = '';
			$view->row->created    = date( 'Y-m-d H:i:s', time() );
			$view->row->created_by = $this->juser->get('username');
			$view->row->qid    = $qid;
			$view->row->helpful    = 0;
			$view->row->nothelpful = 0;
		} else {
			$view->row->answer = AnswersHtml::unpee($view->row->answer);
		}

		$view->question = new AnswersQuestion( $this->database );
		$view->question->load($qid);
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//----------------------------------------------------------
	//  Processers
	//----------------------------------------------------------
	
	protected function saveQuestion() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming data
		$question = JRequest::getVar('question', array(), 'post');
		$question = array_map('trim',$question);
		
		// Ensure we have at least one tag
		if (!$question['tags']) {
			echo AnswersHtml::alert( JText::_('Question must have at least 1 tag') );
			exit();
		}
		
		// Initiate extended database class
		$row = new AnswersQuestion( $this->database );
		if (!$row->bind( $question )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		// Updating entry
		$row->created = $row->created ? $row->created : date( "Y-m-d H:i:s" );
		$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('username');

		// Code cleaner
		//$row->subject  = TextFilter::cleanXss($row->subject);
		//$row->question = TextFilter::cleanXss($row->question);
		$row->question = nl2br($row->question);

		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		// Store content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Add the tag(s)
		$at = new AnswersTags($this->database);
		$at->tag_object($this->juser->get('id'), $row->id, $question['tags'], 1, 1);

		// Redirect back to the full questions list
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('Question Successfully Saved');
	}

	//-----------

	protected function saveAnswer() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$answer = JRequest::getVar('answer', array(), 'post');
		$answer = array_map('trim',$answer);

		// initiate extended database class
		$row = new AnswersResponse( $this->database );
		if (!$row->bind( $answer )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Code cleaner
		//$row->answer = TextFilter::cleanXss($row->answer);
		$row->answer = nl2br($row->answer);
		$row->created = $row->created ? $row->created : date( "Y-m-d H:i:s" );
		$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('username');

		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Close the question if the answer is accepted
		if ($row->state == 1) {
			$aq = new AnswersQuestion( $this->database );
			$aq->load( $answer['qid'] );
			$aq->state = 1;
			if (!$aq->store()) {
				JError::raiseError( 500, $aq->getError() );
				return;
			}
		}

		// Redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
		$this->_redirect .= ($answer['qid']) ? '&task=answers&qid='.$answer['qid'] : '';
		$this->_message = JText::_('Answer Successfully Saved');
	}

	//-----------

	protected function deleteAnswer() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$qid = JRequest::getInt( 'qid', 0 );
		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}
		
		// Do we have any IDs?
		if (count($ids) > 0) {
			// Instantiate some objects
			$ar = new AnswersResponse( $this->database );
			$al = new AnswersLog( $this->database );
			
			// Loop through each ID
			foreach ($ids as $id) 
			{
				if (!$ar->delete($id)) {
					JError::raiseError( 500, $ar->getError() );
					return;
				}

				if (!$al->deleteLog($id)) {
					JError::raiseError( 500, $al->getError() );
					return;
				}	
			}
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=answers&qid='.$qid;
	}

	//-----------

	protected function deleteQuestion() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}
	
		if (count($ids) <= 0) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
	
		$aq = new AnswersQuestion( $this->database );
		$ar = new AnswersResponse( $this->database );
		$al = new AnswersLog( $this->database );

		foreach ($ids as $id)
		{
			// Delete the question
			$aq->load( $id );
			$aq->state = 2;  // Deleted by user
			$aq->reward = 0;
			
			// Store new content
			if (!$aq->store()) {
				JError::raiseError( 500, $aq->getError() );
				return;
			}
			
			if ($this->banking) {
				// Remove hold
				$BT = new BankTransaction( $this->database );
				$reward = $BT->getAmount( 'answers', 'hold', $id );
				$BT->deleteRecords( 'answers', 'hold', $id );
				
				$creator =& JUser::getInstance($aq->created_by);
				
				// Make credit adjustment
				if (is_object($creator)) {
					$BTL = new BankTeller( $this->database, $creator->get('id') );
					$credit = $BTL->credit_summary();
					$adjusted = $credit - $reward;
					$BTL->credit_adjustment($adjusted);
				}
			}
			
			// Get all the answers for this question
			$ip = Hubzero_Environment::ipAddress();
			$answers = $ar->getRecords( array('ip'=>$ip,'qid'=>$id));

			if ($answers) {
				foreach ($answers as $answer)
				{
					// Delete response's log entry
					if (!$al->deleteLog($answer->id)) {
						JError::raiseError( 500, $al->getError() );
						return;
					}
				
					// Delete response
					if (!$ar->deleteResponse($answer->id)) {
						JError::raiseError( 500, $ar->getError() );
						return;
					}
				}
			}
			
			// Delete all tag associations	
			$tagging = new AnswersTags( $this->database );
			$tags = $tagging->remove_all_tags($id);
		}
		
		// Redirect
		$this->_message = JText::_('Question deleted');		
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------

	protected function state() 
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}

		$publish = ($this->_task == 'close') ? 1 : 0;

		// Check for an ID
		if (count( $ids ) < 1) {
			$action = ($publish == 1) ? 'close' : 'open';
			echo AnswersHtml::alert( JText::_('Select a question to '.$action) );
			exit;
		}

		// Load the plugins
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		
		foreach ($ids as $id) 
		{
			// Update record(s)
			$aq = new AnswersQuestion( $this->database );
			$aq->load($id);
			$aq->state = $publish;
			if ($publish == 1) {
				$aq->reward = 0;
			}
			if (!$aq->store()) {
				JError::raiseError( 500, $aq->getError() );
				return;
			}
			
			if ($publish == 1) {
				$creator =& JUser::getInstance($aq->created_by);
				
				if ($this->banking) {
					// Remove hold
					$BT = new BankTransaction( $this->database );
					$reward = $BT->getAmount( 'answers', 'hold', $id );
					$BT->deleteRecords( 'answers', 'hold', $id );
					
					// Make credit adjustment
					if (is_object($creator)) {
						$BTL = new BankTeller( $this->database, $creator->get('id') );
						$credit = $BTL->credit_summary();
						$adjusted = $credit - $reward;
						$BTL->credit_adjustment($adjusted);
					}
				}
				
				// Call the plugin
				if (!$dispatcher->trigger( 'onTakeAction', array( 'answers_reply_submitted', array($creator->get('id')), $this->_option, $id ))) {
					$this->setError( JText::_('Failed to remove alert.')  );
				}
			}
		}

		// set message
		if ($publish == 1) {
			$this->_message = JText::_(count( $ids ) .' Item(s) successfully Closed');
		} else if ($publish == 0) {
			$this->_message = JText::_(count( $ids ) .' Item(s) successfully Opened');
		}

		$this->_redirect = 'index.php?option='.$this->_option;
	}
	
	//-----------
	
	protected function accept() 
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$qid = JRequest::getInt( 'qid', 0 );
		$id = JRequest::getVar( 'id', array(0) );
		
		if (!is_array( $id )) {
			$id = array(0);
		}

		$publish = ($this->_task == 'accept') ? 1 : 0;

		// Check for an ID
		if (count( $id ) < 1) {
			$action = ($publish == 1) ? 'accept' : 'unaccept';
			echo AnswersHtml::alert( JText::_('Select an answer to '.$action) );
			exit;
		} else if (count( $id ) > 1) {
			echo AnswersHtml::alert( JText::_('A question can only have one accepted answer') );
			exit;
		}

		$ar = new AnswersResponse( $this->database );
		$ar->load($id[0]);
		$ar->state = $publish;
		if (!$ar->store()) {
			JError::raiseError( 500, $ar->getError() );
			return;
		}

		// Close the question if the answer is accepted
		$aq = new AnswersQuestion( $this->database );
		$aq->load($qid);
		
		if ($publish == 1) {
			$aq->state = 1;
			if ($publish == '1') {
				$aq->reward = 0;
			}
			if (!$aq->store()) {
				JError::raiseError( 500, $aq->getError() );
				return;
			}
			
			if ($this->banking) {
				// Calculate and distribute earned points
				$AE = new AnswersEconomy( $this->database );			
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
			$aq->state = 0;
			if (!$aq->store()) {
				JError::raiseError( 500, $aq->getError() );
				return;
			}
		}

		// Set message
		if ($publish == '1') {
			$this->_message = JText::_('Item successfully Accepted');
		} else if ( $publish == '0' ) {
			$this->_message = JText::_('Item successfully Unaccepted');
		}

		$this->_redirect  = 'index.php?option='.$this->_option;
		$this->_redirect .= ($qid) ? '&task=answers&qid='.$qid : '';
	}
	
	//-----------

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------
	
	protected function cancelResponse()
	{
		$qid = JRequest::getInt('qid', 0);
		
		$this->_redirect = 'index.php?option='.$this->_option.'&qid=';
	}

	//-----------
	
	private function _getPointReward($id)
	{
		// Check if question owner assigned a reward for answering his Q
		$BT = new BankTransaction( $this->database );
		return $BT->getAmount( 'answers', 'hold', $id );
	}
	
	//-----------
	
	private function _get_vote($id)
	{
		// Get the user's IP address
		$ip = Hubzero_Environment::ipAddress();
				
		// See if a person from this IP has already voted in the last week
		$aql = new AnswersQuestionsLog( $this->database );
		$voted = $aql->checkVote($id, $ip, $this->juser->get('id'));
	
		return $voted;
	}
	
	//-----------
	
	private function _getAbuseReports($id, $cat)
	{
		// Incoming
		$filters = array();
		$filters['id']  = $id;
		$filters['category']  = $cat;
		$filters['state']  = 0;
		
		// Check for abuse reports on an item
		$ra = new ReportAbuse( $this->database );
		
		return $ra->getCount( $filters );
	}
	
	//-----------

	protected function resetHelpful()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$answer = JRequest::getVar( 'answer', array() );
		
		// Reset some values
		$ar = new AnswersResponse( $this->database );
		$ar->load($answer['id']);
		$ar->helpful = 0;
		$ar->nothelpful = 0;
		if (!$ar->store()) {
			JError::raiseError( 500, $ar->getError() );
			return;
		}
	
		// Clear the history of "helpful" clicks
		$al = new AnswersLog( $this->database );
		if (!$al->deleteLog($answer['id'])) {
			JError::raiseError( 500, $al->getError() );
			return;
		}
		
		// Redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
		$this->_redirect .= ($answer['qid']) ? '&task=answers&qid='.$answer['qid'] : '';
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
}
