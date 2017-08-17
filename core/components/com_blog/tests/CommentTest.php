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
use Components\Blog\Models\Comment;

require_once dirname(__DIR__) . '/models/entry.php';

/**
 * Comment model test
 */
class CommentTest extends Database
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
	 * @covers  Components\Blog\Models\Comment::blank
	 * @return  void
	 **/
	public function testConstruct()
	{
		$model = Comment::blank();

		$this->assertInstanceOf('\Hubzero\Database\Relational', $model, 'Model is not an instance of \Hubzero\Database\Relational');
		$this->assertEquals($model->getModelName(), 'Comment', 'Model should have a model name of "Comment"');
	}

	/**
	 * Tests to make sure that a result can be retrieved
	 *
	 * @covers  Components\Blog\Models\Comment::one
	 * @return  void
	 **/
	public function testOneReturnsResult()
	{
		$this->assertEquals(1, Comment::one(1)->id, 'Model should have returned an instance with ID of 1');
	}

	/**
	 * Tests that a call for a non-existant row via oneOrFail method throws an exception
	 *
	 * @covers  Components\Blog\Models\Comment::oneOrFail
	 * @expectedException RuntimeException
	 * @return  void
	 **/
	public function testOneOrFailThrowsException()
	{
		Comment::oneOrFail(0);
	}

	/**
	 * Tests that a request for a non-existant row via oneOrNew method returns new model
	 *
	 * @covers  Components\Blog\Models\Comment::oneOrNew
	 * @return  void
	 **/
	public function testOneOrNewCreatesNew()
	{
		$this->assertTrue(Comment::oneOrNew(0)->isNew(), 'Model should have stated that it was new');
	}

	/**
	 * Tests that parent/child relationship retrieves the correct parent
	 *
	 * @covers  Components\Blog\Models\Comment::parent
	 * @return  void
	 **/
	public function testGetParent()
	{
		$comment = Comment::one(2);

		$parent = $comment->parent();

		$this->assertInstanceOf('\Components\Blog\Models\Comment', $parent, 'Parent is not an instance of \Components\Blog\Models\Comment');
		$this->assertEquals(1, $parent->id, 'Comment should have returned a parent with ID of 1');
	}

	/**
	 * Tests that flagged records are correctly identified as such
	 *
	 * @covers  Components\Blog\Models\Comment::isReported
	 * @return  void
	 **/
	public function testIsReported()
	{
		$comment = Comment::one(1);

		$this->assertNotEquals(Comment::STATE_FLAGGED, $comment->get('state'), 'Comment->state should not have returned a value of ' . Comment::STATE_FLAGGED);
		$this->assertFalse($comment->isReported(), 'Comment->isReported() should have returned a value of false');

		$comment = Comment::one(3);

		$this->assertEquals(Comment::STATE_FLAGGED, $comment->get('state'), 'Comment->state should have returned a value of ' . Comment::STATE_FLAGGED);
		$this->assertTrue($comment->isReported(), 'Comment->isReported() should have returned a value of true');
	}

	/**
	 * Tests that modified records are correctly identified as such
	 *
	 * @covers  Components\Blog\Models\Comment::wasModified
	 * @return  void
	 **/
	public function testWasModified()
	{
		$comment = Comment::one(1);

		$this->assertFalse($comment->wasModified(), 'Comment->wasModified() should have returned a value of false');

		$comment = Comment::one(2);

		$this->assertTrue($comment->wasModified(), 'Comment->wasModified() should have returned a value of true');
	}
}
