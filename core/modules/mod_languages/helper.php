<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Languages;

use Hubzero\Module\Module;
use MenusHelper;
use Config;
use Route;
use User;
use Lang;
use App;

/**
 * Module class for displaying a menu
 */
class Helper extends Module
{
    /**
     * Display module
     *
     * @return  void
     */
    public function display()
    {
        // [!] Legacy compatibility
        $params = $this->params;
        $module = $this->module;

        $headerText = trim($params->get('header_text', ''));
        $footerText = trim($params->get('footer_text', ''));

        $list = self::getList($params);

        $moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx', ''));

        require $this->getLayoutPath($params->get('layout', 'default'));
    }

    /**
     * Get a list of languages
     *
     * @param   object  $params  Registry The module options.
     * @return  array
     */
    public static function getList(&$params)
    {
        require_once \Component::path('com_menus') . '/helpers/menus.php';

        $lang = Lang::getRoot();
        $menu = App::get('menu');

        // Get menu home items
        $homes = array();
        foreach ($menu->getMenu() as $item) {
            if ($item->home) {
                $homes[$item->language] = $item;
            }
        }

        // Load associations
        $assoc = isset($app->menu_associations) ? $app->menu_associations : 0;
        if ($assoc) {
            $active = $menu->getActive();
            if ($active) {
                $associations = MenusHelper::getAssociations($active->id);
            }
        }

        $levels    = User::getAuthorisedViewLevels();
        $languages = Lang::available();

        // Filter allowed languages
        foreach ($languages as $i => &$language) {
            $clientName = App::get('client')->name;
            $appLangPath = PATH_APP . DS . 'bootstrap' . DS . strtolower($clientName);
            $coreLangPath = PATH_CORE . DS . 'bootstrap' . DS . ucfirst($clientName);
            if (
                !Lang::exists($language->lang_code, $appLangPath)
                && !Lang::exists($language->lang_code, $coreLangPath)
            ) {
                // Do not display language without frontend UI
                unset($languages[$i]);
            } elseif (isset($language->access) && $language->access && !in_array($language->access, $levels)) {
                // Do not display language without authorized access level
                unset($languages[$i]);
            } else {
                $language->active = $language->lang_code == $lang->getTag();

                if (App::get('language.filter')) {
                    $langCode = $language->lang_code;
                    $langSef = $language->sef;
                    if (isset($associations[$langCode]) && $menu->getItem($associations[$langCode])) {
                        $itemid = $associations[$langCode];
                        if (Config::get('sef')) {
                            $language->link = Route::url(
                                'index.php?lang=' . $langSef . '&Itemid=' . $itemid
                            );
                        } else {
                            $language->link = 'index.php?lang=' . $langSef . '&amp;Itemid=' . $itemid;
                        }
                    } else {
                        if (Config::get('sef')) {
                            $itemid = isset($homes[$langCode]) ? $homes[$langCode]->id : $homes['*']->id;
                            $language->link = Route::url(
                                'index.php?lang=' . $langSef . '&Itemid=' . $itemid
                            );
                        } else {
                            $language->link = 'index.php?lang=' . $langSef;
                        }
                    }
                } else {
                    $language->link = Route::url('&Itemid=' . $homes['*']->id);
                }
            }
        }
        return $languages;
    }
}
