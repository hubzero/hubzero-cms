<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tests;

use Components\Tools\Models\Tool;
use Components\Tools\Models\Version;
use Hubzero\Container\Container;
use Hubzero\Facades\Facade;
use PHPUnit\Framework\TestCase;

/**
 * The tool and version models keep their own state (the list keys, which
 * keys changed, whether to write everything) in properties beside the
 * columns. clear() and the UPDATE they write must leave that state alone.
 */
class ModelColumnsTest extends TestCase
{
    /**
     * The facade application before the test
     *
     * @var  \Hubzero\Container\Container|null
     */
    private $previousApp = null;

    /**
     * The driver the models write through
     *
     * @var  RecordingDriver
     */
    private $db;

    /**
     * @return  void
     */
    protected function setUp(): void
    {
        $this->db = new RecordingDriver([]);

        $app = new Container();
        // App::get('db') asks the container for itself first
        $app['app'] = $app;
        $app['db'] = $this->db;
        $app['log'] = new class {
            /**
             * @param   string        $name
             * @param   array<mixed>  $args
             * @return  void
             */
            public function __call($name, $args) {}
        };

        $this->previousApp = Facade::getApplication();
        Facade::setApplication($app);
    }

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        if ($this->previousApp !== null) {
            Facade::setApplication($this->previousApp);
        }
    }

    /**
     * @return  void
     */
    public function testVersionUpdateNamesOnlyColumns(): void
    {
        $version = (new \ReflectionClass(Version::class))->newInstanceWithoutConstructor();
        $version->clear();
        $version->id = 7;
        $version->title = 'Sieve';

        $version->update();

        $this->assertSame(
            "UPDATE #__tool_version SET `id`='7',`title`='Sieve' WHERE `id`='7';",
            $this->db->queries[0] ?? null,
        );
    }

    /**
     * @return  void
     */
    public function testToolUpdateNamesOnlyColumns(): void
    {
        $tool = (new \ReflectionClass(Tool::class))->newInstanceWithoutConstructor();
        $tool->clear();
        $tool->id = 3;
        $tool->title = 'Sieve';

        $tool->update();

        $this->assertSame(
            "UPDATE `#__tool` SET `id`='3',`title`='Sieve' WHERE `id`='3';",
            $this->db->queries[0] ?? null,
        );
    }
}
