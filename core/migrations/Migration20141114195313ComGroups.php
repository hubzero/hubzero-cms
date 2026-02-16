<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding id field to groups members table
**/
class Migration20141114195313ComGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xgroups_members')) {
            if (!$schema->hasColumn('#__xgroups_members', 'id')) {
                $schema->dropPrimaryKey('#__xgroups_members');
                $schema->addAutoIncrementPrimaryKey('#__xgroups_members', 'id', true);
            }
        }
    }
}
