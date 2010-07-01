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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class AnswersLog extends JTable 
{
	var $id      = NULL;  // @var int(11) Primary key
	var $rid     = NULL;  // @var int(11)
	var $ip      = NULL;  // @var varchar(15)
	var $helpful = NULL;  // @var varchar(10)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__answers_log', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->rid ) == '') {
			$this->setError( JText::_('Missing response ID') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function checkVote($rid=null, $ip=null) 
	{
		if ($rid == null) {
			$rid = $this->rid;
		}
		if ($rid == null) {
			return false;
		}
		
		$query = "SELECT helpful FROM $this->_tbl WHERE rid='".$rid."' AND ip='".$ip."'";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function deleteLog($rid=null) 
	{
		if ($rid == null) {
			$rid = $this->rid;
		}
		if ($rid == null) {
			return false;
		}
		
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE rid=".$rid );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		
		return true;
	}
}
