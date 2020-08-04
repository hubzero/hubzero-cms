<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests;

use Hubzero\Test\Basic;
use Hubzero\Config\Registry;
use Hubzero\Config\Processor;
use stdClass;

/**
 * Registry tests
 */
class RegistryTest extends Basic
{
	/**
	 * Tests set() and get()
	 *
	 * @covers  \Hubzero\Config\Registry::set
	 * @covers  \Hubzero\Config\Registry::get
	 * @covers  \Hubzero\Config\Registry::offsetSet
	 * @covers  \Hubzero\Config\Registry::offsetGet
	 * @return  void
	 **/
	public function testSetAndGet()
	{
		$data = new Registry();

		// Test that default value is returned
		$this->assertEquals($data->get('foo'), null);
		$this->assertEquals($data->get('foo', 'one'), 'one');
		$this->assertEquals($data->get('lorem.ipsum.dolor', 'baz'), 'baz');

		// Test correct value is returned
		$data->set('foo', 'bar');

		$this->assertEquals($data->get('foo'), 'bar');

		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$this->assertEquals($data->get('lorem.ipsum'), 'sham');

		$data['foo'] = 'lorem';

		$this->assertEquals($data->get('', 'lorem'), 'lorem');
		$this->assertEquals($data->get('foo'), 'lorem');
		$this->assertEquals($data['foo'], 'lorem');
		$this->assertEquals($data->get('fake.path', 'lorem'), 'lorem');

		$data['lorem.ipsum'] = 'ipsum';

		$this->assertEquals($data->get('lorem.ipsum'), 'ipsum');
		$this->assertEquals($data['lorem.ipsum'], 'ipsum');
		$this->assertEquals($data->get('lorem.dolor', 'mit'), 'mit');

		$data['lorem'] = array('ipsum' => 'dolor');

		$this->assertEquals($data->get('lorem.ipsum'), 'dolor');

		$data->set('lorem.ipsum', array('dolor' => 'mit'));

		$this->assertEquals($data->get('lorem.ipsum.dolor'), 'mit');

		$data->set('lorem', array('ipsum' => 'dolor'));
		$data->set('lorem.dolor.foo', 'bar');

		$this->assertEquals($data->get('lorem.dolor.foo'), 'bar');

		$data = new Registry();
		$data->set('dinosaur', new stdClass);
		$data->set('dinosaur.therapod.tyrannosaurid', 'rex');
		$data->set('dinosaur.therapod.raptor', '');

		$this->assertInstanceOf('stdClass', $data->get('dinosaur.therapod'));
		$this->assertEquals($data->get('dinosaur.therapod.tyrannosaurid'), 'rex');
		$this->assertEquals($data->get('dinosaur.therapod.raptor', 'velociraptor'), 'velociraptor');
	}

	/**
	 * Tests the has() method
	 *
	 * @covers  \Hubzero\Config\Registry::has
	 * @covers  \Hubzero\Config\Registry::offsetExists
	 * @return  void
	 **/
	public function testHas()
	{
		$data = new Registry();

		$data->set('foo', 'bar');

		$this->assertTrue($data->has('foo'));
		$this->assertFalse($data->has('bar'));

		$this->assertTrue(isset($data['foo']));
		$this->assertFalse(isset($data['bar']));
	}

	/**
	 * Tests the def() method
	 *
	 * @covers  \Hubzero\Config\Registry::def
	 * @return  void
	 **/
	public function testDef()
	{
		$data = new Registry();

		$data->def('foo', 'bar');

		$this->assertEquals($data->get('foo'), 'bar');

		$data->set('bar', 'foo');
		$data->def('bar', 'oop');

		$this->assertEquals($data->get('bar'), 'foo');
	}

	/**
	 * Tests the reset() method
	 *
	 * @covers  \Hubzero\Config\Registry::reset
	 * @return  void
	 **/
	public function testReset()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');

		$data->reset();

