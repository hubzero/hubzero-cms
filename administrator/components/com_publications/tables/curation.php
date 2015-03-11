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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Publications\Tables;

/**
 * Table class for publication curation flow
 */
class Curation extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_curation', 'id', $db );
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (!$this->publication_id)
		{
			$this->setError( \JText::_('Must have a publication ID.') );
			return false;
		}

		if (!$this->publication_version_id)
		{
			$this->setError( \JText::_('Must have a publication version ID.') );
			return false;
		}

		return true;
	}

	/**
	 * Get curation record
	 *
	 * @param      integer 	$vid Publication Version ID
	 * @return     mixed False if error, Object on success
	 */
	public function getRecords( $vid = NULL )
	{
		if (!intval($vid))
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $this->_db->Quote($vid);
		$query.= " ORDER BY step ASC, element ASC ";
		$this->_db->setQuery( $query );

		return $this->_db->loadObjectList();
	}

	/**
	 * Load record
	 *
	 * @param      integer 	$pid Publication ID
	 * @param      integer 	$vid Publication Version ID
	 * @return     mixed False if error, Object on success
	 */
	public function getRecord( $pid = NULL, $vid = NULL, $block = NULL, $step = 0, $element = NULL )
	{
		if (!$pid || !$vid || !$block || !intval($step))
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid);
		$query.= " AND publication_version_id=" . $this->_db->Quote($vid);
		$query.= " AND block=" . $this->_db->Quote($block);
		$query.= " AND step=" . $this->_db->Quote($step);
		$query.= $element ? " AND element=" . $this->_db->Quote($element) : " AND (element IS NULL OR element=0)";
		$query.= " ORDER BY id DESC LIMIT 1";
		$this->_db->setQuery( $query );
		$results = $this->_db->loadObjectList();
		return $results ? $results[0] : NULL;
	}

	/**
	 * Load record
	 *
	 * @param      integer 	$pid Publication ID
	 * @param      integer 	$vid Publication Version ID
	 * @return     mixed False if error, Object on success
	 */
	public function loadRecord( $pid = NULL, $vid = NULL, $block = NULL, $step = 0, $element = NULL )
	{
		if (!$pid || !$vid || !$block || !intval($step))
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid);
		$query.= " AND publication_version_id=" . $this->_db->Quote($vid);
		$query.= " AND block=" . $this->_db->Quote($block);
		$query.= " AND step=" . $this->_db->Quote($step);
		$query.= $element ? " AND element=" . $this->_db->Quote($element) : " AND (element IS NULL OR element=0)";
		$query.= " ORDER BY id DESC LIMIT 1";
		$this->_db->setQuery( $query );

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind( $result );
		}
		else
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}
