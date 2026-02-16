<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for deleting com_weblinks
 *
 */
class Migration20140110132217ComWeblinks extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $id = $this->db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('type', '=', 'component')
            ->where('element', '=', 'com_weblinks')
            ->value('extension_id');

        if ($id) {
            $this->deleteComponentEntry('weblinks');

            $this->deletePluginEntry('search', 'weblinks');

            $this->deleteModuleEntry('mod_weblinks');

            $results = $this->db->getQuery(true)
                ->select('id')
                ->from('#__modules')
                ->where('module', '=', 'mod_weblinks')
                ->loadColumn();

            if ($results) {
                $this->db->getQuery(true)
                    ->delete('#__modules_menu')
                    ->whereIn('moduleid', $results)
                    ->execute();

                $this->db->getQuery(true)
                    ->delete('#__modules')
                    ->where('module', '=', 'mod_weblinks')
                    ->execute();
            }

            $this->db->schema()->dropTable('#__weblinks');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $id = $this->db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('type', '=', 'component')
            ->where('element', '=', 'com_weblinks')
            ->value('extension_id');

        if (!$id) {
            $this->addComponentEntry('weblinks');

            $this->addPluginEntry('weblinks', 'contacts', 0);

            if (!$schema->tableExists('#__weblinks')) {
                $schema->createTable('#__weblinks')
                    ->unsignedInteger('id', ['autoIncrement' => true])
                    ->integer('catid')->default(0)
                    ->integer('sid')->default(0)
                    ->string('title', 250)->default('')
                    ->string('alias', 255)->default('')
                    ->string('url', 250)->default('')
                    ->text('description')
                    ->datetime('date')->default('0000-00-00 00:00:00')
                    ->integer('hits')->default(0)
                    ->tinyInteger('state')->default(0)
                    ->integer('checked_out')->default(0)
                    ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                    ->integer('ordering')->default(0)
                    ->tinyInteger('archived')->default(0)
                    ->tinyInteger('approved')->default(1)
                    ->integer('access')->default(1)
                    ->text('params')
                    ->char('language', 7)->default('')
                    ->datetime('created')->default('0000-00-00 00:00:00')
                    ->unsignedInteger('created_by')->default(0)
                    ->string('created_by_alias', 255)->default('')
                    ->datetime('modified')->default('0000-00-00 00:00:00')
                    ->unsignedInteger('modified_by')->default(0)
                    ->text('metakey')
                    ->text('metadesc')
                    ->text('metadata')
                    ->unsignedTinyInteger('featured')->default(0)
                    ->string('xreference', 50)
                    ->datetime('publish_up')->default('0000-00-00 00:00:00')
                    ->datetime('publish_down')->default('0000-00-00 00:00:00')
                    ->primaryKey('id')
                    ->index('idx_access', 'access')
                    ->index('idx_checkout', 'checked_out')
                    ->index('idx_state', 'state')
                    ->index('idx_catid', 'catid')
                    ->index('idx_createdby', 'created_by')
                    ->index('idx_featured_catid', ['featured', 'catid'])
                    ->index('idx_language', 'language')
                    ->index('idx_xreference', 'xreference')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }
        }
    }
}
