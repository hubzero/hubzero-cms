<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication audience level
 */
class AudienceLevel extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__publication_audience_levels', 'id', $db);
    }

    /**
     * Get records to a determined level
     *
     * @param   integer  $numlevels     Number of levels to return
     * @param   array    $levels        Array to populate
     * @param   array    $return_array  Return as array?
     * @return  array
     */
    public function getLevels($numlevels = 4, $levels = [], $return_array = 1)
    {
        $sql  = "SELECT label, title, description FROM $this->tableName ";
        $sql .= $numlevels == 4 ? " WHERE label != 'level5' " : "";
        $sql .= " ORDER BY label ASC";

        $this->db->setQuery($sql);
        $result = $this->db->loadObjectList();
        if ($result) {
            foreach ($result as $r) {
                $levels[$r->label] = $r->title;
            }
        }

        return $return_array ? $levels : $result;
    }
}
