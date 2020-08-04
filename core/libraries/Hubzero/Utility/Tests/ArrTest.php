<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Test\Basic;
use Hubzero\Utility\Arr;
use stdClass;

/**
 * Arr utility test
 */
class ArrTest extends Basic
{
	/**
	 * Tests converting values to integers
	 *
	 * @covers  \Hubzero\Utility\Arr::toInteger
	 * @return  void
	 **/
	public function testToInteger()
	{
		$data = array(
			'1',
			'322',
			55,
			false,
			'foo'
		);

		Arr::toInteger($data);

		$this->assertTrue(is_array($data), 'Value returned was not an array');

		foreach ($data as $val)
		{
			$this->assertTrue(is_int($val), 'Value returned was not an integer');
		}

		$data = new stdClass;
		$data->one = '1';
		$data->two = false;
		$data->three = 55;

		Arr::toInteger($data);

		$this->assertTrue(is_array($data), 'Value returned was not an array');
		$this->assertTrue(empty($data), 'Value returned was not an empty array');

		$dflt = array(
			'1',
			'322',
			55,
			false,
			'foo'
		);

		$data = new stdClass;
		$data->one = '1';
		$data->two = false;
		$data->three = 55;

		Arr::toInteger($data, $dflt);

		$this->assertTrue(is_array($data), 'Value returned was not an array');
		$this->assertFalse(empty($data), 'Value returned was an empty array');

		foreach ($data as $key => $val)
		{
			$this->assertTrue(is_int($val), 'Value returned was not an integer');
			$this->assertEquals($val, (int)$dflt[$key]);
		}

		// In this case, the return value should be `array($dflt2)`
		$data2 = new stdClass;
		$data2->one = '1';

		$dflt2 = 555;

		Arr::toInteger($data2, $dflt2);

		$this->assertTrue(is_array($data2), 'Value returned was not an array');
		$this->assertFalse(empty($data2), 'Value returned was an empty array');

		foreach ($data2 as $key => $val)
		{
			$this->assertTrue(is_int($val), 'Value returned was not an integer');
			$this->assertEquals($val, (int)$dflt2);
		}
	}

	/**
	 * Tests converting values to objects
	 *
	 * @covers  \Hubzero\Utility\Arr::toObject
	 * @return  void
	 **/
	public function testToObject()
	{
		$data1 = array(
			'one' => '1',
			'two' => '322',
			'three' => 55,
			'four' => array(
				'a' => 'foo',
				'b' => 'bar'
			)
		);

		$data2 = new stdClass;
		$data2->foo = 'one';
		$data2->bar = 'two';
		$data2->lor = array(
			'ipsum',
			'lorem'
		);

		$datas = array();
		$datas[] = $data1;
		$datas[] = $data2;

		foreach ($datas as $data)
		{
			$result = Arr::toObject($data);

			$this->assertTrue(is_object($result), 'Value returned was not an object');

			foreach ((array)$data as $key => $val)
			{
				$this->assertTrue(isset($result->$key), 'Property "' . $key . '" not set on returned object');
				if (!is_array($val))
				{
					$this->assertEquals($result->$key, $val);
				}
				else
				{
					$this->assertTrue(is_object($result->$key), 'Value returned was not an object');

					foreach ($val as $k => $v)
					{
						$this->assertTrue(isset($result->$key->$k), 'Property not set on returned object');
						$this->assertEquals($result->$key->$k, $v);
					}
				}
			}
		}
	}

