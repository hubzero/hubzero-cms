<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault\Macros;

use Plugins\Wiki\Parserdefault\WikiMacro;


/**
 * Wiki macro class for displaying all available macros and their documentation
 */
class Macrolist extends WikiMacro
{
    /**
     * Returns description of macro, use, and accepted arguments
     *
     * @return     array
     */
    public function description()
    {
        $txt = array();
        $txt['wiki'] = 'Displays a list of all installed Wiki macros, including documentation if available. '
            . 'Optionally, the name of a specific macro can be provided as an argument. In that case, only the '
            . 'documentation for that macro will be rendered.';
        $txt['html'] = '<p>Displays a list of all installed Wiki macros, including documentation if available. '
            . 'Optionally, the name of a specific macro can be provided as an argument. In that case, only the '
            . 'documentation for that macro will be rendered.</p>';
        return $txt['html'];
    }

    /**
     * Generate macro output
     *
     * @return     string
     */
    public function render()
    {
        $path = dirname(__FILE__);

        $d = @dir($path);

        $macros = array();

        if ($d) {
            while (false !== ($entry = $d->read())) {
                $img_file = $entry;
                if (
                    is_file($path . DS . $entry)
                    && substr($entry, 0, 1) != '.'
                    && strtolower($entry) !== 'index.html'
                ) {
                    if (preg_match("#php#i", $entry)) {
                        $macros[] = $entry;
                    }
                }
            }

            $d->close();
        }

        $m = array();
        foreach ($macros as $f) {
            include_once $path . DS . $f;

            $f = str_replace('.php', '', $f);

            $macroname = ucfirst($f) . 'Macro';

            // Try namespaced class name (preferred)
            if (!class_exists($macroname)) {
                $macroname = __NAMESPACE__ . '\\' . ucfirst($f);
            }

            if (class_exists($macroname)) {
                $macro = new $macroname();

                $displayname = $macro->name();
                $m[strtolower($displayname)] = '<dt id="'
                    . strtolower($displayname)
                    . '"><code>&#91;&#91;'
                    . ucfirst($displayname)
                    . '(args)&#93;&#93;</code></dt><dd>'
                    . $macro->description()
                    . '</dd>';
            }
        }
        asort($m);

        $txt  = '<dl>' . "\n";
        $txt .= implode("\n", $m);
        $txt .= '</dl>' . "\n";

        return $txt;
    }
}
