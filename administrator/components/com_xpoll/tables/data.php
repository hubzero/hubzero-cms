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


class XPollData extends JTable 
{
	var $id     = NULL; // @var int(11) Primary key
	var $pollid = NULL; // @var int(11)
	var $text   = NULL; // @var text
	var $hits   = NULL; // @var int(11)

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__xpoll_data', 'id', $db );
	}

	//-----------

	public function check() 
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
	
	public function getPollData( $poll_id=NULL ) 
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
	
	public function getPollOptions( $poll_id=NULL, $blanks=false ) 
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
