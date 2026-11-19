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
 * Table class for publication curation flow
 *
 * @property  mixed  $block
 * @property  mixed  $element
 * @property  mixed  $publication_id
 * @property  mixed  $publication_version_id
 * @property  mixed  $step
 */
class Curation extends Table
{
    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__publication_curation', 'id', $db);
    }

    /**
     * Validate data
     *
     * @return  boolean  True if data is valid
     */
    public function check()
    {
        if (!$this->publication_id) {
            $this->setError(Lang::txt('Must have a publication ID.'));
            return false;
        }

        if (!$this->publication_version_id) {
            $this->setError(Lang::txt('Must have a publication version ID.'));
            return false;
        }

        return true;
    }

    /**
     * Get curation record
     *
     * @param   integer|null  $vid  Publication Version ID
     * @return  mixed    False if error, Object on success
     */
    public function getRecords($vid = null)
    {
        if (!intval($vid)) {
            return false;
        }

        $query = "SELECT * FROM $this->tableName WHERE publication_version_id=" . $this->db->quote($vid);
        $query .= " ORDER BY step ASC, element ASC ";
        $this->db->setQuery($query);

        return $this->db->loadObjectList();
    }

    /**
     * Load record
     *
     * @param   integer|null  $pid  Publication ID
     * @param   integer|null  $vid  Publication Version ID
     * @param   mixed  $block
     * @param   int  $step
     * @param   mixed  $element
     * @return  \stdClass|false|null  The row, or false or null
     */
    public function getRecord($pid = null, $vid = null, $block = null, $step = 0, $element = null)
    {
        if (!$pid || !$vid || !$block || !intval($step)) {
            return false;
        }

        $query = "SELECT * FROM $this->tableName WHERE publication_id=" . $this->db->quote($pid);
        $query .= " AND publication_version_id=" . $this->db->quote($vid);
        $query .= " AND block=" . $this->db->quote($block);
        $query .= " AND step=" . $this->db->quote($step);
        $query .= $element ? " AND element=" . $this->db->quote($element) : " AND (element IS null OR element=0)";
        $query .= " ORDER BY id DESC LIMIT 1";
        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();
        return $results ? $results[0] : null;
    }

    /**
     * Load record
     *
     * @param   integer|null  $pid  Publication ID
     * @param   integer|null  $vid  Publication Version ID
     * @param   mixed  $block
     * @param   int  $step
     * @param   mixed  $element
     * @return  bool    True when the record was found and loaded
     */
    public function loadRecord($pid = null, $vid = null, $block = null, $step = 0, $element = null)
    {
        if (!$pid || !$vid || !$block || !intval($step)) {
            return false;
        }

        $query = "SELECT * FROM $this->tableName WHERE publication_id=" . $this->db->quote($pid);
        $query .= " AND publication_version_id=" . $this->db->quote($vid);
        $query .= " AND block=" . $this->db->quote($block);
        $query .= " AND step=" . $this->db->quote($step);
        $query .= $element ? " AND element=" . $this->db->quote($element) : " AND (element IS null OR element=0)";
        $query .= " ORDER BY id DESC LIMIT 1";
        $this->db->setQuery($query);

        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            return false;
        }
    }
}
