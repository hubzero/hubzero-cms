<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adjusting venue tables
 *
*/
class Migration20130114000000Core extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__venue') && !$schema->tableExists('venue')) {
            $schema->renameTable('#__venue', 'venue');
        }

        if ($schema->tableExists('venue')) {
            $schema->modifyColumn('venue', 'venue')->string(40)->nullable()->default(null)->execute();
        }

        if ($schema->hasColumn('venue', 'network')) {
            $schema->dropColumn('venue', 'network');
        }
        if (!$schema->hasColumn('venue', 'state')) {
            $schema->addColumn('venue', 'state')->string(15)->nullable()->default(null);
        }
        if (!$schema->hasColumn('venue', 'type')) {
            $schema->addColumn('venue', 'type')->string(10)->nullable()->default(null);
        }
        if (!$schema->hasColumn('venue', 'mw_version')) {
            $schema->addColumn('venue', 'mw_version')->string(3)->nullable()->default(null);
        }
        if (!$schema->hasColumn('venue', 'ssh_key_path')) {
            $schema->addColumn('venue', 'ssh_key_path')->string(200)->nullable()->default(null);
        }
        if (!$schema->hasColumn('venue', 'latitude')) {
            $schema->addColumn('venue', 'latitude')->double()->nullable()->default(null);
        }
        if (!$schema->hasColumn('venue', 'longitude')) {
            $schema->addColumn('venue', 'longitude')->double()->nullable()->default(null);
        }
        if (!$schema->hasColumn('venue', 'master')) {
            $schema->addColumn('venue', 'master')->string(255)->nullable()->default(null);
        }

        if ($schema->tableExists('#__venue_countries') && !$schema->tableExists('venue_countries')) {
            $schema->renameTable('#__venue_countries', 'venue_countries');
        }

        if (!$schema->hasColumn('venue_countries', 'id')) {
            // Adding auto-increment primary key to existing table requires multiple steps:
            // Step 1: Add the column without auto_increment
            $schema->addColumn('venue_countries', 'id')->integer()->notNull()->default(0);

            // Step 2: Populate existing rows with unique sequential values (cross-database portable)
            $schema->populateSequentialValues('venue_countries', 'id');

            // Step 3: Add primary key constraint
            $schema->addPrimaryKey('venue_countries', 'id');

            // Step 4: Enable auto_increment
            $schema->modifyColumn('venue_countries', 'id')->integer()->notNull()->autoIncrement()->execute();
        }
        if (!$schema->hasColumn('venue_countries', 'venue_id')) {
            $schema->addColumn('venue_countries', 'venue_id')->integer()->notNull();
        }
        if ($schema->hasColumn('venue_countries', 'venue')) {
            $schema->dropColumn('venue_countries', 'venue');
        }
    }
}
