<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests;

use Hubzero\Test\Basic;
use Hubzero\Config\Legacy;

/**
 * Legacy tests
 */
class LegacyTest extends Basic
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
}
