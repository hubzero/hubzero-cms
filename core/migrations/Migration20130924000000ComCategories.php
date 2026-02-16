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
 * Migration script for joomla conversion of sections to categories
**/
class Migration20130924000000ComCategories extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();
        // $schema->setTableEngine('#__categories', 'MYISAM');

        // Batch column modifications
        $schema->table('#__categories')->alter()
            ->modifyColumn('parent_id')->integer()->unsigned()->notNull()->default(0)
            ->modifyColumn('alias')->string(255)->notNull()->default('')
            ->modifyColumn('access')->integer()->unsigned()->notNull()->default(0)
            ->modifyColumn('description')->mediumText()->notNull()
            ->modifyColumn('params')->text()->notNull()
            ->execute();

        // Add new columns with position (MySQL-specific)
        $schema->alterTable('#__categories')
            ->addColumnIfNotExists('lft')
                ->integer()
                ->notNull()
                ->default(0)
                ->after('parent_id')
                ->comment('Nested set lft.')
            ->addColumnIfNotExists('rgt')->integer()->notNull()->default(0)->after('lft')->comment('Nested set rgt.')
            ->addColumnIfNotExists('asset_id')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->after('id')
                ->comment('FK to the #__assets table.')
            ->addColumnIfNotExists('level')->integer()->unsigned()->notNull()->default(0)->after('rgt')
            ->addColumnIfNotExists('path')->string(255)->notNull()->default('')->after('level')
            ->addColumnIfNotExists('extension')->string(50)->notNull()->default('')->after('path')
            ->addColumnIfNotExists('note')->string(255)->notNull()->default('')->after('alias')
            ->addColumnIfNotExists('metadesc')
                ->string(1024)
                ->notNull()
                ->after('params')
                ->comment('The meta description for the page.')
            ->addColumnIfNotExists('metakey')
                ->string(1024)
                ->notNull()
                ->after('metadesc')
                ->comment('The meta keywords for the page.')
            ->addColumnIfNotExists('metadata')
                ->string(2048)
                ->notNull()
                ->after('metakey')
                ->comment('JSON encoded metadata properties.')
            ->addColumnIfNotExists('created_user_id')->integer()->unsigned()->notNull()->default(0)->after('metadata')
            ->addColumnIfNotExists('created_time')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->after('created_user_id')
            ->addColumnIfNotExists('modified_user_id')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->after('created_time')
            ->addColumnIfNotExists('modified_time')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->after('modified_user_id')
            ->addColumnIfNotExists('hits')->integer()->unsigned()->notNull()->default(0)->after('modified_time')
            ->addColumnIfNotExists('language')->char(7)->notNull()->after('hits')
            ->execute();

        // Batch index operations
        $schema->table('#__categories')->alter()
            ->dropIndex('cat_idx')
            ->addIndex('idx_extension_published_access', ['extension', 'published', 'access'])
            ->addIndex('idx_alias', 'alias')
            ->addIndex('idx_path', 'path')
            ->addIndex('idx_left_right', ['lft', 'rgt'])
            ->addIndex('idx_language', 'language')
            ->execute();

        // Data migration from sections to categories
        if ($schema->hasColumn('#__categories', 'section')) {
            $this->migrateDataFromSections();

            // Drop obsolete columns
            $schema->table('#__categories')->alter()
                ->dropColumn('ordering')
                ->dropColumn('image')
                ->dropColumn('image_position')
                ->dropColumn('editor')
                ->dropColumn('count')
                ->dropColumn('name')
                ->dropColumn('section')
                ->execute();
        } else {
            // Just drop columns if they exist
            if ($schema->hasColumn('#__categories', 'ordering')) {
                $schema->dropColumn('#__categories', 'ordering');
            }
            if ($schema->hasColumn('#__categories', 'image')) {
                $schema->dropColumn('#__categories', 'image');
            }
            if ($schema->hasColumn('#__categories', 'image_position')) {
                $schema->dropColumn('#__categories', 'image_position');
            }
            if ($schema->hasColumn('#__categories', 'editor')) {
                $schema->dropColumn('#__categories', 'editor');
            }
            if ($schema->hasColumn('#__categories', 'count')) {
                $schema->dropColumn('#__categories', 'count');
            }
            if ($schema->hasColumn('#__categories', 'name')) {
                $schema->dropColumn('#__categories', 'name');
            }
        }
    }

    /**
     * Migrate data from sections to categories
     */
    protected function migrateDataFromSections()
    {
        // @FIXME: should we fix up references in the data to com_banner(s) here?

        // @FIXME: should we fix up references in the data to com_banner(s) here?
        $this->db->getQuery(true)
            ->update('#__categories')
            ->set(['extension' => Expression::column('section')])
            ->where(Expression::substring('section', 1, 3), '=', 'com')
            ->execute();

        $this->db->getQuery(true)
            ->update('#__categories')
            ->set(['extension' => 'com_content'])
            ->where(Expression::substring('section', 1, 3), '!=', 'com')
            ->execute();

        $this->db->getQuery(true)
            ->update('#__categories')
            ->set(['parent_id' => 0])
            ->where(Expression::substring('section', 1, 3), '=', 'com')
            ->execute();

        $this->db->getQuery(true)
            ->update('#__categories')
            ->set(['parent_id' => Expression::column('section')])
            ->where(Expression::substring('section', 1, 3), '!=', 'com')
            ->execute();

        $this->db->getQuery(true)
            ->update('#__categories')
            ->set(['alias' => Expression::lower('title')])
            ->beginOrGroup()
                ->whereIsNull('alias')
                ->orWhere('alias', '=', '')
            ->endAndGroup()
            ->execute();

        // UPDATE #__categories SET level=1
        $this->db->getQuery(true)
            ->update('#__categories')
            ->set(['level' => 1])
            ->execute();

        // Insert default "uncategorised" categories
        // (set access to 0, because we'll increment it later with all the old categories)
        // Insert default "uncategorised" categories
        $defaults = [
            ['com_content', '2010-06-28 13:26:37', '{"target":"","image":""}'],
            ['com_banners', '2010-06-28 13:27:35', '{"target":"","image":"","foobar":""}'],
            ['com_contact', '2010-06-28 13:27:57', '{"target":"","image":""}'],
            ['com_newsfeeds', '2010-06-28 13:28:15', '{"target":"","image":""}'],
            ['com_weblinks', '2010-06-28 13:28:33', '{"target":"","image":""}']
        ];

        foreach ($defaults as $default) {
            $this->db->getQuery(true)
                ->insert('#__categories')
                ->columns([
                    'parent_id',
                    'lft',
                    'rgt',
                    'level',
                    'path',
                    'extension',
                    'title',
                    'alias',
                    'note',
                    'description',
                    'published',
                    'checked_out',
                    'checked_out_time',
                    'access',
                    'params',
                    'metadesc',
                    'metakey',
                    'metadata',
                    'created_user_id',
                    'created_time',
                    'modified_user_id',
                    'modified_time',
                    'hits',
                    'language'
                ])
                ->values([
                    0,
                    0,
                    0,
                    1,
                    'uncategorised',
                    $default[0],
                    'Uncategorised',
                    'uncategorised',
                    '',
                    '',
                    1,
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    $default[2],
                    '',
                    '',
                    '{"page_title":"","author":"","robots":""}',
                    62,
                    $default[1],
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    '*'
                ])
                ->execute();
        }

        // Grab sections and insert them into categories
        $query = $this->db->getQuery(true);
        $query->select('*')
            ->from('#__sections');
        $results = $query->loadObjectList();

        if (count($results) > 0) {
            foreach ($results as $r) {
                // Collapse duplicate section/categories down into one level
                $query = $this->db->getQuery(true);
                $query->select('id')
                    ->from('#__categories')
                    ->where('alias', '=', $r->alias);
                if ($$query->exists()) {
                    // Set any categories that were in recently added section
                    // to have that new category id as parent_id
                    $query = $this->db->getQuery(true);
                    $query->update('#__categories')
                        ->set(['parent_id' => 0])
                        ->where('section', '=', $r->id);
                    $query->execute();

                    continue;
                }

                $query = $this->db->getQuery(true);
                $query->insert('#__categories')
                    ->columns([
                        'parent_id',
                        'lft',
                        'rgt',
                        'level',
                        'path',
                        'extension',
                        'title',
                        'alias',
                        'note',
                        'description',
                        'published',
                        'checked_out',
                        'checked_out_time',
                        'access',
                        'params',
                        'metadesc',
                        'metakey',
                        'metadata',
                        'created_user_id',
                        'created_time',
                        'modified_user_id',
                        'modified_time',
                        'hits',
                        'language'
                    ])
                    ->values([
                        0,
                        0,
                        0,
                        1,
                        $r->alias,
                        'com_content',
                        $r->title,
                        $r->alias,
                        '',
                        $r->description,
                        $r->published,
                        $r->checked_out,
                        $r->checked_out_time,
                        $r->access,
                        $r->params,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    ]);
                $query->execute();

                // Get last id
                $id = $this->db->insertid();

                // Set any categories that were in recently added section to have that new category id as parent_id
                $query = $this->db->getQuery(true);
                $query->update('#__categories')
                    ->set(['parent_id' => $id, 'level' => 2])
                    ->where('section', '=', $r->id);
                $query->execute();
            }
        }

        // Insert root category and set all 1st level categories to point to it
        $this->db->getQuery(true)
            ->insert('#__categories')
            ->columns([
                'asset_id',
                'parent_id',
                'lft',
                'rgt',
                'level',
                'path',
                'extension',
                'title',
                'alias',
                'note',
                'description',
                'published',
                'checked_out',
                'checked_out_time',
                'access',
                'params',
                'metadesc',
                'metakey',
                'metadata',
                'created_user_id',
                'created_time',
                'modified_user_id',
                'modified_time',
                'hits',
                'language'
            ])
            ->values([
                0,
                0,
                0,
                0,
                0,
                '',
                'system',
                'ROOT',
                'root',
                '',
                '',
                1,
                0,
                '0000-00-00 00:00:00',
                1,
                '{}',
                '',
                '',
                '',
                0,
                '2009-10-18 16:07:09',
                0,
                '0000-00-00 00:00:00',
                0,
                '*'
            ])
            ->execute();

        $rootId = $this->db->insertid();

        $query = $this->db->getQuery(true);
        $query->update('#__categories')
            ->set(['parent_id' => $rootId])
            ->where('parent_id', '=', 0)
            ->where('id', '!=', $rootId);
        $query->execute();

        // Fix up "path" field
        $query = $this->db->getQuery(true);
        $query->update('#__categories')
            ->set(['path' => 'alias'], true)
            ->beginOrGroup()
                ->whereIsNull('path')
                ->orWhere('path', '=', '')
            ->endAndGroup()
            ->where('level', '=', 1);
        $query->execute();

        $query = $this->db->getQuery(true);
        $query->select('*')
            ->from('#__categories')
            ->beginOrGroup()
                ->whereIsNull('path')
                ->orWhere('path', '=', '')
            ->endAndGroup()
            ->where('level', '=', 2);
        $results = $query->loadObjectList();

        if (count($results) > 0) {
            $aliases = $this->db->getQuery(true)
                ->select(['id', 'alias'])
                ->from('#__categories')
                ->pluck('alias', 'id');

            foreach ($results as $r) {
                // Get the parent item alias
                $alias = $aliases[$r->parent_id] ?? null;

                // Build path var
                $path = $alias . '/' . $r->alias;

                // Save the sub-category path
                $query = $this->db->getQuery(true);
                $query->update('#__categories')
                    ->set(['path' => $this->db->quote($path)])
                    ->where('id', '=', $r->id);
                $query->execute();
            }
        }
    }
}
