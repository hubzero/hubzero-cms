<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Test\Basic;
use Hubzero\Base\Traits\ErrorBag;
use Exception;

/**
 * ErrorBag Trait test
 */
class ErrorBagTraitTest extends Basic
{
	/**
	 * The object under test.
	 *
	 * @var  object
	 */
	private $traitObject;

	/**
	 * Sets up the fixture.
	 *
	 * @return  void
	 */
	public function setUp()
	{
		$this->obj = $this->getObjectForTrait('Hubzero\Base\Traits\ErrorBag');

		parent::setUp();
	}

	/**
	 * Test ErrorBag methods
	 *
	 * @covers  \Hubzero\Base\Obj::setError
	 * @covers  \Hubzero\Base\Obj::setErrors
	 * @covers  \Hubzero\Base\Obj::getError
	 * @covers  \Hubzero\Base\Obj::getErrors
	 * @return  void
	 **/
	public function testErrorBag()
	{
		// Test that an array is returned
		$errors = $this->obj->getErrors();

		// Test that the array is empty
		$this->assertTrue(is_array($errors));
		$this->assertCount(0, $errors);

		// Set some errors
		$this->obj->setError('Donec sed odio dui.');
		$this->obj->setError(new Exception('Aenean lacinia bibendum.'));
		$this->obj->setError('Nulla sed consectetur.');

		// Get the list of set errors
		$errors = $this->obj->getErrors();

		// Make sure:
		//    - the list of errors matches the number of errors set
		//    - getError() returns the last error set
		//    - getError($index) returns the correct item
		//    - getError($index, false) returns Exception object instead of string
		$this->assertCount(3, $errors);
		$this->assertEquals($this->obj->getError(), 'Nulla sed consectetur.');
		$this->assertTrue(is_string($this->obj->getError(1)));
		$this->assertFalse($this->obj->getError(5));
		$this->assertInstanceOf('Exception', $this->obj->getError(1, false));

		// Test overwriting an existing entry
		$this->obj->setError('Aenean lacinia bibendum.', 0);
		$err = $this->obj->getErrors();

		$this->assertEquals($this->obj->getError(0), 'Aenean lacinia bibendum.');

		// Test setting the entire list
		$newerrors = array(
			'Integer posuere erat',
			'Ante venenatis dapibus',
			'Posuere velit aliquet.'
		);

		$this->obj->setErrors($newerrors);

		$this->assertEquals($this->obj->getErrors(), $newerrors);
	}
}
