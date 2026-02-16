<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indices and setting default field value
  *
**/
class Migration20131108095500ComAnswers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->migrateQuestionsTable();
        $this->migrateResponsesTable();
        $this->migrateQuestionsLogTable();
        $this->migrateLogTable();
    }

    /**
     * Migrate answers_questions table
     */
    protected function migrateQuestionsTable()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__answers_questions')) {
            return;
        }

        $schema->table('#__answers_questions')->alter()
            ->modifyInteger('id', true)->notNull()->autoIncrement()
            ->modifyString('subject', 250)->notNull()->default('')
            ->modifyText('question')->notNull()
            ->modifyString('created_by', 50)->notNull()->default('')
            ->modifyTinyInteger('email', true)->notNull()->default(0)
            ->modifyInteger('helpful', true)->notNull()->default(0)
            ->addIndex('idx_created_by', 'created_by')
            ->addIndex('idx_state', 'state')
            ->execute();
    }

    /**
     * Migrate answers_responses table
     */
    protected function migrateResponsesTable()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__answers_responses')) {
            return;
        }

        $schema->table('#__answers_responses')->alter()
            ->modifyInteger('id', true)->notNull()->autoIncrement()
            ->modifyInteger('qid', true)->notNull()->default(0)
            ->modifyString('created_by', 50)->notNull()->default('')
            ->modifyTinyInteger('state')->notNull()->default(0)
            ->modifyInteger('nothelpful', true)->notNull()->default(0)
            ->modifyInteger('helpful', true)->notNull()->default(0)
            ->addIndex('idx_created_by', 'created_by')
            ->addIndex('idx_state', 'state')
            ->addIndex('idx_qid', 'qid')
            ->execute();
    }

    /**
     * Migrate answers_questions_log table
     */
    protected function migrateQuestionsLogTable()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__answers_questions_log')) {
            return;
        }

        $schema->table('#__answers_questions_log')->alter()
            ->modifyInteger('id', true)->notNull()->autoIncrement()
            ->modifyInteger('voter')->notNull()->default(0)
            ->modifyString('ip', 15)->notNull()->default('')
            ->modifyInteger('qid', true)->notNull()->default(0)
            ->addIndex('idx_voter', 'voter')
            ->addIndex('idx_qid', 'qid')
            ->execute();
    }

    /**
     * Migrate answers_log table
     */
    protected function migrateLogTable()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__answers_log')) {
            return;
        }

        $schema->table('#__answers_log')->alter()
            ->modifyInteger('id', true)->notNull()->autoIncrement()
            ->modifyString('ip', 15)->notNull()->default('')
            ->modifyInteger('rid', true)->notNull()->default(0)
            ->modifyString('helpful', 10)->notNull()->default('')
            ->addIndex('idx_rid', 'rid')
            ->execute();
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__answers_questions')) {
            $schema->table('#__answers_questions')->alter()
                ->dropIndex('idx_created_by')
                ->dropIndex('idx_state')
                ->execute();
        }

        if ($schema->tableExists('#__answers_responses')) {
            $schema->table('#__answers_responses')->alter()
                ->dropIndex('idx_created_by')
                ->dropIndex('idx_qid')
                ->dropIndex('idx_state')
                ->execute();
        }

        if ($schema->tableExists('#__answers_questions_log')) {
            $schema->table('#__answers_questions_log')->alter()
                ->dropIndex('idx_qid')
                ->dropIndex('idx_voter')
                ->execute();
        }

        if ($schema->tableExists('#__answers_log')) {
            $schema->dropIndex('#__answers_log', 'idx_rid');
        }
    }
}