	/**
	 * Tests converting values from objects
	 *
	 * @covers  \Hubzero\Utility\Arr::fromObject
	 * @return  void
	 **/
	public function testFromObject()
	{
		$data1 = array(
			'one' => '1',
			'two' => '322',
			'three' => 55,
			'four' => array(
				'a' => 'foo',
				'b' => 'bar'
			)
		);

		$data2 = new stdClass;
		$data2->foo = 'one';
		$data2->bar = 'two';
		$data2->lor = array(
			'ipsum',
			'lorem'
		);
		$data2->ipsum = new stdClass;
		$data2->ipsum->dolor = 'mit';
		$data2->ipsum->carac = 'kol';

		$datas = array();
		$datas[] = $data1;
		$datas[] = $data2;

		foreach ($datas as $data)
		{
			$result = Arr::fromObject($data);

			$this->assertTrue(is_array($result), 'Value returned was not an array');

			foreach ((array)$data as $key => $val)
			{
				$this->assertTrue(isset($result[$key]), 'Array value not set from object property');
				if (!is_array($val) && !is_object($val))
				{
					$this->assertEquals($result[$key], $val);
				}
				else
				{
					$this->assertTrue(isset($result[$key]), 'Array value not set from object property');

					foreach ((array)$val as $k => $v)
					{
						$this->assertTrue(isset($result[$key][$k]), 'Property not set on returned object');
						$this->assertEquals($result[$key][$k], $v);
					}
				}
			}
		}

		$result = Arr::fromObject($data2, false);

		$this->assertTrue(is_array($result), 'Value returned was not an array');
		foreach ((array)$data2 as $key => $val)
		{
			$this->assertTrue(isset($result[$key]), 'Array value not set from object property');
			$this->assertEquals($result[$key], $val);
		}
	}

	/**
	 * Tests determining if array is associative array
	 *
	 * @covers  \Hubzero\Utility\Arr::isAssociative
	 * @return  void
	 **/
	public function testIsAssociative()
	{
		$data = array(
			'one' => '1',
			'two' => '322',
			'three' => 55
		);

		$this->assertTrue(Arr::isAssociative($data), 'Value is an associative array');

		$data = array(
			133,
			675,
			744
		);

		$this->assertFalse(Arr::isAssociative($data), 'Value is not an associative array');

		$data = new stdClass;
		$data->one = 'foo';
		$data->two = 'bar';

		$this->assertFalse(Arr::isAssociative($data), 'Value is not an associative array');
	}

	/**
	 * Tests mapping an array to a string
	 *
	 * @covers  \Hubzero\Utility\Arr::toString
	 * @return  void
	 **/
	public function testToString()
	{
		$data = array(
			'one' => '1',
			'two' => '322',
			'three' => 55,
			'four' => array(
				'a' => 'foo',
				'b' => 'bar'
			)
		);

		$result = Arr::toString($data, '=', '&');

		$this->assertTrue(is_string($result), 'Value is not a string');
		$this->assertEquals($result, 'one="1"&two="322"&three="55"&a="foo"&b="bar"');

		$result = Arr::toString($data, '=', '&', true);
		$this->assertEquals($result, 'one="1"&two="322"&three="55"&four&a="foo"&b="bar"');
	}

	/**
	 * Tests returning a value from a named array
	 *
	 * @covers  \Hubzero\Utility\Arr::getValue
	 * @return  void
	 **/
	public function testGetValue()
	{
		$data = array(
			'one' => '1',
			'two' => '322',
			'three' => 55,
			'four' => array(
				'a' => 'foo',
				'b' => 'bar'
			),
			'six' => '!! good123',
			'seven' => '5.5'
		);

		$result = Arr::getValue($data, 'one');

		$this->assertEquals($result, '1');

		$result = Arr::getValue($data, 'one', null, 'integer');

		$this->assertTrue(is_int($result), 'Value is not an integer');
		$this->assertEquals($result, 1);

		$result = Arr::getValue($data, 'three', null, 'string');

		$this->assertTrue(is_string($result), 'Value is not a string');
		$this->assertEquals($result, '55');

		$result = Arr::getValue($data, 'two', null, 'array');

		$this->assertTrue(is_array($result), 'Value is not an array');
		$this->assertEquals($result, array('322'));

		$result = Arr::getValue($data, 'four', null, 'array');

		$this->assertTrue(is_array($result), 'Value is not an array');
		$this->assertEquals($result, array(
			'a' => 'foo',
			'b' => 'bar'
		));

		$result = Arr::getValue($data, 'five');

		$this->assertTrue(is_null($result), 'Value is not null');

		$result = Arr::getValue($data, 'five', 'glorp');

		$this->assertEquals($result, 'glorp');

		$result = Arr::getValue($data, 'one', null, 'bool');

		$this->assertTrue($result);

		$result = Arr::getValue($data, 'six', null, 'word');

		$this->assertEquals($result, 'good123');

		$result = Arr::getValue($data, 'seven', null, 'float');

		$this->assertTrue(is_float($result), 'Value is not a float');
		$this->assertEquals($result, 5.5);
	}

