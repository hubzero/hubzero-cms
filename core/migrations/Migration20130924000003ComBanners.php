<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for joomla banner tables
**/
class Migration20130924000003ComBanners extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->migrateBannersTable();
        $this->migrateBannerClientsTable();
        $this->migrateBannerTracksTable();
    }

    /**
     * Migrate the banners table
     */
    protected function migrateBannersTable()
    {
        $schema = $this->db->schema();

        // Rename old table if needed
        if ($schema->tableExists('#__banner') && !$schema->tableExists('#__banners')) {
            $schema->renameTable('#__banner', '#__banners');
            $schema->setTableEngine('#__banners', 'MYISAM');
        }

        if (!$schema->tableExists('#__banners')) {
            return;
        }

        // Rename columns if needed
        if ($schema->hasColumn('#__banners', 'bid') && !$schema->hasColumn('#__banners', 'id')) {
            $schema->renameColumn('#__banners', 'bid', 'id')
                ->integer()
                ->notNull()
                ->autoIncrement()
                ->execute();
        }
        if ($schema->hasColumn('#__banners', 'showBanner') && !$schema->hasColumn('#__banners', 'state')) {
            $schema->renameColumn('#__banners', 'showBanner', 'state')
                ->tinyInteger()
                ->notNull()
                ->default(0)
                ->execute();
        }
        if ($schema->hasColumn('#__banners', 'tags') && !$schema->hasColumn('#__banners', 'metakey')) {
            $schema->renameColumn('#__banners', 'tags', 'metakey')
                ->text()
                ->notNull()
                ->execute();
        }
        if ($schema->hasColumn('#__banners', 'date') && !$schema->hasColumn('#__banners', 'created')) {
            $schema->renameColumn('#__banners', 'date', 'created')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();
        }

        // Batch column modifications
        $schema->table('#__banners')->alter()
            ->modifyColumn('alias', function ($column) {
                $column->string(255)->notNull()->default('');
            })
            ->modifyColumn('checked_out', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('type', function ($column) {
                $column->integer()->notNull()->default(0);
            })
            ->modifyColumn('catid', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('description', function ($column) {
                $column->text()->notNull();
            })
            ->modifyColumn('custombannercode', function ($column) {
                $column->string(2048)->notNull();
            })
            ->modifyColumn('sticky', function ($column) {
                $column->tinyInteger()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('ordering', function ($column) {
                $column->integer()->notNull()->default(0);
            })
            ->modifyColumn('params', function ($column) {
                $column->text()->notNull();
            })
            ->execute();

        // Drop obsolete columns
        if ($schema->hasColumn('#__banners', 'editor')) {
            $schema->dropColumn('#__banners', 'editor');
        }
        if ($schema->hasColumn('#__banners', 'imageurl')) {
            $schema->dropColumn('#__banners', 'imageurl');
        }

        // Add new columns (with position - MySQL-specific)
        if (!$schema->hasColumn('#__banners', 'own_prefix') && $schema->hasColumn('#__banners', 'params')) {
            $schema->addColumn('#__banners', 'own_prefix')
                ->tinyInteger()
                ->notNull()
                ->default(0)
                ->after('params')
                ->execute();
        }
        if (!$schema->hasColumn('#__banners', 'metakey_prefix') && $schema->hasColumn('#__banners', 'own_prefix')) {
            $schema->addColumn('#__banners', 'metakey_prefix')
                ->string(255)
                ->notNull()
                ->default('')
                ->after('own_prefix')
                ->execute();
        }
        if (!$schema->hasColumn('#__banners', 'purchase_type') && $schema->hasColumn('#__banners', 'metakey_prefix')) {
            $schema->addColumn('#__banners', 'purchase_type')
                ->tinyInteger()
                ->notNull()
                ->default(-1)
                ->after('metakey_prefix')
                ->execute();
        }
        if (!$schema->hasColumn('#__banners', 'track_clicks') && $schema->hasColumn('#__banners', 'purchase_type')) {
            $schema->addColumn('#__banners', 'track_clicks')
                ->tinyInteger()
                ->notNull()
                ->default(-1)
                ->after('purchase_type')
                ->execute();
        }
        if (
            !$schema->hasColumn('#__banners', 'track_impressions')
            && $schema->hasColumn('#__banners', 'track_clicks')
        ) {
            $schema->addColumn('#__banners', 'track_impressions')
                ->tinyInteger()
                ->notNull()
                ->default(-1)
                ->after('track_clicks')
                ->execute();
        }
        if (!$schema->hasColumn('#__banners', 'reset') && $schema->hasColumn('#__banners', 'publish_down')) {
            $schema->addColumn('#__banners', 'reset')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->after('publish_down')
                ->execute();
        }
        if (!$schema->hasColumn('#__banners', 'language') && $schema->hasColumn('#__banners', 'created')) {
            $schema->addColumn('#__banners', 'language')
                ->char(7)
                ->notNull()
                ->default('')
                ->after('created')
                ->execute();
        }

        // Update type based on custombannercode
        if ($schema->hasColumn('#__banners', 'type') && $schema->hasColumn('#__banners', 'custombannercode')) {
            $this->db->getQuery(true)
                ->update('#__banners')
                ->set(['type' => 1])
                ->where(Expression::trim('custombannercode'), '!=', '')
                ->execute();
        }

        // Batch index operations
        $schema->table('#__banners')->alter()
            ->dropIndex('viewbanner')
            ->addIndex('idx_own_prefix', 'own_prefix')
            ->addIndex('idx_metakey_prefix', 'metakey_prefix')
            ->addIndex('idx_language', 'language')
            ->addIndex('idx_state', 'state')
            ->execute();
    }

    /**
     * Migrate the banner_clients table
     */
    protected function migrateBannerClientsTable()
    {
        $schema = $this->db->schema();

        // Rename old table if needed
        if ($schema->tableExists('#__bannerclient') && !$schema->tableExists('#__banner_clients')) {
            $schema->renameTable('#__bannerclient', '#__banner_clients');
            $schema->setTableEngine('#__banner_clients', 'MYISAM');
        }

        if (!$schema->tableExists('#__banner_clients')) {
            return;
        }

        // Rename column if needed
        if ($schema->hasColumn('#__banner_clients', 'cid') && !$schema->hasColumn('#__banner_clients', 'id')) {
            $schema->renameColumn('#__banner_clients', 'cid', 'id')
                ->integer()
                ->notNull()
                ->autoIncrement()
                ->execute();
        }

        // Batch column modifications
        $schema->table('#__banner_clients')->alter()
            ->modifyColumn('checked_out', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('checked_out_time', function ($column) {
                $column->datetime()->notNull()->default('0000-00-00 00:00:00');
            })
            ->execute();

        // Drop obsolete column
        if ($schema->hasColumn('#__banner_clients', 'editor')) {
            $schema->dropColumn('#__banner_clients', 'editor');
        }

        // Add new columns
        if (!$schema->hasColumn('#__banner_clients', 'state')) {
            $schema->addColumn('#__banner_clients', 'state')
                ->tinyInteger()
                ->notNull()
                ->default(0)
                ->after('extrainfo')
                ->execute();
        }
        if (!$schema->hasColumn('#__banner_clients', 'metakey')) {
            $schema->addColumn('#__banner_clients', 'metakey')
                ->text()
                ->notNull()
                ->execute();
        }
        if (!$schema->hasColumn('#__banner_clients', 'own_prefix')) {
            $schema->addColumn('#__banner_clients', 'own_prefix')
                ->tinyInteger()
                ->notNull()
                ->default(0)
                ->execute();
        }
        if (!$schema->hasColumn('#__banner_clients', 'metakey_prefix')) {
            $schema->addColumn('#__banner_clients', 'metakey_prefix')
                ->string(255)
                ->notNull()
                ->default('')
                ->execute();
        }
        if (!$schema->hasColumn('#__banner_clients', 'purchase_type')) {
            $schema->addColumn('#__banner_clients', 'purchase_type')
                ->tinyInteger()
                ->notNull()
                ->default(-1)
                ->execute();
        }
        if (!$schema->hasColumn('#__banner_clients', 'track_clicks')) {
            $schema->addColumn('#__banner_clients', 'track_clicks')
                ->tinyInteger()
                ->notNull()
                ->default(-1)
                ->execute();
        }
        if (!$schema->hasColumn('#__banner_clients', 'track_impressions')) {
            $schema->addColumn('#__banner_clients', 'track_impressions')
                ->tinyInteger()
                ->notNull()
                ->default(-1)
                ->execute();
        }

        // Batch index operations
        $schema->table('#__banner_clients')->alter()
            ->addIndex('idx_own_prefix', 'own_prefix')
            ->addIndex('idx_metakey_prefix', 'metakey_prefix')
            ->execute();

        // Update state
        if ($schema->hasColumn('#__banner_clients', 'state')) {
            $this->db->getQuery(true)
                ->update('#__banner_clients')
                ->set(['state' => 1])
                ->execute();
        }
    }

    /**
     * Migrate the banner_tracks table
     */
    protected function migrateBannerTracksTable()
    {
        $schema = $this->db->schema();

        // Rename old table if needed
        if ($schema->tableExists('#__bannertrack') && !$schema->tableExists('#__banner_tracks')) {
            $schema->renameTable('#__bannertrack', '#__banner_tracks');
            $schema->setTableEngine('#__banner_tracks', 'MYISAM');
        }

        if (!$schema->tableExists('#__banner_tracks')) {
            return;
        }

        // Add count column
        if (!$schema->hasColumn('#__banner_tracks', 'count')) {
            $schema->addColumn('#__banner_tracks', 'count')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();

            // Populate count from existing data
            // Populate count from existing data
            $this->db->getQuery(true)
                ->insert('#__banner_tracks')
                ->columns(['track_date', 'track_type', 'banner_id', 'count'])
                ->fromSelect(
                    $this->db->getQuery(true)
                        ->select('track_date, track_type, banner_id')
                        ->select('count(*)', 'count')
                        ->from('#__banner_tracks')
                        ->group('track_date')
                        ->group('track_type')
                        ->group('banner_id')
                )
                ->execute();

            $this->db->getQuery(true)
                ->delete('#__banner_tracks')
                ->where('count', '=', 0)
                ->execute();
        }

        // Modify column and add primary key
        if (!$schema->hasPrimaryKey('#__banner_tracks')) {
            $schema->table('#__banner_tracks')->alter()
                ->modifyColumn('track_date', function ($column) {
                    $column->datetime()->notNull();
                })
                ->addPrimaryKey(['track_date', 'track_type', 'banner_id'])
                ->addIndex('idx_track_date', 'track_date')
                ->addIndex('idx_track_type', 'track_type')
                ->addIndex('idx_banner_id', 'banner_id')
                ->execute();
        } else {
            $schema->table('#__banner_tracks')->alter()
                ->modifyColumn('track_date', function ($column) {
                    $column->datetime()->notNull();
                })
                ->addIndex('idx_track_date', 'track_date')
                ->addIndex('idx_track_type', 'track_type')
                ->addIndex('idx_banner_id', 'banner_id')
                ->execute();
        }
    }
}
