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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

class SupportCategory extends JTable 
{
	var $id       = NULL;  // @var int(11) Primary key
	var $category = NULL;  // @var varchar(50)
	var $section  = NULL;  // @var int(11)

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__support_categories', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->category ) == '') {
			$this->setError( JText::_('SUPPORT_ERROR_BLANK_FIELD') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	public function getCategories( $section=NULL ) 
	{
		if ($section !== NULL) {
			$section = ($section) ? $section : 1;
			$where = "WHERE section=$section";
		} else {
			$where = "";
		}
		
		$this->_db->setQuery( "SELECT category AS id, category AS txt FROM $this->_tbl $where ORDER BY category");
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function buildQuery( $filters=array() ) 
	{
		$query = " FROM $this->_tbl AS c, #__support_sections AS s"
				. " WHERE c.section=s.id";
		if (isset($filters['order']) && $filters['order'] != '') {
			$query .= " ORDER BY ".$filters['order'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		
		return $query;
	}
	
	//-----------
	
	public function getCount( $filters=array() ) 
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->buildQuery( $filters );
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() )
	{
		$filters['order'] = 'section, category';
		
		$query  = "SELECT c.id, c.category, s.section";
		$query .= $this->buildQuery( $filters );
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
?>