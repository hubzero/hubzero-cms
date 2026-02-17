<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

/**
 * Copy operation
 */
class DiffOpCopy extends DiffOp
{
    /**
     * Description for 'type'
     *
     * @var string
     */
    public $type = 'copy';

    /**
     * Short description for 'DiffOpCopy'
     *
     * Long description (if any) ...
     *
     * @param      unknown $orig Parameter description (if any) ...
     * @param      boolean $closing Parameter description (if any) ...
     * @return     void
     */
    public function __construct($orig, $closing = false)
    {
        if (!is_array($closing)) {
            $closing = $orig;
        }
        $this->orig = $orig;
        $this->closing = $closing;
    }

    /**
     * Short description for 'reverse'
     *
     * Long description (if any) ...
     *
     * @return     object Return description (if any) ...
     */
    public function reverse()
    {
        return new DiffOpCopy($this->closing, $this->orig);
    }
}
