<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type\Html;

use Hubzero\Document\Renderer;
use Hubzero\Facades\App;

/**
 * Modules renderer
 *
 * Inspired by Joomla's JDocumentRendererModules class
 */
class Modules extends Renderer
{
    /**
     * Renders multiple modules script and returns the results as a string
     *
     * @param   string  $position  The position of the modules to render
     * @param   array<mixed>   $params    Associative array of values
     * @param   string|null  $content   Module content
     * @return  string  The output of the script
     */
    public function render($position, $params = [], $content = null)
    {
        $renderer = $this->doc->loadRenderer('module');

        $buffer = '';
        foreach (App::get('module')->byPosition($position) as $mod) {
            $buffer .= $renderer->render($mod, $params, $content);
        }

        return $buffer;
    }
}
