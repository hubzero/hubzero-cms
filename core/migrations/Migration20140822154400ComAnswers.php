<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming fulltext index on #__answers_questions
**/
class Migration20140822154400ComAnswers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__answers_questions')) {
            $schema->dropIndex('#__answers_questions', 'jos_answers_questions_question_subject_ftidx');

            $schema->addFulltextIndex('#__answers_questions', 'ftidx_question_subject', ['question', 'subject']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__answers_questions')) {
            $schema->dropIndex('#__answers_questions', 'ftidx_question_subject');

            $schema->addFulltextIndex('#__answers_questions', 'jos_answers_questions_question_subject_ftidx', [
                'question',
                'subject',
            ]);
        }
    }
}
