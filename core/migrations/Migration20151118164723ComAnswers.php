<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

// Restricted access
/**
 * Migration script for moving voting logs to #__item_votes
**/
class Migration20151118164723ComAnswers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__answers_questions_log')) {
            $query = $this->db->getQuery(true)
                ->select('*')
                ->from('#__answers_questions_log');
            $rows = $query->loadObjectList();

            foreach ($rows as $row) {
                $this->db->getQuery(true)
                    ->insert('#__item_votes')
                    ->set([
                        'id'        => null,
                        'item_id'   => $row->question_id,
                        'item_type' => 'question',
                        'ip'        => $row->ip,
                        'created'   => $row->expires,
                        'created_by' => $row->voter,
                        'vote'      => 0
                    ])
                    ->execute();
            }

            $schema->dropTable('#__answers_questions_log');
        }

        if ($schema->tableExists('#__answers_log')) {
            $query = $this->db->getQuery(true)
                ->select('*')
                ->from('#__answers_log');
            $rows = $query->loadObjectList();

            foreach ($rows as $row) {
                $this->db->getQuery(true)
                    ->insert('#__item_votes')
                    ->set([
                        'id'        => null,
                        'item_id'   => $row->response_id,
                        'item_type' => 'response',
                        'ip'        => $row->ip,
                        'created'   => '0000-00-00 00:00:00',
                        'created_by' => 0,
                        'vote'      => ($row->helpful == 'yes' ? 1 : -1)
                    ])
                    ->execute();
            }

            $schema->dropTable('#__answers_log');
        }

        if ($schema->tableExists('#__answers_questions')) {
            if (!$schema->hasColumn('#__answers_questions', 'nothelpful')) {
                $schema->addColumn('#__answers_questions', 'nothelpful')->integer()->notNull()->default(0)->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__answers_questions_log')) {
            $schema->createTable('#__answers_questions_log')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('question_id')->default(0)
                ->datetime('expires')->default('0000-00-00 00:00:00')
                ->integer('voter')->default(0)
                ->string('ip', 15)->default('')
                ->primaryKey('id')
                ->index('idx_qid', 'question_id')
                ->index('idx_voter', 'voter')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $query = $this->db->getQuery(true)
                ->select('*')
                ->from('#__item_votes')
                ->where('item_type', '=', 'question');
            $rows = $query->loadObjectList();

            foreach ($rows as $row) {
                $this->db->getQuery(true)
                    ->insert('#__answers_questions_log')
                    ->set([
                        'id'          => null,
                        'question_id' => $row->item_id,
                        'expires'     => $row->created,
                        'voter'       => $row->created_by,
                        'ip'          => $row->ip
                    ])
                    ->execute();
            }
        }

        if (!$schema->tableExists('#__answers_log')) {
            $schema->createTable('#__answers_log')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('response_id')->default(0)
                ->string('ip', 15)->default('')
                ->string('helpful', 10)->default('')
                ->primaryKey('id')
                ->index('idx_rid', 'response_id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $query = $this->db->getQuery(true)
                ->select('*')
                ->from('#__item_votes')
                ->where('item_type', '=', 'response');
            $rows = $query->loadObjectList();

            foreach ($rows as $row) {
                $this->db->getQuery(true)
                    ->insert('#__answers_log')
                    ->set([
                        'id'          => null,
                        'response_id' => $row->item_id,
                        'ip'          => $row->ip,
                        'helpful'     => ($row->vote == 1 ? 'yes' : 'no')
                    ])
                    ->execute();
            }
        }

        if ($schema->tableExists('#__answers_questions')) {
            if ($schema->hasColumn('#__answers_questions', 'nothelpful')) {
                $schema->dropColumn('#__answers_questions', 'nothelpful');
            }
        }
    }
}
