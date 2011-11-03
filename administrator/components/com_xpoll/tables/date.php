<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'XPollDate'
 * 
 * Long description (if any) ...
 */
class XPollDate extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id       = NULL; // @var int(11) Primary key

	/**
	 * Description for 'date'
	 * 
	 * @var unknown
	 */
	var $date     = NULL; // @var datetime(0000-00-00 00:00:00)

	/**
	 * Description for 'vote_id'
	 * 
	 * @var string
	 */
	var $vote_id  = NULL; // @var int(11)

	/**
	 * Description for 'poll_id'
	 * 
	 * @var string
	 */
	var $poll_id  = NULL; // @var int(11)

	/**
	 * Description for 'voter_ip'
	 * 
	 * @var unknown
	 */
	var $voter_ip = NULL; // @var varchar(50)

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__xpoll_date', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		// Check for pollid
		if ($this->vote_id == '') {
			$this->setError( JText::_('XPOLL_ERROR_MISSING_VOTE_ID') );
			return false;
		}

		// Check for pollid
		if ($this->poll_id == '') {
			$this->setError( JText::_('XPOLL_ERROR_MISSING_POLL_ID') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'getMinMaxDates'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $poll_id Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getMinMaxDates( $poll_id=NULL )
	{
		if ($poll_id == NULL) {
			$poll_id = $this->poll_id;
		}
		$query = "SELECT MIN(date) AS mindate, MAX(date) AS maxdate"
				." FROM $this->_tbl"
				." WHERE poll_id='$poll_id'";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'deleteEntries'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $poll_id Parameter description (if any) ...
	 * @return     void
	 */
	public function deleteEntries( $poll_id=NULL )
	{
		if ($poll_id == NULL) {
			$poll_id = $this->poll_id;
		}

		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE poll_id='$poll_id'" );
		if (!$this->_db->query()) {
			echo $this->_db->getErrorMsg();
			exit();
		}
	}
}

