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
 * Courses grade book table
 */
class CoursesTableGradePolicies extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $alias = NULL;

	/**
	 * mediumtext
	 * 
	 * @var string
	 */
	var $description = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $type = NULL;

	/**
	 * mediumtext
	 * 
	 * @var string
	 */
	var $grade_criteria = NULL;

	/**
	 * mediumtext
	 * 
	 * @var string
	 */
	var $score_criteria = NULL;

	/**
	 * mediumtext
	 * 
	 * @var string
	 */
	var $badge_criteria = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_grade_policies', 'id', $db);
	}

	/**
	 * Loads a row from the database and binds the fields to the object properties
	 *
	 * @access	public
	 * @param	mixed	Optional primary key.  If not specifed, the value of current key is used
	 * @return	boolean	True if successful
	 */
	public function load($oid=null)
	{
		$k = $this->_tbl_key;

		if ($oid !== null)
		{
			$this->$k = $oid;
		}

		$oid = $this->$k;

		if ($oid === null)
		{
			return false;
		}

		$this->reset();

		$query  = 'SELECT *';
		$query .= ' FROM ' . $this->_tbl;

		if (is_numeric($oid))
		{
			$query .= ' WHERE ' . $this->_tbl_key . ' = ' . $this->_db->Quote($oid);
		}
		elseif (is_string($oid))
		{
			$query .= ' WHERE `alias` = ' . $this->_db->Quote($oid);
		}
		else
		{
			return false;
		}

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