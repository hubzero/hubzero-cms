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


class KbCategory extends JTable 
{
	var $id           = NULL;  // @var int(11) Primary key
	var $title        = NULL;  // @var varchar(250)
	var $description  = NULL;  // @var text
	var $section      = NULL;  // @var int(1)
	var $state        = NULL;  // @var int(3)
	var $access       = NULL;  // @var int(3)
	var $alias        = NULL;  // @var varchar(200)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__faq_categories', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->title ) == '') {
			$this->setError( JText::_('KB_ERROR_EMPTY_TITLE') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function loadAlias( $oid=NULL ) 
	{
		if (empty($oid)) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE alias='$oid'" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function getCategories( $noauth, $empty_cat=0, $catid=0 )
	{
        $juser =& JFactory::getUser();

		if ($empty_cat) {
			$empty = '';
		} else {
			$empty = "\n HAVING COUNT( b.id ) > 0";
		}
		
		if ($catid) {
			$sect = "b.category";
		} else {
			$sect = "b.section";
		}
		
		$query = "SELECT a.*, COUNT( b.id ) AS numitems"
				. " FROM $this->_tbl AS a"
				. " LEFT JOIN #__faq AS b ON ".$sect." = a.id AND b.state=1 AND b.access=0"
				. " WHERE a.state=1 AND a.section=".$catid
				. ( $noauth ? " AND a.access <= '". $juser->get('aid') ."'" : '' )
				. " GROUP BY a.id"
				. $empty
				. " ORDER BY a.title";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function deleteSef( $option, $id=NULL ) 
	{
		if ($id == NULL) {
			$id = $this->id;
		}
		$this->_db->setQuery( "DELETE FROM #__redirection WHERE newurl='index.php?option=".$option."&task=category&id=".$id."'" );
		if ($this->_db->query()) {
			return true;
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function getAllSections() 
	{
		$this->_db->setQuery( "SELECT m.id, m.title, m.alias FROM $this->_tbl AS m WHERE m.section=0 ORDER BY m.title" );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getAllCategories() 
	{
		$this->_db->setQuery( "SELECT m.id, m.title, m.alias FROM $this->_tbl AS m WHERE m.section!=0 ORDER BY m.title" );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getCategoriesCount( $filters=array() ) 
	{
		$query  = "SELECT count(*) FROM $this->_tbl WHERE section=";
		$query .= (isset($filters['id'])) ? $filters['id'] : "0";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getCategoriesAll( $filters=array() ) 
	{
		if (isset($filters['id']) && $filters['id']) {
			$sect = $filters['id'];
			$sfield = "category";
		} else {
			$sect = 0;
			$sfield = "section";
		}
		
		$query = "SELECT m.id, m.title, m.section, m.state, m.access, m.alias, g.name AS groupname, 
				(SELECT count(*) FROM #__faq AS fa WHERE fa.".$sfield."=m.id) AS total, 
				(SELECT count(*) FROM $this->_tbl AS fc WHERE fc.section=m.id) AS cats"
			. " FROM #__faq_categories AS m"
			. " LEFT JOIN #__groups AS g ON g.id = m.access"
			. " WHERE m.section=".$sect
			. " ORDER BY ".$filters['filterby']
			. " LIMIT ".$filters['start'].",".$filters['limit'];
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
