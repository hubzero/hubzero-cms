<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming tables and fields to conform to naming conventions
**/
class Migration20160425154200ComPoll extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__polls')) {
            if (
                $schema->hasColumn('#__polls', 'published')
                && !$schema->hasColumn('#__polls', 'state')
            ) {
                $schema->renameColumn('#__polls', 'published', 'state')
                    ->tinyInteger()
                    ->default(0)
                    ->execute();
            }
        }

        if ($schema->tableExists('#__poll_data') && !$schema->tableExists('#__poll_options')) {
            $schema->renameTable('#__poll_data', '#__poll_options');
        }

        if ($schema->tableExists('#__poll_options')) {
            if (
                $schema->hasColumn('#__poll_options', 'pollid')
                && !$schema->hasColumn('#__poll_options', 'poll_id')
            ) {
                $schema->renameColumn('#__poll_options', 'pollid', 'poll_id')
                    ->integer()
                    ->default(0)
                    ->execute();
            }
        }

        if ($schema->tableExists('#__poll_date') && !$schema->tableExists('#__poll_dates')) {
            $schema->renameTable('#__poll_date', '#__poll_dates');
        }

        if ($schema->tableExists('#__poll_menu') && !$schema->tableExists('#__poll_menus')) {
            $schema->renameTable('#__poll_menu', '#__poll_menus');
        }

        if ($schema->tableExists('#__poll_menus')) {
            if (
                $schema->hasColumn('#__poll_menus', 'pollid')
                && !$schema->hasColumn('#__poll_menus', 'poll_id')
            ) {
                $schema->renameColumn('#__poll_menus', 'pollid', 'poll_id')
                    ->integer()
                    ->default(0)
                    ->execute();
            }

            if (
                $schema->hasColumn('#__poll_menus', 'menuid')
                && !$schema->hasColumn('#__poll_menus', 'menu_id')
            ) {
                $schema->renameColumn('#__poll_menus', 'menuid', 'menu_id')
                    ->integer()
                    ->default(0)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__polls')) {
            if (
                $schema->hasColumn('#__polls', 'state')
                && !$schema->hasColumn('#__polls', 'published')
            ) {
                $schema->renameColumn('#__polls', 'state', 'published')
                    ->tinyInteger()
                    ->default(0)
                    ->execute();
            }
        }

        if ($schema->tableExists('#__poll_options') && !$schema->tableExists('#__poll_data')) {
            $schema->renameTable('#__poll_options', '#__poll_data');
        }

        if ($schema->tableExists('#__poll_data')) {
            if (
                $schema->hasColumn('#__poll_data', 'poll_id')
                && !$schema->hasColumn('#__poll_data', 'pollid')
            ) {
                $schema->renameColumn('#__poll_data', 'poll_id', 'pollid')
                    ->integer()
                    ->default(0)
                    ->execute();
            }
        }

        if ($schema->tableExists('#__poll_dates') && !$schema->tableExists('#__poll_date')) {
            $schema->renameTable('#__poll_dates', '#__poll_date');
        }

        if ($schema->tableExists('#__poll_menus')) {
            $schema->renameTable('#__poll_menus', '#__poll_menu');
        }

        if ($schema->tableExists('#__poll_menu')) {
            if (
                $schema->hasColumn('#__poll_menu', 'poll_id')
                && !$schema->hasColumn('#__poll_menu', 'pollid')
            ) {
                $schema->renameColumn('#__poll_menu', 'poll_id', 'pollid')
                    ->integer()
                    ->default(0)
                    ->execute();
            }

            if (
                $schema->hasColumn('#__poll_menu', 'menu_id')
                && !$schema->hasColumn('#__poll_menu', 'menuid')
            ) {
                $schema->renameColumn('#__poll_menu', 'menu_id', 'menuid')
                    ->integer()
                    ->default(0)
                    ->execute();
            }
        }
    }
}
