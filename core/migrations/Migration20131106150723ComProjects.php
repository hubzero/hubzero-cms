<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding projects fulltext key
 *
*/
class Migration20131106150723ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__projects')) {
            $schema->addFulltextIndex('#__projects', 'idx_fulltxt_alias_title_about', ['alias', 'title', 'about']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__projects', 'idx_fulltxt_alias_title_about');
    }
}
