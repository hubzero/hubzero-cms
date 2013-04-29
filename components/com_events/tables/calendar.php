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
	 * Get Calendar Objects
	 *
	 * @param      object    $group     Group Object
	 * @param      int       $id        Calendar ID
	 * @return     array
	 */
	public function getCalendars( $group, $id = null )
	{
		$sql = "SELECT * FROM {$this->_tbl} 
				WHERE scope=" . $this->_db->quote('group') . "
				AND scope_id=" . $this->_db->quote( $group->get('gidNumber') );
		
		if (isset($id) && $id != '' && $id != null)
		{
			$sql .= " AND id=" . $this->_db->quote( $id );
		}
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	
	/**
	 * Check Method for saving
	 */
	public function check()
	{
		if (!isset($this->title) || $this->title == '')
		{
			$this->setError(JText::_('Calendar must have a title.'));
			return false;
		}
		return true;
	}
}