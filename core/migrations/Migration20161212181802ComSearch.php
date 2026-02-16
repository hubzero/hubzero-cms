<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for refactoring the blacklist table
  *
**/
class Migration20161212181802ComSearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__search_blacklist') &&
            $schema->hasColumn('#__search_blacklist', 'scope_id')
        ) {
            $schema->renameColumn('#__search_blacklist', 'scope', 'doc_id')
                ->string(255)
                ->notNull()
                ->default('')
                ->execute();

            $schema->dropColumn('#__search_blacklist', 'scope_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__search_blacklist') &&
            !$schema->hasColumn('#__search_blacklist', 'scope_id')
        ) {
            $schema->renameColumn('#__search_blacklist', 'doc_id', 'scope')
                ->string(255)
                ->notNull()
                ->default('')
                ->execute();

            $schema->addColumn('#__search_blacklist', 'scope_id')
                ->integer(11)
                ->nullable()
                ->default(null)
                ->execute();
        }
    }
}
