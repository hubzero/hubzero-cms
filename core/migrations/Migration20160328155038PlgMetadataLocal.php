<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding local metadata support
  *
**/
class Migration20160328155038PlgMetadataLocal extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $this->addPluginEntry('metadata', 'local', 0);

        if (!$schema->tableExists('#__file_metadata')) {
            $schema->createTable('#__file_metadata')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('path', 255)->default('')
                ->string('key', 255)->default('')
                ->string('value', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->deletePluginEntry('metadata', 'local');

        if (!$schema->tableExists('#__file_metadata')) {
            $schema->createTable('#__file_metadata')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('path', 255)->default('')
                ->string('key', 255)->default('')
                ->string('value', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
