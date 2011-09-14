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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------


/**
 * Short description for 'SupportComment'
 * 
 * Long description (if any) ...
 */
class SupportComment extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'ticket'
	 * 
	 * @var unknown
	 */
	var $ticket     = NULL;  // @var int(11)


	/**
	 * Description for 'comment'
	 * 
	 * @var unknown
	 */
	var $comment    = NULL;  // @var text


	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created    = NULL;  // @var datetime


	/**
	 * Description for 'created_by'
	 * 
	 * @var unknown
	 */
	var $created_by = NULL;  // @var var(50)


	/**
	 * Description for 'changelog'
	 * 
	 * @var unknown
	 */
	var $changelog  = NULL;  // @var text


	/**
	 * Description for 'access'
	 * 
	 * @var unknown
	 */
	var $access     = NULL;  // @var int(3)

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
		parent::__construct( '#__support_comments', 'id', $db );
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
		if (trim( $this->comment ) == '' && trim( $this->changelog ) == '') {
			$this->setError( JText::_('SUPPORT_ERROR_BLANK_COMMENT') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'getComments'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @param      string $ticket Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getComments( $authorized, $ticket=NULL )
	{
		if (!$ticket) {
			$ticket = $this->_ticket;
		}
		if ($authorized) {
			$sqladmin = "";
		} else {
			$sqladmin = "AND access=0";
		}
		$sql = "SELECT * FROM $this->_tbl WHERE ticket=".$ticket." $sqladmin ORDER BY created ASC";

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'countComments'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @param      string $ticket Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function countComments( $authorized, $ticket=NULL )
	{
		if (!$ticket) {
			$ticket = $this->_ticket;
		}
		if ($authorized) {
			$sqladmin = "";
		} else {
			$sqladmin = "AND access=0";
		}
		$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE ticket=".$ticket." $sqladmin" );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'newestComment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @param      string $ticket Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function newestComment( $authorized, $ticket=NULL )
	{
		if (!$ticket) {
			$ticket = $this->_ticket;
		}
		if ($authorized) {
			$sqladmin = "";
		} else {
			$sqladmin = "AND access=0";
		}
		$this->_db->setQuery( "SELECT created FROM $this->_tbl WHERE ticket=".$ticket." $sqladmin ORDER BY created DESC LIMIT 1" );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'deleteComments'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $ticket Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteComments( $ticket=NULL )
	{
		if ($ticket === NULL) {
			$ticket = $this->ticket;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE ticket=".$ticket );
		if (!$this->_db->query()) {
			$this->setError( $database->getErrorMsg() );
			return false;
		}
	}
}

