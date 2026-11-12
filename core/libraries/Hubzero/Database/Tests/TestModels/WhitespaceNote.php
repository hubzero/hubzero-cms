<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Model for the write-trimming tests
 */
class WhitespaceNote extends Relational
{
    /**
     * @var  string
     */
    protected $table = 'notes';

    /**
     * @var  array<string, string|callable|array<string, mixed>>
     */
    protected $rules = [
        'body' => 'notempty',
    ];
}
