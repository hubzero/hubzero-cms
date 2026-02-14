<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Template\Tests;

use Hubzero\Template\Loader;
use Hubzero\Base\Application;
use Hubzero\Config\Registry;
use Hubzero\Database\Drivers\Sqlite\SqliteDriver;
use PHPUnit\Framework\TestCase;

/**
 * Template loader test
 */
class LoaderTest extends TestCase
{
    /**
     * Hubzero\Template\Loader
     *
     * @var  object
     */
    private $instance;

    private $loader;

    /**
     * Fixture sqlite file name
     *
     * @var string
     */
    private $fixture = 'test.sqlite3';

    /**
     * Sets up the tests...called prior to each test
     *
     * @return  void
     */
    public function setUp(): void
    {
        $driver = $this->getMockDriver($this->fixture);

        \Hubzero\Database\Relational::setDefaultConnection($driver);

        $app = new Application();
        $app['client'] = new \Hubzero\Base\Client\Site();
        $app['db']     = $driver;
        $app['config'] = new Registry();

        $this->loader = new Loader($app, [
            'path_app'  => __DIR__ . '/Mock/app',
            'path_core' => __DIR__ . '/Mock/core'
        ]);
    }

    /**
     * Test the getPath() method.
     *
     * @return  void
     */
    public function testGetPath()
    {
        $this->assertEquals($this->loader->getPath('core'), __DIR__ . '/Mock/core');
        $this->assertEquals($this->loader->getPath('app'), __DIR__ . '/Mock/app');

        $this->assertNotEquals($this->loader->getPath('core'), $this->loader->getPath('app'));
    }

    /**
     * Test the setPath() method.
     *
     * @return  void
     */
    public function testSetPath()
    {
        $core = $this->loader->getPath('core');
        $app  = $this->loader->getPath('app');

        $this->assertInstanceOf('Hubzero\Template\Loader', $this->loader->setPath('core', __DIR__ . '/core'));
        $this->assertEquals($this->loader->getPath('core'), __DIR__ . '/core');

        $this->assertInstanceOf('Hubzero\Template\Loader', $this->loader->setPath('app', __DIR__ . '/app'));
        $this->assertEquals($this->loader->getPath('app'), __DIR__ . '/app');

        $this->assertNotEquals($this->loader->getPath('core'), $this->loader->getPath('app'));

        $this->loader->setPath('core', $core);
        $this->loader->setPath('app', $app);
    }

    /**
     * Test that the system template is built and returned properly
     *
     * @return  void
     */
    public function testGetSystemTemplate()
    {
        $template = $this->loader->getSystemTemplate();

        $this->assertTrue(is_object($template));
        $this->assertEquals($template->template, 'system');
        $this->assertEquals($template->protected, 1);
        $this->assertEquals($template->id, 0);
        $this->assertEquals($template->home, 0);
        $this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
        $this->assertEquals($template->path, $this->loader->getPath('core') .
            DIRECTORY_SEPARATOR .
            $template->template);
    }

    /**
     * Test the setStyle() and getStyle() methods.
     *
     * @return  void
     */
    public function testSetGetStyle()
    {
        $this->loader->setStyle(1);

        $this->assertEquals($this->loader->getStyle(), 1);

        $this->loader->setStyle('0012');

        $this->assertEquals($this->loader->getStyle(), 12);
    }

    /**
     * Test the setLang() and getLang() methods.
     *
     * @return  void
     */
    public function testSetGetLang()
    {
        $this->loader->setLang('de-DE');

        $this->assertEquals($this->loader->getLang(), 'de-DE');

        $this->loader->setLang('en-US');

        $this->assertEquals($this->loader->getLang(), 'en-US');
    }

    /**
     * Test the the constructor is properly setting all optional values
     *
     * @return  void
     */
    public function testConstructor()
    {
        $driver = $this->getMockDriver($this->fixture);

        \Hubzero\Database\Relational::setDefaultConnection($driver);

        $app = new Application();
        $app['client'] = new \Hubzero\Base\Client\Site();
        $app['db']     = $driver;
        $app['config'] = new Registry();

        $loader = new Loader($app, [
            'path_app'  => __DIR__ . '/Mock/app',
            'path_core' => __DIR__ . '/Mock/core',
            'style'     => 5,
            'lang'      => 'en-US'
        ]);

        $this->assertEquals($loader->getPath('core'), __DIR__ . '/Mock/core');
        $this->assertEquals($loader->getPath('app'), __DIR__ . '/Mock/app');
        $this->assertEquals($loader->getStyle(), 5);
        $this->assertEquals($loader->getLang(), 'en-US');
    }