	/**
	 * Tests extracting a column from an array
	 *
	 * @covers  \Hubzero\Utility\Arr::getColumn
	 * @return  void
	 **/
	public function testGetColumn()
	{
		$arrs = array(
			array(
				'id' => 1,
				'name' => 'Joe',
				'age' => 27
			),
			array(
				'id' => 2,
				'name' => 'Susan',
				'age' => 24
			),
		);

		$item = new stdClass;
		$item->id = 3;
		$item->name = 'Frank';
		$item->age = 56;

		$arrs[] = $item;

		$item = new stdClass;
		$item->id = 4;
		$item->name = 'Helen';
		$item->age = 32;

		$arrs[] = $item;

		$result = Arr::getColumn($arrs, 'id');

		$this->assertEquals($result, array(1, 2, 3, 4));

		$result = Arr::getColumn($arrs, 'name');

		$this->assertEquals($result, array('Joe', 'Susan', 'Frank', 'Helen'));
	}

	/**
	 * Tests that #filterKeys filters correctly
	 *
	 * @covers  \Hubzero\Utility\Arr::filterKeys
	 * @return  void
	 **/
	public function testFilterKeysFilters()
	{
		$unfiltered = ['a' => 0, 'b' => 1, 'c' => 2];
		$whitelist = ['b'];

		$filtered = Arr::filterKeys($unfiltered, $whitelist);

		$this->assertEquals(array_keys($filtered), $whitelist);
	}

	/**
	 * Tests that #filterKeys does not change argument array
	 *
	 * @covers  \Hubzero\Utility\Arr::filterKeys
	 * @return  void
	 **/
	public function testFilterKeysNonDestructive()
	{
		$original = ['a' => 0, 'b' => 1, 'c' => 2];
		$copy = $original;

		Arr::filterKeys($original, []);

		$this->assertEquals($original, $copy);
	}

	/**
	 * Tests that #pluck unsets the given key
	 *
	 * @covers  \Hubzero\Utility\Arr::pluck
	 * @return  void
	 **/
	public function testPluckRemovesKey()
	{
		$array = ['a' => 0, 'b' => 1, 'c' => 2];
		$key = 'c';

		Arr::pluck($array, $key);

		$this->assertFalse(array_key_exists($key, $array));
	}

	/**
	 * Tests that #pluck returns value under the given key
	 *
	 * @covers  \Hubzero\Utility\Arr::pluck
	 * @return  void
	 **/
	public function testPluckReturnsValue()
	{
		$array = ['a' => 0, 'b' => 1, 'c' => 2];
		$key = 'c';
		$value = $array[$key];

		$pluckValue = Arr::pluck($array, $key);

		$this->assertEquals($value, $pluckValue);
	}

	/**
	 * Tests that #pluck returns default when key is missing
	 *
	 * @covers  \Hubzero\Utility\Arr::pluck
	 * @return  void
	 **/
	public function testPluckReturnsDefaultIfKeyMissing()
	{
		$array = ['a' => 0, 'b' => 1, 'c' => 2];
		$default = 'test';
		$key = 'd';

		$pluckValue = Arr::pluck($array, $key, $default);

		$this->assertEquals($default, $pluckValue);
	}

