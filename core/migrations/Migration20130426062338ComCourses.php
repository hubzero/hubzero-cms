<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating grading policies
 *
*/
class Migration20130426062338ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableHasField('#__courses_grade_policies', 'score_criteria')) {
            $this->db->getQuery(true)
                ->delete('#__courses_grade_policies')
                ->where('id', '=', 1)
                ->execute();

            // First insert - passfail policy
            $description1 = 'Scores are based on the average of all exams.  '
                . 'An average exam score of 70% or greater is required to pass.';

            // ----------------------------------------------------------------
            // POLICY: Pass/Fail
            // ----------------------------------------------------------------

            // 1. gradeCriteria (Pass Logic):
            //    - Checks if the user's computed course score is >= 70.
            //    - Returns TRUE (passing) or FALSE.
            //    - Supports template injection for filtering by scope (course/section) and user.

            $gradeCriteria1 = <<<SQL
SELECT IF(score >= 70, TRUE, FALSE) as passing, cgb.user_id
FROM #__courses_grade_book AS cgb
[[::section_id::LEFT JOIN #__courses_members AS cm ON cgb.user_id = cm.user_id]]
WHERE scope = 'course'
[[:scope_id:AND scope_id = '{{var}}']]
[[:section_id:AND cm.section_id = '{{var}}']]
[[:user_id:AND cgb.user_id = '{{var}}']]
[[::section_id::AND cm.student = 1]]
SQL;
            // 2. scoreCriteria (Score Calculation):
            //    - Calculates the average score of all active 'exam' assets.
            //    - Joins grade_book -> assets -> (optional) asset_associations/groups.
            //    - Results in a single average score per user.

            $scoreCriteria1 = <<<SQL
SELECT user_id, AVG(cgb.score) as average[[:scope:, '{{var}}' as scope]][[:scope:, {{var}}_id as scope_id]]
FROM #__courses_grade_book AS cgb
LEFT JOIN #__courses_assets AS ca ON cgb.scope_id = ca.id
[[::unit::LEFT JOIN #__courses_asset_associations AS caa ON ca.id = caa.asset_id]]
[[::unit::LEFT JOIN #__courses_asset_groups AS cag ON caa.scope_id = cag.id]]
WHERE ca.subtype = 'exam'
AND ca.state = 1
AND cgb.scope = 'asset'
[[:user_id:AND cgb.user_id = '{{var}}']]
[[:course_id:AND ca.course_id = '{{var}}']]
GROUP BY user_id[[::unit::, cag.unit_id]]
SQL;

            $this->db->getQuery(true)
                ->insert('#__courses_grade_policies')
                ->columns(['id', 'alias', 'description', 'type', 'grade_criteria', 'score_criteria', 'badge_criteria'])
                ->values([
                    1,
                    'passfail',
                    $description1,
                    'passfail',
                    $gradeCriteria1,
                    $scoreCriteria1,
                    'pass'
                ])
                ->execute();

            // Second insert - minminusone policy
            $description2 = 'Scores are based on the average of all exams.  '
                . 'A minimum exam score of 70% or greater on all exams, '
                . 'less the lowest one, is required to pass.';

            // ----------------------------------------------------------------
            // POLICY: MinMinusOne (Drop Lowest Score)
            // ----------------------------------------------------------------

            // 1. gradeCriteria (Pass Logic):
            //    - Subquery: Ranks all active 'exam' scores for a user, ordered by score ASC (lowest first).
            //    - Outer Query: Filters out rank #1 (the lowest score).
            //    - Checks if the *minimum* of the remaining scores is > 70.
            //    - Returns TRUE (passing) only if ALL counted exams are > 70.

            $gradeCriteria2 = <<<SQL
SELECT if(min(score)>70, TRUE, FALSE) AS passing, user_id
FROM (
    SELECT cgb.user_id as user_id, score, section_id, student,
        @num := if(@user_id = cgb.user_id, @num + 1, 1) AS row_number,
        @user_id := cgb.user_id AS placeholder
    FROM #__courses_grade_book cgb
    LEFT JOIN #__courses_assets ca ON cgb.scope_id = ca.id
    [[::section_id::LEFT JOIN #__courses_members AS cm ON cgb.user_id = cm.user_id]]
    WHERE scope = 'asset'
    AND score IS NOT NULL
    AND ca.subtype = 'exam'
    AND ca.state = 1
    [[:user_id:AND cgb.user_id = '{{var}}']]
    [[:section_id:HAVING cm.section_id = '{{var}}']]
    [[::section_id::AND cm.student = '1']]
    ORDER BY cgb.user_id asc, score asc ) AS sub
WHERE sub.row_number != 1
GROUP BY user_id
SQL;

            // 2. scoreCriteria (Score Calculation):
            //    - Identical to Pass/Fail policy (Average of all exams).
            //    - Note: This calculates the average of ALL exams (including the dropped one),
            //      even though the passing criteria drops the lowest.

            $scoreCriteria2 = <<<SQL
SELECT user_id, AVG(cgb.score) as average[[:scope:, '{{var}}' as scope]][[:scope:, {{var}}_id as scope_id]]
FROM #__courses_grade_book AS cgb
LEFT JOIN #__courses_assets AS ca ON cgb.scope_id = ca.id
[[::unit::LEFT JOIN #__courses_asset_associations AS caa ON ca.id = caa.asset_id]]
[[::unit::LEFT JOIN #__courses_asset_groups AS cag ON caa.scope_id = cag.id]]
WHERE ca.subtype = 'exam'
AND ca.state = 1
AND cgb.scope = 'asset'
[[:user_id:AND cgb.user_id = '{{var}}']]
[[:course_id:AND ca.course_id = '{{var}}']]
GROUP BY user_id[[::unit::, cag.unit_id]]
SQL;

            $this->db->getQuery(true)
                ->insert('#__courses_grade_policies')
                ->columns(['alias', 'description', 'type', 'grade_criteria', 'score_criteria', 'badge_criteria'])
                ->values([
                    'minminusone',
                    $description2,
                    'passfail',
                    $gradeCriteria2,
                    $scoreCriteria2,
                    'pass'
                ])
                ->execute();
        }
    }
}
