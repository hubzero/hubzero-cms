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
 * Table class for question votes
 */
class AnswersTableQuestionsLog extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id      = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $qid     = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var unknown
	 */
	var $expires = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $voter   = NULL;

	/**
	 * varchar(15)
	 * 
	 * @var string
	 */
	var $ip      = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__answers_questions_log', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->qid = intval($this->qid);
		if (!$this->qid) 
		{
			$this->setError(JText::_('Missing question ID'));
			return false;
		}

		$this->voter = intval($this->voter);
		if (!$this->voter) 
		{
			$this->voter = JFactory::getUser()->get('id');
		}

		if (!$this->expires)
		{
			$this->expires = date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)); // in a week
		}

		ximport('Hubzero_Environment');
		if (!Hubzero_Environment::validIp($this->ip))
		{
			$this->setError(JText::_('Invalid IP address'));
			return false;
		}

		return true;
	}

	/**
	 * Check if a user has voted
	 * 
	 * @param      integer $qid   Question ID
	 * @param      string  $ip    IP address
	 * @param      integer $voter Voter user ID
	 * @return     mixed False if error, integer on success
	 */
	public function checkVote($qid=null, $ip=null, $voter=null)
	{
		if ($qid == null) 
		{
			$qid = $this->qid;
		}
		if ($qid == null) 
		{
			return false;
		}

		$now = date('Y-m-d H:i:s', time());

		if ($voter !== null)
		{
			$and = " AND voter=" . $this->_db->Quote($voter);
		}
		else
		{
			$and = " AND ip=" . $this->_db->Quote($ip);
		}

		$query = "SELECT count(*) FROM $this->_tbl WHERE qid=" . $this->_db->Quote($qid) . $and;

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

