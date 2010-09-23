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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class JobCategory extends JTable
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $category		= NULL;  // @var varchar(150)
	var $description	= NULL;  // @var varchar(255)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_categories', 'id', $db );
	}
	
	//-----------
	
	public function getCats ($sortby = 'ordernum', $sortdir = 'ASC', $getobject = 0)
	{
		$cats = array();
		
		$query  = $getobject ? "SELECT * " : "SELECT id, category ";
		$query .= "FROM #__jobs_categories   ";
		$query .= " ORDER BY $sortby $sortdir";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if ($getobject) {
			return $result;
		}
		
		if ($result) {
			foreach ($result as $r) 
			{
				$cats[$r->id] = $r->category;
			}
		}
		
		return $cats;		
	}
	
	//-----------
	
	public function getCat($id = NULL, $default = 'unspecified' )
	{
		if ($id === NULL) {
			 return false;
		}
		if ($id == 0 ) {
			return $default;
		}
		
		$query  = "SELECT category ";
		$query .= "FROM #__jobs_categories WHERE id='".$id."'  ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();		
	}
	
	//-----------
	
	public function updateOrder($id = NULL, $ordernum = 1 )
	{
		if ($id === NULL or !intval($ordernum)) {
			 return false;
		}
	
		$query  = "UPDATE $this->_tbl SET ordernum=$ordernum WHERE id=".$id;		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;		
	}		
}
