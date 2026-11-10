<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Tests;

use Components\Cart\Helpers\Audit;
use Components\Cart\Lib\Auditors\BaseAuditor;
use PHPUnit\Framework\TestCase;

/**
 * A product's model, as its product type records it, picks the class that
 * audits it. Membership and software products have auditors of their own;
 * every other model falls back to the base auditor.
 */
class AuditorClassTest extends TestCase
{
    /**
     * @return  void
     */
    public function testMembershipAndSoftwareHaveAuditors(): void
    {
        foreach (['membership', 'Membership', 'software', 'SOFTWARE'] as $model) {
            $class = Audit::auditorClass($model);

            $this->assertTrue(class_exists($class), $model);
            $this->assertTrue(is_subclass_of($class, BaseAuditor::class), $model);
        }
    }

    /**
     * @return  void
     */
    public function testOtherModelsHaveNone(): void
    {
        foreach (['normal', 'course', 'no such model'] as $model) {
            $this->assertFalse(class_exists(Audit::auditorClass($model)), $model);
        }
    }
}
