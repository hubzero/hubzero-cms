<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting state=3 on reported question comments
**/
class Migration20140702130951ComAnswers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__answers_questions', 'state')) {
            $ids = $this->db->getQuery(true)
                ->select('referenceid')
                ->from('#__abuse_reports')
                ->where('state', '=', 0)
                ->where('category', '=', 'question')
                ->loadColumn();

            if (!empty($ids)) {
                $this->db->getQuery(true)
                    ->update('#__answers_questions')
                    ->set(['state' => 3])
                    ->whereIn('id', array_map('intval', $ids))
                    ->execute();
            }
        }

        if ($schema->hasColumn('#__answers_responses', 'state')) {
            $ids = $this->db->getQuery(true)
                ->select('referenceid')
                ->from('#__abuse_reports')
                ->where('state', '=', 0)
                ->where('category', '=', 'answer')
                ->loadColumn();

            if (!empty($ids)) {
                $this->db->getQuery(true)
                    ->update('#__answers_responses')
                    ->set(['state' => 3])
                    ->whereIn('id', array_map('intval', $ids))
                    ->execute();
            }
        }

        if ($schema->hasColumn('#__item_comments', 'state')) {
            $ids = $this->db->getQuery(true)
                ->select('referenceid')
                ->from('#__abuse_reports')
                ->where('state', '=', 0)
                ->whereIn('category', ['itemcomment', 'answercomment'])
                ->loadColumn();

            if (!empty($ids)) {
                $this->db->getQuery(true)
                    ->update('#__item_comments')
                    ->set(['state' => 3])
                    ->whereIn('id', array_map('intval', $ids))
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__answers_questions', 'state')) {
            $this->db->getQuery(true)
                ->update('#__answers_questions')
                ->set(['state' => 1])
                ->where('state', '=', 3)
                ->execute();
        }

        if ($schema->hasColumn('#__answers_responses', 'state')) {
            $this->db->getQuery(true)
                ->update('#__answers_responses')
                ->set(['state' => 1])
                ->where('state', '=', 3)
                ->execute();
        }

        if ($schema->hasColumn('#__item_comments', 'state')) {
            $this->db->getQuery(true)
                ->update('#__item_comments')
                ->set(['state' => 1])
                ->where('state', '=', 3)
                ->execute();
        }
    }
}
