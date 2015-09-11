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

namespace Components\Tags\Tables;

use stdClass;
use User;
use Date;
use Lang;

/**
 * Table class for substituting tags for another tag
 */
class Substitute extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tags_substitute', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  True if data is valid
	 */
	public function check()
	{
		if ($this->tag && !$this->raw_tag)
		{
			$this->raw_tag = $this->tag;
		}
		$this->raw_tag = trim($this->raw_tag);
		if (!$this->raw_tag)
		{
			$this->setError(Lang::txt('You must enter a tag.'));
			return false;
		}

		$this->tag = $this->normalize($this->raw_tag);

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
		}

		if (!$this->tag_id)
		{
			$this->setError(Lang::txt('You must enter the ID of the tag to substitute this tag for.'));
			return false;
		}

		return true;
	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * @param   boolean      $updateNulls  If false, null object variables are not updated
	 * @return  null|string  null if successful otherwise returns and error message
	 */
	public function store($updateNulls=false)
	{
		$k = $this->_tbl_key;
		if ($this->$k)
		{
			$action = 'substitute_edited';
		}
		else
		{
			$action = 'substitute_created';
		}

		$result = parent::store($updateNulls);
		if ($result)
		{
			require_once(__DIR__ . DS . 'log.php');

			$data = new stdClass;
			$data->tag_id  = $this->tag_id;
			$data->tag     = $this->tag;
			$data->raw_tag = $this->raw_tag;
			$data->id      = $this->$k;

			$log = new Log($this->_db);
			$log->log($this->tag_id, $action, json_encode($data));
		}
		return $result;
	}

	/**
	 * Default delete method
	 *
	 * @param   integer  $oid  Entry ID
	 * @return  boolean  True if successful otherwise returns and error message
	 */
	public function delete($oid=null)
	{
		$result = parent::delete($oid);
		if ($result)
		{
			require_once(__DIR__ . DS . 'log.php');

			$log = new Log($this->_db);
			$log->log($oid, 'substitute_deleted');
		}
		return $result;
	}

	/**
	 * Normalize a raw tag
	 * Strips all non-alphanumeric characters
	 *
	 * @param   string  $tag  Raw tag
	 * @return  string
	 */
	public function normalize($tag)
	{
		return strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $tag));
	}

	/**
	 * Remove all tag references for a given object
	 *
	 * @param   integer  $tag_id  Tag ID
	 * @param   array    $data    List of specific tags to remove (removes all if empty)
	 * @return  boolean  True if records removed
	 */
	public function removeForTag($tag_id=null, $data=array())
	{
		$tag_id = $tag_id ?: $this->tag_id;

		if (!$tag_id)
		{
			$this->setError(Lang::txt('Missing argument.'));
			return false;
		}

		$sql = "DELETE FROM $this->_tbl WHERE tag_id=" . $this->_db->quote($tag_id);
		if (count($data) > 0)
		{
			$sql .= " AND tag IN ('" . implode("','", $data) . "')";
		}

		$this->_db->setQuery($sql);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		require_once(__DIR__ . DS . 'log.php');

		$d = new stdClass;
		$d->tags = $data;

		$log = new Log($this->_db);
		$log->log($tag_id, 'substitute_deleted', json_encode($d));

		return true;
	}

	/**
	 * Get a record count for a tag ID
	 *
	 * @param   integer  $tag_id  Tag ID
	 * @return  mixed    Integer if successful, false if not
	 */
	public function getCount($tag_id=null)
	{
		$tag_id = $tag_id ?: $this->tag_id;

		if (!$tag_id)
		{
			$this->setError(Lang::txt('Missing argument.'));
			return false;
		}

		$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE tag_id=" . $this->_db->quote($tag_id));
		return $this->_db->loadResult();
	}

	/**
	 * Get all the tags on an object
	 *
	 * @param   integer  $tag_id  Tag ID
	 * @param   integer  $offset  Record offset
	 * @param   integer  $limit   Number of records to return (returns all if less than 1)
	 * @return  mixed    Array if successful, false if not
	 */
	public function getRecords($tag_id=null, $offset=0, $limit=0)
	{
		$tag_id = $tag_id ?: $this->tag_id;

		if (!$tag_id)
		{
			$this->setError(Lang::txt('Missing argument.'));
			return false;
		}

		$sql = "SELECT * FROM $this->_tbl WHERE tag_id=" . $this->_db->quote($tag_id) . " ORDER BY raw_tag ASC";
		if ($limit > 0)
		{
			$sql .= " LIMIT " . intval($offset) . ", " . intval($limit);
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList('tag');
	}

	/**
	 * Get all the tags on an object
	 *
	 * @param   integer  $tag_id  Tag ID
	 * @param   integer  $offset  Record offset
	 * @param   integer  $limit   Number of records to return (returns all if less than 1)
	 * @return  string
	 */
	public function getRecordString($tag_id=null, $offset=0, $limit=0)
	{
		$subs = array();
		if ($items = $this->getRecords($tag_id, $offset, $limit))
		{
			foreach ($items as $k => $item)
			{
				$subs[] = $item['raw_tag'];
			}
		}
		return implode(', ', $subs);
	}

	/**
	 * Move all references to one tag to another tag
	 *
	 * @param   integer  $oldtagid  ID of tag to be moved
	 * @param   integer  $newtagid  ID of tag to move to
	 * @return  boolean  True if records changed
	 */
	public function moveSubstitutes($oldtagid=null, $newtagid=null)
	{
		$oldtagid = $oldtagid ?: $this->tag_id;

		if (!$oldtagid || !$newtagid)
		{
			return false;
		}

		$this->_db->setQuery("SELECT tag FROM $this->_tbl WHERE tag_id=" . $this->_db->quote($oldtagid));
		$items = $this->_db->loadColumn();

		$this->_db->setQuery("UPDATE $this->_tbl SET tag_id=" . $this->_db->quote($newtagid) . " WHERE tag_id=" . $this->_db->quote($oldtagid));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		else
		{
			require_once(__DIR__ . DS . 'log.php');

			$data = new stdClass;
			$data->old_id  = $oldtagid;
			$data->new_id  = $newtagid;
			$data->entries = $items;

			$log = new Log($this->_db);
			$log->log($newtagid, 'substitutes_moved', json_encode($data));
		}
		return $this->cleanUp($newtagid);
	}

	/**
	 * Clean up duplicate references
	 *
	 * @param   integer  $tag_id  ID of tag to clean up
	 * @return  boolean  True on success, false if errors
	 */
	public function cleanUp($tag_id=null)
	{
		$tag_id = $tag_id ?: $this->tag_id;

		if (!$tag_id)
		{
			$this->setError(Lang::txt('Missing argument.'));
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE tag_id=" . $this->_db->quote($tag_id) . " ORDER BY id ASC");

		if ($subs = $this->_db->loadObjectList())
		{
			$tags = array();
			foreach ($subs as $sub)
			{
				if (!isset($tags[$sub->tag]))
				{
					// Item isn't in collection yet, so add it
					$tags[$sub->tag] = $sub->id;
				}
				else
				{
					// Item tag *is* in collection.
					if ($tags[$sub->tag] == $sub->id)
					{
						// Really this shouldn't happen
						continue;
					}
					else
					{
						// Duplcate tag with a different ID!
						// We don't need duplicates.
						$this->delete($sub->id);
					}
				}
			}
		}

		return true;
	}
}

