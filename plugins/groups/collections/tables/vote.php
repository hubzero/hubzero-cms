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
 * Table class for forum posts
 */
class BulletinboardVote extends JTable
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
	var $user_id  = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer 
	 */
	var $bulletin_id = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string  
	 */
	var $voted      = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__bulletinboard_votes', 'id', $db);
	}

	/**
	 * Load a record by its bulletin and user IDs
	 * 
	 * @param      integer $bulletin_id Bulletin ID
	 * @param      integer $user_id     User ID
	 * @return     boolean True upon success, False if errors
	 */
	public function loadByBulletin($bulletin_id=null, $user_id=null)
	{
		if (!$bulletin_id || !$user_id) 
		{
			return false;
		}
		$bulletin_id = intval($bulletin_id);
		$user_id = intval($user_id);

		$query = "SELECT * FROM $this->_tbl WHERE bulletin_id='$bulletin_id' AND user_id='$user_id'";

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

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->bulletin_id = intval($this->bulletin_id);
		if (!$this->bulletin_id) 
		{
			$this->setError(JText::_('Please provide a bulletin ID'));
			return false;
		}

		$juser =& JFactory::getUser();
		if (!$this->id) 
		{
			$this->voted = date('Y-m-d H:i:s', time());  // use gmdate() ?
			$this->user_id = $juser->get('id');
		}

		return true;
	}
}
