<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

namespace Hubzero\Item\Comment;

/**
 * Table class for comments
 */
class File extends \JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id          = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $comment_id  = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $filename    = NULL;

	/**
	 * Upload path
	 *
	 * @var string
	 */
	var $_uploadDir    = '/sites/comments';

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__item_comment_files', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->filename = trim($this->filename);
		if (!$this->filename)
		{
			$this->setError(\JText::_('Please provide a file name'));
			return false;
		}

		$this->filename = $this->_checkFileName($this->_getUploadDir(), $this->filename);

		$this->comment_id = intval($this->comment_id);
		if (!$this->comment_id)
		{
			$this->setError(\JText::_('Missing comment ID.'));
			return false;
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
	private function _getUploadDir()
	{
		return JPATH_ROOT . DS . ltrim($this->_uploadDir, DS);
	}

	/**
	 * Ensure no conflicting file names
	 *
	 * @param      string $uploadDir Upload path
	 * @param      string $fileName  File name
	 * @return     string
	 */
	private function _checkFileName($uploadDir, $fileName)
	{
		$ext = strrchr($fileName, '.');
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
	 * Build query method
	 *
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS f";

		$where = array();
		if (isset($filters['comment_id']))
		{
			$where[] = "f.`comment_id`=" . $this->_db->Quote($filters['comment_id']);
		}
		if (isset($filters['filename']))
		{
			$where[] = "f.`filename`=" . $this->_db->Quote($filters['filename']);
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "LOWER(f.`filename`) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
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
		$query  = "SELECT COUNT(*)" . $this->_buildquery($filters);

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
		$query  = "SELECT f.*" . $this->_buildquery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'filename';
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
	 * Get Attachment by Comment ID
	 *
	 * @param      integer $comment_id ID of parent comment
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function loadByComment($comment_id=NULL)
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE comment_id=" . $this->_db->Quote((int) $comment_id));
		return $this->_db->loadObject();
	}


	/**
	 * Delete records by comment ID
	 *
	 * @param      integer $comment_id ID of parent comment
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function deleteByComment($comment_id=NULL)
	{
		if ($comment_id === null)
		{
			$this->setError(JText::_('Missing argument: comment ID'));
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE comment_id=" . $this->_db->Quote((int) $comment_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Delete a file
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

		if (!$this->deleteFile($oid))
		{
			return false;
		}

		return parent::delete($oid);
	}

	/**
	 * Delete records by comment ID
	 *
	 * @param      integer $comment_id ID of parent comment
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function deleteFile($filename=NULL)
	{
		if ($filename === null)
		{
			$filename = $this->filename;
		}
		else if (is_numeric($filename) && $filename != $this->id)
		{
			$tbl = new self($this->_db);
			$tbl->load($filename);
			$filename = $tbl->filename;
		}

		if (file_exists($this->_getUploadDir() . DS . $filename))
		{
			jimport('joomla.filesystem.file');
			if (!\JFile::delete($this->_getUploadDir() . DS . $filename))
			{
				$this->setError(\JText::_('Unable to delete file.'));
				return false;
			}
		}
		return true;
	}
}
