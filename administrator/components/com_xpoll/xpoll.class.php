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

class XPollPoll extends JTable 
{
	var $id               = NULL; // @var int Primary key
	var $title            = NULL; // @var string
	var $voters           = NULL; // @var int(9)
	var $checked_out      = NULL; // @var int(11)
	var $checked_out_time = NULL; // @var datetime(0000-00-00 00:00:00)
	var $published        = NULL; // @var tinyint(1)
	var $access	          = NULL; // @var int(11)
	var $lag              = NULL; // @var int(11)
	var $open             = NULL; // @var tinyint(1)

	//-----------

	function __construct( &$db ) 
	{
		parent::__construct( '#__xpolls', 'id', $db );
	}

	//-----------

	function check() 
	{
		// Check for valid name
		if (trim( $this->title ) == '') {
			$this->setError( JText::_('XPOLL_ERROR_MUST_HAVE_A_TITLE') );
			return false;
		}
		
		// Check for valid lag
		$this->lag = intval( $this->lag );
		if ($this->lag == 0) {
			$this->setError( JText::_('XPOLL_ERROR_MUST_HAVE_A_NON-ZERO_LAG_TIME') );
			return false;
		}
		
		// Check for existing title
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE title='$this->title'");
		$xid = intval( $this->_db->loadResult() );
		if ($xid && $xid != intval( $this->id )) {
			$this->setError( JText::_('XPOLL_ERROR_TITLE_EXIST') );
			return false;
		}

		// Sanitise some data
		if (!get_magic_quotes_gpc()) {
			$row->title = addslashes( $row->title );
		}

		return true;
	}

	//-----------

	function delete( $oid=NULL ) 
	{
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}

