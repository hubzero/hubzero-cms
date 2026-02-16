<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding an owner field to project connection entries
  *
**/
class Migration20160307191342PlgProjectsFiles extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__projects_connections')
            && $schema->hasColumn('#__projects_connections', 'provider_id')
            && !$schema->hasColumn('#__projects_connections', 'owner_id')
        ) {
            $schema->addColumn('#__projects_connections', 'owner_id')->integer()->nullable()->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__projects_connections')
            && $schema->hasColumn('#__projects_connections', 'owner_id')
        ) {
            $schema->dropColumn('#__projects_connections', 'owner_id');
        }
    }
}
