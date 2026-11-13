<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Module\Tests;

use Hubzero\Test\Basic;
use Hubzero\Module\Module;
use Hubzero\Module\Loader;
use Hubzero\Config\Registry;
use Hubzero\Cache\Storage\Memory;
use Hubzero\Template\Tests\Support\Environment;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * How a module is entered: the loader calls display(), the base display()
 * calls run(), and a module that caches checks the cache in display()
 * before it calls run()
 *
 * Runs in separate processes because the code under test goes through the
 * `App` facade, whose container this test replaces.
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class EntryTest extends Basic
{
    /**
     * @var  Environment
     */
    private $env;

    /**
     * @var  Memory
     */
    private $store;

    /**
     * Directory of the module written for the loader test
     *
     * @var  string|null
     */
    private $probe = null;

    /**
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->env = Environment::create();
        $this->store = new Memory();

        $app = \Hubzero\Facades\Facade::getApplication();
        $store = $this->store;
        $app['cache.store'] = function () use ($store) {
            return $store;
        };
        $app['config'] = function () {
            return new Registry(['debug' => 0]);
        };
        // The cached copy is stamped with a date in the database's format
        $app['db'] = function () {
            return new class {
                public function getDateFormat()
                {
                    return 'Y-m-d H:i:s';
                }
            };
        };
    }

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        if ($this->probe) {
            @unlink($this->probe . '/mod_entryprobe.php');
            @rmdir($this->probe);
            @rmdir(dirname($this->probe));
        }

        $this->env->restore();

        parent::tearDown();
    }

    /**
     * The loader enters a module through display(), where a caching module
     * checks the cache, not through run()
     *
     * @return  void
     */
    public function testLoaderEntersThroughDisplay()
    {
        $this->probe = PATH_APP . '/modules/mod_entryprobe';
        @mkdir($this->probe, 0700, true);
        file_put_contents($this->probe . '/mod_entryprobe.php', <<<'PHP'
            <?php

            namespace Modules\Entryprobe;

            class Entryprobe extends \Hubzero\Module\Module
            {
                public function display()
                {
                    echo 'display';
                }

                public function run()
                {
                    echo 'run';
                }
            }
            PHP);

        $app = \Hubzero\Facades\Facade::getApplication();
        $app['language'] = function () {
            return new class {
                public function load()
                {
                    return true;
                }
            };
        };

        $module = (object) [
            'id'     => 3,
            'module' => 'mod_entryprobe',
            'title'  => 'Probe',
            'params' => '{}',
        ];

        $this->assertSame('display', new Loader($app)->render($module));
    }

    /**
     * A module that overrides only run() is reached through display()
     *
     * @return  void
     */
    public function testDisplayRunsTheModule()
    {
        $module = new class (new Registry(), (object) ['id' => 1]) extends Module {
            public function run()
            {
                echo 'ran';
            }
        };

        $this->assertSame('ran', $this->rendered($module));
    }

    /**
     * A caching module stores its first rendering and serves it after that
     * without running again
     *
     * @return  void
     */
    public function testCachingModuleRunsOnceAndThenServesTheStoredCopy()
    {
        $module = $this->cachingModule(['cache' => 1, 'cache_time' => 15]);

        $first  = $this->rendered($module);
        $second = $this->rendered($module);

        $this->assertSame(1, $module->runs);
        $this->assertStringStartsWith('ran', $first);
        $this->assertSame($first, $second);
        $this->assertSame($first, $this->store->get('modules.7'));
    }

    /**
     * Without the cache parameter, the module runs every time
     *
     * @return  void
     */
    public function testModuleWithCachingOffRunsEveryTime()
    {
        $module = $this->cachingModule(['cache' => 0, 'cache_time' => 15]);

        $this->rendered($module);
        $this->rendered($module);

        $this->assertSame(2, $module->runs);
        $this->assertNull($this->store->get('modules.7'));
    }

    /**
     * A module written the way the core caching modules are
     *
     * @param   array  $params
     * @return  Module&object{runs: int}  A module that counts how often it ran
     */
    private function cachingModule(array $params)
    {
        return new class (new Registry($params), (object) ['id' => 7]) extends Module {
            public $runs = 0;

            public function run()
            {
                $this->runs++;
                echo 'ran';
            }

            public function display()
            {
                if ($content = $this->getCacheContent()) {
                    echo $content;
                    return;
                }

                $this->run();
            }
        };
    }

    /**
     * What a module prints when the loader enters it
     *
     * @param   Module  $module
     * @return  string
     */
    private function rendered(Module $module)
    {
        ob_start();
        $module->display();
        return ob_get_clean();
    }
}
