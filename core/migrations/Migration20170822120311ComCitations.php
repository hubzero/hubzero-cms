<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to set context for existing resource citations
 *
*/
class Migration20170822120311ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__citations_assoc') && $schema->hasColumn('#__citations_assoc', 'type')) {
            $this->db->getQuery(true)
                ->update('#__citations_assoc')
                ->set(['type' => 'referencedby'])
                ->whereEquals('tbl', 'resource')
                ->beginAndGroup()
                    ->whereEquals('type', '')
                    ->orWhereIsNull('type')
                ->endGroup()
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__citations_assoc') && $schema->hasColumn('#__citations_assoc', 'type')) {
            $this->db->getQuery(true)
                ->update('#__citations_assoc')
                ->set(['type' => ''])
                ->whereEquals('tbl', 'resource')
                ->whereEquals('type', 'referencedby')
                ->execute();
        }
    }
}
