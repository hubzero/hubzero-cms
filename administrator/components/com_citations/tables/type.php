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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for citation type
 */
class CitationsType extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $type        = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $type_title  = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $type_desc   = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $type_export = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $fields      = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__citations_types', 'id', $db);
	}

	/**
	 * Load citation type(s)
	 * If ID is passed, it loads only one record
	 *
	 * @param      integer $id Type ID
	 * @return     array
	 */
	public function getType($id = '')
	{
		$where = ($id != '') ? "WHERE id=" . $this->_db->Quote($id) : "";

		$sql = "SELECT * FROM {$this->_tbl} {$where} ORDER BY type";
		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList();
	}
}

