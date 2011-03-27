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

