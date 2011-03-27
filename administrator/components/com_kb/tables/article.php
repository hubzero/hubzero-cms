<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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


class KbArticle extends JTable 
{
	var $id           = NULL;  // @var int(11) Primary key
	var $title        = NULL;  // @var varchar(250)
	var $params       = NULL;  // @var text
	var $fulltext     = NULL;  // @var text
	var $created      = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $created_by   = NULL;  // @var int(11)
	var $modified     = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $modified_by  = NULL;  // @var int(11)
	var $checked_out  = NULL;  // @var int(11)
	var $checked_out_time = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $state        = NULL;  // @var int(3)
	var $access       = NULL;  // @var int(3)
	var $hits         = NULL;  // @var int(11)
	var $version      = NULL;  // @var int(11)
	var $section      = NULL;  // @var int(11)
	var $category     = NULL;  // @var int(11)
	var $helpful      = NULL;  // @var int(11)
	var $nothelpful   = NULL;  // @var int(11)
	var $alias        = NULL;  // @var varchar(200)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__faq', 'id', $db );
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
	
	public function loadAlias( $oid=NULL, $cat=NULL ) 
	{
		if (empty($oid)) {
			return false;
		}
		$sql  = "SELECT * FROM $this->_tbl WHERE alias='$oid'";
		$sql .= ($cat) ? " AND section='$cat'" : '';
		$this->_db->setQuery( $sql );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function getCategoryArticles($noauth, $section, $category, $access) 
	{
		$juser =& JFactory::getUser();
		
		$query = "SELECT a.id, a.title, a.created, a.created_by, a.access, a.hits, a.section, a.category, a.helpful, a.nothelpful, a.alias, c.alias AS calias"
				. " FROM $this->_tbl AS a"
				. " LEFT JOIN #__faq_categories AS c ON c.id = a.category"
				. " WHERE a.section=".$section." AND a.category=".$category." AND a.state=1"
				. ( $noauth ? " AND a.access<='". $juser->get('aid') ."'" : '' )
				. " AND '". $access ."'<='". $juser->get('aid') ."'"
				. " ORDER BY a.modified DESC";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getArticles($limit, $order)
	{
		$juser =& JFactory::getUser();
		
		$query = "SELECT a.id, a.title, a.state, a.access, a.created, a.modified, a.hits, a.alias, c.alias AS category,  cc.alias AS section"
				." FROM $this->_tbl AS a"
				. " LEFT JOIN #__faq_categories AS c ON c.id = a.section"
				. " LEFT JOIN #__faq_categories AS cc ON cc.id = a.category"
				." WHERE a.state=1"
				." AND a.access <= ". $juser->get('aid') .""
				." ORDER BY ".$order
				." LIMIT ".$limit;
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getCollection( $cid=NULL )
	{
		if ($cid == NULL) {
			$cid = $this->category;
		}
		$query = "SELECT r.id, r.section, r.category"
				. " FROM $this->_tbl AS r"
				. " WHERE r.section=".$cid." OR r.category=".$cid;
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getArticlesCount( $filters=array() ) 
	{
		if (isset($filters['cid']) && $filters['cid']) {
			$where = "m.section=".$filters['cid']." AND m.category=".$filters['id'];
		} else {
			if (isset($filters['id']) && $filters['id']) {
				$where = "m.section=".$filters['id'];
			} else {
				$where = "m.section!=0";
			}
		}
		if (isset($filters['orphans'])) {
			$where = "m.section=0";
		}
		
		$query = "SELECT count(*) FROM $this->_tbl AS m WHERE ".$where;

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getArticlesAll( $filters=array() ) 
	{
		if (isset($filters['cid']) && $filters['cid']) {
			$where = "m.section=".$filters['cid']." AND m.category=".$filters['id'];
		} else {
			if (isset($filters['id']) && $filters['id']) {
				$where = "m.section=".$filters['id'];
			} else {
				$where = "m.section!=0";
			}
		}
		if (isset($filters['orphans'])) {
			$where = "m.section=0";
		}
		
		$query = "SELECT m.id, m.title, m.created, m.state, m.access, m.checked_out, m.section, m.category, m.helpful, m.nothelpful, m.alias, c.title AS ctitle, cc.title AS cctitle, u.name AS editor, g.name AS groupname"
			. " FROM $this->_tbl AS m"
			. " LEFT JOIN #__users AS u ON u.id = m.checked_out"
			. " LEFT JOIN #__groups AS g ON g.id = m.access"
			. " LEFT JOIN #__faq_categories AS c ON c.id = m.section"
			. " LEFT JOIN #__faq_categories AS cc ON cc.id = m.category"
			. " WHERE ".$where
			. " ORDER BY ".$filters['filterby']
			. " LIMIT ".$filters['start'].",".$filters['limit'];

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function deleteSef( $option, $id=NULL ) 
	{
		if ($id == NULL) {
			$id = $this->id;
		}
		$this->_db->setQuery( "DELETE FROM #__redirection WHERE newurl='index.php?option=".$option."&task=article&id=".$id."'" );
		if ($this->_db->query()) {
			return true;
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//----
	//----
	
	//-----------
	
	public function buildQuery( $filters=array() ) 
	{
		$sql = "FROM $this->_tbl AS m 
				LEFT JOIN #__faq_categories AS c ON c.id = m.section 
				LEFT JOIN #__faq_categories AS cc ON cc.id = m.category ";
		/*if (isset($filters['search']) && $filters['search'] != '') {
			$sql .= " LEFT JOIN #__tags_object AS tt ON tt.objectid=m.id AND tt.tbl='kb'";
			$sql .= " LEFT JOIN #__tags AS t ON tt.tagid=t.id";
		}*/
		if (isset($filters['user_id']) && $filters['user_id'] > 0) {
			$sql .= " LEFT JOIN #__faq_helpful_log AS v ON v.object_id=m.id AND v.user_id=".$filters['user_id']." AND v.type='entry' ";
		}
		
		$w = array();
		if (isset($filters['section']) && $filters['section']) {
			$w[] = "m.section=".$filters['section'];
		}
		if (isset($filters['category']) && $filters['category']) {
			$w[] = "m.category=".$filters['category'];
		}
		if (isset($filters['state'])) {
			$w[] = "m.state=".$filters['state'];
		}
		if (isset($filters['search']) && $filters['search'] != '') {
			/*$w[] = "(
					m.title LIKE '%".$filters['search']."%' 
					OR m.fulltext LIKE '%".$filters['search']."%' 
					OR t.raw_tag LIKE '%".$filters['search']."%' 
					OR t.tag LIKE '%".$filters['search']."%'
			)";*/
			$w[] = "(
					m.title LIKE '%".$filters['search']."%' 
					OR m.fulltext LIKE '%".$filters['search']."%' 
				)";
		}
		
		$sql .= (count($w) > 0) ? "WHERE " : "";
		$sql .= implode(" AND ",$w);
		
		if (isset($filters['order']) && $filters['order'] != '') {
			switch ($filters['order']) 
			{
				case 'recent': $order = 'm.modified DESC, m.created DESC'; break;
				//case 'created': $order = $filters['orderby'].' DESC'; break;
				case 'popularity': $order = '(m.helpful-m.nothelpful) DESC'; break;
				default: $order = $filters['order']; break;
			}
			$sql .= " ORDER BY ".$order;
		}
		if (isset($filters['limit']) && $filters['limit'] != '') {
			$sql .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		return $sql;
	}
	
	public function getCount( $filters=array() ) 
	{
		$query = "SELECT count(*) ".$this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() ) 
	{
		$query = "SELECT DISTINCT(m.id), m.title, m.created, m.state, m.access, m.modified, m.section, m.category, m.helpful, m.nothelpful, m.alias, c.title AS ctitle, c.alias AS calias, cc.title AS cctitle, cc.alias AS ccalias ";
		if (isset($filters['user_id']) && $filters['user_id'] > 0) {
			$query .= ", v.vote, v.user_id ";
		}
		$query .= $this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

