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

namespace Components\Wishlist\Tables;

use Hubzero\Utility\Sanitize;
use Config;
use Lang;
use Date;
use User;

/**
 * Table class for wishlist
 */
class Wishlist extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wishlist', 'id', $db);
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
			$this->setError(Lang::txt('Missing title for the wish list'));
			return false;
		}

		$this->description  = rtrim(stripslashes($this->description));
		$this->description  = Sanitize::clean($this->description);
		$this->description  = nl2br($this->description);

		return true;
	}

	/**
	 * Load an entry from the database and bind to $this
	 *
	 * @param   string   $oid    Entry alias
	 * @param   string   $scope  Entry scope [site, group, member]
	 * @return  boolean  True if data was retrieved and loaded
	 */
	public function loadByCategory($referenceid=null, $category=null)
	{
		$fields = array(
			'referenceid' => (int) $referenceid,
			'category'    => (string) $category
		);

		return parent::load($fields);
	}

	/**
	 * Get the ID of an entry
	 *
	 * @param   integer  $rid  Reference ID
	 * @param   string   $cat  Category
	 * @return  mixed    False if errors, integer on success
	 */
	public function get_wishlistID($rid=0, $cat='resource')
	{
		if ($rid === null)
		{
			$rid = $this->referenceid;
		}
		if ($rid === null)
		{
			return false;
		}

		// get individuals
		$sql = "SELECT id"
			. " FROM $this->_tbl "
			. " WHERE referenceid=" . $this->_db->quote($rid) . " AND category=" . $this->_db->quote($cat) . " ORDER BY id DESC LIMIT 1";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Create an entry
	 *
	 * @param   string   $category     Category
	 * @param   integer  $refid        Reference ID
	 * @param   integer  $public       Public/private list
	 * @param   string   $title        Entry title
	 * @param   string   $description  Entry description
	 * @return  mixed    False if errors, integer on success
	 */
	public function createlist($category='resource', $refid, $public=1, $title='', $description='')
	{
		if ($refid === null)
		{
			return false;
		}

		$sitename = Config::get('sitename');

		$this->created     = Date::toSql();
		$this->category    = $category;
		$this->created_by  = User::get('id');
		$this->referenceid = $refid;
		$this->description = $description;
		$this->public      = $public;

		switch ($category)
		{
			case 'general':
				$this->title = $title ? $title : $sitename;

				if (!$this->store())
				{
					$this->_error = $this->getError();
					return false;
				}
				// Checkin wishlist
				$this->checkin();

				return $this->id;
			break;

			case 'resource':
				// resources can only have one list
				if (!$this->get_wishlist('', $refid, 'resource'))
				{
					$this->title = $title ? $title : 'Resource #' . $refid;

					if (!$this->store())
					{
						$this->_error = $this->getError();
						return false;
					}
					// Checkin wishlist
					$this->checkin();

					return $this->id;
				}
				else
				{
					return $this->get_wishlistID($refid); // return existing id
				}
			break;

			case 'group':
				$this->title = $title ? $title : 'Group #' . $refid;
				if (!$this->store())
				{
					$this->_error = $this->getError();
					return false;
				}
				// Checkin wishlist
				$this->checkin();

				return $this->id;
			break;

			case 'publication':
				$this->title = $title ? $title : 'Publication #' . $refid;
				if (!$this->store())
				{
					$this->_error = $this->getError();
					return false;
				}
				// Checkin wishlist
				$this->checkin();

				return $this->id;
			break;

			case 'user':
				$this->title = $title;
				if (!$this->store())
				{
					$this->_error = $this->getError();
					return false;
				}
				// Checkin wishlist
				$this->checkin();

				return $this->id;
			break;
		}

		return 0;
	}

	/**
	 * Get the title for an entry
	 *
	 * @param   integer  $id  Entry ID
	 * @return  mixed    False if error, string on success
	 */
	public function getTitle($id)
	{
		if ($id === null)
		{
			return false;
		}
		$sql = "SELECT w.title FROM $this->_tbl AS w WHERE w.id=" . $this->_db->quote($id);

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Check if an entry is the primary wislist
	 *
	 * @param   integer  $id  Entry ID
	 * @return  boolean  True if primary
	 */
	public function is_primary($id)
	{
		if ($id === null)
		{
			return false;
		}
		$sql = "SELECT w.* FROM $this->_tbl AS w WHERE w.id=" . $this->_db->quote($id) . " AND w.referenceid=1 AND w.category='general'";

		$this->_db->setQuery($sql);
		if ($this->_db->loadResult())
		{
			return true;
		}
		return false;
	}

	/**
	 * Load a record
	 *
	 * @param   integer  $id           Entry ID
	 * @param   integer  $refid        Reference ID
	 * @param   string   $cat          Category
	 * @param   integer  $primary      Primary list?
	 * @param   integer  $getversions  Return reference versions?
	 * @return  mixed    False if error, object on success
	 */
	public function get_wishlist($id='', $refid=0, $cat='', $primary=0, $getversions=0)
	{
		if ($id===null && $refid===0 && $cat===null)
		{
			return false;
		}
		if ($id && !intval($id))
		{
			return false;
		}
		if ($refid && !intval($refid))
		{
			return false;
		}

		$sql = "SELECT w.* FROM $this->_tbl AS w";

		if ($id)
		{
			$sql .= " WHERE w.id=" . $this->_db->quote($id);
		}
		else if ($refid && $cat)
		{
			$sql .= " WHERE w.referenceid=" . $this->_db->quote($refid) . " AND w.category=" . $this->_db->quote($cat);
		}
		else if ($primary)
		{
			$sql .= " WHERE w.referenceid=1 AND w.category='general'";
		}

		$this->_db->setQuery($sql);
		$res = $this->_db->loadObjectList();
		$wishlist = ($res) ? $res[0] : array();

		// get parent
		if (count($wishlist) > 0 && $wishlist->category=='resource')
		{
			$wishlist->resource = $this->get_wishlist_parent($wishlist->referenceid, $wishlist->category);
			// Currenty for tools only
			if ($getversions && $wishlist->resource && isset($wishlist->resource->type) && $wishlist->resource->type==7)
			{
				$wishlist->resource->versions = $this->get_parent_versions($wishlist->referenceid, $wishlist->resource->type);
			}
		}

		return $wishlist;
	}

	/**
	 * Get the parent tool version
	 *
	 * @param   integer  $rid   Resource ID
	 * @param   integer  $type  Resource type
	 * @return  array
	 */
	public function get_parent_versions($rid, $type)
	{
		$versions = array();
		// currently for tools only
		if ($type == 7)
		{
			$query  = "SELECT v.id FROM `#__tool_version` as v JOIN `#__resources` as r ON r.alias = v.toolname WHERE r.id=" . $this->_db->quote($rid);
			$query .= " AND v.state=3 ";
			$query .= " OR v.state!=3 ORDER BY state DESC, revision DESC LIMIT 3";
			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();
			$versions = $result ? $result : array();
		}

		return $versions;
	}

	/**
	 * Get the parent resource for a wishlist
	 *
	 * @param   integer  $refid  Resource ID
	 * @param   string   $cat    Resource type
	 * @return  array
	 */
	public function get_wishlist_parent($refid, $cat='resource')
	{
		$resource = array();
		if ($cat == 'resource')
		{
			$sql = "SELECT r.title, r.type, r.alias, r.introtext, t.type as typetitle
				FROM `#__resources` AS r
				LEFT JOIN `#__resource_types` AS t ON t.id=r.type
				WHERE r.id=" . $this->_db->quote($refid);
			$this->_db->setQuery($sql);
			$res  = $this->_db->loadObjectList();
			$resource = ($res) ? $res[0]: array();
		}

		return $resource;
	}

	/**
	 * Get authors of a resource
	 *
	 * @param   integer  $refid  Resource ID
	 * @return  array
	 */
	public function getCons($refid)
	{
		$sql = "SELECT n.id
			FROM `#__users` AS n
			JOIN `#__author_assoc` AS a ON n.id=a.authorid
			WHERE a.subtable = 'resources'
			AND a.subid=" . $this->_db->quote($refid);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a tool's development group
	 *
	 * @param   integer  $refid   Tool ID
	 * @param   array    $groups  ?
	 * @return  string
	 */
	public function getToolDevGroup($refid, $groups = array())
	{
		$query = "SELECT g.cn FROM `#__tool_groups` AS g
				JOIN `#__xgroups` AS xg ON g.cn=xg.cn
				JOIN `#__tool` AS t ON g.toolid=t.id
				JOIN `#__resources` as r ON r.alias = t.toolname
				WHERE r.id = " . $this->_db->quote($refid) . " AND g.role=1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	public function buildQuery($filters=array())
	{
		$sql = "FROM $this->_tbl AS m LEFT JOIN #__users AS u ON m.created_by=u.id ";

		$w = array();
		if (isset($filters['category']) && $filters['category'])
		{
			$w[] = "m.category=" . $this->_db->quote($filters['category']);
		}
		if (isset($filters['referenceid']) && $filters['referenceid'])
		{
			$w[] = "m.referenceid=" . $this->_db->quote($filters['referenceid']);
		}
		if (isset($filters['state']))
		{
			$w[] = "m.state=" . $this->_db->quote($filters['state']);
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$w[] = "m.title LIKE " . $this->_db->quote('%' . $filters['search'] . '%');
		}

		$sql .= (count($w) > 0) ? "WHERE " : "";
		$sql .= implode(" AND ", $w);

		return $sql;
	}

	/**
	 * Get a record count
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = '';
		$query = "SELECT count(*) " . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getRecords($filters=array())
	{
		$query = "SELECT m.*, u.name, (SELECT COUNT(*) FROM `#__wishlist_item` AS w WHERE w.wishlist = m.id) AS wishes " . $this->buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		if (isset($filters['limit']) && $filters['limit'] != '')
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
