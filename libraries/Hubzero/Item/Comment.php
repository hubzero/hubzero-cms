<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2014 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2014 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Hubzero\Item;

use Hubzero\Item\Comment\File;

/**
 * Table class for comments
 */
class Comment extends \JTable
{
	/**
	 * Primary key
	 *
	 * @var integer int(11)
	 */
	var $id            = NULL;

	/**
	 * Object this is a comment for
	 *
	 * @var integer int(11)
	 */
	var $item_id      = NULL;

	/**
	 * Object type (resource, kb, etc)
	 *
	 * @var string varchar(100)
	 */
	var $item_type    = NULL;

	/**
	 * Comment
	 *
	 * @var string text
	 */
	var $content       = NULL;

	/**
	 * When the entry was created
	 *
	 * @var string datetime (0000-00-00 00:00:00)
	 */
	var $created       = NULL;

	/**
	 * Who created this entry
	 *
	 * @var integer int(11)
	 */
	var $created_by    = NULL;

	/**
	 * When the entry was modifed
	 *
	 * @var string datetime (0000-00-00 00:00:00)
	 */
	var $modified      = NULL;

	/**
	 * Who modified this entry
	 *
	 * @var integer int(11)
	 */
	var $modified_by   = NULL;

	/**
	 * Display comment as anonymous
	 *
	 * @var integer tinyint(3)
	 */
	var $anonymous     = NULL;

	/**
	 * Parent comment
	 *
	 * @var integer int(11)
	 */
	var $parent        = NULL;

	/**
	 * Notify the user of replies
	 *
	 * @var integer tinyint(2)
	 */
	var $notify        = NULL;

	/**
	 * Access level (0=public, 1=registered, 2=special, 3=protected, 4=private)
	 *
	 * @var integer tinyint(2)
	 */
	var $access        = NULL;

	/**
	 * Pushed state (0=unpublished, 1=published, 2=trashed)
	 *
	 * @var integer int(2)
	 */
	var $state         = NULL;

	/**
	 * Positive votes (people liked this comment)
	 *
	 * @var integer int(11)
	 */
	var $positive      = NULL;

	/**
	 * Negative votes (people disliked this comment)
	 *
	 * @var integer int(11)
	 */
	var $negative      = NULL;

	/**
	 * Upload path
	 *
	 * @var string
	 */
	var $_uploadDir    = '/sites/comments';

	/**
	 * Allowed Extensions
	 *
	 * @var string
	 */
	var $_extensions    = array('jpg','png','gif','bmp','tiff');

	/**
	 * array
	 *
	 * @var array
	 */
	var $attachmentNames = NULL;

	/**
	 * decimal(2,1)
	 *
	 * @var integer
	 */
	var $rating      = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__item_comments', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->content = trim($this->content);
		if (!$this->content || $this->content == \JText::_('Enter your comments...'))
		{
			$this->setError(\JText::_('Please provide a comment'));
			return false;
		}

		$this->item_id = intval($this->item_id);
		if (!$this->item_id)
		{
			$this->setError(\JText::_('Missing entry ID.'));
			return false;
		}

		$this->item_type = strtolower(preg_replace("/[^a-zA-Z0-9\-]/", '', trim($this->item_type)));
		if (!$this->item_type)
		{
			$this->setError(\JText::_('Missing entry type.'));
			return false;
		}

		if (!$this->created_by)
		{
			$juser = \JFactory::getUser();
			$this->created_by = $juser->get('id');
		}

		if (!$this->id)
		{
			$this->created = \JFactory::getDate()->toSql();
			$this->state = 1;
		}
		else
		{
			$juser = \JFactory::getUser();
			$this->modified_by = $juser->get('id');
			$this->modified = \JFactory::getDate()->toSql();
		}

