<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Builder;

use Hubzero\Facades\Document;
use Hubzero\Facades\Request;

/**
 * Utility class for Sliders elements
 *
 * Legacy mode: jQuery UI accordion with inline JS init.
 * daisyUI mode: native <details>/<summary> collapse elements (pure CSS).
 */
class Sliders
{
    /**
     * Flag for if a pane is currently open or not
     *
     * @var  boolean
     */
    public static $open = false;

    /**
     * Whether we are rendering in daisyui mode
     *
     * @var  boolean
     */
    protected static $blade = false;

    /**
     * Whether the first panel has been rendered (for daisyui open state)
     *
     * @var  boolean
     */
    protected static $firstPanel = true;

    /**
     * Creates a panes and loads the javascript behavior for it.
     *
     * @param   string  $group   The pane identifier.
     * @param   array   $params  An array of options.
     * @return  string
     */
    public static function start($group = 'sliders', $params = array())
    {
        self::behavior($group, $params);
        self::$open = false;
        self::$blade = Document::getCssFramework() === 'daisyui';
        self::$firstPanel = true;

        if (self::$blade) {
            return '<div id="' . $group
                . '" class="join join-vertical w-full">';
        }

        return '<div id="' . $group . '" class="pane-sliders">';
    }

    /**
     * Close the current pane.
     *
     * @return  string  HTML to close the pane
     */
    public static function end()
    {
        $content = '';
        if (self::$open) {
            if (self::$blade) {
                // Close collapse-content + details
                $content .= '</div></details>';
            } else {
                // Close pane-slider content + panel
                $content .= '</div></div>';
            }
        }
        self::$open = false;
        $content .= '</div>';
        return $content;
    }

    /**
     * Begins the display of a new panel.
     *
     * @param   string  $text  Text to display.
     * @param   string  $id    Identifier of the panel.
     * @return  string  HTML to start a panel
     */
    public static function panel($text, $id)
    {
        $content = '';

        if (self::$blade) {
            if (self::$open) {
                // Close previous collapse-content + details
                $content .= '</div></details>';
            } else {
                self::$open = true;
            }
            $open = self::$firstPanel ? ' open' : '';
            self::$firstPanel = false;

            $content .= '<details class="collapse collapse-arrow'
                . ' join-item border border-base-300"'
                . ' id="' . $id . '"' . $open . '>'
                . '<summary class="collapse-title font-medium">'
                . $text . '</summary>'
                . '<div class="collapse-content">';
        } else {
            if (self::$open) {
                $content .= '</div></div>';
            } else {
                self::$open = true;
            }
            $content .= '<h3 class="pane-toggler title" id="'
                . $id . '"><a href="#' . $id . '"><span>'
                . $text . '</span></a></h3>'
                . '<div class="panel">'
                . '<div class="pane-slider content">';
        }

        return $content;
    }

    /**
     * Load the JavaScript behavior.
     *
     * @param   string  $group   The pane identifier.
     * @param   array   $params  Array of options.
     * @return  void
     */
    protected static function behavior($group, $params = array())
    {
        static $loaded = array();

        if (!array_key_exists($group, $loaded)) {
            $loaded[$group] = true;

            // In daisyui mode, <details>/<summary> handles
            // expand/collapse natively — no JS needed.
            if (Document::getCssFramework() === 'daisyui') {
                return;
            }

            $opt = array();
            $opt['heightStyle'] = "'content'";

            $options = array();
            foreach ($opt as $k => $v) {
                if ($v) {
                    $options[] = $k . ': ' . $v;
                }
            }
            $options = '{' . implode(',', $options) . '}';

            Behavior::framework(true);

            \Hubzero\Facades\App::get('document')->addScriptDeclaration(
                "jQuery(document).ready(function($){
					$('div#" . $group . "').accordion(" . $options . ");
				});"
            );
        }
    }
}
