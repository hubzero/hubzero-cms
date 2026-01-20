<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Add operation
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 */
class _DiffOp_Add extends _DiffOp
{
    /**
     * Description for 'type'
     *
     * @var string
     */
    public $type = 'add';

    /**
     * Short description for '_DiffOp_Add'
     *
     * Long description (if any) ...
     *
     * @param      unknown $lines Parameter description (if any) ...
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
     * @return     object Return description (if any) ...
     */
    public function reverse()
    {
        return new _DiffOp_Delete($this->closing);
    }
}
