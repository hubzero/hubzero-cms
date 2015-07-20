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
 * Table class for knowledge base categories
 */
class KbTableCategory extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__faq_categories', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->title = trim($this->title);
		if ($this->title == '')
		{
			$this->setError(JText::_('COM_KB_ERROR_EMPTY_TITLE'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = str_replace(' ', '-', strtolower($this->title));
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', $this->alias);

		$this->id          = intval($this->id);
		$this->description = trim($this->description);
		$this->section     = intval($this->section);
		$this->state       = intval($this->state);

		if ($this->section)
		{
			$this->_db->setQuery("SELECT COUNT(*) FROM `#__faq` WHERE `section`=" . $this->_db->quote($this->section) . " AND `alias`=" . $this->_db->quote($this->alias));
			if ($result = $this->_db->loadResult())
			{
				$this->setError(JText::_('COM_KB_ERROR_ALIAS_IN_USE'));
				return false;
			}
		}

		if (!$this->id)
		{
			$this->access = $this->access ? $this->access : 0;
		}
		$this->access = intval($this->access);

		return true;
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_kb.category.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     The id for the asset
	 * @return  integer  The id of the asset's parent
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db = $this->getDbo();

		if ($assetId === null)
		{
			// Build the query to get the asset id for the parent category.
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__assets');
			$query->where('name = ' . $db->quote('com_kb'));

			// Get the asset id from the database.
			$db->setQuery($query);
			if ($result = $db->loadResult())
			{
				$assetId = (int) $result;
			}
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
	 */
	public function load($keys = null, $reset = true)
	{
		if (is_string($keys) && !is_numeric($keys))
		{
			return parent::load(array(
				'alias' => $keys
			), $reset);
		}

		return parent::load($keys, $reset);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   string   $oid  Alias
	 * @return  boolean  True upon success, False if errors
	 */
	public function loadAlias($oid=NULL)
	{
		return $this->load($oid);
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	private function _buildQuery($filters=array())
	{
		$query = "FROM `$this->_tbl` AS a LEFT JOIN `#__viewlevels` AS g ON g.`id` = (a.`access` + 1)";

		$where = array();

		if (isset($filters['section']) && $filters['section'] >= 0)
		{
			$where[] = "a.`section`=" . $this->_db->Quote($filters['section']);
		}
		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			$where[] = "a.`state`=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['access']) && $filters['access'] >= 0)
		{
			$where[] = "a.`access`=" . $this->_db->Quote($filters['access']);
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "(a.`title` LIKE " . $this->_db->quote('%' . $filters['search'] . '%') . " OR a.`description` LIKE " . $this->_db->quote('%' . $filters['search'] . '%') . ")";
		}
		if (isset($filters['empty']) && !$filters['empty'])
		{
			if (isset($filters['section']) && $filters['section'] > 0)
			{
				$where[] = "(SELECT COUNT(*) FROM `#__faq` AS fa WHERE fa.category=a.id) > 0";
			}
			else
			{
				$where[] = "(SELECT COUNT(*) FROM `#__faq` AS fa WHERE fa.section=a.id) > 0";
			}
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count of, single entry, or list of entries
	 * 
	 * @param   string   $rtrn     Data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   array    $select   List of fields to select
	 * @return  mixed
	 */
	public function find($what='', $filters=array())
	{
		$what = strtolower($what);

		switch ($what)
		{
			case 'count':
				$query = "SELECT COUNT(a.id) " . $this->_buildQuery($filters);

				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'one':
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'first':
				$filters['start'] = 0;

				return $this->find('one', $filters);
			break;

			case 'all':
				if (isset($filters['limit']))
				{
					unset($filters['limit']);
				}
				return $this->find('list', $filters);
			break;

			case 'list':
			default:
				// Make sure article count takes access and state into consideration
				$access = '';
				if (isset($filters['state']) && $filters['state'] >= 0)
				{
					$access .= " AND fa.`state`=" . $this->_db->Quote($filters['state']);
				}
				if (isset($filters['access']) && $filters['access'] >= 0)
				{
					$access .= " AND fa.`access`=" . $this->_db->Quote($filters['access']);
				}

				if (isset($filters['section']) && $filters['section'] > 0)
				{
					$query  = "SELECT a.*, g.`title` AS groupname,
						(SELECT COUNT(*) FROM `#__faq` AS fa WHERE fa.category=a.id $access) AS articles,
						(SELECT COUNT(*) FROM `$this->_tbl` AS fc WHERE fc.section=a.id) AS categories ";
				}
				else
				{
					$query  = "SELECT a.*, g.`title` AS groupname,
						(SELECT COUNT(*) FROM `#__faq` AS fa WHERE fa.section=a.id $access) AS articles,
						(SELECT COUNT(*) FROM `$this->_tbl` AS fc WHERE fc.section=a.id) AS categories ";
				}

				$query .= $this->_buildQuery($filters);

				if (isset($filters['sort']) && $filters['sort'])
				{
					if (substr($filters['sort'], 0, 2) != 'a.' && array_key_exists($filters['sort'], $this->getFields()))
					{
						$filters['sort'] = 'a.' . $filters['sort'];
					}
					$filters['sort_Dir'] = (isset($filters['sort_Dir'])) ? $filters['sort_Dir'] : 'DESC';
					$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
					if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
					{
						$filters['sort_Dir'] = 'DESC';
					}

					$query .= " ORDER BY " . $filters['sort'] . " " .  $filters['sort_Dir'];
				}

				if (isset($filters['limit']) && $filters['limit'] > 0)
				{
					$filters['start'] = (isset($filters['start']) ? $filters['start'] : 0);

					$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}
}

