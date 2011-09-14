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
 * Short description for 'XPollPoll'
 * 
 * Long description (if any) ...
 */
class XPollPoll extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id               = NULL; // @var int Primary key


	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title            = NULL; // @var string


	/**
	 * Description for 'voters'
	 * 
	 * @var unknown
	 */
	var $voters           = NULL; // @var int(9)


	/**
	 * Description for 'checked_out'
	 * 
	 * @var unknown
	 */
	var $checked_out      = NULL; // @var int(11)


	/**
	 * Description for 'checked_out_time'
	 * 
	 * @var unknown
	 */
	var $checked_out_time = NULL; // @var datetime(0000-00-00 00:00:00)


	/**
	 * Description for 'published'
	 * 
	 * @var unknown
	 */
	var $published        = NULL; // @var tinyint(1)


	/**
	 * Description for 'access'
	 * 
	 * @var unknown
	 */
	var $access	          = NULL; // @var int(11)


	/**
	 * Description for 'lag'
	 * 
	 * @var integer
	 */
	var $lag              = NULL; // @var int(11)


	/**
	 * Description for 'open'
	 * 
	 * @var unknown
	 */
	var $open             = NULL; // @var tinyint(1)

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
		parent::__construct( '#__xpolls', 'id', $db );
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
		// Check for valid name
		if (trim( $this->title ) == '') {
			$this->setError( JText::_('XPOLL_ERROR_MUST_HAVE_A_TITLE') );
			return false;
		}

		// Check for valid lag
		$this->lag = intval( $this->lag );
		if ($this->lag == 0) {
			$this->setError( JText::_('XPOLL_ERROR_MUST_HAVE_A_NON-ZERO_LAG_TIME') );
			return false;
		}

		// Check for existing title
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE title='$this->title'");
		$xid = intval( $this->_db->loadResult() );
		if ($xid && $xid != intval( $this->id )) {
			$this->setError( JText::_('XPOLL_ERROR_TITLE_EXIST') );
			return false;
		}

		// Sanitise some data
		if (!get_magic_quotes_gpc()) {
			$row->title = addslashes( $row->title );
		}

		return true;
	}

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function delete( $oid=NULL )
	{
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}

		if (JTable::delete( $oid )) {
			$this->_db->setQuery( "DELETE FROM #__xpoll_data WHERE pollid='".$this->$k."'" );
			if (!$this->_db->query()) {
				$this->setError( $this->_db->getErrorMsg() . "\n" );
			}

			$this->_db->setQuery( "DELETE FROM #__xpoll_date WHERE poll_id='".$this->$k."'" );
			if (!$this->_db->query()) {
				$this->setError( $this->_db->getErrorMsg() . "\n" );
			}

			$this->_db->setQuery( "DELETE from #__xpoll_menu where pollid='".$this->$k."'" );
			if (!$this->_db->query()) {
				$this->setError( $this->_db->getErrorMsg() . "\n" );
			}

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Short description for 'getAllPolls'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	public function getAllPolls()
	{
		$query = "SELECT id, title FROM $this->_tbl WHERE published=1 ORDER BY id";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getLatestPoll'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function getLatestPoll()
	{
		$Itemid = 1;

		$query = "SELECT p.id, p.title"
				."\n FROM #__xpoll_menu AS pm, $this->_tbl AS p"
				."\n WHERE (pm.menuid='$Itemid' OR pm.menuid='0') AND p.id=pm.pollid"
				."\n AND p.published=1 AND p.open=1 ORDER BY id DESC LIMIT 1";

		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'increaseVoteCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $poll_id Parameter description (if any) ...
	 * @return     void
	 */
	public function increaseVoteCount( $poll_id=NULL )
	{
		if ($poll_id == NULL) {
			$poll_id = $this->id;
		}

		$this->_db->setQuery( "UPDATE $this->_tbl SET voters=voters + 1 WHERE id='$poll_id'" );
		if (!$this->_db->query()) {
			echo $this->_db->getErrorMsg();
			exit;
		}
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery( $filters=array() )
	{
		$query = " FROM $this->_tbl AS m";

		return $query;
	}

	/**
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount( $filters )
	{
		$query  = "SELECT COUNT(m.id)";
		$query .= $this->buildquery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecords( $filters )
	{
		$query = "SELECT m.*, u.name AS editor, COUNT(d.id) AS numoptions";
		$query .= $this->buildquery( $filters );
		$query .= " LEFT JOIN #__users AS u ON u.id = m.checked_out";
		$query .= " LEFT JOIN #__xpoll_data AS d ON d.pollid = m.id AND d.text <> ''";
		$query .= " GROUP BY m.id";
		$query .= " LIMIT ".$filters['start'].",".$filters['limit'];

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

