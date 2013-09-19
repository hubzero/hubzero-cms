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
 * Table class for votes
 */
class Vote extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id      		= NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $referenceid    = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $voted 			= NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $voter   		= NULL;

	/**
	 * varchar(11)
	 * 
	 * @var string
	 */
	var $helpful     	= NULL;

	/**
	 * varchar(15)
	 * 
	 * @var string
	 */
	var $ip      		= NULL;

	/**
	 * varchar(50)
	 * 
	 * @var string
	 */
	var $category     	= NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__vote_log', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->referenceid = intval($this->referenceid);
		if (!$this->referenceid) 
		{
			$this->setError(JText::_('Missing reference ID'));
			return false;
		}

		$this->category = trim($this->category);
		if (!$this->category) 
		{
			$this->setError(JText::_('Missing category'));
			return false;
		}

		if (!$this->id)
		{
			$this->voted = ($this->voted) ? $this->voted : date('Y-m-d H:i:s', time());  // use gmdate() ?
			$this->voter = ($this->voter) ? $this->voter : JFactory::getUser()->get('id');
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
	 * Check if a user has voted on an item
	 * 
	 * @param      integer $refid    Reference ID
	 * @param      string  $category Reference type
	 * @param      integer $voter    User ID
	 * @return     mixed False on error, integer on success
	 */
	public function checkVote($refid=null, $category=null, $voter=null)
	{
		if ($refid == null) 
		{
			$refid = $this->referenceid;
		}
		if ($refid == null) 
		{
			return false;
		}
		if ($category == null) 
		{
			$category = $this->category;
		}
		if ($category == null) 
		{
			return false;
		}

		$now = date('Y-m-d H:i:s', time());

		$query = "SELECT count(*) FROM $this->_tbl WHERE referenceid=" . $this->_db->Quote($refid) . " AND category = " . $this->_db->Quote($category) . " AND voter=" . $this->_db->Quote($voter);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getResults($filters=array())
	{
		$query = "SELECT c.* 
				FROM $this->_tbl AS c 
				WHERE c.referenceid=" . $this->_db->Quote($filters['id']) . " AND category=" . $this->_db->Quote($filters['category']) . " ORDER BY c.voted DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

