<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding potentially missing alias field on polls table
  *
**/
class Migration20140822203559ComPoll extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__polls')
            && !$schema->hasColumn('#__polls', 'alias')
        ) {
            $schema->addColumn('#__polls', 'alias')->string(255)->notNull()->default('')->execute();

            $polls = $this->db->getQuery(true)
                ->select(['id', 'title'])
                ->from('#__polls')
                ->loadObjectList();

            if ($polls && count($polls) > 0) {
                foreach ($polls as $poll) {
                    $alias = preg_replace("/[^a-zA-Z0-9]/", '', $poll->title);
                    $alias = strtolower($alias);

                    $this->db->getQuery(true)
                        ->update('#__polls')
                        ->set(['alias' => $alias])
                        ->where('id', '=', $poll->id)
                        ->execute();
                }
            }
        }
    }
}