		$this->assertFalse($data->has('foo'));
		$this->assertFalse($data->has('bar'));
	}

	/**
	 * Tests the offsetUnset() method
	 *
	 * @covers  \Hubzero\Config\Registry::offsetUnset
	 * @return  void
	 **/
	public function testOffsetUnset()
	{
		$data = new Registry();

		$data->set('foo', 'bar');

		unset($data['foo']);

		$this->assertFalse($data->has('foo'));
	}

	/**
	 * Tests the count() method
	 *
	 * @covers  \Hubzero\Config\Registry::count
	 * @return  void
	 **/
	public function testCount()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');

		$this->assertEquals($data->count(), 2);

		$data->set('lorem', 'ipsum');

		$this->assertEquals($data->count(), 3);

		$data->reset();

		$this->assertEquals($data->count(), 0);
	}

	/**
	 * Tests the toString() method
	 *
	 * @covers  \Hubzero\Config\Registry::toString
	 * @covers  \Hubzero\Config\Registry::__toString
	 * @return  void
	 **/
	public function testToString()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');
		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$str = $data->toString();

		$this->assertEquals($str, '{"foo":"bar","bar":"foo","lorem":{"ipsum":"sham"}}');

		$str = (string)$data;

		$this->assertEquals($str, '{"foo":"bar","bar":"foo","lorem":{"ipsum":"sham"}}');
	}

	/**
	 * Tests the toObject() method
	 *
	 * @covers  \Hubzero\Config\Registry::toObject
	 * @return  void
	 **/
	public function testToObject()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');
		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$obj = $data->toObject();

		$this->assertInstanceOf('stdClass', $obj);
		$this->assertTrue(isset($obj->bar));
		$this->assertEquals($obj->foo, 'bar');
		$this->assertTrue(isset($obj->lorem->ipsum));
	}

	/**
	 * Tests the toArray() method
	 *
	 * @covers  \Hubzero\Config\Registry::toArray
	 * @covers  \Hubzero\Config\Registry::asArray
	 * @return  void
	 **/
	public function testToArray()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');
		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$arr = $data->toArray();

		$this->assertTrue(is_array($arr));
		$this->assertTrue(isset($arr['bar']));
		$this->assertTrue(isset($arr['lorem']['ipsum']));
		$this->assertEquals($arr['lorem']['ipsum'], 'sham');
	}

	/**
	 * Tests the flatten() method
	 *
	 * @covers  \Hubzero\Config\Registry::flatten
	 * @covers  \Hubzero\Config\Registry::toFlatten
	 * @return  void
	 **/
	public function testFlatten()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');
		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$arr = $data->flatten();

		$this->assertTrue(is_array($arr));
		$this->assertTrue(isset($arr['bar']));
		$this->assertTrue(isset($arr['lorem.ipsum']));
		$this->assertEquals($arr['lorem.ipsum'], 'sham');
	}

	/**
	 * Tests the jsonSerialize() method
	 *
	 * @covers  \Hubzero\Config\Registry::jsonSerialize
	 * @return  void
	 **/
	public function testJsonSerialize()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');
		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$result = $data->jsonSerialize();

		$this->assertInstanceOf('stdClass', $result);

		$result = json_encode($data);

		$this->assertEquals($result, '{"foo":"bar","bar":"foo","lorem":{"ipsum":"sham"}}');
	}

	/**
	 * Tests the getIterator() method
	 *
	 * @covers  \Hubzero\Config\Registry::getIterator
	 * @return  void
	 **/
	public function testGetIterator()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');

		$result = $data->getIterator();

		$this->assertInstanceOf('ArrayIterator', $result);
	}

	/**
	 * Tests the processors() method
	 *
	 * @covers  \Hubzero\Config\Registry::processors
	 * @return  void
	 **/
	public function testProcessors()
	{
		$data = new Registry();

		$results = $data->processors();

		$this->assertTrue(is_array($results));
		$this->assertTrue(count($results) > 0);

		foreach ($results as $result)
		{
			$this->assertInstanceOf(Processor::class, $result);
		}
	}

	/**
	 * Tests the processor() method
	 *
	 * @covers  \Hubzero\Config\Registry::processor
	 * @return  void
	 **/
	public function testProcessor()
	{
		$data = new Registry();

		foreach (array('ini', 'yaml', 'json', 'php', 'xml') as $type)
		{
			$result = $data->processor($type);

			$this->assertInstanceOf(Processor::class, $result);

			$supported = $result->getSupportedExtensions();

			$this->assertTrue(in_array($type, $supported));

			$this->assertInstanceOf('\\Hubzero\\Config\\Processor\\' . ucfirst($type), $result);
		}
	}

	/**
	 * Tests the extract() method
	 *
	 * @covers  \Hubzero\Config\Registry::extract
	 * @return  void
	 **/
	public function testExtract()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');
		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$extracted = $data->extract('lorem');

		$this->assertInstanceOf(Registry::class, $extracted);

		$this->assertTrue(isset($extracted['ipsum']));
		$this->assertEquals($extracted['ipsum'], 'sham');

		$extracted = $data->extract('dolor');

		$this->assertEquals($extracted, null);
	}

	/**
	 * Tests the merge() method
	 *
	 * @covers  \Hubzero\Config\Registry::merge
	 * @covers  \Hubzero\Config\Registry::bind
	 * @return  void
	 **/
	public function testMerge()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');
		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$data2 = new Registry();
		$data2->set('bar', 'newfoo');
		$data2->set('lorem', 'dolor');

		$fake = null;
		$result = $data->merge($fake);

		$this->assertFalse($result);

		$result = $data->merge($data2);

		$this->assertTrue($result);
		$this->assertEquals($data->get('bar'), 'newfoo');
		$this->assertEquals($data->get('lorem'), 'dolor');

		$data3 = array(
			'lorem' => array('ipsum' => 'mit'),
			'cullen' => 'didae'
		);

		$result = $data->merge($data3, true);

		$this->assertTrue($result);
		$this->assertEquals($data->get('lorem.ipsum'), 'mit');
		$this->assertTrue($data->has('cullen'));

		// Test that empty values are discarded
		$data = new Registry();
		$data->set('foo', 'bar');
		$data->set('bar', 'foo');

		$data2 = new Registry();
		$data2->set('bar', 'newfoo');
		$data2->set('lorem', '');

		$result = $data->merge($data2);

		$this->assertTrue($result);
		$this->assertEquals($data->get('lorem'), null);
	}

	/**
	 * Tests the parse() method
	 *
	 * @covers  \Hubzero\Config\Registry::__construct
	 * @covers  \Hubzero\Config\Registry::parse
	 * @covers  \Hubzero\Config\Registry::read
	 * @return  void
	 **/
	public function testParse()
	{
		// Parse from a string
		$data = new Registry();

		// `toObject()` returns the `$data` property which is set in the constructor
		$this->assertTrue(is_object($data->toObject()));
		$this->assertInstanceOf('stdClass', $data->toObject());

		$json = '{"one":"bar","bar":"foo","lorem":{"ipsum":"sham"}}';

		$data->set('one', 'blue');
		$data->set('two', 'shoe');

		$data->parse($json);

		$this->assertEquals($data->get('one'), 'bar');
		$this->assertEquals($data->get('lorem.ipsum'), 'sham');

		// Parse from an array
		$data = new Registry();

		$arr = array('one' => 'bar', 'bar' => 'foo', 'lorem' => array('ipsum' => 'sham'));

		$data->set('one', 'blue');
		$data->set('two', 'shoe');

		$data->parse($arr);

		$this->assertEquals($data->get('one'), 'bar');
		$this->assertEquals($data->get('lorem.ipsum'), 'sham');

		// Test parsing from a file
		$data = new Registry();
		$result = $data->parse(__DIR__ . '/Files/test.json');

		$this->assertTrue($result);
		$this->assertEquals($data->get('app.application_env'), 'development');

		// Try parsing from an unsupported format
		$data = new Registry();
		$result = $data->parse(__DIR__ . '/Files/test.md');

		$this->assertFalse($result);

		// Test parsing from constructor
		$arr = array('one' => 'bar', 'bar' => 'foo', 'lorem' => array('ipsum' => 'sham'));

		$data = new Registry($arr);

		$this->assertEquals($data->get('one'), 'bar');
		$this->assertEquals($data->get('lorem.ipsum'), 'sham');

		$json = '{"three":"jelly","four":"jam","hair":{"head":"eyebrows"}}';

		$data = new Registry($json);

		$this->assertEquals($data->get('three'), 'jelly');
		$this->assertEquals($data->get('hair.head'), 'eyebrows');

		// Try parsing from an unsupported format
		$data = new Registry();
		$result = $data->parse(__DIR__ . '/Files/test.md');

		$this->assertFalse($result);

		// Try reading a nonexistant file
		$data = new Registry();

		$this->setExpectedException('Hubzero\\Error\\Exception\\InvalidArgumentException');

		$data->read(__DIR__ . '/Fles/test.md');
	}

	/**
	 * Tests the __clone() method
	 *
	 * @covers  \Hubzero\Config\Registry::__clone
	 * @return  void
	 **/
	public function testClone()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');
		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$expected = $data->toString();

		$evilclone = clone $data;

		$this->assertInstanceOf(Registry::class, $evilclone);

		$this->assertTrue(isset($evilclone['lorem']));
		$this->assertEquals($evilclone->get('lorem.ipsum'), 'sham');

		$this->assertEquals($evilclone->toString(), $expected);
	}
}
