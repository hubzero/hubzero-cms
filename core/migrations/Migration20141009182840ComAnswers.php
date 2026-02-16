<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing some answers tables column types
 *
*/
class Migration20141009182840ComAnswers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->modifyColumn('#__answers_log', 'response_id')->integer()->unsigned()->notNull()->default(0)->execute();
        $schema->modifyColumn('#__answers_questions', 'anonymous')
            ->tinyInteger()
            ->unsigned()
            ->notNull()
            ->default(0)
            ->execute();
        $schema->modifyColumn('#__answers_questions', 'reward')->tinyInteger()->notNull()->default(0)->execute();
        $schema->modifyColumn('#__answers_questions_log', 'question_id')
            ->integer()
            ->unsigned()
            ->notNull()
            ->default(0)
            ->execute();
        $schema->modifyColumn('#__answers_responses', 'question_id')
            ->integer()
            ->unsigned()
            ->notNull()
            ->default(0)
            ->execute();
        $schema->modifyColumn('#__answers_responses', 'answer')->text()->notNull()->execute();
        $schema->modifyColumn('#__answers_responses', 'anonymous')
            ->tinyInteger()
            ->unsigned()
            ->notNull()
            ->default(0)
            ->execute();
    }
}
