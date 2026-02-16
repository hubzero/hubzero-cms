<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding courses announcements publish up, down, and sticky
**/
class Migration20130404000000ComCourses extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__courses_announcements')) {
            if (!$schema->hasColumn('#__courses_announcements', 'publish_up')) {
                $schema->addColumn('#__courses_announcements', 'publish_up')
                    ->datetime()
                    ->notNull()
                    ->default('0000-00-00 00:00:00')
                    ->execute();
            }
            if (!$schema->hasColumn('#__courses_announcements', 'publish_down')) {
                $schema->addColumn('#__courses_announcements', 'publish_down')
                    ->datetime()
                    ->notNull()
                    ->default('0000-00-00 00:00:00')
                    ->execute();
            }
            if (!$schema->hasColumn('#__courses_announcements', 'sticky')) {
                $schema->addColumn('#__courses_announcements', 'sticky')
                    ->tinyInteger(2)
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_announcements', 'publish_up')) {
            $schema->dropColumn('#__courses_announcements', 'publish_up');
        }
        if ($schema->hasColumn('#__courses_announcements', 'publish_down')) {
            $schema->dropColumn('#__courses_announcements', 'publish_down');
        }
        if ($schema->hasColumn('#__courses_announcements', 'sticky')) {
            $schema->dropColumn('#__courses_announcements', 'sticky');
        }
    }
}
