<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for new joomla search/finder tables
 *
 * Note: MEMORY engine tables become regular tables on SQLite (engine ignored).
 * This is acceptable for development/testing since MEMORY tables are temporary
 * and volatile (lost on server restart) anyway.
 */
class Migration20130924000006ComFinder extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Table: finder_filters
        if (!$schema->tableExists('#__finder_filters')) {
            $schema->create('#__finder_filters')
                ->increments('filter_id')
                ->primaryKey('filter_id')
                ->string('title', 255)->nullable(false)
                ->string('alias', 255)->nullable(false)
                ->tinyInteger('state')->nullable(false)->default(1)
                ->datetime('created')->nullable(false)->default('0000-00-00 00:00:00')
                ->unsignedInteger('created_by')->nullable(false)
                ->string('created_by_alias', 255)->nullable(false)
                ->datetime('modified')->nullable(false)->default('0000-00-00 00:00:00')
                ->unsignedInteger('modified_by')->nullable(false)->default(0)
                ->unsignedInteger('checked_out')->nullable(false)->default(0)
                ->datetime('checked_out_time')->nullable(false)->default('0000-00-00 00:00:00')
                ->unsignedInteger('map_count')->nullable(false)->default(0)
                ->text('data')->nullable(false)
                ->mediumText('params')->nullable()
                ->engine('MYISAM')
                ->execute();
        }

        // Table: finder_links
        if (!$schema->tableExists('#__finder_links')) {
            $schema->create('#__finder_links')
                ->increments('link_id')
                ->primaryKey('link_id')
                ->string('url', 255)->nullable(false)
                ->string('route', 255)->nullable(false)
                ->string('title', 255)->nullable()
                ->string('description', 255)->nullable()
                ->datetime('indexdate')->nullable(false)->default('0000-00-00 00:00:00')
                ->string('md5sum', 32)->nullable()
                ->unsignedInteger('published')->nullable(false)->default(1)
                ->integer('state', 5)->nullable()->default(1)
                ->integer('access', 5)->nullable()->default(0)
                ->string('language', 8)->nullable(false)
                ->datetime('publish_start_date')->nullable(false)->default('0000-00-00 00:00:00')
                ->datetime('publish_end_date')->nullable(false)->default('0000-00-00 00:00:00')
                ->datetime('start_date')->nullable(false)->default('0000-00-00 00:00:00')
                ->datetime('end_date')->nullable(false)->default('0000-00-00 00:00:00')
                ->double('list_price')->unsigned()->nullable(false)->default(0)
                ->double('sale_price')->unsigned()->nullable(false)->default(0)
                ->integer('type_id' /*, 11 */)->nullable(false)
                ->mediumBlob('object')->nullable(false)
                ->index('idx_type', 'type_id')
                ->index('idx_title', 'title')
                ->index('idx_md5', 'md5sum')
                ->index('idx_url', [['url', 75]])  // Prefix index: url(75)
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
                ->engine('MYISAM')
                ->execute();
        }

        // Tables: finder_links_terms[0-9a-f] (16 tables with same structure)
        $termSuffixes = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];
        foreach ($termSuffixes as $suffix) {
            $tableName = '#__finder_links_terms' . $suffix;
            if (!$schema->tableExists($tableName)) {
                $schema->create($tableName)
                    ->unsignedInteger('link_id')->nullable(false)
                    ->unsignedInteger('term_id')->nullable(false)
                    ->float('weight')->unsigned()->nullable(false)
                    ->primaryKey(['link_id', 'term_id'])
                    ->index('idx_term_weight', ['term_id', 'weight'])
                    ->index('idx_link_term_weight', ['link_id', 'term_id', 'weight'])
                    ->engine('MYISAM')
                    ->execute();
            }
        }

        // Table: finder_taxonomy
        if (!$schema->tableExists('#__finder_taxonomy')) {
            $schema->create('#__finder_taxonomy')
                ->increments('id')
                ->primaryKey('id')
                ->unsignedInteger('parent_id')->nullable(false)->default(0)
                ->string('title', 255)->nullable(false)
                ->unsignedTinyInteger('state')->nullable(false)->default(1)
                ->unsignedTinyInteger('access')->nullable(false)->default(0)
                ->unsignedTinyInteger('ordering')->nullable(false)->default(0)
                ->index('parent_id', 'parent_id')
                ->index('state', 'state')
                ->index('ordering', 'ordering')
                ->index('access', 'access')
                ->index('idx_parent_published', ['parent_id', 'state', 'access'])
                ->engine('MYISAM')
                ->execute();

            // Insert root taxonomy entry
            // Insert root taxonomy entry
            // Insert root taxonomy entry
            // Insert root taxonomy entry
            $this->db->getQuery(true)
                ->insert('#__finder_taxonomy')
                ->set([
                    'id'        => 1,
                    'parent_id' => 0,
                    'title'     => 'ROOT',
                    'state'     => 0,
                    'access'    => 0,
                    'ordering'  => 0
                ])
                ->execute();
        }

        // Table: finder_taxonomy_map
        if (!$schema->tableExists('#__finder_taxonomy_map')) {
            $schema->create('#__finder_taxonomy_map')
                ->unsignedInteger('link_id')->nullable(false)
                ->unsignedInteger('node_id')->nullable(false)
                ->primaryKey(['link_id', 'node_id'])
                ->index('link_id', 'link_id')
                ->index('node_id', 'node_id')
                ->engine('MYISAM')
                ->execute();
        }

        // Table: finder_terms
        if (!$schema->tableExists('#__finder_terms')) {
            $schema->create('#__finder_terms')
                ->increments('term_id')
                ->primaryKey('term_id')
                ->string('term', 75)->nullable(false)
                ->string('stem', 75)->nullable(false)
                ->unsignedTinyInteger('common')->nullable(false)->default(0)
                ->unsignedTinyInteger('phrase')->nullable(false)->default(0)
                ->float('weight')->unsigned()->nullable(false)->default(0)
                ->string('soundex', 75)->nullable(false)
                ->integer('links', 10)->nullable(false)->default(0)
                ->uniqueIndex('idx_term', 'term')
                ->index('idx_term_phrase', ['term', 'phrase'])
                ->index('idx_stem_phrase', ['stem', 'phrase'])
                ->index('idx_soundex_phrase', ['soundex', 'phrase'])
                ->engine('MYISAM')
                ->execute();
        }

        // Table: finder_terms_common (no primary key)
        if (!$schema->tableExists('#__finder_terms_common')) {
            $schema->create('#__finder_terms_common')
                ->string('term', 75)->nullable(false)
                ->string('language', 3)->nullable(false)
                ->index('idx_word_lang', ['term', 'language'])
                ->index('idx_lang', 'language')
                ->engine('MYISAM')
                ->execute();

            // Insert common terms
            $this->getCommonTermsInsertQuery();
        }

        // Table: finder_tokens (MEMORY engine - becomes regular table on SQLite)
        if (!$schema->tableExists('#__finder_tokens')) {
            $schema->create('#__finder_tokens')
                ->string('term', 75)->nullable(false)
                ->string('stem', 75)->nullable(false)
                ->unsignedTinyInteger('common')->nullable(false)->default(0)
                ->unsignedTinyInteger('phrase')->nullable(false)->default(0)
                ->float('weight')->unsigned()->nullable(false)->default(1)
                ->unsignedTinyInteger('context')->nullable(false)->default(2)
                ->index('idx_word', 'term')
                ->index('idx_context', 'context')
                ->engine('MEMORY')
                ->execute();
        }

        // Table: finder_tokens_aggregate (MEMORY engine - becomes regular table on SQLite)
        if (!$schema->tableExists('#__finder_tokens_aggregate')) {
            $schema->create('#__finder_tokens_aggregate')
                ->unsignedInteger('term_id')->nullable(false)
                ->char('map_suffix', 1)->nullable(false)
                ->string('term', 75)->nullable(false)
                ->string('stem', 75)->nullable(false)
                ->unsignedTinyInteger('common')->nullable(false)->default(0)
                ->unsignedTinyInteger('phrase')->nullable(false)->default(0)
                ->float('term_weight')->unsigned()->nullable(false)
                ->unsignedTinyInteger('context')->nullable(false)->default(2)
                ->float('context_weight')->unsigned()->nullable(false)
                ->float('total_weight')->unsigned()->nullable(false)
                ->index('token', 'term')
                ->index('keyword_id', 'term_id')
                ->engine('MEMORY')
                ->execute();
        }

        // Table: finder_types
        if (!$schema->tableExists('#__finder_types')) {
            $schema->create('#__finder_types')
                ->increments('id')
                ->primaryKey('id')
                ->string('title', 100)->nullable(false)
                ->string('mime', 100)->nullable(false)
                ->uniqueIndex('title', 'title')
                ->engine('MYISAM')
                ->execute();
        }
    }

    /**
     * Get the INSERT query for common terms
     *
     * @return string
     */
    protected function getCommonTermsInsertQuery()
    {
        $terms = [
            'a', 'about', 'after', 'ago', 'all', 'am', 'an', 'and', 'ani', 'any', 'are', "aren't", 'as', 'at',
            'be', 'but', 'by', 'for', 'from', 'get', 'go', 'how', 'if', 'in', 'into', 'is', "isn't", 'it', 'its',
            'me', 'more', 'most', 'must', 'my', 'new', 'no', 'none', 'not', 'noth', 'nothing', 'of', 'off',
            'often', 'old', 'on', 'onc', 'once', 'onli', 'only', 'or', 'other', 'our', 'ours', 'out', 'over', 'page',
            'she', 'should', 'small', 'so', 'some', 'than', 'thank', 'that', 'the', 'their', 'theirs', 'them',
            'then', 'there', 'these', 'they', 'this', 'those', 'thus', 'time', 'times', 'to', 'too', 'true',
            'under', 'until', 'up', 'upon', 'use', 'user', 'users', 'veri', 'version', 'very', 'via', 'want',
            'was', 'way', 'were', 'what', 'when', 'where', 'whi', 'which', 'who', 'whom', 'whose', 'why', 'wide',
            'will', 'with', 'within', 'without', 'would', 'yes', 'yet', 'you', 'your', 'yours'
        ];

        foreach ($terms as $term) {
            $this->db->getQuery(true)
                ->insert('#__finder_terms_common')
                ->set([
                    'term'     => $term,
                    'language' => 'en'
                ])
                ->execute();
        }
    }
}
