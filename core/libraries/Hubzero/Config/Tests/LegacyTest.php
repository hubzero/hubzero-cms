<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests;

use PHPUnit\Framework\TestCase;
use Hubzero\Config\Legacy;

/**
 * Legacy tests
 */
class LegacyTest extends TestCase
{
    /**
     * Tests reading an invalid file path
     *
     * @return  void
     **/
    public function testReadErrorsWithInvalidFilePath()
    {
        $path = __DIR__ . '/Files';

        $loader = new Legacy($path);
        $this->expectException(\Hubzero\Config\Exception\FileNotFoundException::class);

        $loader->read($path . '/configuration.php');
    }

    /**
     * Tests reading an invalid file
     *
     * @return  void
     **/
    public function testReadErrorsWithInvalidFile()
    {
        $path = __DIR__ . '/Files';

        $loader = new Legacy($path);

        $this->expectException(\Hubzero\Config\Exception\UnsupportedFormatException::class);
        $loader->read($path . '/Legacy/Invalid/configuration.php');
    }

    /**
     * Tests constructor
     *
     * @return  void
     **/
    public function testExists()
    {
        $path = __DIR__ . '/Files/Legacy';

        $loader = new Legacy($path);

        $this->assertTrue($loader->exists());

        $path = __DIR__ . '/Files/Repository';

        $loader = new Legacy($path);

        $this->assertFalse($loader->exists());
    }

    /**
     * Tests reading a config file
     *
     * @return  void
     **/
    public function testRead()
    {
        $path = __DIR__ . '/Files';

        $loader = new Legacy($path);

        $file = $loader->read($path . '/Legacy/configuration.php');

        $this->assertInstanceOf('JConfig', $file);

        // Verify paths are present and valid strings
        $this->assertIsString($file->tmp_path);
        $this->assertIsString($file->log_path);
        $this->assertNotEmpty($file->tmp_path);
        $this->assertNotEmpty($file->log_path);
    }

    /**
     * Tests that read() rewrites paths if rootPath and appPath are provided
     *
     * @return  void
     **/
    public function testReadRewritesPaths()
    {
        $path = __DIR__ . '/Files';
        // These match the paths in Files/Legacy/configuration.php
        $rootPath = '/var/www/hub';
        $appPath = '/custom/app/path';

        $loader = new Legacy($path, $rootPath, $appPath);

        $config = $loader->read($path . '/Legacy/configuration.php');

        $this->assertEquals('/custom/app/path/logs', $config->log_path);
        $this->assertEquals('/custom/app/path/tmp', $config->tmp_path);
    }
}
