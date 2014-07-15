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
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for event pages
 */
class EventsCalendar extends JTable
{

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $id             = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $scope          = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $scope_id       = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $title          = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $url            = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $color          = NULL;

	/**
	 * int(11)
	 *
	 * @var string
	 */
	var $published      = NULL;

	/**
	 * tinyint
	 *
	 * @var string
	 */
	var $readonly       = NULL;

	/**
	 * datetime
	 *
	 * @var string
	 */
	var $last_fetched   = NULL;

	/**
	 * datetime
	 *
	 * @var string
	 */
	var $last_fetched_attempt = NULL;

	/**
	 * datetime
	 *
	 * @var string
	 */
	var $failed_attempts     = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__events_calendars', 'id', $db);
	}

	/**
	 * Check Method for saving
	 *
	 * @return    bool
	 */
	public function check()
	{
		if (!isset($this->title) || $this->title == '')
		{
			$this->setError(JText::_('COM_EVENTS_CALENDAR_MUST_HAVE_TITLE'));
			return false;
		}
		return true;
	}

	/**
	 * Find all calendars matching filters
	 *
	 * @param      array   $filters
	 * @return     array
	 */
	public function find( $filters = array() )
	{
		$sql  = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery( $filters );

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get count of calendars matching filters
	 *
	 * @param      array   $filters
	 * @return     int
	 */
	public function count( $filters = array() )
	{
		$sql  = "SELECT COUNT(*) FROM {$this->_tbl}";
		$sql .= $this->_buildQuery( $filters );

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Build query string for getting list or count of calendars
	 *
	 * @param      array   $filters
	 * @return     string
	 */
	private function _buildQuery( $filters = array() )
	{
		// var to hold conditions
		$where = array();
		$sql   = '';

		// scope
		if (isset($filters['scope']))
		{
			$where[] = "scope=" . $this->_db->quote( $filters['scope'] );
		}

		// scope_id
		if (isset($filters['scope_id']))
		{
			$where[] = "scope_id=" . $this->_db->quote( $filters['scope_id'] );
		}

		// readonly
		if (isset($filters['readonly']))
		{
			$where[] = "readonly=" . $this->_db->quote( $filters['readonly'] );
		}

		// published
		if (isset($filters['published']) && is_array($filters['published']))
		{
			$where[] = "published IN (" . implode(',', $filters['published']) . ")";
		}

		// if we have and conditions
		if (count($where) > 0)
		{
			$sql = " WHERE " . implode(" AND ", $where);
		}

		return $sql;
	}
}