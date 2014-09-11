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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'quotas_log.php';

/**
 * Users quotas
 */
class UsersQuotas extends JTable
{
	/**
	 * ID - primary key
	 *
	 * @var int(11)
	 */
	var $id = null;

	/**
	 * User ID
	 *
	 * @var int(11)
	 */
	var $user_id = null;

	/**
	 * Quota class ID
	 *
	 * @var int(11)
	 */
	var $class_id = null;

	/**
	 * Hard files limit
	 *
	 * @var int(11)
	 */
	var $hard_files = null;

	/**
	 * Soft files limit
	 *
	 * @var int(11)
	 */
	var $soft_files = null;

	/**
	 * Hard blocks limit
	 *
	 * @var int(11)
	 */
	var $hard_blocks = null;

	/**
	 * Soft blocks limit
	 *
	 * @var int(11)
	 */
	var $soft_blocks = null;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__users_quotas', 'id', $db );
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return return true
	 */
	public function check()
	{
		// Make sure they gave numeric values
		if (!is_numeric(trim($this->soft_files)) || !is_numeric(trim($this->hard_files)) || !is_numeric(trim($this->soft_blocks)) || !is_numeric(trim($this->hard_blocks)))
		{
			$this->setError( JText::_('COM_MEMBERS_QUOTA_VALUES_MUST_BE_NUMERIC') );
			return false;
		}

		// Make sure they gave values
		if (is_null($this->soft_files) || is_null($this->hard_files) || is_null($this->soft_blocks) || is_null($this->hard_blocks))
		{
			$this->setError( JText::_('COM_MEMBERS_QUOTA_MISSING_VALUES') );
			return false;
		}

		if (!$this->id)
		{
			$query  = "SELECT uq.id";
			$query .= $this->buildquery();
			$query .= " WHERE uq.user_id = " . $this->_db->quote((int) $this->user_id);

			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError( JText::_('COM_MEMBERS_QUOTA_USER_ALREADY_EXISTS') );
				return false;
			}
		}

		return true;
	}

	/**
	 * Override store to make mw call too
	 *
	 * @return return true
	 */
	public function store($updateNulls = false)
	{
		// Use Juser, rather than JFactory::getUser, as JFactory won't get the right username if it was just updated
		$username = Juser::getInstance($this->user_id)->get('username');

		// Don't try to save quotas for auth link temp accounts (negative number usernames)
		if (is_numeric($username) && $username < 0)
		{
			return false;
		}

		$action = ($this->id) ? 'modify' : 'add';
		$result = parent::store($updateNulls);

		if ($result)
		{
			$command = "update_quota '{$this->user_id}' '{$this->soft_blocks}' '{$this->hard_blocks}'";
			$cmd = "/bin/sh ".JPATH_ROOT."/components/com_tools/scripts/mw {$command} 2>&1 </dev/null";

			exec($cmd, $results, $status);

			// Check exec status
			if (!isset($status) || $status != 0)
			{
				// Something went wrong
				$this->setError( JText::_('COM_MEMBERS_QUOTA_USER_FAILED_TO_SAVE_TO_FILESYSTEM') );
				return false;
			}

			$log = new MembersQuotasLog($this->_db);
			$log->set('object_type', 'user');
			$log->set('object_id'  , $this->id);
			$log->set('name'       , $username);
			$log->set('action'     , $action);
			$log->set('actor_id'   , JFactory::getUser()->get('id'));
			$log->set('soft_blocks', $this->soft_blocks);
			$log->set('hard_blocks', $this->hard_blocks);
			$log->set('soft_files' , $this->soft_files);
			$log->set('hard_files' , $this->hard_files);
			$log->store();

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Build query method
	 *
	 * @param  array $filters
	 * @return $query database query
	 */
	public function buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS uq";
		$query .= " LEFT JOIN `#__users_quotas_classes` AS uqc ON uq.class_id = uqc.id";
		$query .= " LEFT JOIN `#__users` AS m ON uq.user_id = m.id";

		$where = array();

		if (isset($filters['class_id']))
		{
			$where[] = "`class_id` = " . $this->_db->quote($filters['class_id']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND", $where);
		}

		return $query;
	}

	/**
	 * Retrieve a record
	 *
	 * @param  int $id of record to return
	 * @return object Return password rule records
	 */
	public function getRecord($id)
	{
		$query  = "SELECT uq.*, m.username, m.name, uqc.alias AS class_alias";
		$query .= $this->buildquery();
		$query .= " WHERE uq.id = " . $this->_db->quote((int) $id);

		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}

	/**
	 * Get a count of the number of quota classes
	 *
	 * @param  array $filters
	 * @return object Return count of rows
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(uq.id)";
		$query .= $this->buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get the an object list of quotas classes
	 *
	 * @param  array $filters start and limit, needed for pagination
	 * @return object Return password rule records
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT uq.*, m.username, m.name, uqc.alias AS class_alias";
		$query .= $this->buildquery($filters);
		$query .= " ORDER BY m.id ASC";
		if (isset($filters['start']) && isset($filters['limit']))
		{
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Update all quotas of a certain class ID to reflect a change in class defaults
	 *
	 * @param    int $id
	 * @return   void
	 */
	public function updateUsersByClassId($id)
	{
		$class = new MembersQuotasClasses($this->_db);
		$class->load($id);

		if (!$class->id)
		{
			return false;
		}

		$records = self::getRecords(array('class_id'=>$class->id));

		if ($records && count($records) > 0)
		{
			foreach ($records as $r)
			{
				$quota = new self($this->_db);
				$quota->load($r->id);
				$quota->set('hard_files',  $class->hard_files);
				$quota->set('soft_files',  $class->soft_files);
				$quota->set('hard_blocks', $class->hard_blocks);
				$quota->set('soft_blocks', $class->soft_blocks);
				$quota->store();
			}
		}

		return true;
	}

	/**
	 * Upon deletion of a class, restore all users of that class to the default class
	 *
	 * @param    int $id
	 * @return   void
	 */
	public function restoreDefaultClass($id)
	{
		$class = new MembersQuotasClasses($this->_db);
		$class->load(array('alias' => 'default'));

		if (!$class->id)
		{
			return false;
		}

		$records = self::getRecords(array('class_id'=>$id));

		if ($records && count($records) > 0)
		{
			foreach ($records as $r)
			{
				$quota = new self($this->_db);
				$quota->load($r->id);
				$quota->set('hard_files',  $class->hard_files);
				$quota->set('soft_files',  $class->soft_files);
				$quota->set('hard_blocks', $class->hard_blocks);
				$quota->set('soft_blocks', $class->soft_blocks);
				$quota->set('class_id',    $class->id);
				$quota->store();
			}
		}

		return true;
	}
}