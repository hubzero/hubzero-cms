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


class JobType extends JTable
{
	var $id       = NULL;  // @var int(11) Primary key
	var $category = NULL;  // @var varchar(150)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_types', 'id', $db );
	}
	
	//-----------
	
	public function getTypes($sortby = 'id', $sortdir = 'ASC')
	{
		$types = array();
		
		$query  = "SELECT id, category ";
		$query .= "FROM #__jobs_types ORDER BY $sortby $sortdir ";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if ($result) {
			foreach ($result as $r) 
			{
				$types[$r->id] = $r->category;
			}
		}
		
		return $types;		
	}
	
	//-----------
	
	public function getType($id = NULL, $default = 'unspecified')
	{
		if ($id === NULL) {
			 return false;
		}
		if ($id == 0 ) {
			return $default;
		}
		
		$query  = "SELECT category ";
		$query .= "FROM #__jobs_types WHERE id='".$id."'  ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();		
	}		
}
