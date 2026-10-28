<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\BreadCrumbs;

use Hubzero\Module\Module;
use Hubzero\Template\Overrides;
use Request;
use Route;
use Lang;
use Html;
use stdClass;

/**
 * Module class for displaying breadcrumbs
 */
class Helper extends Module
{
    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        // Legacy support in case old view overrides reference
        // $params instead of $this->params
        $params = $this->params;

        // Get the breadcrumbs
        $list   = $this->getList();
        $count  = count($list);

        // Set the default separator
        $separator = $this->setSeparator($this->params->get('separator'));
        $moduleclass_sfx = htmlspecialchars($this->params->get('moduleclass_sfx', ''));

        require $this->getLayoutPath($this->params->get('layout', 'default'));
    }

    /**
     * Get the list of crumbs
     *
     * @return  array
     */
    public function getList()
    {
        $items    = \Pathway::items();
        $showHome = $this->params->get('showHome', 1);
        $home     = null;

        // Inside a supergroup, the trail starts at the group: the site Home,
        // "Groups", and anything else leading up to the group's crumb go.
        // With a Home entry the group's crumb becomes it; without one the
        // group's crumb stays, so the trail still leads back to the group.
        if ($this->params->get('groupRoot', 1) && ($group = Overrides::superGroup())) {
            $at = $this->findGroupCrumb($items, $group);

            if ($at !== null) {
                $home  = $items[$at];
                $items = array_slice($items, $showHome ? $at + 1 : $at);
            }
        }

        $count = count($items);

        // Don't use $items here as it references Pathway properties directly
        $crumbs = array();
        for ($i = 0; $i < $count; $i++) {
            $crumbs[$i] = new stdClass();
            $crumbs[$i]->name = stripslashes(htmlspecialchars($items[$i]->name, ENT_COMPAT, 'UTF-8'));
            $crumbs[$i]->link = Route::url($items[$i]->link);
        }

        if ($showHome) {
            $item = new stdClass();
            $item->name = htmlspecialchars($this->params->get('homeText', Lang::txt('MOD_BREADCRUMBS_HOME')));
            $item->link = Route::url($home ? $home->link : 'index.php?Itemid=' . \App::get('menu')->getDefault()->id);

            array_unshift($crumbs, $item);
        }

        return $crumbs;
    }

    /**
     * Find a supergroup's own crumb in the trail
     *
     * com_groups puts the group's crumb after "Groups"; everything after
     * it is the active page or tab, then any plugin crumbs.
     *
     * @param   array   $items  Pathway items
     * @param   object  $group  The supergroup being viewed
     * @return  mixed   The crumb's index, or null if it isn't in the trail
     */
    protected function findGroupCrumb($items, $group)
    {
        // The group crumb is linked by whichever cn the request used
        $links = array();
        foreach (array($group->get('cn'), Request::getString('cn', '')) as $cn) {
            $links[] = strtolower('index.php?option=com_groups&cn=' . $cn);
        }

        foreach ($items as $i => $item) {
            if (in_array(strtolower($item->link), $links)) {
                return $i;
            }
        }

        return null;
    }

    /**
     * Set the breadcrumbs separator for the breadcrumbs display.
     *
     * @param   string  $custom  Custom xhtml complient string to separate the items of the breadcrumbs
     * @return  string  Separator string
     */
    public function setSeparator($custom = null)
    {
        // If a custom separator has not been provided we try to load a template
        // specific one first, and if that is not present we load the default separator
        if ($custom == null) {
            if (Lang::isRTL()) {
                $_separator = '&lsaquo;';
            } else {
                $_separator = '&rsaquo;';
            }
        } else {
            $_separator = htmlspecialchars($custom);
        }

        return $_separator;
    }
}
