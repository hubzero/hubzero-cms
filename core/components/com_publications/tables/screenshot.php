<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

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
        if (trim($this->filename) == '') {
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
        if ($filename === null) {
            return false;
        }
        if ($versionid === null && $pid === null) {
            return false;
        }

        $query = "SELECT * FROM $this->tableName as s ";
        if (!$versionid) {
            $query .= " JOIN #__publication_versions as V ON V.publication_id=s.publication_id WHERE ";
            if (in_array($version, ['default', 'current', 'main'])) {
                $query .= " AND V.main=1 ";
            } elseif ($version == 'dev') {
                $query .= " AND V.state=3 ";
            } elseif (intval($version)) {
                $query .= " AND V.version_number=" . $this->db->quote($version);
            } else {
                // Error in supplied version value
                $query .= " AND 1=2 ";
            }
            $query .= " AND V.publication_id=" . $this->db->quote($pid);
        } else {
            $query .= " WHERE s.publication_version_id =" . $this->db->quote($versionid);
        }
        $query .= " AND s.filename=" . $this->db->quote($filename);

        $query .= " LIMIT 1";

        $this->db->setQuery($query);
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            $this->setError($this->db->getErrorMsg());
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
        if ($filename === null) {
            return false;
        }
        if ($versionid === null && $pid === null) {
            return false;
        }

        $query = "SELECT * FROM $this->tableName as s ";
        if (!$versionid) {
            $query .= " JOIN #__publication_versions as V ON V.publication_id=s.publication_id WHERE ";
            if (in_array($version, ['default', 'current', 'main'])) {
                $query .= " AND V.main=1 ";
            } elseif ($version == 'dev') {
                $query .= " AND V.state=3 ";
            } elseif (intval($version)) {
                $query .= " AND V.version_number=" . $this->db->quote($version);
            } else {
                // Error in supplied version value
                $query .= " AND 1=2 ";
            }
            $query .= " AND V.publication_id=" . $this->db->quote($pid);
        } else {
            $query .= " WHERE s.publication_version_id =" . $this->db->quote($versionid);
        }
        $query .= " AND s.filename=" . $this->db->quote($filename);

        $query .= " LIMIT 1";

        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
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
        if ($versionid === null && $pid === null) {
            return false;
        }
        $query = "SELECT s.ordering FROM $this->tableName as s ";

        if (!$versionid) {
            $query .= " JOIN #__publication_versions as V ON V.publication_id=s.publication_id WHERE ";
            if (in_array($version, ['default', 'current', 'main'])) {
                $query .= " AND V.main=1 ";
            } elseif ($version == 'dev') {
                $query .= " AND V.state=3 ";
            } elseif (intval($version)) {
                $query .= " AND V.version_number=" . $this->db->quote($version);
            } else {
                // Error in supplied version value
                $query .= " AND 1=2 ";
            }
            $query .= " AND V.publication_id=" . $this->db->quote($pid);
        } else {
            $query .= " WHERE s.publication_version_id =" . $this->db->quote($versionid);
        }
        $query .= " ORDER BY s.ordering DESC LIMIT 1";

        $this->db->setQuery($query);
        return $this->db->loadResult();
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
        if ($filename === null) {
            return false;
        }
        if ($versionid === null or $pid === null) {
            return false;
        }
        if (!$new) {
            $this->db->setQuery("UPDATE $this->tableName SET ordering=" . $this->db->quote($ordering) . "
				WHERE filename=" . $this->db->quote($filename) . " AND publication_id="
                . $this->db->quote($pid) . " AND publication_version_id=" . $this->db->quote($versionid));
            if ($this->db->query()) {
                $ret = true;
            } else {
                $ret = false;
            }
        } else {
            $this->ordering               = $ordering;
            $this->publication_id         = $pid;
            $this->publication_version_id = $versionid;
            $this->filename               = $this->db->quote($filename);
            $ret = $this->db->insertObject($this->tableName, $this, $this->tableKey);
        }
        if (!$ret) {
            $this->setError(strtolower(get_class($this)) . '::store failed <br />' . $this->db->getErrorMsg());
            return false;
        } else {
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
        if ($filename === null) {
            return false;
        }
        if ($versionid === null) {
            return false;
        }

        $query = "DELETE FROM $this->tableName ";
        $query .= " WHERE publication_version_id=" . $this->db->quote($versionid);
        $query .= " AND filename=" . $this->db->quote($filename);
        $this->db->setQuery($query);
        $this->db->query();
    }

    /**
     * Delete records
     *
     * @param   integer  $versionid  Pub version ID
     * @return  boolean
     */
    public function deleteScreenshots($versionid = null)
    {
        if ($versionid === null) {
            return false;
        }

        $query = "DELETE FROM $this->tableName ";
        $query .= " WHERE publication_version_id=" . $this->db->quote($versionid);
        $this->db->setQuery($query);
        if (!$this->db->query()) {
            $this->setError($this->db->getErrorMsg());
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
        if ($versionid === null) {
            return false;
        }

        $result = [];
        $shots = $this->getScreenshots($versionid);

        if ($shots) {
            foreach ($shots as $shot) {
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
        if ($versionid === null && $pid === null) {
            return false;
        }

        $query = "SELECT * FROM $this->tableName as s ";

        if (!$versionid) {
            $query .= " JOIN #__publication_versions as V ON V.publication_id=s.publication_id WHERE ";
            if (in_array($version, ['default', 'current', 'main'])) {
                $query .= " AND V.main=1 ";
            } elseif ($version == 'dev') {
                $query .= " AND V.state=3 ";
            } elseif (intval($version)) {
                $query .= " AND V.version_number=" . $this->db->quote($version);
            } else {
                // Error in supplied version value
                $query .= " AND 1=2 ";
            }
            $query = " AND V.publication_id=" . $this->db->quote($pid);
        } else {
            $query .= " WHERE s.publication_version_id=" . $this->db->quote($versionid);
        }
        $query .= " ORDER BY s.ordering ASC";

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
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
        if ($versionid === null && $pid === null) {
            return false;
        }

        $query = "SELECT s.filename FROM $this->tableName as s ";

        if (!$versionid) {
            $query .= " JOIN #__publication_versions as V ON V.publication_id=s.publication_id WHERE ";
            if (in_array($version, ['default', 'current', 'main'])) {
                $query .= " AND V.main=1 ";
            } elseif ($version == 'dev') {
                $query .= " AND V.state=3 ";
            } elseif (intval($version)) {
                $query .= " AND V.version_number=" . $this->db->quote($version);
            } else {
                // Error in supplied version value
                $query .= " AND 1=2 ";
            }
            $query = " AND V.publication_id=" . $this->db->quote($pid);
        } else {
            $query .= " WHERE s.publication_version_id=" . $this->db->quote($versionid);
        }
        $query .= "ORDER BY s.ordering ASC";

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }
}
