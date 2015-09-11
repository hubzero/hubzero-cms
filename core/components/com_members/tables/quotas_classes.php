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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Tables;

use Lang;
use User;

/**
 * Members quota classes db table class
 */
class QuotasClasses extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__users_quotas_classes', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return  boolean  True if valid, False if not
	 */
	public function check()
	{
		// Make sure they gave an alias
		if (trim($this->alias) == '')
		{
			$this->setError(Lang::txt('COM_MEMBERS_QUOTA_CLASS_MUST_HAVE_ALIAS'));
			return false;
		}

		// Make sure the alias isn't 'custom'
		if (trim($this->alias) == 'custom')
		{
			$this->setError(Lang::txt('COM_MEMBERS_QUOTA_CLASS_CUSTOM'));
			return false;
		}

		if (!$this->id)
		{
			$query  = "SELECT id";
			$query .= $this->buildquery();
			$query .= " WHERE alias = " . $this->_db->quote($this->alias);

			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(Lang::txt('COM_MEMBERS_QUOTA_CLASS_NON_UNIQUE_ALIAS'));
				return false;
			}
		}

		return true;
	}

	/**
	 * Override store to add logging
	 *
	 * @param   boolean  $updateNulls
	 * @return  boolean
	 */
	public function store($updateNulls = false)
	{
		$action = ($this->id) ? 'modify' : 'add';
		$result = parent::store($updateNulls);

		if ($result)
		{
			$log = new QuotasLog($this->_db);
			$log->set('object_type', 'class');
			$log->set('object_id'  , $this->id);
			$log->set('name'       , $this->alias);
			$log->set('action'     , $action);
			$log->set('actor_id'   , User::get('id'));
			$log->set('soft_blocks', $this->soft_blocks);
			$log->set('hard_blocks', $this->hard_blocks);
			$log->set('soft_files' , $this->soft_files);
			$log->set('hard_files' , $this->hard_files);
			$log->store();

			return true;
		}

		return false;
	}

	/**
	 * Override delete to add logging
	 *
	 * @param   string   $pk
	 * @return  boolean
	 */
	public function delete($pk = null)
	{
		$result = parent::delete($pk);

		if ($result)
		{
			$log = new QuotasLog($this->_db);
			$log->set('object_type', 'class');
			$log->set('object_id'  , $this->id);
			$log->set('name'       , $this->alias);
			$log->set('action'     , 'delete');
			$log->set('actor_id'   , User::get('id'));
			$log->set('soft_blocks', $this->soft_blocks);
			$log->set('hard_blocks', $this->hard_blocks);
			$log->set('soft_files' , $this->soft_files);
			$log->set('hard_files' , $this->hard_files);
			$log->store();

			return true;
		}

		return false;
	}

	/**
	 * Build query method
	 *
	 * @param   array   $filters
	 * @return  string  Database query
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS uqc";

		$where = array();

		if (isset($filters['alias']) && $filters['alias'])
		{
			$where[] = "`alias`=" . $this->_db->quote($filters['alias']);
		}

		if (count($where))
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count of the number of quota classes
	 *
	 * @param   array    $filters
	 * @return  integer  Return count of rows
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(uqc.id)";
		$query .= $this->buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get the an object list of quotas classes
	 *
	 * @param   array  $filters  Start and limit, needed for pagination
	 * @return  array  Return password rule records
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT uqc.*";
		$query .= $this->buildquery($filters);
		$query .= " ORDER BY uqc.id ASC";
		if (isset($filters['start']) && isset($filters['limit']))
		{
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the an object list of quotas classes
	 *
	 * @param   array  $filters  Start and limit, needed for pagination
	 * @return  array  Return password rule records
	 */
	public function getGroupIds($id=null)
	{
		if (is_null($id))
		{
			$id = $this->id;
		}

		$groups = array();

		if ($id)
		{
			require_once __DIR__ . '/quotas_classes_groups.php';

			$qcGroups = new QuotasClassesGroups($this->_db);
			foreach ($qcGroups->find('list', array('class_id' => $id)) as $group)
			{
				$groups[] = $group->group_id;
			}
		}

		return $groups;
	}

	/**
	 * Get the an object list of quotas classes
	 *
	 * @param   array  $filters  Start and limit, needed for pagination
	 * @return  array  Return password rule records
	 */
	public function setGroupIds($groups=array())
	{
		if (!is_array($groups))
		{
			$groups = array($groups);
		}
		$groups = array_map('intval', $groups);

		require_once __DIR__ . '/quotas_classes_groups.php';

		$qcGroups = new QuotasClassesGroups($this->_db);
		$qcGroups->class_id = $this->id;

		// Clear old records
		if (!$qcGroups->deleteByClassId($this->id))
		{
			$this->setError($qcGroups->getError());
			return false;
		}

		foreach ($groups as $group)
		{
			$qcGroups->id       = null;
			$qcGroups->group_id = $group;
			if (!$qcGroups->store())
			{
				$this->setError($qcGroups->getError());
				return false;
			}
		}

		return true;
	}
}