<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for adding date columns to gradebook
  *
**/
class Migration20140815153140ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_grade_book')
            && $schema->hasColumn('#__courses_grade_book', 'override')
            && !$schema->hasColumn('#__courses_grade_book', 'score_recorded')
        ) {
            $schema->addColumn('#__courses_grade_book', 'score_recorded')
                ->datetime()
                ->nullable()
                ->execute();
        }

        if (
            $schema->tableExists('#__courses_grade_book')
            && $schema->hasColumn('#__courses_grade_book', 'score_recorded')
            && !$schema->hasColumn('#__courses_grade_book', 'override_recorded')
        ) {
            $schema->addColumn('#__courses_grade_book', 'override_recorded')
                ->datetime()
                ->nullable()
                ->execute();
        }

        // Get deployments
        $deployments = $this->db->getQuery(true)
            ->select(['cfd.id', 'cf.asset_id'])
            ->from('#__courses_form_deployments', 'cfd')
            ->innerJoin('#__courses_forms AS cf', 'cfd.form_id', 'cf.id')
            ->loadObjectList();

        if ($deployments && count($deployments) > 0) {
            foreach ($deployments as $deployment) {
                // Now set the score recorded dates for all existing forms
                $results = $this->db->getQuery(true)
                    ->select('pfr.member_id')
                    ->select('pfr.finished')
                    ->select(
                        Expression::percent(
                            Expression::count('pfa.id'),
                            Expression::count('pfr2.id')
                        ),
                        'score'
                    )
                    ->from('#__courses_form_respondents', 'pfr')
                    ->leftJoin('#__courses_form_latest_responses_view AS pfr2', 'pfr2.respondent_id', 'pfr.id')
                    ->leftJoin('#__courses_form_questions AS pfq', 'pfq.id', 'pfr2.question_id')
                    ->leftJoin('#__courses_form_answers AS pfa', function ($join) {
                        $join->on('pfa.id', '=', 'pfr2.answer_id')
                            ->where('pfa.correct', '=', 1);
                    })
                    ->where('deployment_id', '=', (int)$deployment->id)
                    ->group(['member_id', 'started', 'finished', 'version'])
                    ->order('member_id', 'ASC')
                    ->order('score', 'ASC')
                    ->order('finished', 'ASC')
                    ->loadObjectList('member_id');

                if ($results && count($results) > 0) {
                    foreach ($results as $result) {
                        if (isset($result->finished)) {
                            $this->db->getQuery(true)
                                ->update('#__courses_grade_book')
                                ->set(['score_recorded' => $result->finished])
                                ->where('member_id', '=', (int)$result->member_id)
                                ->where('scope', '=', 'asset')
                                ->where('scope_id', '=', (int)$deployment->asset_id)
                                ->execute();
                        }
                    }
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_grade_book')
            && $schema->hasColumn('#__courses_grade_book', 'score_recorded')
        ) {
            $schema->dropColumn('#__courses_grade_book', 'score_recorded');
        }

        if (
            $schema->tableExists('#__courses_grade_book')
            && $schema->hasColumn('#__courses_grade_book', 'override_recorded')
        ) {
            $schema->dropColumn('#__courses_grade_book', 'override_recorded');
        }
    }
}
