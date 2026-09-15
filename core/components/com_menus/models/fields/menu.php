<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Facades\App;

/**
 * Supports an HTML select list of menus
 */
class Menu extends Select
{
    /**
     * The form field type.
     *
     * @var  string
     */
    public $type = 'Menu';

    /**
     * Method to get the list of menus for the field options.
     *
     * This is what a menu module chooses from, so it offers the menus that are
     * meant to be looked at. A component or routing menu holds addresses rather
     * than navigation - the component menu is the hub's routing table - and
     * putting one of those in a module renders the routing table as a menu.
     * Leaving them out of the list is what makes "never displayed" true rather
     * than merely intended.
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        $db = App::get('db');

        $query = $db->getQuery()
            ->select('menutype', 'value')
            ->select('title', 'text')
            ->from('#__menu_types')
            ->order('title', 'asc');

        // Older hubs have no type; there, every menu is one to display
        if ($db->tableHasField('#__menu_types', 'type')) {
            $query->whereEquals('type', 'display');
        }

        $db->setQuery($query->toString());
        $menus = $db->loadObjectList();

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $menus);

        return $options;
    }
}
