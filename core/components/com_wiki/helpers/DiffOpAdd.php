<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

/**
 * Add operation
 */
class DiffOpAdd extends DiffOp
{
    /**
     * Description for 'type'
     *
     * @var string
     */
    public $type = 'add';

    /**
     * Short description for 'DiffOpAdd'
     *
     * Long description (if any) ...
     *
     * @param      mixed $lines Parameter description (if any) ...
     * @return     void
     */
    public function __construct($lines)
    {
        $this->closing = $lines;
        $this->orig = false;
    }

    /**
     * Short description for 'reverse'
     *
     * Long description (if any) ...
     *
     * @return     \Components\Wiki\Helpers\DiffOpDelete Return description (if any) ...
     */
    public function reverse()
    {
        return new DiffOpDelete($this->closing);
    }
}
