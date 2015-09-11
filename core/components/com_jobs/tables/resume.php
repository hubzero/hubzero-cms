<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

/**
 * Table class for job resumes
 */
class Resume extends \JTable
{
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
			$this->setError(Lang::txt('ERROR_MISSING_UID'));
			return false;
		}

		if (trim($this->filename) == '')
		{
			$this->setError(Lang::txt('ERROR_MISSING_FILENAME'));
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

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE $this->_tbl_key=" . $this->_db->quote($name) . " AND main='1' LIMIT 1");
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

		$query  = "DELETE FROM $this->_tbl WHERE id=" . $this->_db->quote($id);
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
		$query .= 	($pile == 'shortlisted' && $uid)  ? " JOIN #__jobs_shortlist AS W ON W.seeker=s.uid AND W.emp=" . $this->_db->quote($uid) . " AND s.uid != " . $this->_db->quote($uid) . " AND s.uid=r.uid AND W.category='resume' " : "";
		$uid = $admin ? 1 : $uid;
		$query .= 	($pile == 'applied' && $uid)  ? " LEFT JOIN #__jobs_openings AS J ON J.employerid=" . $this->_db->quote($uid) . " JOIN #__jobs_applications AS A ON A.jid=J.id AND A.uid=s.uid AND A.status=1 " : "";
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

