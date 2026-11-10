<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Tests;

use Hubzero\User\Group;
use Hubzero\User\Password;
use PHPUnit\Framework\TestCase;

/**
 * A group's and a password record's fields are their columns; the
 * bookkeeping each keeps beside them is not one of them.
 */
class GroupFieldsTest extends TestCase
{
    /**
     * @return  void
     */
    public function testGroupFieldsCanBeSetAndCleared(): void
    {
        $group = new Group();
        $group->set('cn', 'team');
        $group->set('description', 'The team');

        $this->assertSame('team', $group->get('cn'));
        $this->assertSame('The team', $group->get('description'));
        $this->assertTrue(isset($group->cn));

        $group->clear();

        $this->assertNull($group->get('cn'));
        $this->assertSame([], $group->get('members'));
        $this->assertSame(0, $group->get('discussion_email_autosubscribe', 0) ?? 0);
    }

    /**
     * @return  void
     */
    public function testPasswordFieldsCanBeSetAndCleared(): void
    {
        $password = new \ReflectionClass(Password::class)->newInstanceWithoutConstructor();
        $password->clear();
        $password->set('user_id', 7);
        $password->set('shadowMax', 90);

        $this->assertSame(7, $password->get('user_id'));
        $this->assertSame(90, $password->get('shadowMax'));
        $this->assertNull($password->get('shadowMin'));

        $password->clear();

        $this->assertNull($password->get('user_id'));
    }
}