		// Check file attachment
		$fieldName = 'commentFile';
		if (!empty($_FILES[$fieldName]))
		{
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');

			//any errors the server registered on uploading
			$fileError = $_FILES[$fieldName]['error'];
			if ($fileError > 0)
			{
				switch ($fileError)
				{
					case 1:
						$this->setError(\JText::_('FILE TO LARGE THAN PHP INI ALLOWS'));
						return false;
					break;

					case 2:
						$this->setError(\JText::_('FILE TO LARGE THAN HTML FORM ALLOWS'));
						return false;
					break;

					case 3:
						$this->setError(\JText::_('ERROR PARTIAL UPLOAD'));
						return false;
					break;

					case 4:
						return true;
					break;
				}
			}

			//check for filesize
			$fileSize = $_FILES[$fieldName]['size'];
			if ($fileSize > 2000000)
			{
				$this->setError(\JText::_('FILE BIGGER THAN 2MB'));
				return false;
			}

			//check the file extension is ok
			$fileName = $_FILES[$fieldName]['name'];
			$uploadedFileNameParts = explode('.', $fileName);
			$uploadedFileExtension = array_pop($uploadedFileNameParts);

			$validFileExts = $this->getAllowedExtensions();

			//assume the extension is false until we know its ok
			$extOk = false;

			//go through every ok extension, if the ok extension matches the file extension (case insensitive)
			//then the file extension is ok
			foreach ($validFileExts as $key => $value)
			{
				if (preg_match("/$value/i", $uploadedFileExtension) )
				{
					$extOk = true;
				}
			}

			if ($extOk == false)
			{
				$this->setError(\JText::_('Invalid Extension. Only these file types allowed: ' . implode(', ', $this->getAllowedExtensions())));
				return false;
			}

			//the name of the file in PHP's temp directory that we are going to move to our folder
			$fileTemp = $_FILES[$fieldName]['tmp_name'];

			//lose any special characters in the filename
			$fileName = preg_replace("/[^A-Za-z0-9.]/i", "-", $fileName);

			//always use constants when making file paths, to avoid the possibilty of remote file inclusion
			$uploadDir = $this->getUploadDir();

			// check if file exists -- rename if needed
			$fileName = $this->checkFileName($uploadDir, $fileName);

			$uploadPath = $uploadDir . DS . $fileName;

			if (!\JFile::upload($fileTemp, $uploadPath))
			{
				$this->setError(\JText::_('ERROR MOVING FILE'));
				return false;
			}

			$this->attachmentNames = array($fileName);
		}

