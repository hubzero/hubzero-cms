<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Publications\Tables;

/**
 * Table class for publication curation history
 */
class CurationHistory extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_curation_history', 'id', $db );
	}

	/**
	 * Get history records
	 *
	 * @param      integer 	$vid Publication Version ID
	 * @return     mixed False if error, Object on success
	 */
	public function getRecords( $vid = NULL, $filters = array() )
	{
		if (!intval($vid))
		{
			return false;
		}

		$sortby  = isset($filters['sortby']) && $filters['sortby'] ? $filters['sortby'] : 'created';
		$sortdir = isset($filters['sortdir']) && strtoupper($filters['sortdir']) == 'DESC'  ? 'DESC' : 'ASC';

		$query = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($vid);

		if (isset($filters['curator']) && $filters['curator'] == 1)
		{
			$query .= " AND curator=1";
		}

		$query .= " ORDER BY " . $sortby . " " . $sortdir;
		$this->_db->setQuery( $query );

		return $this->_db->loadObjectList();
	}

	/**
	 * Get last history record
	 *
	 * @param      integer 	$vid Publication Version ID
	 * @return     mixed False if error, Object on success
	 */
	public function getLastRecord( $vid = NULL, $curator = 0 )
	{
		if (!intval($vid))
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($vid);
		$query.= ($curator == 1) ? " AND curator=1" : " AND curator=0";

		$query .= " ORDER BY id DESC LIMIT 1";
		$this->_db->setQuery( $query );

		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : NULL;
	}
}
