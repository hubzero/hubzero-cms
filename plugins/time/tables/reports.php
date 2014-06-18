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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Time - reports database class
 */
Class TimeReports extends JTable
{
	/**
	 * id, primary key
	 *
	 * @var int(11)
	 */
	var $id = null;

	/**
	 * report_type
	 *
	 * @var varchar(255)
	 */
	var $report_type = null;

	/**
	 * user_id
	 *
	 * @var int(11)
	 */
	var $user_id = null;

	/**
	 * time_stamp
	 *
	 * @var datetime
	 */
	var $time_stamp = null;

	/**
	 * Constructor
	 *
	 * @param   unknown &$db Parameter description (if any) ...
	 * @return  void
	 */
	function __construct( &$db )
	{
		parent::__construct('#__time_reports', 'id', $db );
	}

	/**
	 * Build query
	 *
	 * @return $query
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS r";
		$query .= " LEFT JOIN #__time_reports_records_assoc AS assoc ON assoc.report_id = r.id";

		return $query;
	}

	/**
	 * Get list of reports
	 *
	 * @return object list of reports
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT ";
		if(!empty($filters['distinct']))
		{
			$query .= "DISTINCT(assoc.report_id), ";
		}
		$query .= "r.id, r.time_stamp, r.report_type";
		$query .= $this->buildquery($filters);
		if(!empty($filters['user_id']))
		{
			$query .= " WHERE user_id = ".$filters['user_id'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get list of id's associated with a report
	 *
	 * @return array of id's per given report instance
	 */
	public function getRecordIDs($filters=array())
	{
		$query  = "SELECT assoc.record_id";
		$query .= $this->buildquery($filters);
		$query .= " WHERE report_id = ".$filters['report_id'];

		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();
	}
}