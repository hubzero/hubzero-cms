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
 * Table class for substituting tags for another tag
 */
class TagsTableSubstitute extends JTable
{
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $id         = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $tag        = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $raw_tag    = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $tag_id     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created    = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tags_substitute', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     True if data is valid
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
			$this->setError(JText::_('You must enter a tag.'));
			return false;
		}

		$this->tag = $this->normalize($this->raw_tag);

		if (!$this->id) 
		{
			$juser =& JFactory::getUser();
			$this->created = date('Y-m-d H:i:s', time());  // use gmdate() ?
			$this->created_by = $juser->get('id');
		}

		if (!$this->tag_id)
		{
			$this->setError(JText::_('You must enter the ID of the tag to substitute this tag for.'));
			return false;
		}

		return true;
	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * @param     boolean $updateNulls If false, null object variables are not updated
	 * @return    null|string null if successful otherwise returns and error message
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
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'log.php');

			$data = new stdClass;
			$data->tag_id = $this->tag_id;
			$data->tag = $this->tag;
			$data->raw_tag = $this->raw_tag;
			$data->id = $this->$k;

			$log = new TagsTableLog($this->_db);
			$log->log($this->tag_id, $action, json_encode($data));
		}
		return $result;
	}

	/**
	 * Default delete method
	 *
	 * @param      integer $oid Entry ID
	 * @return     true if successful otherwise returns and error message
	 */
	public function delete($oid=null)
	{
		$result = parent::delete($oid);
		if ($result)
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'log.php');
			$log = new TagsTableLog($this->_db);
			$log->log($oid, 'substitute_deleted');
		}
		return $result;
	}

	/**
	 * Normalize a raw tag
	 * Strips all non-alphanumeric characters
	 * 
	 * @param      string $tag Raw tag
	 * @return     string
	 */
	public function normalize($tag)
	{
		return strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $tag));
	}

	/**
	 * Remove all tag references for a given object
	 * 
	 * @param      integer $tag_id Tag ID
	 * @param      array   $data   List of specific tags to remove (removes all if empty)
	 * @return     boolean True if records removed
	 */
	public function removeForTag($tag_id=null, $data=array())
	{
		if (!$tag_id) 
		{
			$tag_id = $this->tag_id;
		}
		if (!$tag_id) 
		{
			$this->setError(JText::_('Missing argument.'));
			return false;
		}

		$sql = "DELETE FROM $this->_tbl WHERE tag_id=" . $this->_db->Quote($tag_id);
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

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'log.php');

		$d = new stdClass;
		$d->tags = $data;

		$log = new TagsTableLog($this->_db);
		$log->log($tag_id, 'substitute_deleted', json_encode($d));

		return true;
	}

	/**
	 * Get a record count for a tag ID
	 * 
	 * @param      integer $tag_id Tag ID
	 * @return     mixed Integer if successful, false if not
	 */
	public function getCount($tag_id=null)
	{
		if (!$tag_id) 
		{
			$tag_id = $this->tag_id;
		}
		if (!$tag_id) 
		{
			$this->setError(JText::_('Missing argument.'));
			return false;
		}

		$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE tag_id=" . $this->_db->Quote($tag_id));
		return $this->_db->loadResult();
	}

	/**
	 * Get all the tags on an object
	 * 
	 * @param      integer $tag_id Tag ID
	 * @param      integer $offset Record offset
	 * @param      integer $limit  Number of records to return (returns all if less than 1)
	 * @return     mixed Array if successful, false if not
	 */
	public function getRecords($tag_id=null, $offset=0, $limit=0)
	{
		if (!$tag_id) 
		{
			$tag_id = $this->tag_id;
		}
		if (!$tag_id) 
		{
			$this->setError(JText::_('Missing argument.'));
			return false;
		}

		$sql = "SELECT * FROM $this->_tbl WHERE tag_id=" . $this->_db->Quote($tag_id) . " ORDER BY raw_tag ASC";
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
	 * @param      integer $tag_id Tag ID
	 * @param      integer $offset Record offset
	 * @param      integer $limit  Number of records to return (returns all if less than 1)
	 * @return     string
	 */
	public function getRecordString($tag_id=null, $offset=0, $limit=0)
	{
		$items = $this->getRecords($tag_id, $offset, $limit);

		$subs = array();
		if ($items)
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
	 * @param      integer $oldtagid ID of tag to be moved
	 * @param      integer $newtagid ID of tag to move to
	 * @return     boolean True if records changed
	 */
	public function moveSubstitutes($oldtagid=null, $newtagid=null)
	{
		if (!$oldtagid) 
		{
			$oldtagid = $this->tag_id;
		}
		if (!$oldtagid) 
		{
			return false;
		}
		if (!$newtagid) 
		{
			return false;
		}

		$this->_db->setQuery("SELECT tag FROM $this->_tbl WHERE tag_id=" . $this->_db->Quote($oldtagid));
		$items = $this->_db->loadResultArray();

		$this->_db->setQuery("UPDATE $this->_tbl SET tag_id=" . $this->_db->Quote($newtagid) . " WHERE tag_id=" . $this->_db->Quote($oldtagid));
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		else 
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'log.php');

			$data = new stdClass;
			$data->old_id = $oldtagid;
			$data->new_id = $newtagid;
			$data->entries = $items;

			$log = new TagsTableLog($this->_db);
			$log->log($newtagid, 'substitutes_moved', json_encode($data));
		}
		return $this->cleanUp($newtagid);
	}

	/**
	 * Clean up duplicate references
	 * 
	 * @param      integer $tag_id ID of tag to clean up
	 * @return     boolean True on success, false if errors
	 */
	public function cleanUp($tag_id=null)
	{
		if (!$tag_id) 
		{
			$tag_id = $this->tag_id;
		}
		if (!$tag_id) 
		{
			$this->setError(JText::_('Missing argument.'));
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE tag_id=" . $this->_db->Quote($tag_id) . " ORDER BY id ASC");

		if (($subs = $this->_db->loadObjectList()))
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

