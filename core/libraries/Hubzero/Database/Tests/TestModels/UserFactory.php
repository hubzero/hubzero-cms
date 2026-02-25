<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Factory;
use Hubzero\Database\Tests\TestModels\User;

/**
 * Factory for creating User test models
 */
class UserFactory extends Factory
{
    protected $model = \Hubzero\Facades\User::class;

    public function definition()
    {
        return [
            'name' => 'User ' . self::sequence(),
            'email' => self::email(),
            'status' => 'active',
        ];
    }

    public function inactive()
    {
        return $this->state(['status' => 'inactive']);
    }
}
