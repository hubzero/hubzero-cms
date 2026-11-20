<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Select as Dropdown;
use Hubzero\Facades\App;

/**
 * Supports an custom SQL select list
 */
class Sql extends Select
{
    /**
     * The form field type.
     *
     * @var  string
     */
    public $type = 'SQL';

    /**
     * Method to get the custom field options.
     * Use the query attribute to supply a query to generate the list.
     *
     * @return  array<mixed>  The field option objects.
     */
    protected function getOptions()
    {
        $element = $this->fieldElement();

        // Initialize variables.
        $options = [];

        // Initialize some field attributes.
        $key       = $element['key_field'] ? (string) $element['key_field'] : 'value';
        $value     = $element['value_field']
            ? (string) $element['value_field']
            : (string) $element['name'];
        $translate = $element['translate'] ? (string) $element['translate'] : false;
        $query     = (string) $element['query'];

        // Get the database object.
        $db = App::get('db');

        // Set the query and get the result list.
        $db->setQuery($query);
        $items = $db->loadObjectlist();

        // Check for an error.

        // Build the field options.
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($translate == true) {
                    $options[] = Dropdown::option($item->$key, App::get('language')->txt($item->$value));
                } else {
                    $options[] = Dropdown::option($item->$key, $item->$value);
                }
            }
        }

        // Merge any additional options in the XML definition.
        return array_merge(parent::getOptions(), $options);
    }
}
