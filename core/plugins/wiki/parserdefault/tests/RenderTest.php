<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault\Tests;

use Hubzero\Facades\Facade;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The wiki parser renders what it set aside: code, HTML, math, anchors, lists
 * and the contents list come back as they went in
 */
class RenderTest extends TestCase
{
    /**
     * Wiki markup, and the HTML it renders to
     *
     * @return  array
     */
    public static function markup(): array
    {
        return [
            'code block' => [
                [],
                '{{{
<b>x</b>
}}}',
                '<pre><code>
&#60;b&#62;x&#60;/b&#62;
</code></pre>
',
            ],
            'inline code' => [
                [],
                'a {{{inline <i>}}} b',
                '<p>a <code>inline &#60;i&#62;</code> b
</p>',
            ],
            'html block' => [
                [],
                '{{{#!html
<p>hi</p>
}}}',
                '

<p>hi</p>

',
            ],
            'anchor' => [
                [],
                'Text [[Anchor(here)]] more',
                '<p>Text <span id="here"></span> more
</p>',
            ],
            'math' => [
                [],
                '<math>x^2</math>',
                '<p><span class="asciimath">$$x^2$$</span>
</p>',
            ],
            'bold and italic' => [
                [],
                'x \'\'\'bold\'\'\' and \'\'it\'\'',
                '<p>x <b>bold</b> and <i>it</i>
</p>',
            ],
            'nested list' => [
                [],
                ' * a
 * b
   * c
',
                '<ul><li> a
</li><li> b
<ul><li> c
</li></ul>
<!-- common: 0, last: 2, prefx:0, * --></li></ul>
<!-- common: 0, last: 1, prefx:0, * -->',
            ],
            'code after a list' => [
                [],
                ' * item

{{{
code
}}}
',
                '<ul><li> item
</li></ul>
<!-- common: 0, last: 1, prefx:0, * --><pre><code>
code
</code></pre>
',
            ],
            'contents list' => [
                ['automatic_toc' => 'inline'],
                '== One ==
text
== Two ==
more
=== Three ===
still
== Four ==
end',
                '<div class="article-toc macro-toc">
<h3 class="article-toc-heading">Contents</h3>

<ul class=\'toc-list\'>
<li class="toclevel toclevel-1"><div class="toc-item"><button class="toggle-button" aria-expanded="false" aria-label="Toggle subsection"></button><a href="/wiki#content-header"><span class="tocnumber"> </span><span class="toctext">(Top)</span></a></div>
<li class="toclevel toclevel-1"><div class="toc-item"><button class="toggle-button" aria-expanded="false" aria-label="Toggle subsection"></button><a href="/wiki#One"><span class="tocnumber">1 </span><span class="toctext">One</span></a></div></li>

<li class="toclevel toclevel-1"><div class="toc-item"><button class="toggle-button" aria-expanded="false" aria-label="Toggle subsection"></button><a href="/wiki#Two"><span class="tocnumber">2 </span><span class="toctext">Two</span></a></div>
<ul class=\'toc-list\' style=\'display: none;\'>
<li class="toclevel toclevel-2"><div class="toc-item"><button class="toggle-button" aria-expanded="false" aria-label="Toggle subsection"></button><a href="/wiki#Three"><span class="tocnumber">2.1 </span><span class="toctext">Three</span></a></div></li>
</ul>
</li>

<li class="toclevel toclevel-1"><div class="toc-item"><button class="toggle-button" aria-expanded="false" aria-label="Toggle subsection"></button><a href="/wiki#Four"><span class="tocnumber">3 </span><span class="toctext">Four</span></a></div></li>
</ul>
</div>

<h2><span id="One" class="tp-headline"> One </span> </h2>
<p>text
</p>
<h2><span id="Two" class="tp-headline"> Two </span> </h2>
<p>more
</p>
<h3><span id="Three" class="tp-headline"> Three </span> </h3>
<p>still
</p>
<h2><span id="Four" class="tp-headline"> Four </span> </h2>
<p>end
</p>',
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
     * @param   array   $config
     * @param   string  $markup
     * @param   string  $html
     * @return  void
     */
    #[DataProvider('markup')]
    public function testRenders(array $config, string $markup, string $html): void
    {
        $parser = new \Plugins\Wiki\Parserdefault\WikiParser(array_merge(
            ['option' => 'com_wiki', 'pagename' => 'Test', 'scope' => '', 'url' => '/wiki'],
            $config,
        ));

        $this->assertSame($html, $parser->parse($markup));
    }
}
