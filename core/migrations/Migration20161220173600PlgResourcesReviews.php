<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for moving votes from #__vote_log to #__item_votes
 *
*/
class Migration20161220173600PlgResourcesReviews extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__vote_log') && $schema->tableExists('#__item_votes')) {
            $votes = $this->db->getQuery(true)
                ->select('*')
                ->from('#__vote_log')
                ->where('category', '=', 'review')
                ->loadObjectList();

            foreach ($votes as $vote) {
                $this->db->getQuery(true)
                    ->insert('#__item_votes')
                    ->set([
                        'id'         => null,
                        'item_id'    => $vote->referenceid,
                        'item_type'  => $vote->category,
                        'ip'         => $vote->ip,
                        'created'    => $vote->voted,
                        'created_by' => $vote->voter,
                        'vote'       => ($vote->helpful == 'no' ? -1 : 1)
                    ])
                    ->execute();
            }

            $this->db->getQuery(true)
                ->delete('#__vote_log')
                ->where('category', '=', 'review')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__vote_log') && $schema->tableExists('#__item_votes')) {
            $votes = $this->db->getQuery(true)
                ->select('*')
                ->from('#__item_votes')
                ->where('item_type', '=', 'review')
                ->loadObjectList();

            foreach ($votes as $vote) {
                $this->db->getQuery(true)
                    ->insert('#__vote_log')
                    ->set([
                        'id'          => null,
                        'referenceid' => $vote->item_id,
                        'category'    => $vote->item_type,
                        'ip'          => $vote->ip,
                        'voted'       => $vote->created,
                        'voter'       => $vote->created_by,
                        'helpful'     => ($vote->vote == 1 ? 'yes' : 'no')
                    ])
                    ->execute();
            }

            $this->db->getQuery(true)
                ->delete('#__item_votes')
                ->where('item_type', '=', 'review')
                ->execute();
        }
    }
}
