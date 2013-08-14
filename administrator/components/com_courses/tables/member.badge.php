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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Courses member badges table
 */
class CoursesTableMemberBadge extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $member_id = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $earned = NULL;

	/**
	 * datetime
	 * 
	 * @var string
	 */
	var $earned_on = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $claim_url = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $action = NULL;

	/**
	 * datetime
	 * 
	 * @var string
	 */
	var $action_on = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_member_badges', 'id', $db);
	}

	/**
	 * Load by member id
	 * 
	 * @param      int $id - member id
	 * @return     void
	 */
	public function loadByMemberId($id)
	{
		$query =   "SELECT *
					FROM {$this->_tbl}
					WHERE member_id = " . $this->_db->Quote($id);

		$this->_db->setQuery($query);

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