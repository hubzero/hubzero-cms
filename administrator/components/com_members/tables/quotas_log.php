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
 * Members quota log db table class
 */
class MembersQuotasLog extends JTable
{
	/**
	 * ID - primary key
	 *
	 * @var int(11)
	 */
	var $id = null;

	/**
	 * Object type (user quota or quota class)
	 *
	 * @var varchar(255)
	 */
	var $object_type = null;

	/**
	 * Object ID, Id of object being changed
	 *
	 * @var int(11)
	 */
	var $object_id = null;

	/**
	 * Name (eithe class name or user name, depending on object type)
	 *
	 * @var varchar(255)
	 */
	var $name = null;

	/**
	 * Action type (add/modify/delete)
	 *
	 * @var varchar(255)
	 */
	var $action = null;

	/**
	 * Actor id, who's performing the change
	 *
	 * @var int(11)
	 */
	var $actor_id = null;

	/**
	 * Soft blocks limit
	 *
	 * @var int(11)
	 */
	var $soft_blocks = null;

	/**
	 * Hard blocks limit
	 *
	 * @var int(11)
	 */
	var $hard_blocks = null;

	/**
	 * Soft files limit
	 *
	 * @var int(11)
	 */
	var $soft_files = null;

	/**
	 * Hard files limit
	 *
	 * @var int(11)
	 */
	var $hard_files = null;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__users_quotas_log', 'id', $db );
	}
}