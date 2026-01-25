<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Test\Basic;
use Hubzero\Database\Traits\ErrorBag;
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
    public function setUp(): void
    {
        $this->traitObject = $this->getObjectForTrait('Hubzero\Database\Traits\ErrorBag');

        parent::setUp();
    }

    /**
     * Test ErrorBag methods
     *
     * @covers  \Hubzero\Database\Traits\ErrorBag::addError
     * @covers  \Hubzero\Database\Traits\ErrorBag::setErrors
     * @covers  \Hubzero\Database\Traits\ErrorBag::getError
     * @covers  \Hubzero\Database\Traits\ErrorBag::getErrors
     * @return  void
     **/
    public function testErrorBag()
    {
        // Test that an array is returned
        $errors = $this->traitObject->getErrors();

        // Test that the array is empty
        $this->assertTrue(is_array($errors));
        $this->assertCount(0, $errors);

        // Set some errors
        $this->traitObject->addError('Donec sed odio dui.');
        $this->traitObject->addError(new Exception('Aenean lacinia bibendum.'));
        $this->traitObject->addError('Nulla sed consectetur.');

        // Get the list of set errors
        $errors = $this->traitObject->getErrors();

        // Make sure:
        //    - the list of errors matches the number of errors set
        //    - getError() returns the first error set
        $this->assertCount(3, $errors);
        $this->assertEquals($this->traitObject->getError(), 'Donec sed odio dui.');

        // Test setting the entire list
        $newerrors = array(
            'Integer posuere erat',
            'Ante venenatis dapibus',
            'Posuere velit aliquet.'
        );

        $this->traitObject->setErrors($newerrors);

        $this->assertEquals($this->traitObject->getErrors(), $newerrors);
    }
}
