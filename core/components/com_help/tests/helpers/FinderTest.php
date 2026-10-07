<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

namespace Components\Help\Tests\Helpers;

use Hubzero\Test\Basic;
use Hubzero\Facades\Facade;
use Hubzero\Container\Container;
use Components\Help\Helpers\Finder;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

require_once dirname(__DIR__, 2) . '/helpers/finder.php';

/**
 * Finder::page() candidate order
 *
 * The finder resolves a help page from a template override, the component's
 * help directory, or a plugin's help directory. These tests pin the order
 * those are tried in, and in particular that giving an extension does not
 * lose the component page (the candidate list used to be edited by numeric
 * index, which broke whenever an entry was added or removed).
 *
 * Runs in separate processes because the finder goes through the global
 * `App` alias, and another test file in the suite replaces that alias at
 * load time with a stub of its own.
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class FinderTest extends Basic
{
    /**
     * Root of the throwaway tree the stubs point at
     *
     * @var  string
     */
    private $root;

    /**
     * The container the bootstrap installed, restored after each test
     *
     * @var  mixed
     */
    private $previousApp;

    /**
     * Point the facades the finder uses at a temporary tree
     *
     * The bootstrap's container has already resolved (and so frozen) some of
     * these services, so swap in a fresh container rather than rebinding.
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->root = sys_get_temp_dir() . '/hz-help-finder-' . getmypid() . '-' . uniqid();
        mkdir($this->root, 0700, true);

        $root = $this->root;
        $app  = new Container();

        $app['template'] = function () use ($root) {
            return (object) ['path' => $root . '/template'];
        };

        $app['component'] = function () use ($root) {
            return new class ($root) {
                private $root;
                public function __construct($root)
                {
                    $this->root = $root;
                }
                public function path($option)
                {
                    return $this->root . '/components/' . substr($option, 4);
                }
            };
        };

        $app['plugin'] = function () use ($root) {
            return new class ($root) {
                private $root;
                public function __construct($root)
                {
                    $this->root = $root;
                }
                public function path($type, $plugin = null)
                {
                    $dir = $this->root . '/plugins/' . $type . ($plugin ? '/' . $plugin : '');
                    return is_dir($dir) ? $dir : '';
                }
            };
        };

        $app['language'] = function () {
            return new class {
                public function getTag()
                {
                    return 'en-GB';
                }
                public function getLanguage()
                {
                    return 'en-GB';
                }
            };
        };

        // Finder::path() asks App::isAdmin(); a bare container does not
        // answer that, so front it with something that does.
        $app['app'] = function () use ($app) {
            return new class ($app) {
                private $container;
                public function __construct($container)
                {
                    $this->container = $container;
                }
                public function isAdmin()
                {
                    return false;
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
     * Put the bootstrap's container back and drop the tree
     *
     * @return  void
     */
    protected function tearDown(): void
    {
        Facade::setApplication($this->previousApp);

        self::rmtree($this->root);

        parent::tearDown();
    }

    /**
     * Create an empty help page at a path relative to the tree root
     *
     * @param   string  $relative
     * @return  string  Absolute path
     */
    private function page($relative)
    {
        $path = $this->root . '/' . $relative;
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0700, true);
        }
        touch($path);
        return $path;
    }

    /**
     * Remove a directory tree
     *
     * @param   string  $path
     * @return  void
     */
    private static function rmtree($path)
    {
        if (is_file($path) || is_link($path)) {
            unlink($path);
            return;
        }
        if (!is_dir($path)) {
            return;
        }
        foreach (scandir($path) as $entry) {
            if ($entry !== '.' && $entry !== '..') {
                self::rmtree($path . '/' . $entry);
            }
        }
        rmdir($path);
    }

    /**
     * Nothing exists: empty string, no exception
     *
     * @return  void
     */
    public function testNoPageReturnsEmptyString()
    {
        $this->assertSame('', Finder::page('com_groups', '', 'membership'));
        $this->assertSame('', Finder::page('com_groups', 'calendar', 'subscriptions'));
    }

    /**
     * A component page is found with no extension
     *
     * @return  void
     */
    public function testComponentPage()
    {
        $expected = $this->page('components/groups/site/help/en-GB/membership.phtml');

        $this->assertSame($expected, Finder::page('com_groups', '', 'membership'));
    }

    /**
     * Giving an extension must not lose the component page
     *
     * @return  void
     */
    public function testComponentPageStillFoundWithExtension()
    {
        $expected = $this->page('components/groups/site/help/en-GB/membership.phtml');

        $this->assertSame($expected, Finder::page('com_groups', 'calendar', 'membership'));
    }

    /**
     * With an extension, the plugin's page outranks the component's
     *
     * @return  void
     */
    public function testPluginPageOutranksComponentPageWithExtension()
    {
        $this->page('components/groups/site/help/en-GB/subscriptions.phtml');
        $expected = $this->page('plugins/groups/calendar/help/en-GB/subscriptions.phtml');

        $this->assertSame($expected, Finder::page('com_groups', 'calendar', 'subscriptions'));
    }

    /**
     * Without an extension, a page named after a plugin falls back to that
     * plugin's index page
     *
     * @return  void
     */
    public function testPluginIndexWithoutExtension()
    {
        $expected = $this->page('plugins/groups/calendar/help/en-GB/index.phtml');

        $this->assertSame($expected, Finder::page('com_groups', '', 'calendar'));
    }

    /**
     * Without an extension, the component's page outranks the plugin index
     *
     * @return  void
     */
    public function testComponentPageOutranksPluginIndexWithoutExtension()
    {
        $this->page('plugins/groups/calendar/help/en-GB/index.phtml');
        $expected = $this->page('components/groups/site/help/en-GB/calendar.phtml');

        $this->assertSame($expected, Finder::page('com_groups', '', 'calendar'));
    }

    /**
     * Template overrides come before everything else, for both shapes
     *
     * @return  void
     */
    public function testTemplateOverridesComeFirst()
    {
        $this->page('components/groups/site/help/en-GB/subscriptions.phtml');
        $this->page('plugins/groups/calendar/help/en-GB/subscriptions.phtml');
        $pluginOverride = $this->page('template/html/plg_groups_calendar/help/en-GB/subscriptions.phtml');

        $this->assertSame($pluginOverride, Finder::page('com_groups', 'calendar', 'subscriptions'));

        $this->page('components/groups/site/help/en-GB/membership.phtml');
        $componentOverride = $this->page('template/html/com_groups/help/en-GB/membership.phtml');

        $this->assertSame($componentOverride, Finder::page('com_groups', '', 'membership'));
    }

    /**
     * An unknown plugin yields no candidate at all, rather than one rooted
     * at the filesystem root
     *
     * @return  void
     */
    public function testUnknownPluginIsSkipped()
    {
        $expected = $this->page('components/groups/site/help/en-GB/membership.phtml');

        $this->assertSame($expected, Finder::page('com_groups', 'nosuchplugin', 'membership'));
    }
}
