<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding course grading policies
 *
 */
class Migration20130411000000ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__courses_grade_policies')) {
            $schema->createTable('#__courses_grade_policies')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('alias', 255)->default('')
                ->mediumText('description')->nullable()
                ->string('type', 255)
                ->mediumText('grade_criteria')
                ->mediumText('score_criteria')
                ->mediumText('badge_criteria')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $gradeCriteria = '{"select":[{"value":"IF(score >= 70, TRUE, FALSE) as passing"}],'
                . '"from":[],"where":[{"field":"cgb.scope","operator":"=","value":"course"}],'
                . '"group":[],"having":[]}';
            $scoreCriteria = '{"select":[{"value":"AVG(cgb.score) as average"}],"from":[],'
                . '"where":[{"field":"ca.title","operator":"LIKE","value":"%exam%"},'
                . '{"field":"ca.type","operator":"=","value":"exam"},'
                . '{"field":"cgb.scope","operator":"=","value":"asset"}],'
                . '"group":[],"having":[]}';

            $this->db->getQuery(true)
                ->insert('#__courses_grade_policies')
                ->set([
                    'id'             => 1,
                    'alias'          => 'passfail',
                    'description'    => 'An average exam score of 70% or greater is required to pass',
                    'type'           => 'passfail',
                    'grade_criteria' => $gradeCriteria,
                    'score_criteria' => $scoreCriteria,
                    'badge_criteria' => 'pass'
                ])
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__courses_grade_policies');
    }
}
