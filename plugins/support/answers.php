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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_support_answers' );

//-----------

class plgSupportAnswers extends JPlugin
{
	public function plgSupportAnswers(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'support', 'answers' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function getReportedItem($refid, $category, $parent) 
	{
		if ($category != 'answer' && $category != 'question' && $category != 'answercomment') {
			return null;
		}
		
		switch ($category) 
		{
			case 'answer':
				$query  = "SELECT r.id, r.answer as text, NULL as subject";
				$query .= ", r.anonymous as anon, r.created_by as author, 'answer' as parent_category, NULL as href";
				$query .= " FROM #__answers_responses AS r"; 
				$query .= " WHERE r.state!=2 AND r.id=".$refid;    
			break;
				
			case 'question':
				$query  = "SELECT q.id, q.subject as text, q.created_by as author, q.question as subject";
				$query .= ", 'question' as parent_category, q.anonymous as anon, NULL as href";
				$query .= " FROM #__answers_questions AS q";
				$query .= " WHERE q.id=".$refid;    
			break;
		}
		
		$database =& JFactory::getDBO();
		$database->setQuery( $query );
		//return $database->loadObjectList();
		$rows = $database->loadObjectList();
		if ($rows) {
			foreach ($rows as $key => $row) 
			{
				switch ($category) 
				{
					case 'answer':
						$rows[$key]->href = ($parent) ? JRoute::_('index.php?option=com_answers&task=question&id='.$parent) : '';
					break;
					case 'question':
						$rows[$key]->href = JRoute::_('index.php?option=com_answers&task=question&id='.$rows[$key]->id);
					break;
				}
			}
		}
		return $rows;
	}
	
	//-----------
	
	public function getParentId( $parentid, $category ) 
	{
		ximport('xcomment');
		
		$database =& JFactory::getDBO();
		$refid = $parentid;
		
		if ($category == 'answercomment') {
			$pdata = $this->parent($parentid);
			$category = $pdata->category;
			$refid = $pdata->referenceid;

			if ($pdata->category == 'answercomment') {
				// Yet another level?
				$pdata = $this->parent($pdata->referenceid);
				$category = $pdata->category;
				$refid = $pdata->referenceid;

				if ($pdata->category == 'answercomment') {
					// Yet another level?
					$pdata = $this->parent($pdata->referenceid);
					$category = $pdata->category;
					$refid = $pdata->referenceid;
				}
			}
		}
		
		if ($category == 'answer') {
			$database->setQuery( "SELECT qid FROM #__answers_responses WHERE id=".$refid );
			$pid = $database->loadResult();
		 	return $pid;
		}
		
		if ($category == 'question') {
		 	return $refid;
		}
	}
	
	//-----------
	
	public function parent($parentid) 
	{
		$database =& JFactory::getDBO();
		$parent = new XComment( $database );
		$parent->load( $parentid );
		
		return $parent;
	}
	
	//-----------
	
	public function getTitle($category, $parentid) 
	{
		if ($category != 'answer' && $category != 'question' && $category != 'answercomment') {
			return null;
		}
		
		switch ($category) 
		{
			case 'answer': 
				return JText::sprintf('Answer to question #%s', $parentid);		
         	break;
			
			case 'question': 
				return JText::sprintf('Question #%s', $parentid);
         	break;

			case 'answercomment': 
				return JText::sprintf('Comment to an answer for question #%s', $parentid);
         	break;
		}
	}
	
	//-----------
	
	public function deleteReportedItem($referenceid, $parentid, $category, $message) 
	{
		if ($category != 'answer' && $category != 'question' && $category != 'answercomment') {
			return null;
		}
		
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		$xuser 	  =& XFactory::getUser();
		
		switch ($category)
		{
			case 'answer':
				$database->setQuery( "UPDATE #__answers_responses SET state='2' WHERE id=".$referenceid );
				if (!$database->query()) {
					echo ReportAbuseHtml::alert( $database->getErrorMsg() );
					exit;
				}
				
				$database->setQuery( "DELETE FROM #__answers_log WHERE rid=".$referenceid );
				if (!$database->query()) {
					echo ReportAbuseHtml::alert( $database->getErrorMsg() );
					exit;
				}
				
				$message .= JText::sprintf('This is to notify you that your answer to question #%s was removed from the site due to granted complaint received from a user.', $parentid);
			break;
			
			case 'question': 
				$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
				$banking = $upconfig->get('bankAccounts');
				
				$reward = 0;
				if ($banking) {
					$reward = $this->get_reward($parentid);
				}
				$responders = array();
				
				// Get all the answers for this question
				$database->setQuery( "SELECT r.id, r.created_by FROM #__answers_responses AS r WHERE r.qid=".$referenceid );
				$answers = $database->loadObjectList();
	
				if ($answers) {
					foreach ($answers as $answer)
					{
						// Delete response's log entry
						$database->setQuery( "DELETE FROM #__answers_log WHERE rid=".$answer->id );
						if (!$database->query()) {
							echo ReportAbuseHtml::alert( $database->getErrorMsg() );
							exit;
						}
					
						// Delete response
						$database->setQuery( "UPDATE #__answers_responses SET state='2' WHERE id=".$answer->id );
						if (!$database->query()) {
							echo ReportAbuseHtml::alert( $database->getErrorMsg() );
							exit;
						}
						
						// Collect responders names
						$responders[] = $answer->created_by;
					}
				}
				
				// Delete all tag associations
				$database->setQuery( "DELETE FROM #__answers_tags WHERE questionid=".$referenceid );
				if (!$database->query()) {
					echo ReportAbuseHtml::alert( $database->getErrorMsg() );
					exit;
				}
	
				$database->setQuery( "UPDATE #__answers_questions SET state='2', reward='0' WHERE id=".$referenceid );
				if (!$database->query()) {
					echo ReportAbuseHtml::alert( $database->getErrorMsg() );
					exit;
				}
				
				if ($banking && $reward) {
					ximport('bankaccount');
					
					// Send email to people who answered question with reward
					if ($responders) {
						foreach ($responders as $r) 
						{
							$zuser =& XUser::getInstance( $r );
							if (is_object($zuser)) {
								if (SupportUtils::check_validEmail($zuser->get('email')) && $email) {
									$xhub =& XFactory::getHub();
									
									$admin_email = $xhub->getCfg('hubSupportEmail');
									$sub  = $xhub->getCfg('hubShortName').' Answers, Question #'.$referenceid.' was removed';
									$from = $xhub->getCfg('hubShortName').' Answers';
									$hub  = array('email' => $admin_email, 'name' => $from);

									$mes  = 'You are receiving this email because you responded to a question, which has been removed by the site administrator. ';
									$mes .= 'As a result, no points for this question will be awarded. We appologize for inconvenience.'."\r\n";
									$mes .= '----------------------------'."\r\n\r\n";
									$mes .= 'QUESTION: '.$referenceid."\r\n";

									SupportUtils::send_email($hub, $zuser->get('email'), $sub, $mes);
							 	}
							}
						}
					}
					
					// get id of asker
					$database->setQuery( "SELECT created_by FROM #__answers_questions WHERE id=".$parentid );
					$asker = $database->loadResult();
						
					if ($asker) {
						$quser =& XUser::getInstance( $asker );
						if (is_object($quser)) {
							$asker_id = $quser->get('uid');
						}
							
						if (isset($asker_id) ) {
							// Remove hold 
							$sql = "DELETE FROM #__users_transactions WHERE category='answers' AND type='hold' AND referenceid=".$parentid." AND uid='".$asker_id."'";
							$database->setQuery( $sql);
							if (!$database->query()) {
								echo ReportAbuseHtml::alert( $database->getErrorMsg() );
								exit;
							}
									
							// Make credit adjustment
							$BTL_Q = new BankTeller( $database, $asker_id );
							$credit = $BTL_Q->credit_summary();
							$adjusted = $credit - $reward;
							$BTL_Q->credit_adjustment($adjusted);		
						}
					}
				}
				
				$message .= JText::sprintf('This is to notify you that your question #%s was removed from the site due to granted complaint received from a user.', $parentid);
			break;
			
			case 'answercomment':
				ximport('xcomment');
				
				$comment = new XComment( $database );
				$comment->load( $referenceid );
				$comment->state = 2;
				if (!$comment->store()) {
					echo ReportAbuseHtml::alert( $comment->getError() );
					exit();
				}
				
				$message .= JText::sprintf('This is to notify you that your comment on an answer to question #%s was removed from the site due to granted complaint received from a user.', $parentid);
			break;
		}
		
		return $message;
	}
	
	//-----------
	
	public function get_reward($id)
	{
		$database =& JFactory::getDBO();
		
		// check if question owner assigned a reward for answering his Q 
		$sql = "SELECT amount FROM #__users_transactions WHERE category='answers' AND type='hold' AND referenceid=".$id;
		$database->setQuery( $sql );
		$reward = $database->loadResult();
			
		return $reward;
	}
}
