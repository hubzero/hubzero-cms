<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Menu;

use Hubzero\Module\Module;
use Hubzero\Facades\Cache;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Hubzero\Facades\App;

/**
 * Module class for displaying a menu
 */
class Menu extends Module
{
    protected $parentLink;
    protected $rootLink;

    /**
     * Display module
     *
     * @return  void
     */
    public function display()
    {
        // [!] Legacy compatibility for older view overrides
        $params = $this->params;
        $module = $this->module;

        $list      = self::getList($params);
        $menu      = App::get('menu');
        $active    = $menu->getActive();
        // A hub can have no item marked home, in which case getDefault()
        // returns 0 rather than an item. With neither, no item is current and
        // nothing is highlighted, which is the right answer for a page that
        // belongs to no menu.
        $default   = $menu->getDefault();
        $active_id = isset($active)
            ? $active->id
            : (is_object($default) ? $default->id : 0);
        $path      = isset($active) ? $active->tree : array();
        $showAll   = $params->get('showAllChildren');
        $class_sfx = htmlspecialchars($params->get('class_sfx', ''));
        $disclosureMenu = $params->get('disclosureMenu', false);
        $toplevelLinks  = $params->get('toplevelLinks', false);

        if (count($list)) {
            require $this->getLayoutPath($params->get('layout', 'default'));
        }
    }

    /**
     * Get a list of the menu items.
     *
     * @param   object  $params  Registry The module options.
     * @return  array
     */
    public static function getList(&$params)
    {
        $menu = App::get('menu');

        // If no active menu, use default. A hub with nothing marked home has
        // neither, and getDefault() answers 0 rather than an item, so the menu
        // is built without a branch to open.
        $active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

        if (!is_object($active)) {
            $active = null;
        }

        $levels = User::getAuthorisedViewLevels();
        asort($levels);

        $key = 'mod_menu.' . 'menu_items' . $params . implode(',', $levels) . '.' . ($active ? $active->id : 0);

        if (!($items = Cache::get($key))) {
            // Initialise variables.
            $list     = array();
            $db       = App::get('db');

            $path     = $active ? $active->tree : array();
            $start    = (int) $params->get('startLevel');
            $end      = (int) $params->get('endLevel');
            $showAll  = $params->get('showAllChildren');
            $items    = $menu->getItems('menutype', $params->get('menutype'));

            $lastitem = 0;
            $hidden   = array();
            $shown  = array();

            if ($items) {
                foreach ($items as $i => $item) {
                    // An item can be a real address the menu does not show:
                    // a page the template draws itself, or one that exists to
                    // give a component its route while the link a visitor
                    // follows sits somewhere else in the tree. An item that
                    // names no component is one of those by default, since
                    // there is nothing to link to, but any item can be told
                    // to keep out of the menu.
                    //
                    // Hiding an item hides what is under it. A branch whose
                    // head is gone reads as a set of pages that belong to
                    // nothing, and the way to show a child without its parent
                    // is to put the child somewhere else, not to leave a gap
                    // where its parent was. Items arrive in tree order, so a
                    // parent is always seen before the children it hides.
                    //
                    // Dropped here rather than in a layout, where a template's
                    // own override would not know to skip it.
                    if (
                        !$item->params->get('menu_show', $item->type == 'none' ? 0 : 1)
                        || in_array($item->parent_id, $hidden)
                    ) {
                        $hidden[] = $item->id;

                        unset($items[$i]);
                        continue;
                    }

                    $shown[$item->parent_id] = true;

                    if (
                        ($start && $start > $item->level)
                        || ($end && $item->level > $end)
                        || (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path))
                        || ($start > 1 && !in_array($item->tree[$start - 2], $path))
                    ) {
                        unset($items[$i]);
                        continue;
                    }

                    $item->deeper = false;
                    $item->shallower = false;
                    $item->level_diff = 0;

                    if (isset($items[$lastitem])) {
                        $items[$lastitem]->deeper     = ($item->level > $items[$lastitem]->level);
                        $items[$lastitem]->shallower  = ($item->level < $items[$lastitem]->level);
                        $items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
                    }

                    $item->parent = (bool) $menu->getItems('parent_id', (int) $item->id, true);

                    $lastitem = $i;
                    $item->active = false;
                    $item->flink  = $item->link;

                    $item->title        = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
                    $anchorCss = $item->params->get('menu-anchor_css', '');
                    $item->anchor_css   = htmlspecialchars($anchorCss, ENT_COMPAT, 'UTF-8', false);
                    $anchorTitle = $item->params->get('menu-anchor_title', '');
                    $item->anchor_title = htmlspecialchars($anchorTitle, ENT_COMPAT, 'UTF-8', false);
                    $menuImage = $item->params->get('menu_image', '');
                    $item->menu_image   = $menuImage
                        ? htmlspecialchars($menuImage, ENT_COMPAT, 'UTF-8', false)
                        : '';

                    // Reverted back for CMS version 2.5.6
                    switch ($item->type) {
                        case 'separator':
                            // No further action needed.
                            continue 2;
                            break;


                        case 'url':
                            $isInternalLink = (strpos($item->link, 'index.php?') === 0);
                            $hasNoItemid = (strpos($item->link, 'Itemid=') === false);
                            if ($isInternalLink && $hasNoItemid) {
                                // If this is an internal link, ensure the Itemid is set.
                                $item->flink = $item->link . '&Itemid=' . $item->id;
                            }
                            break;

                        case 'alias':
                            // If this is an alias use the item id stored in the parameters to make the link.
                            $item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
                            break;

                        default:
                            $item->flink = 'index.php?Itemid=' . $item->id;
                            break;
                    }

                    $isNotHttp = strcasecmp(substr($item->flink, 0, 4), 'http');
                    $hasIndexPhp = (strpos($item->flink, 'index.php?') !== false);
                    if ($isNotHttp && $hasIndexPhp) {
                        $item->flink = Route::url($item->flink, true, $item->params->get('secure'));
                    } else {
                        $item->flink = Route::url($item->flink);
                    }
                }

                // An item is drawn as a parent - a disclosure button rather
                // than a link - on the strength of having children. One whose
                // children were all hidden above has nothing to disclose.
                foreach ($items as $item) {
                    if ($item->parent && !isset($shown[$item->id])) {
                        $item->parent = false;
                    }
                }

                if (isset($items[$lastitem])) {
                    $items[$lastitem]->deeper     = (($start ? $start : 1) > $items[$lastitem]->level);
                    $items[$lastitem]->shallower  = (($start ? $start : 1) < $items[$lastitem]->level);
                    $items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ? $start : 1));
                }
            }

            Cache::put($key, $items, intval($params->get('cache_time', 900)) / 60);
        }
        return $items;
    }
}
