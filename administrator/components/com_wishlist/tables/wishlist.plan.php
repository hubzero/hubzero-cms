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
 * Table class for wishlist plan
 */
class WishlistPlan extends JTable
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
	var $wishid		= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $version	= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $created_by	= NULL;

	/**
	 * int(1)
	 *
	 * @var integer
	 */
	var $minor_edit	= NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $pagetext	= NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $pagehtml	= NULL;

	/**
	 * int(1)
	 *
	 * @var integer
	 */
	var $approved   = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $summary	= NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wishlist_implementation', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->pagetext = rtrim($this->pagetext);
		if (!$this->pagetext)
		{
			$this->setError(JText::_('Please provide a plan'));
			return false;
		}

		$this->version = intval($this->version);

		$this->wishid = intval($this->wishid);
		if (!$this->wishid)
		{
			$this->setError(JText::_('Please provide a wish ID'));
			return false;
		}

		if (!$this->id)
		{
			$juser = JFactory::getUser();
			$this->created = JFactory::getDate()->toSql();
			$this->created_by = $juser->get('id');
		}

		return true;
	}

	/**
	 * Get a record for a wish
	 *
	 * @param      integer $wishid Wish ID
	 * @return     mixed False if error, array on success
	 */
	public function getPlan($wishid)
	{
		if ($wishid == NULL)
		{
			return false;
		}

		$query  = "SELECT *, xp.name AS authorname ";
		$query .= "FROM #__wishlist_implementation AS p ";
		$query .= "LEFT JOIN #__xprofiles AS xp ON xp.uidNumber=p.created_by ";
		$query .= "WHERE p.wishid = " . $this->_db->Quote($wishid) . " ORDER BY p.created DESC LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a record and bind to $this
	 *
	 * @param      integer $keys Record ID
	 * @return     boolean True on success
	 */
	public function load($keys = NULL, $reset = true)
	{
		if ($keys == NULL or !is_numeric($keys))
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE id=" . $this->_db->Quote($keys));
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

	/**
	 * Delete a record based on wish
	 *
	 * @param      integer $wishid Wish ID
	 * @return     boolean False if errors, True on success
	 */
	public function deletePlan($wishid)
	{
		if ($wishid == NULL)
		{
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE wishid=" . $this->_db->Quote($wishid));
		$this->_db->query();
		return true;
	}
}

