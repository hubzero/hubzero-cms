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
 * PHPUnit 10 the type has to exist, so declare it. Methods are deliberately
 * absent: the tests add them through MockBuilder::addMethods().
 */
class ControllerDouble
{
	/**
	 * Controller name, assigned directly by the tests
	 *
	 * @var  string
	 */
	public $name;
}
