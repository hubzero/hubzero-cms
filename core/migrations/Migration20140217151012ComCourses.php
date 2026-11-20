<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding is_default column
 *
*/
class Migration20140217151012ComCourses extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__courses_offering_sections')) {
            if (!$schema->hasColumn('#__courses_offering_sections', 'is_default')) {
                $schema->addColumn('#__courses_offering_sections', 'is_default')->tinyInteger(2)->notNull()->default(0);

                $this->db
                    ->getQuery()
                    ->update('#__courses_offering_sections')
                    ->set(['is_default' => 1])
                    ->where('alias', '=', '__default')
                    ->execute();
            }
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__courses_offering_sections')) {
            if ($schema->hasColumn('#__courses_offering_sections', 'is_default')) {
                $schema->dropColumn('#__courses_offering_sections', 'is_default');
            }
        }
    }
}
