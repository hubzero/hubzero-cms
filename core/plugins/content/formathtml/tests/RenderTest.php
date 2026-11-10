<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Tests;

use Hubzero\Facades\Facade;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Plugins\Content\Formathtml\Parser;

require_once dirname(__DIR__) . '/parser.php';

/**
 * The HTML parser sets preformatted text, code and macros aside while it
 * works and puts each back as it was; a macro it cannot find is dropped,
 * except inside preformatted text, which is left alone
 */
class RenderTest extends TestCase
{
    /**
     * HTML in, HTML out
     *
     * @return  array
     */
    public static function markup(): array
    {
        return [
            'paragraph' => [
                '<p>Plain <b>HTML</b> stays</p>',
                '
<p>Plain <b>HTML</b> stays</p>',
            ],
            'pre block' => [
                '<pre>
<b>raw</b> &amp; spaced
</pre>',
                '
<pre>
<b>raw</b> &amp; spaced
</pre>',
            ],
            'code block' => [
                '<p>run <code>a < b && c</code> now</p>',
                '
<p>run <code>a < b && c</code> now</p>',
            ],
            'pre and code' => [
                '<pre>one</pre>
<p><code>two</code></p>
<pre>three</pre>',
                '
<pre>one</pre>
<p><code>two</code></p>
<pre>three</pre>',
            ],
            'unknown macro' => [
                '<p>before [[NoSuchMacro(x)]] after</p>',
                '
<p>before  after</p>',
            ],
            'macro in pre' => [
                '<pre>[[NoSuchMacro(x)]]</pre>',
                '
<pre>[[NoSuchMacro(x)]]</pre>',
            ],
        ];
    }

    /**
     * Stand in for the router, request and HTML builder the parser reaches
     * for while it renders
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $app = Facade::getApplication();
        $app['router'] = fn() => new class {
            public function url($url, $xhtml = true, $ssl = null)
            {
                return '/' . ltrim($url, '/');
            }
        };
        $app['request'] = fn() => new class {
            public function __call($method, $args)
            {
                return $args[1] ?? null;
            }
        };
        $app['html.builder'] = fn() => new class {
            public function __call($method, $args)
            {
                return '';
            }
        };
    }

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        $app = Facade::getApplication();
        foreach (['router', 'request', 'html.builder'] as $service) {
            $app->forget($service);
        }

        parent::tearDown();
    }

    /**
     * @param   string  $markup
     * @param   string  $html
     * @return  void
     */
    #[DataProvider('markup')]
    public function testRenders(string $markup, string $html): void
    {
        $parser = new Parser(['option' => 'com_content']);

        $this->assertSame($html, $parser->parse($markup));
    }
}
