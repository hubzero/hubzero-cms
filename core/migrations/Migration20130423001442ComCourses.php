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
 * Migration script for adding asset subtype to courses assets
  *
**/
class Migration20130423001442ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_assets')
            && !$schema->hasColumn('#__courses_assets', 'subtype')
        ) {
            $schema->addColumn('#__courses_assets', 'subtype')->string(255)->notNull()->default('file')->execute();

            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['subtype' => new Expression('type')])
                ->execute();

            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['subtype' => 'quiz'])
                ->where('type', '=', 'exam')
                ->whereLike('title', 'quiz')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['subtype' => 'homework'])
                ->where('type', '=', 'exam')
                ->whereLike('title', 'homework')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['subtype' => 'embedded'])
                ->where('type', '=', 'video')
                ->whereIsNotNull('content')
                ->where('content', '!=', '')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['type' => 'form'])
                ->where('type', '=', 'exam')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['type' => 'text'])
                ->where('type', '=', 'note')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['type' => 'text'])
                ->where('type', '=', 'wiki')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['type' => 'url'])
                ->where('type', '=', 'link')
                ->execute();

            $description = 'Scores are based on the average of all exams.  '
                . 'An average exam score of 70% or greater is required to pass.';
            $gradeCriteria = '{"select":[{"value":"IF(score >= 70, TRUE, FALSE) as passing"}],'
                . '"from":[],"where":[{"field":"cgb.scope","operator":"=","value":"course"}],'
                . '"group":[],"having":[]}';
            $scoreCriteria = '{"select":[{"value":"AVG(cgb.score) as average"}],"from":[],'
                . '"where":[{"field":"ca.subtype","operator":"=","value":"exam"},'
                . '{"field":"cgb.scope","operator":"=","value":"asset"}],'
                . '"group":[],"having":[]}';

            $this->db->getQuery(true)
                ->update('#__courses_grade_policies')
                ->set([
                    'description' => $description,
                    'grade_criteria' => $gradeCriteria,
                    'score_criteria' => $scoreCriteria,
                ])
                ->where('id', '=', 1)
                ->execute();
        }
    }
}
