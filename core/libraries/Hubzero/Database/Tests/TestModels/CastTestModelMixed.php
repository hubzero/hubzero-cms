<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Casts\AsJson;
use Hubzero\Database\Casts\AsDateTime;
use Hubzero\Database\Casts\AsCollection;

/**
 * Test model with multiple casts
 */
class CastTestModelMixed extends Relational
{
    protected $table = 'cast_test';
    protected $casts = [
        'options' => AsJson::class,
        'expires' => AsDateTime::class,
        'tags' => AsCollection::class,
    ];

    /**
     * Public accessor for protected isCustomCastClass method
     *
     * @param  string $type Cast type to check
     * @return bool
     */
    public function checkIsCustomCastClass($type)
    {
        return $this->isCustomCastClass($type);
    }
}
