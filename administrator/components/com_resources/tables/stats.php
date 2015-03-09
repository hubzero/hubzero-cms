<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Resources\Tables;

include_once(__DIR__ . DS . 'stats' . DS . 'tools.php');
include_once(__DIR__ . DS . 'stats' . DS . 'tools' . DS . 'top.php');
include_once(__DIR__ . DS . 'stats' . DS . 'tools' . DS . 'topvals.php');
include_once(__DIR__ . DS . 'stats' . DS . 'tools' . DS . 'users.php');
include_once(__DIR__ . DS . 'stats' . DS . 'clusters.php');

/**
 * Resources table class for stats
 */
class Stats extends \JTable
{
	/**
	 * Construct
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_stats', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, False if not
	 */
	public function check()
	{
		if (trim($this->resid) == '')
		{
			$this->setError(\JText::_('Your entry must have a resource ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Load data for a resource and given period
	 *
	 * @param   integer  $resid   Resource ID
	 * @param   integer  $period  Time period
	 * @param   string   $dthis   YYYY-MM
	 * @return  boolean  True on success, False on error
	 */
	public function loadStats($resid=NULL, $period=NULL, $dthis=NULL)
	{
		if ($resid == NULL)
		{
			$resid = $this->resid;
		}
		if ($resid == NULL)
		{
			return false;
		}

		$sql = "SELECT *
				FROM $this->_tbl
				WHERE `period`=" . $this->_db->Quote($period) . " AND `resid`=" . $this->_db->Quote($resid);
		if ($dthis)
		{
			$sql .= " AND `datetime`=" . $this->_db->quote($dthis . "-01 00:00:00");
		}
		else
		{
			$sql .= " ORDER BY `datetime` DESC LIMIT 1";
		}

		$this->_db->setQuery($sql);

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}
}
