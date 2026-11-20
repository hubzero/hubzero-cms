<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
