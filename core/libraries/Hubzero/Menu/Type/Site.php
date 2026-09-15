<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Menu\Type;

/**
 * Site Menu class
 */
class Site extends Base
{
    /**
     * Loads the entire menu table into memory.
     *
     * @return  void
     */
    public function load()
    {
        if (!($this->get('db') instanceof \Hubzero\Database\Driver)) {
            return;
        }

        // Initialise variables.
        $db = $this->get('db');

        $query = $db->getQuery()
            ->select('m.id')
            ->select('m.menutype')
            ->select('m.title')
            ->select('m.alias')
            ->select('m.note')
            ->select('m.path', 'route')
            ->select('m.link')
            ->select('m.type')
            ->select('m.level')
            ->select('m.language')
            ->select('m.browserNav')
            ->select('m.access')
            ->select('m.params')
            ->select('m.home')
            ->select('m.img')
            ->select('m.template_style_id')
            ->select('m.component_id')
            ->select('m.parent_id')
            ->select('e.element', 'component')
            ->from('#__menu', 'm')
            ->join('#__extensions AS e', 'e.extension_id', 'm.component_id', 'left')
            ->whereEquals('m.published', 1)
            ->where('m.parent_id', '>', 0)
            ->whereEquals('m.client_id', 0)
            ->order('m.lft', 'asc');

        // What kind of menu each item came from, so the router can prefer a
        // hub's own menu over the generated one when two items answer at the
        // same address. A hub that has not run the migration yet has no such
        // column, and everything is treated as a menu to look at.
        if ($db->tableHasField('#__menu_types', 'type')) {
            $query
                ->select('t.type', 'menuKind')
                ->join('#__menu_types AS t', 't.menutype', 'm.menutype', 'left');
        }

        // A menu item for a component the hub has switched off is not a page.
        // The dispatcher already refuses to run it, so the item was only ever
        // a dead link in the menu and an address held against anything else
        // that wanted it. Reading it off #__extensions rather than keeping the
        // item's published state in step means it is right however the
        // component was switched off - by a migration, by the extension
        // manager, or by hand.
        //
        // An item with no component - a heading, a separator, a url, an alias -
        // says so with component_id 0 and stays. Anything else has to find its
        // component switched on, so an item left behind by a component that was
        // uninstalled goes too rather than joining to nothing and being kept.
        $query->whereRaw('(`m`.`component_id` = 0 OR `e`.`enabled` = 1)');

        // Set the query
        $db->setQuery($query->toString());

        $this->_items = $db->loadObjectList('id');

        // An alias points at another item by id. If that item has just gone -
        // because its component was switched off - the alias is left pointing
        // at nothing, and the router builds a url out of the hole.
        foreach ($this->_items as $id => $item) {
            if ($item->type != 'alias') {
                continue;
            }

            $params = json_decode((string) $item->params, true);
            $target = is_array($params) && isset($params['aliasoptions'])
                ? (int) $params['aliasoptions']
                : 0;

            if ($target && !isset($this->_items[$target])) {
                unset($this->_items[$id]);
            }
        }

        foreach ($this->_items as &$item) {
            // Get parent information.
            $parent_tree = array();
            if (isset($this->_items[$item->parent_id])) {
                $parent_tree  = $this->_items[$item->parent_id]->tree;
            }

            // Create tree.
            $parent_tree[] = $item->id;
            $item->tree = $parent_tree;

            // Create the query array.
            $url = str_replace('index.php?', '', $item->link);
            $url = str_replace('&amp;', '&', $url);

            parse_str($url, $item->query);
        }
    }

    /**
     * Gets menu items by attribute
     *
     * @param   string   $attributes  The field name
     * @param   string   $values      The value of the field
     * @param   boolean  $firstonly   If true, only returns the first item found
     * @return  array
     */
    public function getItems($attributes, $values, $firstonly = false)
    {
        $attributes = (array) $attributes;
        $values     = (array) $values;

        // Filter by language if not set
        if (($key = array_search('language', $attributes)) === false) {
            if ($this->get('language_filter')) {
                $attributes[] = 'language';
                $values[]     = array($this->get('language'), '*');
            }
        } elseif ($values[$key] === null) {
            unset($attributes[$key]);
            unset($values[$key]);
        }

        // Filter by access level if not set
        if (($key = array_search('access', $attributes)) === false) {
            $attributes[] = 'access';
            $values[]     = $this->get('access');
        } elseif ($values[$key] === null) {
            unset($attributes[$key]);
            unset($values[$key]);
        }

        return parent::getItems($attributes, $values, $firstonly);
    }

    /**
     * Get menu item by id
     *
     * @param   string  $language  The language code.
     * @return  mixed   The item object
     */
    public function getDefault($language = '*')
    {
        if (array_key_exists($language, $this->_default) && $this->get('language_filter')) {
            return $this->_items[$this->_default[$language]];
        }

        if (array_key_exists('*', $this->_default)) {
            return $this->_items[$this->_default['*']];
        }

        return 0;
    }
}
