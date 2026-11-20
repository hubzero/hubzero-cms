<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder;
use Hubzero\Facades\App;

/**
 * Renders a SQL element
 */
class Sql extends Element
{
    /**
     * Element name
     *
     * @var  string
     */
    protected $name = 'Sql';

    /**
     * Fetch a calendar element
     *
     * @param   string  $name          Element name
     * @param   string  $value         Element value
     * @param   \SimpleXMLElement  &$node         XMLElement node object containing the settings for the element
     * @param   string  $control_name  Control name
     * @return  string
     */
    public function fetchElement($name, $value, &$node, $control_name)
    {
        $db = App::get('db');
        $db->setQuery((string) $node['query']);

        $key = (string) $node['key_field'];
        $key = $key ?: 'value';

        $val = (string) $node['value_field'];
        $val = $val ?: $name;

        $options = $db->loadObjectlist();

        // Check for an error.

        if (!$options) {
            $options = [];
        }

        return Builder\Select::genericlist(
            $options,
            $control_name . '[' . $name . ']',
            [
                'id'          => $control_name . $name,
                'list.attr'   => 'class="inputbox"',
                'list.select' => $value,
                'option.key'  => $key,
                'option.text' => $val,
            ],
        );
    }
}
