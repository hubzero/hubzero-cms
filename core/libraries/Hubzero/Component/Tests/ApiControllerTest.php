<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ApiControllerTest extends TestCase
{
    #[Test]
    public function apiControllerNeverParticipatesInInertiaProtocol(): void
    {
        $controller = new DummyApiController(new Response(), array(
            'name' => 'example',
            'controller' => 'dummy'
        ));

        $this->assertFalse($controller->inspectInertiaState());
        $this->assertFalse($controller->inspectHtmxState());
    }
}
