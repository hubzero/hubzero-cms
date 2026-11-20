<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;

/**
 * Renders HTML
 */
class Html extends Element
{
    /**
     * Element name
     *
     * @var  string
     */
    protected $name = 'Html';

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
        return (string) $node;
    }
}
