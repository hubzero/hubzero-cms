<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to #__citations_secondary table
  *
**/
class Migration20160328133847ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__citations_secondary')) {
            $schema->addIndex('#__citations_secondary', 'idx_cid', 'cid');
            $schema->addIndex('#__citations_secondary', 'idx_scope_scope_id', ['scope', 'scope_id']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__citations_secondary', 'idx_cid');
        $schema->dropIndex('#__citations_secondary', 'idx_scope_scope_id');
    }
}
