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
 * Wiki table class for comment
 */
class WikiPageComment extends JTable
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
	var $pageid     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $version    = NULL;

	/**
	 * datetime
	 * 
	 * @var string
	 */
	var $created    = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $ctext      = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $chtml      = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $rating     = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $anonymous  = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $parent     = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $status     = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wiki_comments', 'id', $db);
	}

	/**
	 * Get all replies to a comment
	 * 
	 * @return     array
	 */
	public function getResponses()
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE parent='$this->id' AND status < 2");
		return $this->_db->loadObjectList();
	}

	/**
	 * Mark a comment as abusive
	 * 
	 * @param      integer $oid Entry ID
	 * @return     boolean True on success, False if error
	 */
	public function report($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid) 
		{
			$this->$k = intval($oid);
		}

		$this->_db->setQuery("UPDATE $this->_tbl SET status=1 WHERE $this->_tbl_key = '" . $this->$k . "'");

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

	/**
	 * Get all comments for a page
	 * 
	 * @param      integer $id     Page ID
	 * @param      integer $parent Parent comment ID
	 * @param      string  $ver    Page version
	 * @param      string  $limit  Number of records to return
	 * @return     array
	 */
	public function getComments($id, $parent, $ver='', $limit='')
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE pageid='" . $id . "' AND status < 2 AND parent=" . $parent . " $ver ORDER BY created DESC $limit");
		return $this->_db->loadObjectList();
	}
}

