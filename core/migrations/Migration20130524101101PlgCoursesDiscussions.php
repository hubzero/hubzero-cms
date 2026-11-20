<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
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
