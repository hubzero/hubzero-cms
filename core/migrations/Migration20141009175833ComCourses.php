<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing engine type on some courses tables
 *
*/
class Migration20141009175833ComCourses extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_form_answers')
            && strtolower((string) $schema->getEngine('#__courses_form_answers')) != 'myisam'
        ) {
            $schema->setTableEngine('#__courses_form_answers', 'MyISAM');
        }

        if (
            $schema->tableExists('#__courses_form_deployments')
            && strtolower((string) $schema->getEngine('#__courses_form_deployments')) != 'myisam'
        ) {
            $schema->setTableEngine('#__courses_form_deployments', 'MyISAM');
        }

        if (
            $schema->tableExists('#__courses_form_questions')
            && strtolower((string) $schema->getEngine('#__courses_form_questions')) != 'myisam'
        ) {
            $schema->setTableEngine('#__courses_form_questions', 'MyISAM');
        }

        if (
            $schema->tableExists('#__courses_form_respondent_progress')
            && strtolower((string) $schema->getEngine('#__courses_form_respondent_progress')) != 'myisam'
        ) {
            $schema->setTableEngine('#__courses_form_respondent_progress', 'MyISAM');
        }

        if (
            $schema->tableExists('#__courses_form_respondents')
            && strtolower((string) $schema->getEngine('#__courses_form_respondents')) != 'myisam'
        ) {
            $schema->setTableEngine('#__courses_form_respondents', 'MyISAM');
        }

        if (
            $schema->tableExists('#__courses_form_responses')
            && strtolower((string) $schema->getEngine('#__courses_form_responses')) != 'myisam'
        ) {
            $schema->setTableEngine('#__courses_form_responses', 'MyISAM');
        }

        if (
            $schema->tableExists('#__courses_forms')
            && strtolower((string) $schema->getEngine('#__courses_forms')) != 'myisam'
        ) {
            $schema->setTableEngine('#__courses_forms', 'MyISAM');
        }

        if ($schema->tableExists('#__courses_offering_section_badge_criteria')) {
            if (strtolower((string) $schema->getCharacterSet('#__courses_offering_section_badge_criteria')) != 'utf8') {
                $schema->convertToCharset('#__courses_offering_section_badge_criteria', 'utf8');
            }
        }
    }
}
