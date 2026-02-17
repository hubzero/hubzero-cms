<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault\Macros;

use Plugins\Wiki\Parserdefault\WikiMacro;


/**
 * Wiki macro for unreferenced page
 */
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class Unreferenced extends WikiMacro
{
    /**
     * Returns description of macro, use, and accepted arguments
     *
     * @return     array
     */
    public function description()
    {
        $txt = array();
        $txt['wiki'] = 'Displays a notice that a section is unreferenced. Accepts a date as the only argument.';
        $txt['html'] = '<p>Displays a notice that a section is unreferenced. Accepts a date as the only argument.</p>';
        return $txt['html'];
    }

    /**
     * Generate macro output
     *
     * @return     string
     */
    public function render()
    {
        $dt = '';

        if ($this->args) {
            $dt .= ' <span class="mbox-date">(' . $this->args . ')</span>';
        }

        return '<div class="mbox-content mbox-unreferenced"><p class="mbox-text">'
            . 'This section <strong>does not cite any references or sources</strong>.' . $dt . '</span></p></div>';
    }
}
