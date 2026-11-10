<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault\Macros;

use Plugins\Wiki\Parserdefault\WikiMacro;
use Hubzero\Facades\Route;

/**
 * Wiki macro class for dipslaying a tag
 */
class Tag extends WikiMacro
{
    /**
     * Allow macro in partial parsing?
     *
     * @var string
     */
    public $allowPartial = true;

    /**
     * Returns description of macro, use, and accepted arguments
     *
     * @return     array
     */
    public function description()
    {
        $txt = [];
        $txt['wiki'] = 'This macro will generate a link to a Tag.';
        $txt['html'] = '<p>This macro will generate a link to a Tag.</p>';
        return $txt['html'];
    }

    /**
     * Generate macro output
     *
     * @return     string
     */
    public function render()
    {
        $tag = $this->args;

        if ($tag) {
            // Perform query
            $this->db->setQuery("SELECT raw_tag FROM `#__tags` WHERE tag=" . $this->db->quote($tag) . " LIMIT 1");
            $a = $this->db->loadResult();

            // Did we get a result from the database?
            if ($a) {
                // Build and return the link
                return '<a href="'
                    . Route::url('index.php?option=com_tags&tag='
                    . $tag)
                    . '">'
                    . stripslashes($a)
                    . '</a>';
            } else {
                // Return error message
                return '(' . $tag . ' not found)';
            }
        } else {
            return '';
        }
    }
}
