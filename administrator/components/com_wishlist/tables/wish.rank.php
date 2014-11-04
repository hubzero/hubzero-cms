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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for wish ranking
 */
class WishRank extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wishlist_vote', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->wishid) == '')
		{
			$this->setError(JText::_('WISHLIST_ERROR_NO_WISHID'));
			return false;
		}

		return true;
	}

	/**
	 * Get a record and bind to $this
	 *
	 * @param   integer  $userid  User ID
	 * @param   integer  $wishid  Wish ID
	 * @return  boolean  False if error, True on success
	 */
	public function load_vote($userid=NULL, $wishid=NULL)
	{
		$userid = $userid ?: $this->userid;
		$wishid = $wishid ?: $this->wishid;

		if ($userid === NULL or $wishid === NULL)
		{
			$this->setError(JText::_('Missing wish or user ID'));
			return false;
		}

		return parent::load(array(
			'userid' => $userid,
			'wishid' => $wishid
		));
	}

	/**
	 * Get votes on a wish
	 *
	 * @param   integer  $wishid  Wish ID
	 * @return  mixed    False if error, array on success
	 */
	public function get_votes($wishid=NULL)
	{
		$wishid = $wishid ?: $this->wishid;

		if ($wishid === NULL)
		{
			$this->setError(JText::_('Missing wish ID'));
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE wishid=" . $this->_db->Quote($wishid));
		return $this->_db->loadObjectList();
	}

	/**
	 * Remove a vote
	 *
	 * @param   integer  $wishid  Wish ID
	 * @param   integer  $userid  User ID
	 * @return  boolean  False if error, True on success
	 */
	public function remove_vote($wishid=NULL, $userid=NULL)
	{
		$userid = $userid ?: $this->userid;
		$wishid = $wishid ?: $this->wishid;

		if ($wishid === NULL)
		{
			$this->setError(JText::_('Missing wish ID'));
			return false;
		}

		$query = "DELETE FROM $this->_tbl WHERE wishid=" . $this->_db->Quote($wishid);
		if ($oid)
		{
			$query .= " AND userid=" . $this->_db->Quote($oid);
		}

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}

