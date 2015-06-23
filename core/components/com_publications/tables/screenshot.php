<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Publications\Tables;

/**
 * Table class for publication screenshot
 */
class Screenshot extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_screenshots', 'id', $db );
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim( $this->filename ) == '')
		{
			$this->setError( Lang::txt('Missing filename'));
			return false;
		}

		return true;
	}

	/**
	 * Load record
	 *
	 * @param      string	$filename	File name
	 * @param      integer 	$versionid 	Pub version ID
	 * @param      integer  $pid 		Pub ID
	 * @param      string   $version 	Version number or name
	 * @return     mixed False if error, Object on success
	 */
	public function loadFromFilename( $filename, $versionid = null, $pid = null, $version = 'default')
	{
		if ($filename === NULL)
		{
			return false;
		}
		if ($versionid === NULL && $pid === NULL)
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
				$query.= " AND V.version_number=" . $this->_db->Quote($version);
			}
			else
			{
				// Error in supplied version value
				$query.= " AND 1=2 ";
			}
			$query .= " AND V.publication_id=" . $this->_db->Quote($pid);
		}
		else
		{
			$query.= " WHERE s.publication_version_id =" . $this->_db->Quote($versionid);
		}
		$query.= " AND s.filename=" . $this->_db->Quote($filename);

		$query.= " LIMIT 1";

		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind( $result );
		}
		else
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Get record
	 *
	 * @param      string	$filename	File name
	 * @param      integer 	$versionid 	Pub version ID
	 * @param      integer  $pid 		Pub ID
	 * @param      string   $version 	Version number or name
	 * @return     mixed False if error, Object on success
	 */
	public function getScreenshot( $filename, $versionid = null, $pid = null, $version = 'default')
	{
		if ($filename === NULL)
		{
			return false;
		}
		if ($versionid === NULL && $pid === NULL)
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
				$query.= " AND V.version_number=" . $this->_db->Quote($version);
			}
			else
			{
				// Error in supplied version value
				$query.= " AND 1=2 ";
			}
			$query .= " AND V.publication_id=" . $this->_db->Quote($pid);
		}
		else
		{
			$query.= " WHERE s.publication_version_id =" . $this->_db->Quote($versionid);
		}
		$query.= " AND s.filename=" . $this->_db->Quote($filename);

		$query.= " LIMIT 1";

		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : null;
	}

	/**
	 * Get last ordering
	 *
	 * @param      integer 	$versionid 	Pub version ID
	 * @param      integer  $pid 		Pub ID
	 * @param      string   $version 	Version number or name
	 * @return     integer
	 */
	public function getLastOrdering($versionid = null, $pid = null, $version = 'default')
	{
		if ($versionid === NULL && $pid === NULL)
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
				$query.= " AND V.version_number=" . $this->_db->Quote($version);
			}
			else
			{
				// Error in supplied version value
				$query.= " AND 1=2 ";
			}
			$query .= " AND V.publication_id=" . $this->_db->Quote($pid);
		}
		else
		{
			$query.= " WHERE s.publication_version_id =" . $this->_db->Quote($versionid);
		}
		$query.= " ORDER BY s.ordering DESC LIMIT 1";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Save record
	 *
	 * @param      string	$filename	File name
	 * @param      integer 	$versionid 	Pub version ID
	 * @param      integer  $pid 		Pub ID
	 * @param      integer  $ordering 	Ordering
	 * @param      boolean  $new 		New record?
	 * @return     boolean
	 */
	public function saveScreenshot( $filename, $versionid = null, $pid = null, $ordering = 0, $new = false )
	{
		if ($filename === NULL)
		{
			return false;
		}
		if ($versionid === NULL or $pid === NULL)
		{
			return false;
		}
		if (!$new)
		{
			$this->_db->setQuery( "UPDATE $this->_tbl SET ordering=" . $this->_db->Quote($ordering) . "
				WHERE filename=" . $this->_db->Quote($filename) . " AND publication_id="
				. $this->_db->Quote($pid) . " AND publication_version_id=" . $this->_db->Quote($versionid));
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
			$this->filename               = $this->_db->Quote($filename);
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		}
		if (!$ret)
		{
			$this->setError( strtolower(get_class( $this )) . '::store failed <br />' . $this->_db->getErrorMsg() );
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
	 * @param      string	$filename	File name
	 * @param      integer 	$versionid 	Pub version ID
	 * @return     void
	 */
	public function deleteScreenshot( $filename, $versionid = null )
	{
		if ($filename === NULL)
		{
			return false;
		}
		if ($versionid === NULL)
		{
			return false;
		}

		$query = "DELETE FROM $this->_tbl ";
		$query.= " WHERE publication_version_id=" . $this->_db->Quote($versionid);
		$query.= " AND filename=" . $this->_db->Quote($filename);
		$this->_db->setQuery( $query );
		$this->_db->query();
	}

	/**
	 * Delete records
	 *
	 * @param      integer 	$versionid 	Pub version ID
	 * @return     boolean
	 */
	public function deleteScreenshots( $versionid = null )
	{
		if ($versionid === NULL)
		{
			return false;
		}

		$query = "DELETE FROM $this->_tbl ";
		$query.= " WHERE publication_version_id=" . $this->_db->Quote($versionid);
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 * Get array of screenshots
	 *
	 * @param      integer 	$versionid 	Pub version ID
	 * @return     object
	 */
	public function getScreenshotArray( $versionid = null)
	{
		if ($versionid === NULL)
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
	 * @param      integer 	$versionid 	Pub version ID
	 * @param      integer  $pid 		Pub ID
	 * @param      string   $version 	Version name or number
	 * @return     object
	 */
	public function getScreenshots( $versionid = null, $pid = null, $version = 'default' )
	{
		if ($versionid === NULL && $pid === NULL)
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
				$query.= " AND V.version_number=" . $this->_db->Quote($version);
			}
			else
			{
				// Error in supplied version value
				$query.= " AND 1=2 ";
			}
			$query = " AND V.publication_id=" . $this->_db->Quote($pid);
		}
		else
		{
			$query.= " WHERE s.publication_version_id=" . $this->_db->Quote($versionid);
		}
		$query.= " ORDER BY s.ordering ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Get files
	 *
	 * @param      integer 	$versionid 	Pub version ID
	 * @param      integer  $pid 		Pub ID
	 * @param      string   $version 	Version name or number
	 * @return     array
	 */
	public function getFiles( $versionid = null, $pid = null, $version = 'default' )
	{
		if ($versionid === NULL && $pid === NULL)
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
				$query.= " AND V.version_number=" . $this->_db->Quote($version);
			}
			else
			{
				// Error in supplied version value
				$query.= " AND 1=2 ";
			}
			$query = " AND V.publication_id=" . $this->_db->Quote($pid);
		}
		else
		{
			$query.= " WHERE s.publication_version_id=" . $this->_db->Quote($versionid);
		}
		$query.= "ORDER BY s.ordering ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
