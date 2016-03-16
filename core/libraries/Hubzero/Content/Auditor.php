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

namespace Hubzero\Content;

use Hubzero\Content\Auditor\Test;
use Hubzero\Content\Auditor\Result;
use RuntimeException;

/**
 * Data auditor
 */
class Auditor
{
	/**
	 * Holds registered tests
	 *
	 * @var  array
	 */
	protected $tests = array();

	/**
	 * Holds list of detectors and what they reported
	 *
	 * @var  array
	 */
	protected $report = null;

	/**
	 * Type of items being audited
	 *
	 * @var  string
	 */
	protected $scope = null;

	/**
	 * Log data?
	 *
	 * @var  object
	 */
	protected $log = null;

	/**
	 * Constructor
	 *
	 * @param   string  $scope
	 * @param   object  $logger
	 * @return  void
	 */
	public function __construct($scope, $logger = null)
	{
		$this->scope = $scope;

		if ($logger)
		{
			$this->log = $logger;
		}
	}

	/**
	 * Set logging
	 *
	 * @param   object  $logger
	 * @return  void
	 */
	public function setLogger($logger)
	{
		$this->log = $logger;
	}

	/**
	 * Process data against a series of tests
	 *
	 * @param   string|array  $data
	 * @return  object
	 */
	public function check($data)
	{
		$results = array();

		$data = $this->prepareData($data);

		foreach ($data as $datum)
		{
			$results[] = array(
				'data'  => $datum,
				'tests' => $this->process($datum)
			);
		}

		if ($this->log)
		{
			$this->log($results);
		}

		return $results;
	}

	/**
	 * Run the tests against a single data point
	 *
	 * @param   array  $data
	 * @return  array
	 */
	public function process(array $datum)
	{
		$results = array();

		foreach ($this->tests as $key => $tester)
		{
			$result = $tester->examine($datum);
			$result->set('scope', $this->scope);
			$result->set('test_id', $key);

			$results[$tester->name()] = $result;
		}

		return $results;
	}

	/**
	 * Register a test
	 *
	 * @param   object  $test  TestInterface
	 * @return  object  Test
	 * @throws  RuntimeException
	 */
	public function registerTest(Test $test)
	{
		$key = $this->classSimpleName($test);

		if (isset($this->tests[$key]))
		{
			throw new RuntimeException(
				sprintf('Test [%s] already registered', $key)
			);
		}

		$this->tests[$key] = $test;

		return $this;
	}

	/**
	 * Unregister a test
	 *
	 * @param   string  $key
	 * @return  object
	 */
	public function unregisterTest($key)
	{
		$key = $this->classSimpleName($key);

		if (isset($this->tests[$key]))
		{
			unset($this->tests[$key]);
		}

		return $this;
	}

	/**
	 * Gets a detector using its detector ID (Class Simple Name)
	 *
	 * @param   string  $key
	 * @return  mixed   False or TestInterface
	 */
	public function getTest($key)
	{
		if (!isset($this->tests[$key]))
		{
			return false;
		}

		return $this->tests[$key];
	}

	/**
	 * Gets a list of all spam detectors
	 *
	 * @return  array
	 */
	public function getTests()
	{
		return $this->tests;
	}

	/**
	 * Get summations
	 *
	 * @return  string
	 */
	public function getReport()
	{
		if (!isset($this->report))
		{
			$tests = array();

			foreach ($this->getTests() as $key => $audit)
			{
				if (!isset($tests[$key]))
				{
					$tests[$key] = array(
						'name'   => $audit->name(),
						'total'  => 0,
						'totals' => array(
							'skipped' => 0,
							'passed'  => 0,
							'failed'  => 0
						)
					);
				}

				$tests[$key]['totals']['skipped'] = Result::all()
					->whereEquals('scope', $this->scope)
					->whereEquals('test_id', $key)
					->whereEquals('status', 0)
					->count();

				$tests[$key]['totals']['passed'] = Result::all()
					->whereEquals('scope', $this->scope)
					->whereEquals('test_id', $key)
					->whereEquals('status', 1)
					->count();

				$tests[$key]['totals']['failed'] = Result::all()
					->whereEquals('scope', $this->scope)
					->whereEquals('test_id', $key)
					->whereEquals('status', -1)
					->count();

				$tests[$key]['total'] += $tests[$key]['totals']['skipped'];
				$tests[$key]['total'] += $tests[$key]['totals']['passed'];
				$tests[$key]['total'] += $tests[$key]['totals']['failed'];
			}

			$this->report = $tests;
		}

		return $this->report;
	}

	/**
	 * Used to normalize string before passing
	 * it to detectors
	 *
	 * @param   array   $data
	 * @return  string
	 */
	protected function prepareData($data)
	{
		if (is_string($data))
		{
			$data = array($data);
		}

		return $data;
	}

	/**
	 * Gets the name of a class (w. Namespaces removed)
	 *
	 * @param   mixed   $class  String (class name) or object
	 * @return  string
	 */
	protected function classSimpleName($class)
	{
		if (is_object($class))
		{
			$class = get_class($class);
		}

		return str_replace('\\', '_', $class);
	}

	/**
	 * Log results of the check
	 *
	 * @param   string  $isSpam  Spam detection result
	 * @param   array   $data    Data being checked
	 * @return  void
	 */
	protected function log($report)
	{
		if (!$this->log)
		{
			return;
		}

		$this->log->info(json_encode($report));
	}
}
