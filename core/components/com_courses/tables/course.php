<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;
use User;
use Date;
use Lang;

/**
 * Courses table
 */
class Course extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses', 'id', $db);
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
		return 'com_courses.course.' . (int) $this->$k;
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
	 * @param   object   $table  A Table object for the asset parent.
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
			$query->where('name = ' . $db->quote('com_courses'));

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
	 * Validate fields before store()
	 *
	 * @return  boolean  True if all fields are valid
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
			$this->alias = str_replace(' ', '_', strtolower($this->title));
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9_\-\.]/", '', $this->alias);
		if (is_numeric($this->alias)
		 && intval($this->alias) == $this->alias
		 && $this->alias >= 0)
		{
			$this->setError(Lang::txt('Invalid alias.'));
			return false;
		}
		$this->makeAliasUnique();

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
		}

		return true;
	}

	/**
	 * Return a unique alias based on given alias
	 *
	 * @return  integer
	 */
	private function makeAliasUnique()
	{
		$sql = "SELECT alias FROM $this->_tbl";
		if ($this->id)
		{
			$sql .= " WHERE `id`!=" . $this->_db->quote(intval($this->id));
		}
		$this->_db->setQuery($sql);
		$result = $this->_db->loadColumn();

		$original_alias = $this->alias;

		if ($result)
		{
			for ($i=1; in_array($this->alias, $result); $i++)
			{
				$this->alias = $original_alias . $i;
			}
		}
	}

	/**
	 * Populate the current object with a database record if found
	 * Accepts either an alias or an ID
	 *
	 * @param   mixed    $keys   Unique ID or alias of object to retrieve
	 * @param   mixed    $reset  Reset object
	 * @return  boolean  True on success
	 */
	public function load($keys = null, $reset = true)
	{
		if (empty($keys))
		{
			return false;
		}

		if (is_numeric($keys))
		{
			return parent::load($keys, $reset);
		}

		return parent::load(array(
			'alias' => $keys
		), $reset);
	}

	/**
	 * Build a query based off of filters passed
	 *
	 * @param   array   $filters  Filters to construct query from
	 * @return  string  SQL
	 */
	protected function _buildQuery($filters=array())
	{
		if (isset($filters['tag']) && $filters['tag'] != '')
		{
			$query  = " FROM #__tags_object AS rta";
			$query .= " INNER JOIN #__tags AS t ON rta.tagid = t.id AND rta.tbl='courses'";
			$query .= " INNER JOIN $this->_tbl AS c ON rta.objectid=c.id";
		}
		else
		{
			$query  = " FROM $this->_tbl AS c";
		}

		$where = array();

		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			if (is_array($filters['state']))
			{
				$filters['state'] = array_map('intval', $filters['state']);
				$where[] = "c.state IN (" . $filters['state'] . ")";
			}
			else
			{
				$where[] = "c.state=" . $this->_db->quote($filters['state']);
			}
		}
		if (isset($filters['created_by']) && $filters['created_by'] >= 0)
		{
			$where[] = "c.created_by=" . $this->_db->quote($filters['created_by']);
		}
		if (isset($filters['access']) && $filters['access'] >= 0)
		{
			$where[] = "c.access=" . $this->_db->quote($filters['access']);
		}
		if (isset($filters['alias']) && $filters['alias'])
		{
			$where[] = "c.alias=" . $this->_db->quote($filters['alias']);
		}
		if (isset($filters['group_id']) && $filters['group_id'] >= 0)
		{
			$where[] = "c.group_id=" . $this->_db->quote($filters['group_id']);
		}

		if (isset($filters['index']) && $filters['index'] != '')
		{
			$where[] = "LOWER(LEFT(c.title, 1)) = " . $this->_db->quote(strtolower($filters['index']));
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(c.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(c.blurb) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(c.alias) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (isset($filters['tag']) && $filters['tag'] != '')
		{
			include_once dirname(__DIR__) . DS . 'models' . DS . 'tags.php';
			$tagging = new \Components\Courses\Models\Tags();
			$tags = $tagging->parseTags($filters['tag']);

			$where[] = "t.tag IN ('" . implode("','", $tags) . "')";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		if (isset($filters['tag']) && $filters['tag'] != '')
		{
			$query .= " GROUP BY c.id HAVING uniques=" . (isset($filters['tag_any']) && $filters['tag_any'] ? '1' : count($tags));
		}

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			if (!isset($filters['sort']) || !$filters['sort'])
			{
				$filters['sort'] = 'title';
			}
			if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
			{
				$filters['sort_Dir'] = 'DESC';
			}
			$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
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

		$query  = "SELECT COUNT(*) ";
		$query .= (isset($filters['tag']) && $filters['tag'] != '') ? ", COUNT(DISTINCT t.tag) AS uniques " : " ";
		$query .= $this->_buildQuery($filters);

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
		$query  = "SELECT c.*, (SELECT COUNT(*) FROM `#__courses_members` AS m WHERE m.student=1 AND m.course_id=c.id) AS students";
		$query .= (isset($filters['tag']) && $filters['tag'] != '') ? ", t.tag, t.raw_tag, COUNT(DISTINCT t.tag) AS uniques " : " ";
		$query .= $this->_buildQuery($filters);

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			if (!isset($filters['start']))
			{
				$filters['start'] = 0;
			}
			$query .= ' LIMIT ' . intval($filters['start']) . ',' . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get courses for a user
	 *
	 * @param   integer  $uid    User ID
	 * @param   string   $type   Status in course
	 * @param   integer  $limit  Number of records
	 * @param   integer  $start  Where to start in record paging
	 * @return  array
	 */
	public function getUserCourses($uid, $type='all', $limit=null, $start=0)
	{
		if (!$uid)
		{
			$this->setError(Lang::txt('Missing user ID.'));
			return false;
		}

		$query2 = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title
					FROM $this->_tbl AS c
					JOIN #__courses_members AS m ON m.course_id=c.id
					LEFT JOIN #__courses_offerings AS o ON o.id=m.offering_id
					LEFT JOIN #__courses_offering_sections AS s on s.id=m.section_id
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE m.user_id=" . $this->_db->quote($uid) . " AND m.student=0 AND r.alias='manager'";

		$query3 = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title
					FROM $this->_tbl AS c
					JOIN #__courses_members AS m ON m.course_id=c.id
					LEFT JOIN #__courses_offerings AS o ON o.id=m.offering_id
					LEFT JOIN #__courses_offering_sections AS s on s.id=m.section_id
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE m.user_id=" . $this->_db->quote($uid) . " AND m.student=0 AND r.alias='instructor'";

		$query4 = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title
					FROM $this->_tbl AS c
					JOIN #__courses_members AS m ON m.course_id=c.id
					LEFT JOIN #__courses_offerings AS o ON o.id=m.offering_id
					LEFT JOIN #__courses_offering_sections AS s on s.id=m.section_id
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE m.user_id=" . $this->_db->quote($uid) . " AND m.student=1 AND c.state=1";

		$query5 = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title
					FROM $this->_tbl AS c
					JOIN #__courses_members AS m ON m.course_id=c.id
					LEFT JOIN #__courses_offerings AS o ON o.id=m.offering_id
					LEFT JOIN #__courses_offering_sections AS s on s.id=m.section_id
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE m.user_id=" . $this->_db->quote($uid) . " AND m.student=0 AND r.alias='ta' AND c.state=1";

		switch ($type)
		{
			case 'all':
				$query = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title
					FROM $this->_tbl AS c
					JOIN #__courses_members AS m ON m.course_id=c.id
					LEFT JOIN #__courses_offerings AS o ON o.id=m.offering_id
					LEFT JOIN #__courses_offering_sections AS s on s.id=m.section_id
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE m.user_id=" . $this->_db->quote($uid);
			break;
			case 'manager':
				$query = $query2; //"( $query1 ) UNION ( $query2 )";
			break;
			case 'instructor':
				$query = $query3;
			break;
			case 'student':
				$query = $query4;
			break;
			case 'ta':
				$query = $query5;
			break;

			default:
				$query = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title
					FROM $this->_tbl AS c
					JOIN #__courses_members AS m ON m.course_id=c.id
					LEFT JOIN #__courses_offerings AS o ON o.id=m.offering_id
					LEFT JOIN #__courses_offering_sections AS s on s.id=m.section_id
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE m.user_id=" . $this->_db->quote($uid) . " AND r.alias=" . $this->_db->quote($type);
			break;
		}

		if (!is_null($limit) && $limit != 0)
		{
			if (is_null($start))
			{
				$start = 0;
			}
			$query .= ' LIMIT ' . intval($start) . ',' . intval($limit);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
