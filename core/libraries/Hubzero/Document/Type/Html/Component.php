<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type\Html;

use Hubzero\Document\Renderer;

/**
 * Component renderer
 *
 * Inspired by Joomla's JDocumentRendererComponent class
 */
class Component extends Renderer
{
    /**
     * Renders a component script and returns the results as a string
     *
     * @param   string|null  $component  The name of the component to render
     * @param   array<mixed>   $params     Associative array of values
     * @param   string|null  $content    Content script
     * @return  string  The output of the script
     */
    public function render($component = null, $params = [], $content = null)
    {
        return (string) $content;
    }
}
