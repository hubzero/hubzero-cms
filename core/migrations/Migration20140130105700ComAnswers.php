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
 * Migration script for adding params field to asset groups
  *
**/
class Migration20140130105700ComAnswers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__answers_questions', 'created_by')) {
            $results = $this->db->getQuery(true)
                ->select(['id', 'created_by'])
                ->from('#__answers_questions')
                ->loadObjectList();

            if ($results && count($results) > 0) {
                foreach ($results as $r) {
                    $id = $this->db->getQuery(true)
                        ->select('id')
                        ->from('#__users')
                        ->where('username', '=', $r->created_by)
                        ->value('id');

                    $this->db->getQuery(true)
                        ->update('#__answers_questions')
                        ->set(['created_by' => (int)$id])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }

            $schema->modifyColumn('#__answers_questions', 'created_by')->integer()->notNull()->default(0)->execute();
        }

        if ($schema->hasColumn('#__answers_responses', 'created_by')) {
            $results = $this->db->getQuery(true)
                ->select(['id', 'created_by'])
                ->from('#__answers_responses')
                ->loadObjectList();

            if ($results && count($results) > 0) {
                foreach ($results as $r) {
                    $id = $this->db->getQuery(true)
                        ->select('id')
                        ->from('#__users')
                        ->where('username', '=', $r->created_by)
                        ->value('id');

                    $this->db->getQuery(true)
                        ->update('#__answers_responses')
                        ->set(['created_by' => (int)$id])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }

            $schema->modifyColumn('#__answers_responses', 'created_by')->integer()->notNull()->default(0)->execute();
        }

        if ($schema->hasColumn('#__answers_responses', 'qid')) {
            $schema->renameColumn('#__answers_responses', 'qid', 'question_id')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }

        if ($schema->hasColumn('#__answers_questions_log', 'qid')) {
            $schema->renameColumn('#__answers_questions_log', 'qid', 'question_id')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }

        if ($schema->hasColumn('#__answers_log', 'rid')) {
            $schema->renameColumn('#__answers_log', 'rid', 'response_id')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__answers_questions', 'created_by')) {
            $results = $this->db->getQuery(true)
                ->select(['id', 'created_by'])
                ->from('#__answers_questions')
                ->loadObjectList();

            if ($results && count($results) > 0) {
                foreach ($results as $r) {
                    $username = $this->db->getQuery(true)
                        ->select('username')
                        ->from('#__users')
                        ->where('id', '=', (int)$r->created_by)
                        ->value('username');

                    $this->db->getQuery(true)
                        ->update('#__answers_questions')
                        ->set(['created_by' => $username])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }

            $schema->modifyColumn('#__answers_questions', 'created_by')->string(50)->nullable()->execute();
        }

        if ($schema->hasColumn('#__answers_responses', 'created_by')) {
            $results = $this->db->getQuery(true)
                ->select(['id', 'created_by'])
                ->from('#__answers_responses')
                ->loadObjectList();

            if ($results && count($results) > 0) {
                foreach ($results as $r) {
                    $username = $this->db->getQuery(true)
                        ->select('username')
                        ->from('#__users')
                        ->where('id', '=', (int)$r->created_by)
                        ->value('username');

                    $this->db->getQuery(true)
                        ->update('#__answers_responses')
                        ->set(['created_by' => $username])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }

            $schema->modifyColumn('#__answers_responses', 'created_by')->string(50)->nullable()->execute();
        }

        if ($schema->hasColumn('#__answers_responses', 'question_id')) {
            $schema->renameColumn('#__answers_responses', 'question_id', 'qid')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }

        if ($schema->hasColumn('#__answers_questions_log', 'question_id')) {
            $schema->renameColumn('#__answers_questions_log', 'question_id', 'qid')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }

        if ($schema->hasColumn('#__answers_log', 'response_id')) {
            $schema->renameColumn('#__answers_log', 'response_id', 'rid')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }
}
