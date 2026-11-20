<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Select as Dropdown;

/**
 * Supports an HTML grouped select list of menu item grouped by menu
 */
class Menuitem extends Groupedlist
{
    /**
     * The form field type.
     *
     * @var string
     */
    public $type = 'Menuitem';

    /**
     * Method to get the field option groups.
     *
     * @return  array<mixed>  The field option objects as a nested array in groups.
     */
    protected function getGroups()
    {
        $element = $this->fieldElement();

        // Initialize variables.
        $groups = [];

        // Initialize some field attributes.
        $menuType  = (string) $element['menu_type'];
        $published = $element['published'] ? explode(',', (string) $element['published']) : [];
        $disable   = $element['disable'] ? explode(',', (string) $element['disable']) : [];
        $language  = $element['language'] ? explode(',', (string) $element['language']) : [];

        // Get the menu items.
        $items = [];
        if (class_exists('\\Components\\Menus\\Helpers\\Menus')) {
            $items = \Components\Menus\Helpers\Menus::getMenuLinks($menuType, 0, 0, $published, $language);
        }

        // Build group for a specific menu type.
        if ($menuType) {
            // Initialize the group.
            $groups[$menuType] = [];

            // Build the options array.
            foreach ($items as $link) {
                $groups[$menuType][] = Dropdown::option(
                    $link->value,
                    $link->text,
                    'value',
                    'text',
                    in_array($link->type, $disable),
                );
            }
        } else {
            // Build groups for all menu types.
            // Build the groups arrays.
            foreach ($items as $menu) {
                // Initialize the group.
                $groups[$menu->menutype] = [];

                // Build the options array.
                foreach ($menu->links as $link) {
                    $groups[$menu->menutype][] = Dropdown::option(
                        $link->value,
                        $link->text,
                        'value',
                        'text',
                        in_array($link->type, $disable),
                    );
                }
            }
        }

        // Merge any additional groups in the XML definition.
        return array_merge(parent::getGroups(), $groups);
    }
}
