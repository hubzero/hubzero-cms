<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing component entry for com_store
  *
**/
class Migration20171218000000ComStore extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $this->deleteComponentEntry('store');

        if ($schema->tableExists('#__store')) {
            $schema->dropTable('#__store');
        }

        if ($schema->tableExists('#__orders')) {
            $schema->dropTable('#__orders');
        }

        if ($schema->tableExists('#__order_items')) {
            $schema->dropTable('#__order_items');
        }

        if ($schema->tableExists('#__cart')) {
            $schema->dropTable('#__cart');
        }

        $path = PATH_APP . '/site/store';

        if (is_dir($path)) {
            $this->rrmdir($path);
        }
    }

    /**
     * Recursively remove a directory
     *
     * @param   string  $src
     * @return  void
     **/
    private function rrmdir($src)
    {
        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if ($file != '.' && $file != '..') {
                $full = $src . '/' . $file;
                if (is_dir($full)) {
                    $this->rrmdir($full);
                } else {
                    @unlink($full);
                }
            }
        }
        closedir($dir);
        @rmdir($src);
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->addComponentEntry('store');

        if (!$schema->tableExists('#__store')) {
            $schema->createTable('#__store')
                ->id()
                ->string('title', 127)->default('')
                ->integer('price')->default(0)
                ->text('description')->nullable()
                ->tinyInteger('published')->default(0)
                ->tinyInteger('featured')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('available')->default(0)
                ->text('params')->nullable()
                ->integer('special')->default(0)
                ->integer('type')->default(1)
                ->string('category', 127)->default('')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__orders')) {
            $schema->createTable('#__orders')
                ->id()
                ->integer('uid')->default(0)
                ->string('type', 20)->nullable()
                ->integer('total')->default(0)
                ->integer('status')->default(0)
                ->text('details')->nullable()
                ->string('email', 150)->nullable()
                ->datetime('ordered')->default('0000-00-00 00:00:00')
                ->datetime('status_changed')->default('0000-00-00 00:00:00')
                ->text('notes')->nullable()
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__order_items')) {
            $schema->createTable('#__order_items')
                ->id()
                ->integer('oid')->default(0)
                ->integer('uid')->default(0)
                ->integer('itemid')->default(0)
                ->integer('price')->default(0)
                ->integer('quantity')->default(0)
                ->text('selections')->nullable()
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__cart')) {
            $schema->createTable('#__cart')
                ->id()
                ->integer('uid')->default(0)
                ->integer('itemid')->default(0)
                ->string('type', 20)->nullable()
                ->integer('quantity')->default(0)
                ->datetime('added')->default('0000-00-00 00:00:00')
                ->text('selections')->nullable()
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
