<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Pathway\Tests;

use Hubzero\Test\Basic;
use Hubzero\Pathway\Trail;
use Hubzero\Pathway\Item;

/**
 * Pathway trail tests
 */
class TrailTest extends Basic
{
	/**
	 * Test ArrayAccess methods
	 *
	 * @covers  \Hubzero\Pathway\Trail::set
	 * @covers  \Hubzero\Pathway\Trail::get
	 * @covers  \Hubzero\Pathway\Trail::has
	 * @covers  \Hubzero\Pathway\Trail::forget
	 * @covers  \Hubzero\Pathway\Trail::offsetSet
	 * @covers  \Hubzero\Pathway\Trail::offsetGet
	 * @covers  \Hubzero\Pathway\Trail::offsetUnset
	 * @covers  \Hubzero\Pathway\Trail::offsetExists
	 * @return  void
	 **/
	public function testArrayAccessMethods()
	{
		$pathway = new Trail();

		$crumb1 = new Item('Crumb 1', 'index.php?option=com_lorem');
		$crumb2 = new Item('Crumb 2', 'index.php?option=com_ipsum');
		$crumb3 = new Item('Crumb 3', 'index.php?option=com_dolor');

		$pathway->set(0, $crumb1);
		$pathway->set(1, $crumb2);
		$pathway->offsetSet(2, $crumb3);

		$this->assertTrue($pathway->has(1));
		$this->assertFalse($pathway->has(3));

		$item = $pathway->get(0);

		$this->assertEquals($crumb1->name, $item->name);
		$this->assertEquals($crumb1->link, $item->link);

		$item = $pathway->get(1);

		$this->assertEquals($crumb2->name, $item->name);
		$this->assertEquals($crumb2->link, $item->link);

		$item = $pathway->offsetGet(2);

		$this->assertEquals($crumb3->name, $item->name);
		$this->assertEquals($crumb3->link, $item->link);

		$pathway->forget(1);

		$this->assertFalse($pathway->has(1));

		$pathway->offsetUnset(2);

		$this->assertFalse($pathway->offsetExists(2));
	}

	/**
	 * Tests:
	 *  1. the append() method is chainable
	 *  2. append() adds to the items list
	 *  3. append() adds an Hubzero\Pathway\Item object to the items list
	 *  4. append() adds to the END of the items list
	 *
	 * @covers  \Hubzero\Pathway\Trail::append
	 * @return  void
	 **/
	public function testAppend()
	{
		$pathway = new Trail();

		$this->assertInstanceOf('Hubzero\Pathway\Trail', $pathway->append('Crumb 1', 'index.php?option=com_lorem'));

		$this->assertCount(1, $pathway->items(), 'List of crumbs should have returned one Item');

		$name = 'Crumb 2';
		$link = 'index.php?option=com_ipsum';

		$pathway->append($name, $link);

		$items = $pathway->items();
		$item = array_pop($items);

		$this->assertInstanceOf('Hubzero\Pathway\Item', $item);
		$this->assertEquals($item->name, $name);
		$this->assertEquals($item->link, $link);
	}

	/**
	 * Tests:
	 *  1. the prepend() method is chainable
	 *  2. prepend() adds to the items list
	 *  3. prepend() adds an Hubzero\Pathway\Item object to the items list
	 *  4. prepend() adds to the BEGINNING of the items list
	 *
	 * @covers  \Hubzero\Pathway\Trail::prepend
	 * @return  void
	 **/
	public function testPrepend()
	{
		$pathway = new Trail();

		$this->assertInstanceOf('Hubzero\Pathway\Trail', $pathway->prepend('Crumb 1', 'index.php?option=com_lorem'));

		$this->assertCount(1, $pathway->items(), 'List of crumbs should have returned one Item');

		$name = 'Crumb 2';
		$link = 'index.php?option=com_ipsum';

		$pathway->prepend($name, $link);

		$items = $pathway->items();
		$item = array_shift($items);

		$this->assertInstanceOf('Hubzero\Pathway\Item', $item);
		$this->assertEquals($item->name, $name);
		$this->assertEquals($item->link, $link);
	}

