<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests\Processor;

use PHPUnit\Framework\TestCase;
use Hubzero\Config\Processor\Php;
use Hubzero\Config\Exception\ParseException;
use Hubzero\Config\Exception\UnsupportedFormatException;
use stdClass;

/**
 * Php Processor tests
 */
class PhpTest extends TestCase
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
     * Expected data as a string (array format)
     *
     * @var  string
     */
    private $str = null;

    /**
     * Expected data as a string (object format)
     *
     * @var  string
     */
    private $strObject = null;

    /**
     * Test setup
     *
     * @return  void
     */
    protected function setUp(): void
    {
        $data = new stdClass();

        $data->foo = 1;
        $data->bar = null;

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
            //'foo' => '1',
            //'bar' => '',
            'app' => (array) $data->app,
            'seo' => (array) $data->seo
        );

        $this->processor = new Php();

        // Build expected string (array format)
        $this->str = "<?php\nreturn array(\n\t'foo' => '1',\n\t'bar' => '',\n\t'app' => array("
            . "\"application_env\" => \"development\", \"editor\" => \"ckeditor\", "
            . "\"list_limit\" => \"25\", \"helpurl\" => \"English (GB) - HUBzero help\", "
            . "\"debug\" => \"1\", \"debug_lang\" => \"0\", \"sef\" => \"1\", "
            . "\"sef_rewrite\" => \"1\", \"sef_suffix\" => \"0\", \"sef_groups\" => \"0\", "
            . "\"feed_limit\" => \"10\", \"feed_email\" => \"author\"),\n\t'seo' => array("
            . "\"sef\" => \"1\", \"sef_groups\" => \"0\", \"sef_rewrite\" => \"1\", "
            . "\"sef_suffix\" => \"0\", \"unicodeslugs\" => \"0\", "
            . "\"sitename_pagetitles\" => \"0\"),\n);";

        // Build expected string (object format)
        $this->strObject = "<?php\nclass Config\n{\n\tvar \$foo = '1';\n\tvar \$bar = '';"
            . "\n\tvar \$app = array(\"application_env\" => \"development\", "
            . "\"editor\" => \"ckeditor\", \"list_limit\" => \"25\", "
            . "\"helpurl\" => \"English (GB) - HUBzero help\", \"debug\" => \"1\", "
            . "\"debug_lang\" => \"0\", \"sef\" => \"1\", \"sef_rewrite\" => \"1\", "
            . "\"sef_suffix\" => \"0\", \"sef_groups\" => \"0\", \"feed_limit\" => \"10\", "
            . "\"feed_email\" => \"author\");\n\tvar \$seo = array(\"sef\" => \"1\", "
            . "\"sef_groups\" => \"0\", \"sef_rewrite\" => \"1\", \"sef_suffix\" => \"0\", "
            . "\"unicodeslugs\" => \"0\", \"sitename_pagetitles\" => \"0\");\n}";

        parent::setUp();
    }

    /**
     * Tests the getSupportedExtensions() method.
     *
     * @return  void
     **/
    public function testGetSupportedExtensions()
    {
        $extensions = $this->processor->getSupportedExtensions();

        $this->assertTrue(is_array($extensions));
        $this->assertCount(1, $extensions);
        $this->assertTrue(in_array('php', $extensions));
    }

    /**
     * Tests the canParse() method.
     *
     * @return  void
     **/
    public function testCanParse()
    {
        $this->assertFalse($this->processor->canParse($this->str));
    }

    /**
     * Tests the parse() method.
     *
     * @return  void
     **/
    public function testParse()
    {
        $result = $this->processor->parse(dirname(__DIR__) .
            DIRECTORY_SEPARATOR .
            'Files' .
            DIRECTORY_SEPARATOR .
            'test.php');
        $this->assertEquals($this->arr, $result);
    }

    /**
     * Test a PHP file containing a callable
     *
     * @return  void
     **/
    public function testParseCallable()
    {
        $result = $this->processor->parse(dirname(__DIR__) .
            DIRECTORY_SEPARATOR .
            'Files' .
            DIRECTORY_SEPARATOR .
            'testCallable.php');
        $this->assertEquals($this->arr, $result);
    }

    /**
     * Test that an exception is thrown and caught
     *
     * @return  void
     **/
    public function testParseException()
    {
        $this->expectException(ParseException::class);
        $result = $this->processor->parse(dirname(__DIR__) .
            DIRECTORY_SEPARATOR .
            'Files' .
            DIRECTORY_SEPARATOR .
            'testException.php');
    }

    /**
     * Tests the parse() method throws an Exception for a bad PHP file.
     *
     * @return  void
     **/
    public function testParseEmptyFile()
    {
        $this->expectException(UnsupportedFormatException::class);
        $result = $this->processor->parse(dirname(__DIR__) .
            DIRECTORY_SEPARATOR .
            'Files' .
            DIRECTORY_SEPARATOR .
            'testEmpty.php');
    }

    /**
     * Tests the objectToString() method.
     *
     * @return  void
     **/
    public function testObjectToString()
    {
        // Test that a string is returned as-is
        $result = $this->processor->objectToString($this->str);

        $this->assertEquals($this->str, $result);

        // Test object to string conversion
        $result = $this->processor->objectToString($this->obj, array(
            'format' => 'array'
        ));

        $this->assertEquals($this->str, $result);

        // Test object to string conversion
        $result = $this->processor->objectToString($this->obj, array(
            'format' => 'object'
        ));

        $this->assertEquals($this->strObject, $result);
    }

    /**
     * Tests the stringToObject() method.
     *
     * @return  void
     **/
    public function testStringToObject()
    {
        $result = $this->processor->stringToObject($this->str);

        $this->assertTrue($result);
    }
}
