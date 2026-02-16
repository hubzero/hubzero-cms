<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for deleting com_banners
 *
 */
class Migration20140110130812ComBanners extends Base
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
            ->where('element', '=', 'com_banners')
            ->value('extension_id');

        if ($id) {
            $this->deleteComponentEntry('banners');

            $this->deleteModuleEntry('mod_banners');

            $results = $this->db->getQuery(true)
                ->select('id')
                ->from('#__modules')
                ->where('module', '=', 'mod_banners')
                ->loadColumn();

            if ($results) {
                $this->db->getQuery(true)
                    ->delete('#__modules_menu')
                    ->whereIn('moduleid', $results)
                    ->execute();

                $this->db->getQuery(true)
                    ->delete('#__modules')
                    ->where('module', '=', 'mod_banners')
                    ->execute();
            }

            $schema = $this->db->schema();
            $schema->dropTable('#__banner_clients');
            $schema->dropTable('#__banner_tracks');
            $schema->dropTable('#__banners');
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
            ->where('element', '=', 'com_banners')
            ->value('extension_id');

        if (!$id) {
            $this->addComponentEntry('banners');

            if (!$schema->tableExists('#__banner_clients')) {
                $schema->createTable('#__banner_clients')
                    ->integer('id', ['autoIncrement' => true])
                    ->string('name', 255)->default('')
                    ->string('contact', 255)->default('')
                    ->string('email', 255)->default('')
                    ->text('extrainfo')
                    ->tinyInteger('state')->default(0)
                    ->unsignedInteger('checked_out')->default(0)
                    ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                    ->text('metakey')
                    ->tinyInteger('own_prefix')->default(0)
                    ->string('metakey_prefix', 255)->default('')
                    ->tinyInteger('purchase_type')->default(-1)
                    ->tinyInteger('track_clicks')->default(-1)
                    ->tinyInteger('track_impressions')->default(-1)
                    ->primaryKey('id')
                    ->index('idx_own_prefix', 'own_prefix')
                    ->index('idx_metakey_prefix', 'metakey_prefix')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }

            if (!$schema->tableExists('#__banner_tracks')) {
                $schema->createTable('#__banner_tracks')
                    ->datetime('track_date')
                    ->unsignedInteger('track_type')
                    ->unsignedInteger('banner_id')
                    ->unsignedInteger('count')->default(0)
                    ->primaryKey(['track_date', 'track_type', 'banner_id'])
                    ->index('idx_track_date', 'track_date')
                    ->index('idx_track_type', 'track_type')
                    ->index('idx_banner_id', 'banner_id')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }

            if (!$schema->tableExists('#__banners')) {
                $schema->createTable('#__banners')
                    ->integer('id', ['autoIncrement' => true])
                    ->integer('cid')->default(0)
                    ->integer('type')->default(0)
                    ->string('name', 255)->default('')
                    ->string('alias', 255)->default('')
                    ->integer('imptotal')->default(0)
                    ->integer('impmade')->default(0)
                    ->integer('clicks')->default(0)
                    ->string('clickurl', 200)->default('')
                    ->tinyInteger('state')->default(0)
                    ->unsignedInteger('catid')->default(0)
                    ->text('description')
                    ->string('custombannercode', 2048)
                    ->tinyInteger('sticky')->default(0)
                    ->integer('ordering')->default(0)
                    ->text('metakey')
                    ->text('params')
                    ->tinyInteger('own_prefix')->default(0)
                    ->string('metakey_prefix', 255)->default('')
                    ->tinyInteger('purchase_type')->default(-1)
                    ->tinyInteger('track_clicks')->default(-1)
                    ->tinyInteger('track_impressions')->default(-1)
                    ->unsignedInteger('checked_out')->default(0)
                    ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                    ->datetime('publish_up')->default('0000-00-00 00:00:00')
                    ->datetime('publish_down')->default('0000-00-00 00:00:00')
                    ->datetime('reset')->default('0000-00-00 00:00:00')
                    ->datetime('created')->default('0000-00-00 00:00:00')
                    ->char('language', 7)->default('')
                    ->primaryKey('id')
                    ->index('idx_banner_catid', 'catid')
                    ->index('idx_own_prefix', 'own_prefix')
                    ->index('idx_metakey_prefix', 'metakey_prefix')
                    ->index('idx_language', 'language')
                    ->index('idx_state', 'state')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }
        }
    }
}
