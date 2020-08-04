<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Test\Basic;
use Hubzero\Base\Obj;

/**
 * Obj test
 */
class ObjTest extends Basic
{
	/**
	 * Sample data
	 *
	 * @var  array
	 */
	protected $data = array(
		'one'   => 'for the money',
		'two'   => 'for the show',
		'three' => 'to get ready',
		'four'  => 'to go'
	);

	/**
	 * Test __construct
	 *
	 * @covers  \Hubzero\Base\Obj::__construct
	 * @return  void
	 **/
	public function testConstructor()
	{
		$obj = new Obj($this->data);

		foreach ($this->data as $key => $datum)
		{
			$this->assertTrue(isset($obj->$key));
			$this->assertEquals($obj->$key, $datum);
		}

		$obj2 = new Obj($obj);

		foreach ($this->data as $key => $datum)
		{
			$this->assertTrue(isset($obj2->$key));
			$this->assertEquals($obj2->$key, $datum);
		}
	}

	/**
	 * Test __toString
	 *
	 * @covers  \Hubzero\Base\Obj::__toString
	 * @return  void
	 **/
	public function testToString()
	{
		$obj = new Obj($this->data);

		$result = (string)$obj;

		$this->assertEquals($result, 'Hubzero\Base\Obj');
	}

	/**
	 * Test setProperties
	 *
	 * @covers  \Hubzero\Base\Obj::setProperties
	 * @return  void
	 **/
	public function testSetProperties()
	{
		$obj = new Obj();

		$this->assertFalse($obj->setProperties('foo'));
		$this->assertTrue($obj->setProperties($this->data));

		foreach ($this->data as $key => $datum)
		{
			$this->assertTrue(isset($obj->$key));
			$this->assertEquals($obj->$key, $datum);
		}

		$obj = new Obj();

		$data = new \stdClass;
		$data->one   = 'for the money';
		$data->two   = 'for the show';
		$data->three = 'to get ready';
		$data->four  = 'to go';

		$this->assertTrue($obj->setProperties($data));

		foreach (get_object_vars($data) as $key => $datum)
		{
			$this->assertTrue(isset($obj->$key));
			$this->assertEquals($obj->$key, $datum);
		}
	}

	/**
	 * Test getProperties
	 *
	 * @covers  \Hubzero\Base\Obj::getProperties
	 * @return  void
	 **/
	public function testGetProperties()
	{
		$data = $this->data;
		$data['_private'] = 'Private property';

		$obj = new Obj($data);

		$prop = $obj->getProperties();

		$this->assertTrue(is_array($prop));
		$this->assertCount(4, $prop);

		foreach ($prop as $key => $val)
		{
			$this->assertEquals($this->data[$key], $val);
		}
	}

	/**
	 * Test setting a property
	 *
	 * @covers  \Hubzero\Base\Obj::set
	 * @return  void
	 **/
	public function testSet()
	{
		$obj = new Obj();

		$this->assertInstanceOf('Hubzero\Base\Obj', $obj->set('foo', 'bar'));
		$this->assertTrue(isset($obj->foo));
		$this->assertEquals($obj->foo, 'bar');
	}

	/**
	 * Test retrieving a set property and
	 * retriving a default value if a property isn't set
	 *
	 * @covers  \Hubzero\Base\Obj::get
	 * @return  void
	 **/
	public function testGet()
	{
		$obj = new Obj();
		$obj->set('foo', 'bar');

		$this->assertEquals($obj->get('foo'), 'bar');
		$this->assertEquals($obj->get('bar', 'default'), 'default');
	}

	/**
	 * Test setting a default value if not alreay assigned
	 *
	 * @covers  \Hubzero\Base\Obj::def
	 * @return  void
	 **/
	public function testDef()
	{
		$obj = new Obj();

		$obj->def('bar', 'ipsum');

		$this->assertEquals($obj->get('bar'), 'ipsum');

		$obj->set('foo', 'bar');
		$obj->def('foo', 'lorem');

		$this->assertEquals($obj->get('foo'), 'bar');
	}
}
