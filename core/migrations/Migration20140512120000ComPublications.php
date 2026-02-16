<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting up publication building blocks
 *
 */
class Migration20140512120000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Set up curation
        if (!$schema->tableExists('#__publication_curation')) {
            $schema->createTable('#__publication_curation')
                ->integer('id', ['autoIncrement' => true])
                ->integer('publication_id')->default(0)
                ->integer('publication_version_id')->default(0)
                ->datetime('updated')->nullable()
                ->integer('updated_by')->default(0)
                ->text('update')->nullable()
                ->datetime('reviewed')->nullable()
                ->integer('reviewed_by')->default(0)
                ->text('review')->nullable()
                ->integer('review_status')->default(0)
                ->string('block', 100)->default('')
                ->integer('step')->default(0)
                ->integer('element')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Set up curation blocks
        if (!$schema->tableExists('#__publication_blocks')) {
            $schema->createTable('#__publication_blocks')
                ->integer('id', ['autoIncrement' => true])
                ->string('block', 100)->default('')
                ->string('label', 100)->default('')
                ->string('title', 255)->default('')
                ->integer('status')->default(0)
                ->integer('minimum')->default(0)
                ->integer('maximum')->default(0)
                ->integer('ordering')->default(0)
                ->text('params')->nullable()
                ->text('manifest')->nullable()
                ->primaryKey('id')
                ->uniqueIndex('block', 'block')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            // Set default blocks
            $blocks = [
                [
                    'id' => 1,
                    'block' => 'content',
                    'label' => 'Content',
                    'title' => 'Publication Content',
                    'status' => 1,
                    'minimum' => 1,
                    'maximum' => 5,
                    'ordering' => 1,
                    'params' => '',
                    'manifest' => '',
                ],
                [
                    'id' => 2,
                    'block' => 'description',
                    'label' => 'Description',
                    'title' => 'Publication Description',
                    'status' => 1,
                    'minimum' => 1,
                    'maximum' => 5,
                    'ordering' => 2,
                    'params' => '',
                    'manifest' => '',
                ],
                [
                    'id' => 3,
                    'block' => 'authors',
                    'label' => 'Authors',
                    'title' => 'Publication Authors',
                    'status' => 1,
                    'minimum' => 1,
                    'maximum' => 1,
                    'ordering' => 3,
                    'params' => '',
                    'manifest' => '',
                ],
                [
                    'id' => 4,
                    'block' => 'extras',
                    'label' => 'Extras',
                    'title' => 'Publication Extra Content',
                    'status' => 1,
                    'minimum' => 0,
                    'maximum' => 1,
                    'ordering' => 4,
                    'params' => 'default=1',
                    'manifest' => '',
                ],
                [
                    'id' => 5,
                    'block' => 'license',
                    'label' => 'License',
                    'title' => 'Publication Tags',
                    'status' => 1,
                    'minimum' => 0,
                    'maximum' => 1,
                    'ordering' => 5,
                    'params' => 'default=1',
                    'manifest' => '',
                ],
                [
                    'id' => 6,
                    'block' => 'tags',
                    'label' => 'Tags',
                    'title' => 'Publication Tags',
                    'status' => 1,
                    'minimum' => 0,
                    'maximum' => 1,
                    'ordering' => 6,
                    'params' => 'default=1',
                    'manifest' => '',
                ],
                [
                    'id' => 7,
                    'block' => 'notes',
                    'label' => 'Notes',
                    'title' => 'Version Release Notes',
                    'status' => 1,
                    'minimum' => 0,
                    'maximum' => 1,
                    'ordering' => 7,
                    'params' => 'default=1',
                    'manifest' => '',
                ],
                [
                    'id' => 8,
                    'block' => 'review',
                    'label' => 'Review',
                    'title' => 'Publication Review',
                    'status' => 1,
                    'minimum' => 1,
                    'maximum' => 1,
                    'ordering' => 8,
                    'params' => 'default=1',
                    'manifest' => ''
                ]
            ];

            foreach ($blocks as $block) {
                $this->db->getQuery(true)
                    ->insert('#__publication_blocks')
                    ->set($block)
                    ->execute();
            }
        }

        // Set up handlers
        if (!$schema->tableExists('#__publication_handlers')) {
            $schema->createTable('#__publication_handlers')
                ->integer('id', ['autoIncrement' => true])
                ->string('name', 100)->default('')
                ->string('label', 100)->default('')
                ->string('title', 255)->default('')
                ->integer('status')->default(0)
                ->text('about')->nullable()
                ->text('params')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__publication_handlers')
                ->set([
                    'id'     => 1,
                    'name'   => 'imageviewer',
                    'label'  => 'Image Viewer',
                    'title'  => 'Image Gallery Presenter',
                    'status' => 1,
                    'about'  => '',
                    'params' => ''
                ])
                ->execute();
        }

        // Add curation field
        if (!$schema->hasColumn('#__publication_versions', 'curation')) {
            $schema->addColumn('#__publication_versions', 'curation')->text()->nullable()->execute();
        }
        // Add reviewed field
        if (!$schema->hasColumn('#__publication_versions', 'reviewed')) {
            $schema->addColumn('#__publication_versions', 'reviewed')->datetime()->nullable()->execute();
        }
        // Add reviewed_by field
        if (!$schema->hasColumn('#__publication_versions', 'reviewed_by')) {
            $schema->addColumn('#__publication_versions', 'reviewed_by')->integer()->execute();
        }
        // Add curation field
        if (!$schema->hasColumn('#__publication_master_types', 'curation')) {
            $schema->addColumn('#__publication_master_types', 'curation')->text()->nullable()->execute();
        }
        // Add curation group field
        if (!$schema->hasColumn('#__publication_master_types', 'curatorgroup')) {
            $schema->addColumn('#__publication_master_types', 'curatorgroup')->integer()->execute();
        }
        // Add element field
        if (!$schema->hasColumn('#__publication_attachments', 'element_id')) {
            $schema->addColumn('#__publication_attachments', 'element_id')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__publication_blocks');
        $schema->dropTable('#__publication_curation');
        $schema->dropTable('#__publication_handlers');
    }
}
