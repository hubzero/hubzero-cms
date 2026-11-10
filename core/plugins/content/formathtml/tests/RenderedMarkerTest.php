<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Tests;

use Hubzero\Facades\Facade;
use PHPUnit\Framework\TestCase;
use Plugins\Content\Formathtml\Formathtml;
use Plugins\Content\Formatwiki\Formatwiki;

require_once dirname(__DIR__) . '/formathtml.php';
require_once dirname(__DIR__) . '/parser.php';
require_once dirname(__DIR__, 2) . '/formatwiki/formatwiki.php';

/**
 * formatwiki marks its own output {FORMAT:RENDERED}, and formathtml skips
 * the purifier for it, but only when formatwiki registered that exact
 * content in this request; the marker alone is text anybody can store
 */
class RenderedMarkerTest extends TestCase
{
    /**
     * What formatwiki's output looks like: markup the purifier would change
     */
    private const RENDERED = '<!-- {FORMAT:RENDERED} --><p data-ref="1">Wiki</p>';

    /**
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
            public function root()
            {
                return 'https://hub.example.org/';
            }

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
        // The purifier caches its definitions per client; with no directory
        // to put them in it falls back to the system temporary directory
        $app['client'] = fn() => (object) ['name' => 'site', 'alias' => 'site'];
        $app['filesystem'] = fn() => new class {
            public function makeDirectory($path)
            {
                return false;
            }
        };

        Formatwiki::$rendered = [];
    }

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        $app = Facade::getApplication();
        foreach (['router', 'request', 'html.builder', 'client', 'filesystem'] as $service) {
            $app->forget($service);
        }

        Formatwiki::$rendered = [];

        parent::tearDown();
    }

    /**
     * Content formatwiki rendered in this request is left as it is
     *
     * @return  void
     */
    public function testContentFormatwikiRenderedIsNotPurified()
    {
        Formatwiki::$rendered[md5(self::RENDERED)] = true;

        $this->assertSame('<p data-ref="1">Wiki</p>', $this->prepare(self::RENDERED));
    }

    /**
     * The same marker on content formatwiki did not render is purified
     *
     * @return  void
     */
    public function testStoredMarkerIsPurified()
    {
        $this->assertSame('<p>Wiki</p>', $this->prepare(self::RENDERED));
    }

    /**
     * What formathtml makes of an entry's content
     *
     * @param   string  $content
     * @return  string
     */
    private function prepare($content)
    {
        $plugin  = new Formathtml(null, ['name' => 'formathtml', 'type' => 'content']);
        $article = (object) ['content' => $content];
        $params  = ['option' => 'com_blog'];

        $plugin->onContentPrepare('com_blog.entry.content', $article, $params);

        return trim($article->content);
    }
}
