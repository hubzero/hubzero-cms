<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add a table for product images and a couple extra fields
  *
**/
class Migration20160620172827ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__storefront_images')) {
            $schema->createTable('#__storefront_images')
                ->unsignedInteger('imgId', ['autoIncrement' => true])
                ->char('imgName', 255)->nullable()
                ->char('imgObject', 25)->nullable()
                ->integer('imgObjectId')->nullable()
                ->tinyInteger('imgPrimary')->default(1)
                ->primaryKey('imgId')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (
            $schema->tableExists('#__storefront_option_groups')
            && !$schema->hasColumn('#__storefront_option_groups', 'ogActive')
        ) {
            $schema->addColumn('#__storefront_option_groups', 'ogActive')
                ->tinyInteger()
                ->notNull()
                ->default(0)
                ->execute();
        }

        if (
            $schema->tableExists('#__storefront_options')
            && !$schema->hasColumn('#__storefront_options', 'oActive')
        ) {
            $schema->addColumn('#__storefront_options', 'oActive')->tinyInteger()->notNull()->default(0)->execute();
        }

        if (
            $schema->tableExists('#__storefront_collections')
            && !$schema->hasColumn('#__storefront_collections', 'cAlias')
        ) {
            $schema->addColumn('#__storefront_collections', 'cAlias')->char(50)->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__storefront_images');

        if (
            $schema->tableExists('#__storefront_option_groups')
            && $schema->hasColumn('#__storefront_option_groups', 'ogActive')
        ) {
            $schema->dropColumn('#__storefront_option_groups', 'ogActive');
        }

        if (
            $schema->tableExists('#__storefront_options')
            && $schema->hasColumn('#__storefront_options', 'oActive')
        ) {
            $schema->dropColumn('#__storefront_options', 'oActive');
        }

        if (
            $schema->tableExists('#__storefront_collections')
            && $schema->hasColumn('#__storefront_collections', 'cAlias')
        ) {
            $schema->dropColumn('#__storefront_collections', 'cAlias');
        }
    }
}
