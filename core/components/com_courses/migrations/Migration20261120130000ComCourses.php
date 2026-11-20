<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Remove grades left behind by deleted course memberships
 *
 * A member's #__courses_grade_book rows are keyed by the membership. Until
 * a3912d5cb5, deleting a membership left them in place, where no gradebook
 * or report can reach them: nothing points at that membership again, and
 * enrolling anew makes another. They are deleted here, as deleting a
 * membership now does. (nanoHUB held 6,925 of them, from 313 memberships,
 * on 2026-11-20.)
 *
 * There is no down(): the rows belonged to nobody.
 */
class Migration20261120130000ComCourses extends Base
{
    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        if (
            !$this->db->tableExists('#__courses_grade_book')
            || !$this->db->tableExists('#__courses_members')
        ) {
            return;
        }

        $orphaned = 'FROM `#__courses_grade_book`'
            . ' WHERE NOT EXISTS ('
            . 'SELECT 1 FROM `#__courses_members` AS m WHERE m.`id` = `#__courses_grade_book`.`member_id`'
            . ')';

        $this->db->setQuery('SELECT COUNT(*) ' . $orphaned);
        $count = (int) $this->db->loadResult();

        if (!$count) {
            return;
        }

        $this->db->setQuery('DELETE ' . $orphaned);
        $this->db->query();

        $this->log(sprintf('Removed %d grade(s) of deleted course memberships', $count));
    }
}
