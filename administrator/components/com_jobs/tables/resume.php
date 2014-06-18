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

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for job resumes
 */
class Resume extends JTable
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
	var $uid		= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created	= NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $title		= NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $filename	= NULL;

	/**
	 * tinyint  0 - no, 1 - yes
	 *
	 * @var integer
	 */
	var $main		= NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_resumes', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (intval($this->uid) == 0)
		{
			$this->setError(JText::_('ERROR_MISSING_UID'));
			return false;
		}

		if (trim($this->filename) == '')
		{
			$this->setError(JText::_('ERROR_MISSING_FILENAME'));
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      integer $name Parameter description (if any) ...
	 * @return     boolean True upon success
	 */
	public function loadResume($name=NULL)
	{
		if ($name !== NULL)
		{
			$this->_tbl_key = 'uid';
		}
		$k = $this->_tbl_key;
		if ($name !== NULL)
		{
			$this->$k = $name;
		}
		$name = $this->$k;
		if ($name === NULL)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE $this->_tbl_key=" . $this->_db->Quote($name) . " AND main='1' LIMIT 1");
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}

	/**
	 * Delete a record
	 *
	 * @param      integer $id Resume ID
	 * @return     boolean False if errors, True upon success
	 */
	public function delete_resume($id = NULL)
	{
		if ($id === NULL)
		{
			$id == $this->id;
		}
		if ($id === NULL)
		{
			return false;
		}

		$query  = "DELETE FROM $this->_tbl WHERE id=" . $this->_db->Quote($id);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get resume files
	 *
	 * @param      string  $pile  shortlisted or applied
	 * @param      integer $uid   User ID
	 * @param      integer $admin Admin access?
	 * @return     array
	 */
	public function getResumeFiles($pile = 'all', $uid = 0, $admin = 0)
	{
		$query  = "SELECT DISTINCT r.uid, r.filename FROM $this->_tbl AS r ";
		$query .= "JOIN #__jobs_seekers AS s ON s.uid=r.uid ";
		$query .= 	($pile == 'shortlisted' && $uid)  ? " JOIN #__jobs_shortlist AS W ON W.seeker=s.uid AND W.emp=" . $this->_db->Quote($uid) . " AND s.uid != " . $this->_db->Quote($uid) . " AND s.uid=r.uid AND W.category='resume' " : "";
		$uid = $admin ? 1 : $uid;
		$query .= 	($pile == 'applied' && $uid)  ? " LEFT JOIN #__jobs_openings AS J ON J.employerid=" . $this->_db->Quote($uid) . " JOIN #__jobs_applications AS A ON A.jid=J.id AND A.uid=s.uid AND A.status=1 " : "";
		$query .= "WHERE s.active=1 AND r.main=1 ";

		$files = array();

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		if ($result)
		{
			foreach ($result as $r)
			{
				$files[$r->uid] = $r->filename;
			}
		}

		return array_unique($files);
	}
}

