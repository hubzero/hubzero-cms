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
 * Password blacklist class
 */
class MembersPasswordBlacklist extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__password_blacklist', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return return true
	 */
	public function check()
	{
		// Make sure they gave a word
		$this->word = trim($this->word);
		if ($this->word == '')
		{
			$this->setError(JText::_('PASSWORD_BLACKLIST_MUST_HAVE_WORD'));
			return false;
		}

		return true;
	}

	/**
	 * Build query method
	 *
	 * @param   array   $filters
	 * @return  string  Database query
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS pb";

		return $query;
	}

	/**
	 * Get a count of the number of blacklisted passwords (used mainly for pagination)
	 *
	 * @param   array    $filters
	 * @return  integer  Return count of rows
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(pb.id)";
		$query .= $this->buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get the an object list of blacklisted passwords
	 *
	 * @param   array  $filters  Start and limit, needed for pagination
	 * @return  array  Return password rule records
	 */
	public function getRecords($filters=array())
	{
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'word';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
		if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
		{
			$filters['sort_Dir'] = 'ASC';
		}

		$query  = "SELECT pb.*";
		$query .= $this->buildquery($filters);
		$query .= " ORDER BY `" . $filters['sort'] . "` " . $filters['sort_Dir'];
		$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}