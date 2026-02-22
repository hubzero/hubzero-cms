<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for merging com_register data into com_members
 * and removing com_register component entry
**/
class Migration20140528145010ComRegister extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $rparams = $this->getParams('com_register');

        if (!empty($rparams)) {
            $values = $rparams->toArray();

            $query = $this->db->getQuery(true);
            $query->select('*')
                ->from('#__extensions')
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_members')
                ->limit(1);
            if ($data = $query->first()) {
                $data = (array) $data;
                $mparams = new \Hubzero\Config\Registry($data['params']);
                foreach ($values as $key => $value) {
                    $mparams->set($key, $value);
                }

                $data['params'] = $mparams->toString();

                $query = $this->db->getQuery(true);
                $query->update('#__extensions')
                    ->where('extension_id', '=', $data['extension_id']);

                $vals = array();
                foreach ($data as $key => $val) {
                    if ($key == 'extension_id') {
                        continue;
                    }
                    $query->set([$key => $val]);
                }

                $query->execute();
            }
        }

        // Get the default menu identifier
        // Get the default menu identifier
        $query = $this->db->getQuery(true);
        $query->select('extension_id')
            ->from('#__extensions')
            ->where('type', '=', 'component')
            ->where('element', '=', 'com_members');
        $component = $query->value('extension_id');

        // Check if there's a menu item for com_register
        // Could also filter by menutype: "... AND menutype=" . $this->db->quote($menutype)
        $query = $this->db->getQuery(true);
        $query->select('id')
            ->from('#__menu')
            ->where('alias', '=', 'register')
            ->where('path', '=', 'register');
        if ($id = $query->value('id')) {
            // There is!
            // So, just update its link
            $this->db->getQuery(true)
                ->update('#__menu')
                ->set([
                    'link' => 'index.php?option=com_members&view=register&layout=create',
                    'component_id' => $component,
                ])
                ->where('id', '=', $id)
                ->execute();
        } else {
            $query = $this->db->getQuery(true);
            $query->select('menutype')
                ->from('#__menu')
                ->where('home', '=', 1);
            $menutype = $query->value('menutype');

            $query = $this->db->getQuery(true);
            $query->select('ordering')
                ->from('#__menu')
                ->where('menutype', '=', $menutype)
                ->order('ordering', 'DESC');
            $ordering = intval($query->value('ordering'));
            $ordering++;

            // No menu item for com_register so we need to create one for the new com_members controler
            // No menu item for com_register so we need to create one for the new com_members controler
            $this->db->getQuery(true)
                ->insert('#__menu')
                ->columns([
                    'menutype',
                    'title',
                    'alias',
                    'note',
                    'path',
                    'link',
                    'type',
                    'published',
                    'parent_id',
                    'level',
                    'component_id',
                    'ordering',
                    'checked_out',
                    'checked_out_time',
                    'browserNav',
                    'access',
                    'img',
                    'template_style_id',
                    'params',
                    'lft',
                    'rgt',
                    'home',
                    'language',
                    'client_id',
                ])
                ->values([
                    $menutype,
                    'Register',
                    'register',
                    '',
                    'register',
                    'index.php?option=com_members&view=register&layout=create',
                    'component',
                    1,
                    1,
                    1,
                    $component,
                    $ordering,
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    1,
                    '',
                    0,
                    '',
                    0,
                    0,
                    0,
                    '*',
                    0,
                ])
                ->execute();

            require_once PATH_CORE . '/components/com_menus/models/menu.php';
            $table = \Components\Menus\Models\Menu::blank();
            $table->rebuild();
        }

        $this->deleteComponentEntry('register');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addComponentEntry('register');

        $rparams = $this->getParams('com_members');
        $values = $rparams->toArray();

        $query = $this->db->getQuery(true);
        $query->select('*')
            ->from('#__extensions')
            ->where('type', '=', 'component')
            ->where('element', '=', 'com_register')
            ->limit(1);
        if ($data = $query->first()) {
            $data = (array) $data;
            $mparams = new \Hubzero\Config\Registry($data['params']);
            foreach ($values as $key => $value) {
                $mparams->set($key, $value);
            }

            $data['params'] = $mparams->toString();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->where('extension_id', '=', $data['extension_id']);

            foreach ($data as $key => $val) {
                if ($key == 'extension_id') {
                    continue;
                }
                $query->set([$key => $val]);
            }

            $query->execute();
        }
    }
}