		if (JTable::delete( $oid )) {
			$this->_db->setQuery( "DELETE FROM #__xpoll_data WHERE pollid='".$this->$k."'" );
			if (!$this->_db->query()) {
				$this->setError( $this->_db->getErrorMsg() . "\n" );
			}

			$this->_db->setQuery( "DELETE FROM #__xpoll_date WHERE poll_id='".$this->$k."'" );
			if (!$this->_db->query()) {
				$this->setError( $this->_db->getErrorMsg() . "\n" );
			}

			$this->_db->setQuery( "DELETE from #__xpoll_menu where pollid='".$this->$k."'" );
			if (!$this->_db->query()) {
				$this->setError( $this->_db->getErrorMsg() . "\n" );
			}

			return true;
		} else {
			return false;
		}
	}
	
	//-----------
	
	function getAllPolls() 
	{
		$query = "SELECT id, title FROM $this->_tbl WHERE published=1 ORDER BY id";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function getLatestPoll() 
	{
		$Itemid = 1;
		
		$query = "SELECT p.id, p.title"
				."\n FROM #__xpoll_menu AS pm, $this->_tbl AS p"
				."\n WHERE (pm.menuid='$Itemid' OR pm.menuid='0') AND p.id=pm.pollid"
				."\n AND p.published=1 AND p.open=1 ORDER BY id DESC LIMIT 1";

		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	function increaseVoteCount( $poll_id=NULL ) 
	{
		if ($poll_id == NULL) {
			$poll_id = $this->id;
		}
		
		$this->_db->setQuery( "UPDATE $this->_tbl SET voters=voters + 1 WHERE id='$poll_id'" );
		if (!$this->_db->query()) {
			echo $this->_db->getErrorMsg();
			exit;
		}
	}
	
	//-----------
	
	function buildQuery( $filters=array() ) 
	{
		$query = " FROM $this->_tbl AS m";

		return $query;
	}
	
	//-----------
	
	function getCount( $filters ) 
	{
		$query  = "SELECT COUNT(m.id)";
		$query .= $this->buildquery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getRecords( $filters ) 
	{
		$query = "SELECT m.*, u.name AS editor, COUNT(d.id) AS numoptions";
		$query .= $this->buildquery( $filters );
		$query .= " LEFT JOIN #__users AS u ON u.id = m.checked_out";
		$query .= " LEFT JOIN #__xpoll_data AS d ON d.pollid = m.id AND d.text <> ''";
		$query .= " GROUP BY m.id";
		$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}


class XPollData extends JTable 
{
	var $id     = NULL; // @var int(11) Primary key
	var $pollid = NULL; // @var int(11)
	var $text   = NULL; // @var text
	var $hits   = NULL; // @var int(11)

	//-----------

	function __construct( &$db ) 
	{
		parent::__construct( '#__xpoll_data', 'id', $db );
	}

	//-----------

	function check() 
	{
		// Check for pollid
		if ($this->pollid == '') {
			$this->setError( JText::_('XPOLL_ERROR_MISSING_POLL_ID') );
			return false;
		}

		// Sanitise some data
		if (!get_magic_quotes_gpc()) {
			$row->text = addslashes( $row->text );
		}

		return true;
	}
	
	//-----------
	
	function getPollData( $poll_id=NULL ) 
	{
		if ($poll_id == NULL) {
			$poll_id = $this->pollid;
		}
		$query = "SELECT a.id, a.text, count( DISTINCT b.id ) AS hits, count( DISTINCT b.id )/COUNT( DISTINCT a.id )*100.0 AS percent"
			. "\n FROM #__xpoll_data AS a"
			. "\n LEFT JOIN #__xpoll_date AS b ON b.vote_id = a.id"
			. "\n WHERE a.pollid = $poll_id"
			. "\n AND a.text != ''"
			. "\n GROUP BY a.id"
			. "\n ORDER BY a.id"
			;
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function getPollOptions( $poll_id=NULL, $blanks=false ) 
	{
		if ($poll_id == NULL) {
			$poll_id = $this->pollid;
		}
		$query = "SELECT id, text FROM $this->_tbl"
				. " WHERE pollid='$poll_id'";
		if (!$blanks) {
			$query .= " AND text <> ''";
		}
		$query .= " ORDER BY id";
				
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}


class XPollDate extends JTable 
{
	var $id       = NULL; // @var int(11) Primary key
	var $date     = NULL; // @var datetime(0000-00-00 00:00:00)
	var $vote_id  = NULL; // @var int(11)
	var $poll_id  = NULL; // @var int(11)
	var $voter_ip = NULL; // @var varchar(50)

	//-----------

	function __construct( &$db ) 
	{
		parent::__construct( '#__xpoll_date', 'id', $db );
	}
	
	//-----------

	function check() 
	{
		// Check for pollid
		if ($this->vote_id == '') {
			$this->setError( JText::_('XPOLL_ERROR_MISSING_VOTE_ID') );
			return false;
		}
		
		// Check for pollid
		if ($this->poll_id == '') {
			$this->setError( JText::_('XPOLL_ERROR_MISSING_POLL_ID') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	function getMinMaxDates( $poll_id=NULL ) 
	{
		if ($poll_id == NULL) {
			$poll_id = $this->poll_id;
		}
		$query = "SELECT MIN(date) AS mindate, MAX(date) AS maxdate"
				." FROM $this->_tbl"
				." WHERE poll_id='$poll_id'";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function deleteEntries( $poll_id=NULL ) 
	{
		if ($poll_id == NULL) {
			$poll_id = $this->poll_id;
		}
		
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE poll_id='$poll_id'" );
		if (!$this->_db->query()) {
			echo $this->_db->getErrorMsg();
			exit();
		}
	}
}


class XPollMenu extends JTable 
{
	var $pollid  = NULL; // @var int(11)
	var $menuid  = NULL; // @var int(11)

	//-----------

	function XPollMenu( &$db ) 
	{
		parent::__construct( '#__xpoll_menu', 'pollid', $db );
	}
	
	//-----------

	function check() 
	{
		// Check for pollid
		if ($this->menuid == '') {
			$this->setError( JText::_('XPOLL_ERROR_MISSING_MENU_ID') );
			return false;
		}
		
		// Check for pollid
		if ($this->pollid == '') {
			$this->setError( JText::_('XPOLL_ERROR_MISSING_POLL_ID') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	function deleteEntries( $poll_id=NULL ) 
	{
		if ($poll_id == NULL) {
			$poll_id = $this->pollid;
		}
		
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE pollid='$poll_id'" );
		if (!$this->_db->query()) {
			echo $this->_db->getErrorMsg();
			exit();
		}
	}
	
	//-----------
	
	function insertEntry( $poll_id=NULL, $menu_id=NULL ) 
	{
		if ($poll_id == NULL) {
			$poll_id = $this->pollid;
		}
		if ($menu_id == NULL) {
			$menu_id = $this->menuid;
		}
		
		$this->_db->setQuery( "INSERT INTO $this->_tbl (pollid, menuid) VALUES ($poll_id, $menu_id)" );
		if (!$this->_db->query()) {
			echo $this->_db->getErrorMsg();
			//exit();
		}
	}
	
	//-----------
	
	function getMenuIds( $poll_id=NULL ) 
	{
		if ($poll_id == NULL) {
			$poll_id = $this->pollid;
		}
		$this->_db->setQuery( "SELECT menuid AS value FROM $this->_tbl WHERE pollid='$poll_id'" );
		return $this->_db->loadObjectList();
	}
}
?>