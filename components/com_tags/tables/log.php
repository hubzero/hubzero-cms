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
 * Tags class for log table
 * This is used to track events on tags (adding, editing, merging, deleting)
 */
class TagsLog extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id       = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $tag_id    = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $timestamp = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $user_id   = NULL;

	/**
	 * varchar(50)
	 * 
	 * @var string
	 */
	var $action    = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $comments  = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $actorid   = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tags_log', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->tag_id = intval($this->tag_id);
		if (!$this->tag_id) 
		{
			$this->setError(JText::_('TAGS_LOGS_MUST_HAVE_TAG_ID'));
			return false;
		}

		$juser =& JFactory::getUser();
		if (!$this->id) 
		{
			$this->timestamp = date('Y-m-d H:i:s', time());  // use gmdate() ?
			$this->actorid = $juser->get('id');
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id) 
		{
			$this->user_id = $juser->get('id');
		}

		return true;
	}

	/**
	 * Get all records for a tag
	 * 
	 * @param      integer $tag_id Tag ID
	 * @return     array
	 */
	public function log($tag_id=null, $action=null, $comments=null)
	{
		if (!$tag_id) 
		{
			$tag_id = $this->tag_id;
		}
		if (!$tag_id || !$action) 
		{
			$this->setError(JText::_('Missing argument.'));
			return false;
		}

		$this->tag_id = $tag_id;
		$this->action = $action;
		if ($comments)
		{
			$this->comments = $comments;
		}

		if (!$this->check())
		{
			return false;
		}
		if (!$this->store())
		{
			return false;
		}
		return true;
	}

	/**
	 * Get all records for a tag
	 * 
	 * @param      integer $tag_id Tag ID
	 * @return     array
	 */
	public function getLogs($tag_id=null)
	{
		if (!$tag_id) 
		{
			$tag_id = $this->tag_id;
		}
		if (!$tag_id) 
		{
			return null;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE `tag_id`=$tag_id ORDER BY `timestamp` DESC, id DESC");
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete logs for a tag
	 * 
	 * @param      integer $tag_id Tag ID
	 * @return     boolean True on success
	 */
	public function deleteLogs($tag_id=null)
	{
		if (!$tag_id) 
		{
			$tag_id = $this->tag_id;
		}
		if (!$tag_id) 
		{
			return null;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE `tag_id`=" . $tag_id);
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Delete logs for a tag
	 * 
	 * @param      object $log Database row (TagsLog)
	 * @return     string
	 */
	public function formatLog($log=null)
	{
		if (!$log) 
		{
			return '';
		}

		/*$data = $this->escape(stripslashes($log->action)); ?> on <?php echo $log->timestamp; ?> by ' . $this->escape(stripslashes($user->get('name')));
		switch ($log->action)
		{
			case 'substitute_created':
			break;
		}*/
	}
}

