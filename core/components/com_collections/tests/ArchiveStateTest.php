<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Tests;

use Components\Collections\Models\Archive;
use PHPUnit\Framework\TestCase;

/**
 * A collections archive belongs to an object that callers read by name.
 */
class ArchiveStateTest extends TestCase
{
    /**
     * @return  void
     */
    public function testTheOwnerIsReadByName(): void
    {
        $archive = new Archive('member', 5);

        $this->assertSame('member', $archive->get('object_type'));
        $this->assertSame(5, $archive->get('object_id'));

        $this->assertNull($archive->set('isFollowing', true));
        $this->assertTrue($archive->get('isFollowing'));
        $this->assertSame('none', $archive->get('nosuchthing', 'none'));
    }
}
