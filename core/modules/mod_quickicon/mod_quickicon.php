<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Quickicon;

use Hubzero\Module\Module;
use Hubzero\Facades\Plugin;
use Hubzero\Facades\Route;
use Hubzero\Facades\Event;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;

/**
 * Module class for displaying shortcut idons for common tasks
 */
class Quickicon extends Module
{
    /**
     * Stack to hold buttons
     *
     * @var  array
     */
    protected static $buttons = [];

    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        if (!\Hubzero\Facades\App::isAdmin()) {
            return;
        }

        $buttons = self::getButtons($this->params);

        require $this->getLayoutPath($this->params->get('layout', 'default'));
    }

    /**
     * Helper method to return button list.
     *
     * This method returns the array by reference so it can be
     * used to add custom buttons or remove default ones.
     *
     * @param   object  $params  Registry
     * @return  array   An array of buttons
     */
    public static function &getButtons($params)
    {
        $key = (string) $params;

        if (!isset(self::$buttons[$key])) {
            $context = $params->get('context', 'mod_quickicon');
            if ($context == 'mod_quickicon') {
                // Load mod_quickicon language file in case this method is called before rendering the module
                Lang::load('mod_quickicon');

                self::$buttons[$key] = [
                    [
                        'link'   => Route::url('index.php?option=com_content&task=article.add'),
                        'id'     => 'icon-article-add',
                        'text'   => Lang::txt('MOD_QUICKICON_ADD_NEW_ARTICLE'),
                        'access' => ['core.manage', 'com_content', 'core.create', 'com_content', ]
                    ],
                    [
                        'link'   => Route::url('index.php?option=com_content'),
                        'id'     => 'icon-article',
                        'text'   => Lang::txt('MOD_QUICKICON_ARTICLE_MANAGER'),
                        'access' => ['core.manage', 'com_content']
                    ],
                    [
                        'link'   => Route::url('index.php?option=com_categories&extension=com_content'),
                        'id'     => 'icon-category',
                        'text'   => Lang::txt('MOD_QUICKICON_CATEGORY_MANAGER'),
                        'access' => ['core.manage', 'com_content']
                    ],
                    [
                        'link'   => Route::url('index.php?option=com_media'),
                        'id'     => 'icon-media',
                        'text'   => Lang::txt('MOD_QUICKICON_MEDIA_MANAGER'),
                        'access' => ['core.manage', 'com_media']
                    ],
                    [
                        'link'   => Route::url('index.php?option=com_menus'),
                        'id'     => 'icon-menumgr',
                        'text'   => Lang::txt('MOD_QUICKICON_MENU_MANAGER'),
                        'access' => ['core.manage', 'com_menus']
                    ],
                    [
                        'link'   => Route::url('index.php?option=com_members'),
                        'id'     => 'icon-user',
                        'text'   => Lang::txt('MOD_QUICKICON_USER_MANAGER'),
                        'access' => ['core.manage', 'com_members']
                    ],
                    [
                        'link'   => Route::url('index.php?option=com_modules'),
                        'id'     => 'icon-module',
                        'text'   => Lang::txt('MOD_QUICKICON_MODULE_MANAGER'),
                        'access' => ['core.manage', 'com_modules']
                    ],
                    [
                        'link'   => Route::url('index.php?option=com_installer'),
                        'id'     => 'icon-extension',
                        'text'   => Lang::txt('MOD_QUICKICON_EXTENSION_MANAGER'),
                        'access' => ['core.manage', 'com_installer']
                    ],
                    [
                        'link'   => Route::url('index.php?option=com_languages'),
                        'id'     => 'icon-language',
                        'text'   => Lang::txt('MOD_QUICKICON_LANGUAGE_MANAGER'),
                        'access' => ['core.manage', 'com_languages']
                    ],
                    [
                        'link'   => Route::url('index.php?option=com_config'),
                        'id'     => 'icon-config',
                        'text'   => Lang::txt('MOD_QUICKICON_GLOBAL_CONFIGURATION'),
                        'access' => ['core.manage', 'com_config', 'core.admin', 'com_config']
                    ],
                    [
                        'link'   => Route::url('index.php?option=com_templates'),
                        'id'     => 'icon-themes',
                        'text'   => Lang::txt('MOD_QUICKICON_TEMPLATE_MANAGER'),
                        'access' => ['core.manage', 'com_templates']
                    ],
                    [
                        'link'   => Route::url('index.php?option=com_members&task=edit&id=' . User::get('id')),
                        'id'     => 'icon-user-profile',
                        'text'   => Lang::txt('MOD_QUICKICON_PROFILE'),
                        'access' => true
                    ],
                ];
            } else {
                self::$buttons[$key] = [];
            }

            // Include buttons defined by published quickicon plugins
            Plugin::import('quickicon');

            $arrays = (array) Event::trigger('onGetIcons', [$context]);

            foreach ($arrays as $response) {
                foreach ($response as $icon) {
                    $default = [
                        'link'   => null,
                        'id'     => '',
                        'class'  => 'icon-config',
                        'text'   => null,
                        'access' => true
                    ];
                    $icon = array_merge($default, $icon);
                    if (!is_null($icon['link']) && !is_null($icon['text'])) {
                        self::$buttons[$key][] = $icon;
                    }
                }
            }
        }

        return self::$buttons[$key];
    }

    /**
     * Get the alternate title for the module
     *
     * @param   object  $params  The module parameters.
     * @param   object  $module  The module.
     * @return  string  The alternate title for the module.
     */
    public static function getTitle($params, $module)
    {
        $key = $params->get('context', 'mod_quickicon') . '_title';

        if (Lang::hasKey($key)) {
            return Lang::txt($key);
        }

        return $module->title;
    }
}
