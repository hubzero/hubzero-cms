<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tests;

use Components\Courses\Models\Permissions;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/models/permissions.php';

/**
 * Course permissions are worked out for a course, offering and section that
 * callers set and read by name.
 */
class PermissionsStateTest extends TestCase
{
    /**
     * @return  void
     */
    public function testTheScopeIsSetAndReadByName(): void
    {
        $permissions = new Permissions(3, 4, 5);

        $this->assertSame(3, $permissions->get('course_id'));
        $this->assertSame(4, $permissions->get('offering_id'));
        $this->assertSame(5, $permissions->get('section_id'));

        $this->assertSame(4, $permissions->set('offering_id', 9));
        $this->assertSame(9, $permissions->get('offering_id'));
        $this->assertSame('none', $permissions->get('nosuchthing', 'none'));
    }

    /**
     * What set() stores is what the model works from
     *
     * @return  void
     */
    public function testSetStateIsTheModelsOwn(): void
    {
        $permissions = new Permissions(3);
        $permissions->set('managers', [7 => 'manager']);

        $this->assertSame([7 => 'manager'], $permissions->managers());
    }
}
