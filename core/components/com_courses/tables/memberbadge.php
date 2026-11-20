<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Courses member badges table
 *
 * @property  mixed  $id
 * @property  mixed  $member_id
 */
class MemberBadge extends Table
{
    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
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
