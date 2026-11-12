<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for tracking when course form entries are submitted
  *
**/
class Migration20130322000000ComCourses extends Base
{
    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_form_respondent_progress')
            && !$schema->hasColumn('#__courses_form_respondent_progress', 'submitted')
        ) {
            $schema
                ->addColumn('#__courses_form_respondent_progress', 'submitted')
                ->datetime()
                ->nullable()
                ->execute();
        }
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_form_respondent_progress', 'submitted')) {
            $schema->dropColumn('#__courses_form_respondent_progress', 'submitted');
        }
    }
}
