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

//----------------------------------------------------------
// Answers question database class
//----------------------------------------------------------

class AnswersQuestion extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $subject    = NULL;  // @var varchar(250)
	var $question   = NULL;  // @var text
	var $created    = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $created_by = NULL;  // @var int(11)
	var $state      = NULL;  // @var int(3)
	var $anonymous  = NULL;  // @var int(2)
	var $email      = NULL;  // @var int(2)
	var $helpful    = NULL;  // @var int(11)
	var $reward 	= NULL;  // @var int(2)
	
	function __construct( &$db )
	{
		parent::__construct( '#__answers_questions', 'id', $db );
	}
	
	function check() 
	{
		if (trim( $this->subject ) == '') {
			$this->setError( JText::_('Your question must contain a subject.') );
			return false;
		}
		return true;
	}
	
	function buildQuery($filters=array()) 
	{
		$juser =& JFactory::getUser();
		
		// build body of query
		$query  = "";
		if ($filters['tag']) {
			include_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'answers.tags.php' );
			$tagging = new AnswersTags( $this->_db );
			
			$query .= "FROM $this->_tbl AS C";
			if(isset($filters['count'])) {
			$query .= " JOIN #__tags_object AS RTA ON RTA.objectid=C.id AND RTA.tbl='answers' ";
			}
			else {
			$query .= ", #__tags_object AS RTA ";
			}
			$query .= "INNER JOIN $tagging->_tag_tbl AS TA ON TA.id=RTA.tagid ";
		} else {
			$query .= "FROM $this->_tbl AS C ";
		}
		
		
		switch ($filters['filterby'])
		{
			case 'mine':   		$query .= "WHERE C.state!=2 "; $filters['mine'] = 1;       break;
			case 'all':    		$query .= "WHERE C.state!=2 ";      break;
			case 'closed': 		$query .= "WHERE C.state=1 "; 		break;
			case 'open':   		$query .= "WHERE C.state=0 "; 		break;
			case 'none':   		$query .= "WHERE 1=2 "; 			break;
			default:       		$query .= "WHERE C.state!=2 "; 		break;
		}
		if (isset($filters['q']) && $filters['q'] != '') {
			$words   = explode(' ', $filters['q']);
			foreach ($words as $word) 
			{
				$query .= "AND ( (LOWER(C.subject) LIKE '%$word%') 
					OR (LOWER(C.question) LIKE '%$word%') 
					OR (SELECT COUNT(*) FROM #__answers_responses AS a WHERE a.state!=2 AND a.qid=C.id AND (LOWER(a.answer) LIKE '%$word%')) > 0 )";
			}
		}
		if (isset($filters['mine']) && $filters['mine'] != 0) {
			$query .= " AND C.created_by='" . $juser->get('username') . "' ";
		}
		if (isset($filters['mine']) && $filters['mine'] == 0) {
			$query .= " AND C.created_by!='" . $juser->get('username') . "' ";
		}
		if (isset($filters['created_before']) && $filters['created_before'] != '') {
			$query .= " AND C.created <='" . $filters['created_before'] . "' ";
		}
		if ($filters['tag']) {
			
			$tags = $tagging->_parse_tags($filters['tag']);

			$query .= "AND (RTA.objectid=C.id AND (RTA.tbl='answers') AND (TA.tag IN (";
			$tquery = '';
			foreach ($tags as $tagg)
			{
				$tquery .= "'".$tagg."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.") OR TA.raw_tag IN (".$tquery;
			$query .= ")))";
			//$query .= " GROUP BY C.id HAVING uniques=".count($tags);
			if(!isset($filters['count'])) {
			$query .= " GROUP BY C.id ";
			}
		}
		switch ($filters['sortby'])
		{
			case 'rewards':   $query .= " ORDER BY C.reward DESC, points DESC, C.created DESC"; break;
			case 'votes':     $query .= " ORDER BY C.helpful DESC, C.created DESC"; break;
			case 'date':      $query .= " ORDER BY C.created DESC"; break;
			case 'random':    $query .= " ORDER BY RAND()"; break;
			case 'responses': $query .= " ORDER BY rcount DESC, C.reward DESC, points DESC, C.state ASC, C.created DESC"; break;
			case 'status':    $query .= " ORDER BY C.reward DESC, points DESC, C.state ASC, C.created DESC"; break;
			case 'withinplugin':   $query .= " ORDER BY C.reward DESC, points DESC, C.state ASC, C.created DESC"; break;
			default:       	  $query .= " "; break;
		}

		return $query;
	}
	
	function getCount($filters=array()) 
	{
		$query  = "SELECT COUNT(C.id) ";
		//$query .= ", (SELECT SUM(tr.amount) FROM #__users_transactions AS tr WHERE tr.category='answers' AND tr.type='hold' AND tr.referenceid=C.id ) AS points";
		//$query .= (isset($filters['tag']) && $filters['tag']!='') ? ", TA.tag, COUNT(DISTINCT TA.tag) AS uniques " : " ";
			
		$filters['sortby'] = '';
		$filters['count'] = 1;
		$query .= $this->buildQuery( $filters );
	
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	function getResults($filters=array()) 
	{
		$ar = new AnswersResponse( $this->_db );
		
		$query  = "SELECT C.id, C.subject, C.question, C.created, C.created_by, C.state, C.anonymous, C.reward, C.helpful";
		$query .= ", (SELECT COUNT(*) FROM $ar->_tbl AS a WHERE a.state!=2 AND a.qid=C.id) AS rcount";
		$query .= ", (SELECT SUM(tr.amount) FROM #__users_transactions AS tr WHERE tr.category='answers' AND tr.type='hold' AND tr.referenceid=C.id ) AS points";
		$query .= ($filters['tag']) ? ", TA.tag, COUNT(DISTINCT TA.tag) AS uniques " : " ";
		$query .= $this->buildQuery( $filters );
		$query .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : "";
	
		//$query .= " LIMIT " . $filters['start'] . ", " . $filters['limit'];
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	function getQuestionsByTag( $tag, $limit=100 ) 
	{
		$query = "SELECT a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous, (SELECT COUNT(*) FROM #__answers_responses AS r WHERE r.qid=a.id) AS rcount";
		//$query.= "\n FROM $this->_tbl AS a, #__answers_tags AS t, #__tags AS tg";
		$query .= " FROM $this->_tbl AS a, #__tags_object AS RTA ";
		$query .= "INNER JOIN #__tags AS TA ON TA.id=RTA.tagid ";
		$query.= "\n WHERE RTA.objectid=a.id AND RTA.tbl='answers' AND (TA.tag='".strtolower($tag)."' OR TA.raw_tag='".$tag."' OR TA.alias='".$tag."')";

		$query .= "\n ORDER BY a.created DESC";
		$query .= ($limit) ? "\n LIMIT ".$limit : "";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

//----------------------------------------------------------
// Answers response database class
//----------------------------------------------------------

class AnswersResponse extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $qid        = NULL;  // @var int(11)
	var $answer     = NULL;  // @var text
	var $created    = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $created_by = NULL;  // @var varchar(200)
	var $helpful    = NULL;  // @var int(11)
	var $nothelpful = NULL;  // @var int(11)
	var $state      = NULL;  // @var int(3)
	var $anonymous  = NULL;  // @var int(2)
	
	function __construct( &$db )
	{
		parent::__construct( '#__answers_responses', 'id', $db );
	}
	
	function check() 
	{
		if (trim( $this->answer ) == '') {
			$this->setError( JText::_('Your response must contain text.') );
			return false;
		}
		return true;
	}
	
	function getRecords($filters=array()) 
	{
		$juser =& JFactory::getUser();

		$ab = new ReportAbuse( $this->_db );

		if (isset($filters['qid'])) {
			$qid = $filters['qid'];
		} else {
			$qid = $this->qid;
		}
		if ($qid == null) {
			return false;
		}

		if (!$juser->get('guest')) {
			$query  = "SELECT r.*";
			$query .= ", (SELECT COUNT(*) FROM $ab->_tbl AS a WHERE a.category='answers' AND a.state=0 AND a.referenceid=r.id) AS reports";
			$query .= ", l.helpful AS vote FROM $this->_tbl AS r LEFT JOIN #__answers_log AS l ON r.id=l.rid AND ip='".$filters['ip']."' WHERE r.state!=2 AND r.qid=".$qid;
		} else {
			$query  = "SELECT r.*";
			$query .= ", (SELECT COUNT(*) FROM $ab->_tbl AS a WHERE a.category='answers' AND a.state=0 AND a.referenceid=r.id) AS reports";
			$query .= " FROM $this->_tbl AS r WHERE r.state!=2 AND r.qid=".$qid;
		}
		$query .= " ORDER BY r.state DESC, r.created DESC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	function getActions( $qid=null ) 
	{
		if ($qid == null) {
			$qid = $this->qid;
		}
		if ($qid == null) {
			return false;
		}
		
		$query = "SELECT id, helpful, nothelpful, state, created_by FROM $this->_tbl WHERE qid=".$qid." AND state!='2'";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	function getResponse( $id=null, $ip = null ) 
	{
		if ($id == null) {
			$id = $this->id;
		}
		if ($id == null) {
			return false;
		}
		if ($ip == null) {
			$ip = $this->ip;
		}
		if ($ip == null) {
			return false;
		}
		
		$query  = "SELECT r.*, l.helpful AS vote FROM $this->_tbl AS r LEFT JOIN #__answers_log AS l ON r.id=l.rid AND ip='".$ip."' WHERE r.state!=2 AND r.id=".$id;
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//------------
	
	function deleteResponse( $id=null) 
	{
		if ($id == null) {
			$id = $this->id;
		}
		if ($id == null) {
			return false;
		}
		
		$query  = "UPDATE $this->_tbl SET state='2' WHERE id=".$id;
		
		$this->_db->setQuery( $query );
		$this->_db->query();
	}
	
	//------------
	
	function getIds( $qid=null) 
	{
		if ($qid == null) {
			$qid = $this->qid;
		}
		if ($qid == null) {
			return false;
		}
		
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE qid=".$qid );
		return $this->_db->loadObjectList();
	}
}

//----------------------------------------------------------
// Answers questions log database class
//----------------------------------------------------------

class AnswersQuestionsLog extends JTable 
{
	var $id      = NULL;  // @var int(11) Primary key
	var $qid     = NULL;  // @var int(11)
	var $expires = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $voter   = NULL;  // @var int(11)
	var $ip      = NULL;  // @var varchar(15)
	
	function __construct( &$db )
	{
		parent::__construct( '#__answers_questions_log', 'id', $db );
	}
	
	function check() 
	{
		if (trim( $this->qid ) == '') {
			$this->setError( JText::_('Missing question ID') );
			return false;
		}
		return true;
	}
	
	function checkVote($qid=null, $ip=null, $voter=null) 
	{
		if ($qid == null) {
			$qid = $this->qid;
		}
		if ($qid == null) {
			return false;
		}
		
		$now = date( 'Y-m-d H:i:s', time() );
		
		//$query = "SELECT count(*) FROM $this->_tbl WHERE qid='".$qid."' AND expires > '".$now."' AND (ip='".$ip."' OR voter='".$voter."')";
		$query = "SELECT count(*) FROM $this->_tbl WHERE qid='".$qid."' AND (ip='".$ip."' OR voter='".$voter."')";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}

//----------------------------------------------------------
// Answers log database class
//----------------------------------------------------------

class AnswersLog extends JTable 
{
	var $id      = NULL;  // @var int(11) Primary key
	var $rid     = NULL;  // @var int(11)
	var $ip      = NULL;  // @var varchar(15)
	var $helpful = NULL;  // @var varchar(10)
	
	function __construct( &$db )
	{
		parent::__construct( '#__answers_log', 'id', $db );
	}
	
	function check() 
	{
		if (trim( $this->rid ) == '') {
			$this->setError( JText::_('Missing response ID') );
			return false;
		}
		return true;
	}
	
	function checkVote($rid=null, $ip=null) 
	{
		if ($rid == null) {
			$rid = $this->rid;
		}
		if ($rid == null) {
			return false;
		}
		
		$query = "SELECT helpful FROM $this->_tbl WHERE rid='".$rid."' AND ip='".$ip."'";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	function deleteLog($rid=null) 
	{
		if ($rid == null) {
			$rid = $this->rid;
		}
		if ($rid == null) {
			return false;
		}
		
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE rid=".$rid );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		
		return true;
	}
}

//----------------------------------------------------------
// Backwards compatibility
//----------------------------------------------------------

class mosAnswersQuestion extends AnswersQuestion 
{
}

class mosAnswersResponse extends AnswersResponse
{	
}
?>