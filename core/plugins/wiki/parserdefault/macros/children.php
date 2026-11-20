<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault\Macros;

use Plugins\Wiki\Parserdefault\WikiMacro;
use Hubzero\Facades\Route;

/**
 * Wiki macro class for listing children of a page
 */
class Children extends WikiMacro
{
    /**
     * Returns description of macro, use, and accepted arguments
     *
     * @return  string
     */
    public function description()
    {
        $txt = [];
        $txt['wiki'] = 'Inserts an alphabetic list of all sub-pages (children) of the current page into the output. '
            . 'Accepts two parameters:' . "\n"
            . ' * \'\'\'depth: how deep to mine for pages. Default is one level.' . "\n"
            . ' * \'\'\'description: show/hide the first line of text from the page.';
        $txt['html'] = '<p>Inserts an alphabetic list of all sub-pages (children) of the current page into the output. '
            . 'Accepts one parameter:</p>
		<ul>
			<li><strong>depth</strong>: how deep to mine for pages. Default is one level.</li>
			<!-- <li><strong>description</strong>: show/hide the first line of text from the page</li> -->
		</ul>
		<p>Example usage: <code>[[Children(depth=3)]]</code></p>';
        return $txt['html'];
    }

    /**
     * Generate macro output
     *
     * @return  string
     */
    public function render()
    {
        $depth = 1;
        $description = 0;

        if ($this->args) {
            $args = explode(',', $this->args);
            foreach ($args as $arg) {
                $arg = trim($arg);
                if (substr($arg, 0, 6) == 'depth=') {
                    $bits = explode('=', $arg);
                    $depth = intval(trim(end($bits)));
                    continue;
                }
                if (substr($arg, 0, 12) == 'description=') {
                    $bits = explode('=', $arg);
                    $description = intval(trim(end($bits)));
                    continue;
                }
            }
        }

        return $this->listChildren(1, $depth, $this->pageid);
    }

    /**
     * List children of a page
     *
     * @param   integer  $currentDepth  How far down the tree we are
     * @param   integer  $targetDepth   How far down the tree to go
     * @param   integer  $pageid
     * @return  string   HMTL
     */
    private function listChildren($currentDepth, $targetDepth, $pageid)
    {
        $html = '';

        if ($currentDepth > $targetDepth) {
            return $html;
        }

        $rows = \Components\Wiki\Models\Page::all()
            ->whereEquals('parent', $pageid)
            ->whereEquals('state', 1)
            ->rows();

        if ($rows->count()) {
            $html = '<ul>';
            foreach ($rows as $row) {
                // rows() already yields Page models; re-wrapping one in the
                // constructor binds nothing and printed an empty link

                $html .= '<li><a href="' . Route::url($row->link()) . '">';
                $html .= stripslashes($row->get('title', $row->get('pagename')));
                $html .= '</a>';
                $html .= $this->listChildren($currentDepth + 1, $targetDepth, $row->get('id'));
                $html .= '</li>' . "\n";
            }
            $html .= '</ul>';
        } elseif ($currentDepth == 1) {
            // Return error message
            return '<p>(No sub-pages to display)</p>';
        }

        return $html;
    }
}
