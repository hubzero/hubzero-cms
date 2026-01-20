<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Change operation
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 */
class _DiffOp_Change extends _DiffOp
{
    /**
     * Description for 'type'
     *
     * @var string
     */
    public $type = 'change';

    /**
     * Short description for '_DiffOp_Change'
     *
     * Long description (if any) ...
     *
     * @param      unknown $orig Parameter description (if any) ...
     * @param      unknown $closing Parameter description (if any) ...
     * @return     void
     */
    public function __construct($orig, $closing)
    {
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
        return new _DiffOp_Change($this->closing, $this->orig);
    }
}
