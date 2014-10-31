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
 * Table class for answer votes
 */
class AnswersTableLog extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__answers_log', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   integer  $response_id  Answer ID
	 * @param   string   $ip           IP address
	 * @return  boolean  True upon success, False if errors
	 */
	public function loadByIp($response_id=null, $ip=null)
	{
		$response_id = $response_id ?: $this->response_id;

		if ($response_id == null)
		{
			return false;
		}

		return parent::load(array(
			'response_id' => (int) $response_id,
			'ip'          => (string) $ip
		));
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->response_id = intval($this->response_id);
		if (!$this->response_id)
		{
			$this->setError(JText::_('Missing response ID'));
			return false;
		}

		$this->helpful = strtolower(trim($this->helpful));
		if (!$this->helpful)
		{
			$this->setError(JText::_('Missing vote'));
			return false;
		}

		if (!in_array($this->helpful, array(1, 'yes', 'like', 'up', -1, 'no', 'dislike', 'down')))
		{
			$this->setError(JText::_('Invalid vote'));
			return false;
		}

		if (!\Hubzero\Utility\Validate::ip($this->ip))
		{
			$this->setError(JText::_('Invalid IP address'));
			return false;
		}

		return true;
	}

	/**
	 * Check if a vote has been registered for an answer/IP
	 *
	 * @param   integer  $response_id  Answer ID
	 * @param   string   $ip           IP address
	 * @return  integer
	 */
	public function checkVote($response_id=null, $ip=null)
	{
		$response_id = $response_id ?: $this->response_id;

		if ($response_id == null)
		{
			return 0;
		}

		$query = "SELECT helpful FROM `$this->_tbl` WHERE response_id=" . $this->_db->Quote($response_id) . " AND ip=" . $this->_db->Quote($ip);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Delete a record by answer ID
	 *
	 * @param   integer  $response_id  Answer ID
	 * @return  boolean  True on success, false if error
	 */
	public function deleteLog($response_id=null)
	{
		$response_id = $response_id ?: $this->response_id;

		if ($response_id == null)
		{
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE response_id=" . $this->_db->Quote($response_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}

