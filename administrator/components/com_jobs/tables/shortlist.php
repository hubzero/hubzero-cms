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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for job shortlist
 */
class Shortlist extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $emp		= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $seeker		= NULL;

	/**
	 * varchar (job / resume)
	 *
	 * @var string
	 */
	var $category	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $jobid		= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $added		= NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_shortlist', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (intval($this->emp) == 0)
		{
			$this->setError(JText::_('ERROR_MISSING_EMPLOYER_ID'));
			return false;
		}

		if (trim($this->seeker) == 0)
		{
			$this->setError(JText::_('ERROR_MISSING_JOB_SEEKER_ID'));
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      integer $emp      Employer ID
	 * @param      integer $seeker   Seeker ID
	 * @param      string  $category Category
	 * @return     boolean True upon success
	 */
	public function loadEntry($emp, $seeker, $category = 'resume')
	{
		if ($emp === NULL or $seeker === NULL)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE emp=" . $this->_db->Quote($emp) . " AND seeker=" . $this->_db->Quote($seeker) . " AND category=" . $this->_db->Quote($category) . " LIMIT 1");

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}
}

