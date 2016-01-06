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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Tables;

use User;
use Lang;
use Date;

/**
 * Table class for a forum category
 */
class Category extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__forum_categories', 'id', $db);
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
		return 'com_forum.category.' . (int) $this->$k;
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
	 * @param   object   $table  A JTable object for the asset parent.
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
			$query->where('name = ' . $db->quote('com_forum'));

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
	 * Overloaded bind function.
	 *
	 * @param   mixed  $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore.
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 */
	public function bind($src, $ignore = array())
	{
		// Bind the rules.
		if (is_object($src))
		{
			if (isset($src->rules) && is_array($src->rules))
			{
				$rules = new \JAccessRules($src->rules);
				$this->setRules($rules);
			}
		}
		else if (is_array($src))
		{
			if (isset($src['rules']) && is_array($src['rules']))
			{
				$rules = new \JAccessRules($src['rules']);
				$this->setRules($rules);
			}
		}

		return parent::bind($src, $ignore);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   string   $oid  Record alias
	 * @return  boolean  True on success
	 */
	public function loadByAlias($oid=NULL, $section_id=null, $scope_id=null, $scope='site')
	{
		$fields = array(
			'alias' => trim((string) $oid),
			'state' => 1
		);

		if ($section_id !== null)
		{
			$fields['section_id'] = (int) $section_id;
		}
		if ($scope_id !== null)
		{
			$fields['scope_id'] = (int) $scope_id;
			$fields['scope']    = (string) $scope;
		}

		return parent::load($fields);
	}

	/**
	 * Load a record by its alias and bind data to $this
	 *
	 * @param   string   $oid  Record alias
	 * @return  boolean  True upon success, False if errors
	 */
	public function loadByObject($oid=NULL, $section_id=null, $scope_id=null, $scope='site')
	{
		$fields = array(
			'object_id' => intval($oid),
			'state'     => 1
		);

		if ($section_id !== null)
		{
			$fields['section_id'] = (int) $section_id;
		}
		if ($scope_id !== null)
		{
			$fields['scope_id'] = (int) $scope_id;
			$fields['scope']    = (string) $scope;
		}

		return parent::load($fields);
	}

	/**
	 * Populate the object with default data
	 *
	 * @param   integer  $group  ID of group the data belongs to
	 * @return  boolean  True if data is bound to $this object
	 */
	public function loadDefault($scope_id=0, $scope='site')
	{
		$result = array(
			'id'          => 0,
			'title'       => Lang::txt('COM_FORUM_CATEGORY_DEFAULT'),
			'description' => Lang::txt('COM_FORUM_CATEGORY_DEFAULT_DESCRIPTION'),
			'section_id'  => 0,
			'created_by'  => 0,
			'scope'       => $scope,
			'scope_id'    => $scope_id,
			'state'       => 1,
			'access'      => 1
		);
		$result['alias'] = str_replace(' ', '-', $result['title']);
		$result['alias'] = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($result['alias']));

		return $this->bind($result);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->title = trim($this->title);

		if (!$this->title)
		{
			$this->setError(Lang::txt('Please provide a title.'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = str_replace(' ', '-', strtolower($this->title));
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', $this->alias);
		if (!$this->alias)
		{
			$this->setError(Lang::txt('Alias cannot be all punctuation or blank.'));
			return false;
		}

		$this->scope = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($this->scope));
		$this->scope_id = intval($this->scope_id);

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
			if (!$this->ordering)
			{
				$this->ordering = $this->getHighestOrdering($this->scope, $this->scope_id);
			}
		}
		else
		{
			$this->modified    = Date::toSql();
			$this->modified_by = User::get('id');
		}

		return true;
	}

	/**
	 * Get the last page in the ordering
	 *
	 * @param   string   $offering_id
	 * @return  integer
	 */
	public function getHighestOrdering($scope, $scope_id)
	{
		$sql = "SELECT MAX(ordering)+1 FROM $this->_tbl WHERE scope_id=" . $this->_db->quote(intval($scope_id)) . " AND scope=" . $this->_db->quote($scope);
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Build a query based off of filters passed
	 *
	 * @param   array   $filters  Filters to construct query from
	 * @return  string  SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query  = "FROM $this->_tbl AS c";
		if (isset($filters['group']) && (int) $filters['group'] >= 0)
		{
			$query .= " LEFT JOIN #__xgroups AS g ON g.gidNumber=c.scope_id";
		}
		$query .= " LEFT JOIN #__viewlevels AS a ON c.access=a.id";

		$where = array();
		if (isset($filters['state']) && (int) $filters['state'] >= 0)
		{
			$where[] = "c.state=" . $this->_db->quote(intval($filters['state']));
		}
		if (isset($filters['access']))
		{
			if (is_array($filters['access']))
			{
				$filters['access'] = array_map('intval', $filters['access']);
				$where[] = "c.access IN (" . implode(',', $filters['access']) . ")";
			}
			else if ($filters['access'] >= 0)
			{
				$where[] = "c.access=" . $this->_db->quote(intval($filters['access']));
			}
		}
		if (isset($filters['closed']))
		{
			$where[] = "c.closed=" . $this->_db->quote(intval($filters['closed']));
		}
		if (isset($filters['group']) && (int) $filters['group'] >= 0)
		{
			$where[] = "(c.scope_id=" . $this->_db->quote(intval($filters['group'])) . " AND c.scope=" . $this->_db->quote('group') . ")";
		}
		if (isset($filters['scope']) && (string) $filters['scope'])
		{
			$where[] = "c.scope=" . $this->_db->quote(strtolower($filters['scope']));
		}
		if (isset($filters['scope_id']) && (int) $filters['scope_id'] >= 0)
		{
			$where[] = "c.scope_id=" . $this->_db->quote(intval($filters['scope_id']));
		}
		/*if (isset($filters['scope_sub_id']) && (int) $filters['scope_sub_id'] >= 0)
		{
			$where[] = "c.scope_sub_id=" . $this->_db->quote(intval($filters['scope_sub_id']));
		}*/
		if (isset($filters['section_id']) && (int) $filters['section_id'] >= 0)
		{
			$where[] = "c.section_id=" . $this->_db->quote(intval($filters['section_id']));
		}
		if (isset($filters['object_id']) && (int) $filters['object_id'] >= 0)
		{
			$where[] = "c.object_id=" . $this->_db->quote(intval($filters['object_id']));
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(c.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
				OR LOWER(c.description) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param   array    $filters  Filters to construct query from
	 * @return  integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to construct query from
	 * @return  array
	 */
	public function getRecords($filters=array())
	{
		$flt = "";
		if (isset($filters['scope_sub_id']) && (int) $filters['scope_sub_id'] >= 0)
		{
			$flt = " AND (r.scope_sub_id=" . $this->_db->quote(intval($filters['scope_sub_id'])) . " OR r.sticky=1)";
		}
		if (isset($filters['access']))
		{
			if (is_array($filters['access']))
			{
				$filters['access'] = array_map('intval', $filters['access']);
				$flt .= " AND r.access IN (" . implode(',', $filters['access']) . ")";
			}
			else if ($filters['access'] >= 0)
			{
				$flt .= " AND r.access=" . $this->_db->quote(intval($filters['access']));
			}
		}

		if (isset($filters['admin']))
		{
			$query  = "SELECT c.*";
			if (isset($filters['group']) && (int) $filters['group'] >= 0)
			{
				$query .= ", g.cn AS group_alias";
			}
			$query .= ", (SELECT COUNT(*) FROM #__forum_posts AS r WHERE r.category_id=c.id AND r.parent=0 $flt) AS threads,
						(SELECT COUNT(*) FROM #__forum_posts AS r WHERE r.category_id=c.id $flt) AS posts";
		}
		else
		{
			$query  = "SELECT c.*";
			if (isset($filters['group']) && (int) $filters['group'] >= 0)
			{
				$query .= ", g.cn AS group_alias";
			}
			$query .= ", (SELECT COUNT(*) FROM #__forum_posts AS r WHERE r.category_id=c.id AND r.parent=0 AND r.state=1 $flt) AS threads,
						(SELECT COUNT(*) FROM #__forum_posts AS r WHERE r.category_id=c.id AND r.state=1 $flt) AS posts";
		}
		$query .= ", a.title AS access_level";
		$query .= " " . $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= ' LIMIT ' . $filters['start'] . ',' . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a count of all threads for a category
	 *
	 * @param   integer  $oid       Category ID
	 * @param   integer  $group_id  Group ID
	 * @return  array
	 */
	public function getThreadCount($oid=null, $scope_id=0, $scope='site')
	{
		$k = $this->_tbl_key;
		if ($oid !== null)
		{
			$this->$k = intval($oid);
		}

		$query = "SELECT COUNT(*) FROM `#__forum_posts` WHERE `category_id`=" . $this->_db->quote($this->$k) . " AND `scope_id`=" . $this->_db->quote($scope_id) . " AND `scope`=" . $this->_db->quote($scope) . " AND parent=0 AND state < 2";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a count of all posts for a category
	 *
	 * @param   integer  $oid       Category ID
	 * @param   integer  $group_id  Group ID
	 * @return  array
	 */
	public function getPostCount($oid=null, $scope_id=0, $scope='site')
	{
		$k = $this->_tbl_key;
		if ($oid !== null)
		{
			$this->$k = intval($oid);
		}

		//$query = "SELECT COUNT(*) FROM `#__forum_posts` WHERE parent IN (SELECT r.id FROM `#__forum_posts` AS r WHERE r.category_id=" . $this->$k . " AND group_id=$group_id AND parent=0 AND state < 2)";
		$query = "SELECT COUNT(*) FROM `#__forum_posts` AS r WHERE r.category_id=" . $this->_db->quote($this->$k) . " AND scope_id=" . $this->_db->quote($scope_id) . " AND scope=" . $this->_db->quote($scope) . " AND parent=0 AND state < 2";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Delete a category and all associated content
	 *
	 * @param   integer  $oid  Object ID (primary key)
	 * @return  boolean  True if successful otherwise returns and error message
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid)
		{
			$this->$k = intval($oid);
		}

		include_once(__DIR__ . DS . 'post.php');

		$post = new Post($this->_db);
		if (!$post->deleteByCategory($this->$k))
		{
			$this->setError($post->getErrorMsg());
			return false;
		}

		return parent::delete();
	}

	/**
	 * Set the state of records for a section
	 *
	 * @param   integer  $section  Section ID
	 * @param   integer  $state    State (0, 1, 2)
	 * @return  array
	 */
	public function setStateBySection($section=null, $state=null)
	{
		if ($section=== null)
		{
			$section = $this->section_id;
		}
		if ($state === null || $section === null)
		{
			return false;
		}

		if (is_array($section))
		{
			$section = array_map('intval', $section);
			$section = implode(',', $section);
		}
		else
		{
			$section = intval($section);
		}

		$this->_db->setQuery("UPDATE $this->_tbl SET state=" . $this->_db->quote($state) . " WHERE section_id IN ($section)");
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}
