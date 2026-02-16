<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for grade policy changes
 *
*/
class Migration20130506233657ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_grade_policies', 'score_criteria')) {
            // If the table is of the 'old' style, just get rid of it
            $schema->dropTable('#__courses_grade_policies');

            // Now create the new one
            if (!$schema->hasTable('#__courses_grade_policies')) {
                $schema->createTable('#__courses_grade_policies')
                    ->integer('id')->unsigned()->autoIncrement()
                    ->mediumText('description')->nullable()
                    ->decimal('threshold', 3, 2)->nullable()
                    ->decimal('exam_weight', 3, 2)->nullable()
                    ->decimal('quiz_weight', 3, 2)->nullable()
                    ->decimal('homework_weight', 3, 2)->nullable()
                    ->primaryKey('id')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }

            // Insert default row
            $description = 'An average exam score of 70% or greater is required to pass the class.  '
                . 'Quizzes and homeworks do not count toward the final score.';

            $this->db->getQuery(true)
                ->insert('#__courses_grade_policies')
                ->set([
                    'id'              => 1,
                    'description'     => $description,
                    'threshold'       => 0.70,
                    'exam_weight'     => 1.00,
                    'quiz_weight'     => 0.00,
                    'homework_weight' => 0.00
                ])
                ->execute();
        }
    }
}
