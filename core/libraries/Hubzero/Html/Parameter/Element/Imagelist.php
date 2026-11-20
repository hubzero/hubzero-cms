<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;

/**
 * Renders a imagelist element
 */
class ImageList extends Element
{
    /**
     * Element name
     *
     * @var  string
     */
    protected $name = 'ImageList';

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
        $filter = '\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$';
        $node->addAttribute('filter', $filter);

        $parameter = $this->parent ? $this->parent->loadElement('filelist') : false;

        // No file list element to build on
        if (!$parameter) {
            return '';
        }

        return $parameter->fetchElement($name, $value, $node, $control_name);
    }
}
