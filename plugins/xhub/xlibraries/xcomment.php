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
// Comment database class
//----------------------------------------------------------

class XComment extends JTable 
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $referenceid    = NULL;  // @var int(11)
	var $category		= NULL;  // @var varchar(50)
	var $comment   		= NULL;  // @var text
	var $added    		= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $added_by 		= NULL;  // @var int(11)
	var $state      	= NULL;  // @var int(3)
	var $anonymous      = NULL;  // @var int(3)
	var $email      	= NULL;  // @var int(3)
	var $subject		= NULL;  // @var varchar(150)
	
	function __construct( &$db )
	{
		parent::__construct( '#__comments', 'id', $db );
	}
	
	function check() 
	{
		if (trim( $this->comment ) == '' or trim( $this->comment ) == JText::_('Enter your comments...')) {
			$this->setError( JText::_('Please provide a comment') );
			return false;
		}
		return true;
	}
	
	function getResults( $filters=array() ) 
	{
		$query = "SELECT c.* 
				FROM $this->_tbl AS c 
				WHERE c.referenceid=".$filters['id']." AND category='".$filters['category']."' AND state!=2 ORDER BY c.added ASC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
?>