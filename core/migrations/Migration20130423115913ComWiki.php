<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding wiki page links table
 *
*/
class Migration20130423115913ComWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__wiki_page_links')) {
            $schema->createTable('#__wiki_page_links')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('page_id')->default(0)
                ->datetime('timestamp')->default('0000-00-00 00:00:00')
                ->string('scope', 50)->default('')
                ->integer('scope_id')->default(0)
                ->string('link', 255)->default('')
                ->string('url', 250)->default('')
                ->primaryKey('id')
                ->index('idx_page_id', 'page_id')
                ->index('idx_scoped', ['scope', 'scope_id'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__wiki_page_links');
    }
}
