<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for tracking section enrollment
 *
*/
class Migration20130401000000ComCourses extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_offering_sections')
            && !$schema->hasColumn('#__courses_offering_sections', 'enrollment')
        ) {
            $schema->addColumn('#__courses_offering_sections', 'enrollment')->tinyInteger(2)->notNull()->default(0);
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_offering_sections', 'enrollment')) {
            $schema->dropColumn('#__courses_offering_sections', 'enrollment');
        }
    }
}
