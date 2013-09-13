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
 * Table class for knowledge base article vote
 */
class KbHelpful extends JTable
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
	var $object_id     = NULL;

	/**
	 * varchar(15)
	 * 
	 * @var string
	 */
	var $ip      = NULL;

	/**
	 * varchar(10)
	 * 
	 * @var string
	 */
	var $vote = NULL;

	/**
	 * int(11)
	 * 
	 * @var string
	 */
	var $user_id = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $type = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__faq_helpful_log', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->fid) == '') 
		{
			$this->setError(JText::_('KB_ERROR_MISSING_ARTICLE_ID'));
			return false;
		}
		return true;
	}

	/**
	 * Get the positive vote count for an article
	 * 
	 * @param      integer $fid Article ID
	 * @param      string  $ip  IP address
	 * @return     integer
	 */
	public function getHelpful($fid=NULL, $ip=NULL)
	{
		if ($fid == NULL) 
		{
			$fid = $this->fid;
		}
		if ($ip == NULL) 
		{
			$ip = $this->ip;
		}
		$this->_db->setQuery("SELECT vote FROM $this->_tbl WHERE object_id =" . $this->_db->Quote($fid) . " AND ip=" . $this->_db->Quote($ip));
		return $this->_db->loadResult();
	}

	/**
	 * Delete vote records for an article
	 * 
	 * @param      string $fid Article ID
	 * @return     boolean True upon success, False if errors
	 */
	public function deleteHelpful($fid=NULL)
	{
		if ($fid == NULL) 
		{
			$fid = $this->fid;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE object_id=" . $this->_db->Quote($fid));
		if ($this->_db->query()) 
		{
			return true;
		} 
		else 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}
}

