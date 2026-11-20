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
 * Renders a list element
 */
class Select extends Element
{
    /**
     * Element type
     *
     * @var  string
     */
    protected $name = 'Select';

    /**
     * Get the options for the element
     *
     * @param   \SimpleXMLElement  &$node  XMLElement node object containing the settings for the element
     * @return  array<int, mixed>
     */
    protected function getOptions(&$node)
    {
        $options = [];
        foreach ($node->children() as $option) {
            $val  = $option['value'];
            $text = (string) $option;
            $options[] = Builder\Select::option((string) $val, App::get('language')->txt($text));
        }
        return $options;
    }

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
        $ctrl = $control_name . '[' . $name . ']';
        $attribs = ' ';

        if ($v = $node['size']) {
            $attribs .= 'size="' . (string) $v . '"';
        }
        if ($v = $node['class']) {
            $attribs .= 'class="' . (string) $v . '"';
        } else {
            $attribs .= 'class="inputbox"';
        }
        if ($m = $node['multiple']) {
            $attribs .= 'multiple="multiple"';
            $ctrl .= '[]';
        }

        return Builder\Select::genericlist(
            $this->getOptions($node),
            $ctrl,
            [
                'id' => $control_name . $name,
                'list.attr' => $attribs,
                'list.select' => $value,
            ],
        );
    }
}
