<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parsermarkdown\Tests;

use Hubzero\Facades\Facade;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/markdownparser.php';

/**
 * The markdown parser restores the math it set aside before rendering
 */
class MathRenderTest extends TestCase
{
    /**
     * Wiki markup, and the HTML it renders to
     *
     * @return  array
     */
    public static function markup(): array
    {
        return [
            'markdown math' => [
                [],
                'Area is <math>\\pi r^2</math> here.',
                '<p>Area is <span class="asciimath">$$\\pi r^2$$</span> here.</p>
',
            ],
            'markdown code and math' => [
                [],
                '`a <b>` and <math>x</math>

    indented <code>',
                '<p><code>a &lt;b&gt;</code> and <span class="asciimath">$$x$$</span></p>
<pre><code>indented &lt;code&gt;
</code></pre>
',
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
        $app['router'] = fn () => new class {
            public function url($url, $xhtml = true, $ssl = null)
            {
                return '/' . ltrim($url, '/');
            }
        };
        $app['request'] = fn () => new class {
            public function __call($method, $args)
            {
                return $args[1] ?? null;
            }
        };
        $app['html.builder'] = fn () => new class {
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
     * @param   array   $config
     * @param   string  $markup
     * @param   string  $html
     * @return  void
     */
    #[DataProvider('markup')]
    public function testRenders(array $config, string $markup, string $html): void
    {
        $parser = new \Plugins\Wiki\Parsermarkdown\MarkdownParser(array_merge(['style' => 'Markdown'], $config));

        $this->assertSame($html, $parser->parse($markup));
    }
}
