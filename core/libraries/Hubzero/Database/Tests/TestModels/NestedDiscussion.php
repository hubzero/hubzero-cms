<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Nested;

/**
 * Nested discussion model for testing
 *
 * Uses scoped nested sets (scope + scope_id)
 */
class NestedDiscussion extends Nested
{
    protected $table = 'nested_discussions';
    protected $namespace = '';
    protected $scopes = ['scope', 'scope_id'];
}
