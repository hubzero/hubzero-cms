<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Base\Obj;

/**
 * An object that keeps state of its own beside its data
 */
class StatefulObj extends Obj
{
    /**
     * @var  string
     */
    public $title = 'declared';

    protected $cache = ['kept' => true];

    /**
     * @var  int
     */
    private $count = 0;

    /**
     * The object's own counter, which is not part of its data
     *
     * @return  int
     */
    public function counted(): int
    {
        return $this->count;
    }
}
