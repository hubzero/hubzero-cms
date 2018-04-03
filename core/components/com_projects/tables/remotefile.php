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

namespace Components\Projects\Tables;

use Hubzero\Database\Table;
use Lang;
use Date;

/**
 * Table class for project shared files
 */
class RemoteFile extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_remote_files', 'id', $db);
	}

	/**
	 * Get remote connections
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $service
	 * @param   string   $local_dirpath
	 * @param   string   $converted
	 * @return  array
	 */
	public function getRemoteConnections($projectid = null, $service = '', $local_dirpath = '', $converted = 'na')
	{
		if (!$projectid)
		{
			return false;
		}

		$locals = array('paths' => array(), 'ids' => array());

		$query  = "SELECT local_path, local_dirpath, remote_id, synced, type,
					remote_editing as converted, remote_parent, remote_format,
					remote_modified, paired, original_path FROM $this->_tbl";
		$query .= " WHERE projectid=" . $this->_db->quote($projectid);
		$query .= " AND service=" . $this->_db->quote($service);
		$query .= $local_dirpath ? " AND local_dirpath=" . $this->_db->quote($local_dirpath) : '';
		if ($converted != 'na')
		{
			$converted = $converted == 1 ? 1 : 0;
			$query .= " AND remote_editing = " . $this->_db->quote($converted);
		}

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();

		if ($results)
		{
			foreach ($results as $result)
			{
				$item = array(
					'type'      => $result->type,
					'remote_id' => $result->remote_id,
					'path'      => $result->local_path,
					'dirpath'   => $result->local_dirpath,
					'format'    => $result->remote_format,
					'converted' => $result->converted,
					'rParent'   => $result->remote_parent,
					'synced'    => $result->synced,
					'original'  => $result->original_path,
					'modified'  => $result->remote_modified
				);

				$locals['paths'][$result->local_path] = $item;
				$locals['ids'][$result->remote_id] = $item;
			}
		}

		return $locals;
	}

	/**
	 * Get remote connections count
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $service
	 * @param   string   $converted
	 * @return  array
	 */
	public function getFileCount($projectid = null, $service = '', $converted = 'na')
	{
		if (!$projectid)
		{
			return false;
		}

		$query  = "SELECT COUNT(*) FROM $this->_tbl";
		$query .= " WHERE projectid=" . $this->_db->quote($projectid);
		$query .= $service ? " AND service=" . $this->_db->quote($service) : "";
		if ($converted != 'na')
		{
			$converted = $converted == 1 ? 1 : 0;
			$query .= " AND remote_editing = " . $this->_db->quote($converted);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get remote connections
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $service
	 * @param   string   $subdir
	 * @param   boolean  $remoteEdit
	 * @return  array
	 */
	public function getRemoteFiles($projectid = null, $service = '', $subdir = '', $remoteEdit = false)
	{
		if (!$projectid)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl";
		$query .= " WHERE projectid=$projectid  ";
		$query .= " AND service='" . $service . "' ";
		if ($remoteEdit)
		{
			$query .= " AND remote_editing=1 ";
		}
		$query .= " AND local_dirpath='" . $subdir . "' ";

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();

		$files = array();
		if ($results)
		{
			foreach ($results as $result)
			{
				$files[$result->local_path] = $result;
			}
		}

		return $files;
	}

	/**
	 * Load item
	 *
	 * @param   integer  $projectid   Project ID
	 * @param   string   $id          Remote ID
	 * @param   string   $service     Service name (google)
	 * @param   string   $local_path  Local path to file
	 * @return  mixed    False if error, Object on success
	 */
	public function loadItem($projectid = null, $id = null, $service = '', $local_path = '')
	{
		if (!$projectid || (!$id && !$local_path))
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE projectid =" . $this->_db->quote($projectid);
		$query .= $service ? " AND service=" . $this->_db->quote($service) : '';

		if ($id)
		{
			$query .= " AND remote_id = " . $this->_db->quote($id);
		}
		else
		{
			$query .= " AND local_path =" . $this->_db->quote($local_path);
		}

		$query .= " ORDER BY modified DESC, created DESC LIMIT 1";

		$this->_db->setQuery($query);
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
	 * Get item
	 *
	 * @param   integer  $projectid   Project ID
	 * @param   string   $id          Remote ID
	 * @param   string   $service     Service name (google)
	 * @param   string   $local_path  Local path to file
	 * @param   string   $converted
	 * @return  mixed    False if error, Object on success
	 */
	public function getConnection($projectid, $id, $service, $local_path, $converted = 'na')
	{
		$query  = "SELECT id as record_id, remote_id as id, local_path as fpath,
					remote_parent as parent, remote_format as mimeType,
					remote_title as title, remote_editing as converted,
					service, remote_modified as modified, remote_author as author,
					remote_md5 as md5, synced, paired, type, original_path,
					original_format, original_id
					FROM $this->_tbl WHERE projectid = " . $this->_db->quote($projectid);
		$query .= $service ? " AND service=" . $this->_db->quote($service) : '';
		if ($id)
		{
			$query .= " AND remote_id = " . $this->_db->quote($id);
		}
		else
		{
			$query .= " AND local_path = " . $this->_db->quote($local_path);
		}
		if ($converted != 'na')
		{
			$converted = $converted == 1 ? 1 : 0;
			$query .= " AND remote_editing = " . $this->_db->quote($converted);
		}
		$query .= " ORDER BY modified DESC, created DESC LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadAssoc();
	}

	/**
	 * Provision record for remote item
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $service    Service name (google)
	 * @param   integer  $uid
	 * @param   array    $item       Remote item information array
	 * @param   array    $connections
	 * @return  mixed    False if error, Object on success
	 */
	public function checkRemoteRecord($projectid = null, $service = 'google', $uid = 0, $item = array(), $connections = array())
	{
		if (!$projectid || empty($item))
		{
			return false;
		}

		$id = $item['remoteid'];

		if (!$this->loadItem($projectid, $id, $service))
		{
			$this->projectid       = $projectid;
			$this->remote_id       = $id;
			$this->service         = $service;
			$this->remote_parent   = $item['rParent'];
			$this->remote_title    = $item['title'];
			$this->remote_md5      = $item['md5'];
			$this->type            = $item['type'];
			$this->created         = Date::toSql();
			$this->created_by      = $uid;
			$this->remote_editing  = $item['converted'];
			$this->remote_format   = $item['mimeType'];
			$this->remote_modified = $item['modified'];

			// Store local path for connection only if new
			if (!empty($connections) && !isset($connections[$item['local_path']]))
			{
				$this->local_path    = $item['local_path'];
				$this->local_dirpath = dirname($item['local_path']) != '.' ? dirname($item['local_path']) : '';
			}

			if ($this->store())
			{
				return true;
			}

			return false;
		}

		return true;
	}

	/**
	 * Delete record if exists
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $service    Service name (google)
	 * @param   integer  $id
	 * @param   string   $filename
	 * @return  mixed    False if error, Object on success
	 */
	public function deleteRecord($projectid = null, $service = 'google', $id = 0, $filename = null)
	{
		if (!$projectid || !$id)
		{
			return false;
		}

		if ($this->loadItem($projectid, $id, $service))
		{
			$this->delete();
		}

		return true;
	}

	/**
	 * Update record at sync
	 *
	 * @param   integer  $projectid
	 * @param   string   $service
	 * @param   integer  $uid
	 * @param   string   $type
	 * @param   integer  $id
	 * @param   string   $filename
	 * @param   array    $local
	 * @param   array    $remote
	 * @return  mixed    False if error, Object on success
	 */
	public function updateSyncRecord($projectid = null, $service = '', $uid = 0, $type = 'file', $id = null, $filename = null, $local = array(), $remote = array())
	{
		if (!$projectid || (!$id && !$filename))
		{
			return false;
		}

		// Load record to update
		if (!$this->loadItem($projectid, $id, $service, $filename))
		{
			$this->projectid  = $projectid;
			$this->service    = $service;
			$this->created    = Date::toSql();
			$this->created_by = $uid ? $uid : $this->uid;
			$this->type       = $type;
		}

		$this->remote_id       = $id ? $id : $this->remote_id;
		$this->local_path      = $filename ? $filename : $this->local_path;
		$this->local_dirpath   = dirname($this->local_path) != '.' ? dirname($this->local_path) : '';
		$this->local_md5       = isset($local['md5']) ? $local['md5'] : $this->local_md5;
		$this->local_format    = isset($local['mimeType']) ? $local['mimeType'] : $this->local_format;

		$this->remote_parent   = isset($remote['rParent']) ? $remote['rParent'] : $this->remote_parent;
		$this->remote_title    = isset($remote['title']) ? $remote['title'] : $this->remote_title;
		$this->remote_md5      = isset($remote['md5']) ? $remote['md5'] : $this->remote_md5;
		$this->remote_editing  = isset($remote['converted']) ? $remote['converted'] : $this->remote_editing;
		$this->remote_format   = isset($remote['mimeType']) ? $remote['mimeType'] : $this->remote_format;
		$this->remote_modified = isset($remote['modified']) ? $remote['modified'] : $this->remote_modified;
		$this->remote_author   = isset($remote['author']) ? $remote['author'] : $this->remote_author;

		$this->modified        = Date::toSql();
		$this->modified_by     = $uid ? $uid : $this->uid;
		$this->synced          = gmdate('Y-m-d H:i:s');

		if ($this->store())
		{
			return true;
		}

		return false;
	}

	/**
	 * Update record
	 *
	 * @param   integer  $projectid	  Project ID
	 * @param   string   $service     Service name (google)
	 * @param   integer  $id          Remote ID
	 * @param   string   $local_path
	 * @param   string   $type
	 * @param   integer  $uid
	 * @param   integer  $parentId
	 * @param   string   $title
	 * @param   string   $remote_md5
	 * @param   string   $local_md5
	 * @param   string   $converted
	 * @param   string   $remote_format
	 * @param   string   $local_format
	 * @param   string   $remote_modified
	 * @param   string   $remote_author
	 * @return  mixed    False if error, Object on success
	 */
	public function updateRecord($projectid = null, $service = '', $id = null, $local_path = '', $type = 'file', $uid = 0, $parentId = 0, $title = '', $remote_md5 = '', $local_md5 = '', $converted = '', $remote_format = '', $local_format = '', $remote_modified = '', $remote_author = '')
	{
		if (!$projectid || !$id)
		{
			return false;
		}

		// Load record to update
		if (!$this->loadItem($projectid, $id, $service))
		{
			$this->projectid  = $projectid;
			$this->remote_id  = $id;
			$this->service    = $service;
			$this->created    = Date::toSql();
			$this->created_by = $uid ? $uid : $this->uid;
		}

		$this->remote_parent   = $parentId ? $parentId : $this->remote_parent;
		$this->remote_title    = $title ? $title : $this->remote_title;
		$this->remote_md5      = $remote_md5 ? $remote_md5 : $this->remote_md5;
		$this->local_md5       = $local_md5 ? $local_md5 : $this->local_md5;

		$this->local_path      = $local_path ? $local_path : $this->local_path;
		$this->type	           = $type;
		$this->modified        = Date::toSql();
		$this->modified_by     = $uid ? $uid : $this->uid;
		$this->synced          = gmdate('Y-m-d H:i:s');
		$this->remote_editing  = $converted ? $converted : $this->remote_editing;
		$this->local_format    = $local_format ? $local_format : $this->local_format;
		$this->remote_format   = $remote_format ? $remote_format : $this->remote_format;
		$this->remote_modified = $remote_modified ? $remote_modified : $this->remote_modified;
		$this->remote_author   = $remote_author ? $remote_author : $this->remote_author;
		$this->local_dirpath   = dirname($this->local_path) != '.' ? dirname($this->local_path) : '';

		if ($this->store())
		{
			return true;
		}

		return false;
	}
}
