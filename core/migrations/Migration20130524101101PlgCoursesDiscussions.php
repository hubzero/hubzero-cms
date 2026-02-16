<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing wrong datatype on column
 *
*/
class Migration20130524101101PlgCoursesDiscussions extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->db
            ->schema()
            ->modifyColumn('#__courses_member_notes', 'timestamp')
            ->time()
            ->notNull()
            ->default('00:00:00');
    }
}
