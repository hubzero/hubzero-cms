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
            /**
             * @param   string  $url
             * @param   bool  $xhtml
             * @param   bool|null  $ssl
             * @return  string
             */
            public function url($url, $xhtml = true, $ssl = null)
            {
                return '/' . ltrim($url, '/');
            }
        };
        $app['request'] = fn() => new class {
            /**
             * @return  string
             */
            public function root()
            {
                return 'https://hub.example.org/';
            }

            /**
             * @param   string  $method
             * @param   array<int, mixed>  $args
             * @return  mixed
             */
            public function __call($method, $args)
            {
                return $args[1] ?? null;
            }
        };
        $app['html.builder'] = fn() => new class {
            /**
             * @param   string  $method
             * @param   array<int, mixed>  $args
             * @return  string
             */
            public function __call($method, $args)
            {
                return '';
            }
        };
        // The purifier caches its definitions per client; with no directory
        // to put them in it falls back to the system temporary directory
        $app['client'] = fn() => (object) ['name' => 'site', 'alias' => 'site'];
        $app['filesystem'] = fn() => new class {
            /**
             * @param   string  $path
             * @return  false
             */
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
        $this->assertNotNull($app);
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
     * Saved content gets the HTML marker in front of it
     *
     * @return  void
     */
    public function testSavedContentIsMarked()
    {
        $this->assertSame('<!-- {FORMAT:HTML} --><p>Text</p>', $this->save('<p>Text</p>'));
    }

    /**
     * Content with nothing left once sanitised is saved empty, not as a
     * marker that reads as content to anything checking for some
     *
     * @return  void
     */
    public function testNothingIsSavedEmpty()
    {
        $this->assertSame('', $this->save('<!-- {FORMAT:HTML} -->'));
        $this->assertSame('', $this->save("<!-- {FORMAT:HTML} -->\n "));
        $this->assertSame('', $this->save('<script>x</script>'));
    }

    /**
     * A forum category's description is plain text: saved as typed, without a marker, and a marker saved
     * there before is dropped
     *
     * @return  void
     */
    public function testPlainTextIsSavedAsTyped()
    {
        $context = 'com_forum.category.description';
        $this->assertSame('Tips & <tricks>', $this->save('Tips & <tricks>', $context));
        $this->assertSame('How the site works', $this->save('<!-- {FORMAT:HTML} -->How the site works', $context));
    }

    /**
     * What formathtml stores for a post's description
     *
     * @param   string  $content
     * @param   string  $context
     * @return  string
     */
    private function save($content, $context = 'com_collections.post.description')
    {
        $plugin = new Formathtml(null, [
            'name'   => 'formathtml',
            'type'   => 'content',
            'params' => ['applyFormat' => 1, 'sanitizeBefore' => 1],
        ]);
        $post = new \Hubzero\Base\Obj();
        $post->set('description', $content);

        $plugin->onContentBeforeSave($context, $post, false);

        return (string) $post->get('description');
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
        $article = new \stdClass();
        $article->content = $content;
        $params  = ['option' => 'com_blog'];

        $plugin->onContentPrepare('com_blog.entry.content', $article, $params);

        return trim($article->content ?? '');
    }
}