	/**
	 * Tests that #pluck returns default when value is null
	 *
	 * @covers  \Hubzero\Utility\Arr::pluck
	 * @return  void
	 **/
	public function testPluckReturnsDefaultIfValueNull()
	{
		$array = ['a' => 0, 'b' => 1, 'c' => null];
		$default = 'test';
		$key = 'c';

		$pluckValue = Arr::pluck($array, $key, $default);

		$this->assertEquals($default, $pluckValue);
	}

	/**
	 * Tests multidimensional array unique
	 *
	 * @covers  \Hubzero\Utility\Arr::arrayUnique
	 * @return  void
	 **/
	public function testArrayUnique()
	{
		$arrs = array(
			array(
				'id' => 1,
				'name' => 'Joe',
				'age' => 27
			),
			array(
				'id' => 2,
				'name' => 'Susan',
				'age' => 24
			),
			array(
				'id' => 1,
				'name' => 'Joe',
				'age' => 27
			),
		);

		$foo = 'bar';

		$result = Arr::arrayUnique($foo);

		$this->assertEquals($result, $foo);

		$result = Arr::arrayUnique($arrs);

		$this->assertNotEquals($result, $arrs);
		$this->assertCount(2, $result);
	}

	/**
	 * Tests pivot method
	 *
	 * @covers  \Hubzero\Utility\Arr::pivot
	 * @return  void
	 **/
	public function testPivot()
	{
		$arrs = array(
			array(
				'id' => 1,
				'name' => 'Joe',
				'age' => 27
			),
			'name' => 'Mark'
		);

		$item = new stdClass;
		$item->id = 4;
		$item->name = 'Jane';
		$item->age = 20;

		$arrs[] = $item;

		$result = Arr::pivot($arrs, 'name');

		$expected = array(
			'Joe' => array(
				'id' => 1,
				'name' => 'Joe',
				'age' => 27
			),
			'Mark' => 'name',
			'Jane' => $item
		);

		$this->assertEquals($result, $expected);
	}

	/**
	 * Tests sortObjects method
	 *
	 * @covers  \Hubzero\Utility\Arr::sortObjects
	 * @covers  \Hubzero\Utility\Arr::_sortObjects
	 * @return  void
	 **/
	public function testSortObjects()
	{
		$arr = array();

		$item1 = new stdClass;
		$item1->id = 1;
		$item1->name = 'Mark';
		$item1->age = 20;

		$item2 = new stdClass;
		$item2->id = 2;
		$item2->name = 'Jane';
		$item2->age = 30;

		$item3 = new stdClass;
		$item3->id = 3;
		$item3->name = 'Bill';
		$item3->age = 25;

		$item4 = new stdClass;
		$item4->id = 4;
		$item4->name = 'dave';
		$item4->age = 55;

		$item5 = new stdClass;
		$item5->id = 5;
		$item5->name = 'David';
		$item5->age = 19;

		$arr[] = $item1;
		$arr[] = $item2;
		$arr[] = $item3;
		$arr[] = $item4;
		$arr[] = $item5;

		$result = Arr::sortObjects($arr, 'name');

		$expected = array(
			$item3,
			$item5,
			$item2,
			$item1,
			$item4
		);

		$this->assertEquals($result, $expected);

		$result = Arr::sortObjects($arr, 'name', -1);

		$expected = array_reverse($expected);

		$this->assertEquals($result, $expected);

		$result = Arr::sortObjects($arr, 'name', 1, false);

		$expected = array(
			$item3,
			$item4,
			$item5,
			$item2,
			$item1
		);

		$this->assertEquals($result, $expected);

		$result = Arr::sortObjects($arr, 'name', -1, false);

		$expected = array_reverse($expected);

		$this->assertEquals($result, $expected);

		$result = Arr::sortObjects($arr, 'age');

		$expected = array(
			$item5,
			$item1,
			$item3,
			$item2,
			$item4
		);

		$this->assertEquals($result, $expected);
	}
}
