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
 * Table class for knowledge base votes
 */
class KbTableVote extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__faq_helpful_log', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->object_id = intval($this->object_id);
		if (!$this->object_id)
		{
			$this->setError(JText::_('COM_KB_ERROR_MISSING_ARTICLE_ID'));
			return false;
		}

		$this->type = strtolower(trim($this->type));
		if (!in_array($this->type, array('entry', 'comment')))
		{
			$this->setError(JText::_('COM_KB_ERROR_UNKNOWN_TYPE'));
			return false;
		}

		return true;
	}

	/**
	 * Get the vote for a specific object/type combination and user
	 *
	 * @param   integer  $object_id  Object ID
	 * @param   integer  $user_id    User ID
	 * @param   string   $ip         IP Address
	 * @param   string   $type       Object type (article, comment)
	 * @return  string
	 */
	public function getVote($object_id=NULL, $user_id=NULL, $ip=NULL, $type=NULL)
	{
		$object_id = $object_id ?: $this->object_id;
		$user_id   = $user_id   ?: $this->user_id;
		$ip        = $ip        ?: $this->ip;
		$type      = $type      ?: $this->type;

		$this->_db->setQuery(
			"SELECT vote FROM `$this->_tbl` 
			WHERE object_id=" . $this->_db->Quote($object_id) . " 
			AND (user_id=" . $this->_db->Quote($user_id) . " OR ip=" . $this->_db->Quote($ip) . ") 
			AND type=" . $this->_db->Quote($type)
		);
		return $this->_db->loadResult();
	}

	/**
	 * Delete a record for a specific object/user combination
	 *
	 * @param      integer $object_id Object ID
	 * @param      integer $user_id   User ID
	 * @return     boolean True upon success
	 */
	public function deleteVote($object_id=NULL, $user_id=NULL, $ip=NULL, $type=NULL)
	{
		$object_id = $object_id ?: $this->object_id;
		$user_id   = $user_id   ?: $this->user_id;
		$ip        = $ip        ?: $this->ip;
		$type      = $type      ?: $this->type;

		$sql  = "DELETE FROM $this->_tbl WHERE object_id=" . $this->_db->Quote($object_id) . " AND type=" . $this->_db->Quote($type);
		if ($user_id || $ip)
		{
			$sql .= " AND (user_id=" . $this->_db->Quote($user_id) . " OR ip=" . $this->_db->Quote($ip) . ")";
		}

		$this->_db->setQuery($sql);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}

