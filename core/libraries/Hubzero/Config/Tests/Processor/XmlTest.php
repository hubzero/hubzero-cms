<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests\Processor;

use Hubzero\Test\Basic;
use Hubzero\Config\Processor\Xml;
use stdClass;

/**
 * Xml Processor tests
 */
class XmlTest extends Basic
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
	 * Expected data as an array
	 *
	 * @var  array
	 */
	private $arr = null;

	/**
	 * Expected data as a string
	 *
	 * @var  string
	 */
	private $str = '<?xml version="1.0"?>
<config>
	<setting name="app" type="object">
		<setting name="application_env" type="string">development</setting>
		<setting name="editor" type="string">ckeditor</setting>
		<setting name="list_limit" type="integer">25</setting>
		<setting name="helpurl" type="string">English (GB) - HUBzero help</setting>
		<setting name="debug" type="integer">1</setting>
		<setting name="debug_lang" type="integer">0</setting>
		<setting name="sef" type="integer">1</setting>
		<setting name="sef_rewrite" type="integer">1</setting>
		<setting name="sef_suffix" type="integer">0</setting>
		<setting name="sef_groups" type="integer">0</setting>
		<setting name="feed_limit" type="integer">10</setting>
		<setting name="feed_email" type="string">author</setting>
		<setting name="ratelimit" type="array">
			<setting name="short" type="double">500.1</setting>
			<setting name="long" type="double">5000.7</setting>
		</setting>
	</setting>
	<setting name="seo" type="object">
		<setting name="sef" type="integer">1</setting>
		<setting name="sef_groups" type="integer">0</setting>
		<setting name="sef_rewrite" type="integer">1</setting>
		<setting name="sef_suffix" type="integer">0</setting>
		<setting name="unicodeslugs" type="integer">0</setting>
		<setting name="sitename_pagetitles" type="integer">0</setting>
	</setting>
</config>';

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
		$data->app->ratelimit = array(
			'short' => 500.1,
			'long' => 5000.7
		);

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

		$this->processor = new Xml();

		parent::setUp();
	}

	/**
	 * Tests the getSupportedExtensions() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Xml::getSupportedExtensions
	 * @return  void
	 **/
	public function testGetSupportedExtensions()
	{
		$extensions = $this->processor->getSupportedExtensions();

		$this->assertTrue(is_array($extensions));
		$this->assertCount(1, $extensions);
		$this->assertTrue(in_array('xml', $extensions));
	}

	/**
	 * Tests the canParse() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Xml::canParse
	 * @return  void
	 **/
	public function testCanParse()
	{
		$this->assertFalse($this->processor->canParse('Cras justo odio, dapibus ac facilisis in, egestas eget quam.'));
		$this->assertFalse($this->processor->canParse('{"application_env":"development","editor":"ckeditor","list_limit":"25"}'));
		$this->assertTrue($this->processor->canParse($this->str));
	}

	/**
	 * Tests the parse() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Xml::parse
	 * @return  void
	 **/
	public function testParse()
	{
		$result = $this->processor->parse(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Files' . DIRECTORY_SEPARATOR . 'test.xml');

		$this->assertEquals($this->arr, $result);

		$this->setExpectedException('Hubzero\Config\Exception\ParseException');

		$result = $this->processor->parse(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Files' . DIRECTORY_SEPARATOR . 'test.ini');
	}

	/**
	 * Tests the objectToString() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Xml::objectToString
	 * @covers  \Hubzero\Config\Processor\Xml::getXmlChildren
	 * @return  void
	 **/
	public function testObjectToString()
	{
		// Test that a string is returned as-is
		$result = $this->processor->objectToString($this->str);

		$this->assertEquals($this->str, $result);

		// Test object to string conversion
		$result = $this->processor->objectToString($this->obj, array(
			'name'     => 'config',
			'nodeName' => 'setting'
		));

		$str = str_replace(array("\n", "\t"), '', $this->str);
		$str = str_replace('<?xml version="1.0"?>', "<?xml version=\"1.0\"?>\n", $str);

		$this->assertEquals($str, trim($result));
	}

	/**
	 * Tests the stringToObject() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Xml::stringToObject
	 * @covers  \Hubzero\Config\Processor\Xml::getValueFromNode
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
	}
}
