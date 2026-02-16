<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing old venues tables in favor of zones
 *
*/
class Migration20140421112851ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        /* We can just drop the old tables because they were never used on a live hub */

        if ($mwdb->schema()->tableExists('venues')) {
            $mwdb->schema()->dropTable('venues');
        }

        if ($mwdb->schema()->tableExists('venue_locations')) {
            $mwdb->schema()->dropTable('venue_locations');
        }

        if ($mwdb->schema()->tableExists('venue_countries')) {
            $mwdb->schema()->dropTable('venue_countries');
        }

        if (!$mwdb->schema()->tableExists('zones')) {
            $mwdb->schema()->createTable('zones')
                ->integer('id', ['autoIncrement' => true])
                ->string('zone', 40)->nullable()
                ->string('title', 255)->nullable()
                ->string('state', 15)->nullable()
                ->string('type', 10)->nullable()
                ->string('master', 255)->nullable()
                ->string('mw_version', 3)->nullable()
                ->string('ssh_key_path', 200)->nullable()
                ->string('picture', 250)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        $mwSchema = $mwdb->schema();

        if (!$mwSchema->hasColumn('zones', 'title')) {
            $mwSchema->addColumn('zones', 'title')
                ->string(255)
                ->nullable()
                ->default(null)
                ->execute();
        }

        if (!$mwdb->schema()->tableExists('zone_locations')) {
            $mwdb->schema()->createTable('zone_locations')
                ->integer('id', ['autoIncrement' => true])
                ->integer('zone_id')
                ->unsignedInteger('ipFROM')->default(0)
                ->unsignedInteger('ipTO')->default(0)
                ->char('continent', 2)
                ->char('countrySHORT', 2)
                ->string('countryLONG', 64)
                ->string('ipREGION', 128)
                ->string('ipCITY', 128)
                ->double('ipLATITUDE')->nullable()
                ->double('ipLONGITUDE')->nullable()
                ->string('notes', 128)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
