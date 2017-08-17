<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Tests;

use Hubzero\Test\Database;
use Components\Blog\Models\Entry;

require_once dirname(__DIR__) . '/models/entry.php';

/**
 * Entry model test
 */
class EntryTest extends Database
{
	/**
	 * Sets up the tests...called prior to each test
	 *
	 * @return  void
	 */
	public function setUp()
	{
		\Hubzero\Database\Relational::setDefaultConnection($this->getMockDriver());
	}

	/**
	 * Tests object construction and variable initialization
	 *
	 * @covers  Components\Blog\Models\Entry::blank
	 * @return  void
	 **/
	public function testConstruct()
	{
		$model = Entry::blank();

		$this->assertInstanceOf('\Hubzero\Database\Relational', $model, 'Model is not an instance of \Hubzero\Database\Relational');
		$this->assertEquals($model->getModelName(), 'Entry', 'Model should have a model name of "Entry"');
	}

	/**
	 * Tests to make sure that a result can be retrieved
	 *
	 * @covers  Components\Blog\Models\Entry::one
	 * @return  void
	 **/
	public function testOneReturnsResult()
	{
		$this->assertEquals(1, Entry::one(1)->id, 'Model should have returned an instance with ID of 1');
	}

	/**
	 * Tests that a call for a non-existant row via oneOrFail method throws an exception
	 *
	 * @covers  Components\Blog\Models\Entry::oneOrFail
	 * @expectedException RuntimeException
	 * @return  void
	 **/
	public function testOneOrFailThrowsException()
	{
		Entry::oneOrFail(0);
	}

	/**
	 * Tests that a request for a non-existant row via oneOrNew method returns new model
	 *
	 * @covers  Components\Blog\Models\Entry::oneOrNew
	 * @return  void
	 **/
	public function testOneOrNewCreatesNew()
	{
		$this->assertTrue(Entry::oneOrNew(0)->isNew(), 'Model should have stated that it was new');
	}

	/**
	 * Tests that a oneToMany relationship properly grabs the many side of the relationship
	 *
	 * @covers  Components\Blog\Models\Entry::comments
	 * @return  void
	 **/
	public function testCommentsRelationship()
	{
		$this->assertCount(2, Entry::oneOrFail(1)->comments, 'Model should have returned a count of 2 comments for entry 1');
	}

	/**
	 * Test that scoped entries are returned correctly
	 *
	 * @covers  Components\Blog\Models\Entry::oneByScope
	 * @return  void
	 */
	public function testGetScopedEntry()
	{
		$scopes = array(
			array(
				'alias' => 'bibendum',
				'scope' => 'site',
				'scope_id' => 0
			),
			array(
				'alias' => 'commodo',
				'scope' => 'group',
				'scope_id' => 1001
			),
			array(
				'alias' => 'fringilla',
				'scope' => 'member',
				'scope_id' => 1001
			),
		);

		foreach ($scopes as $item)
		{
			$entry = Entry::oneByScope($item['alias'], $item['scope'], $item['scope_id']);

			$this->assertTrue(is_object($entry));
			$this->assertEquals($entry->get('alias'), $item['alias']);
			$this->assertEquals($entry->get('scope'), $item['scope']);
			$this->assertEquals($entry->get('scope_id'), $item['scope_id']);
		}
	}
}
