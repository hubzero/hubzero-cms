<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests;

use PHPUnit\Framework\TestCase;
use Hubzero\Config\FileLoader;

/**
 * FileLoader tests
 */
class FileLoaderTest extends TestCase
{
    /**
     * Tests constructor
     *
     * @return  void
     **/
    public function testLoad()
    {
        $expected = array(
            'app' => array(
                'application_env' => 'development',
                'editor' => 'ckeditor',
                'list_limit' => '25',
                'helpurl' => 'English (GB) - HUBzero help',
                'debug' => '1',
                'debug_lang' => '0',
                'sef' => '1',
                'sef_rewrite' => '1',
                'sef_suffix' => '0',
                'sef_groups' => '0',
                'feed_limit' => '10',
                'feed_email' => 'author'
            ),
            'seo' => array(
                'sef' => '1',
                'sef_groups' => '0',
                'sef_rewrite' => '1',
                'sef_suffix' => '0',
                'unicodeslugs' => '0',
                'sitename_pagetitles' => '0'
            )
        );

        $basePath = __DIR__ . '/Files';

        $loader = new FileLoader($basePath, $basePath);

        $this->assertEquals($basePath . DIRECTORY_SEPARATOR . 'config', $loader->getConfigPath());

        $data = $loader->load();

        $this->assertEquals($expected, $data);

        $expected['app']['application_env'] = 'production';
        $expected['app']['editor'] = 'none';
        $expected['app']['debug'] = '0';
        $expected['session'] = array(
            'cookie_domain' => '',
            'cookie_path' => '',
            'cookiesubdomains' => '0',
            'lifetime' => '45',
            'session_handler' => 'database'
        );

        $data = $loader->load('api');

        $this->assertEquals($expected, $data);

        // Test with multiple paths (not supported in new API, kept as single path)
        $loader = new FileLoader($basePath, $basePath);

        $data = $loader->load('api');

        $this->assertEquals($expected, $data);

        // Test with a bad path
        $expected = array();
        $badPath = __DIR__ . '/Foo';

        $loader = new FileLoader($badPath, $badPath);

        $data = $loader->load();

        $this->assertEquals($expected, $data);

        // Test loading from a minimal bootstrap path (direct file loading not supported in simplified API)
        // Skipped: The new API always derives config path from appPath
    }

    /**
     * Tests that load() handling of unknown extensions
     *
     * @return  void
     **/
    public function testLoadSkipsUnknownExtensions()
    {
        $basePath = __DIR__ . '/Files';
        $configFile = $basePath . '/config/unknown.bak';

        // Create a file with an unknown extension
        file_put_contents($configFile, '<?php return [];');

        try {
            $loader = new FileLoader($basePath, $basePath);
            $data = $loader->load();

            // It should not be empty (should load other files)
            $this->assertNotEmpty($data);
            // It should not have 'unknown' key (since .bak is skipped)
            $this->assertArrayNotHasKey('unknown', $data);
        } finally {
            // Cleanup
            if (file_exists($configFile)) {
                unlink($configFile);
            }
        }
    }
}
