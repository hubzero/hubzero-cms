<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Factory;

/**
 * Factory for creating User test models
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => 'User ' . self::sequence(),
            'email' => self::email(),
            'status' => 'active',
        ];
    }

    /**
     * @return  $this
     */
    public function inactive()
    {
        return $this->state(['status' => 'inactive']);
    }
}
