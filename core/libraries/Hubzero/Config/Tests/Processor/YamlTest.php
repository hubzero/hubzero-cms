<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests\Processor;

use Hubzero\Test\Basic;
use Hubzero\Config\Processor\Yaml;
use stdClass;

/**
 * Yaml Processor tests
 */
class YamlTest extends Basic
{
	/**
	 * Format processor
	 *
	 * @var  object
	 */
	private $processor = null;

	/**
	 * Expected datain object form
	 *
	 * @var  object
	 */
	private $obj = null;

	/**
	 * Expected data as a string
	 *
	 * @var  string
	 */
	private $str = "app:
    application_env: development
    editor: ckeditor
    list_limit: 25
    helpurl: 'English (GB) - HUBzero help'
    debug: 1
    debug_lang: 0
    sef: 1
    sef_rewrite: 1
    sef_suffix: 0
    sef_groups: 0
    feed_limit: 10
    feed_email: author
seo:
    sef: 1
    sef_groups: 0
    sef_rewrite: 1
    sef_suffix: 0
    unicodeslugs: 0
    sitename_pagetitles: 0
";

	/**
	 * Test setup
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		$data = new stdClass();

		$data->app = new stdClass();
		$data->app->application_env = "development";
		$data->app->editor = "ckeditor";
		$data->app->list_limit = 25;
		$data->app->helpurl = "English (GB) - HUBzero help";
		$data->app->debug = 1;
		$data->app->debug_lang = 0;
		$data->app->sef = 1;
		$data->app->sef_rewrite = 1;
		$data->app->sef_suffix = 0;
		$data->app->sef_groups = 0;
		$data->app->feed_limit = 10;
		$data->app->feed_email = "author";

		$data->seo = new stdClass();
		$data->seo->sef = 1;
		$data->seo->sef_groups = 0;
		$data->seo->sef_rewrite = 1;
		$data->seo->sef_suffix = 0;
		$data->seo->unicodeslugs = 0;
		$data->seo->sitename_pagetitles = 0;

		$this->obj = $data;
		$this->arr = array(
			'app' => (array)$data->app,
			'seo' => (array)$data->seo
		);

		$this->processor = new Yaml();

		parent::setUp();
	}

	/**
	 * Tests the getSupportedExtensions() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Yaml::getSupportedExtensions
	 * @return  void
	 **/
	public function testGetSupportedExtensions()
	{
		$extensions = $this->processor->getSupportedExtensions();

		$this->assertTrue(is_array($extensions));
		$this->assertCount(2, $extensions);
		$this->assertTrue(in_array('yml', $extensions));
		$this->assertTrue(in_array('yaml', $extensions));
	}

	/**
	 * Tests the canParse() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Yaml::canParse
	 * @return  void
	 **/
	public function testCanParse()
	{
		$this->assertFalse($this->processor->canParse("foo:\n	bar"));
		$this->assertTrue($this->processor->canParse($this->str));
	}

	/**
	 * Tests the parse() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Yaml::parse
	 * @return  void
	 **/
	public function testParse()
	{
		$result = $this->processor->parse(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Files' . DIRECTORY_SEPARATOR . 'test.yaml');

		$this->assertEquals($this->arr, $result);

		$this->setExpectedException('Hubzero\Config\Exception\ParseException');

		$result = $this->processor->parse(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Files' . DIRECTORY_SEPARATOR . 'test.xml');
	}

	/**
	 * Tests the objectToString() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Yaml::objectToString
	 * @covers  \Hubzero\Config\Processor\Yaml::asArray
	 * @return  void
	 **/
	public function testObjectToString()
	{
		// Test that a string is returned as-is
		$result = $this->processor->objectToString($this->str);

		$this->assertEquals($this->str, $result);

		// Test object to string conversion
		$result = $this->processor->objectToString($this->obj);

		$this->assertEquals($this->str, $result);
	}

	/**
	 * Tests the stringToObject() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Yaml::stringToObject
	 * @covers  \Hubzero\Config\Processor\Yaml::toObject
	 * @return  void
	 **/
	public function testStringToObject()
	{
		// Test that an object is returned as-is
		$result = $this->processor->stringToObject($this->obj);

		$this->assertEquals($this->obj, $result);

		// Test that a string gets converted as expected
		$result = $this->processor->stringToObject($this->str);

		$this->assertEquals($this->obj, $result);

		// Test that an unparsable string throws an exception
		$this->setExpectedException('Hubzero\Config\Exception\ParseException');

		$result = $this->processor->stringToObject("foo:\n	bar");
	}
}
