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
 * Short description for 'XPollData'
 * 
 * Long description (if any) ...
 */
class XPollData extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id     = NULL; // @var int(11) Primary key


	/**
	 * Description for 'pollid'
	 * 
	 * @var string
	 */
	var $pollid = NULL; // @var int(11)


	/**
	 * Description for 'text'
	 * 
	 * @var unknown
	 */
	var $text   = NULL; // @var text


	/**
	 * Description for 'hits'
	 * 
	 * @var unknown
	 */
	var $hits   = NULL; // @var int(11)

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
		parent::__construct( '#__xpoll_data', 'id', $db );
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
		if ($this->pollid == '') {
			$this->setError( JText::_('XPOLL_ERROR_MISSING_POLL_ID') );
			return false;
		}

		// Sanitise some data
		if (!get_magic_quotes_gpc()) {
			$row->text = addslashes( $row->text );
		}

		return true;
	}

	/**
	 * Short description for 'getPollData'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $poll_id Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getPollData( $poll_id=NULL )
	{
		if ($poll_id == NULL) {
			$poll_id = $this->pollid;
		}
		$query = "SELECT a.id, a.text, count( DISTINCT b.id ) AS hits, count( DISTINCT b.id )/COUNT( DISTINCT a.id )*100.0 AS percent"
			. "\n FROM #__xpoll_data AS a"
			. "\n LEFT JOIN #__xpoll_date AS b ON b.vote_id = a.id"
			. "\n WHERE a.pollid = $poll_id"
			. "\n AND a.text != ''"
			. "\n GROUP BY a.id"
			. "\n ORDER BY a.id"
			;
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getPollOptions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $poll_id Parameter description (if any) ...
	 * @param      boolean $blanks Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getPollOptions( $poll_id=NULL, $blanks=false )
	{
		if ($poll_id == NULL) {
			$poll_id = $this->pollid;
		}
		$query = "SELECT id, text FROM $this->_tbl"
				. " WHERE pollid='$poll_id'";
		if (!$blanks) {
			$query .= " AND text <> ''";
		}
		$query .= " ORDER BY id";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