	/**
	 * Test the count() method returns the number of items added
	 *
	 * @covers  \Hubzero\Pathway\Trail::count
	 * @return  void
	 **/
	public function testCount()
	{
		$pathway = new Trail();
		$pathway->append('Crumb 1', 'index.php?option=com_lorem');
		$pathway->append('Crumb 2', 'index.php?option=com_ipsum');

		$this->assertEquals(2, $pathway->count());
		$this->assertEquals(2, count($pathway));
	}

	/**
	 * Tests:
	 *  1. the names() method returns an array
	 *  2. the number of items in the array matches the number of items added
	 *  3. the array returned contains just the names of the items added
	 *
	 * @covers  \Hubzero\Pathway\Trail::names
	 * @return  void
	 **/
	public function testNames()
	{
		$data = [
			'Crumb 1',
			'Crumb 2'
		];

		$pathway = new Trail();
		$pathway->append('Crumb 1', 'index.php?option=com_lorem');
		$pathway->append('Crumb 2', 'index.php?option=com_ipsum');

		$names = $pathway->names();

		$this->assertTrue(is_array($names), 'names() should return an array');
		$this->assertCount(2, $names, 'names() returned incorrect number of entries');
		$this->assertEquals($names, $data);
	}

	/**
	 * Tests:
	 *  1. the items() method returns an array
	 *  2. the number of items in the array matches the number of items added
	 *  3. the array returned contains a Hubzero\Pathway\Item object for each entry added
	 *
	 * @covers  \Hubzero\Pathway\Trail::items
	 * @return  void
	 **/
	public function testItems()
	{
		$data = [
			new Item('Crumb 1', 'index.php?option=com_lorem'),
			new Item('Crumb 2', 'index.php?option=com_ipsum')
		];

		$pathway = new Trail();
		$pathway->append('Crumb 1', 'index.php?option=com_lorem');
		$pathway->append('Crumb 2', 'index.php?option=com_ipsum');

		$items = $pathway->items();

		$this->assertTrue(is_array($items), 'items() should return an array');
		$this->assertCount(2, $items, 'items() should have returned two Items');
		$this->assertEquals($items, $data);
	}

	/**
	 * Tests:
	 *  1. the names() method returns an array
	 *  2. the number of items in the array matches the number of items added
	 *  3. the array returned contains just the names of the items added
	 *
	 * @covers  \Hubzero\Pathway\Trail::clear
	 * @return  void
	 **/
	public function testClear()
	{
		$pathway = new Trail();
		$pathway->append('Crumb 1', 'index.php?option=com_lorem');
		$pathway->append('Crumb 2', 'index.php?option=com_ipsum');
		$pathway->clear();

		$items = $pathway->items();

		$this->assertTrue(empty($items), 'items() should return an empty array after calling clear()');
	}

	/**
	 * Tests array traversing methods
	 *
	 * @covers  \Hubzero\Pathway\Trail::current
	 * @covers  \Hubzero\Pathway\Trail::key
	 * @covers  \Hubzero\Pathway\Trail::next
	 * @covers  \Hubzero\Pathway\Trail::valid
	 * @covers  \Hubzero\Pathway\Trail::rewind
	 * @return  void
	 **/
	public function testIterator()
	{
		$items = array(
			new Item('Crumb 1', 'index.php?option=com_lorem'),
			new Item('Crumb 2', 'index.php?option=com_ipsum'),
			new Item('Crumb 3', 'index.php?option=com_foo'),
			new Item('Crumb 4', 'index.php?option=com_bar'),
			new Item('Crumb 5', 'index.php?option=com_mollum')
		);

		$pathway = new Trail();
		foreach ($items as $item)
		{
			$pathway->append($item->name, $item->link);
		}

		// both cycles must pass
		for ($n = 0; $n < 2; ++$n)
		{
			$i = 0;
			reset($items);
			foreach ($pathway as $key => $val)
			{
				if ($i >= 5)
				{
					$this->fail('Iterator overflow!');
				}
				$this->assertEquals(key($items), $key);
				$this->assertEquals(current($items), $val);
				next($items);
				++$i;
			}
			$this->assertEquals(5, $i);
		}

		// both cycles must pass
		$first = reset($items);

		$i = 0;
		foreach ($pathway as $key => $val)
		{
			if ($i > 3)
			{
				break;
			}
			$i++;
		}
		$this->assertEquals($first, $pathway->rewind());
	}
}
