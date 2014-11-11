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

/**
 * Members quota classes db table class
 */
class MembersQuotasClasses extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct( &$db )
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
			$this->setError(JText::_('COM_MEMBERS_QUOTA_CLASS_MUST_HAVE_ALIAS'));
			return false;
		}

		// Make sure the alias isn't 'custom'
		if (trim($this->alias) == 'custom')
		{
			$this->setError(JText::_('COM_MEMBERS_QUOTA_CLASS_CUSTOM'));
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
				$this->setError(JText::_('COM_MEMBERS_QUOTA_CLASS_NON_UNIQUE_ALIAS'));
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
			$log = new MembersQuotasLog($this->_db);
			$log->set('object_type', 'class');
			$log->set('object_id'  , $this->id);
			$log->set('name'       , $this->alias);
			$log->set('action'     , $action);
			$log->set('actor_id'   , JFactory::getUser()->get('id'));
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
			$log = new MembersQuotasLog($this->_db);
			$log->set('object_type', 'class');
			$log->set('object_id'  , $this->id);
			$log->set('name'       , $this->alias);
			$log->set('action'     , 'delete');
			$log->set('actor_id'   , JFactory::getUser()->get('id'));
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
			$where[] = "`alias`=" . $this->_db->Quote($filters['alias']);
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
}