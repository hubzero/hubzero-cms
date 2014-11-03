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
 * Table class for resource media tracking
 */
class ResourceMediaTracking extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__media_tracking', 'id', $db);
	}

	/**
	 * Get tracking info for a specific user/resource combination
	 *
	 * @param   string  $user_id      User ID
	 * @param   string  $object_id    Object ID
	 * @param   string  $object_type  Object type
	 * @return  object
	 */
	public function getTrackingInformationForUserAndResource($user_id = '', $object_id = '', $object_type = 'resource')
	{
		// Make sure we have a resource
		if (!$object_id)
		{
			return;
		}

		$sql = "SELECT m.* FROM $this->_tbl AS m WHERE ";

		// If we don't have a user ID use session ID
		if (!$user_id)
		{
			$session = JFactory::getSession();
			$session_id = $session->getId();
			$sql .= "m.session_id=" . $this->_db->quote($session_id);
		}
		else
		{
			$sql .= "m.user_id=" . $this->_db->quote($user_id);
		}

		$sql .= " AND m.object_id=" . $this->_db->quote($object_id) . " AND m.object_type=" . $this->_db->quote($object_type);

		$this->_db->setQuery($sql);
		return $this->_db->loadObject();
	}
}