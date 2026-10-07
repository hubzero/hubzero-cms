<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

namespace Components\Kb\Tests\Models;

use Hubzero\Test\Basic;
use Hubzero\Container\Container;
use Hubzero\Facades\Facade;
use Hubzero\Config\Registry;
use Components\Kb\Models\Article;
use Components\Kb\Models\Category;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

require_once dirname(__DIR__, 2) . '/models/article.php';
require_once dirname(__DIR__, 2) . '/models/category.php';

/**
 * Article::snippet() and Category::link()
 *
 * Runs in separate processes because the models go through global facades
 * that another test file replaces at load time.
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class ArticleSnippetTest extends Basic
{
    /**
     * @var  mixed
     */
    private $previousApp;

    /**
     * The models read component params when built; answer with an empty set
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $app = new Container();
        $app['component'] = function () {
            return new class {
                public function params($option)
                {
                    return new Registry();
                }
            };
        };
        $app['app'] = function () use ($app) {
            return new class ($app) {
                private $container;
                public function __construct($container)
                {
                    $this->container = $container;
                }
                public function get($id)
                {
                    return $this->container->get($id);
                }
                public function has($id)
                {
                    return $this->container->has($id);
                }
            };
        };

        $this->previousApp = Facade::getApplication();
        Facade::setApplication($app);
    }

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        Facade::setApplication($this->previousApp);

        parent::tearDown();
    }

    /**
     * An article with the given body
     *
     * @param   string  $fulltxt
     * @return  Article
     */
    private function article(string $fulltxt): Article
    {
        $article = new Article();
        $article->set('fulltxt', $fulltxt);

        return $article;
    }

    /**
     * The phrase is found, escaped, and marked
     *
     * @return  void
     */
    public function testMarksThePhrase()
    {
        $article = $this->article('<p>Upload your <b>data set</b> &amp; describe it.</p>');

        $this->assertSame('Upload your <mark>data set</mark> &amp; describe it.', $article->snippet('data set'));
    }

    /**
     * Matching ignores case; the original casing is kept
     *
     * @return  void
     */
    public function testMatchesCaseInsensitively()
    {
        $article = $this->article('<p>Your Dataset lives here.</p>');

        $this->assertSame('Your <mark>Dataset</mark> lives here.', $article->snippet('dataset'));
    }

    /**
     * When the phrase is absent, its words are marked instead
     *
     * @return  void
     */
    public function testFallsBackToWords()
    {
        $article = $this->article('<p>Publish the files, then cite the dataset.</p>');

        $this->assertSame(
            'Publish the <mark>files</mark>, then cite the <mark>dataset</mark>.',
            $article->snippet('dataset files')
        );
    }

    /**
     * Field wrappers and markup are stripped before searching
     *
     * @return  void
     */
    public function testStripsFieldWrappersAndTags()
    {
        $article = $this->article('<nb:summary>summary data</nb:summary><p>Body <em>text</em> about data.</p>');

        $this->assertSame('Body text about <mark>data</mark>.', $article->snippet('data'));
    }

    /**
     * A long body is cut to the context either side, on word boundaries
     *
     * @return  void
     */
    public function testCutsLongBodiesAroundTheMatch()
    {
        $article = $this->article(str_repeat('before ', 60) . 'needle here ' . str_repeat('after ', 60));

        $snippet = $article->snippet('needle', 30);

        $this->assertStringStartsWith('&hellip; before', $snippet);
        $this->assertStringEndsWith('after &hellip;', $snippet);
        $this->assertStringContainsString('<mark>needle</mark> here', $snippet);
        $this->assertLessThan(120, strlen(strip_tags($snippet)));
    }

    /**
     * Nothing to show when the term is empty or absent
     *
     * @return  void
     */
    public function testEmptyWhenAbsent()
    {
        $article = $this->article('<p>Nothing relevant.</p>');

        $this->assertSame('', $article->snippet(''));
        $this->assertSame('', $article->snippet('   '));
        $this->assertSame('', $article->snippet('zebra'));
        $this->assertSame('', $this->article('')->snippet('zebra'));
    }

    /**
     * Markup in the term cannot escape into the page
     *
     * @return  void
     */
    public function testTermIsEscaped()
    {
        $article = $this->article('<p>See <b>bold</b> text.</p>');

        $this->assertSame('See <mark>&lt;b&gt;bold</mark> text.', $this->article('See &lt;b&gt;bold text.')->snippet('<b>bold'));
        $this->assertStringNotContainsString('<script', $article->snippet('<script>'));
    }

    /**
     * A category built in code links by its alias when it has no path
     *
     * @return  void
     */
    public function testCategoryLinkFallsBackToAlias()
    {
        $all = new Category();
        $all->set('alias', 'all');

        $this->assertSame('index.php?option=com_kb&section=all', $all->link());

        $stored = new Category();
        $stored->set('alias', 'child');
        $stored->set('path', 'parent/child');

        $this->assertSame('index.php?option=com_kb&section=parent/child', $stored->link());
    }
}
