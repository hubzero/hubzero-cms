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


class Hubzero_Comment extends JTable 
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
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__comments', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->comment ) == '' or trim( $this->comment ) == JText::_('Enter your comments...')) {
			$this->setError( JText::_('Please provide a comment') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getResults( $filters=array(), $get_profile_name = 0, $get_abuse_reports = 0 ) 
	{
		$query = "SELECT c.* ";
		$query.= $get_profile_name ? ", xp.name AS authorname " : "";
		$query.= $get_abuse_reports ? ", (SELECT count(*) FROM #__abuse_reports AS RR WHERE RR.referenceid=c.id AND RR.state=0 AND RR.category='wishcomment') AS reports " : "";
		$query.= "FROM $this->_tbl AS c ";
		$query.= $get_profile_name ? "JOIN #__xprofiles AS xp ON xp.uidNumber=c.added_by " : "";
		$query.= "WHERE c.referenceid=".$filters['id']." AND c.category='".$filters['category']."' AND c.state!=2 ";
		$query.= "ORDER BY c.added ASC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
