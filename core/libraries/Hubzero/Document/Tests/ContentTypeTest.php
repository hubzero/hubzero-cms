<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Tests;

use Hubzero\Document\Type\Error;
use Hubzero\Document\Type\Html;
use Hubzero\Document\Type\Json;
use Hubzero\Document\Type\Raw;
use Hubzero\Document\Type\Xml;
use PHPUnit\Framework\TestCase;

/**
 * Each kind of document knows the content type it is sent as; render()
 * puts it in the Content-Type header.
 */
class ContentTypeTest extends TestCase
{
    /**
     * @return  void
     */
    public function testEachDocumentKnowsItsContentType(): void
    {
        $this->assertSame('application/json', new Json()->getMimeEncoding());
        $this->assertSame('text/html', new Error()->getMimeEncoding());
        $this->assertSame('text/html', new Raw()->getMimeEncoding());
        $this->assertSame('application/xml', new Xml()->getMimeEncoding());
        $this->assertSame('text/html', new Html()->getMimeEncoding());
    }
}
