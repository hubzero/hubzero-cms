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
 * Courses table
 */
class CoursesTableCourse extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $alias    = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $group_id = NULL;

	/**
	 * varchar(50)
	 *
	 * @var string
	 */
	var $title = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $state = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $type = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $access = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $blurb = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $description = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $logo = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created_by = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $params = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
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
	 *
	 * @since   11.1
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
	 *
	 * @since   11.1
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
	 *
	 * @return  integer  The id of the asset's parent
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db = $this->getDbo();

		if ($assetId === null)
		{
			// Build the query to get the asset id for the parent category.
			$query	= $db->getQuery(true);
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
	 * @return     boolean True if all fields are valid
	 */
	public function check()
	{
		$this->title = trim($this->title);

		if (!$this->title)
		{
			$this->setError(JText::_('Please provide a title.'));
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
			$this->setError(JText::_('Invalid alias.'));
			return false;
		}
		$this->makeAliasUnique();

		if (!$this->id)
		{
			$juser = JFactory::getUser();
			$this->created = JFactory::getDate()->toSql();
			$this->created_by = $juser->get('id');
		}
		return true;
	}

	/**
	 * Return a unique alias based on given alias
	 *
	 * @return     integer
	 */
	private function makeAliasUnique()
	{
		$sql = "SELECT alias FROM $this->_tbl";
		if ($this->id)
		{
			$sql .= " WHERE `id`!=" . $this->_db->Quote(intval($this->id));
		}
		$this->_db->setQuery($sql);
		$result = $this->_db->loadResultArray();

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
	 * Save changes
	 *
	 * @return     boolean
	 */
	/*public function save()
	{
		$this->setError('You\'re doing it wrong!');
		return false;
	}

	/**
	 * Insert or Update the object
	 *
	 * @return     boolean
	 */
	/*public function store()
	{
		$this->setError('You\'re doing it wrong!');
		return false;
	}

	/**
	 * Populate the current object with a database record if found
	 * Accepts either an alias or an ID
	 *
	 * @param      mixed $oid Unique ID or alias of object to retrieve
	 * @return     boolean True on success
	 */
	public function load($keys = NULL, $reset = true)
	{
		if (empty($keys))
		{
			return false;
		}

		if (is_numeric($keys))
		{
			return parent::load($keys);
		}

		$sql  = "SELECT * FROM $this->_tbl WHERE `alias`=" . $this->_db->Quote($keys) . " LIMIT 1";
		$this->_db->setQuery($sql);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Build a query based off of filters passed
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     string SQL
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

		if (isset($filters['state']))
		{
			$where[] = "c.state=" . $this->_db->Quote($filters['state']);
		}

		if (isset($filters['group_id']) && $filters['group_id'] >= 0)
		{
			$where[] = "c.group_id=" . $this->_db->Quote($filters['group_id']);
		}

		if (isset($filters['index']) && $filters['index'] != '')
		{
			$where[] = "LOWER(LEFT(c.title, 1)) = " . $this->_db->Quote(strtolower($filters['index']));
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(c.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(c.blurb) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(c.alias) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (isset($filters['tag']) && $filters['tag'] != '')
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'tags.php');
			$tagging = new CoursesTags($this->_db);
			$tags = $tagging->_parse_tags($filters['tag']);

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
	 * @param      array $filters Filters to construct query from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) ";
		$query .= (isset($filters['tag']) && $filters['tag'] != '') ? ", COUNT(DISTINCT t.tag) AS uniques " : " ";
		$query .= $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT c.*, (SELECT COUNT(*) FROM #__courses_members AS m WHERE m.student=1 AND m.course_id=c.id) AS students";
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
	 * Get groups for a user
	 *
	 * @param      integer $uid  User ID
	 * @param      string  $type Membership type to return groups for
	 * @return     array
	 */
	public function getUserCourses($uid, $type='all', $limit=null, $start=0)
	{
		if (!$uid)
		{
			$this->setError(JText::_('Missing user ID.'));
			return false;
		}

		$query2 = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title
					FROM $this->_tbl AS c
					JOIN #__courses_members AS m ON m.course_id=c.id
					LEFT JOIN #__courses_offerings AS o ON o.id=m.offering_id
					LEFT JOIN #__courses_offering_sections AS s on s.id=m.section_id
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE m.user_id=" . $this->_db->Quote($uid) . " AND m.student=0 AND r.alias='manager'";

		$query3 = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title
					FROM $this->_tbl AS c
					JOIN #__courses_members AS m ON m.course_id=c.id
					LEFT JOIN #__courses_offerings AS o ON o.id=m.offering_id
					LEFT JOIN #__courses_offering_sections AS s on s.id=m.section_id
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE m.user_id=" . $this->_db->Quote($uid) . " AND m.student=0 AND r.alias='instructor'";

		$query4 = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title
					FROM $this->_tbl AS c
					JOIN #__courses_members AS m ON m.course_id=c.id
					LEFT JOIN #__courses_offerings AS o ON o.id=m.offering_id
					LEFT JOIN #__courses_offering_sections AS s on s.id=m.section_id
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE m.user_id=" . $this->_db->Quote($uid) . " AND m.student=1 AND c.state=1";

		$query5 = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title
					FROM $this->_tbl AS c
					JOIN #__courses_members AS m ON m.course_id=c.id
					LEFT JOIN #__courses_offerings AS o ON o.id=m.offering_id
					LEFT JOIN #__courses_offering_sections AS s on s.id=m.section_id
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE m.user_id=" . $this->_db->Quote($uid) . " AND m.student=0 AND r.alias='ta' AND c.state=1";

		switch ($type)
		{
			case 'all':
				$query = "SELECT c.id, c.alias, c.title, c.blurb, m.enrolled, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, s.alias AS section_alias, s.title AS section_title
					FROM $this->_tbl AS c
					JOIN #__courses_members AS m ON m.course_id=c.id
					LEFT JOIN #__courses_offerings AS o ON o.id=m.offering_id
					LEFT JOIN #__courses_offering_sections AS s on s.id=m.section_id
					LEFT JOIN #__courses_roles AS r ON r.id=m.role_id
					WHERE m.user_id=" . $this->_db->Quote($uid);
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
					WHERE m.user_id=" . $this->_db->Quote($uid) . " AND r.alias=" . $this->_db->Quote($type);
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

