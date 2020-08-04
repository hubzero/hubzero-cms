<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests;

use Hubzero\Test\Basic;
use Hubzero\Config\FileLoader;

/**
 * FileLoader tests
 */
class FileLoaderTest extends Basic
{
	/**
	 * Tests constructor
	 *
	 * @covers  \Hubzero\Config\FileLoader::__construct
	 * @covers  \Hubzero\Config\FileLoader::getDefaultPath
	 * @covers  \Hubzero\Config\FileLoader::getPaths
	 * @covers  \Hubzero\Config\FileLoader::getParser
	 * @covers  \Hubzero\Config\FileLoader::load
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

		$path = __DIR__ . '/Files/Repository';

		$loader = new FileLoader($path);

		$this->assertEquals($path, $loader->getDefaultPath());

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

		// Test with multiple paths
		$path = array(
			__DIR__ . '/Files/Repository',
			__DIR__ . '/Files/Repository/api'
		);

		$loader = new FileLoader($path);

		$data = $loader->load();

		$this->assertEquals($expected, $data);

		// Test with a bad path
		$expected = array();
		$path = __DIR__ . '/Foo';

		$loader = new FileLoader($path);

		$data = $loader->load();

		$this->assertEquals($expected, $data);

		// Test loading a specific file
		$loader = new FileLoader(__DIR__ . '/Files/Repository/seo.php');

		$data = $loader->load();

		$expected = array(
			'seo' => array(
				'sef' => '1',
				'sef_groups' => '0',
				'sef_rewrite' => '1',
				'sef_suffix' => '0',
				'unicodeslugs' => '0',
				'sitename_pagetitles' => '0'
			)
		);

		$this->assertEquals($expected, $data);
	}
}
