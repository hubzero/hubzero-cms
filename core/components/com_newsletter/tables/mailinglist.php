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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Tables;

/**
 * Table class for mailinglists
 */
class Mailinglist extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  $db  Database Object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__newsletter_mailinglists', 'id', $db);

		//set up the assoc table
		$this->_tbl_assoc = '#__newsletter_mailinglist_emails';
		$this->_tbl_assoc_key = 'id';
	}

	/**
	 * Newsletter Mailing List Save Check method
	 *
	 * @return  boolean
	 */
	public function check()
	{
		if (trim($this->name) == '')
		{
			$this->setError('Newsletter mailing list must have a name.');
			return false;
		}

		return true;
	}

	/**
	 * Get Mailing Lists
	 *
	 * @param   integer  $id       Mailing List Id
	 * @param   string   $privacy
	 * @return  mixed
	 */
	public function getLists($id = null, $privacy = null)
	{
		$sql = "SELECT
					ml.*,
					(SELECT COUNT(*) FROM {$this->_tbl_assoc} AS mle WHERE mle.mid=ml.id AND mle.status='active') as active_count,
					(SELECT COUNT(*) FROM {$this->_tbl_assoc} AS mle WHERE mle.mid=ml.id) as total_count
				FROM {$this->_tbl} AS ml
				WHERE ml.deleted=0";

		//do we have a specific status
		if (strtolower($privacy) == 'private')
		{
			$sql .= " AND ml.private=1";
		}
		else if (strtolower($privacy) == 'public')
		{
			$sql .= " AND ml.private=0";
		}

		//do we have an id
		if ($id)
		{
			$sql .= " AND ml.id=" . $this->_db->quote($id);
			$this->_db->setQuery($sql);
			return $this->_db->loadObject();
		}
		else
		{
			$this->_db->setQuery($sql);
			return $this->_db->loadObjectList();
		}
	}

	/**
	 * Get number of emails in list
	 *
	 * @param   array   $filters
	 * @return  integer
	 */
	public function getListEmailsCount($filters)
	{
		// are we loading default list
		if (isset($filters['lid']) && $filters['lid'] == -1)
		{
			return count($this->_getHubMailingList());
		}

		$sql = "SELECT COUNT(*) FROM {$this->_tbl_assoc} AS mle";
		$wheres = array();

		if (isset($filters['lid']))
		{
			$wheres[] = "mle.mid=" . $this->_db->quote($filters['lid']);
		}

		if (isset($filters['status']))
		{
			$wheres[] = "mle.status=" . $this->_db->quote($filters['status']);
		}

		if (count($wheres) > 0)
		{
			$sql .= " WHERE " . implode(' AND ', $wheres);
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get Mailing List Emails
	 *
	 * @param   integer  $mailinglistId
	 * @param   string   $key
	 * @param   array    $filters
	 * @return  array
	 */
	public function getListEmails($mailinglistId, $key = null, $filters = array())
	{
		// make sure we have a mailing list
		if (!$mailinglistId)
		{
			return;
		}

		// are we loading default list
		if ($mailinglistId == '-1')
		{
			$list = $this->_getHubMailingList();
			if (isset($filters['select']))
			{
				return array_keys($list);
			}
			return $list;
		}

		// default select
		$select = "mle.*, (SELECT reason FROM #__newsletter_mailinglist_unsubscribes AS u
				WHERE mle.email=u.email AND mle.mid=u.mid LIMIT 1) AS unsubscribe_reason";

		// specific select
		if (isset($filters['select']))
		{
			$select = $filters['select'];
		}

		// get list of emails
		$sql = "SELECT {$select}
				FROM {$this->_tbl_assoc} AS mle
				WHERE mle.mid=" . $this->_db->quote($mailinglistId);

		// do we have a status
		if (isset($filters['status']) && $filters['status'] != 'all')
		{
			 $sql .= " AND mle.status=" . $this->_db->quote($filters['status']);
		}

		// do we have an order filter
		if (isset($filters['sort']) && $filters['sort'] != '')
		{
			$sql .= " ORDER BY mle." . $filters['sort'];
		}
		else
		{
			$sql .= " ORDER BY mle.id";
		}

		// limit and start
		if (isset($filters['limit']))
		{
			$start = (isset($filters['start'])) ? $filters['start'] : 0;
			$sql .= " LIMIT " . $start . ", " . $filters['limit'];
		}

		$this->_db->setQuery($sql);

		if (isset($filters['select']))
		{
			return $this->_db->loadColumn();
		}

		return $this->_db->loadObjectList($key);
	}

	/**
	 * Get Mailing List Emails
	 *
	 * @param   string  $email
	 * @param   string  $key
	 * @param   string  $status
	 * @return  array
	 */
	public function getListsForEmail($email, $key = null, $status = 'all')
	{
		if (!$email)
		{
			return;
		}

		// get lists that member belongs to
		$sql = "SELECT mle.id AS id, ml.id as mailinglistid, ml.name, ml.description, mle.status, mle.confirmed
				FROM {$this->_tbl} AS ml, {$this->_tbl_assoc} AS mle
				WHERE ml.id=mle.mid
				AND ml.deleted=0
				AND mle.email=" . $this->_db->quote($email);

		// do we have a status
		if ($status != 'all')
		{
			$sql .= " AND mle.status=" . $this->_db->quote($status);
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList($key);
	}

	/**
	 * Get default hub members list
	 *
	 * @return  array  Email List
	 */
	private function _getHubMailingList()
	{
		$sql = "SELECT DISTINCT `email` FROM `#__users` WHERE `activation` >= 1 AND `sendEmail` > '0'";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList('email');
	}
}
