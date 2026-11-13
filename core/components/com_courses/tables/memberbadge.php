<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Courses member badges table
 */
class MemberBadge extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__courses_member_badges', 'id', $db);
    }

    /**
     * Load by member id
     *
     * @param   integer  $id  member id
     * @return  boolean
     */
    public function loadByMemberId($id)
    {
        $query =   "SELECT *
					FROM {$this->tableName}
					WHERE member_id = " . $this->db->quote($id);

        $this->db->setQuery($query);

        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        }

        return false;
    }
}
