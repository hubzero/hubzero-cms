<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for courses member notes indices
 *
*/
class Migration20130410000000ComCourses extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        $schema->addIndex('#__courses_member_notes', 'idx_scoped', ['scope', 'scope_id']);
        $schema->addIndex('#__courses_member_notes', 'idx_createdby', 'created_by');
    }

    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__courses_member_notes', 'idx_scoped');
        $schema->dropIndex('#__courses_member_notes', 'idx_createdby');
    }
}
