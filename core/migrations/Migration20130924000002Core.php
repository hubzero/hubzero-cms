<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for migrating joomla content
**/
class Migration20130924000002Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Create assets table (all of this will only run the first time the table is created)
        if (!$schema->tableExists('#__assets')) {
            $schema->createTable('#__assets')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('parent_id')->default(0)
                ->integer('lft')->default(0)
                ->integer('rgt')->default(0)
                ->unsignedInteger('level')
                ->string('name', 50)
                ->string('title', 100)
                ->string('rules', 5120)
                ->primaryKey('id')
                ->uniqueIndex('idx_asset_name', 'name')
                ->index('idx_lft_rgt', ['lft', 'rgt'])
                ->index('idx_parent_id', 'parent_id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->collate('utf8_general_ci')
                ->execute();

            // Insert some default values
            $rootRules = '{"core.login.site":{"1":1,"6":1,"2":1},"core.login.admin":{"6":1},'
                . '"core.admin":{"8":1},"core.manage":{"7":1},"core.create":{"6":1,"3":1},'
                . '"core.delete":{"6":1},"core.edit":{"6":1,"4":1},"core.edit.state":{"6":1,"5":1},'
                . '"core.edit.own":{"6":1,"3":1}}';
            $r3 = '{"core.admin":{"7":1},"core.manage":{"6":1},"core.create":[],'
                . '"core.delete":[],"core.edit":[],"core.edit.state":[]}';
            $r7 = '{"core.admin":{"7":1},"core.manage":{"6":1},"core.create":[],'
                . '"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}';
            $r8 = '{"core.admin":{"7":1},"core.manage":{"6":1},"core.create":{"3":1},'
                . '"core.delete":[],"core.edit":{"4":1},"core.edit.state":{"5":1},"core.edit.own":[]}';
            $r10 = '{"core.admin":{"7":0},"core.manage":{"7":0},"core.delete":{"7":0},"core.edit.state":{"7":0}}';
            $r11 = '{"core.admin":{"7":1},"core.manage":[],"core.create":[],'
                . '"core.delete":[],"core.edit":[],"core.edit.state":[]}';
            $r19 = '{"core.admin":{"7":1},"core.manage":{"6":1},"core.create":[],'
                . '"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}';
            $r24 = '{"core.admin":{"7":1},"core.manage":[],"core.create":[],'
                . '"core.delete":[],"core.edit":[],"core.edit.own":{"6":1},"core.edit.state":[]}';
            $r25 = '{"core.admin":{"7":1},"core.manage":{"6":1},"core.create":{"3":1},'
                . '"core.delete":[],"core.edit":{"4":1},"core.edit.state":{"5":1},"core.edit.own":[]}';
            $this->db->getQuery(true)
                ->insert('#__assets')
                ->columns(['id', 'parent_id', 'lft', 'rgt', 'level', 'name', 'title', 'rules'])
                ->values([1, 0, 0, 0, 0, 'root.1', 'Root Asset', $rootRules])
                ->values([2, 1, 0, 0, 1, 'com_admin', 'com_admin', '{}'])
                ->values([3, 1, 0, 0, 1, 'com_banners', 'com_banners', $r3])
                ->values([4, 1, 0, 0, 1, 'com_cache', 'com_cache', '{"core.admin":{"7":1},"core.manage":{"7":1}}'])
                ->values([5, 1, 0, 0, 1, 'com_checkin', 'com_checkin', '{"core.admin":{"7":1},"core.manage":{"7":1}}'])
                ->values([6, 1, 0, 0, 1, 'com_config', 'com_config', '{}'])
                ->values([7, 1, 0, 0, 1, 'com_contact', 'com_contact', $r7])
                ->values([8, 1, 0, 0, 1, 'com_content', 'com_content', $r8])
                ->values([9, 1, 0, 0, 1, 'com_cpanel', 'com_cpanel', '{}'])
                ->values([10, 1, 0, 0, 1, 'com_installer', 'com_installer', $r10])
                ->values([11, 1, 0, 0, 1, 'com_languages', 'com_languages', $r11])
                ->values([12, 1, 0, 0, 1, 'com_login', 'com_login', '{}'])
                ->values([13, 1, 0, 0, 1, 'com_mailto', 'com_mailto', '{}'])
                ->values([14, 1, 0, 0, 1, 'com_massmail', 'com_massmail', '{}'])
                ->values([
                    15,
                    1,
                    0,
                    0,
                    1,
                    'com_media',
                    'com_media',
                    '{"core.admin":{"7":1},"core.manage":{"6":1},"core.create":{"3":1},"core.delete":{"5":1}}',
                ])
                ->values([16, 1, 0, 0, 1, 'com_menus', 'com_menus', $r11])
                ->values([
                    17,
                    1,
                    0,
                    0,
                    1,
                    'com_messages',
                    'com_messages',
                    '{"core.admin":{"7":1},"core.manage":{"7":1}}',
                ])
                ->values([18, 1, 0, 0, 1, 'com_modules', 'com_modules', $r11])
                ->values([19, 1, 0, 0, 1, 'com_newsfeeds', 'com_newsfeeds', $r19])
                ->values([
                    20,
                    1,
                    0,
                    0,
                    1,
                    'com_plugins',
                    'com_plugins',
                    '{"core.admin":{"7":1},"core.manage":[],"core.edit":[],"core.edit.state":[]}',
                ])
                ->values([21, 1, 0, 0, 1, 'com_redirect', 'com_redirect', '{"core.admin":{"7":1},"core.manage":[]}'])
                ->values([22, 1, 0, 0, 1, 'com_search', 'com_search', '{"core.admin":{"7":1},"core.manage":{"6":1}}'])
                ->values([23, 1, 0, 0, 1, 'com_templates', 'com_templates', $r11])
                ->values([24, 1, 0, 0, 1, 'com_users', 'com_users', $r24])
                ->values([25, 1, 0, 0, 1, 'com_weblinks', 'com_weblinks', $r25])
                ->values([26, 1, 0, 0, 1, 'com_wrapper', 'com_wrapper', '{}'])
                ->execute();

            // Insert all components as assets (parent is 0 because we don't need more than
            // 1 entry per component - i.e. no sub items used for menus in 1.5)
            // Insert all components as assets (parent is 0 because we don't need more than
            // 1 entry per component - i.e. no sub items used for menus in 1.5)
            $query = $this->db->getQuery(true);
            $query->select('*')
                ->from('#__components')
                ->where('parent', '=', 0);
            $components = $query->loadObjectList();

            if (count($components) > 0) {
                // Build default ruleset
                $defaulRules = array(
                    "core.admin"      => array(
                        "7" => 1
                        ),
                    "core.manage"     => array(
                        "6" => 1
                        ),
                    "core.create"     => array(),
                    "core.delete"     => array(),
                    "core.edit"       => array(),
                    "core.edit.state" => array()
                    );

                foreach ($components as $com) {
                    // Make sure it isn't already in there
                    if (
                        $this->db->getQuery(true)
                        ->select('id')
                        ->from('#__assets')
                        ->where('name', '=', $com->option)
                        ->exists()
                    ) {
                        continue;
                    }

                    // Craft query
                    $this->db->getQuery(true)
                        ->insert('#__assets')
                        ->columns(['parent_id', 'lft', 'rgt', 'level', 'name', 'title', 'rules'])
                        ->values([1, '', '', 1, $com->option, $com->option, json_encode($defaulRules)])
                        ->execute();
                }
            }

            // Insert existing categories as assets (ignore root item)
            $query = $this->db->getQuery(true);
            $query->select('*')
                ->from('#__categories')
                ->where('extension', '!=', 'system');
            $categories = $query->loadObjectList();

            if (count($categories) > 0) {
                foreach ($categories as $cat) {
                    // Make sure it isn't already in there
                    $catName = $cat->extension . '.category.' . $cat->id;
                    // Make sure it isn't already in there
                    $catName = $cat->extension . '.category.' . $cat->id;
                    if (
                        $this->db->getQuery(true)
                        ->select('id')
                        ->from('#__assets')
                        ->where('name', '=', $catName)
                        ->exists()
                    ) {
                        continue;
                    }

                    // Query for parent id
                    $result = $this->db->getQuery(true)
                        ->select('id')
                        ->from('#__assets')
                        ->where('name', '=', $cat->extension)
                        ->value('id');
                    if (!is_numeric($result)) {
                        // If we don't find the component entry, continue
                        continue;
                    }

                    $catRules = '{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[]}';
                    $this->db->getQuery(true)
                        ->insert('#__assets')
                        ->columns(['parent_id', 'lft', 'rgt', 'level', 'name', 'title', 'rules'])
                        ->values([$result, '', '', $cat->level + 1, $catName, $cat->extension, $catRules])
                        ->execute();

                    // Now, update the categories table with the asset id
                    $id = $this->db->insertid();
                    $query = $this->db->getQuery(true);
                    $query->update('#__categories')
                        ->set(['asset_id' => $id])
                        ->where('id', '=', $cat->id);
                    $query->execute();
                }
            }

            // Now, go back and set parent_id for categories that are level 2
            // (those were original 1.5 categories, i.e. below sections)
            // Now, go back and set parent_id for categories that are level 2
            // (those were original 1.5 categories, i.e. below sections)
            $query = $this->db->getQuery(true);
            $query->select('*')
                ->from('#__categories')
                ->where('level', '=', 2);
            $results = $query->loadObjectList();

            if (count($results) > 0) {
                foreach ($results as $r) {
                    // Get the category id from the assets table
                    $assetName = 'com_content.category.' . $r->id;
                    $query = $this->db->getQuery(true);
                    $query->select('id')
                        ->from('#__assets')
                        ->where('name', '=', $assetName);
                    $id = $query->value('id');

                    // Get the category parent id from the assets table
                    $parentAssetName = 'com_content.category.' . $r->parent_id;
                    $query = $this->db->getQuery(true);
                    $query->select('id')
                        ->from('#__assets')
                        ->where('name', '=', $parentAssetName);
                    $parent_id = $query->value('id');

                    // Update the assets table
                    $query = $this->db->getQuery(true);
                    $query->update('#__assets')
                        ->set(['parent_id' => $parent_id])
                        ->where('id', '=', $id);
                    $query->execute();
                }
            }

            // We're going to go ahead and add asset_id here, as we need to insert into below
            if (!$schema->hasColumn('#__content', 'asset_id') && $schema->hasColumn('#__content', 'id')) {
                $schema->addColumn('#__content', 'asset_id')->integer()->unsigned()->notNull()->default(0)->after('id');
            }

            // Insert articles
            $query = $this->db->getQuery(true);
            $query->select('*')
                ->from('#__content');
            $articles = $query->loadObjectList();

            if (count($articles) > 0) {
                foreach ($articles as $art) {
                    // Query for parent ID
                    $artCatName = 'com_content.category.' . $art->catid;
                    $query = $this->db->getQuery(true);
                    $query->select(['id', 'level'])
                        ->from('#__assets')
                        ->where('name', '=', $artCatName);
                    $obj    = $query->first();
                    $level  = (is_object($obj) && is_numeric($obj->level)) ? $obj->level + 1 : 4;
                    if (is_object($obj) && is_numeric($obj->id)) {
                        $result = $obj->id;
                    } else {
                        // We didn't find a parent id, so just use the 'uncategorised' category
                        if (
                            !$result = $this->db->getQuery(true)
                            ->select('asset_id')
                            ->from('#__categories')
                            ->where('extension', '=', 'com_content')
                            ->where('alias', '=', 'uncategorised')
                            ->value('asset_id')
                        ) {
                            continue;
                        }
                    }

                    $this->db->getQuery(true)
                        ->insert('#__assets')
                        ->columns(['parent_id', 'lft', 'rgt', 'level', 'name', 'title', 'rules'])
                        ->values([$result, '', '', $level, 'com_content.article.' . $art
                            ->id, $art
                            ->title, '{"core.delete":[],"core.edit":[],"core.edit.state":[]}'])
                        ->execute();

                    // Now, update the content table with the asset id
                    $id = $this->db->insertid();
                    $query = $this->db->getQuery(true);
                    $query->update('#__content')
                        ->set(['asset_id' => $id])
                        ->where('id', '=', $art->id);
                    $query->execute();
                }
            }

            // Rule set for super admins only
            $rules = array(
                "core.admin"      => array(
                    "7" => 1
                    ),
                "core.manage"     => array(
                    "7" => 1
                    ),
                "core.create"     => array(
                    "7" => 1
                    ),
                "core.delete"     => array(
                    "7" => 1
                    ),
                "core.edit"       => array(
                    "7" => 1
                    ),
                "core.edit.state" => array(
                    "7" => 1
                    )
                );
            $this->db->getQuery(true)
                ->update('#__assets')
                ->set(['rules' => json_encode($rules)])
                ->where('NAME', '=', 'com_mailto')
                ->orWhere('NAME', '=', 'com_massmail')
                ->orWhere('NAME', '=', 'com_config')
                ->execute();

            // If we have the nested set class available, use it to rebuild lft/rgt
            if (file_exists(PATH_CORE . '/components/com_categories/models/category.php')) {
                include_once PATH_CORE . '/components/com_categories/models/category.php';

                if (
                    class_exists('Components\Categories\Models\Category')
                    && method_exists('Components\Categories\Models\Category', 'rebuild')
                ) {
                    $table = Components\Categories\Models\Category::blank();
                    $table->rebuild(1);
                }
            }
        }
    }

    private function rebuildAssets($parentId = 1, $leftId = 0, $level = 0)
    {
        $children = $this->db->getQuery(true)
            ->select('id')
            ->from('#__assets')
            ->where('parent_id', '=', (int) $parentId)
            ->order('parent_id', 'asc')
            ->order('lft', 'asc')
            ->loadObjectList();

        $rightId = $leftId + 1;

        foreach ($children as $node) {
            $rightId = $this->rebuildAssets($node->id, $rightId, $level + 1);

            if ($rightId === false) {
                return false;
            }
        }

        $this->db->getQuery(true)
            ->update('#__assets')
            ->set([
                'lft'   => (int) $leftId,
                'rgt'   => (int) $rightId,
                'level' => (int) $level
            ])
            ->where('id', '=', (int) $parentId)
            ->execute();

        return $rightId + 1;
    }
}
