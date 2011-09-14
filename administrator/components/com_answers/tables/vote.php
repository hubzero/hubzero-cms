<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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

class Vote extends JTable
{
	var $id      		= NULL;  // @var int(11) Primary key
	var $referenceid    = NULL;  // @var int(11)
	var $voted 			= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $voter   		= NULL;  // @var int(11)
	var $helpful     	= NULL;  // @var varchar(11)
	var $ip      		= NULL;  // @var varchar(15)
	var $category     	= NULL;  // @var varchar(50)

	//-----------

	public function __construct( &$db )
	{
		parent::__construct( '#__vote_log', 'id', $db );
	}

	public function check()
	{
		if (trim( $this->referenceid ) == '') {
			$this->setError( JText::_('Missing reference ID') );
			return false;
		}
		return true;

		if (trim( $this->category ) == '') {
			$this->setError( JText::_('Missing category') );
			return false;
		}
		return true;
	}

	public function checkVote($refid=null, $category=null, $voter=null)
	{
		if ($refid == null) {
			$refid = $this->referenceid;
		}
		if ($refid == null) {
			return false;
		}
		if ($category == null) {
			$category = $this->category;
		}
		if ($category == null) {
			return false;
		}

		$now = date( 'Y-m-d H:i:s', time() );

		$query = "SELECT count(*) FROM $this->_tbl WHERE referenceid='".$refid."' AND category = '".$category."' AND voter='".$voter."'";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	public function getResults( $filters=array() )
	{
		$query = "SELECT c.* 
				FROM $this->_tbl AS c 
				WHERE c.referenceid=".$filters['id']." AND category='".$filters['category']."' ORDER BY c.voted DESC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

