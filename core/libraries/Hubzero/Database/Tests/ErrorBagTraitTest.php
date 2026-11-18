<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\TestCase;
use Exception;

/**
 * ErrorBag Trait test
 */
class ErrorBagTraitTest extends TestCase
{
    /**
     * The object under test.
     *
     * @var  ErrorBagSubject
     */
    private $traitObject;

    /**
     * Sets up the fixture.
     *
     * @return  void
     */
    public function setUp(): void
    {
        $this->traitObject = new ErrorBagSubject();

        parent::setUp();
    }

    /**
     * Test ErrorBag methods
     *
     * @return  void
     **/
    public function testErrorBag()
    {
        // getErrors() returns an array (by its type); it starts empty
        $errors = $this->traitObject->getErrors();

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
        $newerrors = [
            'Integer posuere erat',
            'Ante venenatis dapibus',
            'Posuere velit aliquet.',
        ];

        $this->traitObject->setErrors($newerrors);

        $this->assertEquals($this->traitObject->getErrors(), $newerrors);
    }
}
