<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Builder;

use Hubzero\Facades\Document;

/**
 * Utility class for Tabs elements.
 *
 * Legacy mode: jQuery UI tabs with <dl>/<dt>/<dd> structure.
 * daisyUI mode: radio-input tabs (pure CSS, no JS).
 */
class Tabs
{
    /**
     * Flag for if a pane is currently open
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
     * Current group name (used as radio input name in daisyui mode)
     *
     * @var  string
     */
    protected static $group = 'tabs';

    /**
     * Whether the first panel has been rendered
     *
     * @var  boolean
     */
    protected static $firstPanel = true;

    /**
     * Creates a panes and creates the JavaScript object for it.
     *
     * @param   string  $group   The pane identifier.
     * @param   array   $params  An array of option.
     * @return  string
     */
    public static function start($group = 'tabs', $params = array())
    {
        self::behavior($group, $params);
        self::$open = false;
        self::$blade = Document::getCssFramework() === 'daisyui';
        self::$group = $group;
        self::$firstPanel = true;

        if (self::$blade) {
            return '<div id="' . $group
                . '" role="tablist" class="tabs tabs-bordered">';
        }

        return '<dl class="tabs" id="' . $group . '">';
    }

    /**
     * Close the current pane
     *
     * @return  string  HTML to close the pane
     */
    public static function end()
    {
        $content = '';

        if (self::$open) {
            if (self::$blade) {
                $content .= '</div>';
            } else {
                $content .= '</dd>';
            }
        }
        self::$open = false;

        if (self::$blade) {
            $content .= '</div>';
        } else {
            $content .= '</dl>';
        }

        return $content;
    }

    /**
     * Begins the display of a new panel.
     *
     * @param   string  $text  Text to display.
     * @param   string  $id    Identifier of the panel.
     * @return  string  HTML to start a new panel
     */
    public static function panel($text, $id)
    {
        $content = '';

        if (self::$blade) {
            if (self::$open) {
                // Close previous tab-content
                $content .= '</div>';
            } else {
                self::$open = true;
            }
            $checked = self::$firstPanel ? ' checked="checked"' : '';
            self::$firstPanel = false;

            $content .= '<input type="radio"'
                . ' name="' . self::$group . '"'
                . ' role="tab"'
                . ' class="tab"'
                . ' aria-label="' . htmlspecialchars($text, ENT_COMPAT, 'UTF-8') . '"'
                . ' id="tab' . $id . '"'
                . $checked . ' />'
                . '<div role="tabpanel"'
                . ' class="tab-content border-base-300 p-4">';
        } else {
            if (self::$open) {
                $content .= '</dd>';
            } else {
                self::$open = true;
            }
            $content .= '<dt id="tab' . $id . '">'
                . '<a href="#tab' . $id . '">'
                . $text . '</a></dt><dd>';
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

        if (!array_key_exists((string) $group, $loaded)) {
            $loaded[(string) $group] = true;

            // In daisyui mode, radio-input tabs are pure CSS —
            // no JavaScript needed.
            if (Document::getCssFramework() === 'daisyui') {
                return;
            }

            Behavior::framework(true);

            \Hubzero\Facades\App::get('document')->addScriptDeclaration(
                'jQuery(document).ready(function($){
					$("dl#' . $group . '.tabs").tabs();
				});'
            );

            Asset::script('system/jquery.tabs.js', false, true);
        }
    }
}
