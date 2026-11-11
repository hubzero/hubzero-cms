<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;
use Hubzero\Facades\Date;

/**
 * macro class for displaying a timestamp
 */
class Timestamp extends Macro
{
    /**
     * Allow macro in partial parsing?
     *
     * @var bool
     */
    public $allowPartial = true;

    /**
     * Returns description of macro, use, and accepted arguments
     *
     * @return     string
     */
    public function description()
    {
        $txt = [];
        $txt['wiki'] = 'Displays a time-stamp in the following format: YYYY-MM-DD HH:MM:SS';
        $txt['html'] = '<p>Displays a time-stamp in the following format: YYYY-MM-DD HH:MM:SS</p>';
        return $txt['html'];
    }

    /**
     * Generate macro output
     *
     * @return     string
     */
    public function render()
    {
        return Date::toSql();
    }
}
