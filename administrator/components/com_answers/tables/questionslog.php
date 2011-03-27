<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class AnswersQuestionsLog extends JTable 
{
	var $id      = NULL;  // @var int(11) Primary key
	var $qid     = NULL;  // @var int(11)
	var $expires = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $voter   = NULL;  // @var int(11)
	var $ip      = NULL;  // @var varchar(15)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__answers_questions_log', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->qid ) == '') {
			$this->setError( JText::_('Missing question ID') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function checkVote($qid=null, $ip=null, $voter=null) 
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

