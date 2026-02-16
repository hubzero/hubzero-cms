<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for deleting com_finder
**/
class Migration20140110135511ComFinder extends Base
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
            ->where('element', '=', 'com_finder')
            ->value('extension_id');

        if ($id) {
            $this->deleteComponentEntry('finder');

            $this->deleteModuleEntry('mod_finder');

            $results = $this->db->getQuery(true)
                ->select('id')
                ->from('#__modules')
                ->where('module', '=', 'mod_finder')
                ->loadColumn();

            if (!empty($results)) {
                $this->db->getQuery(true)
                    ->delete('#__modules_menu')
                    ->whereIn('moduleid', $results)
                    ->execute();

                $this->db->getQuery(true)
                    ->delete('#__modules')
                    ->where('module', '=', 'mod_finder')
                    ->execute();
            }

            $this->deletePluginEntry('content', 'finder');

            $this->deletePluginEntry('finder', 'categories');
            $this->deletePluginEntry('finder', 'contacts');
            $this->deletePluginEntry('finder', 'content');
            $this->deletePluginEntry('finder', 'newsfeeds');
            $this->deletePluginEntry('finder', 'weblinks');
        }

        $schema->dropTable('#__finder_filters');
        $schema->dropTable('#__finder_links');

        for ($i = 0; $i < 10; $i++) {
            $schema->dropTable('#__finder_links_terms' . $i);
        }

        foreach (['a', 'b', 'c', 'd', 'e', 'f'] as $suffix) {
            $schema->dropTable('#__finder_links_terms' . $suffix);
        }

        $schema->dropTable('#__finder_taxonomy');
        $schema->dropTable('#__finder_taxonomy_map');
        $schema->dropTable('#__finder_terms');
        $schema->dropTable('#__finder_terms_common');
        $schema->dropTable('#__finder_tokens');
        $schema->dropTable('#__finder_tokens_aggregate');
        $schema->dropTable('#__finder_types');
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
            ->where('element', '=', 'com_finder')
            ->value('extension_id');

        if (!$id) {
            $this->addComponentEntry('finder');

            $this->addPluginEntry('content', 'finder', 0);

            $this->addPluginEntry('finder', 'categories', 0);
            $this->addPluginEntry('finder', 'contacts', 0);
            $this->addPluginEntry('finder', 'content', 0);
            $this->addPluginEntry('finder', 'newsfeeds', 0);
            $this->addPluginEntry('finder', 'weblinks', 0);

            if (!$schema->tableExists('#__finder_filters')) {
                $schema->createTable('#__finder_filters')
                    ->unsignedInteger('filter_id', ['autoIncrement' => true])
                    ->string('title', 255)
                    ->string('alias', 255)
                    ->tinyInteger('state')->default(1)
                    ->datetime('created')->default('0000-00-00 00:00:00')
                    ->unsignedInteger('created_by')
                    ->string('created_by_alias', 255)
                    ->datetime('modified')->default('0000-00-00 00:00:00')
                    ->unsignedInteger('modified_by')->default(0)
                    ->unsignedInteger('checked_out')->default(0)
                    ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                    ->unsignedInteger('map_count')->default(0)
                    ->text('data')
                    ->mediumText('params')->nullable()
                    ->primaryKey('filter_id')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }

            if (!$schema->tableExists('#__finder_links')) {
                $schema->createTable('#__finder_links')
                    ->unsignedInteger('link_id', ['autoIncrement' => true])
                    ->string('url', 255)
                    ->string('route', 255)
                    ->string('title', 255)->nullable()
                    ->string('description', 255)->nullable()
                    ->datetime('indexdate')->default('0000-00-00 00:00:00')
                    ->string('md5sum', 32)->nullable()
                    ->tinyInteger('published')->default(1)
                    ->integer('state')->default(1)
                    ->integer('access')->default(0)
                    ->string('language', 8)
                    ->datetime('publish_start_date')->default('0000-00-00 00:00:00')
                    ->datetime('publish_end_date')->default('0000-00-00 00:00:00')
                    ->datetime('start_date')->default('0000-00-00 00:00:00')
                    ->datetime('end_date')->default('0000-00-00 00:00:00')
                    ->double('list_price')->default(0)
                    ->double('sale_price')->default(0)
                    ->integer('type_id')
                    ->mediumBlob('object')
                    ->primaryKey('link_id')
                    ->index('idx_type', 'type_id')
                    ->index('idx_title', 'title')
                    ->index('idx_md5', 'md5sum')
                    ->index('idx_url', 'url')
                    ->index('idx_published_list', [
                        'published',
                        'state',
                        'access',
                        'publish_start_date',
                        'publish_end_date',
                        'list_price',
                    ])
                    ->index('idx_published_sale', [
                        'published',
                        'state',
                        'access',
                        'publish_start_date',
                        'publish_end_date',
                        'sale_price',
                    ])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }

            $suffixes = array_merge(range(0, 9), ['a', 'b', 'c', 'd', 'e', 'f']);

            foreach ($suffixes as $suffix) {
                if (!$schema->tableExists('#__finder_links_terms' . $suffix)) {
                    $schema->createTable('#__finder_links_terms' . $suffix)
                        ->unsignedInteger('link_id')
                        ->unsignedInteger('term_id')
                        ->float('weight')
                        ->primaryKey(['link_id', 'term_id'])
                        ->index('idx_term_weight', ['term_id', 'weight'])
                        ->index('idx_link_term_weight', ['link_id', 'term_id', 'weight'])
                        ->engine('MyISAM')
                        ->charset('utf8')
                        ->execute();
                }
            }

            if (!$schema->tableExists('#__finder_taxonomy')) {
                $schema->createTable('#__finder_taxonomy')
                    ->unsignedInteger('id', ['autoIncrement' => true])
                    ->unsignedInteger('parent_id')->default(0)
                    ->string('title', 255)
                    ->unsignedTinyInteger('state')->default(1)
                    ->unsignedTinyInteger('access')->default(0)
                    ->unsignedTinyInteger('ordering')->default(0)
                    ->primaryKey('id')
                    ->index('parent_id', 'parent_id')
                    ->index('state', 'state')
                    ->index('ordering', 'ordering')
                    ->index('access', 'access')
                    ->index('idx_parent_published', ['parent_id', 'state', 'access'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }

            if (!$schema->tableExists('#__finder_taxonomy_map')) {
                $schema->createTable('#__finder_taxonomy_map')
                    ->unsignedInteger('link_id')
                    ->unsignedInteger('node_id')
                    ->primaryKey(['link_id', 'node_id'])
                    ->index('link_id', 'link_id')
                    ->index('node_id', 'node_id')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }

            if (!$schema->tableExists('#__finder_terms')) {
                $schema->createTable('#__finder_terms')
                    ->unsignedInteger('term_id', ['autoIncrement' => true])
                    ->string('term', 75)
                    ->string('stem', 75)
                    ->unsignedTinyInteger('common')->default(0)
                    ->unsignedTinyInteger('phrase')->default(0)
                    ->float('weight')->default(0)
                    ->string('soundex', 75)
                    ->integer('links')->default(0)
                    ->primaryKey('term_id')
                    ->uniqueIndex('idx_term', 'term')
                    ->index('idx_term_phrase', ['term', 'phrase'])
                    ->index('idx_stem_phrase', ['stem', 'phrase'])
                    ->index('idx_soundex_phrase', ['soundex', 'phrase'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }

            if (!$schema->tableExists('#__finder_terms_common')) {
                $schema->createTable('#__finder_terms_common')
                    ->string('term', 75)
                    ->string('language', 3)
                    ->index('idx_word_lang', ['term', 'language'])
                    ->index('idx_lang', 'language')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }

            if (!$schema->tableExists('#__finder_tokens')) {
                $schema->createTable('#__finder_tokens')
                    ->string('term', 75)
                    ->string('stem', 75)
                    ->unsignedTinyInteger('common')->default(0)
                    ->unsignedTinyInteger('phrase')->default(0)
                    ->float('weight')->default(1)
                    ->unsignedTinyInteger('context')->default(2)
                    ->index('idx_word', 'term')
                    ->index('idx_context', 'context')
                    ->engine('MEMORY')
                    ->charset('utf8')
                    ->execute();
            }

            if (!$schema->tableExists('#__finder_tokens_aggregate')) {
                $schema->createTable('#__finder_tokens_aggregate')
                    ->unsignedInteger('term_id')
                    ->char('map_suffix', 1)
                    ->string('term', 75)
                    ->string('stem', 75)
                    ->unsignedTinyInteger('common')->default(0)
                    ->unsignedTinyInteger('phrase')->default(0)
                    ->float('term_weight')
                    ->unsignedTinyInteger('context')->default(2)
                    ->float('context_weight')
                    ->float('total_weight')
                    ->index('token', 'term')
                    ->index('keyword_id', 'term_id')
                    ->engine('MEMORY')
                    ->charset('utf8')
                    ->execute();
            }

            if (!$schema->tableExists('#__finder_types')) {
                $schema->createTable('#__finder_types')
                    ->unsignedInteger('id', ['autoIncrement' => true])
                    ->string('title', 100)
                    ->string('mime', 100)
                    ->primaryKey('id')
                    ->uniqueIndex('title', 'title')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }
        }
    }
}
