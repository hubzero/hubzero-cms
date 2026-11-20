<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Trac\Tests;

use Hubzero\Trac\Project;
use PHPUnit\Framework\TestCase;

/**
 * A Trac project's fields are its columns; the bookkeeping it keeps beside
 * them is not one of them.
 */
class ProjectFieldsTest extends TestCase
{
    /**
     * @return  void
     */
    public function testFieldsAreTheColumns(): void
    {
        $project = new Project();
        $project->set('name', 'tool');

        $this->assertSame('tool', $project->get('name'));
        $this->assertSame(['id' => null, 'name' => 'tool'], $project->toArray());
    }

    /**
     * @return  void
     */
    public function testClearEmptiesTheFields(): void
    {
        $project = new Project();
        $project->set('id', 4);
        $project->set('name', 'tool');
        $project->clear();

        $this->assertSame(['id' => null, 'name' => null], $project->toArray());
    }
}
