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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Table class for publication screenshot
 */
class Screenshot extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_screenshots', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->filename) == '')
		{
			$this->setError(Lang::txt('Missing filename'));
			return false;
		}

		return true;
	}

	/**
	 * Load record
	 *
	 * @param   string   $filename   File name
	 * @param   integer  $versionid  Pub version ID
	 * @param   integer  $pid        Pub ID
	 * @param   string   $version    Version number or name
	 * @return  mixed    False if error, Object on success
	 */
	public function loadFromFilename($filename, $versionid = null, $pid = null, $version = 'default')
	{
		if ($filename === null)
		{
			return false;
		}
		if ($versionid === null && $pid === null)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl as s ";
		if (!$versionid)
		{
			$query.= " JOIN #__publication_versions as V ON V.publication_id=s.publication_id WHERE ";
			if ($version == 'default' or $version == 'current' && $version == 'main')
			{
				$query.= " AND V.main=1 ";
			}
			elseif ($version == 'dev')
			{
				$query.= " AND V.state=3 ";
			}
			elseif (intval($version))
			{
				$query.= " AND V.version_number=" . $this->_db->quote($version);
			}
			else
			{
				// Error in supplied version value
				$query.= " AND 1=2 ";
			}
			$query .= " AND V.publication_id=" . $this->_db->quote($pid);
		}
		else
		{
			$query.= " WHERE s.publication_version_id =" . $this->_db->quote($versionid);
		}
		$query.= " AND s.filename=" . $this->_db->quote($filename);

		$query.= " LIMIT 1";

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
	 * Get record
	 *
	 * @param   string   $filename   File name
	 * @param   integer  $versionid  Pub version ID
	 * @param   integer  $pid        Pub ID
	 * @param   string   $version    Version number or name
	 * @return  mixed    False if error, Object on success
	 */
	public function getScreenshot($filename, $versionid = null, $pid = null, $version = 'default')
	{
		if ($filename === null)
		{
			return false;
		}
		if ($versionid === null && $pid === null)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl as s ";
		if (!$versionid)
		{
			$query.= " JOIN #__publication_versions as V ON V.publication_id=s.publication_id WHERE ";
			if ($version == 'default' or $version == 'current' && $version == 'main')
			{
				$query.= " AND V.main=1 ";
			}
			elseif ($version == 'dev')
			{
				$query.= " AND V.state=3 ";
			}
			elseif (intval($version))
			{
				$query.= " AND V.version_number=" . $this->_db->quote($version);
			}
			else
			{
				// Error in supplied version value
				$query.= " AND 1=2 ";
			}
			$query .= " AND V.publication_id=" . $this->_db->quote($pid);
		}
		else
		{
			$query.= " WHERE s.publication_version_id =" . $this->_db->quote($versionid);
		}
		$query.= " AND s.filename=" . $this->_db->quote($filename);

		$query.= " LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : null;
	}

	/**
	 * Get last ordering
	 *
	 * @param   integer  $versionid  Pub version ID
	 * @param   integer  $pid        Pub ID
	 * @param   string   $version    Version number or name
	 * @return  integer
	 */
	public function getLastOrdering($versionid = null, $pid = null, $version = 'default')
	{
		if ($versionid === null && $pid === null)
		{
			return false;
		}
		$query = "SELECT s.ordering FROM $this->_tbl as s ";

		if (!$versionid)
		{
			$query.= " JOIN #__publication_versions as V ON V.publication_id=s.publication_id WHERE ";
			if ($version == 'default' or $version == 'current' && $version == 'main')
			{
				$query.= " AND V.main=1 ";
			}
			elseif ($version == 'dev')
			{
				$query.= " AND V.state=3 ";
			}
			elseif (intval($version))
			{
				$query.= " AND V.version_number=" . $this->_db->quote($version);
			}
			else
			{
				// Error in supplied version value
				$query.= " AND 1=2 ";
			}
			$query .= " AND V.publication_id=" . $this->_db->quote($pid);
		}
		else
		{
			$query.= " WHERE s.publication_version_id =" . $this->_db->quote($versionid);
		}
		$query.= " ORDER BY s.ordering DESC LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Save record
	 *
	 * @param   string   $filename   File name
	 * @param   integer  $versionid  Pub version ID
	 * @param   integer  $pid        Pub ID
	 * @param   integer  $ordering   Ordering
	 * @param   boolean  $new        New record?
	 * @return  boolean
	 */
	public function saveScreenshot($filename, $versionid = null, $pid = null, $ordering = 0, $new = false)
	{
		if ($filename === null)
		{
			return false;
		}
		if ($versionid === null or $pid === null)
		{
			return false;
		}
		if (!$new)
		{
			$this->_db->setQuery("UPDATE $this->_tbl SET ordering=" . $this->_db->quote($ordering) . "
				WHERE filename=" . $this->_db->quote($filename) . " AND publication_id="
				. $this->_db->quote($pid) . " AND publication_version_id=" . $this->_db->quote($versionid));
			if ($this->_db->query())
			{
				$ret = true;
			}
			else
			{
				$ret = false;
			}
		}
		else
		{
			$this->ordering               = $ordering;
			$this->publication_id         = $pid;
			$this->publication_version_id = $versionid;
			$this->filename               = $this->_db->quote($filename);
			$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		}
		if (!$ret)
		{
			$this->setError(strtolower(get_class($this)) . '::store failed <br />' . $this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Delete record
	 *
	 * @param   string   $filename   File name
	 * @param   integer  $versionid  Pub version ID
	 * @return  mixed
	 */
	public function deleteScreenshot($filename, $versionid = null)
	{
		if ($filename === null)
		{
			return false;
		}
		if ($versionid === null)
		{
			return false;
		}

		$query = "DELETE FROM $this->_tbl ";
		$query.= " WHERE publication_version_id=" . $this->_db->quote($versionid);
		$query.= " AND filename=" . $this->_db->quote($filename);
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	/**
	 * Delete records
	 *
	 * @param   integer  $versionid  Pub version ID
	 * @return  boolean
	 */
	public function deleteScreenshots($versionid = null)
	{
		if ($versionid === null)
		{
			return false;
		}

		$query = "DELETE FROM $this->_tbl ";
		$query.= " WHERE publication_version_id=" . $this->_db->quote($versionid);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get array of screenshots
	 *
	 * @param   integer  $versionid  Pub version ID
	 * @return  object
	 */
	public function getScreenshotArray($versionid = null)
	{
		if ($versionid === null)
		{
			return false;
		}

		$result = array();
		$shots = $this->getScreenshots($versionid);

		if ($shots)
		{
			foreach ($shots as $shot)
			{
				$result[$shot->srcfile] = $shot->filename;
			}
		}

		return $result;
	}

	/**
	 * Get records
	 *
	 * @param   integer  $versionid  Pub version ID
	 * @param   integer  $pid        Pub ID
	 * @param   string   $version    Version name or number
	 * @return  object
	 */
	public function getScreenshots($versionid = null, $pid = null, $version = 'default')
	{
		if ($versionid === null && $pid === null)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl as s ";

		if (!$versionid)
		{
			$query.= " JOIN #__publication_versions as V ON V.publication_id=s.publication_id WHERE ";
			if ($version == 'default' or $version == 'current' && $version == 'main')
			{
				$query.= " AND V.main=1 ";
			}
			elseif ($version == 'dev')
			{
				$query.= " AND V.state=3 ";
			}
			elseif (intval($version))
			{
				$query.= " AND V.version_number=" . $this->_db->quote($version);
			}
			else
			{
				// Error in supplied version value
				$query.= " AND 1=2 ";
			}
			$query = " AND V.publication_id=" . $this->_db->quote($pid);
		}
		else
		{
			$query.= " WHERE s.publication_version_id=" . $this->_db->quote($versionid);
		}
		$query.= " ORDER BY s.ordering ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get files
	 *
	 * @param   integer  $versionid  Pub version ID
	 * @param   integer  $pid        Pub ID
	 * @param   string   $version    Version name or number
	 * @return  array
	 */
	public function getFiles($versionid = null, $pid = null, $version = 'default')
	{
		if ($versionid === null && $pid === null)
		{
			return false;
		}

		$query = "SELECT s.filename FROM $this->_tbl as s ";

		if (!$versionid)
		{
			$query.= " JOIN #__publication_versions as V ON V.publication_id=s.publication_id WHERE ";
			if ($version == 'default' or $version == 'current' && $version == 'main')
			{
				$query.= " AND V.main=1 ";
			}
			elseif ($version == 'dev')
			{
				$query.= " AND V.state=3 ";
			}
			elseif (intval($version))
			{
				$query.= " AND V.version_number=" . $this->_db->quote($version);
			}
			else
			{
				// Error in supplied version value
				$query.= " AND 1=2 ";
			}
			$query = " AND V.publication_id=" . $this->_db->quote($pid);
		}
		else
		{
			$query.= " WHERE s.publication_version_id=" . $this->_db->quote($versionid);
		}
		$query.= "ORDER BY s.ordering ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