		return true;
	}

	/**
	 * Set the upload path
	 *
	 * @param      string $path PAth to set to
	 * @return     void
	 */
	public function setUploadDir($path)
	{
		$path = trim($path);

		jimport('joomla.filesystem.path');
		$path = \JPath::clean($path);
		$path = str_replace(' ', '_', $path);

		$this->_uploadDir = ($path) ? $path : $this->_uploadDir;
	}

	/**
	 * Get the upload path
	 *
	 * @return     string
	 */
	private function getUploadDir()
	{
		return JPATH_ROOT . DS . ltrim($this->_uploadDir, DS);
	}

	/**
	 * Get allowed file extensions
	 *
	 * @return     array
	 */
	public function getAllowedExtensions()
	{
		return $this->_extensions;
	}

	/**
	 * Set allowed file extensions
	 *
	 * @param      $exts    Array of file extensions
	 * @return     void
	 */
	public function setAllowedExtensions($exts = array())
	{
		if (is_array($exts) && !empty($exts))
		{
			$this->_extensions = $exts;
		}
	}

	/**
	 * Check File Name
	 *
	 * @param      $uploadDir    Upload Directory
	 * @param      $fileName     File Name
	 * @return     void
	 */
	private function checkFileName($uploadDir, $fileName)
	{
		$ext    = strrchr($fileName, '.');
		$prefix = substr($fileName, 0, -strlen($ext));

		// rename file if exists
		$i = 1;
		while (is_file($uploadDir . DS . $fileName))
		{
			$fileName = $prefix . ++$i . $ext;
		}
		return $fileName;
	}

	/**
	 * Store attachments
	 *
	 * @return     void
	 */
	public function store($updateNulls = false)
	{
		$result = parent::store($updateNulls);

		if (!$result)
		{
			return false;
		}

		if ($this->attachmentNames && count($this->attachmentNames) > 0)
		{
			// save the attachments
			foreach ($this->attachmentNames as $nm)
			{
				// delete old attachment
				// find old file and remove it from file system
				$file = new File($this->_db);
				$file->loadByComment($this->id);
				if ($file->id)
				{
					if (!$file->deleteFile())
					{
						$this->setError($file->getError());
						continue;
					}
				}
				$file->filename = $nm;
				$file->comment_id = $this->id;
				if (!$file->store())
				{
					$this->setError($file->getError());
					continue;
				}
			}
		}

		return true;
	}

	/**
	 * Get all the comments on an entry
	 *
	 * @param      string  $item_type Type of entry these comments are attached to
	 * @param      integer $item_id   ID of entry these comments are attached to
	 * @param      integer $parent     ID of parent comment
	 * @return     mixed False if error otherwise array of records
	 */
	public function getComments($item_type=NULL, $item_id=0, $parent=0, $limit=25, $start=0)
	{
		if (!$item_type)
		{
			$item_type = $this->item_type;
		}
		if (!$item_id)
		{
			$item_id = $this->item_id;
		}
		if (!$parent)
		{
			$parent = 0;
		}

		if (!$item_type || !$item_id)
		{
			$this->setError(\JText::_('Missing parameter(s). item_type:' . $item_type . ', item_id:' . $item_id));
			return false;
		}

		$juser = \JFactory::getUser();

		if (!$juser->get('guest'))
		{
			$sql  = "SELECT c.*, u.name, v.vote, (c.positive - c.negative) AS votes, f.filename FROM $this->_tbl AS c ";
			$sql .= "LEFT JOIN #__item_comment_files AS f ON f.comment_id=c.id ";
			$sql .= "LEFT JOIN #__users AS u ON u.id=c.created_by ";
			$sql .= "LEFT JOIN #__item_votes AS v ON v.item_id=c.id AND v.created_by=" . $this->_db->Quote($juser->get('id')) . " AND v.item_type='comment' ";
		}
		else
		{
			$sql  = "SELECT c.*, u.name, NULL as vote, (c.positive - c.negative) AS votes, f.filename FROM $this->_tbl AS c ";
			$sql .= "LEFT JOIN #__item_comment_files AS f ON f.comment_id=c.id ";
			$sql .= "LEFT JOIN #__users AS u ON u.id=c.created_by ";
		}
		$sql .= "WHERE c.item_type=" . $this->_db->Quote($item_type) . " AND c.item_id=" . $this->_db->Quote($item_id) . " AND c.parent=" . $this->_db->Quote($parent) . " AND c.state IN (1, 3) ORDER BY created ASC LIMIT $start,$limit";

		$this->_db->setQuery($sql);

		$rows = $this->_db->loadObjectList();
		if ($rows && count($rows) > 0)
		{
			foreach ($rows as $k => $row)
			{
				$rows[$k]->replies = $this->getComments($item_type, $item_id, $row->id, $limit, $start);
			}
		}
		return $rows;
	}

	/**
	 * Delete a comment and any chldren
	 *
	 * @param      integer $id     ID of parent comment
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function delete($oid=null)
	{
		if (!$oid)
		{
			$oid = $this->id;
		}

		if (!$this->deleteDescendants($oid, 2))
		{
			return false;
		}

		return parent::delete($oid);
	}

	/**
	 * Delete descendants of a comment
	 *
	 * @param      integer $id     ID of parent comment
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function deleteDescendants($id=NULL)
	{
		if (is_array($id))
		{
			$id = array_map('intval', $id);
			$id = implode(',', $id);
		}
		else
		{
			$id = intval($id);
		}

		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE parent IN ($id)");
		$rows = $this->_db->loadResultArray();
		if ($rows && count($rows) > 0)
		{
			$state = intval($state);
			$rows = array_map('intval', $rows);
			$ids = implode(',', $rows);

			$this->_db->setQuery("DELETE FROM $this->_tbl WHERE id IN ($ids)");
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return $this->deleteDescendants($rows, $state);
		}
		return true;
	}

	/**
	 * Set the state of a comment and all descendants
	 *
	 * @param      integer $id     ID of parent comment
	 * @param      integer $state  State to set (0=unpublished, 1=published, 2=trashed)
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function setState($oid=null, $state=0)
	{
		if (!$oid)
		{
			$oid = $this->id;
		}
		$oid = intval($oid);

		if (!$this->setDescendantState($oid, $state))
		{
			return false;
		}

		$this->_db->setQuery("UPDATE $this->_tbl SET state=" . $this->_db->Quote($state) . " WHERE id=" . $this->_db->Quote($oid));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Set the state of descendants of a comment
	 *
	 * @param      integer $id     ID of parent comment
	 * @param      integer $state  State to set (0=unpublished, 1=published, 2=trashed)
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function setDescendantState($id=NULL, $state=0)
	{
		if (is_array($id))
		{
			$id = array_map('intval', $id);
			$id = implode(',', $id);
		}
		else
		{
			$id = intval($id);
		}

		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE parent IN ($id)");
		$rows = $this->_db->loadResultArray();
		if ($rows && count($rows) > 0)
		{
			$state = intval($state);
			$rows = array_map('intval', $rows);
			$id = implode(',', $rows);

			$this->_db->setQuery("UPDATE $this->_tbl SET state=" . $this->_db->Quote($state) . " WHERE parent IN ($id)");
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return $this->setDescendantState($rows, $state);
		}
		return true;
	}

	/**
	 * Short description for 'buildQuery'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery($filters=array())
	{
		$query  = "FROM $this->_tbl AS c";
		$query .= " LEFT JOIN #__viewlevels AS a ON c.access=a.id";

		$where = array();

		if (isset($filters['state']))
		{
			if (is_array($filters['state']))
			{
				$filters['state'] = array_map('intval', $filters['state']);
				$where[] = "c.state IN (" . implode(',', $filters['state']) . ")";
			}
			else if ($filters['state'] >= 0)
			{
				$where[] = "c.state=" . $this->_db->Quote(intval($filters['state']));
			}
		}

		if (isset($filters['item_type']) && $filters['item_type'] >= 0)
		{
			$where[] = "c.item_type=" . $this->_db->Quote($filters['item_type']);
		}

		if (isset($filters['item_id']) && $filters['item_id'] >= 0)
		{
			$where[] = "c.item_id=" . $this->_db->Quote($filters['item_id']);
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "LOWER(c.content) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
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
	 * @param      array $filters Filters to build query off of
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an array of records
	 *
	 * @param      array $filters Filters to build query off of
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT c.*";
		$query .= ", a.title AS access_level";
		$query .= " " . $this->buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'created';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'DESC';
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
	 * Build query method
	 *
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS r LEFT JOIN #__users AS u ON u.id=r.created_by";
		$query .= " LEFT JOIN #__viewlevels AS a ON r.access=a.id";

		$where = array();
		if (isset($filters['item_id']))
		{
			$where[] = "r.`item_id`=" . $this->_db->Quote($filters['item_id']);
		}
		if (isset($filters['item_type']))
		{
			$where[] = "r.`item_type`=" . $this->_db->Quote($filters['item_type']);
		}
		if (isset($filters['state']))
		{
			if (is_array($filters['state']))
			{
				$filters['state'] = array_map('intval', $filters['state']);
				$where[] = "r.`state` IN (" . implode(',', $filters['state']) . ")";
			}
			else
			{
				$where[] = "r.`state`=" . $this->_db->Quote($filters['state']);
			}
		}
		if (isset($filters['access']))
		{
			$where[] = "r.`access`=" . $this->_db->Quote($filters['access']);
		}
		if (isset($filters['parent']))
		{
			$where[] = "r.`parent`=" . $this->_db->Quote($filters['parent']);
		}
		if (isset($filters['created_by']))
		{
			$where[] = "r.`created_by`=" . $this->_db->Quote($filters['created_by']);
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "LOWER(r.`content`) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get an object list of course units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(*) ";
		$query .= $this->_buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of course units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function find($filters=array())
	{
		$query  = "SELECT r.*, u.name";
		$query .= ", a.title AS access_level";
		$query .= $this->_buildquery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'created';
		}
		if (!isset($filters['sort_Dir']) || !in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get an object list of course units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function ratings($filters=array())
	{
		$query  = "SELECT r.rating";
		$query .= $this->_buildquery($filters);

		/*if (isset($filters['sort']) && $filters['sort'])
		{
			if (!isset($filters['sort_Dir']) || !in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
			{
				$filters['sort_Dir'] = 'ASC';
			}
			$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		}
		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}*/

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get an object list of course units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function hasRated($item_id, $item_type, $created_by)
	{
		if (!$item_id || !$item_type || !$created_by)
		{
			return false;
		}

		$filters = array(
			'state'      => 1,
			'created_by' => $created_by,
			'parent'     => 0,
			'item_id'    => $item_id,
			'item_type'  => $item_type
		);

		$query  = "SELECT COUNT(*) ";
		$query .= $this->_buildquery($filters);

		$this->_db->setQuery($query);
		if (($total = $this->_db->loadResult()))
		{
			return true;
		}
		return false;
	}
}
