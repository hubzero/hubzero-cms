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
 * Migration script for menu table migrations
  *
**/
class Migration20130924000009ComMenus extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->setTableEngine('#__menu', 'MYISAM');

        $first = false;

        if ($schema->hasColumn('#__menu', 'pollid')) {
            $schema->dropColumn('#__menu', 'pollid');

            $first = true;
        }
        if ($schema->hasColumn('#__menu', 'utaccess')) {
            $schema->dropColumn('#__menu', 'utaccess');
        }
        if ($schema->hasColumn('#__menu', 'menutype')) {
            $schema->modifyColumn('#__menu', 'menutype')->string(24)->notNull()->execute();
        }
        if ($schema->hasColumn('#__menu', 'name') && !$schema->hasColumn('#__menu', 'title')) {
            $schema->renameColumn('#__menu', 'name', 'title')->string(255)->notNull()->execute();
        }
        if ($schema->hasColumn('#__menu', 'alias')) {
            $schema->modifyColumn('#__menu', 'alias')->string()->notNull()->execute();
        }
        if (!$schema->hasColumn('#__menu', 'note') && $schema->hasColumn('#__menu', 'alias')) {
            $schema->addColumn('#__menu', 'note')->string()->notNull()->default('')->execute();
        }
        if ($schema->hasColumn('#__menu', 'link')) {
            $schema->modifyColumn('#__menu', 'link')->string(1024)->notNull()->execute();
        }
        if ($schema->hasColumn('#__menu', 'type')) {
            $schema->modifyColumn('#__menu', 'type')->string(16)->notNull()->execute();
        }
        if ($schema->hasColumn('#__menu', 'published')) {
            $schema->modifyColumn('#__menu', 'published')->tinyInteger()->notNull()->default(0)->execute();
        }
        if ($schema->hasColumn('#__menu', 'parent') && !$schema->hasColumn('#__menu', 'parent_id')) {
            $schema->renameColumn('#__menu', 'parent', 'parent_id')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(1)
                ->execute();
        }
        if (
            !$schema->hasColumn('#__menu', 'level')
            && $schema->hasColumn('#__menu', 'parent_id')
            && $schema->hasColumn('#__menu', 'sublevel')
        ) {
            $schema->renameColumn('#__menu', 'sublevel', 'level')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
        }
        if (
            $schema->hasColumn('#__menu', 'componentid')
            && !$schema->hasColumn('#__menu', 'component_id')
        ) {
            $schema->renameColumn('#__menu', 'componentid', 'component_id')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
        }
        if ($schema->hasColumn('#__menu', 'ordering')) {
            $schema->modifyColumn('#__menu', 'ordering')->integer()->notNull()->default(0)->execute();
        }
        if ($schema->hasColumn('#__menu', 'checked_out')) {
            $schema->modifyColumn('#__menu', 'checked_out')->integer()->unsigned()->notNull()->default(0)->execute();
        }
        if ($schema->hasColumn('#__menu', 'checked_out_time')) {
            $schema->modifyColumn('#__menu', 'checked_out_time')
                ->timestamp()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();
        }
        if ($schema->hasColumn('#__menu', 'browserNav')) {
            $schema->modifyColumn('#__menu', 'browserNav')->tinyInteger()->notNull()->default(0)->execute();
        }
        if ($schema->hasColumn('#__menu', 'access')) {
            $schema->modifyColumn('#__menu', 'access')->integer(10)->unsigned()->notNull()->default(0)->execute();
        }
        if ($schema->hasColumn('#__menu', 'params')) {
            $schema->modifyColumn('#__menu', 'params')->text()->notNull()->execute();
        }
        if ($schema->hasColumn('#__menu', 'lft')) {
            $schema->modifyColumn('#__menu', 'lft')->integer()->notNull()->default(0)->execute();
        }
        if ($schema->hasColumn('#__menu', 'rgt')) {
            $schema->modifyColumn('#__menu', 'rgt')->integer()->notNull()->default(0)->execute();
        }
        if ($schema->hasColumn('#__menu', 'home')) {
            $schema->modifyColumn('#__menu', 'home')->tinyInteger()->unsigned()->notNull()->default(0)->execute();
        }
        // Use addColumn helper (AFTER position parameter removed for SQLite compatibility)
        if (!$schema->hasColumn('#__menu', 'path') && $schema->hasColumn('#__menu', 'note')) {
            $schema->addColumn('#__menu', 'path')->string(1024)->notNull()->execute();
        }
        if (!$schema->hasColumn('#__menu', 'img') && $schema->hasColumn('#__menu', 'access')) {
            $schema->addColumn('#__menu', 'img')->string()->notNull()->execute();
        }
        if (!$schema->hasColumn('#__menu', 'template_style_id') && $schema->hasColumn('#__menu', 'img')) {
            $schema->addColumn('#__menu', 'template_style_id')
                ->integer(10)
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
        }
        if (!$schema->hasColumn('#__menu', 'language') && $schema->hasColumn('#__menu', 'home')) {
            $schema->addColumn('#__menu', 'language')->string(7)->notNull()->default('')->execute();
        }
        if (!$schema->hasColumn('#__menu', 'client_id') && $schema->hasColumn('#__menu', 'language')) {
            $schema->addColumn('#__menu', 'client_id')->tinyInteger()->notNull()->default(0)->execute();
        }
        $schema->dropIndex('#__menu', 'componentid');
        $schema->dropIndex('#__menu', 'menutype');
        $schema->addIndex('#__menu', 'idx_componentid', ['component_id', 'menutype', 'published', 'access']);
        $schema->addIndex('#__menu', 'idx_menutype', 'menutype');
        if (
            $schema->hasColumn('#__menu', 'lft')
            && $schema->hasColumn('#__menu', 'rgt')
        ) {
            $schema->addIndex('#__menu', 'idx_left_right', ['lft', 'rgt']);
        }
        $schema->addIndex('#__menu', 'idx_alias', 'alias');
        if ($schema->hasColumn('#__menu', 'path')) {
            $schema->addIndex('#__menu', 'idx_path', 'path');
        }
        $schema->addIndex('#__menu', 'idx_language', 'language');

        $schema->setTableEngine('#__menu_types', 'MYISAM');

        if ($schema->hasColumn('#__menu_types', 'menutype')) {
            $schema->modifyColumn('#__menu_types', 'menutype')->string(24)->notNull()->execute();
        }
        if ($schema->hasColumn('#__menu_types', 'title')) {
            $schema->modifyColumn('#__menu_types', 'title')->string(48)->notNull()->execute();
        }
        $schema->dropIndex('#__menu_types', 'menutype');
        if ($schema->hasColumn('#__menu_types', 'menutype')) {
            $schema->addUniqueIndex('#__menu_types', 'idx_menutype', 'menutype');
        }

        if ($first) {
            // Joomla seems to expect the root item to be 1...blah!
            // So, if id 1 is taken, we need to clear it out
            $result = $this->db->getQuery(true)
                ->select('*')
                ->from('#__menu')
                ->where('id', '=', 1)
                ->first();

            if ($result) {
                $result->id = null;
                $this->db->queryBuilder()->pushObject('#__menu', $result);
                $id = $this->db->insertid();

                $this->db->getQuery(true)
                    ->update('#__menu')
                    ->set(['parent_id' => $id])
                    ->where('parent_id', '=', 1)
                    ->execute();

                $this->db->getQuery(true)
                    ->update('#__modules_menu')
                    ->set(['menuid' => $id])
                    ->where('menuid', '=', 1)
                    ->execute();

                $this->db->getQuery(true)
                    ->delete('#__menu')
                    ->where('id', '=', 1)
                    ->execute();
            }

            // Insert new root menu item
            $this->db->getQuery(true)
                ->insert('#__menu')
                ->set([
                    'id'               => 1,
                    'menutype'         => '',
                    'title'            => 'Menu_Item_Root',
                    'alias'            => 'root',
                    'note'             => '',
                    'path'             => '',
                    'link'             => '',
                    'type'             => '',
                    'published'        => 1,
                    'parent_id'        => 0,
                    'level'            => 0,
                    'component_id'     => 0,
                    'ordering'         => 0,
                    'checked_out'      => 0,
                    'checked_out_time' => '0000-00-00 00:00:00',
                    'browserNav'       => 0,
                    'access'           => 0,
                    'img'              => '',
                    'template_style_id' => 0,
                    'params'           => '',
                    'lft'              => 0,
                    'rgt'              => 0,
                    'home'             => 0,
                    'language'         => '*',
                    'client_id'        => 0
                ])
                ->execute();

            // Get the id of the new root menu item
            $id = $this->db->getQuery(true)
                ->select('id')
                ->from('#__menu')
                ->where('alias', '=', 'root')
                ->value('id');

            // Update parent_id's of existing menus to relate to the new root
            $this->db->getQuery(true)
                ->update('#__menu')
                ->set(['parent_id' => $id])
                ->where('parent_id', '=', 0)
                ->where('alias', '!=', 'root')
                ->execute();

            // Also increment the level 1
            $this->db->getQuery(true)
                ->update('#__menu')
                ->set(['level' => Expression::column('level')->plus(1)])
                ->where('alias', '!=', 'root')
                ->execute();

            // Build paths
            $this->db->getQuery(true)
                ->update('#__menu')
                ->set(['path' => Expression::column('alias')])
                ->where('alias', '!=', 'root')
                ->execute();

            // Get max depth
            $maxlevel = $this->db->getQuery(true)
                ->select(['level'])
                ->from('#__menu')
                ->order('level', 'desc')
                ->value('level');

            $aliases = $this->db->getQuery(true)
                ->select(['id', 'alias'])
                ->from('#__menu')
                ->pluck('alias', 'id');

            for ($i = 2; $i <= $maxlevel; $i++) {
                $results = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__menu')
                    ->where('level', '>=', $i)
                    ->loadObjectList();

                if (count($results) > 0) {
                    foreach ($results as $r) {
                        $alias = $aliases[$r->parent_id] ?? null;

                        $path = $alias . '/' . $r->path;

                        $this->db->getQuery(true)
                            ->update('#__menu')
                            ->set(['path' => $path])
                            ->where('id', '=', $r->id)
                            ->execute();
                    }
                }
            }

            // Add entries for components menu on backend
            $results = $this->db->getQuery(true)
                ->select('*')
                ->from('#__components')
                ->where('parent', '=', 0)
                ->where('iscore', '=', 0)
                ->where('enabled', '=', 1)
                ->whereNotNull('admin_menu_link')
                ->where('admin_menu_link', '!=', '')
                ->loadObjectList();

            if (count($results) > 0) {
                foreach ($results as $r) {
                    $alias = substr($r->option, 4);
                    $link  = 'index.php?' . $r->admin_menu_link;
                    // Insert item
                    $this->db->getQuery(true)
                        ->insert('#__menu')
                        ->set([
                            'menutype'     => 'main',
                            'title'        => $r->option,
                            'alias'        => $alias,
                            'path'         => $alias,
                            'link'         => $link,
                            'type'         => 'component',
                            'published'    => 1,
                            'parent_id'    => 1,
                            'level'        => 1,
                            'component_id' => $r->id,
                            'language'     => '*',
                            'client_id'    => 1
                        ])
                        ->execute();
                }
            }

            // If we have the nested set class available, use it to rebuild lft/rgt
            if (file_exists(PATH_CORE . '/components/com_menus/models/menu.php')) {
                include_once PATH_CORE . '/components/com_menus/models/menu.php';

                if (
                    class_exists('Components\Menus\Models\Menu')
                    && method_exists('Components\Menus\Models\Menu', 'rebuild')
                ) {
                    $table = \Components\Menus\Models\Menu::blank();
                    $table->rebuild();
                }
            }

            // Update menu params (specifically to fix menu_image)
            $results = $this->db->getQuery(true)
                ->select(['id', 'params', 'link'])
                ->from('#__menu')
                ->loadObjectList();

            if (count($results) > 0) {
                foreach ($results as $r) {
                    $params = trim($r->params);
                    if (empty($params) || $params == '{}') {
                        continue;
                    }

                    $array = array();
                    $ar    = explode("\n", $params);

                    foreach ($ar as $a) {
                        $a = trim($a);
                        if (empty($a)) {
                            continue;
                        }

                        $ar2 = explode("=", $a, 2);
                        if ($ar2[0] == 'menu_image' && $ar2[1] == "-1") {
                            $ar2[1] = "0";
                        }

                        $array[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
                    }

                    // Check to see if this menu item points to an article
                    preg_match('/index\.php\?option=com_content&view=article&id=([0-9]+)/', $r->link, $matches);

                    // Need to merge in content params (if applicable), as menu item params now take precidence
                    if (isset($matches[1]) && !empty($matches[1])) {
                        $art_params = $this->db->getQuery(true)
                            ->select('attribs')
                            ->from('#__content')
                            ->where('id', '=', $matches[1])
                            ->value('attribs');
                        $art_params = json_decode($art_params);

                        foreach ($art_params as $k => $v) {
                            if (($v !== null) && ($v !== '') && array_key_exists($k, $array)) {
                                $array[$k] = $v;
                            }
                        }
                    }

                    $this->db->getQuery(true)
                        ->update('#__menu')
                        ->set(['params' => json_encode($array)])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }

            // Update component_id -> extension_id
            $results = $this->db->getQuery(true)
                ->select(['id', 'link', 'component_id'])
                ->from('#__menu')
                ->where('component_id', '!=', 0)
                ->loadObjectList();

            if (count($results) > 0) {
                foreach ($results as $r) {
                    preg_match('/index\.php\?option=([a-z0-9_]+)/', $r->link, $matches);

                    if (isset($matches[1]) && !empty($matches[1])) {
                        $id = $this->db->getQuery(true)
                            ->select('extension_id')
                            ->from('#__extensions')
                            ->where('element', '=', $matches[1])
                            ->where('type', '=', 'component')
                            ->order('client_id', 'asc')
                            ->value('extension_id');

                        $id = (!is_null($id)) ? $id : 0;

                        $this->db->getQuery(true)
                            ->update('#__menu')
                            ->set(['component_id' => $id])
                            ->where('id', '=', $r->id)
                            ->execute();
                    }
                }
            }

            // Set language for all menu items
            $this->db->getQuery(true)
                ->update('#__menu')
                ->set(['language' => '*'])
                ->execute();

            // Fix com_user->com_users in menu items
            $id = $this->db->getQuery(true)
                ->select('extension_id')
                ->from('#__extensions')
                ->where('element', '=', 'com_users')
                ->value('extension_id');

            $results = $this->db->getQuery(true)
                ->select('*')
                ->from('#__menu')
                ->where('menutype', '=', 'default')
                ->whereIn('alias', ['login', 'logout', 'remind', 'reset'])
                ->loadObjectList();

            if ($results) {
                foreach ($results as $r) {
                    $link = preg_replace('/(index\.php\?option=com_user)(&view=[a-z]+)/', '${1}s${2}', $r->link);
                    $params = json_decode($r->params);

                    if ($r->alias == 'login') {
                        $params->login_redirect_url = $params->login;
                        unset($params->login);
                    }

                    $this->db->getQuery(true)
                        ->update('#__menu')
                        ->set([
                            'link'         => $link,
                            'component_id' => $id,
                            'params'       => json_encode($params)
                        ])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }

            // Fix menu link type menu items to be alias type
            $this->db->getQuery(true)
                ->update('#__menu')
                ->set([
                    'type'   => 'alias',
                    'link'   => 'index.php?Itemid=',
                    'params' => Expression::replace(Expression::column('params'), 'menu_item', 'aliasoptions')
                ])
                ->where('type', '=', 'menulink')
                ->execute();
        }

        // Now we can get rid of the components table as well
        if ($schema->tableExists('#__components')) {
            $schema->dropTable('#__components');
        }

        if (
            $schema->hasColumn('#__menu', 'client_id')
                && $schema->hasColumn('#__menu', 'parent_id')
                && $schema->hasColumn('#__menu', 'alias')
                && $schema->hasColumn('#__menu', 'language')
        ) {
            $schema->addIndex('#__menu', 'idx_client_id_parent_id_alias_language', [
                'client_id',
                'parent_id',
                'alias',
                'language',
            ]);
        }
    }
}
