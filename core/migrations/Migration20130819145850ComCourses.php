<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding concept of 'attempts' to forms
 *
*/
class Migration20130819145850ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_form_deployments')
            && !$schema->hasColumn('#__courses_form_deployments', 'allowed_attempts')
            && $schema->hasColumn('#__courses_form_deployments', 'user_id')
        ) {
            $schema->addColumn('#__courses_form_deployments', 'allowed_attempts')->integer()->notNull()->default(1);
        }
        if (
            $schema->tableExists('#__courses_form_respondents')
            && !$schema->hasColumn('#__courses_form_respondents', 'attempt')
            && $schema->hasColumn('#__courses_form_respondents', 'finished')
        ) {
            $schema->addColumn('#__courses_form_respondents', 'attempt')->integer()->notNull()->default(1);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_form_deployments', 'allowed_attempts')) {
            $schema->dropColumn('#__courses_form_deployments', 'allowed_attempts');
        }
        if ($schema->hasColumn('#__courses_form_respondents', 'attempt')) {
            $schema->dropColumn('#__courses_form_respondents', 'attempt');
        }
    }
}
