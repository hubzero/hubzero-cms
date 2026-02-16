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
 * Migration script for group pages updates
 *
**/
class Migration20140108233318ComGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // a bunch of name changes
        if (!$schema->hasColumn('#__xgroups_pages', 'gidNumber')) {
            $schema->alterTable('#__xgroups_pages')
                ->renameColumn('gid', 'gidNumber')->integer()
                ->renameColumn('url', 'alias')->string(100)
                ->renameColumn('porder', 'ordering')->integer()
                ->renameColumn('active', 'state')->integer()->default(1)
                ->addColumn('home')->integer()->default(0)
                ->addColumn('category')->integer()
                ->addColumn('template')->string(100)
                ->execute();

            $schema->alterTable('#__xgroups_pages_hits')
                ->renameColumn('gid', 'gidNumber')->integer()
                ->renameColumn('pid', 'pageid')->integer()
                ->renameColumn('uid', 'userid')->integer()
                ->renameColumn('datetime', 'date')->datetime()
                ->execute();

            $schema->alterTable('#__xgroups_log')
                ->renameColumn('gid', 'gidNumber')->integer()
                ->renameColumn('uid', 'userid')->integer()
                ->execute();
        }

        // create page versions table
        if (!$schema->tableExists('#__xgroups_pages_versions')) {
            $schema->createTable('#__xgroups_pages_versions')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('pageid')->nullable()
                ->integer('version')->nullable()
                ->longText('content')->nullable()
                ->datetime('created')->nullable()
                ->integer('created_by')->nullable()
                ->integer('approved')->default(1)
                ->datetime('approved_on')->nullable()
                ->integer('approved_by')->nullable()
                ->integer('checked_errors')->default(0)
                ->integer('scanned')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // create page category table
        if (!$schema->tableExists('#__xgroups_pages_categories')) {
            $schema->createTable('#__xgroups_pages_categories')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('gidNumber')->nullable()
                ->string('title', 255)->nullable()
                ->string('color', 6)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // create modules table
        if (!$schema->tableExists('#__xgroups_modules')) {
            $schema->createTable('#__xgroups_modules')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('gidNumber')->nullable()
                ->string('title', 255)->default('')
                ->text('content')->nullable()
                ->string('position', 50)->nullable()
                ->integer('ordering')->nullable()
                ->integer('state')->nullable()
                ->datetime('created')->nullable()
                ->integer('created_by')->nullable()
                ->datetime('modified')->nullable()
                ->integer('modified_by')->nullable()
                ->integer('approved')->default(1)
                ->datetime('approved_on')->nullable()
                ->integer('approved_by')->nullable()
                ->integer('checked_errors')->default(0)
                ->integer('scanned')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // create modules menu table
        if (!$schema->tableExists('#__xgroups_modules_menu')) {
            $schema->createTable('#__xgroups_modules_menu')
                ->integer('moduleid')->nullable()
                ->integer('pageid')->nullable()
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // remove content field from pages table
        if ($schema->hasColumn('#__xgroups_pages', 'content')) {
            $this->db->getQuery(true)
                ->insert('#__xgroups_pages_versions')
                ->columns([
                    'pageid',
                    'version',
                    'content',
                    'created',
                    'created_by',
                    'approved',
                    'approved_on',
                    'approved_by',
                ])
                ->fromSelect(
                    $this->db->getQuery(true)
                        ->select([
                            'id as pageid',
                            '1',
                            'content',
                            Expression::now(),
                            '1000',
                            '1',
                            Expression::now(),
                            '1000',
                        ])
                        ->from('#__xgroups_pages')
                )
                ->execute();
        }

        // if the groups table still has the home page overview content
        if ($schema->hasColumn('#__xgroups', 'overview_type')) {
            // get list of groups
            $groups = $this->db->getQuery(true)
                ->select(['gidNumber', 'overview_type', 'overview_content'])
                ->from('#__xgroups')
                ->whereIn('type', [1, 3])
                ->loadObjectList();

            // loop through each group
            foreach ($groups as $group) {
                // if group has overview content
                if (trim((string)$group->overview_content) != '') {
                    // create page to store page info
                    $this->db->getQuery(true)
                        ->insert('#__xgroups_pages')
                        ->columns(['gidNumber', 'alias', 'title', 'ordering', 'state', 'privacy', 'home'])
                        ->values([
                            $group->gidNumber,
                            'home_page',
                            'Home Page',
                            0,
                            1,
                            'default',
                            $group->overview_type
                        ])
                        ->execute();

                    $pageId = $this->db->insertid();

                    // create page version to store page content
                    $this->db->getQuery(true)
                        ->insert('#__xgroups_pages_versions')
                        ->columns([
                            'pageid',
                            'version',
                            'content',
                            'created',
                            'created_by',
                            'approved',
                            'approved_on',
                            'approved_by',
                        ])
                        ->values([
                            $pageId,
                            1,
                            $group->overview_content,
                            Expression::now(),
                            1000,
                            1,
                            Expression::now(),
                            1000
                        ])
                        ->execute();
                }
            }

            if ($schema->hasColumn('#__xgroups_pages', 'content')) {
                $schema->dropColumn('#__xgroups_pages', 'content');
            }

            if ($schema->hasColumn('#__xgroups', 'overview_type')) {
                $schema->alterTable('#__xgroups')
                    ->dropColumn('overview_type')
                    ->dropColumn('overview_content')
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        //add overview type back
        if (!$schema->hasColumn('#__xgroups', 'overview_type')) {
            $schema->alterTable('#__xgroups')
                ->addColumn('overview_type')->integer()
                ->addColumn('overview_content')->text()
                ->execute();
        }

        //move pages back
        if (!$schema->hasColumn('#__xgroups_pages', 'content')) {
            $schema->addColumn('#__xgroups_pages', 'content')->text();

            $query = $this->db->getQuery(true)
                ->select('id')
                ->from('#__xgroups_pages');

            $pages = $query->loadObjectList();

            if ($pages) {
                foreach ($pages as $page) {
                    $content = $this->db->getQuery(true)
                        ->select('content')
                        ->from('#__xgroups_pages_versions')
                        ->where('pageid', '=', $page->id)
                        ->order('version', 'DESC')
                        ->value('content');

                    if ($content) {
                        $this->db->getQuery(true)
                            ->update('#__xgroups_pages')
                            ->set(['content' => $content])
                            ->where('id', '=', $page->id)
                            ->execute();
                    }
                }
            }
        }

        //move home content back to xgroups table and delete those records
        if ($schema->hasColumn('#__xgroups_pages', 'home')) {
            $query = $this->db->getQuery(true)
                ->select('gidNumber')
                ->from('#__xgroups');

            $groups = $query->loadObjectList();

            if ($groups) {
                foreach ($groups as $group) {
                    $page = $this->db->getQuery(true)
                        ->select('content, home')
                        ->from('#__xgroups_pages')
                        ->where('gidNumber', '=', $group->gidNumber)
                        ->where('alias', '=', 'home_page')
                        ->first();

                    if ($page) {
                        $this->db->getQuery(true)
                            ->update('#__xgroups')
                            ->set([
                                'overview_content' => $page->content,
                                'overview_type' => $page->home
                            ])
                            ->where('gidNumber', '=', $group->gidNumber)
                            ->execute();
                    }
                }
            }

            $this->db->getQuery(true)
                ->delete('#__xgroups_pages')
                ->where('alias', '=', 'home_page')
                ->execute();
        }

        // a bunch of name changes
        if ($schema->hasColumn('#__xgroups_pages', 'gidNumber')) {
            $schema->alterTable('#__xgroups_pages')
                ->renameColumn('gidNumber', 'gid')->integer()
                ->renameColumn('alias', 'url')->string(100)
                ->renameColumn('ordering', 'porder')->integer()
                ->renameColumn('state', 'active')->integer()
                ->dropColumn('home')
                ->dropColumn('category')
                ->dropColumn('template')
                ->execute();

            $schema->alterTable('#__xgroups_pages_hits')
                ->renameColumn('gidNumber', 'gid')->integer()
                ->renameColumn('pageid', 'pid')->integer()
                ->renameColumn('userid', 'uid')->integer()
                ->renameColumn('date', 'datetime')->datetime()
                ->execute();

            $schema->alterTable('#__xgroups_log')
                ->renameColumn('gidNumber', 'gid')->integer()
                ->renameColumn('userid', 'uid')->integer()
                ->execute();
        }

        // delete page version table
        $schema->dropTable('#__xgroups_pages_versions');

        // delete categories table
        $schema->dropTable('#__xgroups_pages_categories');

        // delete modules table
        $schema->dropTable('#__xgroups_modules');

        // delete modules menu table
        $schema->dropTable('#__xgroups_modules_menu');
    }
}
