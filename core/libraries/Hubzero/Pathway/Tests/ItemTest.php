<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Pathway\Tests;

use Hubzero\Test\Basic;
use Hubzero\Pathway\Item;

/**
 * Pathway trail item tests
 */
class ItemTest extends Basic
{
	/**
	 * Tests that data passed in constructor is set to correct properties
	 *
	 * @return  void
	 **/
	public function testConstructor()
	{
		$name = 'Crumb';
		$link = 'index.php?option=com_example';

		$item = new Item($name, $link);

		$this->assertEquals($item->name, $name);
		$this->assertEquals($item->link, $link);
	}
}
