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

class FeaturesHistory extends JTable
{
	var $id          = NULL;  // int(11)
	var $featured    = NULL;  // datetime(0000-00-00 00:00:00)
	var $objectid    = NULL;  // string(100)
	var $tbl         = NULL;  // string(100)
	var $note        = NULL;  // string(100)

	//-----------

	function __construct( &$db ) 
	{
		parent::__construct( '#__feature_history', 'id', $db );
	}
	
	//-----------
	
	function loadActive( $start, $tbl='', $note='' ) 
	{
		$query  = "SELECT * FROM $this->_tbl WHERE featured='$start' AND tbl='$tbl'";
		$query .= ($note) ? " AND note='$note'" : '';
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	function loadObject( $objectid, $tbl='' ) 
	{
		$query = "SELECT * FROM $this->_tbl WHERE objectid='$objectid' AND tbl='$tbl'";
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	function getCount( $filters=array(), $authorized=false ) 
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->buildQuery( $filters, $authorized );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getRecords( $filters=array(), $authorized=false ) 
	{
		$query  = "SELECT *";
		$query .= $this->buildQuery( $filters, $authorized );
		if (isset($filters['limit']) && $filters['limit'] != 'all' && $filters['limit'] != '0') {
			$query .= " LIMIT " . $filters['start'] . ", " . $filters['limit'];
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function buildQuery($filters=array(), $authorized=false) 
	{
		$juser =& JFactory::getUser();
		
		// build body of query
		$query  = " FROM $this->_tbl AS f ";

		if (isset($filters['type']) && $filters['type'] != '') {
			$query .= " WHERE";
			if ($filters['type'] == 'tools') {
				$filters['type'] = 'resources';
				$filters['note'] = 'tools';
			} else if ($filters['type'] == 'resources') {
				$filters['note'] = 'nontools';
			}
			$query .= " f.tbl='" . $filters['type'] . "' ";
		}
		if (isset($filters['note']) && $filters['note'] != '') {
			if (isset($filters['type']) && $filters['type'] != '') {
				$query .= " AND";
			} else {
				$query .= " WHERE";
			}
			$query .= " f.note='" . $filters['note'] . "' ";
		}
		if (!$authorized) {
			$now = date( 'Y-m-d H:i:s' );
			if (isset($filters['note']) && $filters['note'] != '' && isset($filters['type']) && $filters['type'] != '') {
				$query .= " AND";
			} else {
				$query .= " WHERE";
			}
			$query .= " f.featured <= '$now'";
		}
		$query .= " ORDER BY f.featured DESC, f.id ASC";

		return $query;
	}
}