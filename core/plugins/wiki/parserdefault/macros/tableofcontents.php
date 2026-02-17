<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault\Macros;

use Plugins\Wiki\Parserdefault\WikiMacro;


/**
 * Wiki macro class for displaying a table of contents for a page
 */
class TableOfContents extends WikiMacro
{
    /**
     * Returns description of macro, use, and accepted arguments
     *
     * @return     array
     */
    public function description()
    {
        $txt = array();
        $txt['wiki'] = 'Outputs a Table of Contents based off the page headers';
        $txt['html'] = '<p>Outputs a Table of Contents based off the page headers</p>';
        return $txt['html'];
    }

    /**
     * Generate macro output
     *
     * @return     string
     */
    public function render()
    {
        return 'MACRO' . $this->uniqPrefix . '[[TableOfContents]]';
    }
}
