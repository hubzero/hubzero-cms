<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication curation history
 */
class CurationVersion extends Table
{
    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__publication_curation_versions', 'id', $db);
    }

    /**
     * Get last record for type
     *
     * @param   integer|string $type_id  Publication Master Type ID
     * @param   string   $get
     * @return  \stdClass|null  The row or null
     */
    public function getLatest($type_id = '', $get = '*')
    {
        $query = "SELECT $get FROM $this->tableName WHERE type_id=" . $this->db->quote($type_id);
        $query .= " ORDER BY id DESC LIMIT 1";

        $this->db->setQuery($query);

        $result = $this->db->loadObjectList();
        return $result ? $result[0] : null;
    }

    /**
     * Load last record for type
     *
     * @param   integer|string $type_id  Publication Master Type ID
     * @return  bool    True when the record was found and loaded
     */
    public function loadLatest($type_id = '')
    {
        $query = "SELECT * FROM $this->tableName WHERE type_id=" . $this->db->quote($type_id);
        $query .= " ORDER BY id DESC LIMIT 1";
        $this->db->setQuery($query);

        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            return false;
        }
    }
}
