<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

/**
 * Delete operation
 */
class DiffOpDelete extends DiffOp
{
    /**
     * Description for 'type'
     *
     * @var string
     */
    public $type = 'delete';

    /**
     * Short description for 'DiffOpDelete'
     *
     * Long description (if any) ...
     *
     * @param      unknown $lines Parameter description (if any) ...
     * @return     void
     */
    public function __construct($lines)
    {
        $this->orig = $lines;
        $this->closing = false;
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
        return new DiffOpAdd($this->orig);
    }
}
