<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Doubles;

/**
 * Stand-in for a HUBzero controller in RecordProcessingHelper tests
 *
 * The tests only need something whose setView()/newTask()/editTask() calls can
 * be observed. Older PHPUnit generated a class for an unknown type name; since
 * PHPUnit 10 the type has to exist, so declare it, with the methods the
 * tests stub.
 */
class ControllerDouble
{
    /**
     * Controller name, assigned directly by the tests
     *
     * @var  string
     */
    public $name;

    /**
     * Stubbed by the tests to observe the view chosen
     *
     * @param   mixed  $name
     * @param   mixed  $layout
     * @return  void
     */
    public function setView($name, $layout)
    {
    }

    /**
     * Stubbed by the tests to observe a return to the new form
     *
     * @param   mixed  $record
     * @return  void
     */
    public function newTask($record)
    {
    }

    /**
     * Stubbed by the tests to observe a return to the edit form
     *
     * @param   mixed  $record
     * @return  void
     */
    public function editTask($record)
    {
    }
}
