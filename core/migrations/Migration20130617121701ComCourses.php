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
class Migration20130617121701ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_offering_sections')
            && !$schema->hasColumn('#__courses_offering_sections', 'params')
        ) {
            $schema->addColumn('#__courses_offering_sections', 'params')->text()->notNull();
        }

        if (
            $schema->tableExists('#__courses_offerings')
            && !$schema->hasColumn('#__courses_offerings', 'params')
        ) {
            $schema->addColumn('#__courses_offerings', 'params')->text()->notNull();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_offering_sections', 'params')) {
            $schema->dropColumn('#__courses_offering_sections', 'params');
        }

        if ($schema->hasColumn('#__courses_offerings', 'params')) {
            $schema->dropColumn('#__courses_offerings', 'params');
        }
    }
}