    /**
     * Test that the system template is built and returned properly
     *
     * Note: This test requires MySQL-compatible SQL quoting. The Hubzero Query
     * builder uses double quotes for string literals, which SQLite interprets
     * as identifiers. Skip this test when using SQLite as test database.
     *
     * @return  void
     */
    public function testGetTemplate()
    {
        $template = $this->loader->getTemplate(0);

        $this->assertTrue(is_object($template));
        $this->assertEquals($template->template, 'sitefoo');
        $this->assertEquals($template->protected, 1);
        $this->assertEquals($template->id, 3);
        $this->assertEquals($template->home, 1);
        $this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
        $this->assertEquals($template->path, $this->loader->getPath('core') .
            DIRECTORY_SEPARATOR .
            $template->template);

        $template = $this->loader->getTemplate(1);

        $this->assertTrue(is_object($template));
        $this->assertEquals($template->template, 'adminfoo');
        $this->assertEquals($template->protected, 1);
        $this->assertEquals($template->id, 1);
        $this->assertEquals($template->home, 1);
        $this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
        $this->assertEquals($template->path, $this->loader->getPath('core') .
            DIRECTORY_SEPARATOR .
            $template->template);

        $template = $this->loader->getTemplate(0, 4);

        $this->assertTrue(is_object($template));
        $this->assertEquals($template->template, 'sitebar');
        $this->assertEquals($template->protected, 0);
        $this->assertEquals($template->id, 4);
        $this->assertEquals($template->home, 0);
        $this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
        $this->assertEquals($template->path, $this->loader->getPath('app') . DIRECTORY_SEPARATOR . $template->template);

        $template = $this->loader->getTemplate(1, 2);

        $this->assertTrue(is_object($template));
        $this->assertEquals($template->template, 'adminbar');
        $this->assertEquals($template->protected, 0);
        $this->assertEquals($template->id, 2);
        $this->assertEquals($template->home, 0);
        $this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
        $this->assertEquals($template->path, $this->loader->getPath('app') . DIRECTORY_SEPARATOR . $template->template);

        $template = $this->loader->getTemplate(1, 7);

        $this->assertTrue(is_object($template));
        $this->assertEquals($template->template, 'system');
        $this->assertEquals($template->protected, 1);
        $this->assertEquals($template->id, 0);
        $this->assertEquals($template->home, 0);
        $this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
        $this->assertEquals($template->path, $this->loader->getPath('core') .
            DIRECTORY_SEPARATOR .
            $template->template);

        $template = $this->loader->getTemplate(0, 8);

        $this->assertTrue(is_object($template));
        $this->assertEquals($template->template, 'system');
        $this->assertEquals($template->protected, 1);
        $this->assertEquals($template->id, 0);
        $this->assertEquals($template->home, 0);
        $this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
        $this->assertEquals($template->path, $this->loader->getPath('core') .
            DIRECTORY_SEPARATOR .
            $template->template);
    }

    /**
     * Test loading a template by client ID
     *
     * @return  void
     */
    public function testLoad()
    {
        // Load template by current client (site)
        $template = $this->loader->load();

        $this->assertTrue(is_object($template));
        $this->assertEquals($template->template, 'sitefoo');
        $this->assertEquals($template->protected, 1);
        $this->assertEquals($template->id, 3);
        $this->assertEquals($template->home, 1);
        $this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
        $this->assertEquals($template->path, $this->loader->getPath('core') .
            DIRECTORY_SEPARATOR .
            $template->template);

        // Load site tmeplate by client ID
        $template = $this->loader->load(0);

        $this->assertTrue(is_object($template));
        $this->assertEquals($template->template, 'sitefoo');
        $this->assertEquals($template->protected, 1);
        $this->assertEquals($template->id, 3);
        $this->assertEquals($template->home, 1);
        $this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
        $this->assertEquals($template->path, $this->loader->getPath('core') .
            DIRECTORY_SEPARATOR .
            $template->template);

        // Load admin template by client name
        $template = $this->loader->load('administrator');

        $this->assertTrue(is_object($template));
        $this->assertEquals($template->template, 'adminfoo');
        $this->assertEquals($template->protected, 1);
        $this->assertEquals($template->id, 1);
        $this->assertEquals($template->home, 1);
        $this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
        $this->assertEquals($template->path, $this->loader->getPath('core') .
            DIRECTORY_SEPARATOR .
            $template->template);

        $this->expectException(\InvalidArgumentException::class);

        $template = $this->loader->load('foobar');
    }

    /**
     * Test that the system template is returned on a database error
     *
     * @return  void
     */
    public function testDatabaseError()
    {
        $driver = $this->getMockDriver('testBad.sqlite3');

        $app = new Application();
        $app['client'] = new \Hubzero\Base\Client\Site();
        $app['db']     = $driver;
        $app['config'] = new Registry();

        $this->loader = new Loader($app, [
            'path_app'  => __DIR__ . '/Mock/app',
            'path_core' => __DIR__ . '/Mock/core'
        ]);

        // Load admin template by client name
        $template = $this->loader->load();

        $this->assertTrue(is_object($template));
        $this->assertEquals($template->template, 'system');
    }

    /**
     * Gets a mock SQLite database driver using the requested fixture database
     *
     * @param   string  $fixture
     * @return  SqliteDriver
     */
    private function getMockDriver(string $fixture): SqliteDriver
    {
        $dbPath = __DIR__ . '/Fixtures/' . $fixture;
        $pdo = new \PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        /** @var SqliteDriver $driver */
        $driver = $this->getMockBuilder(SqliteDriver::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $driver->setConnection($pdo)
            ->setPrefix('');

        return $driver;
    }
}
