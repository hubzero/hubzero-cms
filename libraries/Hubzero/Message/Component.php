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


class Hubzero_Message_Component extends JTable
{
	var $id        = NULL;  // @var int(11) Primary key
	var $component = NULL;  // @var varchar(50)
	var $action    = NULL;  // @var varchar(100)
	var $title     = NULL;  // @var varchar(255)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage_component', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->component ) == '') {
			$this->setError( JText::_('Please provide a component.') );
			return false;
		}
		if (trim( $this->action ) == '') {
			$this->setError( JText::_('Please provide an action.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getRecords() 
	{
		$query  = "SELECT x.*, c.name 
					FROM $this->_tbl AS x, #__components AS c
					WHERE x.component=c.option AND c.parent=0
					ORDER BY x.component, x.action DESC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getComponents() 
	{
		$query  = "SELECT DISTINCT x.component 
					FROM $this->_tbl AS x
					ORDER BY x.component ASC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}
}
