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
 * Table class for publication stats
 */
class Stats extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_stats', 'id', $db );
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim( $this->publication_id ) == '')
		{
			$this->setError( Lang::txt('Your entry must have a publication ID.') );
			return false;
		}
		return true;
	}

	/**
	 * Load record
	 *
	 * @param      integer $publication_id      Pub ID
	 * @param      integer $period 				Period
	 * @param      integer $dthis
	 * @return     mixed False if error, Object on success
	 */
	public function loadStats( $publication_id = NULL, $period = NULL, $dthis = NULL )
	{
		if ($publication_id == NULL)
		{
			$publication_id = $this->publication_id;
		}
		if ($publication_id == NULL)
		{
			return false;
		}

		$sql = "SELECT *
				FROM $this->_tbl
				WHERE period =" . $this->_db->quote($period) . "
				AND publication_id =" . $this->_db->quote($publication_id);
		$sql.= $dthis ? " AND datetime='" . $dthis . "-00 00:00:00'" : '';
		$sql.= " ORDER BY datetime DESC LIMIT 1";

		$this->_db->setQuery( $sql );

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
