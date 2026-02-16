<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add `username` column to storefront_permissions table
 *
*/
class Migration20170920165818ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__storefront_permissions')) {
            if (!$schema->hasColumn('#__storefront_permissions', 'username')) {
                $schema->table('#__storefront_permissions')->alter()
                    ->addString('username', 255)->nullable()
                    ->execute();
            }

            if ($schema->hasKey('#__storefront_permissions', 'single entry per item')) {
                $schema->dropIndex('#__storefront_permissions', 'single entry per item');
            }

            $schema->addIndex('#__storefront_permissions', 'idx_scope_scope_id', ['scope', 'scope_id']);

            $schema->addIndex('#__storefront_permissions', 'idx_uid', 'uid');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__storefront_permissions')) {
            $schema->dropIndex('#__storefront_permissions', 'idx_uid');

            $schema->dropIndex('#__storefront_permissions', 'idx_scope_scope_id');

            if ($schema->hasColumn('#__storefront_permissions', 'username')) {
                $schema->table('#__storefront_permissions')->alter()
                    ->dropColumn('username')
                    ->execute();
            }

            $schema->addIndex('#__storefront_permissions', 'single entry per item', ['scope', 'scope_id', 'uid']);
        }
    }
}
