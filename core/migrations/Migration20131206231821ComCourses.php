<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding some needed indices to courses forms tables
 *
 */
class Migration20131206231821ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_form_deployments')
            && !$schema->hasKey('#__courses_form_deployments', 'idx_crumb')
        ) {
            $schema->addUniqueIndex('#__courses_form_deployments', 'idx_crumb', 'crumb');
        }
        if (
            $schema->tableExists('#__courses_form_responses')
            && !$schema->hasKey('#__courses_form_responses', 'idx_respondent_id')
        ) {
            $schema->addIndex('#__courses_form_responses', 'idx_respondent_id', 'respondent_id');
        }
        if (
            $schema->tableExists('#__courses_form_responses')
            && !$schema->hasKey('#__courses_form_responses', 'idx_question_id')
        ) {
            $schema->addIndex('#__courses_form_responses', 'idx_question_id', 'question_id');
        }
        if (
            $schema->tableExists('#__courses_form_responses')
            && !$schema->hasKey('#__courses_form_responses', 'idx_answer_id')
        ) {
            $schema->addIndex('#__courses_form_responses', 'idx_answer_id', 'answer_id');
        }
        if (
            $schema->tableExists('#__courses_form_answers')
            && !$schema->hasKey('#__courses_form_answers', 'idx_question_id')
        ) {
            $schema->addIndex('#__courses_form_answers', 'idx_question_id', 'question_id');
        }
        if (
            $schema->tableExists('#__courses_form_respondents')
            && !$schema->hasKey('#__courses_form_respondents', 'idx_member_id')
        ) {
            $schema->addIndex('#__courses_form_respondents', 'idx_member_id', 'member_id');
        }
        if (
            $schema->tableExists('#__courses_form_respondents')
            && !$schema->hasKey('#__courses_form_respondents', 'idx_deployment_id')
        ) {
            $schema->addIndex('#__courses_form_respondents', 'idx_deployment_id', 'deployment_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_form_deployments')
            && $schema->hasKey('#__courses_form_deployments', 'idx_crumb')
        ) {
            $schema->dropIndex('#__courses_form_deployments', 'idx_crumb');
        }
        if (
            $schema->tableExists('#__courses_form_responses')
            && $schema->hasKey('#__courses_form_responses', 'idx_respondent_id')
        ) {
            $schema->dropIndex('#__courses_form_responses', 'idx_respondent_id');
        }
        if (
            $schema->tableExists('#__courses_form_responses')
            && $schema->hasKey('#__courses_form_responses', 'idx_question_id')
        ) {
            $schema->dropIndex('#__courses_form_responses', 'idx_question_id');
        }
        if (
            $schema->tableExists('#__courses_form_responses')
            && $schema->hasKey('#__courses_form_responses', 'idx_answer_id')
        ) {
            $schema->dropIndex('#__courses_form_responses', 'idx_answer_id');
        }
        if (
            $schema->tableExists('#__courses_form_answers')
            && $schema->hasKey('#__courses_form_answers', 'idx_question_id')
        ) {
            $schema->dropIndex('#__courses_form_answers', 'idx_question_id');
        }
        if (
            $schema->tableExists('#__courses_form_respondents')
            && $schema->hasKey('#__courses_form_respondents', 'idx_member_id')
        ) {
            $schema->dropIndex('#__courses_form_respondents', 'idx_member_id');
        }
        if (
            $schema->tableExists('#__courses_form_respondents')
            && $schema->hasKey('#__courses_form_respondents', 'idx_deployment_id')
        ) {
            $schema->dropIndex('#__courses_form_respondents', 'idx_deployment_id');
        }
    }
}
