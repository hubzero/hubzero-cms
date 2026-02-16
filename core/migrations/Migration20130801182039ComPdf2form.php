<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indices to pdf2form tables
 *
*/
class Migration20130801182039ComPdf2form extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__pdf_forms')) {
            if (
                $schema->tableExists('#__pdf_form_deployments')
                && !$schema->hasKey('#__pdf_form_deployments', 'jos_pdf_form_deployments_crumb_uidx')
            ) {
                $schema->addUniqueIndex('#__pdf_form_deployments', 'jos_pdf_form_deployments_crumb_uidx', 'crumb');
            }
            if (
                $schema->tableExists('#__pdf_form_responses')
                && !$schema->hasKey('#__pdf_form_responses', 'jos_pdf_form_responses_respondent_id_idx')
            ) {
                $schema->addIndex('#__pdf_form_responses', 'jos_pdf_form_responses_respondent_id_idx', 'respondent_id');
            }
            if (
                $schema->tableExists('#__pdf_form_responses')
                && !$schema->hasKey('#__pdf_form_responses', 'jos_pdf_form_responses_question_id_idx')
            ) {
                $schema->addIndex('#__pdf_form_responses', 'jos_pdf_form_responses_question_id_idx', 'question_id');
            }
            if (
                $schema->tableExists('#__pdf_form_responses')
                && !$schema->hasKey('#__pdf_form_responses', 'jos_pdf_form_responses_answer_id_idx')
            ) {
                $schema->addIndex('#__pdf_form_responses', 'jos_pdf_form_responses_answer_id_idx', 'answer_id');
            }
            if (
                $schema->tableExists('#__pdf_form_answers')
                && !$schema->hasKey('#__pdf_form_answers', 'jos_pdf_form_answers_question_id_idx')
            ) {
                $schema->addIndex('#__pdf_form_answers', 'jos_pdf_form_answers_question_id_idx', 'question_id');
            }
            if (
                $schema->tableExists('#__pdf_form_respondents')
                && !$schema->hasKey('#__pdf_form_respondents', 'jos_pdf_form_respondents_user_id_idx')
            ) {
                $schema->addIndex('#__pdf_form_respondents', 'jos_pdf_form_respondents_user_id_idx', 'user_id');
            }
            if (
                $schema->tableExists('#__pdf_form_respondents')
                && !$schema->hasKey('#__pdf_form_respondents', 'jos_pdf_form_respondents_deployment_id_idx')
            ) {
                $schema->addIndex(
                    '#__pdf_form_respondents',
                    'jos_pdf_form_respondents_deployment_id_idx',
                    'deployment_id'
                );
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__pdf_forms')) {
            $indices = [
                'jos_pdf_form_deployments_crumb_uidx' => '#__pdf_form_deployments',
                'jos_pdf_form_responses_respondent_id_idx' => '#__pdf_form_responses',
                'jos_pdf_form_responses_question_id_idx' => '#__pdf_form_responses',
                'jos_pdf_form_responses_answer_id_idx' => '#__pdf_form_responses',
                'jos_pdf_form_answers_question_id_idx' => '#__pdf_form_answers',
                'jos_pdf_form_respondents_user_id_idx' => '#__pdf_form_respondents',
                'jos_pdf_form_respondents_deployment_id_idx' => '#__pdf_form_respondents'
            ];

            foreach ($indices as $index => $table) {
                if ($schema->tableExists($table) && $schema->hasKey($table, $index)) {
                     $schema->dropIndex($table, $index);
                }
            }
        }
    }
}
