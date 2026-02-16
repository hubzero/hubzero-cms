<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding ordering column to collection posts and sort to collections
 *
*/
class Migration20141111193301ComCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections')) {
            $schema->alterTable('#__collections')
                ->addString('sort', 50)->notNull()->default('created')
                ->addString('layout', 50)->notNull()->default('grid')
                ->execute();
        }

        if ($schema->tableExists('#__collections_posts') && !$schema->hasColumn('#__collections_posts', 'ordering')) {
            $schema->addColumn('#__collections_posts', 'ordering')->integer()->notNull()->default(0)->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections')) {
            if ($schema->hasColumn('#__collections', 'sort')) {
                $schema->dropColumn('#__collections', 'sort');
            }
            if ($schema->hasColumn('#__collections', 'layout')) {
                $schema->dropColumn('#__collections', 'layout');
            }
        }

        if ($schema->tableExists('#__collections_posts') && $schema->hasColumn('#__collections_posts', 'ordering')) {
            $schema->dropColumn('#__collections_posts', 'ordering');
        }
    }
}
